<?php

namespace App\Livewire\Admin\Data;

use App\Models\Form;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class LeadPartners extends Component
{
    use LivewireAlert;
    public $leadPartners = [];
    public $sources;
    #[Validate('required', 'lead partners')]
    public $selectedLeadPartners = [];
    public $indicators;

    public $forms;

    #[Validate('required', 'forms')]
    public $selectedForms = [];

    public $rowId;
    public function save()
    {
        $this->validate();


        try {
            $indicator = Indicator::find($this->rowId);
            $indicator->responsiblePeopleforIndicators()->delete();
            foreach ($this->selectedLeadPartners as $organisationId) {
                $indicator->responsiblePeopleforIndicators()->create([
                    'organisation_id' => $organisationId,
                    'indicator_id' => $indicator->id,

                ]);
            }

            $indicator->forms()->sync($this->selectedForms);

            $this->dispatch('refresh');
            $this->alert('success', 'Indicator updated successfully');

        } catch (\Throwable $th) {

            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }


    }

    #[On('showModal')]
    public function setData($rowId)
    {
        $row = Indicator::find($rowId);


        $responsiblePeople = $row->organisation->pluck('id');
        $this->selectedLeadPartners = $responsiblePeople;

        $forms = $row->forms->pluck('id');


        $this->selectedForms = $forms->toArray();

        $this->dispatch('updateSelect', data: $responsiblePeople, formData: $this->selectedForms);
        $this->rowId = $rowId;




    }
    public function mount()
    {
        $this->leadPartners = Organisation::get();
        $this->forms = Form::whereNot('name', 'ATTENDANCE REGISTER')->whereNot('name', 'SEED DISTRIBUTION REGISTER')->get();
        $this->indicators = Indicator::get();

    }


    public function render()
    {
        return view('livewire.admin.data.lead-partners');
    }
}
