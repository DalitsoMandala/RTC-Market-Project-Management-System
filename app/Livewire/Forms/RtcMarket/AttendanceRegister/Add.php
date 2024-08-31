<?php

namespace App\Livewire\Forms\RtcMarket\AttendanceRegister;

use App\Exceptions\UserErrorException;
use App\Livewire\tables\RtcMarket\AttendanceRegisterTable;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Ramsey\Uuid\Uuid;
use Throwable;

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

                $data = [
                    'meetingTitle' => $this->meetingTitle,
                    'meetingCategory' => $this->meetingCategory,
                    'rtcCrop' => json_encode($this->rtcCrop),
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
                    'uuid' => Uuid::uuid4()->toString(),
                ];


                AttendanceRegister::create($data);

                $this->resetExcept(
                    'meetingTitle',
                    'meetingCategory',
                    'rtcCrop',
                    'venue',
                    'district',
                    'startDate',
                    'endDate',
                    'totalDays',
                    'added'
                );

                $this->dispatch('refresh-data');
                session()->flash('success', 'Successfully submitted! <a href="#table">View Sumbissions</a>');
                session()->flash('info', 'Your ID is: <b>' . substr($uuid, 0, 8) . '</b>' . '<br><br> Please keep this ID for future reference.');

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

    public function mount()
    {

    }




    public function render()
    {
        return view('livewire.forms.rtc-market.attendance-register.add');
    }
}
