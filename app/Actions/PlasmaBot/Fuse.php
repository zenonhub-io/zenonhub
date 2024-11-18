<?php

declare(strict_types=1);

namespace App\Actions\PlasmaBot;

use App\Exceptions\PlasmaBotException;
use App\Exceptions\ZenonCliException;
use App\Models\PlasmaBotEntry;
use App\Services\ZenonCli\ZenonCli;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class Fuse
{
    use AsAction;

    public function __construct(
        private readonly ZenonCli $cli
    ) {
        $this->cli->setKeystore(config('services.plasma-bot.keystore'));
        $this->cli->setPassphrase(config('services.plasma-bot.passphrase'));
    }

    /**
     * @throws PlasmaBotException
     */
    public function handle(string $address, int $amount, Carbon $expires): bool
    {
        try {
            $this->cli->plasmaFuse($address, $amount);
        } catch (ZenonCliException $e) {
            throw new PlasmaBotException($e->getMessage());
        }

        $account = load_account($address);

        PlasmaBotEntry::create([
            'account_id' => $account->id,
            'address' => $account->address,
            'amount' => $amount,
            'expires_at' => $expires,
        ]);
    }
}
