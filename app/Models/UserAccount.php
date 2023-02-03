<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class UserAccount extends Pivot
{
    protected $table = 'user_accounts_pivot';

    protected $casts = [
        'verified_at' => 'datetime'
    ];
}
