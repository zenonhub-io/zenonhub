<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\BridgeUnwrap;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class SetUnwrapFromAddress
{
    use AsAction;

    public string $commandSignature = 'nom:set-unwrap-from-addresses';

    public function handle(BridgeUnwrap $unwrap): void
    {
        Log::debug('Set unwrap from address - Start');

        if ($unwrap->from_address) {
            return;
        }

        if ($fromAddress = $this->getFromAddress($unwrap)) {
            $unwrap->update([
                'from_address' => $fromAddress,
            ]);
        }
    }

    public function asCommand(Command $command): void
    {
        $totalUnwraps = BridgeUnwrap::whereNotProcessed()->count();
        $progressBar = new ProgressBar(new ConsoleOutput, $totalUnwraps);
        $progressBar->start();

        BridgeUnwrap::whereNotProcessed()->chunk(1000, function (Collection $unwraps) use ($progressBar) {
            $unwraps->each(function ($unwrap) use ($progressBar) {
                $this->handle($unwrap);
                $progressBar->advance();
            });
        });

        $progressBar->finish();
    }

    private function getFromAddress(BridgeUnwrap $unwrap): ?string
    {
        // $supportedNetworks = ['Ethereum', 'BNB Chain'];
        $supportedNetworks = ['Ethereum'];
        $unwrap->load('bridgeNetwork');

        if (! in_array($unwrap->bridgeNetwork->name, $supportedNetworks, true)) {
            return null;
        }

        return Http::get('https://api.etherscan.io/v2/api', [
            'chainid' => $unwrap->bridgeNetwork->chain_identifier,
            'module' => 'proxy',
            'action' => 'eth_getTransactionByHash',
            'txhash' => '0x' . $unwrap->transaction_hash,
            'apikey' => config('services.etherscan.api_key'),
        ])->json('result.from');
    }
}
