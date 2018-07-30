<?php

namespace App\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Http400ApiException
 * @package App\Exceptions
 */
class Http400ApiException extends HttpResponseException
{
    /**
     * Error code for Single Entity case in Exception (Bad Request) (400 http error).
     * Stores short information about error.
     */
    const BAD_REQUEST_CODE = 'GL-API-BAD-REQUEST-001';

    /**
     * Error title for Single item.
     * Heading of the error.
     */
    const BAD_REQUEST_TITLE = 'Bad Request';

    /**
     * Error details for item not found or edited.
     * Explains details of the error for user.
     */
    const ERROR_ITEM_NOT_FOUND_OR_EDITED = 'Item not found in database or could not be edited';

    /**
     * Http400ApiException constructor.
     * @param null|string $message Message from validator
     */
    public function __construct($message = null)
    {
        $jsonResponse = null;

        $jsonResponse = new JsonResponse([
            'errors' => [
                'code' => self::BAD_REQUEST_CODE,
                'title' => self::BAD_REQUEST_TITLE,
                'detail' => $message === null ? self::ERROR_ITEM_NOT_FOUND_OR_EDITED : $message
            ]
        ], Response::HTTP_BAD_REQUEST);

        parent::__construct($jsonResponse);
    }
}
