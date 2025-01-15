<?php

namespace App\Livewire\Components;

use App\Models\Baseline;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class BaselineSingle extends Component
{
    use LivewireAlert;

    public $model;
    public $baseline_value;
    public $modelId;
    public $initValue;

    public function singleSave($id)
    {
    $this->validate();
    Baseline::find($id)->update([
        'baseline_value' => $this->baseline_value
    ]);


$this->dispatch('refresh');
    $this->alert('success', 'Value updated successfully');
    }

        public function rules()
        {
            return [
                'baseline_value' => ['required', 'numeric', 'min:0'],
            ];
        }

        protected function validationAttributes()
        {
            return [
                'baseline_value' => 'Baseline value',
            ];
        }
public function cancel(){
    $this->baseline_value = $this->initValue;
}
    public function mount()
    {
        $this->fill([
            'baseline_value' => $this->model->baseline_value,
            'modelId' => $this->model->id,
            'initValue' => $this->model->baseline_value
        ]);
    }

    public function render()
    {
        return view('livewire.components.baseline-single');
    }
}