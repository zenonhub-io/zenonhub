<?php

namespace App\Http\Controllers\Api\Nom;

use App\Http\Controllers\Api\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class Stake extends ApiController
{
    public function getEntriesByAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->stake->getEntriesByAddress(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getUncollectedReward(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->stake->getUncollectedReward($request->input('address', 0));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getFrontierRewardByPage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->stake->getFrontierRewardByPage(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
