<?php

namespace App\Helpers;

class IncreasePercentage
{
    protected $annualValue;

    protected $baselineValue;

    public function __construct($annualValue, $baselineValue)
    {

        $this->annualValue = $annualValue;
        $this->baselineValue = $baselineValue;
    }

    public function percentage(): float
    {

        if ($this->annualValue == 0) {
            return 0;
        }
        return round(

            (($this->annualValue - $this->baselineValue) / $this->annualValue) * 100,
            2
        );
    }
}
