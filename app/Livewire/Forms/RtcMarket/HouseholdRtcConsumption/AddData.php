<?php

namespace App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption;

use Throwable;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Helpers\LogError;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\LocationHrc;
use App\Models\MainFoodHrc;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use App\Models\HouseholdRtcConsumption;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;
use App\Notifications\AggregateDataAddedNotification;

class AddData extends Component
{
    use LivewireAlert;

    public $variable;
    public $rowId;

    public $submissionPeriodId;
    public $inputs = [];

    public $epa;

    public $section;

    public $district;


    public $enterprise = 'Cassava';

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
    public $routePrefix;
    public $targetSet = false;
    public $targetIds = [];
    protected function rules()
    {
        return [
            'epa' => 'required',
            'section' => 'required',
            'district' => 'required',
            'enterprise' => 'required',
            'inputs.*.date_of_assessment' => 'required|date',
            'inputs.*.actor_type' => 'required',
            'inputs.*.rtc_group_platform' => 'required',
            'inputs.*.producer_organisation' => 'required',
            'inputs.*.actor_name' => 'required',
            'inputs.*.age_group' => 'required',
            'inputs.*.sex' => 'required',
            'inputs.*.phone_number' => 'required',
            'inputs.*.household_size' => 'required|numeric|min:0',
            'inputs.*.under_5_in_household' => 'required|numeric|min:0|lte:inputs.*.household_size',
            'inputs.*.rtc_consumers' => 'required|numeric|min:0|lte:inputs.*.household_size',
            'inputs.*.rtc_consumers_potato' => 'required|numeric|min:0|lte:inputs.*.rtc_consumers',
            'inputs.*.rtc_consumers_sw_potato' => 'required|numeric|min:0|lte:inputs.*.rtc_consumers',
            'inputs.*.rtc_consumers_cassava' => 'required|numeric|min:0|lte:inputs.*.rtc_consumers',
            'inputs.*.rtc_consumption_frequency' => 'required|numeric|min:0',
            'inputs.*.main_food' => 'required',
        ];
    }
    protected function validationAttributes()
    {
        return [
            'epa' => 'epa',
            'section' => 'section',
            'district' => 'district',
            'enterprise' => 'enterprise',
            'inputs.*.date_of_assessment' => 'date of assessment',
            'inputs.*.actor_type' => 'actor type',
            'inputs.*.rtc_group_platform' => 'RTC group platform',
            'inputs.*.producer_organisation' => 'producer organisation',
            'inputs.*.actor_name' => 'actor name',
            'inputs.*.age_group' => 'age group',
            'inputs.*.sex' => 'sex',
            'inputs.*.phone_number' => 'phone number',
            'inputs.*.household_size' => 'household size',
            'inputs.*.under_5_in_household' => 'under 5 in household',
            'inputs.*.rtc_consumers' => 'RTC consumers',
            'inputs.*.rtc_consumers_potato' => 'RTC consumers (potato)',
            'inputs.*.rtc_consumers_sw_potato' => 'RTC consumers (sweet potato)',
            'inputs.*.rtc_consumers_cassava' => 'RTC consumers (cassava)',
            'inputs.*.rtc_consumption_frequency' => 'RTC consumption frequency',
            'inputs.*.main_food' => 'main food',
        ];
    }

    public function addInput()
    {
        $validated = $this->validate();

        $this->inputs->push(
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
        );

    }
    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
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

        try {
            $this->validate($this->rules(), [], $this->validationAttributes());
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {
            $uuid = Uuid::uuid4()->toString();

            $userId = auth()->user()->id;

            $data = [];
            $now = Carbon::now();
            foreach ($this->inputs as $index => $input) {


                $input['uuid'] = $uuid;
                $input['user_id'] = $userId;
                $input['created_at'] = $now;
                $input['updated_at'] = $now;
                $input['epa'] = $this->epa;
                $input['section'] = $this->section;
                $input['district'] = $this->district;
                $input['enterprise'] = $this->enterprise;
                $data[] = $input;


            }


            $currentUser = Auth::user();



            try {

                $period = SubmissionPeriod::where('is_open', true)->where('is_expired', false)->where('form_id', $this->selectedForm)
                    ->where('financial_year_id', $this->selectedFinancialYear)->where('month_range_period_id', $this->selectedMonth)->first();
                if (!$period) {
                    throw new UserErrorException("Sorry you can not submit your form right now!"); // expired or closed

                }

                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    //     'data' => json_encode($data),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => 'household_rtc_consumption',

                ]);

                foreach ($data as $dt) {

                    $dt['submission_period_id'] = $this->submissionPeriodId;
                    $dt['period_month_id'] = $this->selectedMonth;
                    $dt['organisation_id'] = Auth::user()->organisation->id;
                    $dt['financial_year_id'] = $this->selectedFinancialYear;

                    $dt['status'] = 'approved';
                    $mainFood = $dt['main_food'];
                    unset($dt['main_food']);

                    $hrc = HouseholdRtcConsumption::create($dt);


                    foreach ($mainFood as $food) {
                        $hrc->mainFoods()->create([
                            'name' => $food
                        ]);
                    }






                }



                $this->reset('epa', 'section', 'district', 'enterprise');
                $this->resetErrorBag();
                $this->readdInputs();


                session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/household-consumption-form/view">View Submission here</a>');
                return redirect()->to(url()->previous());

            } catch (UserErrorException $e) {

                // Log the actual error for debugging purposes
                Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());
            }



        } catch (Throwable $th) {

            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
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

                    ]),
            ]
        );

    }

    public function savePack()
    {

        try {
            $uuid = Uuid::uuid4()->toString();

            $userId = auth()->user()->id;

            $currentUser = Auth::user();
            $user = User::find($userId);
            if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {

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
                            //       'data' => json_encode($this->aggregateData),
                            'batch_type' => 'aggregate',
                            'is_complete' => 1,
                            'period_id' => $this->submissionPeriodId,
                            'table_name' => 'household_rtc_consumption',
                        ]);




                        session()->flash('success', 'Successfully submitted! <a href="' . route('cip-internal-submissions') . '#manual-submission">View Submission here</a>');

                    }
                } catch (\Exception $e) {
                    // Log the actual error for debugging purposes
                    Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            } else if ($user->hasAnyRole('external') || $user->hasAnyRole('staff')) {

                try {

                    Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'user_id' => $currentUser->id,
                        //  'data' => json_encode($this->aggregateData),
                        'batch_type' => 'aggregate',
                        'is_complete' => 1,
                        'period_id' => $this->submissionPeriodId,
                        'table_name' => 'household_rtc_consumption',
                    ]);


                    session()->flash('success', 'Successfully submitted! <a href="' . route('external-submissions') . '#manual-submission">View Submission here</a>');




                } catch (\Exception $e) {
                    // Log the actual error for debugging purposes
                    Log::channel('system_log')->error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }
            }

        } catch (Throwable $th) {
            Log::error($th->getMessage());

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

            $target = SubmissionTarget::where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->get();
            $user = User::find(auth()->user()->id);

            $checkOrganisationTargetTable = OrganisationTarget::where('organisation_id', $user->organisation->id)->whereIn('submission_target_id', $target->pluck('id'))->get();
            $this->targetIds = $target->pluck('id')->toArray();


            if ($submissionPeriod && $checkOrganisationTargetTable->count() > 0) {

                $this->openSubmission = true;
                $this->targetSet = true;
            } else {
                $this->openSubmission = false;
                $this->targetSet = false;
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
                            'sex' => 'Male',
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

                    ]),
            ]
        );

        $this->routePrefix = Route::current()->getPrefix();
    }
    public function render()
    {
        return view('livewire.forms.rtc-market.household-rtc-consumption.add-data');
    }
}
