<?php

declare(strict_types=1);

namespace App\Traits;

trait ModelCacheKeyTrait
{
    public function cacheKey(): string
    {
        return sprintf(
            '%s/%s-%s',
            $this->getTable(),
            $this->getKey(),
            $this->updated_at?->timestamp ?: $this->created_at->timestamp
        );
    }
}
