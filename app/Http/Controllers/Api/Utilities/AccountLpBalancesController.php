<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Enums\Nom\NetworkTokensEnum;
use App\Http\Controllers\Api\ApiController;
use App\Models\Nom\Account;
use App\Models\Nom\Token;
use Illuminate\Http\JsonResponse;

class AccountLpBalancesController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $lpToken = Token::firstWhere('token_standard', NetworkTokensEnum::LP_ZNN_ETH->value);
        $accounts = Account::whereHas(
            'stakes',
            fn ($q) => $q->where('token_id', $lpToken->id)
        )->get();

        $accounts = $accounts->map(function ($account) use ($lpToken) {

            $query = $account->stakes()
                ->where('token_id', $lpToken->id)
                ->whereNull('ended_at');

            $balance = $query->sum('amount');
            $count = $query->count();
            $hashes = $query->pluck('hash');

            return [
                'address' => $account->address,
                'count' => $count,
                'balance' => $balance,
                'display_balance' => $lpToken->getDisplayAmount($balance),
                'hashes' => $hashes,
            ];
        });

        return $this->success($accounts);
    }
}
