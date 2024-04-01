<?php

namespace App\Http\Controllers\Api;

use App\Services\ZenonSdk;
use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Validator;

/**
 * ApiController
 * Contains methods for returning responses to the client, all API controllers need to extend from this
 */
class ApiController
{
    protected Zenon $znn;

    public function __construct()
    {
        $this->znn = App::make(ZenonSdk::class);
    }

    /**
     * Returns a successful response to the client
     *
     * @param  mixed  $result The result data.
     */
    protected function success(mixed $result): JsonResponse|JsonResource
    {
        if ($result instanceof JsonResource) {
            return $result;
        }

        $response = [
            'data' => $result,
        ];

        return response()->json($response);
    }

    /**
     * Returns errors to the client
     *
     * @param  string  $error Custom error message.
     * @param  ?string  $detail Detailed error message
     * @param  int  $code HTTP Error code, defaults to 404.
     * @param  array  $data Custom data to include with the response.
     * @param  string|null  $redirect Redirect route for frontend.
     */
    protected function error(string $title, ?string $detail = null, int $code = 404, array $data = [], ?string $redirect = null): JsonResponse
    {
        $response = [
            'type' => null,
            'title' => $title,
            'detail' => $detail,
            'instance' => null,
        ];

        if (! empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $code);
    }

    /**
     * Returns validation errors to the client
     *
     * @param  Validator  $validator The validator object.
     */
    protected function validationError(Validator $validator): JsonResponse
    {
        $response = [
            'message' => 'Validation error',
            'data' => $validator->errors()->getMessages(),
        ];

        return response()->json($response, 422);
    }
}
