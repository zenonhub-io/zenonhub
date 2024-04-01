<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use Illuminate\Support\Facades\App;
use Spatie\QueueableAction\QueueableAction;

class Install
{
    use QueueableAction;

    public function execute(): void
    {
        $plasmaBot = App::make(\App\Domains\Nom\Services\PlasmaBot::class);
        $plasmaBot->install();
    }
}
