<?php

namespace Database\Seeders;

use App\Models\Nom\Contract;
use App\Models\Nom\ContractMethod;
use Illuminate\Database\Seeder;

class ContractMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $contracts = [
            'Accelerator' => \DigitalSloth\ZnnPhp\Abi\Accelerator::class,
            'Common' => \DigitalSloth\ZnnPhp\Abi\Common::class,
            'Pillar' => \DigitalSloth\ZnnPhp\Abi\Pillar::class,
            'Plasma' => \DigitalSloth\ZnnPhp\Abi\Plasma::class,
            'Sentinel' => \DigitalSloth\ZnnPhp\Abi\Sentinel::class,
            'Stake' => \DigitalSloth\ZnnPhp\Abi\Stake::class,
            'Token' => \DigitalSloth\ZnnPhp\Abi\Token::class,
        ];

        foreach ($contracts as $contract => $abiClass) {

            $abi = new $abiClass();
            $methods = $abi->getMethods();

            $blockContract = Contract::create([
                'name' => $contract,
            ]);

            foreach ($methods as $method) {
                ContractMethod::create([
                    'contract_id' => $blockContract->id,
                    'name' => $method,
                    'signature' => $abi->getMethodSignature($method),
                    'fingerprint' => $abi->getSignatureFingerprint($method),
                ]);
            }
        }
    }
}
