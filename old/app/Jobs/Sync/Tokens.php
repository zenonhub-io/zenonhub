<?php

declare(strict_types=1);

namespace App\Jobs\Sync;

use App\Domains\Nom\Models\Token;
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

class Tokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    protected Collection $tokens;

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
        $znn = App::make(ZenonSdk::class);
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
            $token = Token::firstWhere('token_standard', $data->tokenStandard);
            $owner = load_account($data->owner);
            if (! $token) {
                $chain = app('currentChain');
                $token = Token::create([
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

            $token->owner_id = $owner->id;
            $token->total_supply = $data->totalSupply;
            $token->max_supply = $data->maxSupply;
            $token->is_burnable = $data->isBurnable;
            $token->is_mintable = $data->isMintable;
            $token->is_utility = $data->isUtility;
            $token->save();
        });
    }
}
