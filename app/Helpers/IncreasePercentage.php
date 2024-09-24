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


        return (($this->annualValue - $this->baselineValue) / $this->annualValue) * 100;
    }
}
