<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Factories;

use App\Domains\Indexer\Exceptions\ContractMethodProcessorNotFound;
use App\Domains\Nom\Models\ContractMethod;

class ContractMethodProcessorFactory
{
    /**
     * @throws ContractMethodProcessorNotFound
     */
    public static function create(ContractMethod $contractMethod): string
    {
        $className = "App\Domains\Indexer\Actions\\{$contractMethod->contract->name}\\$contractMethod->name";

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
