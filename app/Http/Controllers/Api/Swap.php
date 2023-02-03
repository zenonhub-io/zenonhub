<?php

namespace App\Http\Controllers\Api;

use Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use DigitalSloth\ZnnPhp\Exceptions\Exception;

class Swap extends ApiController
{
    public function getAssetsByKeyIdHash(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'id_key' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->swap->getAssetsByKeyIdHash($request->input('id_key'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getAssets(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->swap->getAssets();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getLegacyPillars(Request $request): JsonResponse
    {
        try {
            $response = $this->znn->swap->getLegacyPillars();

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
