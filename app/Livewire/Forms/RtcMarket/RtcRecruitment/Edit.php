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

class Edit extends Component
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
    public $uniqueId;
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
                'name_of_representative' => 'nullable',
                'phone_number' => 'nullable',
                'type' => 'required',
                'group' => 'nullable',
                'approach' => 'required_if:group,Producer Organization (PO)',
                'sector' => 'nullable',
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


    public function mount($id, $uuid)
    {
        $farmer = Recruitment::where('uuid', $uuid)->where('id', $id)->firstOrFail();

        $this->openSubmission = true;
        $this->routePrefix = Route::current()->getPrefix();
        $this->editData($farmer);
    }

    public function editData($data)
    {

        $this->fill([
            'uniqueId' => $data->rc_id,
            'location_data' => [
                'district' => $data->district,
                'epa' => $data->epa,
                'enterprise' => $data->enterprise,
                'section' => $data->section,
            ],
            'date_of_recruitment' => $data->date_of_recruitment,
            'name_of_actor' => $data->name_of_actor,
            'name_of_representative' => $data->name_of_representative,
            'phone_number' => $data->phone_number,
            'type' => $data->type,
            'group' => $data->group,
            'approach' => $data->approach,
            'sector' => $data->sector,
            'category' => $data->category,
            'establishment_status' => $data->establishment_status,
            'is_registered' => $data->is_registered,
            'registration_details' => [
                'registration_body' => $data->registration_body,
                'registration_number' => $data->registration_number,
                'registration_date' => $data->registration_date ? Carbon::parse($data->registration_date)->format('Y-m-d') : null,
            ],
            'number_of_employees' => [
                'formal' => [
                    'female_18_35' => $data->emp_formal_female_18_35,
                    'female_35_plus' => $data->emp_formal_female_35_plus,
                    'male_18_35' => $data->emp_formal_male_18_35,
                    'male_35_plus' => $data->emp_formal_male_35_plus,
                ],
                'informal' => [
                    'female_18_35' => $data->emp_informal_female_18_35,
                    'female_35_plus' => $data->emp_informal_female_35_plus,
                    'male_18_35' => $data->emp_informal_male_18_35,
                    'male_35_plus' => $data->emp_informal_male_35_plus,
                ],
            ],
            'number_of_members' => [
                'female_18_35' => $data->mem_female_18_35,
                'female_35_plus' => $data->mem_female_35_plus,
                'male_18_35' => $data->mem_male_18_35,
                'male_35_plus' => $data->mem_male_35_plus,
            ],
            'area_under_cultivation' => $data->area_under_cultivation,
            'is_registered_seed_producer' => $data->is_registered_seed_producer,
            'uses_certified_seed' => $data->uses_certified_seed,
        ]);
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









        try {
            $user = Auth::user();
            DB::beginTransaction();
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
                'registration_body' => $this->is_registered == true ? $this->registration_details['registration_body'] : null,
                'registration_number' => $this->is_registered == true ? $this->registration_details['registration_number'] : null,
                'registration_date' => $this->is_registered == true ? $this->registration_details['registration_date'] : null,
                'area_under_cultivation' => $this->area_under_cultivation ?? 0,
                'emp_formal_female_18_35' => $this->number_of_employees['formal']['female_18_35'] ?? 0,
                'emp_formal_male_18_35' => $this->number_of_employees['formal']['male_18_35'] ?? 0,
                'emp_formal_male_35_plus' => $this->number_of_employees['formal']['male_35_plus'] ?? 0,
                'emp_formal_female_35_plus' => $this->number_of_employees['formal']['female_35_plus'] ?? 0,
                'emp_informal_female_18_35' => $this->number_of_employees['informal']['female_18_35'] ?? 0,
                'emp_informal_male_18_35' => $this->number_of_employees['informal']['male_18_35'] ?? 0,
                'emp_informal_male_35_plus' => $this->number_of_employees['informal']['male_35_plus'] ?? 0,
                'emp_informal_female_35_plus' => $this->number_of_employees['informal']['female_35_plus'] ?? 0,
                'mem_female_18_35' => $this->number_of_members['female_18_35'] ??0,
                'mem_male_18_35' => $this->number_of_members['male_18_35'] ??0,
                'mem_male_35_plus' => $this->number_of_members['male_35_plus'] ??0,
                'mem_female_35_plus' => $this->number_of_members['female_35_plus'] ??0,
                'uses_certified_seed' => $this->uses_certified_seed,

            ];

            $recruit = Recruitment::where('rc_id', $this->uniqueId)->first();

            $recruit->update($modelData);

            if ($this->is_registered_seed_producer == 1) {
                foreach ($this->registrations as $reg) {

                    RecruitSeedRegistration::create([
                        'recruitment_id' => $recruit->id, // Replace with real parent ID
                        'variety' => $reg['variety'],
                        'reg_date' => $reg['reg_date'],
                        'reg_no' => $reg['reg_no'],
                    ]);
                }
            }

            DB::commit();
             $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/rtc-actor-recruitment-form/view/' . $recruit->rc_id . '">View Submission here</a>',
            ]);
        } catch (Throwable $th) {
            DB::rollBack();
            $this->dispatch('show-alert', data: ['type' => 'error', 'message' => 'Something went wrong!']);
            Log::error($th->getMessage());
        }
    }





    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.rtc-recruitment.edit');
    }
}
