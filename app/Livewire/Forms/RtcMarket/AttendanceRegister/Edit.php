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
use App\Helpers\DistrictObject;
class Edit extends Component
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
public $uniqueId;
protected $table_name = 'attendance-register';
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
        'phone_number' => 'nullable|string',
        'email' => 'nullable|email|max:255',
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

            ];


            AttendanceRegister::where('att_id', $this->uniqueId)->update($data);

            $this->dispatch('show-alert', data: [
                'type' => 'success',
                'message' => 'Successfully submitted! <a href="' . $this->routePrefix . '/forms/rtc_market/attendance-register/view/' . $this->uniqueId . '">View Submission here</a>',
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

    public function mount($id, $uuid)
    {
        $farmer = AttendanceRegister::where('uuid', $uuid)->where('id', $id)->firstOrFail();

        $this->openSubmission = true;
        $this->routePrefix = Route::current()->getPrefix();
        $this->uniqueId = $farmer->att_id;
        $this->editData($farmer);

    }

    public function editData($data)
    {
        $this->fill([
            'meetingTitle' => $data->meetingTitle,
            'meetingCategory' => $data->meetingCategory,
            'rtcCrop' => [
                $data->rtcCrop_cassava ? 'Cassava' : null,
                $data->rtcCrop_potato ? 'Potato' : null,
                $data->rtcCrop_sweet_potato ? 'Sweet potato' : null,
            ],
            'venue' => $data->venue,
            'district' => DistrictObject::cleanDistrict($data->district),
            'startDate' => $data->startDate,
            'endDate' => $data->endDate,
            'totalDays' => $data->totalDays,
            'name' => $data->name,
            'email' => $data->email,
            'sex' => $data->sex,
            'organization' => $data->organization,
            'designation' => $data->designation,
            'category' => $data->category,
            'phone_number' => $data->phone_number,
        ]);
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
        return view('livewire.forms.rtc-market.attendance-register.edit');
    }
}
