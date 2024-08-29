<?php

declare(strict_types=1);

namespace App\DataTransferObjects\Nom;

use Spatie\LaravelData\Data;

class VoteDTO extends Data
{
    public function __construct(
        public string $id,
        public int $total,
        public int $yes,
        public int $no,
    ) {
    }
}
