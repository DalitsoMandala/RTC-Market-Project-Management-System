<?php

namespace App\Livewire\Forms\RtcMarket\SchoolConsumption;

use Throwable;
use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
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

    public $variable;
    public $rowId;


    public $location_data = [];

    public $date;
    public $crop = [];
    public $male_count;
    public $female_count;
    public $total = 0;


    public $submissionPeriodId;

    public $period;

    public $forms = [];

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;
    public $form_name = 'SCHOOL CONSUMPTION FORM';
    public $openSubmission = false;

    public $indicators;

    public $selectedIndicator;

    public $organisation_id;

    public $loadAggregate = [];

    public $aggregateData = [];
    public $checkifAggregate = false;

    public $showReport = false;

    public $validated = true;

    public $routePrefix;

    public $targetSet = false;

    public $targetIds = [];
    public function rules()
    {

        return [
            'location_data.school_name' => 'required',
            'location_data.district' => 'required',
            'location_data.epa' => 'required',
            'location_data.section' => 'required',
            'date' => 'required|date',
            'crop' => 'required',
            'male_count' => 'required|numeric',
            'female_count' => 'required|numeric',
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
        ];
    }

    public function save()
    {

        try {
            $this->validate();
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }






        try {
            $uuid = Uuid::uuid4()->toString();

            $userId = auth()->user()->id;
            $currentUser = Auth::user();
            $data = [];
            $now = Carbon::now();

            $uuid = Uuid::uuid4()->toString();
            $user = User::find($userId);
            $latest = '';
            $cropCollection = collect($this->crop);
            if (($user->hasAnyRole('internal') && $user->hasAnyRole('manager')) || $user->hasAnyRole('admin')) {

                $data = [
                    'date' => $this->date,
                    'epa' => $this->location_data['epa'],
                    'district' => $this->location_data['district'],
                    'section' => $this->location_data['section'],
                    // 'enterprise' => $this->location_data['enterprise'],
                    'school_name' => $this->location_data['school_name'],
                    'male_count' => $this->male_count,
                    'female_count' => $this->female_count,
                    'total' => $this->total,
                    // 'crop' => $this->crop,
                    'crop_cassava' => $cropCollection->contains('cassava') ? 1 : 0,
                    'crop_potato' => $cropCollection->contains('potato') ? 1 : 0,
                    'crop_sweet_potato' => $cropCollection->contains('sweet_potato') ? 1 : 0,
                    'uuid' => $uuid,
                    'user_id' => auth()->user()->id,
                    'submission_period_id' => $this->submissionPeriodId,
                    'period_month_id' => $this->selectedMonth,
                    'organisation_id' => Auth::user()->organisation->id,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'status' => 'approved'
                ];
                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    //   'data' => json_encode($data),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => 'school_rtc_consumption',

                ]);
                $latest = SchoolRtcConsumption::create($data);
            } else {
                $data = [
                    'date' => $this->date,
                    'epa' => $this->location_data['epa'],
                    'district' => $this->location_data['district'],
                    'section' => $this->location_data['section'],
                    //  'enterprise' => $this->location_data['enterprise'],
                    'school_name' => $this->location_data['school_name'],
                    'male_count' => $this->male_count,
                    'female_count' => $this->female_count,
                    'total' => $this->total,
                    // 'crop' => $this->crop,
                    'crop_cassava' => $cropCollection->contains('cassava') ? 1 : 0,
                    'crop_potato' => $cropCollection->contains('potato') ? 1 : 0,
                    'crop_sweet_potato' => $cropCollection->contains('sweet_potato') ? 1 : 0,
                    'uuid' => $uuid,
                    'user_id' => auth()->user()->id,
                    'submission_period_id' => $this->submissionPeriodId,
                    'period_month_id' => $this->selectedMonth,
                    'organisation_id' => Auth::user()->organisation->id,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'status' => 'approved',
                ];
                Submission::create([
                    'batch_no' => $uuid,
                    'form_id' => $this->selectedForm,
                    'user_id' => $currentUser->id,
                    'status' => 'approved',
                    //   'data' => json_encode($data),
                    'batch_type' => 'manual',
                    'is_complete' => 1,
                    'period_id' => $this->submissionPeriodId,
                    'table_name' => 'school_rtc_consumption',

                ]);
                $latest = SchoolRtcConsumption::create($data);
            }


            session()->flash('success', 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/school-rtc-consumption-form/view">View Submission here</a>');
            session()->flash('info', 'Your ID is: <b>' . substr($latest->id, 0, 8) . '</b>' . '<br><br> Please keep this ID for future reference.');

            return redirect()->to(url()->previous());
        } catch (\Exception $e) {
            # code...

            session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
        }
    }


    public function updated($property, $value)
    {
        $this->total = ($this->male_count ?? 0) + ($this->female_count ?? 0);
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

        $this->routePrefix = Route::current()->getPrefix();
    }
    #[On('open-submission')]
    public function clearTable()
    {
        $this->openSubmission = true;
        $this->targetSet = true;
        session()->flash('success', 'Successfully submitted your targets! You can proceed to submit your data now.');
    }
    public function render()
    {
        return view('livewire.forms.rtc-market.school-consumption.add');
    }
}
