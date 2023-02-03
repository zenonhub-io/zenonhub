<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

class Token extends ApiController
{
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
            $response = $this->znn->token->getAll(
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
            'page' => 'numeric',
            'per_page' => 'numeric',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->token->getByOwner(
                $request->input('address'),
                $request->input('page', 0),
                $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getByZts(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->token->getByZts($request->input('token'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
