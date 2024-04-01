<?php

declare(strict_types=1);

namespace App\Traits;

trait ModelCacheKeyTrait
{
    public function cacheKey()
    {
        return sprintf(
            '%s/%s-%s',
            $this->getTable(),
            $this->getKey(),
            $this->updated_at->timestamp
        );
    }
}
