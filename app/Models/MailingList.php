<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailingList extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function submissionPeriod()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'submission_period_id');
    }

    public function user()
    {

        return $this->belongsTo(User::class, 'user_id');
    }
}
