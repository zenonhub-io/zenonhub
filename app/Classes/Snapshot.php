<?php

namespace App\Classes;

use App\Models\Nom\Momentum;
use Cache;
use DB;

use Illuminate\Console\OutputStyle;

class Snapshot
{
    protected Momentum $momentum;
    protected ?OutputStyle $console;

    public function __construct(Momentum $momentum, ?OutputStyle $console = null)
    {
        $this->console = $console;

        /*
         * transactions sent / receive
         * active addresses
         * stakes znn
         * delegated znn
         * fused qsr
         * average plasma usage
         *
         *
         * loop each embedded contract and pillar
         * znn in
         * znn out
         * transactions sent / receive
         */
    }
}
