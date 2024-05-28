<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionPeriod extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'period_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
