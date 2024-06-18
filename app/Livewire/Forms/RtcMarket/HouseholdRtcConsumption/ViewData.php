<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\HouseholdImport\HrcImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\BatchDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class ViewData extends Component
{
    use LivewireAlert;
    use WithFileUploads;

    public $epa;

    public $section;

    public $district;

    public $enterprise;

    #[Validate('required')]
    public $upload;

    public $period;

    public $form_name = 'HOUSEHOLD CONSUMPTION FORM';
    public $batch_no;

    public $forms = [];

    #[Validate('required')]
    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];
    #[Validate('required')]
    public $selectedMonth;
    #[Validate('required')]
    public $selectedFinancialYear;
    #[Validate('required')]
    public $selectedProject;

    public $openSubmission = false;
    public function mount($batch = null)
    {

        if ($batch) {
            $data = HouseholdRtcConsumption::where('uuid', $batch)->first();
            $this->batch_no = $data->uuid ?? null;

            if (!$this->batch_no) {
                abort(404);
            }

        }

        $this->loadData();

    }

    public function loadData()
    {
        $form = Form::with(['project', 'indicators'])->where('name', $this->form_name)
            ->whereHas('project', fn($query) => $query->where('name', 'RTC MARKET'))->first();

        if (!$form) {
            abort(404);
        }
        $this->forms = Form::get();
        $this->projects = Project::get();
        $this->financialYears = FinancialYear::get();
        $this->months = ReportingPeriodMonth::get();

        $submissionPeriods = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $form->id)->get();

        $this->openSubmission = $submissionPeriods->count() > 0;

        $this->selectedProject = $form->project->id;
        $this->selectedForm = $form->id;
        if ($this->openSubmission) {
            $monthIds = $submissionPeriods->pluck('month_range_period_id')->toArray();
            $years = $submissionPeriods->pluck('financial_year_id')->toArray();
            $this->months = $this->months->whereIn('id', $monthIds);

            $this->financialYears = $this->financialYears->whereIn('id', $years);
        }

    }
    public function submitUpload()
    {

        $this->validate();
        try {
            //code...

            $userId = auth()->user()->id;

            if ($this->upload) {

                $name = 'hrc_' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs(path: 'imports', name: $name);
                $path = storage_path('app/imports/' . $name);
                $sheets = SheetNamesValidator::getSheetNames($path);

                try {
                    Excel::import(new HrcImport($userId, $sheets, $this->upload), $this->upload);

                    $uuid = session()->get('uuid');
                    $batch_data = session()->get('batch_data');

                    $currentUser = Auth::user();
                    $period = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $this->selectedForm)
                        ->where('financial_year_id', $this->selectedFinancialYear)->where('month_range_period_id', $this->selectedMonth)->first();
                    if (!$period) {
                        throw new \Exception("Something went wrong");

                    }
                    if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                        $checkSubmission = Submission::where('period_id', $period->id)->where('user_id', auth()->user()->id)->first();
                        if ($checkSubmission) {
                            throw new \Exception("You have already submitted your data!");
                        }

                        $submission = Submission::create([
                            'batch_no' => $uuid,
                            'form_id' => $this->selectedForm,
                            'user_id' => $currentUser->id,
                            'status' => 'approved',
                            'data' => json_encode($batch_data),
                            'batch_type' => 'batch',
                            'period_id' => $period->id,
                        ]);

                        $data = json_decode($submission->data, true);

                        foreach ($data as $row) {

                            $insert = HouseholdRtcConsumption::create($row);

                        }

                        $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                        $currentUser->notify(new BatchDataAddedNotification($uuid, $link));
                        $this->dispatch('notify');

                    } else if ($currentUser->hasAnyRole('external')) {

                        Submission::create([
                            'batch_no' => $uuid,
                            'form_id' => $this->selectedForm,
                            'period_id' => $period->id,
                            'user_id' => $currentUser->id,
                            'data' => json_encode($batch_data),
                            'batch_type' => 'batch',
                        ]);

                    }

                    $this->reset();
                    $this->loadData();
                    $this->dispatch('removeUploadedFile');
                    $this->dispatch('refresh');
                    session()->flash('success', 'Successfully uploaded your data!');
                } catch (\Exception $e) {

                    $this->reset();
                    $this->loadData();
                    $this->dispatch('removeUploadedFile');
                    $this->dispatch('refresh');
                    session()->flash('error', $e->getMessage());
                }

            }

        } catch (\Throwable $th) {
            //throw $th;

            session()->flash('error', $th->getMessage());
            // Log::error($th);

        }

        $this->removeTemporaryFile();

    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new HrcExport, 'household_rtc_consumption_template_' . $time . '.xlsx');

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
                    \Log::error('Failed to delete temporary file: ' . $e->getMessage());
                }
            }

        }

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.view-data');
    }
}
