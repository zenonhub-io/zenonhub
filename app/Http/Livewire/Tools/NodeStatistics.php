<?php

namespace App\Http\Livewire\Tools;

use App\Http\Livewire\ChartTrait;
use Cache;
use Carbon\Carbon;
use Livewire\Component;

class NodeStatistics extends Component
{
    use ChartTrait;

    public bool $dataCached = false;
    public array $mapData = [
        'ips',
        'countries',
        'cities',
        'updated',
    ];
    public array $countriesData = [
        'labels',
        'data',
    ];
    public array $citiesData = [
        'labels',
        'data',
    ];
    public array $networksData = [
        'labels',
        'data',
    ];
    public string $updated;

    public string $tab = 'map';
    protected $queryString = [
        'tab' => ['except' => 'map'],
    ];

    protected $listeners = ['tabChange'];

    public function tabChange($tab = 'map')
    {
        $this->tab = $tab;
    }

    public function render()
    {
        $this->dataCached = (bool) Cache::get('node-ips', false);
        $this->loadMapData();
        $this->loadCountriesData();
        $this->loadCitiesData();
        $this->loadNetworksData();
        $this->updated = Carbon::parse(Cache::get('node-data-updated'))->format(config('zenon.date_format'));

        return view('livewire.tools.node-statistics');
    }

    private function loadMapData()
    {
        $this->mapData = [
            'ips' => Cache::get('node-ips', []),
            'countries' => Cache::get('node-countries'),
            'cities' => Cache::get('node-cities'),
        ];
    }

    private function loadCountriesData()
    {
        $data = collect(Cache::get('node-countries', []))->sortDesc();
        $this->countriesData = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }

    private function loadCitiesData()
    {
        $data = collect(Cache::get('node-cities', []))->sortDesc();
        $this->citiesData = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }

    private function loadNetworksData()
    {
        $data = collect(Cache::get('node-providers', []))->sortDesc();
        $this->networksData = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }
}
