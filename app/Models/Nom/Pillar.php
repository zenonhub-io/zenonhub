<?php

namespace App\Models\Nom;

use App;
use App\Models\Markable\Favorite;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Maize\Markable\Markable;
use Spatie\Sitemap\Contracts\Sitemapable;

class Pillar extends Model implements Sitemapable
{
    use HasFactory, Markable;

    protected static array $marks = [
        Favorite::class,
    ];

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
        'chain_id',
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
        'momentum_rewards',
        'delegate_rewards',
        'az_engagement',
        'az_avg_vote_time',
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

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url|string|array
    {
        return route('pillars.detail', ['slug' => $this->slug]);
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class, 'chain_id', 'id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function producer_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'producer_id', 'id');
    }

    public function withdraw_account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'withdraw_id', 'id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(PillarHistory::class, 'pillar_id', 'id');
    }

    public function delegators(): HasMany
    {
        return $this->hasMany(PillarDelegator::class, 'pillar_id', 'id');
    }

    public function az_votes(): HasMany
    {
        return $this->hasMany(AcceleratorVote::class, 'pillar_id', 'id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(PillarMessage::class, 'pillar_id', 'id');
    }

    //
    // Scopes

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

    //
    // Attributes

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
        return Cache::remember("pillar-{$this->id}-rank", 60 * 10, function () {
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
                $this->previous_history->momentum_rewards !== $this->momentum_rewards ||
                $this->previous_history->delegate_rewards !== $this->delegate_rewards
            )
        ) {
            return true;
        }

        return false;
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("pillar-{$this->id}-json", 10, function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->pillar->getByOwner($this->owner->address)['data'];
            } catch (\Exception $exception) {
                return null;
            }
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

    public function getDisplayAzAvgVoteTimeAttribute()
    {
        if ($this->az_avg_vote_time) {
            return now()->subSeconds($this->az_avg_vote_time)->diffForHumans(['parts' => 2], true);
        }

        return '-';
    }

    //
    // Methods

    public static function findBySlug(string $slug): ?Pillar
    {
        return static::where('slug', $slug)->first();
    }

    public function updateAzEngagementScores()
    {
        $totalProjects = AcceleratorProject::where('created_at', '>=', $this->created_at)->count();
        $totalPhases = AcceleratorPhase::where('created_at', '>=', $this->created_at)->count();
        $totalVotableItems = ($totalProjects + $totalPhases);

        $this->az_engagement = 0;

        if ($totalVotableItems > 0) {

            // Make sure the vote item was created after the pillar
            // Projects/phases might be open after a pillar spawned, dont include these
            $votes = $this->az_votes()->get();
            $totalVotes = $votes->map(function ($vote) {
                if ($vote->votable->created_at >= $this->created_at) {
                    return 1;
                }

                return 0;
            })->sum();

            // If a pillar has more votes than projects ensure the pillar doenst get over 100% engagement
            if ($totalVotes > $totalVotableItems) {
                $totalVotes = $totalVotableItems;
            }

            $percentage = ($totalVotes * 100) / $totalVotableItems;
            $this->az_engagement = round($percentage, 1);
        }

        $averageVoteTime = $this->az_votes->map(function ($vote) {
            return $vote->created_at->timestamp - $vote->votable->created_at->timestamp;
        })->average();

        $this->az_avg_vote_time = $averageVoteTime;
        $this->save();
    }
}
