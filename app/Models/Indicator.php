<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indicator extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function responsiblePeopleforIndicators()
    {
        return $this->hasMany(ResponsiblePerson::class, 'indicator_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }

    public function forms()
    {
        return $this->belongsToMany(Form::class, 'indicator_form');
    }

    public function disaggregations()
    {
        return $this->hasMany(IndicatorDisaggregation::class, 'indicator_id');
    }

    public function class()
    {
        return $this->hasOne(IndicatorClass::class, 'indicator_id');
    }

    public function submissionPeriods()
    {

        return $this->hasMany(SubmissionPeriod::class, 'indicator_id');
    }
}
