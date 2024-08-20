<?php

namespace App\Livewire\Admin\Data;

use Livewire\Component;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;
class IndicatorTargetDetails extends Component
{
    use LivewireAlert;


    public $targetDetails = [];

    public function mount()
    {
        // Initialize with one target detail as an example
        $this->targetDetails[] = ['name' => '', 'target_value' => '', 'type' => 'number'];
    }

    public function addTargetDetail()
    {
        $this->targetDetails[] = ['name' => '', 'target_value' => '', 'type' => 'number'];
    }

    public function removeTargetDetail($index)
    {
        unset($this->targetDetails[$index]);
        $this->targetDetails = array_values($this->targetDetails); // Reindex array
    }


    public function render()
    {
        return view('livewire.admin.data.indicator-target-details');
    }
}
