<?php

namespace App\Http\Controllers\Api\Nom;

use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class Htlc extends ApiController
{
    public function getById(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->htlc->getById(
                $request->input('id'),
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getProxyUnlockStatus(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->htlc->getProxyUnlockStatus($request->input('address'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
