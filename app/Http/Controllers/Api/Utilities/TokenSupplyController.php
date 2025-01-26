<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use App\Models\Nom\Token;

class TokenSupplyController extends ApiController
{
    public function __invoke(string $zts, ?string $value = 'total'): string
    {
        $allowedValues = [
            'total',
            'max',
            'circulating',
        ];

        if (! $zts || ! in_array($value, $allowedValues, true)) {
            abort(404);
        }

        $token = Token::firstWhere('token_standard', $zts);

        if (! $token) {
            abort(404);
        }

        if ($value === 'total' || $value === 'circulating') {
            $supply = $token->total_supply;
        } else {
            $supply = $token->max_supply;
        }

        return $token->getFormattedAmount($supply, $token->decimals, '.', '');
    }
}
