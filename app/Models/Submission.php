<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{

    use HasFactory;
    protected $table   = 'submissions';
    protected $guarded = [];

    public function period()
    {
        return $this->belongsTo(SubmissionPeriod::class, 'reporting_period_month_id');
    }

    // check reporting period through submission period
    public function reporting_period()
    {
        return $this->hasManyThrough(ReportingPeriodMonth::class, SubmissionPeriod::class, '', 'id', 'id', 'reporting_period_month_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id');
    }
}
