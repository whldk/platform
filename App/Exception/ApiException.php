<?php
namespace App\Exception;

use Throwable;

class ApiException extends \RuntimeException{
    public function __construct(array $apiErr, Throwable $previous = null)
    {
        $code = $apiErr[0];
        $message = $apiErr[1];
        parent::__construct($message, $code, $previous);
    }
}