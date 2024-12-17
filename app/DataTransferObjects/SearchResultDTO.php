<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class SearchResultDTO extends Data
{
    public function __construct(
        public string $group,
        public string $title,
        public ?string $comment,
        public string $link,
    ) {}
}
