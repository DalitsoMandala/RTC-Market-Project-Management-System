<?php

namespace App\Livewire\Internal\Cip;

use App\Models\Form;
use App\Models\Indicator;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Forms extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $name;
    #[Validate('required')]
    public $type = 'routine/recurring';
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();
        $form = Form::find($id);
        $this->rowId = $id;
        $this->name = $form->name;
        $this->type = $form->type;
    }

    public function save()
    {
        $this->validate();

        try {

            Form::find($this->rowId)->update([
                'name' => $this->name,
                'type' => $this->type
            ]);

            $this->alert('success', 'Successfully updated');
            $this->dispatch('refresh');
        } catch (\Throwable $th) {
            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }

        $this->dispatch('hideModal');
        $this->reset();
    }

    public function render()
    {
        return view('livewire.internal.cip.forms');
    }
}
