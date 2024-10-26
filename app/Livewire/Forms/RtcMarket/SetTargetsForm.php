<?php

namespace App\Livewire\Forms\RtcMarket;

use App\Models\OrganisationTarget;
use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class SetTargetsForm extends Component
{
    use LivewireAlert;


    public $targets = [];
    public $organisationTargets = [];
    public $submissionTargetIds = [];
    public $organisation;
    public $rules = [
        'targets.*.value' => 'required',
    ];

    public $messages = [
        'targets.*.value.required' => 'Please enter your value',
    ];

    public function saveTargets()
    {

        $this->validate();

        try {

            foreach ($this->targets as $target) {
                OrganisationTarget::create([
                    'organisation_id' => $this->organisation->id,
                    'submission_target_id' => $target->id,
                    'value' => $target->value
                ]);
            }



            $this->dispatch('open-submission');
        } catch (\Throwable $th) {
            //throw $th;
            $this->alert('error', 'Something went wrong!');
        }
    }

    public function mount()
    {
        $this->targets = SubmissionTarget::with('Indicator', 'financialYear', 'reportPeriodMonth')->whereIn('id', $this->submissionTargetIds)->get();
        $user = User::find(auth()->user()->id);

        $organisation_id = $user->organisation;
        $this->organisation = $organisation_id;
    }


    public function render()
    {
        return view('livewire.forms.rtc-market.set-targets-form');
    }
}
