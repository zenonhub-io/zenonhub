<?php

declare(strict_types=1);

namespace App\Domains\Nom\Services\Sdk;

use App\Domains\Nom\Models\ContractMethod;
use Illuminate\Support\Str;

trait Abi
{
    protected array $abiContracts = [
        'accelerator' => \DigitalSloth\ZnnPhp\Abi\Contracts\Accelerator::class,
        'bridge' => \DigitalSloth\ZnnPhp\Abi\Contracts\Bridge::class,
        'common' => \DigitalSloth\ZnnPhp\Abi\Contracts\Common::class,
        'htlc' => \DigitalSloth\ZnnPhp\Abi\Contracts\Htlc::class,
        'liquidity' => \DigitalSloth\ZnnPhp\Abi\Contracts\Liquidity::class,
        'pillar' => \DigitalSloth\ZnnPhp\Abi\Contracts\Pillar::class,
        'plasma' => \DigitalSloth\ZnnPhp\Abi\Contracts\Plasma::class,
        'sentinel' => \DigitalSloth\ZnnPhp\Abi\Contracts\Sentinel::class,
        'spork' => \DigitalSloth\ZnnPhp\Abi\Contracts\Spork::class,
        'stake' => \DigitalSloth\ZnnPhp\Abi\Contracts\Stake::class,
        'swap' => \DigitalSloth\ZnnPhp\Abi\Contracts\Swap::class,
        'token' => \DigitalSloth\ZnnPhp\Abi\Contracts\Token::class,
    ];

    /**
     * @throws \DigitalSloth\ZnnPhp\Exceptions\DecodeException
     */
    public function abiDecode(ContractMethod $contractMethod, string $data): ?array
    {
        $abiContract = $this->getAbiContract($contractMethod);
        $decoded = $abiContract->decode($contractMethod->name, $data);
        $parameters = $abiContract->getParameterNames($contractMethod->name);

        if ($decoded && $parameters) {
            $parameters = explode(',', $parameters);

            return array_combine(
                $parameters,
                $decoded
            );
        }

        return null;
    }

    public function abiEncode(ContractMethod $contractMethod, ?array $data): ?string
    {
        return $this->getAbiContract($contractMethod)
            ->encode($contractMethod->name, $data);
    }

    /**
     * Get the ABI contract for the given ContractMethod.
     *
     * @param  ContractMethod  $contractMethod  The ContractMethod instance.
     * @return \DigitalSloth\ZnnPhp\Abi\Abi The ABI contract instance.
     */
    private function getAbiContract(ContractMethod $contractMethod): \DigitalSloth\ZnnPhp\Abi\Abi
    {
        $contractName = Str::lower($contractMethod->contract->name);
        if (is_string($this->abiContracts[$contractName])) {
            $this->abiContracts[$contractName] = new $this->abiContracts[$contractName];
        }

        return $this->abiContracts[$contractName];
    }
}
