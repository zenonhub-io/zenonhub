<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BridgeGuardian extends Model
{
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
    protected $table = 'nom_bridge_guardians';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_id',
        'nominated_at',
        'accepted_at',
        'revoked_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta_data' => 'array',
            'nominated_at' => 'datetime',
            'accepted_at' => 'datetime',
            'revoked_at' => 'datetime',
        ];
    }

    public static function setNewGuardians(array $guardianAddresses, Carbon $timestamp): Collection
    {
        return DB::transaction(function () use ($guardianAddresses, $timestamp) {
            self::where('accepted_at')->update([
                'revoked_at' => $timestamp,
            ]);

            $newGuardians = collect();
            foreach ($guardianAddresses as $address) {
                $account = load_account($address);

                $guardian = self::create([
                    'account_id' => $account->id,
                    'nominated_at' => $timestamp,
                    'accepted_at' => $timestamp,
                ]);

                $newGuardians->push($guardian);
            }

            return $newGuardians;
        });
    }

    //
    // Relations

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeWhereActive($query)
    {
        return $query->whereNotNull('accepted_at')->whereNull('revoked_at');
    }

    public function scopeWhereProposed($query)
    {
        return $query->whereNull('accepted_at');
    }
}
