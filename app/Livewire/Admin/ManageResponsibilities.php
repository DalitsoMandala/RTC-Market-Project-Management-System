<?php

namespace App\Livewire\Admin;

use App\Exceptions\UserErrorException;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\IndicatorForm;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class ManageResponsibilities extends Component
{
    use LivewireAlert;
    public $rowId;
    public $organisation;

    public $indicator;

    public $forms = [];
    #[Validate('required')]
    public $selectedForms = [];

    public function updated($property, $value)
    {


        if ($property === 'rowId') {

            $person = ResponsiblePerson::find($value);
            if ($person) {

                $indicator = Indicator::find($person->indicator_id);
                $organisation = Organisation::find($person->organisation_id);
                $this->indicator = $indicator->indicator_name;
                $this->organisation = $organisation->name;
                $indicatorForm = IndicatorForm::where('indicator_id', $person->indicator_id)->pluck('form_id')->toArray();
                $this->forms = Form::whereIn('id', $indicatorForm)->get();
                $this->dispatch('refreshSelect2', data: $this->forms);
                $sources = $person->sources;

                if (!empty($sources)) {

                    $this->selectedForms = $sources->pluck('form_id')->toArray();
                    $this->dispatch('select-forms', data: $this->selectedForms);
                }
            }
        }
    }
    public function save()
    {

        try {
            $this->validate();
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {
            $responsible_people = ResponsiblePerson::find($this->rowId);
            $responsible_people->sources()->where('person_id', $this->rowId)->delete();
            foreach ($this->selectedForms as $form) {
                $responsible_people->sources()->create([
                    'form_id' => $form,
                ]);

            }
            $this->dispatch('closeModal');


            session()->flash('success', 'Successfully submitted.');
        } catch (UserErrorException $e) {
            # code...

            session()->flash('error', 'Something went wrong during submission.');
        }





    }

    public function mount()
    {
        $this->forms = Form::get();

    }


    public function render()
    {
        return view('livewire.admin.manage-responsibilities');
    }
}
