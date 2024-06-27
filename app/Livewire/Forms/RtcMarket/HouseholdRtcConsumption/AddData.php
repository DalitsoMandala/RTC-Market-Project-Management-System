<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use App\Exceptions\UserErrorException;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\HouseholdRtcConsumption;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Notifications\AggregateDataAddedNotification;
use App\Notifications\ManualDataAddedNotification;
use Carbon\Carbon;
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

    public $submissionPeriodId;
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

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;
    public $form_name = 'HOUSEHOLD CONSUMPTION FORM';
    public $openSubmission = false;

    public $indicators;

    public $selectedIndicator;

    public $organisation_id;

    public $loadAggregate = [];

    public $aggregateData = [];
    public $checkifAggregate = false;

    public $showReport = false;
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

    public function updated($property, $value)
    {

    }

    public function updatedSelectedIndicator($value)
    {

        $period = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $this->selectedForm)
            ->where('indicator_id', $this->selectedIndicator)->pluck('month_range_period_id');
        $this->months = ReportingPeriodMonth::get();
        $this->months = $this->months->whereIn('id', $period);

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

            $data = [];
            $now = Carbon::now();
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
                $input['created_at'] = $now;
                $input['updated_at'] = $now;
                $data[] = $input;
                //   HouseholdRtcConsumption::create($input);

            }

            $currentUser = Auth::user();

            if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                try {

                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'user_id' => $currentUser->id,
                        'status' => 'approved',
                        'data' => json_encode($data),
                        'batch_type' => 'manual',
                        'is_complete' => 1,
                        'period_id' => $this->submissionPeriodId,
                        'table_name' => 'household_rtc_consumption',

                    ]);

                    HouseholdRtcConsumption::insert($data);

                    $this->reset('epa', 'section', 'district', 'enterprise');
                    $this->resetErrorBag();
                    $this->readdInputs();

                    $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                    $currentUser->notify(new ManualDataAddedNotification($uuid, $link));
                    $this->dispatch('notify');
                    session()->flash('success', 'Successfully submitted! <a href="/cip/forms/rtc_market/household-consumption-form/view">View Submission here</a>');
                    //    $this->redirect(route('rtc-market-hrc', ['project' => 'rtc_market']));

                } catch (UserErrorException $e) {
                    // Log the actual error for debugging purposes
                    \Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            } else if ($currentUser->hasAnyRole('external')) {

                try {
                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'period_id' => $this->submissionPeriodId,
                        'user_id' => $currentUser->id,
                        'data' => json_encode($data),
                        'batch_type' => 'manual',
                        'status' => 'approved',
                        'is_complete' => 1,
                        'table_name' => 'household_rtc_consumption',

                    ]);

                    HouseholdRtcConsumption::insert($data);

                    $this->reset('epa', 'section', 'district', 'enterprise');
                    $this->resetErrorBag();
                    $this->readdInputs();

                    $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                    $currentUser->notify(new ManualDataAddedNotification($uuid, $link));
                    $this->dispatch('notify');
                    session()->flash('success', 'Successfully submitted! <a href="/external/forms/rtc_market/household-consumption-form/view">View Submission here</a>');

                } catch (UserErrorException $e) {
                    // Log the actual error for debugging purposes
                    \Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            }

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

    public function savePack()
    {

        try {
            $uuid = Uuid::uuid4()->toString();

            $userId = auth()->user()->id;

            $currentUser = Auth::user();

            if ($currentUser->hasAnyRole('internal') && $currentUser->hasAnyRole('organiser')) {

                try {
                    $checkSubmissions = Submission::where('period_id', $this->submissionPeriodId)
                        ->where('batch_type', 'aggregate')->where('user_id', $userId)->first();

                    if ($checkSubmissions) {
                        session()->flash('error', 'You have already submitted your data for this indicator.');
                        $this->dispatch('to-top');
                    } else {
                        Submission::create([
                            'batch_no' => $uuid,
                            'form_id' => $this->selectedForm,
                            'user_id' => $currentUser->id,
                            'status' => 'approved',
                            'data' => json_encode($this->aggregateData),
                            'batch_type' => 'aggregate',
                            'is_complete' => 1,
                            'period_id' => $this->submissionPeriodId,
                            'table_name' => 'household_rtc_consumption',
                        ]);

                        $this->aggregateData = [];

                        $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                        $currentUser->notify(new AggregateDataAddedNotification($uuid, $link));
                        $this->dispatch('notify');
                        $this->reset('selectedIndicator', 'selectedMonth', 'selectedFinancialYear');
                        session()->flash('success', 'Successfully submitted! <a href="' . route('rtc-market-hrc') . '#manual-submission">View Submission here</a>');

                    }
                } catch (\Exception $e) {
                    // Log the actual error for debugging purposes
                    \Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            } else if ($currentUser->hasAnyRole('external')) {

                try {

                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'user_id' => $currentUser->id,
                        'data' => json_encode($this->aggregateData),
                        'batch_type' => 'aggregate',
                        'is_complete' => 1,
                        'period_id' => $this->submissionPeriodId,
                        'table_name' => 'household_rtc_consumption',
                    ]);

                    $this->aggregateData = [];

                    session()->flash('success', 'Successfully submitted! <a href="../../../submissions">View Submission here</a>');

                    $link = 'forms/rtc-market/household-consumption-form/' . $uuid . '/view';
                    $currentUser->notify(new AggregateDataAddedNotification($uuid, $link));
                    $this->dispatch('notify');
                    $this->reset('selectedIndicator', 'selectedMonth', 'selectedFinancialYear');
                    $this->dispatch('to-top');

                    $this->alert('success', 'Successfully submitted!', [
                        'toast' => true,
                        'position' => 'center',
                    ]);

                } catch (\Exception $e) {
                    // Log the actual error for debugging purposes
                    \Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }
            }

        } catch (\Throwable $th) {

            session()->flash('error', 'Something went wrong!');

        }

    }

    #[On('added-aggregates')]
    public function addData($data)
    {

        $this->aggregateData = $data;
        $this->savePack();
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

            if ($submissionPeriod) {

                $this->openSubmission = true;
                $user = Auth::user();
                $organisation = $user->organisation;

                $getSubmissionType = ResponsiblePerson::where('indicator_id', $this->selectedIndicator)->where('organisation_id', $organisation->id)->first();
                if ($getSubmissionType) {
                    $this->showReport = $getSubmissionType->type_of_submission == 'normal' ? false : true;
                }
            } else {
                $this->openSubmission = false;
            }
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
                        'sex' => 'MALE',
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
