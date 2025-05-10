<?php

namespace App\Helpers;

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Submission;
use App\Exports\JsonExport;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\UserErrorException;
use Illuminate\Support\Facades\Storage;
use Spatie\SimpleExcel\SimpleExcelWriter;
use App\Notifications\ImportSuccessNotification;

class SubmitAggregateData
{
    public function storeData($data)
    {
        $fileName = 'exported_data_' . time() . '.xlsx';

        $directory = 'public/exports';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }

        // Store the file in the `storage/app/exports` directory
        $filePath = storage_path('app/public/exports/' . $fileName);

        $writer = SimpleExcelWriter::create($filePath)->addHeader([
            'Title',
            'Value',
        ]);

        foreach ($data as $key => $value) {
            $writer->addRow([$key, (float) $value]);
        }
        $writer->close();  // Finalize the file
        return $fileName;
    }

    function submit_aggregate_data($data, $user, $submissionPeriodId, $selectedForm, $selectedIndicator, $selectedFinancialYear, $redirectRoute, $roleType)
    {
        try {
            $uuid = Uuid::uuid4()->toString();
            $currentUser = Auth::user();

            $file = $this->storeData($data);

            // Check if a submission already exists
            $checkSubmission = Submission::where('period_id', $submissionPeriodId)
                ->where('batch_type', 'aggregate')
                ->where(function ($query) {
                    $query
                        ->where('status', '=', 'pending')
                        ->orWhere('status', '=', 'approved');
                })
                ->where('user_id', $currentUser->id)
                ->first();

            if ($checkSubmission) {
                session()->flash('error', 'You have already submitted your data for this period!');
                return false;
            }

            // Create the submission
            $submission = Submission::create([
                'batch_no' => $uuid,
                'form_id' => $selectedForm,
                'user_id' => $currentUser->id,
                'status' => $roleType === 'manager' ? 'approved' : 'pending',  // Status based on role
                'batch_type' => 'aggregate',
                'is_complete' => 1,
                'period_id' => $submissionPeriodId,
                'table_name' => 'reports',
                'file_link' => $file,
            ]);

            $period = SubmissionPeriod::find($submission->period_id);

            // Create the submission report
            SubmissionReport::create([
                'indicator_id' => $selectedIndicator,
                // 'submission_id' => $submission->id,
                'financial_year_id' => $selectedFinancialYear,
                'submission_period_id' => $submissionPeriodId,
                'period_month_id' => $period->month_range_period_id,
                'organisation_id' => $user->organisation->id,
                'user_id' => $user->id,
                'status' => $roleType === 'manager' ? 'approved' : 'pending',  // Status based on role
                'data' => json_encode($data),
                'uuid' => $uuid,
                'file_name' => null
            ]);

            $user = User::find($user->id);
            //    $user->notify(new JobNotification($uuid, 'Your file has finished importing, you can find your submissions on the submissions page!', []));
            if ($user->hasAnyRole('manager')) {
                $user->notify(
                    new ImportSuccessNotification(
                        $uuid,
                        route('cip-submissions', [
                            'batch' => $uuid,
                        ], true) . '#aggregate-submission'
                    )
                );
            } else if ($user->hasAnyRole('admin')) {
                $user->notify(
                    new ImportSuccessNotification(
                        $uuid,
                        route('admin-submissions', [
                            'batch' => $uuid,
                        ], true) . '#aggregate-submission'
                    )
                );
            } else if ($user->hasAnyRole('staff')) {
                $user->notify(new ImportSuccessNotification(
                    $uuid,
                    route('cip-staff-submissions', [
                        'batch' => $uuid,
                    ], true) . '#aggregate-submission'
                ));
            } else {
                $user->notify(new ImportSuccessNotification(
                    $uuid,
                    route('external-submissions', [
                        'batch' => $uuid,
                    ], true) . '#aggregate-submission'
                ));
            }

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
