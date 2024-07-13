<?php

namespace App\Exceptions;

use Exception;

class SheetImportException extends Exception
{
    protected $errors;
    protected $sheet;

    public function __construct($sheet, $errors)
    {
        parent::__construct("Errors in sheet: $sheet");
        $this->errors = $errors;
        $this->sheet = $sheet;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getSheet()
    {
        return $this->sheet;
    }
}
