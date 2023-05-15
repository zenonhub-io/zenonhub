<?php

namespace App\Models\Nom;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Mail\Markdown;

class PillarMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'nom_pillar_messages';

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
        'pillar_id',
        'title',
        'post',
        'message',
        'signature',
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

    public function pillar(): BelongsTo
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
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

    public function getFormattedMessageAttribute()
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
