<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Models\Markable\Favorite;
use App\Services\ZenonSdk;
use App\Traits\FindByColumnTrait;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Maize\Markable\Markable;
use Throwable;

class Momentum extends Model
{
    use FindByColumnTrait, HasFactory;
    //use HasFactory, Markable;

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
    protected $table = 'nom_momentums';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'producer_account_id',
        'producer_pillar_id',
        'version',
        'height',
        'hash',
        'data',
        'created_at',
    ];

    protected static array $marks = [
        Favorite::class,
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

    public function producerAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function producerPillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    public function accountBlocks(): HasMany
    {
        return $this->hasMany(AccountBlock::class);
    }

    //
    // Scopes

    public function scopeWhereListSearch($query, $search)
    {
        if (is_numeric($search)) {
            return $query->where('height', $search);
        }

        return $query->where('hash', $search);
    }

    //
    // Attributes

    public function getDecodedDataAttribute(): string
    {
        $data = base64_decode($this->data);

        return ZnnUtilities::toHex($data);
    }

    public function getDisplayHeightAttribute(): string
    {
        return number_format($this->height);
    }

    public function getNextMomentumAttribute(): ?Model
    {
        return self::where('height', ($this->height + 1))->first();
    }

    public function getPreviousMomentumAttribute(): ?Model
    {
        self::where('height', ($this->height - 1))->first();
    }

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.momentum.rawJson.{$this->id}";

        try {
            $znn = App::make(ZenonSdk::class);
            $data = $znn->ledger->getMomentumByHash($this->hash)['data'];
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
        }

        return $data;
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }
}
