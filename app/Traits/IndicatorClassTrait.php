<?php
namespace App\Traits;

trait IndicatorClassTrait
{
    //
    protected $financial_year, $reporting_period, $project;
    protected $organisation_id;

    protected $enterprise;

    public function __construct($reporting_period = null, $financial_year = null, $organisation_id = null, $enterprise = null)
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year   = $financial_year;
        $this->organisation_id  = $organisation_id;
        $this->enterprise       = $enterprise;
    }
}
