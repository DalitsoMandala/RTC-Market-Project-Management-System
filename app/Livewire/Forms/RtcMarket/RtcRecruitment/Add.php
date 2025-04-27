<?php

namespace App\Livewire\Forms\RtcMarket\RtcRecruitment;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\Recruitment;
use Illuminate\Support\Arr;
use Livewire\Attributes\On;
use App\Models\ExchangeRate;
use App\Models\FinancialYear;
use App\Traits\ManualDataTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\RpmFarmerFollowUp;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use App\Models\RpmFarmerDomMarket;
use Illuminate\Support\Facades\DB;
use App\Helpers\ExchangeRateHelper;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use App\Models\RpmFarmerInterMarket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\FarmerSeedRegistration;
use App\Models\RpmFarmerConcAgreement;
use App\Models\HouseholdRtcConsumption;
use App\Models\RecruitSeedRegistration;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Validation\ValidationException;
use App\Notifications\ManualDataAddedNotification;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;
    public $form_name = 'RTC ACTOR RECRUITMENT FORM';

    public $location_data = [
        'enterprise' => 'Cassava',
        'district' => null,
        'epa' => null,
        'section' => null,
    ];
    public $date_of_recruitment;
    public $name_of_actor;
    public $name_of_representative;
    public $phone_number;
    public $type;
    public $approach; // For producer organizations only
    public $sector;
    public $number_of_members = [
        //   'total' => null,
        'female_18_35' => null,
        'female_35_plus' => null,
        'male_18_35' => null,
        'male_35_plus' => null,

    ]; // For producer organizations only
    public $group;
    public $establishment_status;
    public $is_registered = false;
    public $registration_details = [
        'registration_body' => null,
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $number_of_employees = [
        'formal' => [
            // 'total' => null,
            'female_18_35' => null,
            'female_35_plus' => null,
            'male_18_35' => null,
            'male_35_plus' => null,
        ],
        'informal' => [
            //  'total' => null,
            'female_18_35' => null,
            'female_35_plus' => null,
            'male_18_35' => null,
            'male_35_plus' => null,
        ],
    ];
    public $area_under_cultivation; // Stores area by variety (key-value pairs)

    public $is_registered_seed_producer = false;
    public $seed_service_unit_registration_details = [
        'registration_number' => null,
        'registration_date' => null,
    ];
    public $uses_certified_seed = false;
    public $category;
    public $registrations = [
        [


            'variety' => null,
            'reg_no' => null,
            'reg_date' => null
        ]
    ];
    public function rules()
    {

        $rules =
            [
                //first table
                'location_data.district' => 'required',
                'location_data.epa' => 'required',
                'location_data.enterprise' => 'required',
                'location_data.section' => 'required',
                'date_of_recruitment' => 'required|date',
                'name_of_actor' => 'required',
                'name_of_representative' => 'required',
                'phone_number' => 'required',
                'type' => 'required',
                'group' => 'required',
                'approach' => 'required_if:group,Producer Organization (PO)',
                'sector' => 'required',
                'number_of_members.*' => 'required',
                'category' => 'required_if:type,Farmers',
                'establishment_status' => 'required',
                'is_registered' => 'required',
                'registration_details.*' => 'required_if_accepted:is_registered',
                'number_of_employees.formal.female_18_35' => 'required|numeric',
                'number_of_employees.formal.female_35_plus' => 'required|numeric',
                'number_of_employees.formal.male_18_35' => 'required|numeric',
                'number_of_employees.formal.male_35_plus' => 'required|numeric',
                'number_of_employees.informal.female_18_35' => 'required|numeric',
                'number_of_employees.informal.female_35_plus' => 'required|numeric',
                'number_of_employees.informal.male_18_35' => 'required|numeric',
                'number_of_employees.informal.male_35_plus' => 'required|numeric',
                'area_under_cultivation' => 'required_if:type,Farmers',
                'is_registered_seed_producer' => 'required_if:type,Farmers',
                'registrations.*' => 'required_if_accepted:is_registered_seed_producer',
                'uses_certified_seed' => 'required_if:type,Farmers',

            ];



        return $rules;
    }

    public function validationAttributes()
    {
        return [
            'location_data.district' => 'district',
            'location_data.epa' => 'epa',
            'location_data.enterprise' => 'enterprise',
            'location_data.section' => 'section',

            'registration_details.registration_body' => 'registration body',
            'registration_details.registration_number' => 'registration number',
            'registration_details.registration_date' => 'registration date',

            'number_of_employees.formal.female_18_35' => 'Formal Employees Female 18-35',
            'number_of_employees.formal.female_35_plus' => 'Formal Employees Female 35+',
            'number_of_employees.formal.male_18_35' => 'Formal Employees Male 18-35',
            'number_of_employees.formal.male_35_plus' => 'Formal Employees Male 35+',
            'number_of_employees.informal.total' => 'Informal Employees Total',
            'number_of_employees.informal.female_18_35' => 'Informal Employees Female 18-35',
            'number_of_employees.informal.female_35_plus' => 'Informal Employees Female 35+',
            'number_of_employees.informal.male_18_35' => 'Informal Employees Male 18-35',
            'number_of_employees.informal.male_35_plus' => 'Informal Employees Male 35+',

            'is_registered' => 'formally registered entity',
            'is_registered_seed_producer' => 'registered seed producer',

            'seed_service_unit_registration_details.registration_number' => 'registration number',
            'seed_service_unit_registration_details.registration_date' => 'registration date',
            'registrations.*.variety' => 'variety',
            'registrations.*.reg_date' => 'registration date',
            'registrations.*.reg_no' => 'registration number',




            'number_of_members.female_18_35' => 'Female Members 18-35',
            'number_of_members.female_35_plus' => 'Female Members 35+',
            'number_of_members.male_18_35' => 'Male Members 18-35',
            'number_of_members.male_35_plus' => 'Male Members 35+',
            'area_under_cultivation' => 'area under cultivation (number of acres)',

        ];
    }

    public function addRegistration()
    {
        $this->registrations[] = ['variety' => '', 'reg_date' => '', 'reg_no' => ''];
    }

    public function removeRegistration($index)
    {
        unset($this->registrations[$index]);
        $this->registrations = array_values($this->registrations); // Reindex
    }


    public function resetValues($name) // be careful dont delete it will destroy alpinejs
    {

        $this->reset($name);
    }




    public function save()
    {


        try {

            $this->validate();
        } catch (Throwable $e) {

            $this->dispatch('show-alert', data: [
                'type' => 'error', // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }







        foreach ($this->number_of_members as $key => $value) {
            $this->number_of_members[$key] = $value ? $value : 0;
        }

        foreach ($this->number_of_employees as $type => $group) {
            foreach ($group as $key => $value) {
                // Check if the value is null and set it to 0
                if (is_null($value)) {
                    $this->number_of_employees[$type][$key] = 0;
                }
            }
        }


        $user = Auth::user();
        $modelData = [
            //   'location_data' => $this->location_data,
            'epa' => $this->location_data['epa'],
            'district' => $this->location_data['district'],
            'section' => $this->location_data['section'],
            'enterprise' => $this->location_data['enterprise'],
            'date_of_recruitment' => $this->date_of_recruitment,
            'name_of_actor' => $this->name_of_actor,
            'name_of_representative' => $this->name_of_representative,
            'phone_number' => $this->phone_number,
            'type' => $this->type,
            'group' => $this->group,
            'approach' => $this->approach, // For producer organizations only
            'sector' => $this->sector,
            'category' => $this->category,
            'establishment_status' => $this->establishment_status,
            'is_registered' => $this->is_registered,
            'registration_body' => $this->registration_details['registration_body'],
            'registration_number' => $this->registration_details['registration_number'],
            'registration_date' => $this->registration_details['registration_date'],
            'is_registered_seed_producer' => $this->is_registered_seed_producer,
            'registration_number_seed_producer' => $this->seed_service_unit_registration_details['registration_number'],
            'registration_date_seed_producer' => $this->seed_service_unit_registration_details['registration_date'],
            'area_under_cultivation' => $this->area_under_cultivation ?? 0,
            'emp_formal_female_18_35' => $this->number_of_employees['formal']['female_18_35'],
            'emp_formal_male_18_35' => $this->number_of_employees['formal']['male_18_35'],
            'emp_formal_male_35_plus' => $this->number_of_employees['formal']['male_35_plus'],
            'emp_formal_female_35_plus' => $this->number_of_employees['formal']['female_35_plus'],
            'emp_informal_female_18_35' => $this->number_of_employees['informal']['female_18_35'],
            'emp_informal_male_18_35' => $this->number_of_employees['informal']['male_18_35'],
            'emp_informal_male_35_plus' => $this->number_of_employees['informal']['male_35_plus'],
            'emp_informal_female_35_plus' => $this->number_of_employees['informal']['female_35_plus'],
            'mem_female_18_35' => $this->number_of_members['female_18_35'],
            'mem_male_18_35' => $this->number_of_members['male_18_35'],
            'mem_male_35_plus' => $this->number_of_members['male_35_plus'],
            'mem_female_35_plus' => $this->number_of_members['female_35_plus'],
            'uses_certified_seed' => $this->uses_certified_seed,

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
            Recruitment::class, // Model class
            $modelData, // Data for the model
            $submissionData, // Data for the Submission table
            'recruitments', // Table name for submission
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

            if ($this->is_registered_seed_producer == 1) {
                foreach ($this->registrations as $reg) {

                    RecruitSeedRegistration::create([
                        'recruitment_id' => $latest->id, // Replace with real parent ID
                        'variety' => $reg['variety'],
                        'reg_date' => $reg['reg_date'],
                        'reg_no' => $reg['reg_no'],
                    ]);
                }
            }

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
        return view('livewire.forms.rtc-market.rtc-recruitment.add');
    }
}
