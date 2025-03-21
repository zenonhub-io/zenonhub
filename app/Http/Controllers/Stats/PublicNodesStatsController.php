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
    public function __invoke(?string $tab = 'overview'): View
    {
        MetaTags::title(__('Zenon RPC Node Stats: Geographic Distribution & Performance'))
            ->description(__('View detailed statistics for Zenon Network public RPC nodes, including geographic locations, versions and network data'))
            ->canonical(route('stats.public-nodes'))
            ->metaByName('robots', 'index,nofollow');

        return view('stats.nodes', [
            'tab' => $tab,
            'nodes' => PublicNode::all(),
            'topCountries' => $this->getTopCountries(),
            'topCities' => $this->getTopCities(),
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
                ->groupBy('city')
                ->get();

            return $mapMarkers->map(fn (PublicNode $publicNode) => [
                'name' => sprintf('%s - %s %s', $publicNode->city, $publicNode->count, Str::plural('Node', $publicNode->count)),
                'coords' => [
                    $publicNode->latitude, $publicNode->longitude,
                ],
            ]);
        });
    }

    private function getTopCountries(): Collection
    {
        return Cache::remember('stats.nodes.top-countries', now()->addDay(), fn () => PublicNode::select('country', 'country_code')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit('10')
            ->get());
    }

    private function getTopCities(): Collection
    {
        return Cache::remember('stats.nodes.top-cities', now()->addDay(), fn () => PublicNode::select('city', 'country', 'country_code')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit('10')
            ->get());
    }

    private function getTopNetworks(): Collection
    {
        return Cache::remember('stats.nodes.top-networks', now()->addDay(), fn () => PublicNode::select('isp')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('isp')
            ->groupBy('isp')
            ->orderBy('count', 'desc')
            ->limit('5')
            ->get());
    }

    private function getNodeVersions(): Collection
    {
        return Cache::remember('stats.nodes.node-versions', now()->addDay(), fn () => PublicNode::select('version')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('version')
            ->groupBy('version')
            ->orderBy('count', 'desc')
            ->limit('5')
            ->get());
    }
}
