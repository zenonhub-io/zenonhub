<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Enums\Nom\AcceleratorProjectStatusEnum;
use Illuminate\Support\Facades\Cache;

trait AcceleratorVotesTrait
{
    public function getVotesNeededAttribute(): float
    {
        return (float) Cache::rememberForever($this->cacheKey('votes-needed-attribute', 'updated_at'), function () {
            $totalPillars = Pillar::whereActive()->where('created_at', '<=', $this->created_at)->count();

            // New projects or open phases can be voted on by all pillars so creation date shouldnt be accounted for
            if (
                ($this instanceof AcceleratorProject && $this->status === AcceleratorProjectStatusEnum::NEW) ||
                ($this instanceof AcceleratorPhase && $this->status === AcceleratorPhaseStatusEnum::OPEN)
            ) {
                $totalPillars = Pillar::whereActive()->count();
            }

            return ceil($totalPillars * (config('nom.accelerator.voteAcceptanceThreshold') / 100));
        });
    }

    public function getTotalMoreVotesNeededAttribute(): mixed
    {
        $needed = $this->getVotesNeededAttribute() - $this->total_votes;

        return max($needed, 0);
    }

    public function getIsQuorumReachedAttribute(): bool
    {
        return ! ($this->total_more_votes_needed > 0);
    }

    public function getVotesPercentageAttribute(): float
    {
        $percentage = ($this->total_votes * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalYesVotesPercentageAttribute(): float
    {
        $percentage = ($this->total_yes_votes * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalNoVotesPercentageAttribute(): float
    {
        $percentage = ($this->total_no_votes * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalAbstainVotesPercentageAttribute(): float
    {
        $percentage = ($this->total_abstain_votes * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }
}
