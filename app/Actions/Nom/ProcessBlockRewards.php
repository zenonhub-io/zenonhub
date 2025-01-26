<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\AccountReward;
use App\Models\Nom\TokenMint;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class ProcessBlockRewards
{
    use AsAction;

    public string $commandSignature = 'nom:process-rewards';

    public function handle(TokenMint $mint): void
    {
        $mint->loadMissing('accountBlock', 'token', 'issuer', 'receiver');
        $token = $mint->token;
        $rewardReceiver = $mint->receiver;

        if ($rewardReceiver->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            return;
        }

        $rewardMapping = [
            EmbeddedContractsEnum::SENTINEL->value => AccountRewardTypesEnum::SENTINEL->value,
            EmbeddedContractsEnum::STAKE->value => AccountRewardTypesEnum::STAKE->value,
            EmbeddedContractsEnum::LIQUIDITY->value => AccountRewardTypesEnum::LIQUIDITY->value,
            EmbeddedContractsEnum::PILLAR->value => $rewardReceiver->is_pillar_withdraw_address || $rewardReceiver->is_historic_pillar_withdraw_address
                ? AccountRewardTypesEnum::PILLAR->value
                : AccountRewardTypesEnum::DELEGATE->value,
        ];

        $rewardType = $rewardMapping[$mint->issuer->address] ?? null;

        if (! $rewardType) {
            return;
        }

        AccountReward::create([
            'chain_id' => $mint->chain_id,
            'account_block_id' => $mint->accountBlock->id,
            'account_id' => $rewardReceiver->id,
            'token_id' => $token->id,
            'type' => $rewardType,
            'amount' => $mint->amount,
            'created_at' => $mint->created_at,
        ]);
    }

    public function asCommand(Command $command): void
    {
        DB::table('nom_account_rewards')->truncate();

        $query = TokenMint::with('accountBlock', 'token', 'receiver');

        $totalBlocks = $query->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalBlocks);
        $progressBar->start();

        $query->chunk(1000, function (Collection $mints) use ($progressBar) {
            $mints->each(function ($mint) use ($progressBar) {
                $this->handle($mint);
                $progressBar->advance();
            });
        });

        $progressBar->finish();
    }
}
