<?php

declare(strict_types=1);

namespace App\Actions\Nom;

use App\Models\Nom\Account;
use App\Models\Nom\Contract;
use App\Models\Nom\ContractMethod;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateContractMethods
{
    use AsAction;

    public string $commandSignature = 'nom:update-contract-methods';

    private array $contracts = [
        'Accelerator' => \DigitalSloth\ZnnPhp\Abi\Contracts\Accelerator::class,
        'Bridge' => \DigitalSloth\ZnnPhp\Abi\Contracts\Bridge::class,
        'Common' => \DigitalSloth\ZnnPhp\Abi\Contracts\Common::class,
        'Htlc' => \DigitalSloth\ZnnPhp\Abi\Contracts\Htlc::class,
        'Liquidity' => \DigitalSloth\ZnnPhp\Abi\Contracts\Liquidity::class,
        'Pillar' => \DigitalSloth\ZnnPhp\Abi\Contracts\Pillar::class,
        'Plasma' => \DigitalSloth\ZnnPhp\Abi\Contracts\Plasma::class,
        'Sentinel' => \DigitalSloth\ZnnPhp\Abi\Contracts\Sentinel::class,
        'Spork' => \DigitalSloth\ZnnPhp\Abi\Contracts\Spork::class,
        'Stake' => \DigitalSloth\ZnnPhp\Abi\Contracts\Stake::class,
        'Swap' => \DigitalSloth\ZnnPhp\Abi\Contracts\Swap::class,
        'Token' => \DigitalSloth\ZnnPhp\Abi\Contracts\Token::class,
    ];

    public function handle(): void
    {
        foreach ($this->contracts as $contractName => $abiClass) {
            $abi = new $abiClass;
            $methods = $abi->getMethods();

            $contract = Contract::updateOrCreate([
                'name' => $contractName,
                'chain_id' => app('currentChain')->id,
            ], [
                'account_id' => Account::where('name', 'LIKE', "{$contractName}%")
                    ->where('is_embedded_contract', 1)
                    ->first()?->id,
            ]);

            foreach ($methods as $method) {
                ContractMethod::updateOrCreate([
                    'contract_id' => $contract->id,
                    'name' => $method,
                ], [
                    'signature' => $abi->getMethodSignature($method),
                    'fingerprint' => $abi->getMethodFingerprint($method),
                ]);
            }
        }
    }
}
