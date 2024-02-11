<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserNomAccount extends Pivot
{
    protected $table = 'user_nom_accounts_pivot';

    protected $casts = [
        'verified_at' => 'datetime',
    ];
}
