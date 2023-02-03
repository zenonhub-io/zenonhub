<?php

namespace App\Models;

use App\Models\Nom\Pillar;
use Illuminate\Database\Eloquent\Model;

class PillarMessageVoteOption extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pillar_message_vote_options';

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
        'pillar_messages_id',
        'name',
    ];


    /*
     * Relations
     */

    public function pillar_message()
    {
        return $this->belongsTo(PillarMessage::class, 'pillar_messages_id', 'id');
    }

    public function votes()
    {
        return $this->hasMany(PillarMessageVote::class, 'vote_option_id', 'id');
    }
}
