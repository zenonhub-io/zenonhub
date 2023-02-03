<?php

namespace App\Models\Nom;

use App;
use Cache;
use Str;
use App\Traits\AzVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;

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
        'owner_id',
        'hash',
        'name',
        'slug',
        'url',
        'description',
        'status',
        'znn_funds_needed',
        'qsr_funds_needed',
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
    // config

    public function toSitemapTag(): \Spatie\Sitemap\Tags\Url | string | array
    {
        return route('az.project', ['hash' => $this->hash]);
    }

    /*
     * Relations
     */

    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'id');
    }

    public function phases()
    {
        return $this->hasMany(AcceleratorPhase::class, 'accelerator_project_id', 'id');
    }

    public function latest_phase()
    {
        return $this->hasOne(AcceleratorPhase::class, 'accelerator_project_id', 'id')->latestOfMany();
    }

    public function votes()
    {
        return $this->hasMany(AcceleratorProjectVote::class, 'accelerator_project_id', 'id');
    }


    /*
     * Scopes
     */

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

    public function scopeIsComplete($query)
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


    /*
     * Attributes
     */

    public function getDisplayStatusAttribute()
    {
        if ($this->status === 0) {
            return 'New';
        } elseif ($this->status === 1) {
            return 'Accepted';
        } elseif ($this->status === 3) {
            return 'Rejected';
        } elseif ($this->status === 4) {
            return 'Complete';
        }
    }

    public function getDisplayColourStatusAttribute()
    {
        if ($this->status === 0) {
            return 'light';
        } elseif ($this->status === 1) {
            return 'primary';
        } elseif ($this->status === 3) {
            return 'danger';
        } elseif ($this->status === 4) {
            return 'success';
        }
    }

    public function getDisplayBadgeAttribute()
    {
        $text = $this->getDisplayStatusAttribute();
        $colour = $this->getDisplayColourStatusAttribute();
        return "<span class=\"badge bg-{$colour}\">{$text}</span>";
    }

    public function getOpenForTimeAttribute()
    {
        return $this->created_at->addDays(14)->diffForHumans(['parts' => 2], true);
    }

    public function getQuorumStautsAttribute()
    {
        if ($this->status === self::STATUS_NEW && $this->total_more_votes_needed > 0)
            return $this->total_more_votes_needed . ' '  . Str::plural('vote', $this->total_more_votes_needed) . ' needed in ' . $this->open_for_time;
        elseif($this->total_more_votes_needed > 0) {
            return 'Quorum not reached';
        } else {
            return 'Quorum reached';
        }
    }

    public function getDisplayZnnFundsNeededAttribute()
    {
        return znn_token()->getDisplayAmount($this->znn_funds_needed);
    }

    public function getDisplayQsrFundsNeededAttribute()
    {
        return qsr_token()->getDisplayAmount($this->qsr_funds_needed);
    }

    public function getDisplayUsdFundsAttribute()
    {
        $znn = float_number(znn_token()->getDisplayAmount($this->znn_funds_needed));
        $qsr = float_number(qsr_token()->getDisplayAmount($this->qsr_funds_needed));

        $znnPrice = znn_price();
        $qsrPrice = ($znnPrice / 10);

        $znnTotal = ($znnPrice * $znn);
        $qsrTotal = ($qsrPrice * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("project-{$this->id}-json", 10, function () {
            $znn = App::make('zenon.api');
            return $znn->accelerator->getProjectById($this->hash)['data'];
        });
    }

    /*
     * Methods
     */

    public static function findBySlug(string $slug): ?AcceleratorProject
    {
        return static::where('slug', $slug)->first();
    }

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }
}
