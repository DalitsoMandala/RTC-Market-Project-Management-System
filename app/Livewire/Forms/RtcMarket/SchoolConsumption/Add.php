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
use App\Traits\ManualDataTrait;

class Add extends Component
{
    use LivewireAlert;
    use ManualDataTrait;

    public $date;
    public $crop = [];
    public $male_count;
    public $female_count;
    public $total = 0;


    public $form_name = 'SCHOOL CONSUMPTION FORM';

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
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        $user = Auth::user();
        $cropCollection = collect($this->crop);

        // Prepare data for the model
        $modelData = [
            'date' => $this->date,
            'epa' => $this->location_data['epa'],
            'district' => $this->location_data['district'],
            'section' => $this->location_data['section'],
            'school_name' => $this->location_data['school_name'],
            'male_count' => $this->male_count,
            'female_count' => $this->female_count,
            'total' => $this->total,
            'crop_cassava' => $cropCollection->contains('cassava') ? 1 : 0,
            'crop_potato' => $cropCollection->contains('potato') ? 1 : 0,
            'crop_sweet_potato' => $cropCollection->contains('sweet_potato') ? 1 : 0,

        ];

        // Prepare submission data
        $submissionData = [
            'form_id' => $this->selectedForm,
            'user_id' => $user->id,
            'batch_type' => 'manual',
            'is_complete' => 1,
            'period_id' => $this->submissionPeriodId,
        ];

        // Call the trait method to save the submission
        $this->saveManualSubmission(
            SchoolRtcConsumption::class, // Model class
            $modelData, // Data for the model
            $submissionData, // Data for the Submission table
            'rtc_a', // Table name for submission
            $this->routePrefix // Route prefix for the success message
        );
    }


    public function updated($property, $value)
    {
        $this->total = ($this->male_count ?? 0) + ($this->female_count ?? 0);
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.school-consumption.add');
    }
}
