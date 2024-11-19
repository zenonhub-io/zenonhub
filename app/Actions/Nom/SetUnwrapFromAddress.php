<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\BridgeUnwrap;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class SetUnwrapFromAddress
{
    use AsAction;

    public function handle(BridgeUnwrap $unwrap): void
    {
        Log::debug('Set unwrap from address - Start');

        if ($unwrap->from_address) {
            return;
        }

        $unwrap->load('bridgeNetwork');
        $unwrap->from_address = $this->getFromAddress($unwrap);
        $unwrap->save();
    }

    private function getFromAddress(BridgeUnwrap $unwrap): ?string
    {
        if ($unwrap->bridgeNetwork->name === 'Ethereum') {
            return Http::get('https://api.etherscan.io/api', [
                'module' => 'proxy',
                'action' => 'eth_getTransactionByHash',
                'txhash' => '0x' . $unwrap->transaction_hash,
                'apikey' => config('services.etherscan.api_key'),
            ])->json('result.from');
        }

        return null;
    }
}
