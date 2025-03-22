<?php

namespace App\Traits;


use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Project;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Validators\ValidationException;

trait UploadDataTrait
{
    //


    public $variable;
    public $rowId;
    public $location_data = [];
    public $submissionPeriodId;
    public $period;
    public $forms = [];
    public $selectedForm;
    public $months = [];
    public $financialYears = [];
    public $selectedFinancialYear;
    public $projects = [];
    public $selectedProject;
    public $selectedMonth;
    public $openSubmission = false;
    public $indicators;
    public $selectedIndicator;
    public $organisation_id;
    public $loadAggregate = [];
    public $aggregateData = [];
    public $checkifAggregate = false;
    public $showReport = false;
    public $validated = true;
    public $routePrefix;
    public $targetSet = false;
    public $targetIds = [];
    #[Validate('required')]
    public $upload;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public $queue = false;

    public $currentRoute;

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        //import ID
        $this->importId = Uuid::uuid4()->toString();
        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();
    }

    /**
     * Validate that all required IDs are present.
     */
    protected function validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        if (empty($form_id) || empty($indicator_id) || empty($financial_year_id) || empty($month_period_id) || empty($submission_period_id)) {
            Log::error("Missing required IDs: form_id=$form_id, indicator_id=$indicator_id, financial_year_id=$financial_year_id, month_period_id=$month_period_id, submission_period_id=$submission_period_id");
            abort(404);
        }
    }

    /**
     * Find and validate the related models.
     */
    protected function findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        $this->selectedForm = $this->findModelOrFail(Form::class, $form_id)->id;
        $this->selectedIndicator = $this->findModelOrFail(Indicator::class, $indicator_id)->id;
        $this->selectedFinancialYear = $this->findModelOrFail(FinancialYear::class, $financial_year_id)->id;
        $this->selectedMonth = $this->findModelOrFail(ReportingPeriodMonth::class, $month_period_id)->id;
        $this->submissionPeriodId = $this->findModelOrFail(SubmissionPeriod::class, $submission_period_id)->id;
    }

    /**
     * Find a model by ID or abort with a 404 error.
     */
    protected function findModelOrFail($modelClass, $id)
    {
        $model = $modelClass::find($id);
        if (!$model) {
            Log::error("$modelClass with ID $id not found");
            abort(404);
        }
        return $model;
    }

    /**
     * Check if the submission period is open and targets are set.
     */
    protected function checkSubmissionPeriodAndTargets()
    {
        $submissionPeriod = $this->getOpenSubmissionPeriod();
        $targets = $this->getSubmissionTargets();
        $this->targetIds = $targets->pluck('id')->toArray();

        $this->openSubmission = $submissionPeriod && $this->hasOrganisationTargets($targets);
        $this->targetSet = $this->openSubmission;
    }

    /**
     * Get the open submission period for the current selection.
     */
    protected function getOpenSubmissionPeriod()
    {
        return SubmissionPeriod::where('form_id', $this->selectedForm)
            ->where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->where('month_range_period_id', $this->selectedMonth)
            ->where('is_open', true)
            ->first();
    }

    /**
     * Get the submission targets for the current selection.
     */
    protected function getSubmissionTargets()
    {
        return SubmissionTarget::where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->get();
    }

    /**
     * Check if the organisation has targets for the given submission targets.
     */
    protected function hasOrganisationTargets($targets)
    {
        $user = User::find(auth()->user()->id);
        $targetIds = $targets->pluck('id');

        return OrganisationTarget::where('organisation_id', $user->organisation->id)
            ->whereHas('submissionTarget', function ($query) use ($targetIds) {
                $query->whereIn('submission_target_id', $targetIds);
            })
            ->exists();
    }


    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function uploadFile($file, $importId,  $importClass)
    {

        $name = 'src' . time() . '.' . $file->getClientOriginalExtension();
        $file->storeAs('public/imports', $name);
        $path = storage_path('app/public/imports/' . $name);

        // Use the provided import class dynamically
        Excel::import(
            new $importClass(
                cacheKey: $importId,
                filePath: $path,
                submissionDetails: [
                    'submission_period_id' => $this->submissionPeriodId,
                    'organisation_id' => Auth::user()->organisation->id,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'period_month_id' => $this->selectedMonth,
                    'form_id' => $this->selectedForm,
                    'user_id' => Auth::user()->id,
                    'batch_type' => 'batch',
                    'is_complete' => 1,
                    'file_link' => $name,
                    'batch_no' => $this->importId,
                    'route' => $this->currentRoute,
                ]
            ),
            $path
        );

        return $name; // Return the file name for further use

    }

    public function removeTemporaryFile()
    {
        // Get the temporary file path
        if ($this->upload) {
            $temporaryFilePath = $this->upload->getRealPath();

            // Check if the file exists and delete it
            if (file_exists($temporaryFilePath)) {
                try {
                    unlink($temporaryFilePath);
                } catch (\Exception $e) {
                    // Handle the exception (e.g., log the error)
                    Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }
        }
    }
}
