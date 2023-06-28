<?php

namespace App\Models\Markable;

use Illuminate\Database\Eloquent\Model;

class Favorite extends \Maize\Markable\Models\Favorite
{
    public $casts = [
        'notes' => 'encrypted',
    ];

    public function scopeWhereUser($query, $user)
    {
        $query->where('user_id', $user->getKey());
    }

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
        $attributes = [
            'value' => $value,
        ];

        if (is_array($additionalAttributes)) {
            $attributes = array_merge($additionalAttributes, $attributes);
        }

        $favorite = static::findExisting($markable, $user);

        foreach ($attributes as $key => $data) {
            $favorite->{$key} = $data;
        }

        return $favorite->save();
    }

    public static function remove(Model $markable, Model $user, ?string $value = null)
    {
        return static::where([
            app(static::class)->getUserIdColumn() => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
        ])->get()->each->delete();
    }
}
