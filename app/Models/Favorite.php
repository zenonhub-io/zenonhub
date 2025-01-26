<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maize\Markable\Mark;

class Favorite extends Mark
{
    protected $casts = [
        'notes' => 'encrypted',
        'metadata' => 'array',
    ];

    public static function markableRelationName(): string
    {
        return 'favoriters';
    }

    public static function findExisting(Model $markable, ?Model $user): ?Model
    {
        if (! $user) {
            return null;
        }

        return static::where([
            app(static::class)->getUserIdColumn() => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
        ])->first();
    }
}
