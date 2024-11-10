<?php

declare(strict_types=1);

namespace App\Http\Controllers\Stats;

use App\Models\Nom\PublicNode;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use MetaTags;

class PublicNodesStatsController
{
    private string $defaultTab = 'overview';

    public function __invoke(?string $tab = null): View
    {
        MetaTags::title('Zenon Node Stats')
            ->description('Our Public node stats page displays the Zenon Network public RPC node stats including their geographic distribution, version and network data');

        $mapMarkers = PublicNode::select('latitude', 'longitude', 'city')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('longitude')
            ->whereNotNull('latitude')
            ->groupBy('latitude', 'longitude', 'city')
            ->get();

        $topCountries = PublicNode::select('country', 'country_code')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('country')
            ->groupBy('country', 'country_code')
            ->orderBy('count', 'desc')
            ->limit('10')
            ->get();

        $topNetworks = PublicNode::select('isp')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('isp')
            ->groupBy('isp')
            ->orderBy('count', 'desc')
            ->limit('10')
            ->get();

        $topVersions = PublicNode::select('version')
            ->selectRaw('COUNT(*) AS count')
            ->whereNotNull('version')
            ->groupBy('version')
            ->orderBy('count', 'desc')
            ->limit('10')
            ->get();

        return view('stats.nodes', [
            'tab' => $tab ?: $this->defaultTab,
            'nodes' => PublicNode::all(),
            'topCountries' => $topCountries,
            'topNetworks' => $topNetworks,
            'topVersions' => $topVersions,
            'mapMarkers' => $mapMarkers->map(function (PublicNode $publicNode) {
                return [
                    'name' => sprintf('%s - %s %s', $publicNode->city, $publicNode->count, Str::plural('Node', $publicNode->count)),
                    'coords' => [
                        $publicNode->latitude, $publicNode->longitude,
                    ],
                ];
            }),
        ]);
    }
}
