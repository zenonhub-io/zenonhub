<?php

namespace App\Actions\WhaleAlerts;

use Spatie\QueueableAction\QueueableAction;

class SendDiscord
{
    use QueueableAction;

    public function __construct()
    {
    }

    public function execute(): void
    {

    }
}
