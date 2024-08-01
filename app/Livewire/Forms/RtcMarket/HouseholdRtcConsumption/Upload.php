<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Jobs\RandomNames;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\ImportError;
use App\Models\Indicator;
use App\Models\JobProgress;
use App\Models\JobProgressCheck;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use App\Notifications\BatchDataAddedNotification;
use App\Notifications\JobNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use Throwable;

class Upload extends Component
{
    use WithFileUploads;
    use LivewireAlert;
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
    public $importId;

    public $queue = false;
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

                $name = 'hh_' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs('public/imports', $name);

                $path = public_path('storage\imports\\' . $name);
                $sheets = SheetNamesValidator::getSheetNames($path);

                $this->updateJobStatus();

                try {



                    $table = ['household_rtc_consumption'];
                    $this->importing = true;
                    $this->importingFinished = false;


                    $this->dispatch('notify');

                    Excel::queueImport(new HrcImport($userId, $sheets, $path, $this->importId, [
                        'submission_period_id' => $this->submissionPeriodId,
                        'organisation_id' => Auth::user()->organisation->id,
                        'financial_year_id' => $this->selectedFinancialYear,
                        'period_month_id' => $this->selectedMonth,
                        'form_id' => $this->selectedForm,
                        'user_id' => Auth::user()->id,
                        'status' => 'approved',
                        'data' => [],
                        'batch_type' => 'batch',
                        'period_id' => $this->submissionPeriodId,
                        'table_name' => json_encode($table),
                        'is_complete' => 1,
                        'file_link' => $name,

                    ]), $path);




                    $user = User::find($userId);
                    $user->notify(new JobNotification($this->importId, 'File import has started you will be notified when the file has finished importing!'));







                } catch (UserErrorException $e) {

                    $this->reset('upload');
                    $this->importing = false;
                    $this->importingFinished = true;


                    session()->flash('error', $e->getMessage());
                }

            }

        } catch (\Exception $th) {
            //throw $th;
            $this->reset('upload');
            session()->flash('error', 'Something went wrong!');
            Log::channel('system_log')->error($th);

        }

        $this->removeTemporaryFile();

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
                $importJob->update(['status' => 'failed', 'is_finished' => true]);
            }
            $importError->delete();

            cache()->clear();
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
                    $importJob->update(['status' => 'completed', 'is_finished' => true]);
                    $this->importId = Uuid::uuid4()->toString();
                    $this->sendToLocation();
                }



            }





        }


    }

    public function sendToLocation()
    {


        $user = auth()->user();
        cache()->clear();
        if ($user->hasAnyRole('external')) {
            session()->flash('success', 'Successfully submitted!');
            $this->redirect(route('external-submissions') . '#batch-submission');
        } else {
            session()->flash('success', 'Successfully submitted!');
            $this->redirect(route('cip-internal-submissions') . '#batch-submission');
        }


    }

    public function updateJobStatus()
    {

        $pendingjob = JobProgress::where('user_id', auth()->user()->id)->where('job_id', $this->importId)->where('status', 'processing')->first();
        if ($pendingjob) {
            throw new UserErrorException('You have a pending import running in your background! Please wait... You can see the progress in your submissions page');
        }

        $job = JobProgress::where('user_id', auth()->user()->id)->where('job_id', $this->importId)->where('is_finished', true)->first();
        if ($job) {

            $job->update([
                'user_id' => auth()->user()->id,
                'job_id' => $this->importId,
                'status' => 'pending',
                'is_finished' => false,
            ]);
        } else {
            JobProgress::create([
                'user_id' => auth()->user()->id,
                'job_id' => $this->importId,
                'status' => 'pending',
                'form_name' => Form::find($this->selectedForm)->name,
            ]);
        }



    }

    public function checkJobProgress()
    {
        // $job = JobProgress::where('user_id', auth()->user()->id)->where('status', 'processing')->orWhere('status', 'pending')->first();
        // if ($job) {
        //     $this->importing = true;
        // }

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

            if ($submissionPeriod) {

                $this->openSubmission = true;
                $user = Auth::user();
                $organisation = $user->organisation;

                $getSubmissionType = ResponsiblePerson::where('indicator_id', $this->selectedIndicator)->where('organisation_id', $organisation->id)->first();
                if ($getSubmissionType) {
                    $this->showReport = $getSubmissionType->type_of_submission == 'normal' ? false : true;
                }
            } else {
                $this->openSubmission = false;
            }
        }



        $this->importId = $uuid;
        $finishedJob = JobProgress::where('user_id', auth()->user()->id)->where('job_id', $this->importId)->where('status', 'completed')->where('is_finished', true)->first();
        if ($finishedJob) {


            $this->importId = Uuid::uuid4()->toString();

        }

    }
    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new HrcExport, 'household_rtc_consumption_template.xlsx');

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
                    \Log::channel('system')->error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }

        }

    }



    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.upload');
    }
}