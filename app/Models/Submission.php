<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Submission extends Model
{
    use HasFactory;

    protected $table = 'submissions';

    protected $guarded = [];

    /**
     * User who created the submission.
     * Includes soft-deleted users when eager-loaded with withTrashed().
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Form associated with the submission.
     */
    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    /**
     * Submission period associated with this submission.
     */
    public function period(): BelongsTo
    {
        return $this->belongsTo(
            SubmissionPeriod::class,
            'submission_period_new_id'
        );
    }

    /**
     * Reporting period month.
     */
    public function reporting_period_months(): BelongsTo
    {
        return $this->belongsTo(
            ReportingPeriodMonth::class,
            'reporting_period_month_id'
        );
    }

    /**
     * Reporting period through the submission period.
     */
    public function reporting_period(): HasManyThrough
    {
        return $this->hasManyThrough(
            ReportingPeriodMonth::class,
            SubmissionPeriod::class,
            'id',
            'id',
            'submission_period_new_id',
            'reporting_period_month_id'
        );
    }
}
