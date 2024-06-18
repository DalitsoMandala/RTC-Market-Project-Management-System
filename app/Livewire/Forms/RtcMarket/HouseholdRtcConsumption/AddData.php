<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\ManualDataAddedNotification;
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
    public $form_name = 'HOUSEHOLD CONSUMPTION FORM';
    public $openSubmission = false;
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

            $period = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $this->selectedForm)
                ->where('financial_year_id', $this->selectedFinancialYear)->where('month_range_period_id', $this->selectedMonth)->first();
            if (!$period) {
                throw new \Exception("Sorry you can not submit your form right now!"); // expired or closed

            }

            $data = [];
            foreach ($this->inputs as $index => $input) {

                $input['location_data'] = json_encode(['enterprise' => $this->enterprise,
                    'district' => $this->district,
                    'epa' => $this->epa,
                    'section' => $this->section]);

                // for main food lunch,dinner,breakfast
                foreach ($input['main_food'] as $mainfood) {
                    $input['main_food_data'][] = [
                        'name' => $mainfood,
                    ];

                }

                unset($input['main_food']);

                $input['main_food_data'] = json_encode($input['main_food_data']);
                $input['uuid'] = $uuid;
                $input['user_id'] = $userId;
                $data[] = $input;
                HouseholdRtcConsumption::create($input);

            }

            $currentUser = Auth::user();

            if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                try {
                    # code...

                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'user_id' => $currentUser->id,
                        'status' => 'approved',
                        'data' => json_encode($data),
                        'batch_type' => 'manual',
                        'is_complete' => 1,
                        'period_id' => $period->id,
                    ]);

                    $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                    $currentUser->notify(new ManualDataAddedNotification($uuid, $link));
                    $this->dispatch('notify');

                } catch (\Exception $e) {
                    # code...
                    dd($e);
                    session()->flash('error', 'Something went wrong!');

                }

            } else if ($currentUser->hasAnyRole('external')) {

                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'period_id' => $period->id,
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

            $this->readdInputs();

            $this->alert('success', 'Successfully submitted!', [
                'toast' => false,
                'position' => 'center',
            ]);

            session()->flash('success', 'Successfully submitted! <a href="../../../submissions">View Submission here</a>');
            $this->dispatch('to-top');
        } catch (\Throwable $th) {
            session()->flash('error', 'Something went wrong!');

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

    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.add-data');
    }
}
