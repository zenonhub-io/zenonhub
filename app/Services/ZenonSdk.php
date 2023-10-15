<?php

namespace App\Services;

use DigitalSloth\ZnnPhp\Zenon;

class ZenonSdk
{
    protected Zenon $zenon;

    public function __construct(
        private readonly ?string $node
    ) {
        $this->zenon = new Zenon($this->node, config('zenon.throw_api_errors'));
    }

    public function getZenon(): Zenon
    {
        return $this->zenon;
    }
}
