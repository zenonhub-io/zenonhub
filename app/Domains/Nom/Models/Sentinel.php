<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Services\ZenonSdk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Throwable;

class Sentinel extends Model
{
    use HasFactory;

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
    protected $table = 'nom_sentinels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'owner_id',
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
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    public function scopeWhereOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    //
    // Attributes

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.sentinel.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->sentinel->getByOwner($this->owner->address)['data'][0];
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
        }

        return $data;
    }

    public function getIsRevokableAttribute(): bool
    {
        $secsInDay = 24 * 60 * 60;
        $lockTimeWindow = 27 * $secsInDay;
        $revokeTimeWindow = 3 * $secsInDay;

        $epochTime = (now()->timestamp - $this->created_at->timestamp) % ($lockTimeWindow + $revokeTimeWindow);

        return $epochTime >= $lockTimeWindow;
    }

    public function getDisplayRevocableInAttribute(): string
    {
        if (! $this->raw_json) {
            return '-';
        }

        if ($this->raw_json?->revokeCooldown > 0) {
            return now()->addSeconds($this->raw_json->revokeCooldown)->diffForHumans(['parts' => 2], true);
        }

        return 'Now';
    }
}
