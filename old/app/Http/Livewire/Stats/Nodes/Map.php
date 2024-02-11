<?php

namespace App\Http\Livewire\Stats\Nodes;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;

class Map extends Component
{
    public array $nodes;

    public function render()
    {
        return view('livewire.stats.nodes.map');
    }

    public function loadMapData()
    {
        $this->nodes['total'] = count(Cache::get('node-ips'));
        $this->nodes['countries'] = count(Cache::get('node-countries'));
        $this->nodes['cities'] = count(Cache::get('node-cities'));

        $nodeData = $this->getNodeData();
        $mapData = $this->formatNodeData($nodeData);

        $this->emit('stats.nodes.mapDataLoaded', $mapData);
    }

    private function getNodeData()
    {
        $nodes = [];
        $nodeIps = Cache::get('node-ips', []);

        foreach ($nodeIps as $node) {
            $nodeKey = $node['lat'].':'.$node['lng'];
            if (! isset($nodes[$nodeKey])) {
                $nodes[$nodeKey] = [
                    'lat' => $node['lat'],
                    'lng' => $node['lng'],
                    'size' => 1,
                ];
            } else {
                $nodes[$nodeKey]['size']++;
            }
        }

        return $nodes;
    }

    private function formatNodeData($nodes)
    {
        $mapData = [];

        foreach ($nodes as $node) {
            $mapData[] = [
                'location' => [
                    $node['lat'],
                    $node['lng'],
                ],
                'size' => $node['size'] *= 0.05,
            ];
        }

        return $mapData;
    }
}
