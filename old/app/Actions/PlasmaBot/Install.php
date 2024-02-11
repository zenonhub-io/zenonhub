<?php

namespace App\Actions\PlasmaBot;

use Illuminate\Support\Facades\App;
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
