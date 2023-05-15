<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class Bridge extends ApiController
{
    public function getBridgeInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->bridge->getBridgeInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getSecurityInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->bridge->getSecurityInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getOrchestratorInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->bridge->getOrchestratorInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getTimeChallengesInfo(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->bridge->getTimeChallengesInfo();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getNetworkInfo(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'network_class' => 'required|numeric',
            'chain_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getNetworkInfo(
                $request->input('network_class'),
                $request->input('chain_id'),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllNetworks(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllNetworks(
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getRedeemableIn(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'unwrap_token_request' => 'required|string',
            'token_pair' => 'required|string',
            'momentum' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getRedeemableIn(
                $request->input('unwrap_token_request'),
                $request->input('token_pair'),
                $request->input('momentum')
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getConfirmationsToFinality(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'wrap_token_request' => 'required|string',
            'confirmations_to_finality' => 'required|numeric',
            'momentum' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getConfirmationsToFinality(
                $request->input('wrap_token_request'),
                $request->input('confirmations_to_finality'),
                $request->input('momentum')
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getWrapTokenRequestById(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getWrapTokenRequestById(
                $request->input('id')
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllWrapTokenRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllWrapTokenRequests(
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllWrapTokenRequestsByToAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'to_address' => 'required|string',
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllWrapTokenRequestsByToAddress(
                $request->input('to_address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllWrapTokenRequestsByToAddressNetworkClassAndChainId(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'to_address' => 'required|string',
            'network_class' => 'required|numeric',
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllWrapTokenRequestsByToAddressNetworkClassAndChainId(
                $request->input('to_address'),
                $request->input('network_class'),
                $request->input('page', 0),
                $request->input('per_page', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllUnsignedWrapTokenRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllUnsignedWrapTokenRequests(
                $request->input('page', 0),
                $request->input('per_page', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getUnwrapTokenRequestByHashAndLog(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'tx_hash' => 'required|string',
            'log_index' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getUnwrapTokenRequestByHashAndLog(
                $request->input('tx_hash'),
                $request->input('log_index'),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllUnwrapTokenRequests(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getUnwrapTokenRequestByHashAndLog(
                $request->input('page', 0),
                $request->input('per_page', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAllUnwrapTokenRequestsByToAddress(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'to_address' => 'required|string',
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getAllUnwrapTokenRequestsByToAddress(
                $request->input('to_address'),
                $request->input('page', 0),
                $request->input('per_page', 100),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getFeeTokenPair(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'zts' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->bridge->getFeeTokenPair(
                $request->input('zts'),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
