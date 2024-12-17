<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VerifySignedMessageController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
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
}
