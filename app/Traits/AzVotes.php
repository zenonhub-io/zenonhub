<?php

namespace App\Traits;

use Cache;
use Str;
use App\Models\Nom\Pillar;

trait AzVotes
{
    private function getCachePrefix()
    {
        $cachePrefix = Str::replace('App\Models\Nom\\', '', __CLASS__);
        return Str::snake($cachePrefix) . '-' . $this->id;
    }

    public function getVotesNeededAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-votes-needed", 60*10, function () {
            $totalPillars = Pillar::isActive()->where('created_at', '<=', $this->created_at)->count();
            return ceil($totalPillars * .33);
        });
    }

    public function getTotalMoreVotesNeededAttribute()
    {
        $needed = ($this->getVotesNeededAttribute() - $this->getTotalVotesAttribute());
        return (max($needed, 0));
    }

    public function getTotalVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-votes", 60*10, function () {
            return $this->votes()
                ->count();
        });
    }

    public function getTotalYesVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-yes-votes", 60*10, function () {
            return $this->votes()
                ->where('is_yes', '1')
                ->count();
        });
    }

    public function getTotalNoVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-no-votes", 60*10, function () {
            return $this->votes()
                ->where('is_no', '1')
                ->count();
        });
    }

    public function getTotalAbstainVotesAttribute()
    {
        return Cache::remember("{$this->getCachePrefix()}-total-abstain-votes", 60*10, function () {
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
