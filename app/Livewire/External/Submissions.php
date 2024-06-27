<?php

namespace App\Livewire\External;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Submissions extends Component
{
    use LivewireAlert;
    #[Validate('required')]
    public $variable;
    public $rowId;

    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        $this->resetErrorBag();
        try {

            $this->alert('success', 'Successfully updated');

        } catch (\Throwable $th) {
            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }
        $this->reset();
    }

    public function render()
    {
        return view('livewire.external.submissions');
    }
}
