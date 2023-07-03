<?php

namespace App\Models\Markable;

use Illuminate\Database\Eloquent\Model;

class Favorite extends \Maize\Markable\Models\Favorite
{
    public $casts = [
        //'label' => 'encrypted',
        'notes' => 'encrypted',
    ];

    public static function findExisting(Model $markable, Model $user)
    {
        return static::where([
            'user_id' => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
        ])->first();
    }

    public static function change(Model $markable, Model $user, ?string $value = null, ?array $additionalAttributes = null): bool
    {
        static::validMarkable($markable);

        $attributes = [
            'value' => $value,
        ];

        if (is_array($additionalAttributes)) {
            $attributes = array_merge($additionalAttributes, $attributes);
        }

        $favorite = static::findExisting($markable, $user);
        $favorite->fill($attributes);

        return $favorite->save();
    }
}
