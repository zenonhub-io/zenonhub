<?php

namespace App\Models\Nom;

use App\Services\ZenonSdk;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class Sentinel extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_sentinels';

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
        'active',
        'created_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

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

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->whereNull('revoked_at');
    }

    //
    // Attributes

    public function getRawJsonAttribute()
    {
        $cacheKey = "nom.sentinel.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->sentinel->getByOwner($this->owner->address)['data'][0];
            Cache::forever($cacheKey, $data);
        } catch (\Throwable $throwable) {
            $data = Cache::get($cacheKey);
        }

        return $data;
    }

    public function getDisplayRevocableInAttribute()
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
