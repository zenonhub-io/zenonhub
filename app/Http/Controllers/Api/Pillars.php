<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

class Pillars extends ApiController
{
    public function getQsrRegistrationCost(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->pillar->getQsrRegistrationCost();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function checkNameAvailability(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->checkNameAvailability($request->input('name'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAll(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->getAll(
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getByOwner(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->getByOwner($request->input('address'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getByName(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'name' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->getByName($request->input('name'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getDelegatedPillar(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->getDelegatedPillar($request->input('address'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getDepositedQsr(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->pillar->getDepositedQsr($request->input('address'));

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
            $response = $this->znn->pillar->getUncollectedReward($request->input('address'));

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
            $response = $this->znn->pillar->getFrontierRewardByPage(
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
