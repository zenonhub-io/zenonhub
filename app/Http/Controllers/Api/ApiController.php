<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\Validator;

/**
 * ApiController
 * Contains methods for returning responses to the client, all API controllers need to extend from this
 */
class ApiController
{
    public function __construct() {}

    /**
     * Returns a successful response to the client
     *
     * @param  mixed  $result  The result data.
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
     * @param  ?string  $detail  Detailed error message
     * @param  int  $code  HTTP Error code, defaults to 404.
     * @param  array  $data  Custom data to include with the response.
     */
    protected function error(string $title, ?string $detail = null, int $code = 404, array $data = []): JsonResponse
    {
        $response = array_filter([
            'title' => $title,
            'detail' => $detail,
            'type' => null,
            'instance' => null,
        ]);

        if (! empty($data)) {
            $response = array_merge($response, $data);
        }

        return response()->json($response, $code);
    }

    /**
     * Returns validation errors to the client
     *
     * @param  Validator  $validator  The validator object.
     */
    protected function validationError(Validator $validator): JsonResponse
    {
        return $this->error(
            'Validation error',
            'The data sent did not pass validation, see the errors in this response',
            422,
            [
                'errors' => $validator->errors()->getMessages(),
            ]
        );
    }
}
