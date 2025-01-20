<?php

namespace App\Helpers;

class LogError
{

    public $errors;

    public static function sendErrors($e)
    {

        \Log::error($e->getMessage());

        // Provide a generic error message to the user
        session()->flash('error', 'Something went wrong!');

        return true;
    }
}