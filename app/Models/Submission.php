<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    use HasFactory;
    protected $table = 'submissions';
    protected $guarded = ['id'];

    public function period()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'period_id');
    }


}
