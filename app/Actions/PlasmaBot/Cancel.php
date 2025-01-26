<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Exceptions\PlasmaBotException;
use App\Exceptions\ZenonCliException;
use App\Models\PlasmaBotEntry;
use App\Services\ZenonCli\ZenonCli;
use Lorisleiva\Actions\Concerns\AsAction;

class Cancel
{
    use AsAction;

    public function __construct(
        private readonly ZenonCli $cli
    ) {
        $this->cli->setNodeUrl(config('services.plasma-bot.node'));
        $this->cli->setKeystore(config('services.plasma-bot.keystore'));
        $this->cli->setPassphrase(config('services.plasma-bot.passphrase'));
    }

    /**
     * @throws PlasmaBotException
     */
    public function handle(PlasmaBotEntry $entry): void
    {
        try {
            $this->cli->plasmaCancel($entry->hash);
            $entry->delete();
        } catch (ZenonCliException $e) {
            throw new PlasmaBotException($e->getMessage());
        }
    }
}
