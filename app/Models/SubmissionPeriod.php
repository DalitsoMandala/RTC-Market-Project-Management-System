<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionPeriod extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'period_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function financialYears()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }
    public function reportingMonths()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'month_range_period_id');
    }

    public function indicator()
    {

        return $this->belongsTo(Indicator::class, 'indicator_id');
    }


}
