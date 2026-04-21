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

class Edit extends Component
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
    public $crop = 'Potato';
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
    public $uniqueId;
    protected $rules = [
        'district' => 'required|string|max:255',
        'epa' => 'required|string|max:255',
        'section' => 'required|string|max:255',
        'name_of_aedo' => 'required|string|max:255',
        'aedo_phone_number' => 'required|string|max:20',
        'date' => 'required|date',
        'name_of_recipient' => 'required|string|max:255',
        'village' => 'required|string|max:255',
        'sex' => 'required|string',
        'age' => 'required|integer|min:1',
        'marital_status' => 'nullable|string',
        'hh_head' => 'required|string',
        'household_size' => 'required|integer|min:1',
        'children_under_5' => 'required|integer|min:0',
        'selectedVarieties' => 'required|array|min:1',
        'bundles_received' => 'required|integer|min:1',
        'national_id' => 'nullable|string|max:20',
        'phone_number' => 'nullable|max:255',
        'crop' => 'required|string|in:Sweet potato,Potato,Cassava',
        'type_of_actor' => 'nullable|string',
        'type_of_plot' => 'nullable|string',
        'season_type' => 'nullable|string',
        'group_name' => 'nullable|string',
    ];

    protected $validationAttributes = [
        'selectedVarieties' => 'varieties received',
    ];

    public $varieties = [];
    public array $selectedVarieties = [];


    public function save()
    {





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
            SeedBeneficiary::where('sd_id', $this->uniqueId)->update([
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
                'variety_received' => implode(',', $this->selectedVarieties),
                'bundles_received' => $this->bundles_received,
                'phone_number' => $this->phone_number,
                'national_id' => $this->national_id,
                'type_of_actor' => $this->type_of_actor,
                'type_of_plot' => $this->type_of_plot,
                'season_type' => $this->season_type,
                'group_name' => $this->group_name,
                'year' => Carbon::parse($this->date)->year,
                'crop' => $this->crop,
            ]);


            $trigger = match ($this->crop) {
                'Sweet potato' => 'ofsp',
                'Potato' => 'potato',
                'Cassava' => 'cassava',
            };

            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="/' . $this->routePrefix . '/forms/rtc_market/seed-distribution-register/view/' . $this->uniqueId . '/' . $this->crop .'#' . $trigger . '">View Submission here</a>',
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



    public function mount($id, $uuid)
    {
        $farmer = SeedBeneficiary::where('uuid', $uuid)->where('id', $id)->firstOrFail();

        $this->openSubmission = true;
        $this->routePrefix = Route::current()->getPrefix();
        $this->varieties = [];

        $this->editData($farmer);
    }

    public function editData($farmer)
    {


        // 2. Prepare the selected varieties array
        // Assuming variety_received is stored as "1,2,3" or similar.
        // If it's already an array/JSON, you might just need $farmer->variety_received

        $this->fill([
            'district' => $farmer->district,
            'epa' => $farmer->epa,
            'section' => $farmer->section,
            'name_of_aedo' => $farmer->name_of_aedo,
            'aedo_phone_number' => $farmer->aedo_phone_number,
            'date' => $farmer->date,
            'name_of_recipient' => $farmer->name_of_recipient,
            'village' => $farmer->village,
            'sex' => $farmer->sex,
            'age' => $farmer->age,
            'marital_status' => $farmer->marital_status,
            'hh_head' => $farmer->hh_head,
            'household_size' => $farmer->household_size,
            'children_under_5' => $farmer->children_under_5,
            'variety_received' => $farmer->variety_received,
            'bundles_received' => $farmer->bundles_received,
            'phone_number' => $farmer->phone_number,
            'national_id' => $farmer->national_id,
            'type_of_actor' => $farmer->type_of_actor,
            'type_of_plot' => $farmer->type_of_plot,
            'season_type' => $farmer->season_type,
            'group_name' => $farmer->group_name,
            'uniqueId' => $farmer->sd_id,
            'crop' => $farmer->crop,
            'varieties' => $this->getVarieties($farmer->crop),

        ]);



        $var = $farmer->variety_received;
        $result = explode(",", $var);


        $this->selectedVarieties = $result;
    }


    public function getVarieties($crop)
    {


        $cropModel = Crop::where('name', $crop)->with('varieties')->first();

        if (!$cropModel) {
            return [];
        }


        return $cropModel->varieties->map(function ($variety) {
            return [


                'name' => $variety->name,
            ];
        })->toArray();
    }


    public function updatedCrop($crop)
    {
        $this->varieties = [];
        $this->varieties = $this->getVarieties($crop);
        $this->selectedVarieties = [];
        $this->variety_received = null;
    }





    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.other-forms.seed-beneficiaries.edit');
    }
}
