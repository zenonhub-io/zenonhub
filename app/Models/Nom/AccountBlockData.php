<?php

declare(strict_types=1);

namespace App\Models\Nom;

use Database\Factories\Nom\AccountBlockDataFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class AccountBlockData extends Model
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
    protected $table = 'nom_account_block_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'account_block_id',
        'raw',
        'decoded',
        'is_processed',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'decoded' => 'array',
            'is_processed' => 'boolean',
        ];
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return AccountBlockDataFactory::new();
    }

    //
    // Relations

    public function accountBlock(): BelongsTo
    {
        return $this->belongsTo(AccountBlock::class);
    }

    //
    // Attributes

    public function getIsJsonAttribute(): bool
    {
        if ($this->decoded) {
            return true;
        }

        try {
            $data = base64_decode($this->raw);
            json_encode($data, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return false;
        }

        return true;
    }

    public function getParsedAttribute(): string
    {
        $data = $this->decoded ?: base64_decode($this->raw);

        if ($this->getIsJsonAttribute()) {
            return json_encode($data, JSON_PRETTY_PRINT);
        }

        return $data;
    }
}
