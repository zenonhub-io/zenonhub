<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\Nom\NetworkTokensEnum;
use App\Exceptions\TokenPriceException;
use App\Models\Nom\Currency;
use App\Models\Nom\Token;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TokenPrice
{
    private array $tokenIdMap = [
        NetworkTokensEnum::ZNN->value => 'zenon-2',
        NetworkTokensEnum::QSR->value => 'quasar',
    ];

    private string $baseUrl = 'https://api.coingecko.com/api/v3';

    /**
     * @throws TokenPriceException
     */
    public function currentPrice(Token $token): ?array
    {
        $tokenId = $this->getTokenId($token);
        $currencies = Currency::all()->map(fn ($currency) => $currency->code)->implode(',');
        $response = Http::get("{$this->baseUrl}/simple/price?ids={$tokenId}&vs_currencies={$currencies}");

        if (! $response->successful()) {
            throw new TokenPriceException('Unable to load token price');
        }

        return $response->json($tokenId);
    }

    /**
     * @throws TokenPriceException
     */
    public function historicPrice(Token $token, Carbon $timestamp): ?float
    {
        $tokenId = $this->getTokenId($token);
        $date = $timestamp->format('d-m-Y');
        $currencies = Currency::all()->map(fn ($currency) => $currency->code)->implode(',');
        $response = Http::get("{$this->baseUrl}/coins/{$tokenId}/history?date={$date}");

        if (! $response->successful()) {
            throw new TokenPriceException('Unable to load historic token price');
        }

        return $response->json('market_data.current_price}');
    }

    /**
     * @throws TokenPriceException
     */
    private function getTokenId(Token $token): string
    {
        if (! isset($this->tokenIdMap[$token->token_standard])) {
            throw new TokenPriceException('Token standard not supported');
        }

        return $this->tokenIdMap[$token->token_standard];
    }
}
