<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountReward;
use Illuminate\Http\JsonResponse;

class Utilities extends ApiController
{
    public function accountLpBalances(): JsonResponse
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

    public function rewardTotals(): JsonResponse
    {
        $znnToken = znn_token();
        $qsrToken = qsr_token();

        $types = ['delegate', 'stake', 'pillar', 'sentinel', 'liquidity', 'bridge_affiliate'];
        $tokens = ['znn' => $znnToken, 'qsr' => $qsrToken];
        $result = [];

        foreach ($types as $type) {
            foreach ($tokens as $tokenName => $token) {
                $typeConstant = constant('App\Models\Nom\AccountReward::TYPE_' . strtoupper($type));
                $rewardSum = AccountReward::where('type', $typeConstant)
                    ->where('token_id', $token->id)
                    ->sum('amount');

                $uniqueAddresses = AccountReward::where('type', $typeConstant)
                    ->distinct('account_id')
                    ->count();

                $result[$type][$tokenName] = $token->getDisplayAmount($rewardSum);
                $result[$type]['uniqueAddresses'] = $uniqueAddresses;
            }
        }

        return $this->success($result);
    }
}
