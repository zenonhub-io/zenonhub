<?php

declare(strict_types=1);

namespace App\Actions\Tools;

use DigitalSloth\ZnnPhp\Providers\Accelerator;
use DigitalSloth\ZnnPhp\Providers\Bridge;
use DigitalSloth\ZnnPhp\Providers\Htlc;
use DigitalSloth\ZnnPhp\Providers\Ledger;
use DigitalSloth\ZnnPhp\Providers\Liquidity;
use DigitalSloth\ZnnPhp\Providers\Pillar;
use DigitalSloth\ZnnPhp\Providers\Plasma;
use DigitalSloth\ZnnPhp\Providers\Ptlc;
use DigitalSloth\ZnnPhp\Providers\Sentinel;
use DigitalSloth\ZnnPhp\Providers\Stake;
use DigitalSloth\ZnnPhp\Providers\Stats;
use DigitalSloth\ZnnPhp\Providers\Swap;
use DigitalSloth\ZnnPhp\Providers\Token;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Lorisleiva\Actions\Concerns\AsAction;
use ReflectionClass;
use ReflectionMethod;

class GenerateApiPlaygroundJson
{
    use AsAction;

    public string $commandSignature = 'tools:generate-api-playground-json';

    /**
     * NoM classes to load methods from
     *
     * @var array|string[]
     */
    private array $classes = [
        Accelerator::class,
        Bridge::class,
        Htlc::class,
        Ledger::class,
        Liquidity::class,
        Pillar::class,
        Plasma::class,
        // Ptlc::class,
        Sentinel::class,
        Stake::class,
        Stats::class,
        Swap::class,
        Token::class,
    ];

    /**
     * Don't include these methods in the generated JSON
     *
     * @var array|string[]
     */
    private array $blockedMethods = [
        'Stats.processInfo',
        'Stats.osInfo',
        'Stats.runtimeInfo',
    ];

    /**
     * Additional API requests to add to the JSON, these are requests outside the
     * base NoM classes
     *
     * @var array|array[]
     */
    private array $additionalMethods = [
        [
            'name' => 'Utilities.addressFromPublicKey',
            'route' => 'utilities.address-from-public-key',
            'method' => 'get',
            'inputs' => [
                'publicKey' => 'string',
            ],
        ],
        [
            'name' => 'Utilities.ztsFromHash',
            'route' => 'utilities.zts-from-hash',
            'method' => 'get',
            'inputs' => [
                'hash' => 'string',
            ],
        ],
        [
            'name' => 'Utilities.verifySignedMessage',
            'route' => 'utilities.verify-signed-message',
            'method' => 'post',
            'inputs' => [
                'publicKey' => 'string',
                'message' => 'string',
                'signature' => 'string',
                'address' => 'string',
            ],
        ],
    ];

    /**
     * The available requests to be converted to JSON
     *
     * @var array|array[]
     */
    private array $availableRequests = [];

    public function handle(): void
    {
        $this->buildNomRequests();
        $this->addAdditionalRequests();
        $this->saveJson();
    }

    private function buildNomRequests(): void
    {
        foreach ($this->classes as $class) {
            $reflection = new ReflectionClass($class);
            $className = $reflection->getShortName();
            $methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);

            foreach ($methods as $method) {

                $methodName = $method->getName();
                $requestName = "{$className}.{$methodName}";
                $routeName = Str::slug($className) . '.' . Str::slug(Str::headline($methodName));

                if ($methodName === '__construct' || in_array($requestName, $this->blockedMethods, true)) {
                    continue;
                }

                $this->availableRequests[$className][] = [
                    'name' => $requestName,
                    'route' => $routeName,
                    'method' => 'get',
                    'inputs' => $this->generateInputs($method),
                ];
            }
        }
    }

    private function generateInputs(ReflectionMethod $method): array
    {
        if (! $method->getParameters()) {
            return [];
        }

        $inputs = [];
        foreach ($method->getParameters() as $parameter) {
            $paramType = (string) $parameter->getType();
            $paramName = $parameter->getName();
            $defaultValue = false;

            if ($parameter->isOptional()) {
                $defaultValue = $parameter->getDefaultValue();

                if ($paramType === 'array') {
                    $defaultValue = json_encode($defaultValue);
                }

                $defaultValue = ':' . $defaultValue;
            }

            $inputs[$paramName] = $paramType . ($defaultValue ?: null);
        }

        return $inputs;
    }

    private function addAdditionalRequests(): void
    {
        foreach ($this->additionalMethods as $additionalMethod) {
            $this->availableRequests['Utilities'][] = $additionalMethod;
        }
    }

    private function saveJson(): void
    {
        $json = json_encode($this->availableRequests);
        Storage::put('json/api-playground.json', $json);
    }
}
