<?php

namespace App\Actions;

use App\Models\Nom\Contract;
use Spatie\QueueableAction\QueueableAction;

class UpdateContractMethods
{
    use QueueableAction;

    private array $contracts = [
        'Accelerator' => \DigitalSloth\ZnnPhp\Abi\Contracts\Accelerator::class,
        'Bridge' => \DigitalSloth\ZnnPhp\Abi\Contracts\Bridge::class,
        'Common' => \DigitalSloth\ZnnPhp\Abi\Contracts\Common::class,
        'Htlc' => \DigitalSloth\ZnnPhp\Abi\Contracts\Htlc::class,
        'Liquidity' => \DigitalSloth\ZnnPhp\Abi\Contracts\Liquidity::class,
        'Pillar' => \DigitalSloth\ZnnPhp\Abi\Contracts\Pillar::class,
        'Plasma' => \DigitalSloth\ZnnPhp\Abi\Contracts\Plasma::class,
        'Sentinel' => \DigitalSloth\ZnnPhp\Abi\Contracts\Sentinel::class,
        'Stake' => \DigitalSloth\ZnnPhp\Abi\Contracts\Stake::class,
        'Swap' => \DigitalSloth\ZnnPhp\Abi\Contracts\Swap::class,
        'Token' => \DigitalSloth\ZnnPhp\Abi\Contracts\Token::class,
    ];

    public function __construct(
        protected int $chain = 1
    ) {
    }

    public function execute(): void
    {
        foreach ($this->contracts as $contractName => $abiClass) {
            $abi = new $abiClass();
            $methods = $abi->getMethods();

            $contract = Contract::updateOrCreate([
                'chain_id' => $this->chain,
                'name' => $contractName,
            ], []);

            foreach ($methods as $method) {
                $contract->methods()->updateOrCreate([
                    'contract_id' => $contract->id,
                    'name' => $method,
                    'signature' => $abi->getMethodSignature($method),
                    'fingerprint' => $abi->getMethodFingerprint($method),
                ], []);
            }
        }

    }
}
