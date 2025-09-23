<?php

namespace App\Livewire\Baseline;

use Livewire\Component;
use App\Models\Baseline;
use App\Models\Indicator;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use App\Models\BaselineDataMultiple;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UpdateBaselineData extends Component
{
    use LivewireAlert;
    public $baselineDataId;
    public $indicator_id;

    public $baseline_value;
    public $baselineValues = [];
    public $indicators;
    public $indicator;
public $multiple = false;

    public function mount($baselineDataId = null)
    {
        if ($baselineDataId) {
            $baselineData = Baseline::find($baselineDataId);
            $this->baselineDataId = $baselineData->id;
            $this->indicator_id = $baselineData->indicator_id;
            $this->baseline_value = $baselineData->baseline_value;
        }
        $this->indicators = Indicator::all();
    }


    #[On('set')]
    public function updatedIndicatorId($indicator_id)
    {
        $baselineData = Baseline::where('indicator_id', $indicator_id)->first();

        if($baselineData->baseline_is_multiple === 1){

            $this->multiple = true;
                   $this->baselineDataId = $baselineData->id;
            $this->indicator_id = $baselineData->indicator_id;
            $baselineMultiple = $baselineData->baselineMultiple;
            $this->baselineValues = [];
            foreach ($baselineMultiple as $baseline) {

                $this->baselineValues[] = [
                    'baseline_value' => $baseline->baseline_value,
                    'name' => $baseline->name . ' (' . $baseline->unit_type.')',
                    'id' => $baseline->id
                ];
            }

            $this->indicator = $baselineData->indicator->indicator_name;

        }else{
            $this->multiple = false;

            $this->baselineDataId = $baselineData->id;
            $this->indicator_id = $baselineData->indicator_id;
            $this->baseline_value = $baselineData->baseline_value;
            $this->indicator = $baselineData->indicator->indicator_name;
        }


    }

    #[On('submit-form')]
    public function resetForm()
    {
        session()->flash('success', 'Value updated successfully');
    }
  public function save()
{
    try {
   $rules = [
        'indicator_id' => 'required|exists:indicators,id',
    ];

    if ($this->multiple) {
        $rules['baselineValues.*.baseline_value'] = 'required|numeric|min:0';
    } else {
        $rules['baseline_value'] = 'required|numeric|min:0';
    }

    $this->validate($rules,[
             'baselineValues.*.baseline_value.required' => 'The baseline value field is required.',
        'baselineValues.*.baseline_value.numeric' => 'The baseline value must be a number.',
        'baselineValues.*.baseline_value.min' => 'The baseline value must be at least 0.',
    ],[


    ]);
    } catch (\Throwable $e) {
        $this->dispatch('show-alert', data: [
            'type' => 'error',
            'message' => 'There are errors in the form.'
        ]);
        throw $e;
    }

    try {
        if ($this->multiple) {
            // Update multiple baseline values

            foreach ($this->baselineValues as $baselineValue) {
                $data = BaselineDataMultiple::find($baselineValue['id']);

                if ($data) {
                    $data->update([
                        'baseline_value' => $baselineValue['baseline_value'],
                    ]);
                }

            }
        } else {
            // Update single baseline value
            $data = Baseline::find($this->baselineDataId);
            if ($data) {
                $data->update([
                    'baseline_value' => $this->baseline_value,
                ]);
            }
        }

        $this->dispatch('refresh');
        session()->flash('success', 'Baseline data updated successfully.');
        $this->dispatch('hideModal');
    } catch (\Throwable $th) {
        session()->flash('error', 'Something went wrong while updating the baseline data.');
        $this->dispatch('hideModal');
        Log::error($th->getMessage());
    }
}



    public function render()
    {
        return view('livewire.baseline.update-baseline-data');
    }
}
