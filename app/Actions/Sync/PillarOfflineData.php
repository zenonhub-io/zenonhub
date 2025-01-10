<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Models\Nom\Pillar;
use App\Models\SocialProfile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Lorisleiva\Actions\Concerns\AsAction;

class PillarOfflineData
{
    use AsAction;

    public const ZENON_TOOLS_PILLAR_API = 'https://api.zenon.tools/pillars-off-chain';

    public string $commandSignature = 'sync:pillar-offline-data';

    public function handle(Pillar $pillar, $data): void
    {
        $socialProfile = $pillar->socialProfile;

        if (! $socialProfile) {
            $socialProfile = new SocialProfile;
            $pillar->socialProfile()->save($socialProfile);
        }

        $socialProfile->avatar = $data['avatar'];
        $socialProfile->website = $data['links']['website'] ?? null;
        $socialProfile->email = $data['links']['email'] ?? null;
        $socialProfile->x = $data['links']['twitter'] ?? null;
        $socialProfile->telegram = $data['links']['telegram'] ?? null;
        $socialProfile->github = $data['links']['github'] ?? null;
        $socialProfile->discord = $data['links']['discord'] ?? null;
        $socialProfile->medium = $data['links']['medium'] ?? null;
        $socialProfile->save();
    }

    public function asCommand(Command $command): void
    {
        $pillarData = Cache::remember('pillarsOfflineData', now()->addDay(), function () {
            $pillarDataApi = self::ZENON_TOOLS_PILLAR_API;
            $data = Http::get($pillarDataApi)->json();

            return collect($data);
        });

        $pillarData->each(function ($data) {

            $pillar = Pillar::with('socialProfile')
                ->firstWhere('name', $data['name']);

            if (! $pillar) {
                return;
            }

            $this->handle($pillar, $data);
        });
    }
}
