<?php namespace App\Exceptions;

use Throwable;

class ApiException extends \RuntimeException {

    public function __construct(array $apiError, Throwable $previous = null)
    {
        //$code = $apiError[0];  $message = $apiError[1];
        //[$message, $code] = $apiError;
        $message = $apiError[0];
        $code = $apiError[1];
        parent::__construct($message, $code, $previous);
    }
}