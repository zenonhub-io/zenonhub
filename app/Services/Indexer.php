<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Indexer\InsertAccountBlock;
use App\Actions\Indexer\InsertMomentum;
use App\DataTransferObjects\Nom\MomentumContentDTO;
use App\DataTransferObjects\Nom\MomentumDTO;
use App\Exceptions\IndexerException;
use App\Exceptions\ZenonRpcException;
use App\Models\Nom\Momentum;
use App\Services\ZenonSdk\ZenonSdk;
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

        $this->writeOutput([
            'Start height: ' . $this->currentDbHeight,
            'Target height: ' . $momentum->height,
            'Momentums to index: ' . $momentumsToIndex,
        ]);

        $progressBar = $this->initProgressBar($momentumsToIndex);

        while ($this->currentDbHeight < $momentum->height) {
            try {
                $this->processMomentums();
            } catch (Throwable $exception) {
                $this->writeOutput('Indexer error rolling back');
                Log::debug('Indexer - Error', [
                    'message' => $exception->getMessage(),
                ]);
                break;
            }

            $progressBar?->advance($this->momentumsPerBatch);
        }

        $progressBar?->finish();
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
        $emergencyLock = Cache::lock('indexerEmergencyLock', 0, 'indexer');

        // If the emergency lock is active, stop immediately
        if (! $emergencyLock->get()) {
            Log::debug('Indexer - Emergency lock active, operation aborted.');

            return null;
        }

        $regularLock = Cache::lock('indexerLock', 0, 'indexer');

        if (! $regularLock->get()) {
            Log::debug('Indexer - Regular lock active, process already running.');

            return null;
        }

        return $regularLock;
    }

    private function initProgressBar(int $total): ?ProgressBar
    {
        if (app()->runningUnitTests()) {
            return null;
        }

        $progressBar = new ProgressBar($this->output, $total);
        $progressBar->start();

        return $progressBar;
    }

    private function writeOutput(array|string $message): void
    {
        if (! app()->runningUnitTests()) {

            if (is_string($message)) {
                $message = [$message];
            }

            $this->output->write($message, true);
        }
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
                    $accountBlockDTO = $this->znn->getAccountBlockByHash($momentumContentDTO->hash);
                    $this->insertAccountBlock->execute($accountBlockDTO);
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
