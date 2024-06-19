<?php

namespace App\Livewire\Forms\RtcMarket\RtcProductionProcessors;

use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Helpers\SheetNamesValidator;
use App\Imports\rtcmarket\RtcProductionImport\RpmProcessorImport;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmProcessorConcAgreement;
use App\Models\RpmProcessorDomMarket;
use App\Models\RpmProcessorFollowUp;
use App\Models\RpmProcessorInterMarket;
use App\Models\RtcProductionProcessor;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\BatchDataAddedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;

class View extends Component
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

    public $form_name = 'RTC PRODUCTION AND MARKETING FORM PROCESSORS';

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
            $data = RtcProductionProcessor::where('uuid', $batch)->first();
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

            if ($this->upload) {
                $userId = auth()->user()->id;

                $name = 'rpmF_' . time() . '.' . $this->upload->getClientOriginalExtension();
                $this->upload->storeAs(path: 'imports', name: $name);
                $path = storage_path('app/imports/' . $name);
                $sheets = SheetNamesValidator::getSheetNames($path);

                try {
                    Excel::import(new RpmProcessorImport($userId, $sheets, $this->upload), $this->upload);

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
                        //dd($data);
                        // insert into tables
                        $location = null;
                        foreach ($data['main'] as $mainSheet) {
                            $mainSheet['is_registered'] = $mainSheet['is_registered'] === 'YES' ? true : false;
                            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
                            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
                            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
                            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;
                            RtcProductionProcessor::create($mainSheet);

                        }

                        foreach ($data['followup'] as $mainSheet) {

                            $mainSheet['sells_to_domestic_markets'] = $mainSheet['sells_to_domestic_markets'] === 'YES' ? true : false;
                            $mainSheet['has_rtc_market_contract'] = $mainSheet['has_rtc_market_contract'] === 'YES' ? true : false;
                            $mainSheet['sells_to_international_markets'] = $mainSheet['sells_to_international_markets'] === 'YES' ? true : false;
                            $mainSheet['uses_market_information_systems'] = $mainSheet['uses_market_information_systems'] === 'YES' ? true : false;
                            $mainTable = RpmProcessorFollowUp::create($mainSheet);

                            // follow up data

                        }

                        foreach ($data['agreement'] as $mainSheet) {

                            $mainTable = RpmProcessorConcAgreement::create($mainSheet);

                            // conc agreement

                        }

                        foreach ($data['market'] as $mainSheet) {

                            $mainTable = RpmProcessorDomMarket::create($mainSheet);

                            // dom market

                        }

                        foreach ($data['intermarket'] as $mainSheet) {

                            $mainTable = RpmProcessorInterMarket::create($mainSheet);

                            // inter market

                        }

                        $currentUser = Auth::user();
                        $link = 'forms/rtc-market/rtc-production-and-marketing-form-processors/' . $uuid . '/view';
                        $currentUser->notify(new BatchDataAddedNotification($uuid, $link));
                        $this->dispatch('notify');

                    } else if ($currentUser->hasAnyRole('external')) {

                        Submission::create([
                            'batch_no' => $uuid,
                            'form_id' => $this->selectedForm,
                            'period_id' => $period,
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
                    # code...
                    $this->reset();
                    $this->loadData();
                    $this->dispatch('removeUploadedFile');
                    $this->dispatch('refresh');
                    session()->flash('error', $e->getMessage());
                }

            }

        } catch (\Exception $th) {
            //throw $th;

            session()->flash('error', 'Something went wrong!');
            Log::channel('system_log')->error($th->getMessage());

        }

        $this->removeTemporaryFile();

    }

    public function downloadTemplate()
    {
        $time = Carbon::parse(now())->format('d_m_Y_H_i_s');

        return Excel::download(new RtcProductionFarmerWorkbookExport, 'rtc_production_marketing_farmers' . $time . '.xlsx');

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
        return view('livewire.forms.rtc-market.rtc-production-processors.view');
    }
}
