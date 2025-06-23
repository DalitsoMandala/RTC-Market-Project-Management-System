<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressSubmission extends Model
{
    use HasFactory;
    protected $table = "progress_submissions";
    protected $guarded = [];


    public function period()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'period_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organisation()
    {
        return $this->belongsTo(Organisation::class, 'organisation_id');
    }
}
