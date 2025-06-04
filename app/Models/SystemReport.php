<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemReport extends Model
{
    use HasFactory;
    protected $fillable = [
        'financial_year_id',
        'reporting_period_id',
        'organisation_id',
        'project_id',
        'indicator_id',
        'data',
        'crop',
    ];
    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'reporting_period_id');
    }


    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }


    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }


    /**
     * Get all of the data for the SystemReport
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function data()
    {
        return $this->hasMany(SystemReportData::class, 'system_report_id');
    }
}