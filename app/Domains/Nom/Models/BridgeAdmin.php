<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class BridgeAdmin extends Model
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
    protected $table = 'nom_bridge_admins';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_id',
        'nominated_by_id',
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

    public static function setNewAdmin(Account $account, Carbon $timestamp): BridgeAdmin
    {
        return DB::transaction(function () use ($account, $timestamp) {
            // Delete old nominations
            self::whereNull('accepted_at')->delete();

            // Revoke current admin
            $currentAdmin = self::getActiveAdmin();
            $currentAdmin->revoked_at = $timestamp;
            $currentAdmin->save();

            // Create new admin
            return self::create([
                'account_id' => $account->id,
                'accepted_at' => $timestamp,
            ]);
        });
    }

    //
    // Methods

    public static function getActiveAdmin(): BridgeAdmin
    {
        return static::getActive()->sole();
    }

    //
    // Relations

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function nominatedBy(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    //
    // Scopes

    public function scopeIsActive($query)
    {
        return $query->whereNotNull('accepted_at')->whereNull('revoked_at');
    }

    public function scopeIsProposed($query)
    {
        return $query->whereNull('accepted_at');
    }
}
