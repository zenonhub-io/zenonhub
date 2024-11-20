<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddressFromPublicKeyController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
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
}
