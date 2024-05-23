<?php

namespace App\Helpers;

class TruncateText
{

    public $text;

    public function __construct($text)
    {
        $this->text = $text;
    }


    public function truncate()
    {
        if (strlen($this->text) <= 30) {
            return $this->text;
        }

        return substr($this->text, 0, 30) . '...';

    }
}