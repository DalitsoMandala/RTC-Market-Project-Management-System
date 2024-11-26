<?php

namespace App\Helpers;

use Ramsey\Uuid\Uuid;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserErrorException;

class SubmitAggregateData
{


    function submit_aggregate_data($data, $user, $submissionPeriodId, $selectedForm, $selectedIndicator, $selectedFinancialYear, $redirectRoute, $roleType)
    {
        $uuid = Uuid::uuid4()->toString();
        $currentUser = Auth::user();

        // Check if a submission already exists
        $checkSubmission = Submission::where('period_id', $submissionPeriodId)
            ->where('batch_type', 'aggregate')
            ->where(function ($query) {
                $query->where('status', '=', 'pending')
                    ->orWhere('status', '=', 'approved');
            })
            ->where('user_id', $currentUser->id)
            ->first();

        if ($checkSubmission) {
            session()->flash('error', 'You have already submitted your data for this period!');
            return false;
        }

        try {
            // Create the submission
            $submission = Submission::create([
                'batch_no' => $uuid,
                'form_id' => $selectedForm,
                'user_id' => $currentUser->id,
                'status' => $roleType === 'internal' ? 'approved' : 'pending', // Status based on role
                'batch_type' => 'aggregate',
                'is_complete' => 1,
                'period_id' => $submissionPeriodId,
                'table_name' => 'reports',
            ]);

            $period = SubmissionPeriod::find($submission->period_id);

            // Create the submission report
            SubmissionReport::create([
                'indicator_id' => $selectedIndicator,
                'submission_id' => $submission->id,
                'financial_year_id' => $selectedFinancialYear,
                'submission_period_id' => $submissionPeriodId,
                'period_month_id' => $period->month_range_period_id,
                'organisation_id' => $user->organisation->id,
                'user_id' => $user->id,
                'status' => $roleType === 'internal' ? 'approved' : 'pending', // Status based on role
                'data' => json_encode($data),
                'uuid' => $uuid
            ]);

            // Success message and redirect
            session()->flash('success', 'Successfully submitted!');
            return redirect($redirectRoute . '#aggregate-submission');
        } catch (UserErrorException $e) {
            // Log the error for debugging
            Log::error('Submission error: ' . $e->getMessage());
            session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
            return false;
        }
    }
}