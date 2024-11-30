<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Enums\Nom\EmbeddedContractsEnum;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\AccountReward;
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

    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $token = load_token($blockData['tokenStandard']);
        $rewardReceiver = load_account($blockData['receiveAddress']);

        if ($rewardReceiver->address === EmbeddedContractsEnum::LIQUIDITY->value) {
            return;
        }

        $rewardMapping = [
            EmbeddedContractsEnum::SENTINEL->value => AccountRewardTypesEnum::SENTINEL->value,
            EmbeddedContractsEnum::STAKE->value => AccountRewardTypesEnum::STAKE->value,
            EmbeddedContractsEnum::LIQUIDITY->value => AccountRewardTypesEnum::LIQUIDITY->value,
            EmbeddedContractsEnum::PILLAR->value => $rewardReceiver->is_pillar_withdraw_address
                ? AccountRewardTypesEnum::PILLAR->value
                : AccountRewardTypesEnum::DELEGATE->value,
        ];

        $rewardType = $rewardMapping[$accountBlock->account->address] ?? null;

        if (! $rewardType) {
            return;
        }

        AccountReward::create([
            'chain_id' => $accountBlock->chain_id,
            'account_block_id' => $accountBlock->id,
            'account_id' => $rewardReceiver->id,
            'token_id' => $token->id,
            'type' => $rewardType,
            'amount' => $blockData['amount'],
            'created_at' => $accountBlock->created_at,
        ]);
    }

    public function asCommand(Command $command): void
    {
        DB::table('nom_account_rewards')->truncate();

        $query = AccountBlock::with('data', 'account')
            ->whereHas('parent')
            ->whereRelation('contractMethod', 'name', 'Mint');

        $totalBlocks = $query->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalBlocks);
        $progressBar->start();

        $query->chunk(1000, function (Collection $blocks) use ($progressBar) {
            $blocks->each(function ($block) use ($progressBar) {
                $this->handle($block);
                $progressBar->advance();
            });
        });

        $progressBar->finish();
    }
}
