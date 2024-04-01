<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services;

use App\Domains\Nom\Actions\InsertAccountBlock;
use App\Domains\Nom\Actions\InsertMomentum;
use App\Domains\Nom\DataTransferObjects\MomentumContentData as MomentumContentDTO;
use App\Domains\Nom\DataTransferObjects\MomentumData as MomentumDTO;
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

        Log::debug('Indexer - Starting', [
            'current height' => $this->currentDbHeight,
            'target height' => $momentum->height,
        ]);

        while ($this->currentDbHeight < $momentum->height) {
            // Try catch to break out indexing loop in case of error
            try {
                $momentums = $this->znn->getMomentumsByHeight($this->currentDbHeight, 500);
                $momentums->each(function (MomentumDTO $momentumDTO) {
                    DB::beginTransaction();

                    // Try catch to commit db changes per momentum, will throw
                    // an exception to the parent try catch to break the loop
                    try {
                        (new InsertMomentum)->execute($momentumDTO);

                        $momentumDTO->content->each(function (MomentumContentDTO $momentumContentDTO) {
                            (new InsertAccountBlock)->execute($momentumContentDTO);
                        });

                        DB::commit();
                    } catch (Throwable $exception) {
                        DB::rollBack();
                        throw new IndexerException("Indexing error, rollback - {$exception->getMessage()}");
                    }

                    $this->updateCurrentHeight();
                });
            } catch (Throwable $exception) {
                Log::debug('Indexer - Error', [
                    'message' => $exception->getMessage(),
                ]);
                Log::error($exception);
                break;
            }
        }
    }

    private function updateCurrentHeight(): void
    {
        $this->currentDbHeight = Momentum::max('height') ?? 1;
    }

    private function processMomentums(): void
    {
        Cache::put('momentum-count', Momentum::count());
        Cache::put('transaction-count', AccountBlock::count());
        Cache::put('address-count', Account::count());
    }

    private function updateTokenTransferTotals(Account $account, Account $toAccount, Token $token, \App\Domains\Nom\DataTransferObjects\AccountBlockData $blockData): void
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

    private function processLiquidityProgramRewards(AccountBlock $block, \App\Domains\Nom\DataTransferObjects\AccountBlockData $blockData): void
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
