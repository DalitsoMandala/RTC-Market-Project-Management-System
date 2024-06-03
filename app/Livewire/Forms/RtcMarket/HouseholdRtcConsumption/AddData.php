<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\HrcLocation;
use App\Models\HrcMainFood;
use App\Models\Submission;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;

class AddData extends Component
{
    use LivewireAlert;

    public $variable;
    public $rowId;
    public $inputs = [];
    #[Validate('required')]
    public $epa;
    #[Validate('required')]
    public $section;
    #[Validate('required')]
    public $district;

    #[Validate('required')]
    public $enterprise;

    public $period;
    protected function rules()
    {
        $validationRules = [];

        foreach ($this->inputs as $index => $input) {

            $validationRules['inputs.' . $index . '.date_of_assessment'] = 'required|date';
            $validationRules['inputs.' . $index . '.actor_type'] = 'required';
            $validationRules['inputs.' . $index . '.rtc_group_platform'] = 'required';
            $validationRules['inputs.' . $index . '.producer_organisation'] = 'required';
            $validationRules['inputs.' . $index . '.actor_name'] = 'required';
            $validationRules['inputs.' . $index . '.age_group'] = 'required';
            $validationRules['inputs.' . $index . '.sex'] = 'required';
            $validationRules['inputs.' . $index . '.phone_number'] = 'required';
            $validationRules['inputs.' . $index . '.household_size'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.under_5_in_household'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.rtc_consumers'] = 'required|numeric|lte:inputs.' . $index . '.household_size';
            $validationRules['inputs.' . $index . '.rtc_consumers_potato'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.rtc_consumers_sw_potato'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.rtc_consumers_cassava'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.rtc_consumption_frequency'] = 'required|numeric';
            $validationRules['inputs.' . $index . '.main_food'] = 'required';
        }

        return $validationRules;
    }

    protected function messages()
    {
        $validationRules = [];

        foreach ($this->inputs as $index => $input) {

            $validationRules['inputs.' . $index . '.date_of_assessment.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.date_of_assessment.date'] = 'This field should be a date';

            $validationRules['inputs.' . $index . '.actor_type.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_group_platform.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.producer_organisation.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.actor_name.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.age_group.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.sex.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.phone_number.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.household_size.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.household_size.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.under_5_in_household.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.under_5_in_household.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.rtc_consumers.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_consumers.numeric'] = 'This field should be a number';
            $validationRules['inputs.' . $index . '.rtc_consumers.lte'] = 'This field should be less than or equal to the household size';

            $validationRules['inputs.' . $index . '.rtc_consumers_potato.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_consumers_potato.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.rtc_consumers_sw_potato.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_consumers_sw_potato.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.rtc_consumers_cassava.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_consumers_cassava.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.rtc_consumption_frequency.required'] = 'This field is required';
            $validationRules['inputs.' . $index . '.rtc_consumption_frequency.numeric'] = 'This field should be a number';

            $validationRules['inputs.' . $index . '.main_food'] = 'This field is required';
        }

        return $validationRules;
    }

    public function addInput()
    {
        $validated = $this->validate();

        $this->inputs->push([
            'date_of_assessment' => null,
            'actor_type' => null,
            'rtc_group_platform' => null,
            'producer_organisation' => null,
            'actor_name' => null,
            'age_group' => null,
            'sex' => null,
            'phone_number' => null,
            'household_size' => null,
            'under_5_in_household' => null,
            'rtc_consumers' => null,
            'rtc_consumers_potato' => null,
            'rtc_consumers_sw_potato' => null,
            'rtc_consumers_cassava' => null,
            'rtc_consumption_frequency' => null,
            'main_food' => [],
        ],
        );

    }

    public function updated($value)
    {
        // dd($value);
    }
    public function removeInput($index)
    {
        unset($this->inputs[$index]);
    }

    public function save()
    {

        $this->validate();

        try {
            $uuid = Uuid::uuid4()->toString();

            $userId = auth()->user()->id;
            $location = HrcLocation::create([
                'enterprise' => $this->enterprise,
                'district' => $this->district,
                'epa' => $this->epa,
                'section' => $this->section,
            ]);
            $data = [];
            foreach ($this->inputs as $index => $input) {
                $input = (object) $input;

                $data[$index] = [
                    'location_data' => [
                        'enterprise' => $this->enterprise,
                        'district' => $this->district,
                        'epa' => $this->epa,
                        'section' => $this->section,
                    ],
                    'date_of_assessment' => $input->date_of_assessment,
                    'actor_type' => $input->actor_type,
                    'rtc_group_platform' => $input->rtc_group_platform,
                    'producer_organisation' => $input->producer_organisation,
                    'actor_name' => $input->actor_name,
                    'age_group' => $input->age_group,
                    'sex' => $input->sex,
                    'phone_number' => $input->phone_number,
                    'household_size' => $input->household_size,
                    'under_5_in_household' => $input->under_5_in_household,
                    'rtc_consumers' => $input->rtc_consumers,
                    'rtc_consumers_potato' => $input->rtc_consumers_potato,
                    'rtc_consumers_sw_potato' => $input->rtc_consumers_sw_potato,
                    'rtc_consumers_cassava' => $input->rtc_consumers_cassava,
                    'rtc_consumption_frequency' => $input->rtc_consumption_frequency,
                    'user_id' => $userId,
                    'uuid' => $uuid,
                    'main_food_data' => [],
                ];

                $hrc = HouseholdRtcConsumption::create([
                    'location_id' => $location->id,
                    'date_of_assessment' => $input->date_of_assessment,
                    'actor_type' => $input->actor_type,
                    'rtc_group_platform' => $input->rtc_group_platform,
                    'producer_organisation' => $input->producer_organisation,
                    'actor_name' => $input->actor_name,
                    'age_group' => $input->age_group,
                    'sex' => $input->sex,
                    'phone_number' => $input->phone_number,
                    'household_size' => $input->household_size,
                    'under_5_in_household' => $input->under_5_in_household,
                    'rtc_consumers' => $input->rtc_consumers,
                    'rtc_consumers_potato' => $input->rtc_consumers_potato,
                    'rtc_consumers_sw_potato' => $input->rtc_consumers_sw_potato,
                    'rtc_consumers_cassava' => $input->rtc_consumers_cassava,
                    'rtc_consumption_frequency' => $input->rtc_consumption_frequency,
                    'user_id' => $userId,
                    'uuid' => $uuid,
                ]);

                // for main food lunch,dinner,breakfast
                foreach ($input->main_food as $mainfood) {
                    $data[$index]['main_food_data'][] = [
                        'name' => $mainfood,
                    ];
                    HrcMainFood::create([
                        'name' => $mainfood,
                        'hrc_id' => $hrc->id,
                    ]);

                }

            }
            $currentUser = Auth::user();
            $form = Form::where('name', 'HOUSEHOLD CONSUMPTION FORM')->first();

            if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {
                $submission = Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $form->id,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    'data' => json_encode($data),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                ]);

            } else if ($currentUser->hasAnyRole('external')) {

                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $form->id,
                    'period_id' => $this->period,
                    'user_id' => $currentUser->id,
                    'data' => json_encode($data),
                    'batch_type' => 'manual',
                    'status' => 'approved',
                    'is_complete' => 1,
                ]);

            }

            $this->reset('epa',
                'section',
                'district',
                'enterprise');

            $this->resetErrorBag();

            $this->dispatch('refresh-inputs');

            $this->alert('success', 'successfully submitted!');
        } catch (\Throwable $th) {
            dd($th);
            $this->alert('error', 'something went wrong!', [
                'toast' => false,
            ]);

        }

    }

    #[On('refresh-inputs')]
    public function readdInputs()
    {

        $this->fill(
            [
                'inputs' =>
                collect([
                    [
                        'date_of_assessment' => null,
                        'actor_type' => null,
                        'rtc_group_platform' => null,
                        'producer_organisation' => null,
                        'actor_name' => null,
                        'age_group' => null,
                        'sex' => null,
                        'phone_number' => null,
                        'household_size' => null,
                        'under_5_in_household' => null,
                        'rtc_consumers' => null,
                        'rtc_consumers_potato' => null,
                        'rtc_consumers_sw_potato' => null,
                        'rtc_consumers_cassava' => null,
                        'rtc_consumption_frequency' => null,
                        'main_food' => [],
                    ],

                ])
            ]);

    }
    public function mount()
    {

        $form = Form::where('name', 'HOUSEHOLD CONSUMPTION FORM')->first();
        $period = $form->submissionPeriods->where('is_open', true)->first();
        if ($period) {
            $this->period = $period->id;
        } else {
            $this->period = null;
        }

        //has one
        $this->fill(
            [
                'inputs' =>
                collect([
                    [
                        'date_of_assessment' => null,
                        'actor_type' => null,
                        'rtc_group_platform' => null,
                        'producer_organisation' => null,
                        'actor_name' => null,
                        'age_group' => null,
                        'sex' => null,
                        'phone_number' => null,
                        'household_size' => null,
                        'under_5_in_household' => null,
                        'rtc_consumers' => null,
                        'rtc_consumers_potato' => null,
                        'rtc_consumers_sw_potato' => null,
                        'rtc_consumers_cassava' => null,
                        'rtc_consumption_frequency' => null,
                        'main_food' => [],
                    ],

                ])
            ]);

    }

    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.add-data');
    }
}
