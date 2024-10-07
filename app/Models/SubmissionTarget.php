<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubmissionTarget extends Model
{
    use HasFactory;

    protected $table = 'submission_targets';
    protected $guarded = ['id'];


    public function submissionPeriod()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'submission_period_id');
    }
}
