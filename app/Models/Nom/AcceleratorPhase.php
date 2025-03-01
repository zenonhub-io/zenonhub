<?php

declare(strict_types=1);

namespace App\Models\Nom;

use App\DataTransferObjects\Nom\AcceleratorPhaseDTO;
use App\Enums\Nom\AcceleratorPhaseStatusEnum;
use App\Services\ZenonSdk\ZenonSdk;
use App\Traits\ModelCacheKeyTrait;
use Database\Factories\Nom\AcceleratorPhaseFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Throwable;

class AcceleratorPhase extends Model implements Sitemapable
{
    use AcceleratorFundingTrait, AcceleratorVotesTrait, HasFactory, ModelCacheKeyTrait, Searchable;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_accelerator_phases';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'project_id',
        'hash',
        'name',
        'slug',
        'url',
        'description',
        'status',
        'phase_number',
        'znn_requested',
        'qsr_requested',
        'znn_price',
        'qsr_price',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'status' => AcceleratorPhaseStatusEnum::class,
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AcceleratorPhaseFactory::new();
    }

    //
    // Config

    public function toSitemapTag(): Url|string|array
    {
        return route('accelerator-z.phase.detail', ['hash' => $this->hash]);
    }

    /**
     * {@inheritDoc}
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
        ];
    }

    //
    // Relations

    public function project(): BelongsTo
    {
        return $this->belongsTo(AcceleratorProject::class);
    }

    public function votes(): MorphMany
    {
        return $this->morphMany(Vote::class, 'votable');
    }

    //
    // Scopes

    public function scopeWhereOpen($query)
    {
        return $query->where('status', AcceleratorPhaseStatusEnum::OPEN->value);
    }

    public function scopeWherePaid($query)
    {
        return $query->where('status', AcceleratorPhaseStatusEnum::PAID->value);
    }

    //
    // Attributes

    public function getQuorumStatusAttribute(): string
    {
        if (! $this->is_quorum_reached) {
            $votesText = Str::plural('vote', $this->total_more_votes_needed);

            return "{$this->total_more_votes_needed} {$votesText} needed";
        }

        return 'Quorum reached';
    }

    public function getRawJsonAttribute(): AcceleratorPhaseDTO|array
    {
        try {
            return app(ZenonSdk::class)->getPhaseById($this->hash);
        } catch (Throwable $throwable) {
            return ['error' => __('Data unavailable, please try again')];
        }
    }
}
