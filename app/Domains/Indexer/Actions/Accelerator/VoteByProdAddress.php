<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Actions\Accelerator;

use App\Actions\Nom\Accelerator\SyncPhaseStatus;
use App\Actions\Nom\Accelerator\SyncProjectStatus;
use App\Domains\Indexer\Actions\AbstractContractMethodProcessor;
use App\Domains\Nom\Models\AcceleratorPhase;
use App\Domains\Nom\Models\AcceleratorProject;
use App\Domains\Nom\Models\AcceleratorVote;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\Pillar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class VoteByProdAddress extends AbstractContractMethodProcessor
{
    public function handle(AccountBlock $accountBlock): void
    {
        $blockData = $accountBlock->data->decoded;

        if (! $this->validateAction($accountBlock)) {
            Log::info('Contract Method Processor - Accelerator: VoteByProdAddress failed', [
                'accountBlock' => $accountBlock->hash,
                'blockData' => $blockData,
            ]);

            return;
        }

        $this->processVote();

        Log::info('Contract Method Processor - Accelerator: VoteByProdAddress complete', [
            'accountBlock' => $accountBlock->hash,
            'blockData' => $blockData,
        ]);

        $this->setBlockAsProcessed($accountBlock);
    }

    protected function validateAction(): bool
    {
        [$accountBlock] = func_get_args();

        return true;
    }

    private function processVote()
    {
        $blockData = $accountBlock->data->decoded;

        // Load the pillar that voted
        $pillar = Pillar::where('producer_account_id', $accountBlock->account_id)->first();

        if (! $pillar) {
            return;
        }

        // Load the project or phase
        $item = AcceleratorProject::where('hash', $blockData['id'])->first();
        if (! $item) {
            $item = AcceleratorPhase::where('hash', $blockData['id'])->first();
        }

        if (! $item) {
            Log::debug('VOTE ERROR - No item found for vote ' . $blockData['id']);

            return;
        }

        if ($item instanceof AcceleratorProject) {
            Cache::forget("accelerator_project-{$item->id}-votes-needed");
            Cache::forget("accelerator_project-{$item->id}-total-votes");
            Cache::forget("accelerator_project-{$item->id}-total-yes-votes");
            Cache::forget("accelerator_project-{$item->id}-total-no-votes");
            Cache::forget("accelerator_project-{$item->id}-total-abstain-votes");
            (new SyncProjectStatus($item))->execute();
        }

        if ($item instanceof AcceleratorPhase) {
            Cache::forget("accelerator_phase-{$item->id}-votes-needed");
            Cache::forget("accelerator_phase-{$item->id}-total-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-yes-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-no-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-abstain-votes");
            (new SyncPhaseStatus($item))->execute();
        }

        $vote = $item
            ->votes()
            ->where('owner_id', $pillar->owner_id)
            ->first();

        if (! $vote) {
            $vote = AcceleratorVote::create([
                'owner_id' => $pillar->owner_id,
                'pillar_id' => $pillar->id,
                'votable_id' => $item->id,
                'votable_type' => $item::class,
                'is_yes' => $this->isVoteType('yes'),
                'is_no' => $this->isVoteType('no'),
                'is_abstain' => $this->isVoteType('abstain'),
                'created_at' => $accountBlock->created_at,
            ]);
        }

        $vote->is_yes = $this->isVoteType('yes');
        $vote->is_no = $this->isVoteType('no');
        $vote->is_abstain = $this->isVoteType('abstain');
        $vote->save();

        $pillar->updateAzEngagementScores();
    }

    private function isVoteType($type): bool
    {
        $blockData = $accountBlock->data->decoded;

        if ($type === 'yes' && $blockData['vote'] === '0') {
            return true;
        }

        if ($type === 'no' && $blockData['vote'] === '1') {
            return true;
        }

        if ($type === 'abstain' && $blockData['vote'] === '2') {
            return true;
        }

        return false;
    }
}