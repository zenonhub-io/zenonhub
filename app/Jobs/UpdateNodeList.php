<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use MaxMind\Db\Reader;

class UpdateNodeList implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    protected string $nodesJsonUrl = 'https://github.com/sol-znn/znn-node-parser/releases/download/public_nodes/output_nodes.json';

    /**
     * @var ?Collection
     */
    protected ?Collection $data;

    public function handle(): void
    {
        $this->loadNodeData();
        $this->setCacheData();
    }

    protected function loadNodeData()
    {
        try {
            $nodeJson = json_decode(file_get_contents($this->nodesJsonUrl));
            $this->data = collect($nodeJson);
        } catch (\Exception $exception) {
            $this->data = null;
        }
    }

    protected function setCacheData()
    {
        if (! $this->data) {
            return;
        }

        Cache::forget('node-ips');
        Cache::rememberForever('node-ips', function () {
            $reader = new Reader(storage_path('app/maxmind/GeoLite2-City.mmdb'));
            $data = $this->data->flatMap(function ($node) use ($reader) {
                $locationData[$node->ip] = [
                    'version' => $node->znnd,
                    'network' => $node->provider,
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
                ->count();
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
                ->count();
        });

        Cache::forget('node-networks');
        Cache::rememberForever('node-networks', function () {
            return collect(Cache::get('node-ips'))
                ->sortBy('network')
                ->groupBy('network')
                ->filter(function ($item) {
                    return $item[0]['network'] !== 'Unknown';
                })
                ->map
                ->count();
        });

        Cache::forget('node-versions');
        Cache::rememberForever('node-versions', function () {
            return collect(Cache::get('node-ips'))
                ->sortBy('version')
                ->groupBy('version')
                ->map
                ->count();
        });

        Cache::forever('node-data-updated', now()->timestamp);
    }
}
