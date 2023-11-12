<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class Test extends ApiController
{
    /**
     * Example of sending a (200) success response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function successExample(Request $request)
    {
        $data = [
            'user_id' => 123,
            'username' => 'john_doe',
        ];

        return $this->sendResponse(1, 'Successfully', $data);
    }

    /**
     * Example of sending a created data (201) success response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function successCreatedExample(Request $request)
    {
        $data = [
            'username' => $request->username,
            'password' => $request->password,
        ];

        return $this->sendCreatedResponse(1, 'Data created Successfully');
    }

    /**
     * Example of sending a bad request (400) error response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorExample(Request $request)
    {
        $errors = [
            'Validation error' => [
                'Field 1' => 'This field is required.',
                'Field 2' => 'Invalid format.',
            ],
        ];

        return $this->sendError(2, 'Bad Request', $errors);
    }

    /**
     * Example of sending a not found (401) error response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthorizedExample(Request $request)
    {
        $errors = [
            'Unauthorized' => 'You do not have permission to access this resource.',
        ];

        return $this->sendUnauthorized(3, 'Unauthorized', $errors);
    }


    /**
     * Example of sending an unauthenticated (401) error response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unauthenticatedExample(Request $request)
    {
        $errors = [
            'Unauthenticated' => 'You must be logged in to access this resource.',
        ];

        return $this->sendUnauthorized(3, 'Unauthenticated', $errors);
    }

    /**
     * Example of sending a not found (403) error response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function forbiddenExample(Request $request)
    {
        $errors = [
            'Forbidden' => 'Access to this resource is forbidden.',
        ];

        return $this->sendForbidden(4, 'Forbidden', $errors);
    }

    /**
     * Example of sending a not found (404) error response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function notFoundExample(Request $request)
    {
        $errors = [
            'id' => "Not found"
        ];
        return $this->sendNotFound(2, 'Resource not found', $errors);
    }

    /**
     * Example of sending a server error (500) response.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function serverErrorExample(Request $request)
    {
        $errors = [
            'Server error' => 'An unexpected error occurred on the server.',
        ];

        return $this->sendServerError(5, 'Internal Server Error', $errors);
    }
}