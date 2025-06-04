<?php

namespace App\Livewire\Forms\RtcMarket\AttendanceRegister;

use App\Exceptions\UserErrorException;
use App\Livewire\tables\RtcMarket\AttendanceRegisterTable;
use App\Models\AttendanceRegister;
use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\OrganisationTarget;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\User;
use App\Traits\ManualDataTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Throwable;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $meetingTitle;
    public $meetingCategory = 'Meeting';
    public $rtcCrop = [];
    public $venue;
    public $district = 'Balaka';
    public $startDate;
    public $endDate;
    public $totalDays;
    public $name;
    public $sex = 'Male';
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
    public $form_name = 'ATTENDANCE REGISTER';

    public $selectedProject,
        $selectedIndicator,
        $submissionPeriodId;

    public $routePrefix;
    public $openSubmission = false;
    public $targetSet = false;
    public $targetIds = [];
    public $category;

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
        'category' => 'required|string|max:255',
        'phone_number' => 'required|string',
        'email' => 'required|email|max:255',
    ];

    public function validationAttributes()
    {
        return [
            'rtcCrop' => 'Crop'
        ];
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (Throwable $e) {
            //       session()->flash('validation_error', 'There are errors in the form.');
            $this->dispatch('show-alert', data: [
                'type' => 'error',  // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }

        // continue
        DB::beginTransaction();
        try {
            $uuid = Uuid::uuid4()->toString();
            $collect = collect($this->rtcCrop);

            $data = [
                'meetingTitle' => $this->meetingTitle,
                'meetingCategory' => $this->meetingCategory,
                'rtcCrop_cassava' => $collect->contains('Cassava') ? true : false,  // True/False (assuming it's a boolean)
                'rtcCrop_potato' => $collect->contains('Potato') ? true : false,  // True/False
                'rtcCrop_sweet_potato' => $collect->contains('Sweet potato') ? true : false,  // True/False
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
                'category' => $this->category,
                'phone_number' => $this->phone_number,
                'user_id' => auth()->user()->id,
                'uuid' => $uuid,
                'submission_period_id' => $this->submissionPeriodId,
                'organisation_id' => Auth::user()->organisation->id,
                'financial_year_id' => $this->selectedFinancialYear,
                'period_month_id' => $this->selectedMonth,
                'status' => 'approved'
            ];
            $user = User::find(auth()->user()->id);
            $submissionData = [
                'form_id' => $this->selectedForm,
                'user_id' => $user->id,
                'batch_type' => 'manual',
                'is_complete' => 1,
                'period_id' => $this->submissionPeriodId,
                'status' => 'approved',
                'table_name' => 'attendance_registers',
                'batch_no' => $uuid
            ];

            Submission::create($submissionData);
            AttendanceRegister::create($data);
            session()->put([
                'attendance_register' => [
                    'meetingTitle' => $this->meetingTitle,
                    'meetingCategory' => $this->meetingCategory,
                    'rtcCrop' => $this->rtcCrop,
                    'venue' => $this->venue,
                    'district' => $this->district,
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate,
                    'totalDays' => $this->totalDays,
                ]
            ]);

            $this->resetErrorBag();
            $this->resetValidation();
            $this->reset([
                'name',
                'email',
                'sex',
                'organization',
                'designation',
                'category',
                'phone_number'
            ]);
            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/attendance-register/view">View Submission here</a>',
            ]);

            DB::commit();
        } catch (Throwable $th) {
            // code...

            DB::rollBack();

            $this->dispatch('show-alert', data: [
                'type' => 'error',
                'message' => 'Something went wrong!'
            ]);
            Log::error($th->getMessage());
        }
    }

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {
        // Validate required IDs
        $this->validateIds($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Find and validate related models
        $this->findAndSetModels($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id);

        // Check if the submission period is open and targets are set
        $this->checkSubmissionPeriodAndTargets();

        // Set the route prefix
        $this->routePrefix = Route::current()->getPrefix();

        if (session()->has('attendance_register')) {
            $this->meetingTitle = session()->get('attendance_register')['meetingTitle'];
            $this->meetingCategory = session()->get('attendance_register')['meetingCategory'];
            $this->rtcCrop = session()->get('attendance_register')['rtcCrop'];
            $this->venue = session()->get('attendance_register')['venue'];
            $this->district = session()->get('attendance_register')['district'];
            $this->startDate = session()->get('attendance_register')['startDate'];
            $this->endDate = session()->get('attendance_register')['endDate'];
            $this->totalDays = session()->get('attendance_register')['totalDays'];
        }
    }



    public function clearSessionData()
    {
        session()->forget(
            'attendance_register'
        );

        $this->reset([
            'meetingTitle',
            'meetingCategory',
            'rtcCrop',
            'venue',
            'district',
            'startDate',
            'endDate',
            'totalDays',
        ]);

        $this->dispatch('show-alert', data: [
            'type' => 'notice',
            'message' => 'Form data has been cleared!'
        ]);
    }

    public function render()
    {
        if ($this->selectedForm) {
            $this->form_name = Form::find($this->selectedForm)->name;
        }
        return view('livewire.forms.rtc-market.attendance-register.add');
    }
}
