<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterableQuery
{
    protected  $reporting_period = null;
    protected  $financial_year = null;
    protected  $organisation_id = null;
    protected  $target_year_id = null;

    public function applyFilters(Builder $query): Builder
    {
        return $query->where('status', 'approved')
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));
    }

    public function setFilters($reporting_period,  $financial_year,  $organisation_id,  $target_year_id): void
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->target_year_id = $target_year_id;
    }
}
