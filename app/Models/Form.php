<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'responsible_people' => 'array',
    ];
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function submissionPeriods()
    {
        return $this->hasMany(SubmissionPeriod::class, 'form_id');
    }

    public function indicators()
    {
        return $this->belongsToMany(Indicator::class, 'indicator_form');
    }
}
