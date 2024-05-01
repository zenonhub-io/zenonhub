<?php

declare(strict_types=1);

namespace App\Domains\Indexer\Factories;

use App\Domains\Nom\Models\ContractMethod;
use App\Exceptions\ApplicationException;

class ContractMethodProcessorFactory
{
    /**
     * @throws ApplicationException
     */
    public static function create(ContractMethod $contractMethod): string
    {
        $className = "App\Domains\Nom\Actions\Indexer\\{$contractMethod->contract->name}\\$contractMethod->name";

        if (! class_exists($className)) {
            throw new ApplicationException(sprintf(
                'No processor class for %s %s',
                $contractMethod->contract->name,
                $contractMethod->name
            ));
        }

        return $className;
    }
}
