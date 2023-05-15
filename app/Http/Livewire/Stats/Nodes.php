<?php

namespace App\Http\Livewire\Stats;

use App\Http\Livewire\ChartTrait;
use Cache;
use Carbon\Carbon;
use Livewire\Component;

class Nodes extends Component
{
    use ChartTrait;

    public array $nodes = [
        'total',
        'countries',
        'cities',
    ];

    public array $mapData = [];

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

    public array $versionsData = [
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
        $this->loadTotals();
        $this->loadMapData();
        $this->loadCountriesData();
        $this->loadCitiesData();
        $this->loadNetworksData();
        $this->loadVersionsData();
        $this->updated = Carbon::parse(Cache::get('node-data-updated'))->format(config('zenon.date_format'));

        return view('livewire.stats.nodes');
    }

    private function loadTotals()
    {
        $this->nodes['total'] = count(Cache::get('node-ips'));
        $this->nodes['countries'] = count(Cache::get('node-countries'));
        $this->nodes['cities'] = count(Cache::get('node-cities'));
    }

    private function loadMapData()
    {
        $mapData = [];

        foreach (Cache::get('node-ips', []) as $node) {
            $nodeKey = $node['lat'].':'.$node['lng'];
            if (! isset($mapData[$nodeKey])) {
                $mapData[$nodeKey] = [
                    'lat' => $node['lat'],
                    'lng' => $node['lng'],
                    'count' => 1,
                ];
            } else {
                $mapData[$nodeKey]['count']++;
            }
        }

        $this->mapData = $mapData;
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
        $data = collect(Cache::get('node-networks', []))->sortDesc();
        $this->networksData = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }

    private function loadVersionsData()
    {
        $data = collect(Cache::get('node-versions', []))->sortDesc();
        $this->versionsData = [
            'labels' => $data->keys()->toArray(),
            'data' => $data->values()->toArray(),
        ];
    }
}
