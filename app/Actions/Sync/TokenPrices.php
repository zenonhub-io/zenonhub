<?php

declare(strict_types=1);

namespace App\Actions\Sync;

use App\Enums\Nom\NetworkTokensEnum;
use App\Exceptions\TokenPriceException;
use App\Models\Nom\Currency;
use App\Models\Nom\Token;
use App\Services\TokenPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

class TokenPrices
{
    use AsAction;

    public string $commandSignature = 'sync:token-prices';

    public function handle(Token $token): void
    {
        $currencies = Currency::all();
        $prices = $this->getTokenPrice($token);
        $timestamp = now()->roundHour();

        $prices?->each(function ($price, $currencyCode) use ($token, $currencies, $timestamp) {

            $currency = $currencies->firstWhere('code', $currencyCode);

            if (! $currency) {
                return;
            }

            $existingPivot = $token->prices()
                ->wherePivot('currency_id', $currency->id)
                ->wherePivot('timestamp', $timestamp)
                ->first();

            if ($existingPivot) {
                $token->prices()
                    ->updateExistingPivot($currency->id, [
                        'price' => $price,
                        'timestamp' => $timestamp,
                    ]);
            } else {
                $token->prices()
                    ->attach($currency->id, [
                        'price' => $price,
                        'timestamp' => $timestamp,
                    ]);
            }
        });
    }

    public function asCommand(Command $command): void
    {
        $tokens = Token::whereIn('token_standard', [
            NetworkTokensEnum::ZNN->zts(),
            NetworkTokensEnum::QSR->zts(),
        ])->get();

        $progressBar = new ProgressBar(new ConsoleOutput, $tokens->count());
        $progressBar->start();

        $tokens->each(function (Token $token) use ($progressBar): void {
            $this->handle($token);
            $progressBar->advance();
        });

        $progressBar->finish();
    }

    private function getTokenPrice(Token $token): ?Collection
    {
        try {
            $prices = app(TokenPrice::class)->currentPrice($token);

            return collect($prices);
        } catch (TokenPriceException $e) {
            Log::warning(sprintf('Sync Token Prices error - %s', $e->getMessage()), [
                'token' => $token->token_standard,
            ]);

            return null;
        }
    }
}
