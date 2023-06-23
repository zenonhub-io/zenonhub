<?php

namespace App\Models\Markable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Favorite extends \Maize\Markable\Models\Favorite
{
    public $casts = [
        'value' => 'encrypted',
    ];

    public static function add(Model $markable, Model $user, ?string $value = null): self
    {
        static::validMarkable($markable);

        $attributes = [
            app(static::class)->getUserIdColumn() => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
            'value' => $value,
        ];
        $values = static::forceSingleValuePerUser()
            ? [Arr::pull($attributes, 'value')]
            : [];

        return static::firstOrCreate($attributes, $values);
    }
}
