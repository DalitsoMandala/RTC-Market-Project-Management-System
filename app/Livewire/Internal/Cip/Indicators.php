<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\IndicatorForm;
use Livewire\Attributes\Lazy;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class Indicators extends Component
{
    use LivewireAlert;

    public $indicator;
    public $rowId;

    public $projects = [];
    #[Validate('required')]
    public $selectedProject;

    public $indicators = [];

    public $selectedIndicators;


    public $leadPartners = [];
    #[Validate('required')]
    public $selectedLeadPartner = [];

    public $sources = [];
    #[Validate('required')]
    public $selectedSource = [];

    public $submissionTypes = [];

    public $selectedSubmissionType;

    public function setData($id)
    {
        $this->resetErrorBag();
        $indicator = Indicator::find($id);
        $this->rowId = $id;
        $this->indicator = $indicator->indicator_name;
        $this->selectedProject = $indicator->project->id;
        $resp = $indicator->responsiblePeopleforIndicators->pluck('organisation_id');
        $this->selectedLeadPartner = $resp->toArray();
        $forms = $indicator->forms->pluck('id');
        $this->selectedSource = $forms->toArray();

        $this->dispatch('select-partners', data: $this->selectedLeadPartner, data2: $this->selectedSource);
    }

    public function save()
    {
        $this->validate();
        try {
            $id = $this->rowId;
            ResponsiblePerson::where('indicator_id', $id)->delete();

            // Create new ResponsiblePerson records for the selected partners
            foreach ($this->selectedLeadPartner as $partner) {
                ResponsiblePerson::create([
                    'organisation_id' => $partner,
                    'indicator_id' => $id,
                ]);
            }

            $indicator = Indicator::find($id);
            IndicatorForm::where('indicator_id', $id)->delete();

            foreach ($this->selectedSource as $form) {
                IndicatorForm::create([
                    'form_id' => $form,
                    'indicator_id' => $id,
                ]);
            }

            $this->dispatch('refresh');
            session()->flash('success', 'Successfully updated!');
        } catch (\Throwable $th) {
            dd($th);
            session()->flash('error', 'Something went wrong!');
            Log::error($th);
        }

        $this->dispatch('hideModal');
        $this->reset();
    }

    public function mount()
    {

        $this->projects = Project::get();
        $this->leadPartners = Organisation::get();
        $this->sources = Form::get();
        $this->indicators = Indicator::get();


    }
    public function render()
    {
        return view('livewire.internal.cip.indicators');
    }
}