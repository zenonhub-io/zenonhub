<?php

namespace App\Models\Nom;

use App;
use Cache;
use Str;
use App\Traits\AzVotes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcceleratorPhase extends Model
{
    use HasFactory;
    use AzVotes;

    public const STATUS_OPEN = 0;
    public const STATUS_PAID = 2;

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
        'accelerator_project_id',
        'hash',
        'owner',
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


    /*
     * Relations
     */

    public function project()
    {
        return $this->belongsTo(AcceleratorProject::class, 'accelerator_project_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(AcceleratorPhaseVote::class, 'accelerator_phase_id', 'id');
    }


    /*
     * Scopes
     */

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
        if ($this->status === self::STATUS_OPEN) {
            return 'Open';
        } elseif ($this->status === self::STATUS_PAID) {
            return 'Paid';
        }
    }

    public function getDisplayColourStatusAttribute()
    {
        if ($this->status === self::STATUS_OPEN) {
            return 'primary';
        } elseif ($this->status === self::STATUS_PAID) {
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
        return $this->created_at->addWeeks(2)->diffForHumans(['parts' => 2], true);
    }

    public function getQuorumStauts()
    {
        if ($this->total_more_votes_needed > 0) {
            return $this->total_more_votes_needed . ' '  . Str::plural('vote', $this->total_more_votes_needed) . ' needed';
        } else {
            return 'Quorum reached';
        }
    }

    public function getPhaseNumberAttribute()
    {
        return ($this->project->phases->pluck('id')->sort()->search($this->id) + 1);
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

        $znnPrice = (float) znn_price();
        $qsrPrice = ($znnPrice / 10);

        $znnTotal = ($znnPrice * $znn);
        $qsrTotal = ($qsrPrice * $qsr);

        return number_format(($znnTotal + $qsrTotal), 2);
    }

    public function getRawJsonAttribute()
    {
        return Cache::remember("phase-{$this->id}-json", 10, function () {
            $znn = App::make('zenon.api');
            return $znn->accelerator->getPhaseById($this->hash)['data'];
        });
    }


    /*
     * Methods
     */

    public static function findBySlug(string $slug): ?AcceleratorPhase
    {
        return static::where('slug', $slug)->first();
    }

    public static function findByHash($hash)
    {
        return static::where('hash', $hash)->first();
    }
}


