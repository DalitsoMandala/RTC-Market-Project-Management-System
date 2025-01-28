<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function indicators()
    {
        return $this->hasMany(Indicator::class, 'project_id');
    }

    public function forms()
    {
        return $this->hasMany(Form::class, 'project_id');
    }

    public function reportingPeriod()
    {
        return $this->belongsTo(ReportingPeriod::class, 'reporting_period_id');
    }

    public function cgiarProject()
    {
        return $this->belongsTo(Cgiar_Project::class, 'cgiar_project_id');
    }

    public function financialYears()
    {
        return $this->hasMany(FinancialYear::class, 'project_id');
    }
}
