<?php

namespace App\Models\Nom;

use App;
use App\Traits\AzVotes;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Log;
use Spatie\Sitemap\Contracts\Sitemapable;
use Str;

class AcceleratorProject extends Model implements Sitemapable
{
    use HasFactory;
    use AzVotes;

    public const STATUS_NEW = 0;

    public const STATUS_ACCEPTED = 1;

    public const STATUS_REJECTED = 3;

    public const STATUS_COMPLETE = 4;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_projects';

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
        'hash',
        'name',
        'slug',
        'url',
        'description',
        'status',
        'znn_requested',
        'qsr_requested',
        'znn_price',
        'qsr_price',
        'vote_total',
        'vote_yes',
        'vote_no',
        'send_reminders_at',
        'created_at',
        'updated_at',
        'modified_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'send_reminders_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'modified_at' => 'datetime',
    ];

    //
    // Config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url|string|array
    {
        return route('az.project', ['hash' => $this->hash]);
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(AcceleratorPhase::class, 'accelerator_project_id', 'id');
    }

    public function latest_phase(): HasOne
    {
        return $this->hasOne(AcceleratorPhase::class, 'accelerator_project_id', 'id')->latestOfMany();
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(AcceleratorVote::class, 'votable');
    }

    //
    // Scopes

    public function scopeIsNew($query)
    {
        return $query->where('status', self::STATUS_NEW);
    }

    public function scopeIsAccepted($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeIsRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeIsCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETE);
    }

    public function scopeNeedsVotes($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_ACCEPTED])
            ->whereHas('phases', function ($q) {
                $q->where('status', AcceleratorPhase::STATUS_OPEN);
            });
    }

    public function scopeIsOpen($query)
    {
        return $query->whereIn('status', [self::STATUS_NEW, self::STATUS_ACCEPTED])
            ->whereHas('phases');
    }

    public function scopeAwaitingPhases($query)
    {
        return $query->where('status', self::STATUS_ACCEPTED)
            ->doesntHave('phases');
    }

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('url', 'LIKE', "%{$search}%")
                ->orWhere('hash', '=', "{$search}");
        });
    }

    public function scopeOrderByLatest($query)
    {
        return $query->orderBy('modified_at', 'desc');
    }

    public function scopeReminderDue($query)
    {
        return $query->whereNotNull('send_reminders_at')
            ->where('send_reminders_at', '<', now());
    }

    //
    // Attributes

    public function getDisplayStatusAttribute()
    {
        if ($this->status === self::STATUS_NEW) {
            return 'New';
        }

        if ($this->status === self::STATUS_ACCEPTED) {
            return 'Accepted';
        }

        if ($this->status === self::STATUS_REJECTED) {
            return 'Rejected';
        }

        if ($this->status === self::STATUS_COMPLETE) {
            return 'Completed';
        }
    }

    public function getDisplayColourStatusAttribute()
    {
        if ($this->status === self::STATUS_NEW) {
            return 'light';
        }

        if ($this->status === self::STATUS_ACCEPTED) {
            return 'primary';
        }

        if ($this->status === self::STATUS_REJECTED) {
            return 'danger';
        }

        if ($this->status === self::STATUS_COMPLETE) {
            return 'success';
        }
    }

    public function getDisplayBadgeAttribute()
    {
        $text = $this->getDisplayStatusAttribute();
        $colour = $this->getDisplayColourStatusAttribute();

        return "<span class=\"badge bg-{$colour}\">{$text}</span>";
    }

    public function getQuorumStautsAttribute()
    {
        if ($this->status === self::STATUS_NEW && $this->total_more_votes_needed > 0) {
            return $this->total_more_votes_needed.' '.Str::plural('vote', $this->total_more_votes_needed).' needed in '.$this->open_for_time;
        }

        if ($this->total_more_votes_needed > 0) {
            return 'Quorum not reached';
        }

        return 'Quorum reached';
    }

    public function getIsQuorumReachedAttribute()
    {
        return ! ($this->total_more_votes_needed > 0);
    }

    public function getDisplayZnnRequestedAttribute()
    {
        return znn_token()->getDisplayAmount($this->znn_requested);
    }

    public function getDisplayQsrRequestedAttribute()
    {
        return qsr_token()->getDisplayAmount($this->qsr_requested);
    }

    public function getDisplayUsdRequestedAttribute()
    {
        if (! $this->znn_price || ! $this->qsr_price) {
            $znnPrice = App::make('coingeko.api')->historicPrice('zenon', 'usd', $this->created_at->timestamp);
            $qsrPrice = App::make('coingeko.api')->historicPrice('quasar', 'usd', $this->created_at->timestamp);

            // Projects created before QSR price available
            if (is_null($qsrPrice) && $znnPrice > 0) {
                $qsrPrice = $znnPrice / 10;
            }

            if ($znnPrice > 0) {
                $this->znn_price = $znnPrice;
                $this->saveQuietly();
            }

            if ($qsrPrice > 0) {
                $this->qsr_price = $qsrPrice;
                $this->saveQuietly();
            }
        }

        $znn = float_number(znn_token()->getDisplayAmount($this->znn_requested));
        $qsr = float_number(qsr_token()->getDisplayAmount($this->qsr_requested));

        $znnTotal = ($this->znn_price * $znn);
        $qsrTotal = ($this->qsr_price * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("project-{$this->id}-json", 10, function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->accelerator->getProjectById($this->hash)['data'];
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    //
    // Methods

    public static function findBySlug(string $slug): ?AcceleratorProject
    {
        return static::where('slug', $slug)->first();
    }

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }

    public function syncProjectStatus()
    {
        try {
            $znn = App::make('zenon.api');
            $data = $znn->accelerator->getProjectById($this->hash)['data'];

            $this->vote_total = $data->votes->total;
            $this->vote_yes = $data->votes->yes;
            $this->vote_no = $data->votes->no;
            $this->status = $data->status;
            $this->modified_at = $data->lastUpdateTimestamp;
            $this->updated_at = $data->lastUpdateTimestamp;
            $this->save();

        } catch (\Exception $exception) {
            Log::error('Unable to save project status');
            throw $exception;
        }
    }
}
