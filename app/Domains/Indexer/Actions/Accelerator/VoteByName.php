<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AcceleratorVote;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Support\Facades\Log;

class VoteByName extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;
        $pillar = Pillar::findBy('name', $blockData['name']);
        $item = AcceleratorProject::findBy('hash', $blockData['id']);
        if (! $item) {
            $item = AcceleratorPhase::findBy('hash', $blockData['id']);
        }

        if (! $pillar || ! $item || ! $this->validateAction($accountBlock, $pillar)) {
            Log::info('Contract Method Processor - Accelerator: VoteByName failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        AcceleratorVote::updateOrCreate([
            'owner_id' => $pillar->owner_id,
            'pillar_id' => $pillar->id,
            'votable_id' => $item->id,
            'votable_type' => $item::class,
        ], [
            'is_yes' => $this->isVoteType('yes', $blockData['vote']),
            'is_no' => $this->isVoteType('no', $blockData['vote']),
            'is_abstain' => $this->isVoteType('abstain', $blockData['vote']),
            'created_at' => $accountBlock->momentum->created_at,
        ]);

        //$pillar->updateAzEngagementScores();

        Log::info('Contract Method Processor - Accelerator: VoteByName complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    public function validateAction(): bool
    {
        /**
         * @var AccountBlock $accountBlock
         * @var Pillar $pillar
         */
        [$accountBlock, $pillar] = func_get_args();

        if ($pillar->owner_id !== $accountBlock->account_id) {
            return false;
        }

        return true;
    }

    private function isVoteType(string $type, string $vote): bool
    {
        if ($type === 'yes' && $vote === '0') {
            return true;
        }

        if ($type === 'no' && $vote === '1') {
            return true;
        }

        if ($type === 'abstain' && $vote === '2') {
            return true;
        }

        return false;
    }
}
