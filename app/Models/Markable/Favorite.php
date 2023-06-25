<?php

namespace App\Models\Markable;

class Favorite extends \Maize\Markable\Models\Favorite
{
    public $casts = [
        'value' => 'encrypted',
    ];
}
