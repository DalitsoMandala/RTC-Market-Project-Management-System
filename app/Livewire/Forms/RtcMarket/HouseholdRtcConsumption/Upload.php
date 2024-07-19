<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Indicator;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\BatchDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
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

    public function submitUpload()
    {

        ini_set('max_execution_time', 600);
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

                try {

                    try {
                        Excel::import(new HrcImport($userId, $sheets, $this->upload), $this->upload);
                    } catch (SheetImportException $e) {

                        $errors = $e->getErrors();
                        $sheet = $e->getSheet();


                        session()->flash('import_failures', $errors);
                        throw new UserErrorException('Errors on sheet: ' . $sheet);
                    }




                    $uuid = session()->get('uuid');
                    $batch_data = session()->get('batch_data');
                    if (empty($batch_data)) {
                        throw new UserErrorException("Your file has empty rows!");

                    }
                    $currentUser = Auth::user();
                    $table = ['household_rtc_consumption'];
                    if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                        $checkSubmission = Submission::where('period_id', $this->submissionPeriodId)
                            ->where('batch_type', 'batch')
                            ->where('user_id', auth()->user()->id)->first();
                        if ($checkSubmission) {

                            $this->reset('upload');

                            session()->flash('error', 'You have already submitted your batch data for this period!');
                        } else {

                            $submission = Submission::create([
                                'batch_no' => $uuid,
                                'form_id' => $this->selectedForm,
                                'user_id' => $currentUser->id,
                                'status' => 'approved',
                                'data' => json_encode($batch_data),
                                'batch_type' => 'batch',
                                'period_id' => $this->submissionPeriodId,
                                'table_name' => json_encode($table),
                                'is_complete' => 1,
                                'file_link' => $name,
                            ]);

                            $data = json_decode($submission->data, true);



                            foreach ($data as $row) {
                                $row['period_id'] = $this->submissionPeriodId;
                                HouseholdRtcConsumption::create($row);

                            }



                            session()->flash('success', 'Successfully submitted!');
                            $this->redirect(route('cip-internal-submissions') . '#batch-submission');
                        }

                    } else if ($currentUser->hasAnyRole('external')) {

                        $checkSubmission = Submission::where('period_id', $this->submissionPeriodId)
                            ->where('batch_type', 'batch')
                            ->where('user_id', auth()->user()->id)->first();
                        if ($checkSubmission) {

                            $this->reset('upload');
                            $this->dispatch('removeUploadedFile');
                            session()->flash('error', 'You have already submitted your batch data!');
                        } else {
                            Submission::create([
                                'batch_no' => $uuid,
                                'form_id' => $this->selectedForm,
                                'period_id' => $this->submissionPeriodId,
                                'user_id' => $currentUser->id,
                                'data' => json_encode($batch_data),
                                'batch_type' => 'batch',
                                'table_name' => json_encode($table),
                                'file_link' => $name,
                            ]);



                            session()->flash('success', 'Successfully submitted!');
                            $this->redirect(route('external-submissions') . '#batch-submission');

                        }
                    }

                } catch (UserErrorException $e) {

                    $this->reset('upload');

                    //   $this->dispatch('removeUploadedFile');
                    $this->dispatch('refresh');
                    session()->flash('error', $e->getMessage());
                }

            }

        } catch (\Exception $th) {
            //throw $th;

            session()->flash('error', 'Something went wrong!');
            Log::channel('system_log')->error($th);

        }

        $this->removeTemporaryFile();

    }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
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