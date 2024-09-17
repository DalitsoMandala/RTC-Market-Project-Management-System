<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class TestExports extends Component
{
    use LivewireAlert;

    public $selectedOption;

    public function selectOption($option)
    {
        $this->selectedOption = $option;
    }

    public function save()
    {
        // Handle saving the selected option here
        // For example:
        // dd($this->selectedOption);

        $this->alert('success', 'data generated');
    }

    public function mount()
    {

    }


    public function render()
    {
        return view('livewire.test-exports');
    }
}
