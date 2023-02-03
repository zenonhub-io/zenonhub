<?php

namespace App\Models;

use App\Models\Nom\Account;
use Illuminate\Database\Eloquent\Relations\Pivot;

class PillarMessageVote extends Pivot
{
    protected $table = 'pillar_message_votes';

    protected $casts = [
        'created_at' => 'datetime'
    ];


    /*
     * Relations
     */

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'id');
    }
}
