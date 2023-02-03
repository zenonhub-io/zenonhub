<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

class Stats extends ApiController
{
//    public function osInfo(Request $request): JsonResponse
//    {
//        try {
//            $response = $this->znn->stats->osInfo();
//
//            return $this->success($response['data']);
//        } catch (Exception $exception) {
//            return $this->error($exception->getMessage());
//        }
//    }
//
//    public function runtimeInfo(Request $request): JsonResponse
//    {
//        try {
//            $response = $this->znn->stats->runtimeInfo();
//
//            return $this->success($response['data']);
//        } catch (Exception $exception) {
//            return $this->error($exception->getMessage());
//        }
//    }
//
//    public function processInfo(Request $request): JsonResponse
//    {
//        try {
//            $response = $this->znn->stats->processInfo();
//
//            return $this->success($response['data']);
//        } catch (Exception $exception) {
//            return $this->error($exception->getMessage());
//        }
//    }

    public function syncInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->stats->syncInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

//    public function networkInfo(Request $request): JsonResponse
//    {
//        try {
//            $response = $this->znn->stats->networkInfo();
//
//            return $this->success($response['data']);
//        } catch (Exception $exception) {
//            return $this->error($exception->getMessage());
//        }
//    }
}
