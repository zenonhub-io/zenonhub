<?php

namespace App\Models\Nom;

use App;
use App\Models\Markable\Favorite;
use App\Traits\AzVotes;
use Cache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Maize\Markable\Markable;
use Str;

class AcceleratorPhase extends Model
{
    use AzVotes, HasFactory, Markable;

    public const STATUS_OPEN = 0;

    public const STATUS_PAID = 2;

    protected static array $marks = [
        Favorite::class,
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_phases';

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
        'accelerator_project_id',
        'hash',
        'owner',
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
        'accepted_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'send_reminders_at' => 'datetime',
        'accepted_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(AcceleratorProject::class, 'accelerator_project_id', 'id');
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(AcceleratorVote::class, 'votable');
    }

    //
    // Scopes

    public function scopeReminderDue($query)
    {
        return $query->whereNotNull('send_reminders_at')
            ->where('send_reminders_at', '<', now());
    }

    //
    // Attributes

    public function getDisplayStatusAttribute()
    {
        if ($this->status === self::STATUS_OPEN) {
            return 'Open';
        }

        if ($this->status === self::STATUS_PAID) {
            return 'Paid';
        }
    }

    public function getDisplayColourStatusAttribute()
    {
        if ($this->status === self::STATUS_OPEN) {
            return 'primary';
        }

        if ($this->status === self::STATUS_PAID) {
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
        if ($this->total_more_votes_needed > 0) {
            return $this->total_more_votes_needed.' '.Str::plural('vote', $this->total_more_votes_needed).' needed';
        }

        return 'Quorum reached';
    }

    public function getIsQuorumReachedAttribute()
    {
        return ! ($this->total_more_votes_needed > 0);
    }

    public function getPhaseNumberAttribute()
    {
        return $this->project->phases->pluck('id')->sort()->search($this->id) + 1;
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
            $znnPrice = App::make('coingeko.api')->historicPrice('zenon-2', 'usd', $this->created_at->timestamp);
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
        return Cache::remember("phase-{$this->id}-json", 10, function () {
            try {
                $znn = App::make('zenon.api');

                return $znn->accelerator->getPhaseById($this->hash)['data'];
            } catch (\Exception $exception) {
                return null;
            }
        });
    }

    public function getIsFavouritedAttribute()
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }

    //
    // Methods

    public static function findBySlug(string $slug): ?AcceleratorPhase
    {
        return static::where('slug', $slug)->first();
    }

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }
}
