<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Actions\InsertAccountBlock;
use App\Domains\Nom\Actions\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\MomentumContentDTO;
use App\Domains\Nom\DataTransferObjects\MomentumDTO;
use App\Domains\Nom\Enums\AccountRewardTypesEnum;
use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Exceptions\IndexerException;
use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\AccountReward;
use App\Domains\Nom\Models\Momentum;
use App\Domains\Nom\Models\Token;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class Indexer
{
    protected ?int $currentDbHeight = null;

    public function __construct(
        protected ZenonSdk $znn,
        protected InsertMomentum $insertMomentum,
        protected InsertAccountBlock $insertAccountBlock,
    ) {
        $this->updateCurrentHeight();
    }

    public function run(): void
    {
        try {
            $momentum = $this->znn->getFrontierMomentum();
        } catch (ZenonRpcException $e) {
            return;
        }

        $lock = Cache::lock('indexerLock', 0, 'indexer');
        $emergencyLock = Cache::lock('indexerEmergencyLock', 0, 'indexer');

        if (! $lock->get() || ! $emergencyLock->get()) {
            Log::debug('Indexer - Locked', [
                'lock' => $lock->get(),
                'emergency' => $emergencyLock->get(),
            ]);

            return;
        }

        Log::debug('Indexer - Starting', [
            'current height' => $this->currentDbHeight,
            'target height' => $momentum->height,
        ]);

        while ($this->currentDbHeight < $momentum->height) {
            // Try catch to break out indexing loop in case of error
            try {
                $momentums = $this->znn->getMomentumsByHeight($this->currentDbHeight, 500);
                $momentums->each(function (MomentumDTO $momentumDTO) {
                    // Try catch to commit db changes per momentum, will throw
                    // an exception to the parent try catch to break the loop
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

                    $this->updateCurrentHeight();
                });
            } catch (Throwable $exception) {
                Log::error($exception);
                Log::debug('Indexer - Error', [
                    'message' => $exception->getMessage(),
                ]);
                break;
            }
        }

        $lock->release();

        Log::debug('Indexer - Stopping', [
            'current height' => $this->currentDbHeight,
            'target height' => $momentum->height,
        ]);
    }

    private function updateCurrentHeight(): void
    {
        // If DB only has genesis data start from height 2, dont reindex genesis
        $dbHeight = Momentum::max('height');
        $this->currentDbHeight = max($dbHeight, 2);
    }

    private function updateTokenTransferTotals(Account $account, Account $toAccount, Token $token, \App\Domains\Nom\DataTransferObjects\AccountBlockDTO $blockData): void
    {
        if ($blockData->token && $blockData->amount > 0) {
            $save = false;

            if ($token->token_standard === NetworkTokensEnum::ZNN->value) {
                $account->total_znn_sent += $blockData->amount;
                $toAccount->total_znn_received += $blockData->amount;
                $save = true;
            }

            if ($token->token_standard === NetworkTokensEnum::QSR->value) {
                $account->total_qsr_sent += $blockData->amount;
                $toAccount->total_qsr_received += $blockData->amount;
                $save = true;
            }

            if ($save) {
                $account->save();
                $toAccount->save();
            }
        }
    }

    private function processLiquidityProgramRewards(AccountBlock $block, \App\Domains\Nom\DataTransferObjects\AccountBlockDTO $blockData): void
    {
        if ($block->token?->id === 2 && $blockData->address === config('explorer.liquidity_program_distributor')) {
            AccountReward::create([
                'chain_id' => $block->chain->id,
                'account_id' => $block->toAccount->id,
                'token_id' => $block->token->id,
                'type' => AccountRewardTypesEnum::LIQUIDITY_PROGRAM->value,
                'amount' => $block->amount,
                'created_at' => $block->created_at,
            ]);
        }
    }
}
