<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Exceptions\PlasmaBotException;
use App\Exceptions\ZenonCliException;
use App\Services\ZenonCli\ZenonCli;
use Lorisleiva\Actions\Concerns\AsAction;

class ReceiveAll
{
    use AsAction;

    public string $commandSignature = 'plasma-bot:receive-all';

    public function __construct(
        private readonly ZenonCli $cli
    ) {
        $this->cli->setKeystore(config('services.plasma-bot.keystore'));
        $this->cli->setPassphrase(config('services.plasma-bot.passphrase'));
    }

    /**
     * @throws PlasmaBotException
     */
    public function handle(): void
    {
        try {
            $this->cli->receiveAll();
        } catch (ZenonCliException $e) {
            throw new PlasmaBotException($e->getMessage());
        }
    }
}
