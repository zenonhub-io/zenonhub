<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Actions\PlasmaBot\Fuse;
use App\Enums\Nom\NetworkTokensEnum;
use App\Models\Nom\Account;
use App\Models\Nom\AccountReward;
use App\Models\Nom\Token;
use App\Models\PlasmaBotEntry;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Validator;

class Utilities extends ApiController
{
    public function addressFromPublicKey(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'public_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $account = app(ZenonSdk::class)
            ->addressFromPublicKey(
                $request->input('public_key')
            );

        if (! $account) {
            return $this->error('Invalid public key');
        }

        return $this->success($account);
    }

    public function ztsFromHash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'hash' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $zts = app(ZenonSdk::class)
            ->ztsFromHash(
                $request->input('hash')
            );

        if (! $zts) {
            return $this->error('Invalid hash');
        }

        return $this->success($zts);
    }

    public function verifySignedMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'public_key' => 'required|string',
            'message' => 'required|string',
            'signature' => 'required|string',
            'address' => 'required|exists:nom_accounts,address',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $validated = app(ZenonSdk::class)
            ->verifySignature(
                $request->input('public_key'),
                $request->input('address'),
                $request->input('message'),
                $request->input('signature')
            );

        if (! $validated) {
            return $this->error('Invalid signature');
        }

        return $this->success(true);
    }

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

    public function tokenSupply(string $zts, ?string $value = 'total'): string
    {
        $allowedValues = [
            'total',
            'max',
            'circulating',
        ];

        if (! $zts || ! in_array($value, $allowedValues)) {
            abort(404);
        }

        $token = Token::firstWhere('token_standard', $zts);

        if (! $token) {
            abort(404);
        }

        if ($value === 'total' || $value === 'circulating') {
            $supply = $token->raw_json->totalSupply;
        }

        if ($value === 'max') {
            $supply = $token->raw_json->maxSupply;
        }

        return $token->getFormattedAmount($supply, $token->decimals, '.', '');
    }

    public function tokenPrice(): JsonResponse
    {
        return $this->success([
            'znn' => [
                'timestamp' => now(),
                'usd' => app('znnToken')->price,
            ],
            'qsr' => [
                'timestamp' => now(),
                'usd' => app('qsrToken')->price,
            ],
        ]);
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

    public function plasmaBotFuse(Request $request): JsonResponse
    {
        try {
            (new AccessKeyValidator)->execute($request->bearerToken());
        } catch (RuntimeException) {
            return $this->error(
                'Invalid access token',
                'Your API token is invalid',
                403
            );
        }

        $validator = Validator::make($request->input(), [
            'address' => 'required|string|size:40',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $fuseAmount = 100;
        $address = $request->input('address');
        $plasmaBotAccount = Account::findByAddress(config('plasma-bot.address'));

        if ($plasmaBotAccount->qsr_balance < $fuseAmount) {
            return $this->error(
                'Unable to fuse QSR',
                'Not enough QSR available in the bot, try again later',
                400
            );
        }

        $existingFuse = PlasmaBotEntry::whereAddress($address)
            ->whereactive()
            ->whereConfirmed()
            ->exists();

        if (! $existingFuse) {
            $result = Fuse::run($address, $fuseAmount);

            if (! $result) {
                return $this->error(
                    'Error fusing QSR',
                    'An error occurred while fusing QSR, please try again',
                    400
                );
            }
        }

        return $this->success('Success');
    }

    public function plasmaBotExpiration(Request $request, string $address): JsonResponse
    {
        try {
            (new AccessKeyValidator)->execute($request->bearerToken());
        } catch (RuntimeException) {
            return $this->error(
                'Invalid access token',
                'Your API token is invalid',
                403
            );
        }

        $validator = Validator::make([
            'address' => $address,
        ], [
            'address' => 'required|string|size:40',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        $fuse = PlasmaBotEntry::whereAddress($address)
            ->whereActive()
            ->isConfirmed()
            ->first();

        if (! $fuse) {
            return $this->error(
                'Address not found',
                'The supplied address has not used the plasma bot service.',
                404
            );
        }

        $expirationDate = $fuse->expires_at;

        if (! $expirationDate) {
            $account = Account::findByAddress($address);
            if ($account && $account->latest_block) {
                $account->latest_block->created_at->addDays(30);
            }
        }

        if (! $expirationDate) {
            $expirationDate = now()->addDays(30);
        }

        return $this->success($expirationDate->format('Y-m-d H:i:s'));
    }
}
