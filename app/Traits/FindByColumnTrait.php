<?php

namespace App\Traits;

trait FindByColumnTrait
{
    public static function findBy(string $column, string $value) : ?self
    {
        return static::where($column, $value)->first();
    }
}
