<?php

namespace App\Livewire\Forms;

use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class HouseHoldConsumption extends Component
{
    use LivewireAlert;
    public $items;
    public $newItem = ['name' => '', 'quantity' => '', 'additional_data_option' => 'no'];
    public $additionalRows = [];

    public function addAdditionalRow()
    {
        $this->additionalRows[] = ['detail' => '', 'value' => '', 'description' => '', 'date' => ''];

    }

    public function save()
    {

    }

    public function mount()
    {

    }
    public function render()
    {
        return view('livewire.forms.house-hold-consumption');
    }
}
