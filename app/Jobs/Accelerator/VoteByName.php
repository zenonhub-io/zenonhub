<?php

namespace App\Jobs\Accelerator;

use Cache;
use Log;
use App\Jobs\Sync\Projects as SyncProjects;
use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorPhaseVote;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\AcceleratorProjectVote;
use App\Models\Nom\AccountBlock;
use App\Models\Nom\Pillar;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
            Log::debug('VOTE ERROR - No item found for vote '  . $blockData['id']);
        }

        if ($item instanceof AcceleratorProject) {
            $this->processProjectVote($item, $pillar);
        }

        if ($item instanceof AcceleratorPhase) {
            $this->processPhaseVote($item, $pillar);
        }

        SyncProjects::dispatch();
    }

    private function processProjectVote($project, $pillar): void
    {
        Cache::forget("accelerator_project-{$project->id}-votes-needed");
        Cache::forget("accelerator_project-{$project->id}-total-votes");
        Cache::forget("accelerator_project-{$project->id}-total-yes-votes");
        Cache::forget("accelerator_project-{$project->id}-total-no-votes");
        Cache::forget("accelerator_project-{$project->id}-total-abstain-votes");

        $vote = $project
            ->votes()
            ->where('owner_id', $pillar->owner_id)
            ->first();

        if (! $vote) {
            $vote = AcceleratorProjectVote::create([
                'accelerator_project_id' => $project->id,
                'owner_id' => $pillar->owner_id,
                'pillar_id' => $pillar->id,
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

        // Update pillar voting
        $totalProjects = AcceleratorProject::where('created_at', '>=', $pillar->created_at)->count();
        if ($totalProjects > 0) {
            $totalVotes = $pillar->az_project_votes()->count();
            $percentage = ($totalVotes * 100) / $totalProjects;
            $pillar->az_engagement = round($percentage, 1);
            $pillar->save();
        }
    }

    private function processPhaseVote($phase, $pillar): void
    {
        Cache::forget("accelerator_phase-{$phase->id}-votes-needed");
        Cache::forget("accelerator_phase-{$phase->id}-total-votes");
        Cache::forget("accelerator_phase-{$phase->id}-total-yes-votes");
        Cache::forget("accelerator_phase-{$phase->id}-total-no-votes");
        Cache::forget("accelerator_phase-{$phase->id}-total-abstain-votes");

        $vote = $phase
            ->votes()
            ->where('owner_id', $pillar->owner_id)
            ->first();

        if (! $vote) {
            $vote = AcceleratorPhaseVote::create([
                'accelerator_phase_id' => $phase->id,
                'owner_id' => $pillar->owner_id,
                'pillar_id' => $pillar->id,
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
    }

    private function isVoteType($type): bool
    {
        $blockData = $this->block->data->decoded;

        if($type === 'yes' && $blockData['vote'] === '0') {
            return true;
        }

        if($type === 'no' && $blockData['vote'] === '1') {
            return true;
        }

        if($type === 'abstain' && $blockData['vote'] === '2') {
            return true;
        }

        return false;
    }
}
