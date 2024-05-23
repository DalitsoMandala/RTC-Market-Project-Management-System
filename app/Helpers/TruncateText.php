<?php

namespace App\Helpers;

class TruncateText
{

    public $text;
    public $limit;

    public function __construct($text, $limit = 100)
    {
        $this->text = $text;
        $this->limit = $limit;
    }


    public function truncate()
    {
        if (strlen($this->text) <= $this->limit) {
            return $this->text;
        }

        return substr($this->text, 0, $this->limit) . '...';

    }
}
