<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportFile extends Model
{
    use HasFactory;
    public function submissionReports()
    {
        return $this->belongsTo(SubmissionReport::class, 'submission_report_id');
    }
}
