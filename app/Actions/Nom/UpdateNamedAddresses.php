<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use Lorisleiva\Actions\Concerns\AsAction;

class UpdateNamedAddresses
{
    use AsAction;

    public string $commandSignature = 'nom:update-named-addresses';

    public function handle(): void
    {
        $namedAccounts = config('explorer.named_accounts');

        foreach ($namedAccounts as $address => $name) {
            load_account($address, $name);
        }
    }
}
