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

        $unwrap->load('bridgeNetwork');
        $unwrap->from_address = $this->getFromAddress($unwrap);
        $unwrap->save();
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
