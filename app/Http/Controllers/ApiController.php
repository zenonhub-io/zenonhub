<?php

namespace App\Http\Controllers;

use DigitalSloth\ZnnPhp\Zenon;
use Illuminate\Http\JsonResponse;
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
        $this->znn = App::make('zenon.api');
    }

    /**
     * Returns a successful response to the client
     *
     * @param  mixed  $result The result data.
     * @param  string  $message Optional message.
     */
    protected function success(mixed $result, string $message = ''): JsonResponse
    {
        $response = [
            'success' => true,
            'data' => $result,
        ];

        return response()->json($response);
    }

    /**
     * Returns errors to the client
     *
     * @param  string  $error Custom error message.
     * @param  int  $code HTTP Error code, defaults to 404.
     * @param  array  $data Custom data to include with the response.
     * @param  string|null  $redirect Redirect route for frontend.
     */
    protected function error(string $error, int $code = 404, array $data = [], string $redirect = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($data)) {
            $response['data'] = $data;
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
            'success' => false,
            'message' => 'Validation error',
            'data' => $validator->errors()->getMessages(),
        ];

        return response()->json($response, 422);
    }
}
