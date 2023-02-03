<?php

namespace App\Models;

use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Markdown;

class PillarMessage extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pillar_messages';

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
        'is_public',
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


    /*
     * Relations
     */

    public function pillar()
    {
        return $this->belongsTo(Pillar::class, 'pillar_id', 'id');
    }

    public function vote_options()
    {
        return $this->hasMany(PillarMessageVoteOption::class, 'pillar_messages_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(PillarMessageVote::class, 'pillar_messages_id', 'id');
    }


    /*
     * Scopes
     */

    public function scopeWhereListSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('title', 'LIKE', "%{$search}%")
                ->orWhere('post', 'LIKE', "%{$search}%")
                ->orWhere('message', 'LIKE', "%{$search}%");
        });
    }

    /*
     * Attributes
     */

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
