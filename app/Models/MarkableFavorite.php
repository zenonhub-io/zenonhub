<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Maize\Markable\Mark;

class MarkableFavorite extends Mark
{
    protected $table = 'markable_favorites';

    protected $casts = [
        'notes' => 'encrypted',
        'metadata' => 'array',
    ];

    public static function markableRelationName(): string
    {
        return 'favoriters';
    }

    public static function markRelationName(): string
    {
        return 'favorites';
    }

    public static function findExisting(Model $markable, Model $user): ?Model
    {
        return static::where([
            app(static::class)->getUserIdColumn() => $user->getKey(),
            'markable_id' => $markable->getKey(),
            'markable_type' => $markable->getMorphClass(),
        ])->first();
    }
}
