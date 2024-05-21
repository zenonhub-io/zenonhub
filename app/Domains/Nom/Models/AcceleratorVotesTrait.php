<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use Illuminate\Support\Facades\Cache;

trait AcceleratorVotesTrait
{
    public function getOpenForTimeAttribute(): string
    {
        $votingPeriod = config('nom.accelerator.acceleratorProjectVotingPeriod');

        return $this->created_at->addSeconds($votingPeriod)->diffForHumans(['parts' => 2], true);
    }

    public function getVotesNeededAttribute(): int
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getVotesNeededAttribute", function () {

            $totalPillars = Pillar::isActive()->where('created_at', '<=', $this->created_at)->count();

            // New projects or open phases can be voted on by all pillars so creation date shouldnt be accounted for
            if (
                ($this instanceof AcceleratorProject && $this->status->value === AcceleratorProjectStatusEnum::NEW->value) ||
                ($this instanceof AcceleratorPhase && $this->status->value === AcceleratorPhaseStatusEnum::OPEN->value)
            ) {
                $totalPillars = Pillar::isActive()->count();
            }

            return ceil($totalPillars * (config('nom.accelerator.voteAcceptanceThreshold') / 100));
        });
    }

    public function getTotalVotesAttribute(): int
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getTotalVotesAttribute", function () {
            return $this->votes()
                ->count();
        });
    }

    public function getTotalYesVotesAttribute(): int
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getTotalYesVotesAttribute", function () {
            return $this->votes()
                ->whereYesVote()
                ->count();
        });
    }

    public function getTotalNoVotesAttribute(): int
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getTotalNoVotesAttribute", function () {
            return $this->votes()
                ->whereNoVote()
                ->count();
        });
    }

    public function getTotalAbstainVotesAttribute(): int
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getTotalAbstainVotesAttribute", function () {
            return $this->votes()
                ->whereAbstainVote()
                ->count();
        });
    }

    public function getTotalMoreVotesNeededAttribute(): mixed
    {
        $needed = $this->getVotesNeededAttribute() - $this->getTotalVotesAttribute();

        return max($needed, 0);
    }

    public function getVotesPercentageAttribute(): float
    {
        $percentage = ($this->getTotalVotesAttribute() * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalYesVotesPercentageAttribute(): float
    {
        $percentage = ($this->getTotalYesVotesAttribute() * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalNoVotesPercentageAttribute(): float
    {
        $percentage = ($this->getTotalNoVotesAttribute() * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }

    public function getTotalAbstainVotesPercentageAttribute(): float
    {
        $percentage = ($this->getTotalAbstainVotesAttribute() * 100) / $this->getVotesNeededAttribute();

        return round($percentage);
    }
}
