<?php

declare(strict_types=1);

namespace App\Livewire\Tools;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class ApiPlayground extends Component
{
    #[Url]
    public ?string $request = null;

    #[Url]
    public ?array $data = [];

    public ?array $availableRequests = null;

    public ?string $url = null;

    public ?string $route = null;

    public ?string $method = null;

    public ?array $inputs = null;

    public ?array $result = null;

    public function mount(): void
    {
        $this->availableRequests = Storage::json('nom-json/api-playground.json');
        $this->getRequestInputs();
    }

    public function render(): View
    {
        return view('livewire.tools.api-playground');
    }

    public function setRequest(string $value): void
    {
        $this->request = ($value !== 'null' ? $value : null);
        $this->reset('url', 'route', 'method', 'inputs', 'result');
        $this->getRequestInputs();
    }

    public function makeRequest()
    {
        if (! $this->request) {
            return;
        }

        $route = route('api.' . $this->route);
        $http = Http::withOptions([
            'verify' => false,
        ]);

        $data = collect($this->inputs)
            ->mapWithKeys(function ($input) {

                $key = Str::snake($input['name']);
                $value = $this->data[$input['name']] ?? $input['default'];

                $value = match ($input['type']) {
                    'int' => (int) $value,
                    'string' => (string) $value,
                    'array' => explode("\n", str_replace("\r", '', $value)),
                };

                return [$key => $value];
            })->toArray();

        if ($this->method === 'get') {
            $result = $http->get($route, [...$data]);
        } else {
            $result = $http->post($route, [...$data]);
        }

        $this->url = (string) $result->effectiveUri();
        $this->result = json_decode($result->body(), true);
    }

    private function getRequestInputs(): void
    {
        if (! $this->request) {
            return;
        }

        $inputs = [];
        [$group, $request] = explode('.', $this->request);

        $requestData = collect($this->availableRequests[$group])
            ->firstWhere('name', $this->request);

        if (! empty($requestData['inputs'])) {
            foreach ($requestData['inputs'] as $inputKey => $inputType) {

                if (str_contains($inputType, ':')) {
                    [$inputType, $inputDefault] = explode(':', $inputType);
                }

                $inputs[] = [
                    'displayName' => Str::headline($inputKey),
                    'name' => $inputKey,
                    'type' => $inputType,
                    'default' => $inputDefault ?? null,
                    'value' => null,
                ];
            }
        }

        $this->route = $requestData['route'];
        $this->method = $requestData['method'];
        $this->inputs = $inputs;
    }
}
