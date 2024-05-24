<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use Livewire\Component;

class ViewForms extends Component
{
    public $form_name;
    public function mount($id)
    {
        $form = Form::find($id);
        $this->form_name = $form->name;
    }
    public function render()
    {
        return view('livewire.internal.cip.view-forms');
    }
}