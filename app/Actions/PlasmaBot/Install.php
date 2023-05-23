<?php

namespace App\Actions\PlasmaBot;

use App;
use App\Exceptions\ApplicationException;
use Log;
use Spatie\QueueableAction\QueueableAction;

class Install
{
    use QueueableAction;

    public function execute(): void
    {
        try {
            $plasmaBot = App::make(\App\Services\PlasmaBot::class);
            $plasmaBot->install();
        } catch (ApplicationException $exception) {
            Log::error($exception);
        }
    }
}
