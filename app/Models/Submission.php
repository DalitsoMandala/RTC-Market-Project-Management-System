<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Submission extends Model
{

    use HasFactory;
    protected $table = 'submissions';
    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'period_id');
    }



    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function financial_year()
    {
        return $this->belongsTo(FinancialYear::class, 'financial_year_id');
    }
}
