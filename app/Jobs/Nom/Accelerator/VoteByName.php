<?php

namespace App\Jobs\Nom\Accelerator;

use App\Actions\SetBlockAsProcessed;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AcceleratorVote;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Cache;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class VoteByName implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;

    public int $backoff = 10;

    public AccountBlock $block;

    public function __construct(AccountBlock $block)
    {
        $this->block = $block;
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        $this->processVote();
        (new SetBlockAsProcessed($this->block))->execute();
    }

    private function processVote()
    {
        $blockData = $this->block->data->decoded;

        // Load the pillar that voted
        $pillar = Pillar::where('name', $blockData['name'])->first();

        if (! $pillar) {
            return;
        }

        // Load the project or phase
        $item = AcceleratorProject::where('hash', $blockData['id'])->first();
        if (! $item) {
            $item = AcceleratorPhase::where('hash', $blockData['id'])->first();
        }

        if (! $item) {
            Log::debug('VOTE ERROR - No item found for vote '.$blockData['id']);

            return;
        }

        if ($item instanceof AcceleratorProject) {
            Cache::forget("accelerator_project-{$item->id}-votes-needed");
            Cache::forget("accelerator_project-{$item->id}-total-votes");
            Cache::forget("accelerator_project-{$item->id}-total-yes-votes");
            Cache::forget("accelerator_project-{$item->id}-total-no-votes");
            Cache::forget("accelerator_project-{$item->id}-total-abstain-votes");
        }

        if ($item instanceof AcceleratorPhase) {
            Cache::forget("accelerator_phase-{$item->id}-votes-needed");
            Cache::forget("accelerator_phase-{$item->id}-total-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-yes-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-no-votes");
            Cache::forget("accelerator_phase-{$item->id}-total-abstain-votes");
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
                'created_at' => $this->block->momentum->created_at,
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
        $blockData = $this->block->data->decoded;

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
