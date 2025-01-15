<?php

namespace App\Livewire\Baseline;

use Livewire\Component;
use App\Models\Baseline;
use App\Models\Indicator;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class UpdateBaselineData extends Component
{
    use LivewireAlert;
    public $baselineDataId;
    public $indicator_id;
    public $baseline_value;
    public $indicators;

    protected $rules = [
        'indicator_id' => 'required|exists:indicators,id',
        'baseline_value' => 'required|numeric|min:0',
    ];

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

    public function updatedIndicatorId()
    {
        $baselineData = Baseline::where('indicator_id', $this->indicator_id)->first();

        if ($baselineData) {
            $this->baselineDataId = $baselineData->id;
            $this->indicator_id = $baselineData->indicator_id;
            $this->baseline_value = $baselineData->baseline_value;
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

            $this->validate();
        } catch (\Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }


        try {
            $baselineData = Baseline::where('indicator_id', $this->indicator_id)->first();
            if ($baselineData) {
                $baselineData->update([
                    'indicator_id' => $this->indicator_id,
                    'baseline_value' => $this->baseline_value,
                ]);
            }

            $this->dispatch('refresh');
            session()->flash('success', 'Baseline data updated successfully.');
        } catch (\Throwable $th) {
            //throw $th;
            session()->flash('error', 'Something went wrong while updating the baseline data.');
            \Log::error($th->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.baseline.update-baseline-data');
    }
}
