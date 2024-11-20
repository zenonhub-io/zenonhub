<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Nom;

use DigitalSloth\ZnnPhp\Exceptions\Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class AcceleratorController extends NomController
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
            $response = $this->znn->accelerator->getAll(
                (int) $request->input('page', 0),
                (int) $request->input('per_page', 100)
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getProjectById(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->accelerator->getProjectById($request->input('id'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getPhaseById(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->accelerator->getPhaseById($request->input('id'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getPillarVotes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'pillar_name' => 'required|string',
            'project_hashes' => 'required|array',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->accelerator->getPillarVotes(
                $request->input('pillar_name'),
                $request->input('project_hashes')
            );

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }

    public function getVoteBreakdown(Request $request): JsonResponse
    {
        $validator = Validator::make($request->input(), [
            'hash' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->validationError($validator);
        }

        try {
            $response = $this->znn->accelerator->getVoteBreakdown($request->input('hash'));

            return $this->success($response['data']);
        } catch (Exception $exception) {
            return $this->error($exception->getMessage());
        }
    }
}
