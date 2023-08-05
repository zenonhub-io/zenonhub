<?php

namespace App\Http\Controllers\Api\Nom;

use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class Ledger extends ApiController
{
    public function getFrontierAccountBlock(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getFrontierAccountBlock($request->input('address'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getUnconfirmedBlocksByAddress(Request $request): JsonResponse
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
            $response = $this->znn->ledger->getUnconfirmedBlocksByAddress(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getUnreceivedBlocksByAddress(Request $request): JsonResponse
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
            $response = $this->znn->ledger->getUnreceivedBlocksByAddress(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAccountBlockByHash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'hash' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getAccountBlockByHash($request->input('hash'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAccountBlocksByHeight(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
            'height' => 'numeric',
            'count' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getAccountBlocksByHeight(
                $request->input('address'),
                $request->input('height', 25),
                $request->input('count', 5)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAccountBlocksByPage(Request $request): JsonResponse
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
            $response = $this->znn->ledger->getAccountBlocksByPage(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getFrontierMomentum(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->ledger->getFrontierMomentum();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getMomentumBeforeTime(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'time' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getMomentumBeforeTime($request->input('time', now()->timestamp));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getMomentumsByPage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getMomentumsByPage(
                $request->input('page', 0),
                $request->input('per_page', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getMomentumByHash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'hash' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getMomentumByHash($request->input('hash'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getMomentumsByHeight(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'height' => 'numeric',
            'count' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getMomentumsByHeight(
                $request->input('height', 1),
                $request->input('count', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getDetailedMomentumsByHeight(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'height' => 'numeric',
            'count' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getDetailedMomentumsByHeight(
                $request->input('height', 1),
                $request->input('count', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAccountInfoByAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->ledger->getAccountInfoByAddress(
                $request->input('address'),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
