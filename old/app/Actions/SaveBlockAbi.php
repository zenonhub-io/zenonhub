<?php

declare(strict_types=1);

namespace App\Actions;

use App\Domains\Nom\Models\AccountBlock;
use App\Domains\Nom\Models\ContractMethod;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;

class SaveBlockAbi
{
    public function __construct(private AccountBlock $block)
    {
    }

    public function execute(): void
    {
        $block = $this->block;
        $data = base64_decode($block->data->raw);
        $fingerprint = ZnnUtilities::getDataFingerprint($data);
        $contractMethod = ContractMethod::where('contract_id', $block->toAccount->contract?->id)
            ->where('fingerprint', $fingerprint)
            ->first();

        if (! $contractMethod) {
            $contractMethod = ContractMethod::whereHas('contract', fn ($q) => $q->where('name', 'Common'))
                ->where('fingerprint', $fingerprint)
                ->first();
        }

        if ($contractMethod) {
            $block->contract_method_id = $contractMethod->id;
            $block->save();

            $contractName = ucfirst(strtolower($contractMethod->contract->name));
            $embeddedContract = "DigitalSloth\ZnnPhp\Abi\Contracts\\" . $contractName;

            if (class_exists($embeddedContract)) {
                $embeddedContract = new $embeddedContract;
                $decoded = $embeddedContract->decode($contractMethod->name, $data);
                $parameters = $embeddedContract->getParameterNames($contractMethod->name);

                if ($decoded && $parameters) {
                    $parameters = explode(',', $parameters);

                    $block->data->decoded = array_combine(
                        $parameters,
                        $decoded
                    );
                    $block->data->is_processed = true;
                    $block->data->save();
                }
            }
        }
    }
}
