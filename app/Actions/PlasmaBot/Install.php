<?php

namespace App\Actions\PlasmaBot;

use App;
use Spatie\QueueableAction\QueueableAction;

class Install
{
    use QueueableAction;

    public function execute(): void
    {
        $plasmaBot = App::make(\App\Services\PlasmaBot::class);
        $plasmaBot->install();
    }
}
