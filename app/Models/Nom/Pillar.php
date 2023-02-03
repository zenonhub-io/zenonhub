<?php

namespace App\Models\Nom;

use App;
use Cache;
use App\Models\PillarMessage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;

class Pillar extends Model implements Sitemapable
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_pillars';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    public $fillable = [
        'owner_id',
        'producer_id',
        'withdraw_id',
        'name',
        'slug',
        'qsr_burn',
        'weight',
        'produced_momentums',
        'expected_momentums',
        'missed_momentums',
        'give_momentum_reward_percentage',
        'give_delegate_reward_percentage',
        'az_engagement',
        'avg_momentums_produced',
        'total_momentums_produced',
        'is_legacy',
        'revoked_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'revoked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    //
    // config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url | string | array
    {
        return route('pillars.detail', ['slug' => $this->slug]);
    }

    /*
     * Relations
     */

    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function producer_account()
    {
        return $this->belongsTo(Account::class, 'producer_id', 'id');
    }

    public function withdraw_account()
    {
        return $this->belongsTo(Account::class, 'withdraw_id', 'id');
    }

    public function history()
    {
        return $this->hasMany(PillarHistory::class, 'pillar_id', 'id');
    }

    public function delegators()
    {
        return $this->hasMany(PillarDelegator::class, 'pillar_id', 'id');
    }

    public function az_project_votes()
    {
        return $this->hasMany(AcceleratorProjectVote::class, 'owner_id', 'owner_id');
    }

    public function az_phase_votes()
    {
        return $this->hasMany(AcceleratorPhaseVote::class, 'owner_id', 'owner_id');
    }

    public function messages()
    {
        return $this->hasMany(PillarMessage::class, 'pillar_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeIsActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeIsProducing($query)
    {
        return $query->where('missed_momentums', '<=', config('zenon.pillar_missed_momentum_limit'))
            ->whereNull('revoked_at');
    }

    public function scopeIsNotProducing($query)
    {
        return $query->where('missed_momentums', '>', config('zenon.pillar_missed_momentum_limit'));
    }

    public function scopeIsRevoked($query)
    {
        return $query->whereNotNull('revoked_at');
    }

    public function scopeIsTop30($query)
    {
        return $query->orderBy('weight', 'desc')
            ->limit(30);
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%");
    }


    /*
     * Attributes
     */

    public function getDisplayWeightAttribute()
    {
        $weight = $this->weight;

        if ($this->revoked_at) {
            $weight = $this->active_delegators->map(function ($delegator) {
                return $delegator->account->znn_balance;
            })->sum();
        }

        return short_number(znn_token()->getDisplayAmount($weight));
    }

    public function getDisplayQsrBurnAttribute()
    {
        return short_number(qsr_token()->getDisplayAmount($this->qsr_burn));
    }

    public function getRankAttribute()
    {
        return Cache::remember("pillar-{$this->id}-rank", 60*10, function () {
            if ($this->revoked_at || ! $this->weight) {
                return '-';
            }

            $pillars = self::whereNull('revoked_at')->orderBy('weight', 'desc')->get();
            $data = $pillars->where('id', $this->id);
            return $data->keys()->first() + 1;
        });
    }

    public function getProducedMomentumsPercentageAttribute()
    {
        if ($this->expected_momentums) {
            $percentage = ($this->produced_momentums * 100) / $this->expected_momentums;
            return round($percentage, 1);
        }

        return 0;
    }

    public function getActiveDelegatorsAttribute()
    {
        return $this->delegators()
            ->whereHas('account', function ($q) {
                $q->where('znn_balance', '>', '0');
            })
            ->whereNull('ended_at')
            ->get();
    }

    public function getActiveDelegatorsCountAttribute()
    {
        return $this->delegators()
            ->whereHas('account', function ($q) {
                $q->where('znn_balance', '>', '0');
            })
            ->whereNull('ended_at')
            ->count();
    }

    public function getPreviousHistoryAttribute()
    {
        return $this->history()->orderBy('updated_at', 'DESC')->offset(1)->limit(1)->first();
    }

    public function getDidRewardsChangeAttribute()
    {
        if (! $this->previous_history ||
            (
                $this->previous_history->give_momentum_reward_percentage !== $this->give_momentum_reward_percentage ||
                $this->previous_history->give_delegate_reward_percentage !== $this->give_delegate_reward_percentage
            )
        ) {
            return true;
        }

        return false;
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("pillar-{$this->id}-json", 10, function () {
            $znn = App::make('zenon.api');
            return $znn->pillar->getByOwner($this->owner->address)['data'];
        });
    }

    public function getIsProducingAttribute()
    {
        if (is_null($this->revoked_at) && $this->missed_momentums <= config('zenon.pillar_missed_momentum_limit')) {
            return true;
        }

        return false;
    }

    public function getAzStatusIndicatorAttribute()
    {
        if ($this->az_engagement < 1) {
            return 'danger';
        } elseif ($this->az_engagement < 75) {
            return 'warning';
        } else {
            return 'success';
        }
    }


    /*
     * Methods
     */

    public static function findBySlug(string $slug): ?Pillar
    {
        return static::where('slug', $slug)->first();
    }
}
