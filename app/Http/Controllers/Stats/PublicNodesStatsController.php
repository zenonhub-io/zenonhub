<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Models\Nom\PublicNode;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use MetaTags;

class PublicNodesStatsController
{
    private string $defaultTab = 'overview';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Zenon RPC Node Stats')
            ->description('The Public RPC node stats page displays the Zenon Network public RPC node stats including their geographic distribution, version and network data');

        return view('stats.nodes', [
            'tab' => $tab ?: $this->defaultTab,
            'nodes' => PublicNode::all(),
            'topCountries' => $this->getTopCountries(),
            'topNetworks' => $this->getTopNetworks(),
            'nodeVersions' => $this->getNodeVersions(),
            'mapMarkers' => $this->getMapMarkers(),
        ]);
    }

    private function getMapMarkers(): Collection
    {
        return Cache::remember('stats.nodes.map-markers', now()->addDay(), function () {
            $mapMarkers = PublicNode::select('latitude', 'longitude', 'city')
                ->selectRaw('COUNT(*) AS count')
                ->whereNotNull('longitude')
                ->whereNotNull('latitude')
                ->groupBy('latitude', 'longitude', 'city')
                ->get();

            return $mapMarkers->map(function (PublicNode $publicNode) {
                return [
                    'name' => sprintf('%s - %s %s', $publicNode->city, $publicNode->count, Str::plural('Node', $publicNode->count)),
                    'coords' => [
                        $publicNode->latitude, $publicNode->longitude,
                    ],
                ];
            });
        });
    }

    private function getTopCountries(): Collection
    {
        return Cache::remember('stats.nodes.top-countries', now()->addDay(), function () {
            return PublicNode::select('country', 'country_code')
                ->selectRaw('COUNT(*) AS count')
                ->whereNotNull('country')
                ->groupBy('country', 'country_code')
                ->orderBy('count', 'desc')
                ->limit('10')
                ->get();
        });
    }

    private function getTopNetworks(): Collection
    {
        return Cache::remember('stats.nodes.top-networks', now()->addDay(), function () {
            return PublicNode::select('isp')
                ->selectRaw('COUNT(*) AS count')
                ->whereNotNull('isp')
                ->groupBy('isp')
                ->orderBy('count', 'desc')
                ->limit('5')
                ->get();
        });
    }

    private function getNodeVersions(): Collection
    {
        return Cache::remember('stats.nodes.node-versions', now()->addDay(), function () {
            return PublicNode::select('version')
                ->selectRaw('COUNT(*) AS count')
                ->whereNotNull('version')
                ->groupBy('version')
                ->orderBy('count', 'desc')
                ->limit('5')
                ->get();
        });
    }
}
