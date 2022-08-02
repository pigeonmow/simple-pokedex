<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Sends successful response.
     *
     * @param      $result
     * @param      $message
     * @param bool $additional
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendResponse($result, $message, bool $additional = false)
    {
        // if additional metadata needs to be added to JSON resource response then need to
        // return the resource directly otherwise the metadata is stripped out by response()->json() method
        if ($additional) {
            return $result;
        } else {
            $response = [
                'success' => true,
                'data'    => $result,
                'message' => $message,
            ];

            return response()->json($response, 200);
        }
    }

    /**
     * Send unsuccessful response.
     *
     * @param $error
     * @param array $errorMessages
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendError($error, array $errorMessages = [], int $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (! empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}
