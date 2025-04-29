<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionTarget extends Model
{
    use HasFactory;

    protected $table = 'submission_targets';

    protected $guarded = [];

    public function organisationTargets()
    {
        return $this->hasMany(OrganisationTarget::class, 'submission_target_id');
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
