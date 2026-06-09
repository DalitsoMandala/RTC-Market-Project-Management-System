<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AggregatedReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "aggregated_reports";
    public function indicator()
    {
        return $this->belongsTo(Indicator::class);
    }
    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriod::class);
    }
    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }
    public function organisation()
    {
        return $this->belongsTo(Organisation::class);
    }
}
