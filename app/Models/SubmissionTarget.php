<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionTarget extends Model
{
    use HasFactory;

    protected $table = 'submission_targets';
    protected $guarded = ['id'];




    /**
     * Get all of the organisationTargets for the SubmissionTarget
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function organisationTargets()
    {
        return $this->hasMany(OrganisationTarget::class, 'submission_target_id');
    }



    /**
     * Get the reportPeriodMonth that owns the SubmissionTarget
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reportPeriodMonth()
    {
        return $this->belongsTo(ReportingPeriodMonth::class, 'month_range_period_id');
    }

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }


    public function Indicator()
    {
        return $this->belongsTo(Indicator::class, 'indicator_id');
    }
}
