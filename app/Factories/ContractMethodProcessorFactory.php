<?php

declare(strict_types=1);

namespace App\Factories;

use App\Exceptions\ContractMethodProcessorNotFound;
use App\Models\Nom\ContractMethod;

class ContractMethodProcessorFactory
{
    /**
     * @throws ContractMethodProcessorNotFound
     */
    public static function create(ContractMethod $contractMethod): string
    {
        $className = "App\Actions\Indexer\\{$contractMethod->contract->name}\\$contractMethod->name";

        if (! class_exists($className)) {
            throw new ContractMethodProcessorNotFound(sprintf(
                'No processor class for %s %s',
                $contractMethod->contract->name,
                $contractMethod->name
            ));
        }

        return $className;
    }
}
