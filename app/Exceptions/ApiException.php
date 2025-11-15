<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiException extends Exception
{
    protected int $statusCode;

    /**
     * @param string $message
     * @param int $statusCode HTTP status code
     */
    public function __construct(string $message, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct($message, $statusCode);

        $this->statusCode = $statusCode;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'error' => [
                'message' => $this->getMessage(),
                'status' => $this->statusCode,
            ],
        ], $this->statusCode);
    }
}
