<?php

namespace App\Traits;

trait UITrait
{
    //

    public function booleanUI($string, $state = false, $error = false)
    {
        if ($string == '1' || $string == 'true') {
            $string = 'Yes';
        } else if ($string == '0' || $string == 'false') {
            $string = 'No';
        } else if ($string != '1' || $string != 'true' || $string != '0' || $string != 'false') {
            $string = $string;
        }
        if ($state) {
            return "<span class='badge bg-success-subtle text-success'>{$string}</span>";
        } else {
            if ($error) {
                return "<span class='badge bg-danger-subtle text-danger'>{$string}</span>";
            }
            return "<span class='badge bg-secondary-subtle text-secondary'>{$string}</span>";
        }
    }
}
