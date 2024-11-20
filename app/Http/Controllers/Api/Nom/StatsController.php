<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Nom;

use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends NomController
{
    public function runtimeInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->stats->runtimeInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function processInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->stats->processInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function syncInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->stats->syncInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function networkInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->stats->networkInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
