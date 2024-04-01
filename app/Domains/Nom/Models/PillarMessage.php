<?php

declare(strict_types=1);

namespace App\Domains\Nom\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Mail\Markdown;
use Illuminate\Support\HtmlString;

class PillarMessage extends Model
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
    protected $table = 'nom_pillar_messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'pillar_id',
        'title',
        'post',
        'message',
        'signature',
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

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class);
    }

    //
    // Scopes

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('post', 'LIKE', "%{$search}%")
                ->orWhere('message', 'LIKE', "%{$search}%");
        });
    }

    //
    // Attributes

    public function getFormattedMessageAttribute(): HtmlString
    {
        $text = "```
# Proof of Pillar
Pillar: {$this->pillar->name}
Address: {$this->pillar->owner->address}
Public Key: {$this->pillar->owner->public_key}
Message: {$this->message}
Signature: {$this->signature}
```";

        return Markdown::parse($text);
    }
}
