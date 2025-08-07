<?php

namespace App\Livewire\Admin\Data;

use App\Models\Form;
use App\Models\Source;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class IndicatorSources extends Component
{
    use LivewireAlert;
    public $leadPartners = [];
    public $sources;

    public $selectedLeadPartners = [];
    #[Validate('required', 'lead partner')]
    public $selectedLeadPartner = [];
    public $indicators = [];

    public $forms = [];

    // #[Validate('required')]
    public $selectedForms = [];

    public $rowId;
    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            ResponsiblePerson::where('indicator_id', $this->rowId)->delete();

            foreach ($this->selectedLeadPartner as $partner) {
                $organisation = Organisation::find($partner);
                if ($organisation) {
                    ResponsiblePerson::create([
                        'indicator_id' => $this->rowId,
                        'organisation_id' => $organisation->id,
                    ]);
                }
            }

            $currentIndicator = Indicator::find($this->rowId);
            if ($currentIndicator) {
                $currentIndicator->forms()->sync($this->selectedForms);
            }

            DB::commit();

            $this->dispatch('refresh');
            session()->flash('success', 'Indicator updated successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            session()->flash('error', 'Something went wrong');
            Log::error($th);
        }
    }


    #[On('showModal')]
    public function setData($rowId)
    {

        $this->selectedForms = [];

        $row = Indicator::find($rowId);
        $organisations = $row->organisation->pluck('id');
        $forms = $row->forms->pluck('id');
        $this->forms = Form::get();
        $this->selectedForms = $forms->toArray();
        $this->leadPartners = Organisation::all();
        $this->selectedLeadPartner  = $organisations->toArray();
        $this->indicators = Indicator::where('id', $rowId)->get();
        $this->rowId = $rowId;
    }




    // public function updated($property)
    // {
    //     if ($property == 'selectedLeadPartner') {
    //         $this->selectedForms = [];
    //         // $response = ResponsiblePerson::where('indicator_id', $this->rowId)->where('organisation_id', $this->selectedLeadPartner)->first();

    //         // $sources = Source::where('person_id', $response->id)->pluck('form_id')->toArray();

    //         // $this->selectedForms = $sources;




    //     }
    // }


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
