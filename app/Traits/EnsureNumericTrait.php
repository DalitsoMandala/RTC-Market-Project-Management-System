<?php

namespace App\Traits;

trait EnsureNumericTrait
{
    //
    public function ensureNumeric($value): float
{
    if (is_numeric($value)) {
        return (float)$value;
    }

    // Try to extract numeric value from string
    if (is_string($value)) {
        $numericString = preg_replace('/[^0-9\.\-]/', '', $value);
        if (is_numeric($numericString)) {
            return (float)$numericString;
        }
    }

    return 0.0;
}
}
