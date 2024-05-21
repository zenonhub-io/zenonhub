<?php

declare(strict_types=1);

namespace Database\Seeders\Nom;

use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Contract;
use App\Domains\Nom\Models\ContractMethod;
use Illuminate\Database\Seeder;

class ContractMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contracts = [
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

        foreach ($contracts as $contract => $abiClass) {
            $abi = new $abiClass;
            $methods = $abi->getMethods();

            $blockContract = Contract::create([
                'chain_id' => 1,
                'account_id' => Account::where('name', 'LIKE', "{$contract}%")->first()?->id,
                'name' => $contract,
            ]);

            foreach ($methods as $method) {
                ContractMethod::create([
                    'contract_id' => $blockContract->id,
                    'name' => $method,
                    'signature' => $abi->getMethodSignature($method),
                    'fingerprint' => $abi->getMethodFingerprint($method),
                ]);
            }
        }
    }
}
