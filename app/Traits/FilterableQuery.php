<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterableQuery
{
    protected  $reporting_period = null;
    protected  $financial_year = null;
    protected  $organisation_id = null;
    protected  $enterprise = null;
    protected $is_report = false;

    public function applyFilters(Builder $query, $is_report = false): Builder
    {
        $query = $query
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));

        if (!$is_report && $this->enterprise) {
            $query->where('enterprise', $this->enterprise);
        }

        return $query;
    }

    public function applyHouseHoldFilters(Builder $query, $is_report = false): Builder
    {
        $query = $query
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));

        if (!$is_report && $this->enterprise) {
            $query->where(function ($q) {
                switch ($this->enterprise) {
                    case 'Cassava':
                        $q->where('crop_cassava', true);
                        break;
                    case 'Potato':
                        $q->where('crop_potato', true);
                        break;
                    case 'Sweet potato':
                        $q->where('crop_sweet_potato', true);
                        break;
                }
            });
        }

        return $query;
    }

    public function applySeedFilters(Builder $query, $is_report = false): Builder
    {
        $query = $query
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));


        $temp = $this->enterprise;
        $enterprise = $temp === 'Sweet potato' ? 'OFSP' : $temp;


        if (!$is_report && $this->enterprise) {
            $query->where('crop', $enterprise);
        }

        return $query;
    }

    public function applyAttendanceFilters(Builder $query, $is_report = false): Builder
    {
        $query = $query
            ->when($this->reporting_period, fn($q) => $q->where('period_month_id', $this->reporting_period))
            ->when($this->financial_year, fn($q) => $q->where('financial_year_id', $this->financial_year))
            ->when($this->organisation_id, fn($q) => $q->where('organisation_id', $this->organisation_id));

        if (!$is_report && $this->enterprise) {
            $query->where(function ($q) {
                switch ($this->enterprise) {
                    case 'Cassava':
                        $q->where('rtcCrop_cassava', true);
                        break;
                    case 'Potato':
                        $q->where('rtcCrop_potato', true);
                        break;
                    case 'Sweet potato':
                        $q->where('rtcCrop_sweet_potato', true);
                        break;
                }
            });
    }

        return $query;
    }
    public function setFilters($reporting_period,  $financial_year,  $organisation_id, $enterprise): void
    {
        $this->reporting_period = $reporting_period;
        $this->financial_year = $financial_year;
        $this->organisation_id = $organisation_id;
        $this->enterprise = $enterprise;
    }
}