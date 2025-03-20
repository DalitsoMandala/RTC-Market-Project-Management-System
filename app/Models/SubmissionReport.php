<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function files()
    {
        return $this->hasMany(ReportFile::class, 'submission_report_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }
    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function submissionPeriod()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'submission_period_id');
    }

    public function periodMonth()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'period_month_id');
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }
}
