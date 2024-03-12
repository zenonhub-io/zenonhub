<?php

declare(strict_types=1);

namespace App\Traits;

trait FindByColumnTrait
{
    public static function findBy(string $column, string $value): ?self
    {
        return static::where($column, $value)->first();
    }
}
