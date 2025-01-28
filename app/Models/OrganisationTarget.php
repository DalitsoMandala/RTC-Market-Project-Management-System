<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganisationTarget extends Model
{
    use HasFactory;

    protected $table = 'organisation_selected_targets';
    protected $guarded = [];


    public function submissionTarget()
    {
        return $this->belongsTo(SubmissionTarget::class, 'submission_target_id');
    }


    public function organisation()
    {

        return $this->belongsTo(Organisation::class, 'organisation_id');
    }
}
