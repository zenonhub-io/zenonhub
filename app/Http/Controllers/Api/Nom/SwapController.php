<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Nom;

use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class SwapController extends NomController
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
