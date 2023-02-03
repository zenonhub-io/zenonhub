<?php

namespace App\Jobs\Sync;

use App;
use Log;
use Throwable;
use App\Classes\Utilities;
use App\Models\Nom\Sentinel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class Sentinels implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    protected Collection $sentinels;

    public function __construct()
    {
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        try {
            $this->loadSentinels();
            $this->processSentinels();
        } catch (\DigitalSloth\ZnnPhp\Exceptions\Exception) {
            Log::error('Sync sentinels error - could not load data from API');
            $this->release(10);
        } catch (Throwable $exception) {
            Log::error('Sync sentinels error - ' . $exception);
            $this->release(10);
        }
    }

    private function loadSentinels(): void
    {
        $znn = App::make('zenon.api');
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

                $owner = Utilities::loadAccount($data->owner);

                Sentinel::create([
                    'owner_id' => $owner->id,
                    'is_revocable' => $data->isRevocable,
                    'revoke_cooldown' => $data->revokeCooldown,
                    'is_active' => $data->active,
                    'created_at' => $data->registrationTimestamp,
                ]);
            }
        });
    }
}
