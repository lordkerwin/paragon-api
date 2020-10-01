<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// This includes constants for HTTP status codes
use Symfony\Component\HttpFoundation\Response as IlluminateRepsonse;
use Exception;
use Illuminate\Support\Facades\Log;

class BaseController extends Controller
{

    protected $status_code = IlluminateRepsonse::HTTP_OK;

    /*
    {
        "meta": {
            "success": true/false,
            "message": "some details"
        },
        "data": {
            ...
        },
        "links": {

        }
    }
    */

    /**
     * Set the status code for the HTTP response
     *
     * @param Integer $status_code
     * @return void
     * @throws Exception on unknown HTTP status code
     */
    public function setStatusCode(int $status_code)
    {
        // Validate it's a valid code
        if (array_key_exists($status_code, IlluminateRepsonse::$statusTexts)) {
            $this->status_code = $status_code;
        } else {
            print_r(['status_code' => $status_code, 'array' => IlluminateRepsonse::$statusTexts]);
            throw new Exception("Unknown HTTP status code set", 1);
        }
    }

    /**
     * Get the current response status code
     *
     * @return Integer HTTP Status Code
     */
    public function getStatusCode()
    {
        return $this->status_code;
    }

    private function respond(bool $isSuccess, $data, string $message, $headers = [], $options = JSON_PRETTY_PRINT)
    {
        $payload = [
            "meta" => [
                "success" => $isSuccess,
                "message" => $message
            ],
            "data" => $data
        ];

        Log::debug(json_encode([
            'payload' => json_encode($payload),
            'http_code' => $this->getStatusCode(),
            'headers' => $headers,
            'options' => $options
        ]));

        return response()->json($payload, $this->getStatusCode(), $headers, $options);
    }

    // public function respondSuccess(Array $data = [], String $message = "Jolly good old chap")
    // {
    //     $this->setStatusCode(IlluminateRepsonse::HTTP_OK);

    //     return $this->respond([
    //         "meta" => [
    //             "success" => true,
    //             "message" => $message
    //         ],
    //         "data" => $data
    //     ]);
    // }

    public function respondSuccess($data = null, string $message = "Jolly good old chap")
    {
        $this->setStatusCode(IlluminateRepsonse::HTTP_OK);

        return $this->respond(true, $data, $message);
    }

    public function respondNotImplemented(string $message = "Route is not implemented")
    {
        $this->setStatusCode(IlluminateRepsonse::HTTP_NOT_IMPLEMENTED);

        return $this->respond(false, null, $message);
    }

    public function respondError($data = null, string $message = "An error occured", $status = 422)
    {
        if ($status) {
            $this->setStatusCode($status);
        }

        return $this->respond(false, $data, $message);
    }
}
