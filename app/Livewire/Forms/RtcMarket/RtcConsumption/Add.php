<?php

namespace App\Livewire\Forms\RtcMarket\RtcConsumption;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\RtcConsumption;
use App\Traits\ManualDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use App\Models\SchoolRtcConsumption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Notifications\ManualDataAddedNotification;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $date;
    public $crop = [];
    public $male_count;
    public $female_count;
    public $number_of_households;
    public $total = 0;
    public $location_data = [
        'entity_name' => null,
        'district' => null,
        'epa' => null,
        'section' => null,
        'entity_type' => null
    ];
    public $entity_type;

    public $form_name = 'RTC CONSUMPTION FORM';

    public function rules()
    {

        return [
            'location_data.entity_name' => 'required',
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.section' => 'required',
            'location_data.entity_type' => 'required',
            'date' => 'required|date',
            'crop' => 'required',
            'male_count' => 'required|numeric',
            'female_count' => 'required|numeric',
            'number_of_households' => 'required_if:location_data.entity_type,Nutrition intervention group|numeric',
            'total' => 'required|numeric',
        ];
    }

    public function validationAttributes()
    {

        return [
            'location_data.school_name' => 'school name',
            'location_data.district' => 'district',
            'location_data.epa' => 'epa',
            'location_data.section' => 'section',
            'location_data.entity_name' => 'entity name',
        ];
    }

    protected function messages()
    {
        return [
            'number_of_households.required_if' => 'The :attribute is required when entity type is Nutrition intervention group.',

        ];
    }
    public function save()
    {

        try {
            $this->validate();
        } catch (\Throwable $e) {
            $this->dispatch('show-alert', data: [
                'type' => 'error', // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);

            throw $e;
        }

        $user = Auth::user();
        $cropCollection = collect($this->crop);

        // Prepare data for the model
        $modelData = [
            'date' => $this->date,
            'epa' => $this->location_data['epa'],
            'district' => $this->location_data['district'],
            'section' => $this->location_data['section'],
            'entity_name' => $this->location_data['entity_name'],
            'male_count' => $this->male_count,
            'female_count' => $this->female_count,
            'total' => $this->total,
            'crop_cassava' => $cropCollection->contains('cassava') ? 1 : 0,
            'crop_potato' => $cropCollection->contains('potato') ? 1 : 0,
            'crop_sweet_potato' => $cropCollection->contains('sweet_potato') ? 1 : 0,
            'entity_type' => $this->location_data['entity_type'],
            'number_of_households' => $this->location_data['entity_type'] === 'Nutrition intervention group' ? $this->number_of_households : 0,

        ];

        // Prepare submission data
        $submissionData = [
            'form_id' => $this->selectedForm,
            'user_id' => $user->id,
            'batch_type' => 'manual',
            'is_complete' => 1,
            'period_id' => $this->submissionPeriodId,
        ];

        // Call the trait method to save the submission
        $this->saveManualSubmission(
            RtcConsumption::class, // Model class
            $modelData, // Data for the model
            $submissionData, // Data for the Submission table
            'rtc_consumption_data', // Table name for submission
            $this->routePrefix // Route prefix for the success message
        );
    }


    public function saveManualSubmission($modelClass, $data, $submissionData, $tableName, $routePrefix)
    {


        try {
            DB::beginTransaction();
            $uuid = Uuid::uuid4()->toString();
            $user = User::find(auth()->user()->id); // Get the authenticated user using Auth::user();
            $data['uuid'] = $uuid;
            $data['user_id'] = $user->id;

            // Add role-specific logic
            if ($user->hasAnyRole(['manager', 'admin'])) {
                $data['status'] = 'approved';
            } else {
                $data['status'] = 'pending'; // Default status for non-manager/admin users
            }

            // Create the submission record
            $submissionData['batch_no'] = $uuid;
            $submissionData['table_name'] = $tableName;

            $data['submission_period_id'] = $this->submissionPeriodId;
            $data['period_month_id'] = $this->selectedMonth;
            $data['organisation_id'] = $user->organisation->id;
            $data['financial_year_id'] = $this->selectedFinancialYear;

            Submission::create($submissionData);

            // Create the model record
            $model = new $modelClass;
            $latest = $model->create($data);



            $project = Project::where('is_active', true)->first();
            $project_name = strtolower(str_replace(' ', '_', $project->name));
            $form = Form::find($this->selectedForm);
            $formName = strtolower(str_replace(' ', '-', $form->name));
            $this->clearErrorBag();
            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/' . $formName . '/view">View Submission here</a>',
            ]);
            DB::commit();
        } catch (\Exception $th) {
            # code...
            DB::rollBack();

            $this->dispatch('show-alert', data: [
                'type' => 'error',
                'message' => 'Something went wrong!'
            ]);

            Log::error($th->getMessage());
        }
    }


    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-consumption.add');
    }
}
