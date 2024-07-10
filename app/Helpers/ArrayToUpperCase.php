<?php

namespace App\Helpers;

class ArrayToUpperCase
{


    public static function convert($array)
    {
        foreach ($array as &$element) {
            if (is_string($element)) {
                $element = strtoupper($element);
            }
        }
        return $array;
    }
}
