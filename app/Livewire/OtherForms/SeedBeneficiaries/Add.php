<?php

namespace App\Livewire\OtherForms\SeedBeneficiaries;

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
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
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
    public $openSubmission = true;

    public $targetSet = false;
    public $targetIds = [];
    public $routePrefix;
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
    ];

    public $varieties = [];
    public $selectedVarieties = [];


    public function save()
    {


        $collect = collect($this->selectedVarieties);
        if ($collect->isNotEmpty()) {
            $this->variety_received = implode(',', $collect->pluck('id')->toArray());
        } else {
            $this->variety_received = null;
        }

        try {

            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {
            $uuid = Uuid::uuid4()->toString();



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
                'violet' => $collect->contains('name', 'violet'),
                'rosita' =>  $collect->contains('name', 'rosita'),
                'chuma' => $collect->contains('name', 'chuma'),
                'mwai' => $collect->contains('name', 'mwai'),
                'zikomo' =>  $collect->contains('name', 'zikomo'),
                'thandizo' =>  $collect->contains('name', 'thandizo'),
                'royal_choice' => $collect->contains('name', 'royal choice'),
                'kaphulira' => $collect->contains('name', 'kaphulira'),
                'chipika' => $collect->contains('name', 'chipika'),
                'mathuthu' => $collect->contains('name', 'mathuthu'),
                'kadyaubwelere' => $collect->contains('name', 'kadyaubwelere'),
                'sungani' => $collect->contains('name', 'sungani'),
                'kajiyani' => $collect->contains('name', 'kajiyani'),
                'mugamba' => $collect->contains('name', 'mugamba'),
                'kenya' => $collect->contains('name', 'kenya'),
                'nyamoyo' => $collect->contains('name', 'nyamoyo'),
                'anaakwanire' => $collect->contains('name', 'anaakwanire'),
                'other' => $collect->contains('name', 'other'),
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
                'status' => 'approved'
            ]);

            session()->flash('success', 'Seed Beneficiary added successfully.');
            $this->redirect(url()->previous());
        } catch (\Throwable $th) {


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

        $this->crop = 'Potato';
        $this->routePrefix = Route::current()->getPrefix();
        $this->getVarieties($this->crop);
    }
    public function getVarieties($crop)
    {
        if ($crop === 'OFSP') {
            $this->varieties = [
                ['id' => 1, 'name' => 'royal_choice'],
                ['id' => 2, 'name' => 'kaphulira'],
                ['id' => 3, 'name' => 'chipika'],
                ['id' => 4, 'name' => 'mathuthu'],
                ['id' => 5, 'name' => 'kadyaubwelere'],
                ['id' => 6, 'name' => 'sungani'],
                ['id' => 7, 'name' => 'kajiyani'],
                ['id' => 8, 'name' => 'mugamba'],
                ['id' => 9, 'name' => 'kenya'],
                ['id' => 10, 'name' => 'nyamoyo'],
                ['id' => 11, 'name' => 'anaakwanire'],
                ['id' => 12, 'name' => 'other'],
            ];
        } else if ($crop === 'Potato') {
            $this->varieties = [
                ['id' => 1, 'name' => 'violet'],
                ['id' => 2, 'name' => 'rosita'],
                ['id' => 3, 'name' => 'chuma'],
                ['id' => 4, 'name' => 'mwai'],
                ['id' => 5, 'name' => 'zikomo'],
                ['id' => 6, 'name' => 'thandizo'],
                ['id' => 7, 'name' => 'other'],
            ];
        } else {
            $this->varieties = [];
        }
    }
    public function updatedCrop($value)
    {
        $this->getVarieties($value);
        $this->dispatch('get-varieties', data: $this->varieties);
    }




    public function render()
    {
        return view('livewire.other-forms.seed-beneficiaries.add');
    }
}
