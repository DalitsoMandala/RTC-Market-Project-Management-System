<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

use Carbon\Carbon;
use App\Models\Crop;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\JobProgress;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use Livewire\WithFileUploads;
use App\Models\SeedBeneficiary;
use App\Traits\ManualDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\SeedBeneficiariesExport;
use App\Imports\SeedBeneficiariesImport;
use App\Exceptions\ExcelValidationException;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Add extends Component
{
    use LivewireAlert;
    use WithFileUploads;
    use ManualDataTrait;
    public $district;
    public $epa;
    public $section;
    public $name_of_aedo;
    public $aedo_phone_number;
    public $date;
    public $name_of_recipient;
    public $village;
    public $sex;
    public $age;
    public $marital_status;
    public $hh_head;
    public $household_size;
    public $children_under_5;
    public $variety_received;
    public $bundles_received;
    public $national_id;
    public $phone_number;
    public $crop;
    public $upload;
    public $form_name;
    public $progress = 0;
    public $Import_errors = [];
    public $importing = false;
    public $importingFinished = false;

    public $importProgress = 0;
    public $importId;

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject, $selectedIndicator,
        $submissionPeriodId;
    public $selectedForm;
    public $openSubmission = false;

    public $targetSet = false;
    public $targetIds = [];
    public $routePrefix;
    public $type_of_plot,
        $type_of_actor,
        $season_type,
        $group_name;

    public $plots = [];
    public $seasons = [];


    protected $rules = [
        'district' => 'required|string|max:255',
        'epa' => 'required|string|max:255',
        'section' => 'required|string|max:255',
        'name_of_aedo' => 'required|string|max:255',
        'aedo_phone_number' => 'required|string|max:20',
        'date' => 'required|date',
        'name_of_recipient' => 'required|string|max:255',
        'village' => 'required|string|max:255',
        'sex' => 'required|integer|in:1,2',
        'age' => 'required|integer|min:1',
        'marital_status' => 'required|integer|in:1,2,3,4',
        'hh_head' => 'required|integer|in:1,2,3',
        'household_size' => 'required|integer|min:1',
        'children_under_5' => 'required|integer|min:0',
        'variety_received' => 'required|string|max:255',
        'bundles_received' => 'required|integer|min:1',
        'national_id' => 'nullable|string|max:20',
        'phone_number' => 'nullable|max:255',
        'crop' => 'required|string|in:OFSP,Potato,Cassava',
        'type_of_actor' => 'nullable|string',
        'type_of_plot' => 'nullable|string',
        'season_type' => 'required',
        'group_name' => 'nullable|string',
    ];

    public $varieties = [];
    public $selectedVarieties = [];


    public function save()
    {


        $collect = collect($this->selectedVarieties);
        if ($collect->isNotEmpty()) {
            $this->variety_received = implode(',', $collect->pluck('name')->toArray());
        } else {
            $this->variety_received = null;
        }

        try {
            $this->validate();
        } catch (\Throwable $e) {
            //       session()->flash('validation_error', 'There are errors in the form.');
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }

        try {
            $uuid = Uuid::uuid4()->toString();

            DB::beginTransaction();
            SeedBeneficiary::create([
                'district' => $this->district,
                'epa' => $this->epa,
                'section' => $this->section,
                'name_of_aedo' => $this->name_of_aedo,
                'aedo_phone_number' => $this->aedo_phone_number,
                'date' => $this->date,
                'name_of_recipient' => $this->name_of_recipient,
                'village' => $this->village,
                'sex' => $this->sex,
                'age' => $this->age,
                'marital_status' => $this->marital_status,
                'hh_head' => $this->hh_head,
                'household_size' => $this->household_size,
                'children_under_5' => $this->children_under_5,
                'variety_received' => $this->variety_received,
                'bundles_received' => $this->bundles_received,
                'phone_number' => $this->phone_number ?? 'NA',
                'national_id' => $this->national_id ?? 'NA',
                'crop' => $this->crop,
                'uuid' => $uuid,
                'user_id' => auth()->user()->id,
                'submission_period_id' => $this->submissionPeriodId,
                'period_month_id' => $this->selectedMonth,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'status' => 'approved',
                'type_of_actor' => $this->type_of_actor,
                'type_of_plot' => $this->type_of_plot,
                'season_type' => $this->season_type,
                'group_name' => $this->group_name,
                'year' => Carbon::parse($this->date)->year
            ]);

            $this->clearErrorBag();
            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/seed-distribution-register/view">View Submission here</a>',
            ]);
            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();

            $this->dispatch('show-alert', data: [
                'type' => 'error',
                'message' => 'Something went wrong!'
            ]);

            Log::error($th->getMessage());

            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
        }
    }


    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();

        $this->crop = 'Potato';
        $this->getVarieties($this->crop);
    }

    public function getVarieties($crop)
    {
        if ($crop == 'OFSP') {
            $crop = 'Sweet potato';
        }
        $this->varieties = Crop::where('name', $crop)->first()->varieties->toArray();
    }
    public function updatedCrop($value)
    {

        $this->getVarieties($value);

        $this->dispatch('get-varieties', data: $this->varieties);
    }




    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.other-forms.seed-beneficiaries.add');
    }
}
