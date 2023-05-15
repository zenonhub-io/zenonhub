<?php

namespace App\Http\Livewire\Tools;

use DigitalSloth\ZnnPhp\Providers\Accelerator;
use DigitalSloth\ZnnPhp\Providers\Bridge;
use DigitalSloth\ZnnPhp\Providers\Htlc;
use DigitalSloth\ZnnPhp\Providers\Ledger;
use DigitalSloth\ZnnPhp\Providers\Liquidity;
use DigitalSloth\ZnnPhp\Providers\Pillar;
use DigitalSloth\ZnnPhp\Providers\Plasma;
use DigitalSloth\ZnnPhp\Providers\Sentinel;
use DigitalSloth\ZnnPhp\Providers\Stake;
use DigitalSloth\ZnnPhp\Providers\Stats;
use DigitalSloth\ZnnPhp\Providers\Swap;
use DigitalSloth\ZnnPhp\Providers\Token;
use Http;
use Livewire\Component;

class ApiPlayground extends Component
{
    public ?string $result = null;

    public ?array $requests = null;

    public ?string $request = null;

    public ?array $inputs = [];

    public ?array $data = null;

    public ?string $url = null;

    public ?string $method = null;

    public string $tab = 'playground';

    protected $queryString = [
        'tab' => ['except' => 'playground'],
        'request',
        'data',
    ];

    public function mount()
    {
        $classes = [
            Accelerator::class,
            Bridge::class,
            Htlc::class,
            Ledger::class,
            Liquidity::class,
            Pillar::class,
            Plasma::class,
            Sentinel::class,
            Stake::class,
            Stats::class,
            Swap::class,
            Token::class,
        ];
        $requests = [];
        $blockedMethods = [
            'osInfo',
            'runtimeInfo',
        ];
        $requests['Utilities'] = [
            [
                'name' => 'Utilities.addressFromPublicKey',
                'inputs' => [
                    'publicKey' => 'string',
                ],
            ],
            [
                'name' => 'Utilities.verifySignedMessage',
                'inputs' => [
                    'publicKey' => 'string',
                    'message' => 'string',
                    'signature' => 'string',
                    'address' => 'string',
                ],
            ],
        ];

        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $methods = $reflection->getMethods();

            foreach ($methods as $method) {
                if (
                    $method->isPublic() &&
                    ($method->getName() !== '__construct' && ! in_array($method->getName(), $blockedMethods))
                ) {
                    $inputs = [];
                    if ($method->getParameters()) {
                        foreach ($method->getParameters() as $parameter) {
                            $default = false;
                            if ($parameter->isOptional()) {
                                $default = $parameter->getDefaultValue();
                            }

                            $inputs[$parameter->getName()] = $parameter->getType().($default ? ":{$default}" : '');
                        }
                    }

                    $requests[$reflection->getShortName()][] = [
                        'name' => "{$reflection->getShortName()}.{$method->getName()}",
                        'inputs' => $inputs,
                    ];
                }
            }
        }

        $this->requests = $requests;
    }

    public function render()
    {
        $this->setInputs();

        return view('livewire.tools.api-playground');
    }

    public function setRequest($value)
    {
        $this->request = ($value !== 'null' ? $value : null);
        $this->setInputs();
        $this->reset('result', 'data', 'url');
    }

    public function makeRequest()
    {
        if ($this->request && $this->request !== 'null') {
            $http = Http::withOptions([
                'verify' => false,
            ]);

            $data = [];

            if (! empty($this->data)) {
                foreach ($this->data as $key => $value) {
                    $data[\Str::snake($key)] = $value;
                }
            }

            if (in_array($this->request, [
                'Utilities.verifySignedMessage',
            ])) {
                $result = $http->post(route($this->request), [...$data]);
                $this->url = $result->effectiveUri();
                $this->method = 'POST';
            } else {
                $result = $http->get(route($this->request), [...$data]);
                $this->url = $result->effectiveUri();
                $this->method = 'GET';
            }

            $this->result = json_encode(json_decode($result->body()), JSON_PRETTY_PRINT);
        }
    }

    private function setInputs()
    {
        foreach ($this->requests as $group => $requests) {
            foreach ($requests as $request) {
                if ($request['name'] === $this->request) {
                    $this->inputs = $request['inputs'];
                }
            }
        }
    }
}
