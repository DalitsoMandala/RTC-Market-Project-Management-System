<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table   = "aggregated_reports";
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }

    public function data()
    {
        return $this->hasMany(AggregatedReportData::class, 'aggregated_report_id');
    }
    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'reporting_period_id');
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
