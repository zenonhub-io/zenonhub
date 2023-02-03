<?php

namespace App\Jobs\Sync;

use App;
use Log;
use Throwable;
use App\Classes\Utilities;
use App\Models\Nom\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class Tokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 25;
    public int $backoff = 10;
    protected Collection $tokens;

    public function __construct()
    {
        $this->onQueue('indexer');
    }

    public function handle(): void
    {
        try {
            $this->loadTokens();
            $this->processTokens();
        } catch (\DigitalSloth\ZnnPhp\Exceptions\Exception) {
            Log::error('Sync tokens error - could not load data from API');
            $this->release(10);
        } catch (Throwable $exception) {
            Log::error('Sync tokens error - ' . $exception);
            $this->release(10);
        }
    }

    private function loadTokens(): void
    {
        $znn = App::make('zenon.api');
        $total = null;
        $results = [];
        $page = 0;

        while (count($results) !== $total) {
            $data = $znn->token->getAll($page);
            if ($data['status']) {
                if (is_null($total)) {
                    $total = $data['data']->count;
                }
                $results = array_merge($results, $data['data']->list);
            }

            $page++;
        }

        $this->tokens = collect($results);
    }

    private function processTokens()
    {
        $this->tokens->each(function ($data) {
            $exists = Token::whereZts($data->tokenStandard)->first();
            if (! $exists) {
                $owner = Utilities::loadAccount($data->owner);
                Token::create([
                    'owner_id' => $owner->id,
                    'name' => $data->name,
                    'symbol' => $data->symbol,
                    'domain' => $data->domain,
                    'token_standard' => $data->tokenStandard,
                    'total_supply' => $data->totalSupply,
                    'max_supply' => $data->maxSupply,
                    'decimals' => $data->decimals,
                    'is_burnable' => $data->isBurnable,
                    'is_mintable' => $data->isMintable,
                    'is_utility' => $data->isUtility,
                ]);
            }
        });
    }
}
