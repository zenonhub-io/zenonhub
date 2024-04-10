<?php

declare(strict_types=1);

namespace App\Traits;

trait FindByColumnTrait
{
    public static function findBy(string $column, string $value, bool $strict = false): ?self
    {
        if ($strict) {
            return static::where($column, $value)->sole();
        }

        return static::where($column, $value)->first();
    }
}
