<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class UserErrorException extends Exception
{
    public function __construct(string $message = 'Something went wrong!', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
