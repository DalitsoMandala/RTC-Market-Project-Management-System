<?php

namespace App\Livewire\Admin\Data;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use App\Models\Source;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class IndicatorSources extends Component
{
    use LivewireAlert;
    public $leadPartners = [];
    public $sources;

    public $selectedLeadPartners = [];
    #[Validate('required', 'lead partner')]
    public $selectedLeadPartner;
    public $indicators = [];

    public $forms = [];

    #[Validate('required')]
    public $selectedForms = [];

    public $rowId;
    public function save()
    {
        $this->validate();


        try {

            $responsible = ResponsiblePerson::where('indicator_id', $this->rowId)->where('organisation_id', $this->selectedLeadPartner)->first();
            $id = $responsible->id;

            Source::where('person_id', $id)->delete();
            foreach ($this->selectedForms as $form) {
                # code...
                Source::create([
                    'form_id' => $form,
                    'person_id' => $id
                ]);
            }


            $this->dispatch('refresh');
            $this->alert('success', 'Indicator updated successfully');

        } catch (\Throwable $th) {
            dd($th);
            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }


    }

    #[On('showModal')]
    public function setData($rowId)
    {
        $this->selectedForms = [];
        $this->selectedLeadPartner = null;
        $row = Indicator::find($rowId);
        $organisations = $row->organisation->pluck('id');
        $this->leadPartners = Organisation::whereIn('id', $organisations)->get();
        $this->indicators = Indicator::where('id', $rowId)->get();
        $forms = $row->forms->pluck('id');
        $this->forms = Form::whereIn('id', $forms)->get();

        $this->rowId = $rowId;

    }

    public function updated($property)
    {
        if ($property == 'selectedLeadPartner') {
            $this->selectedForms = [];
            $response = ResponsiblePerson::where('indicator_id', $this->rowId)->where('organisation_id', $this->selectedLeadPartner)->first();

            $sources = Source::where('person_id', $response->id)->pluck('form_id')->toArray();

            $this->selectedForms = $sources;




        }
    }


    public function mount()
    {

        $this->indicators = Indicator::get();
        // $this->forms = Form::whereNot('name', 'ATTENDANCE REGISTER')->whereNot('name', 'SEED DISTRIBUTION REGISTER')->whereNot('name', 'EMBASSY OF IRELAND')->get();

    }


    public function render()
    {
        return view('livewire.admin.data.indicator-sources');
    }
}
