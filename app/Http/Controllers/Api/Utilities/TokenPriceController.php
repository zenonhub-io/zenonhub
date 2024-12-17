<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Utilities;

use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;

class TokenPriceController extends ApiController
{
    public function __invoke(): JsonResponse
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
}
