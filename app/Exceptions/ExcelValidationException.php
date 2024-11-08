<?php

namespace App\Exceptions;

use Exception;

class ExcelValidationException extends Exception
{
    //
    protected $errors;
    public function __construct(string $message = 'Something went wrong!', $errors = null)
    {

        parent::__construct($message);
        $this->errors = $errors;
    }





    public function getErrors()
    {
        return $this->errors;
    }
}
