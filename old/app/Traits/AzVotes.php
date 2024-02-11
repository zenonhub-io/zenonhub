<?php

namespace App\Traits;

use App\Models\Nom\AcceleratorPhase;
use App\Models\Nom\AcceleratorProject;
use App\Models\Nom\Pillar;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

trait AzVotes
{
    private function getCachePrefix()
    {
        $cachePrefix = Str::replace('App\Models\Nom\\', '', __CLASS__);

        return Str::snake($cachePrefix).'-'.$this->id;
    }

    public function scopeIsOpenForVoting($query)
    {
        return $query->whereDate('created_at', '>', now()->subDays(40));
    }

    public function getIsVotingOpen()
    {
        return $this->created_at->addDays(14) > now();
    }

    public function getOpenForTimeAttribute()
    {
        return $this->created_at->addDays(14)->diffForHumans(['parts' => 2], true);
    }

    public function getVotesNeededAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-votes-needed", 60 * 10, function () {

            $totalPillars = Pillar::isActive()->where('created_at', '<=', $this->created_at)->count();

            // New projects or open phases can be voted on by all pillars so creation date shouldnt be accounted for
            if (
                ($this instanceof AcceleratorProject && $this->status === AcceleratorProject::STATUS_NEW)
                || ($this instanceof AcceleratorPhase && $this->status === AcceleratorPhase::STATUS_OPEN)
            ) {
                $totalPillars = Pillar::isActive()->count();
            }

            return ceil($totalPillars * .33);
        });
    }

    public function getTotalMoreVotesNeededAttribute()
    {
        $needed = ($this->getVotesNeededAttribute() - $this->getTotalVotesAttribute());

        return max($needed, 0);
    }

    public function getTotalVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-votes", 60 * 10, function () {
            return $this->votes()
                ->count();
        });
    }

    public function getTotalYesVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-yes-votes", 60 * 10, function () {
            return $this->votes()
                ->where('is_yes', '1')
                ->count();
        });
    }

    public function getTotalNoVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-no-votes", 60 * 10, function () {
            return $this->votes()
                ->where('is_no', '1')
                ->count();
        });
    }

    public function getTotalAbstainVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-abstain-votes", 60 * 10, function () {
            return $this->votes()
                ->where('is_abstain', '1')
                ->count();
        });
    }

    public function getVotesPercentageAttribute()
    {
        return round((($this->getTotalVotesAttribute() * 100) / $this->getVotesNeededAttribute()));
    }

    public function getTotalYesVotesPercentageAttribute()
    {
        return round((($this->getTotalYesVotesAttribute() * 100) / $this->getVotesNeededAttribute()));
    }

    public function getTotalNoVotesPercentageAttribute()
    {
        return round((($this->getTotalNoVotesAttribute() * 100) / $this->getVotesNeededAttribute()));
    }

    public function getTotalAbstainVotesPercentageAttribute()
    {
        return round((($this->getTotalAbstainVotesAttribute() * 100) / $this->getVotesNeededAttribute()));
    }
}
