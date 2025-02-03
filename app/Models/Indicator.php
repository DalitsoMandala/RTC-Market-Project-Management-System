<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Indicator extends Model
{
    use HasFactory;
    protected $guarded = [];

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
        return $this->hasManyThrough(Organisation::class, ResponsiblePerson::class, 'indicator_id', 'id', 'id', 'organisation_id');
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

    /**
     * Get all of the comments for the Indicator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function assignedTargets()
    {
        return $this->hasMany(AssignedTarget::class, 'indicator_id');
    }

    public function indicatorTargets()
    {
        return $this->hasMany(IndicatorTarget::class, 'indicator_id');
    }


    /**
     * Get the baseline associated with the Indicator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function baseline()
    {
        return $this->hasOne(Baseline::class);
    }
}