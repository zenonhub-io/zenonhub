<?php

declare(strict_types=1);

namespace App\View\Components\Forms\Inputs;

class Email extends Input
{
    public function __construct(
        public string $name,
        public ?string $id = null,
        public ?string $value = '',
        public bool $required = false,
        public bool $readonly = false,
        public bool $disabled = false,
    ) {
        parent::__construct($name, 'email', $id, $value, $required, $readonly, $disabled);
    }
}
