<?php

namespace App\Jobs;

use Cache;
use Http;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use MaxMind\Db\Reader;

class UpdateNodeList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    /**
     * @var string
     */
    protected string $nodesJsonUrl = 'https://github.com/Sol-Sanctum/Zenon-PoCs/releases/download/znn_node_info/output_nodes.json';

    /**
     * @var ?Collection
     */
    protected ?Collection $nodes;

    public function handle(): void
    {
        $this->setNodes();
        $this->setCacheData();
    }

    protected function setNodes()
    {
        try {
            $nodeJson = json_decode(file_get_contents($this->nodesJsonUrl));
            $this->nodes = collect($nodeJson);
        } catch (\Exception $exception) {
            $this->nodes = null;
        }
    }

    protected function setCacheData()
    {
        if (! $this->nodes) {
            return;
        }

        Cache::forget('node-ips');
        Cache::rememberForever('node-ips', function () {
            $reader = new Reader(storage_path('app/maxmind/GeoLite2-City.mmdb'));
            $data = $this->nodes->flatMap(function ($node) use ($reader) {

                $locationData[$node->ip] = [
                    'provider' => $node->provider,
                    'city' => 'Unknown',
                    'country' => 'Unknown',
                    'lat' => null,
                    'lng' => null,
                ];

                try {
                    $data = $reader->get($node->ip);
                    $locationData[$node->ip]['city'] = isset($data['city']) ? $data['city']['names']['en'] : 'Unknown';
                    $locationData[$node->ip]['country'] = isset($data['country']) ? $data['country']['names']['en'] : 'Unknown';
                    $locationData[$node->ip]['lat'] = isset($data['location']) ? $data['location']['latitude'] : null;
                    $locationData[$node->ip]['lng'] = isset($data['location']) ? $data['location']['longitude'] : null;
                } catch (\Exception $exception) {
                }

                return $locationData;

            })->filter()->toArray();
            $reader->close();

            return $data;
        });

        Cache::forget('node-countries');
        Cache::rememberForever('node-countries', function () {
            return collect(Cache::get('node-ips'))
                ->sortBy('country')
                ->groupBy('country')
                ->filter(function ($item) {
                    return $item[0]['country'] !== 'Unknown';
                })
                ->map
                ->count()
                ->toArray();
        });

        Cache::forget('node-cities');
        Cache::rememberForever('node-cities', function () {
            return collect(Cache::get('node-ips'))
                ->sortBy('city')
                ->groupBy('city')
                ->filter(function ($item) {
                    return $item[0]['city'] !== 'Unknown';
                })
                ->map
                ->count()
                ->toArray();
        });

        Cache::forget('node-providers');
        Cache::rememberForever('node-providers', function () {
            return collect(Cache::get('node-ips'))
                ->sortBy('provider')
                ->groupBy('provider')
                ->filter(function ($item) {
                    return $item[0]['provider'] !== 'Unknown';
                })
                ->map
                ->count()
                ->toArray();
        });

        Cache::forever('node-data-updated', now()->timestamp);
    }
}
