<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use App\Domains\Nom\Enums\AccountBlockTypesEnum;
use App\Domains\Nom\Services\ZenonSdk;
use App\Models\Markable\Favorite;
use App\Traits\FindByColumnTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Cache;
use Maize\Markable\Markable;
use Throwable;

class AccountBlock extends Model
{
    //use HasFactory, Markable;
    use FindByColumnTrait, HasFactory;

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
    protected $table = 'nom_account_blocks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'chain_id',
        'account_id',
        'to_account_id',
        'momentum_id',
        'parent_id',
        'paired_account_block_id',
        'token_id',
        'contract_method_id',
        'version',
        'block_type',
        'height',
        'amount',
        'fused_plasma',
        'base_plasma',
        'used_plasma',
        'difficulty',
        'nonce',
        'hash',
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
            'block_type' => AccountBlockTypesEnum::class,
        ];
    }

    //
    // Relations

    public function chain(): BelongsTo
    {
        return $this->belongsTo(Chain::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function toAccount(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function momentum(): BelongsTo
    {
        return $this->belongsTo(Momentum::class);
    }

    public function pairedAccountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    public function descendants(): HasMany
    {
        return $this->hasMany(AccountBlock::class, 'parent_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    public function token(): BelongsTo
    {
        return $this->belongsTo(Token::class);
    }

    public function contractMethod(): BelongsTo
    {
        return $this->belongsTo(ContractMethod::class);
    }

    public function data(): HasOne
    {
        return $this->hasOne(AccountBlockData::class);
    }

    //
    // Scopes

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('hash', $search);
        });
    }

    public function scopeCreatedLast($query, ?string $limit)
    {
        if ($limit) {
            return $query->where('created_at', '>=', $limit);
        }

        return $query;
    }

    public function scopeCreatedBetweenDates($query, array $dates)
    {
        $start = ($dates[0] instanceof Carbon) ? $dates[0] : Carbon::parse($dates[0]);
        $end = ($dates[1] instanceof Carbon) ? $dates[1] : Carbon::parse($dates[1]);

        return $query->whereBetween('created_at', [
            $start->startOfDay(),
            $end->endOfDay(),
        ]);
    }

    public function scopeInvolvingAccount($query, $account)
    {
        return $query->where(function ($q) use ($account) {
            $q->where('account_id', $account->id)
                ->orWhere('to_account_id', $account->id);
        });
    }

    public function scopeIsReceived($query)
    {
        return $query->whereNotNull('paired_account_block_id');
    }

    public function scopeIsUnreceived($query)
    {
        return $query->whereNull('paired_account_block_id');
    }

    public function scopeNotToEmpty($query)
    {
        return $query->where('to_account_id', '!=', '1');
    }

    public function scopeNotFromPillarProducer($query)
    {
        $producerIds = Account::getAllPillarProducerAddresses();

        return $query->whereNotIn('account_id', $producerIds);
    }

    public function scopeNotContractUpdate($query)
    {
        return $query->where(function ($q) {
            $q->whereNotIn('contract_method_id', [36, 68]) // Ignore update contract calls
                ->orWhereNull('contract_method_id');
        });
    }

    //
    // Attributes

    public function getDisplayTypeAttribute(): ?string
    {
        if ($this->contractMethod) {
            return $this->contractMethod->name;
        }

        if ($this->amount > 0) {
            return 'Transfer';
        }

        return null;
    }

    public function getDisplayAmountAttribute(): string
    {
        return $this->token?->getFormattedAmount($this->amount);
    }

    public function getDisplayHeightAttribute(): string
    {
        return number_format($this->height);
    }

    public function getNextBlockAttribute(): ?Model
    {
        return self::where('account_id', $this->account_id)
            ->where('height', ($this->height + 1))
            ->first();
    }

    public function getPreviousBlockAttribute(): ?Model
    {
        return self::where('account_id', $this->account_id)
            ->where('height', ($this->height - 1))
            ->first();
    }

    public function getRawJsonAttribute(): array
    {
        $updateCache = true;
        $cacheKey = "nom.accountBlock.rawJson.{$this->id}";

        try {
            $data = app(ZenonSdk::class)->getAccountBlockByHash($this->hash);
        } catch (Throwable $throwable) {
            $updateCache = false;
            $data = Cache::get($cacheKey);
        }

        if ($updateCache) {
            Cache::forever($cacheKey, $data);
        }

        return $data->toJson();
    }

    public function getIsUnReceivedAttribute(): bool
    {
        return ! $this->paired_account_block_id;
    }

    public function getIsFavouritedAttribute(): bool
    {
        if ($user = auth()->user()) {
            return Favorite::findExisting($this, $user);
        }

        return false;
    }
}