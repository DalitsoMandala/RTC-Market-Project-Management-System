<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Builder;

abstract class BaseQueryBuilder
{
    protected  $reporting_period;
    protected  $financial_year;
    protected  $organisation_id;
   

    public function __construct(
        $reporting_period = null,
        $financial_year = null,
        $organisation_id = null,

    ) {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;

    }

    public function builder(Builder $query): Builder
    {
        return $query->where('status', 'approved')
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));
    }
}
