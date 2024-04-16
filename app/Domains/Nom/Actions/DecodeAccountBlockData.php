<?php

declare(strict_types=1);

namespace App\Domains\Nom\Actions;

use App\Domains\Nom\Exceptions\ZenonRpcException;
use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use DigitalSloth\ZnnPhp\Utilities;
use Illuminate\Support\Facades\Log;

class DecodeAccountBlockData
{
    /**
     * @throws ZenonRpcException
     */
    public function execute(AccountBlock $accountBlock): void
    {
        Log::debug('Decode account block data', [
            'hash' => $accountBlock->hash,
        ]);

        $data = base64_decode($accountBlock->data->raw);
        $fingerprint = Utilities::getDataFingerprint($data);
        $contractMethod = ContractMethod::whereRelation('contract', 'name', $accountBlock->toAccount->contract?->name)
            ->where('fingerprint', $fingerprint)
            ->first();

        // Fallback for common methods (not related to a specific account)
        if (! $contractMethod) {
            $contractMethod = ContractMethod::whereRelation('contract', 'name', 'Common')
                ->where('fingerprint', $fingerprint)
                ->first();
        }

        if ($contractMethod) {
            $accountBlock->contract_method_id = $contractMethod->id;
            $accountBlock->save();

            $contractName = ucfirst(strtolower($contractMethod->contract->name));
            $embeddedContract = "DigitalSloth\ZnnPhp\Abi\Contracts\\" . $contractName;

            if (class_exists($embeddedContract)) {
                $embeddedContract = new $embeddedContract;
                $decoded = $embeddedContract->decode($contractMethod->name, $data);
                $parameters = $embeddedContract->getParameterNames($contractMethod->name);

                if ($decoded && $parameters) {
                    $parameters = explode(',', $parameters);

                    $accountBlock->data->decoded = array_combine(
                        $parameters,
                        $decoded
                    );
                }
            }
        }

        $accountBlock->data->is_processed = true;
        $accountBlock->data->save();
    }
}
