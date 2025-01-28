<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportingPeriodMonth extends Model
{
    use HasFactory;
    protected $table = 'reporting_period_months';
    protected $guarded = [];
    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriod::class, 'period_id');
    }
}
