<?php

declare(strict_types=1);

namespace App\Traits;

trait ModelCacheKeyTrait
{
    public function cacheKey(?string $name = null, $timestampColumn = 'created_at'): string
    {
        return sprintf(
            '%s/%s-%s|%s',
            $this->getTable(),
            $this->getKey(),
            $this->{$timestampColumn}?->timestamp ?? now()->timestamp,
            $name
        );
    }
}
