<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalReport extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = "additional_report";

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }

    public function indicatorDisaggregation()
    {

        return $this->belongsTo(IndicatorDisaggregation::class, 'indicator_disaggregation_id');
    }

    public function periodMonths()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'period_month_id');
    }
}
