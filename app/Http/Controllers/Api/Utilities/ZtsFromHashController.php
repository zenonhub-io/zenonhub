<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use App\Services\ZenonSdk\ZenonSdk;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ZtsFromHashController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
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
}
