<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Actions\Indexer\InsertAccountBlock;
use App\Domains\Nom\Actions\Indexer\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\MomentumContentDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Exceptions\IndexerException;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\Momentum;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

class Indexer
{
    protected ?int $currentDbHeight = null;

    protected ?int $momentumsPerBatch = 500;

    protected ConsoleOutput $output;

    public function __construct(
        protected ZenonSdk $znn,
        protected InsertMomentum $insertMomentum,
        protected InsertAccountBlock $insertAccountBlock,
    ) {
        $this->output = new ConsoleOutput;
    }

    public function run(): void
    {
        $this->setCurrentHeight();

        if (! $momentum = $this->loadFrontierMomentum()) {
            return;
        }

        if (! $lock = $this->obtainLock()) {
            return;
        }

        $momentumsToIndex = $momentum->height - $this->currentDbHeight;

        Log::debug('Indexer - Starting', [
            'current height' => $this->currentDbHeight,
            'target height' => $momentum->height,
            'to index' => $momentumsToIndex,
        ]);

        $this->output->write([
            'Start height: ' . $this->currentDbHeight,
            'Target height: ' . $momentum->height,
            'Momentums to index: ' . $momentumsToIndex,
        ], true);

        $progressBar = $this->initProgressBar($momentumsToIndex);

        while ($this->currentDbHeight < $momentum->height) {
            try {
                $this->processMomentums();
            } catch (Throwable $exception) {
                $this->output->writeln('Indexer error rolling back');
                Log::debug('Indexer - Error', [
                    'message' => $exception->getMessage(),
                ]);
                break;
            }

            $progressBar->advance($this->momentumsPerBatch);
        }

        $progressBar->finish();
        $lock->release();

        Log::debug('Indexer - Stopping', [
            'current height' => $this->currentDbHeight,
            'target height' => $momentum->height,
        ]);
    }

    private function setCurrentHeight(): void
    {
        // If DB only has genesis data start from height 2, don't re-index genesis
        $dbHeight = Momentum::max('height');
        $this->currentDbHeight = max($dbHeight, 2);
    }

    private function loadFrontierMomentum(): ?MomentumDTO
    {
        try {
            $momentum = $this->znn->getFrontierMomentum();
        } catch (ZenonRpcException $e) {
            $this->output->writeln('Unable to load frontier momentum - ' . $e->getMessage());

            Log::debug('Indexer - Unable to load frontier momentum', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }

        return $momentum;
    }

    private function obtainLock(): ?Lock
    {
        $lock = Cache::lock('indexerLock', 0, 'indexer');
        $emergencyLock = Cache::lock('indexerEmergencyLock', 0, 'indexer');

        if (! $lock->get() || ! $emergencyLock->get()) {
            Log::debug('Indexer - Locked', [
                'lock' => $lock->get(),
                'emergency' => $emergencyLock->get(),
            ]);

            return null;
        }

        return $lock;
    }

    private function initProgressBar(int $total): ProgressBar
    {
        $progressBar = new ProgressBar($this->output, $total);
        $progressBar->start();

        return $progressBar;
    }

    /**
     * @throws IndexerException
     * @throws ZenonRpcException
     * @throws Throwable
     */
    private function processMomentums(): void
    {
        $momentums = $this->znn->getMomentumsByHeight($this->currentDbHeight, $this->momentumsPerBatch);
        $momentums->each(function (MomentumDTO $momentumDTO) {
            try {
                DB::beginTransaction();

                $this->insertMomentum->execute($momentumDTO);

                $momentumDTO->content->each(function (MomentumContentDTO $momentumContentDTO) {
                    $this->insertAccountBlock->execute($momentumContentDTO);
                });

                DB::commit();
            } catch (Throwable $exception) {
                DB::rollBack();
                Log::error($exception);
                throw new IndexerException("Indexing error, rollback - {$exception->getMessage()}");
            }

            $this->setCurrentHeight();
        });
    }
}
