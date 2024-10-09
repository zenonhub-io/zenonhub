<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domains\Nom\Enums\NetworkTokensEnum;
use App\Domains\Nom\Models\Account;
use App\Domains\Nom\Models\Token;
use DigitalSloth\ZnnPhp\Utilities as ZnnUtilities;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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

        try {
            $account = ZnnUtilities::addressFromPublicKey(
                $request->input('public_key')
            );

            return $this->success($account);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function ztsFromHash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'hash' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $zts = ZnnUtilities::ztsFromHash(
                $request->input('hash')
            );

            return $this->success($zts);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
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

        try {
            $validSignature = ZnnUtilities::verifySignedMessage(
                $request->input('public_key'),
                $request->input('message'),
                $request->input('signature')
            );

            $accountCheck = ZnnUtilities::addressFromPublicKey($request->input('public_key'));

            if ($validSignature && ($request->input('address') === $accountCheck)) {
                return $this->success(true);
            }

            return $this->error('Invalid signature');
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
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
                'usd' => znn_price(),
            ],
            'qsr' => [
                'timestamp' => now(),
                'usd' => qsr_price(),
            ],
            'eth' => [
                'timestamp' => now(),
                'usd' => eth_price(),
            ],
            'btc' => [
                'timestamp' => now(),
                'usd' => btc_price(),
            ],
        ]);
    }
}
