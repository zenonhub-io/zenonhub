<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Exceptions\ZenonCliException;
use App\Services\ZenonCli\ZenonCli;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class ReceiveAll
{
    use AsAction;

    public string $commandSignature = 'plasma-bot:receive-all';

    public function __construct(
        private readonly ZenonCli $cli
    ) {
        $this->cli->setNodeUrl(config('services.plasma-bot.node'));
        $this->cli->setKeystore(config('services.plasma-bot.keystore'));
        $this->cli->setPassphrase(config('services.plasma-bot.passphrase'));
    }

    public function handle(): void
    {
        try {
            $this->cli->receiveAll();
        } catch (ZenonCliException $e) {
            Log::error('Plasma Bot - Error receiving all');
        }
    }
}
