<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Services\ZenonSdk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
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

    public function scopeWhereActive($query)
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

    public function getIsRevokableAttribute(?Carbon $dateTime): bool
    {
        $lockTimeWindow = config('nom.sentinel.lockTimeWindow');
        $revokeTimeWindow = config('nom.sentinel.revokeTimeWindow');
        $relativeTo = $dateTime ?? now();
        $epochTime = ($relativeTo->timestamp - $this->created_at->timestamp) % ($lockTimeWindow + $revokeTimeWindow);

        return $epochTime >= $lockTimeWindow;
    }

    public function getTimeUntilRevokableAttribute(?Carbon $dateTime): string
    {
        if ($this->getIsRevokableAttribute($dateTime)) {
            return 'Now';
        }

        $lockTimeWindow = config('nom.sentinel.lockTimeWindow');
        $revokeTimeWindow = config('nom.sentinel.revokeTimeWindow');
        $relativeTo = $dateTime ?? now();
        $epochTime = ($relativeTo->timestamp - $this->created_at->timestamp) % ($lockTimeWindow + $revokeTimeWindow);
        $revokeCooldown = $lockTimeWindow - $epochTime;

        return Carbon::parse($relativeTo)->addSeconds($revokeCooldown)->diffForHumans(['parts' => 2], true);
    }
}