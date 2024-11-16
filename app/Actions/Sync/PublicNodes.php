<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\PublicNode;
use App\Models\Nom\PublicNodeHistory;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;

class PublicNodes
{
    use AsAction;

    public string $commandSignature = 'sync:public-nodes';

    public function handle(): void
    {
        $this->updatePublicNodes();
        $this->updateNodeHistory();

        Cache::forever('public-node-data-updated', now()->timestamp);
    }

    private function updatePublicNodes(): void
    {
        $apiUrl = config('services.public-rpc-nodes.api_url');

        try {
            $nodeJson = Http::get($apiUrl)->throw()->json();
        } catch (RequestException $e) {
            Log::error('Sync Public Nodes - Failed to fetch public nodes', [
                'url' => $apiUrl,
            ]);

            return;
        }

        $nodeIds = collect($nodeJson)->map(function ($nodeData) {
            $publicNode = $this->processNodeData($nodeData);

            if (! $publicNode->latitude || ! $publicNode->longitude) {
                $this->processLocationData($publicNode);
            }

            return $publicNode->id;
        });

        PublicNode::whereNotIn('id', $nodeIds)->update([
            'is_active' => false,
        ]);
    }

    private function updateNodeHistory(): void
    {
        PublicNodeHistory::updateOrCreate([
            'date' => now()->format('Y-m-d'),
        ], [
            'node_count' => PublicNode::whereActive()->count(),
            'unique_versions' => PublicNode::whereActive()->whereNotNull('version')->distinct('country')->count(),
            'unique_isps' => PublicNode::whereActive()->whereNotNull('isp')->distinct('country')->count(),
            'unique_cities' => PublicNode::whereActive()->whereNotNull('city')->distinct('city')->count(),
            'unique_countries' => PublicNode::whereActive()->whereNotNull('country')->distinct('country')->count(),
        ]);
    }

    private function processNodeData(array $nodeData): PublicNode
    {
        $publicNode = PublicNode::firstWhere('ip', $nodeData['ip']);

        if (! $publicNode) {
            $publicNode = PublicNode::create([
                'ip' => $nodeData['ip'],
                'discovered_at' => now(),
            ]);
        }

        $publicNode->version = $nodeData['znnd'];
        $publicNode->is_active = true;
        $publicNode->save();

        return $publicNode;
    }

    private function processLocationData(PublicNode $publicNode): void
    {
        try {
            $apiUrl = sprintf('http://ip-api.com/json/%s', $publicNode->ip);
            $locationData = Http::get($apiUrl)->throw()->json();
        } catch (RequestException $e) {
            Log::error('Sync Public Nodes - Failed to fetch node location data', [
                'url' => $apiUrl,
                'error' => $e->getMessage(),
            ]);

            return;
        }

        if ($locationData['status'] !== 'success') {
            return;
        }

        $publicNode->isp = $locationData['isp'];
        $publicNode->city = $locationData['city'];
        $publicNode->region = $locationData['regionName'];
        $publicNode->country = $locationData['country'];
        $publicNode->country_code = $locationData['countryCode'];
        $publicNode->latitude = $locationData['lat'];
        $publicNode->longitude = $locationData['lon'];
        $publicNode->save();
    }
}
