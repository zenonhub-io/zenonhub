<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Enums\Nom\AccountRewardTypesEnum;
use App\Http\Controllers\Api\ApiController;
use App\Models\Nom\AccountReward;
use Illuminate\Http\JsonResponse;

class RewardTotalsController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $znnToken = app('znnToken');
        $qsrToken = app('qsrToken');

        $types = ['delegate', 'stake', 'pillar', 'sentinel', 'liquidity', 'bridge_affiliate'];
        $tokens = ['znn' => $znnToken, 'qsr' => $qsrToken];
        $result = [];

        foreach ($types as $type) {
            foreach ($tokens as $tokenName => $token) {
                $typeConstant = strtoupper($type);
                $rewardType = AccountRewardTypesEnum::{$typeConstant}->value;
                $rewardSum = AccountReward::where('type', $rewardType)
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
