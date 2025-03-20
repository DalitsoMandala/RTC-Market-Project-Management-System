<?php

namespace App\Livewire\Forms\RtcMarket\RtcRecruitment;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\ImportError;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Log;
use App\Helpers\SheetNamesValidator;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;
use App\Exceptions\UserErrorException;
use App\Notifications\JobNotification;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Imports\rtcmarket\RtcProductionImport\RpmFarmerImport;
use App\Exports\ExportFarmer\RtcProductionFarmersMultiSheetExport;
use App\Imports\ImportFarmer\RtcProductionFarmersMultiSheetImport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Traits\CheckProgressTrait;

class Upload extends Component
{

    use WithFileUploads;
    use LivewireAlert;
    use CheckProgressTrait;
    #[Validate('required')]
    public $upload;
    public $variable;
    public $rowId;
    public $selectedIndicator;
    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $selectedForm;

    public $submissionPeriodId;

    public $openSubmission = false;

    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId, $showReport;

    public $queue = false;
    public $targetSet = false;
    public $targetIds = [];
    public $currentRoute;
    public function submitUpload()
    {

        try {
            $this->validate();
        } catch (Throwable $e) {
            $this->dispatch('errorRemove');
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }
        try {
            //code...

            $userId = auth()->user()->id;

            if ($this->upload) {

                $name = 'rpmf' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs('public/imports', $name);

                // Use storage_path to get the absolute path
                $path = storage_path('app/public/imports/' . $name);






                try {


                    Excel::import(new RtcProductionFarmersMultiSheetImport(cacheKey: $this->importId, filePath: $path, submissionDetails: [

                        'submission_period_id' => $this->submissionPeriodId,
                        'organisation_id' => Auth::user()->organisation->id,
                        'financial_year_id' => $this->selectedFinancialYear,
                        'period_month_id' => $this->selectedMonth,
                        'form_id' => $this->selectedForm,
                        'user_id' => Auth::user()->id,
                        'batch_type' => 'batch',
                        'table_name' => 'household_rtc_consumption',
                        'is_complete' => 1,
                        'file_link' => $name,
                        'batch_no' => $this->importId,
                        'route' => $this->currentRoute,


                    ]), $path);
                    $this->checkProgress();
                } catch (ExcelValidationException $th) {

                    session()->flash('error', $th->getMessage());
                    Log::error($th);
                    $this->redirect(url()->previous());
                }
            }
        } catch (\Exception $th) {
            //throw $th;

            session()->flash('error', 'Something went wrong!');
            Log::error($th);
            $this->redirect(url()->previous());
        }

        $this->removeTemporaryFile();
    }



    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }


    public function checkErrors()
    {

        $importError = ImportError::where('uuid', $this->importId)->where('user_id', auth()->user()->id)->first();

        if ($importError) {
            $this->Import_errors = json_decode($importError->errors, true);
            $this->importing = false;

            $this->reset('upload');
            if ($importError->type == 'validation') {
                session()->flash('import_failures', $this->Import_errors);
            } else {
                session()->flash('error', $this->Import_errors);
            }

            $this->importingFinished = true;
            $userId = auth()->user()->id;

            $importJob = JobProgress::where('user_id', $userId)->where('job_id', $this->importId)->first();
            if ($importJob) {
                $importJob->update([
                    'status' => 'failed',
                    'is_finished' => true
                ]);
            }
            $importError->delete();
        } else {
            // Check progress

            $userId = auth()->user()->id;
            $importJob = JobProgress::where('user_id', $userId)->where('job_id', $this->importId)->first();

            if ($importJob) {
                $this->progress = $importJob->progress;
            }

            $this->dispatch('progress-update', progress: $this->progress ?? 0);

            if ($this->progress > 0 && $this->progress == 100) {

                $this->reset('upload');
                $this->importing = false;
                $this->importingFinished = true;
                $this->dispatch('import-finished');

                if ($importJob) {
                    $importJob->update([
                        'status' => 'completed',
                        'is_finished' => true
                    ]);
                    $this->importId = Uuid::uuid4()->toString();
                    $this->sendToLocation();
                }
            }
        }
    }

    // public function checkProgress()
    // {
    //     $jobProgress = JobProgress::where('cache_key', $this->importId)->first();

    //     $this->progress = $jobProgress ? $jobProgress->progress : 0;
    //     $this->importing = true;
    //     $this->importingFinished = false;


    //     if ($this->progress == 100) {




    //         if ($jobProgress->status == 'failed') {
    //             Cache::forget($this->importId);
    //             session()->flash('error', 'An error occurred during the import! --- ' . $jobProgress->error);

    //             $this->redirect(url()->previous());
    //         } else if ($jobProgress->status == 'completed') {


    //             $user = User::find(auth()->user()->id);
    //             Cache::forget($this->importId);

    //             if ($user->hasAnyRole('external')) {
    //                 session()->flash('success', 'Successfully submitted!');
    //                 $this->redirect(route('external-submissions') . '#batch-submission');
    //             } else if ($user->hasAnyRole('staff')) {
    //                 session()->flash('success', 'Successfully submitted!');
    //                 $this->redirect(route('cip-staff-submissions') . '#batch-submission');
    //             } else {
    //                 session()->flash('success', 'Successfully submitted!');
    //                 $this->redirect(route('cip-submissions') . '#batch-submission');
    //             }
    //         }
    //     }
    // }


    public function send()
    {
        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('external')) {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('external-submissions') . '#batch-submission');
        } else if ($user->hasAnyRole('staff')) {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('cip-staff-submissions') . '#batch-submission');
        } else {
            session()->flash('success', 'Successfully submitted!');
            return redirect(route('cip-submissions') . '#batch-submission');
        }
    }



    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id, $uuid)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);
        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);
        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();

            $target = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)

                ->get();
            $user = User::find(auth()->user()->id);

            $targets = $target->pluck('id');
            $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)
                ->whereHas('submissionTarget', function ($query) use ($targets) {
                    $query->whereIn('submission_target_id', $targets);
                })
                ->get();
            $this->targetIds = $target->pluck('id')->toArray();


            if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

                $this->openSubmission = true;
                $this->targetSet = true;
            } else {
                $this->openSubmission = false;
                $this->targetSet = false;
            }
        }

        $this->importId = Uuid::uuid4()->toString();
        $this->currentRoute = url()->current();
    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new RtcProductionFarmersMultiSheetExport(true), 'rtc_production_marketing_farmers_template.xlsx');
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
    public function render()
    {
        return view('livewire.forms.rtc-market.rtc-recruitment.upload');
    }
}
