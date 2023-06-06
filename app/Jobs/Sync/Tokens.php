<?php

namespace App\Jobs\Sync;

use App;
use App\Classes\Utilities;
use App\Models\Nom\Token;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Log;
use Throwable;

class Tokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

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
        } catch (Throwable $exception) {
            Log::warning('Sync tokens error');
            Log::debug($exception);
            $this->release(30);
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
                $chain = Utilities::loadChain();
                $owner = Utilities::loadAccount($data->owner);
                Token::create([
                    'chain_id' => $chain->id,
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
