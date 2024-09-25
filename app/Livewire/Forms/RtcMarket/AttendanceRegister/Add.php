<?php

namespace App\Livewire\Forms\RtcMarket\AttendanceRegister;

use Throwable;
use App\Models\Form;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use Livewire\Attributes\Validate;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Exceptions\UserErrorException;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Livewire\tables\RtcMarket\AttendanceRegisterTable;

class Add extends Component
{
    use LivewireAlert;





    public $meetingTitle;
    public $meetingCategory;
    public $rtcCrop = [];
    public $venue;
    public $district = 'BALAKA';
    public $startDate;
    public $endDate;
    public $totalDays;
    public $name;
    public $sex = 'MALE';
    public $organization;
    public $designation;
    public $phone_number;
    public $email;

    public $disable = false;

    public $forms = [];

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject, $selectedIndicator,
    $submissionPeriodId;
    public $routePrefix;
    public $openSubmission = true;
    protected $rules = [
        'meetingTitle' => 'required|string|max:255',
        'meetingCategory' => 'required',
        'rtcCrop' => 'required|array',
        'venue' => 'required|string|max:255',
        'district' => 'required|string|max:255',
        'startDate' => 'required|date',
        'endDate' => 'required|date|after_or_equal:startDate',
        'totalDays' => 'required|integer|min:0',
        'name' => 'required|string|max:255',
        'sex' => 'required',
        'organization' => 'required|string|max:255',
        'designation' => 'required|string|max:255',
        'phone_number' => 'required|string',
        'email' => 'required|email|max:255',
    ];

    public function save()
    {


        try {

            $this->validate();



        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        //continue
        try {


            try {

                $uuid = Uuid::uuid4()->toString();
                $collect = collect($this->rtcCrop);


                $data = [
                    'meetingTitle' => $this->meetingTitle,
                    'meetingCategory' => $this->meetingCategory,
                    'rtcCrop_cassava' => $collect->contains('Cassava') ? true : false, // True/False (assuming it's a boolean)
                    'rtcCrop_potato' => $collect->contains('Potato') ? true : false, // True/False
                    'rtcCrop_sweet_potato' => $collect->contains('Sweet potato') ? true : false, // True/False
                    'venue' => $this->venue,
                    'district' => $this->district,
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                    'totalDays' => $this->totalDays,
                    'name' => $this->name,
                    'email' => $this->email,
                    'sex' => $this->sex,
                    'organization' => $this->organization,
                    'designation' => $this->designation,
                    'phone_number' => $this->phone_number,
                    'user_id' => auth()->user()->id,
                    'uuid' => $uuid,
                    'submission_period_id' => $this->submissionPeriodId,
                    'organisation_id' => Auth::user()->organisation->id,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'period_month_id' => $this->selectedMonth,

                ];


                $insert = AttendanceRegister::create($data);


                session()->flash('success', 'Successfully submitted!  <a href="' . $this->routePrefix . '/forms/rtc_market/attendance-register/view">View Submission here</a>');
                session()->flash('info', 'Your ID is: <b>' . $insert->att_id . '</b>' . '<br><br> Please keep this ID for future reference.');
                $this->dispatch('refresh-data');
                $this->resetExcept(
                    'meetingTitle',
                    'meetingCategory',
                    'rtcCrop',
                    'venue',
                    'district',
                    'startDate',
                    'endDate',
                    'totalDays',
                    'added',
                    'submissionPeriodId',
                    'selectedFinancialYear',
                    'selectedMonth',
                    'routePrefix'
                );


            } catch (UserErrorException $e) {
                # code...
                Log::error('Submission error: ' . $e->getMessage());

                // Provide a generic error message to the user
                session()->flash('error', $e->getMessage());
            }

        } catch (Throwable $th) {
            # code...

            session()->flash('error', 'Something went wrong!');
            Log::error($th->getMessage());
        }

    }

    // public function mount()
    // {

    // }
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

            } else {
                $this->openSubmission = false;
            }

            $this->routePrefix = Route::current()->getPrefix();


        }
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.attendance-register.add');
    }
}
