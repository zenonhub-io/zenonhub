<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
use App\Models\Nom\BridgeUnwrap;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ProcessBridgeUnwrapReward
{
    use AsAction;

    public string $commandSignature = 'nom:process-bridge-unwrap-rewards';

    public function handle(AccountBlock $accountBlock, BridgeUnwrap $unwrap): void
    {
        if (! $unwrap->is_affiliate_reward) {
            return;
        }

        AccountReward::create([
            'chain_id' => $accountBlock->chain_id,
            'account_block_id' => $accountBlock->id,
            'account_id' => $unwrap->to_account_id,
            'token_id' => $unwrap->token_id,
            'type' => AccountRewardTypesEnum::BRIDGE_AFFILIATE->value,
            'amount' => $unwrap->amount,
            'created_at' => $accountBlock->created_at,
        ]);
    }

    public function asCommand(Command $command): void
    {
        $totalAccounts = BridgeUnwrap::whereAffiliateReward()->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalAccounts);
        $progressBar->start();

        AccountReward::where('type', AccountRewardTypesEnum::BRIDGE_AFFILIATE->value)->delete();

        BridgeUnwrap::with('accountBlock')
            ->whereAffiliateReward()
            ->chunk(1000, function (Collection $unwraps) use ($progressBar) {
                $unwraps->each(function ($unwrap) use ($progressBar) {
                    $this->handle($unwrap->accountBlock, $unwrap);
                    $progressBar->advance();
                });
            });

        $progressBar->finish();
    }
}
