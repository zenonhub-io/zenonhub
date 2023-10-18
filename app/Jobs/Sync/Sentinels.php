<?php

namespace App\Jobs\Sync;

use App\Classes\Utilities;
use App\Models\Nom\Sentinel;
use App\Services\ZenonSdk;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Throwable;

class Sentinels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    protected Collection $sentinels;

    public function handle(): void
    {
        try {
            $this->loadSentinels();
            $this->processSentinels();
        } catch (Throwable $exception) {
            Log::warning('Sync sentinels error');
            Log::debug($exception);
            $this->release(30);
        }
    }

    private function loadSentinels(): void
    {
        $znn = App::make(ZenonSdk::class);
        $total = null;
        $results = [];
        $page = 0;

        while (count($results) !== $total) {
            $data = $znn->sentinel->getAllActive($page);
            if ($data['status']) {
                if (is_null($total)) {
                    $total = $data['data']->count;
                }
                $results = array_merge($results, $data['data']->list);
            }

            $page++;
        }

        $this->sentinels = collect($results);
    }

    private function processSentinels(): void
    {
        $this->sentinels->each(function ($data) {
            $exists = Sentinel::whereHas('owner', function ($query) use ($data) {
                $query->where('address', $data->owner);
            })->first();

            if (! $exists) {
                $chain = Utilities::loadChain();
                $owner = Utilities::loadAccount($data->owner);

                Sentinel::create([
                    'chain_id' => $chain->id,
                    'owner_id' => $owner->id,
                    'created_at' => $data->registrationTimestamp,
                ]);
            }
        });
    }
}
