<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\AcceleratorPhaseStatusEnum;
use App\Domains\Nom\Enums\AcceleratorProjectStatusEnum;
use Illuminate\Support\Facades\Cache;

trait AcceleratorVotesTrait
{
    public function scopeIsOpenForVoting($query)
    {
        return $query->whereDate('created_at', '>', now()->subDays(40));
    }

    public function getIsVotingOpen(): bool
    {
        return $this->created_at->addDays(14) > now();
    }

    public function getIsQuorumReachedAttribute(): bool
    {
        return ! ($this->total_more_votes_needed > 0);
    }

    public function getOpenForTimeAttribute(): string
    {
        return $this->created_at->addDays(14)->diffForHumans(['parts' => 2], true);
    }

    public function getVotesNeededAttribute()
    {
        return Cache::tags('azVote')->rememberForever("{$this->cacheKey()}|getVotesNeededAttribute", function () {

            $totalPillars = Pillar::isActive()->where('created_at', '<=', $this->created_at)->count();

            // New projects or open phases can be voted on by all pillars so creation date shouldnt be accounted for
            if (
                ($this instanceof AcceleratorProject && $this->status === AcceleratorProjectStatusEnum::NEW->value)
                || ($this instanceof AcceleratorPhase && $this->status === AcceleratorPhaseStatusEnum::OPEN->value)
            ) {
                $totalPillars = Pillar::isActive()->count();
            }

            return ceil($totalPillars * .33);
        });
    }

    public function getTotalVotesAttribute()
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
        return round(($this->getTotalVotesAttribute() * 100) / $this->getVotesNeededAttribute());
    }

    public function getTotalYesVotesPercentageAttribute(): float
    {
        return round(($this->getTotalYesVotesAttribute() * 100) / $this->getVotesNeededAttribute());
    }

    public function getTotalNoVotesPercentageAttribute(): float
    {
        return round(($this->getTotalNoVotesAttribute() * 100) / $this->getVotesNeededAttribute());
    }

    public function getTotalAbstainVotesPercentageAttribute(): float
    {
        return round(($this->getTotalAbstainVotesAttribute() * 100) / $this->getVotesNeededAttribute());
    }
}
