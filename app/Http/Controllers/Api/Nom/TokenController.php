<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Nom;

use App\Http\Controllers\Api\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class TokenController extends ApiController
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
                (int) $request->input('page', 0),
                (int) $request->input('per_page', 100)
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
                (int) $request->input('page', 0),
                (int) $request->input('per_page', 100)
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
