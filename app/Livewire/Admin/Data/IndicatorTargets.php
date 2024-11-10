<?php

namespace App\Livewire\Admin\Data;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorTarget;
use App\Models\Project;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class IndicatorTargets extends Component
{
    use LivewireAlert;
    public $financial_year_id;
    public $project_id;
    public $target_value = 0;
    public $baseline_value = 0;
    public $type;
    public $rowId;
    public $targetDetails = [];

    public $showDetail = false;

    public $indicators = [];

    public $selectedIndicator;


    public $financial_years = [];
    public $projects = [];

    public $selectedProject, $selectedFinancialYear;
    #[On('show-form')]
    public function setData($rowId)
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $row = IndicatorTarget::find($rowId);


        $this->financial_year_id = $row->financial_year_id;
        $this->project_id = $row->project_id;
        $this->target_value = $row->target_value;
        $this->baseline_value = $row->baseline_value;
        $this->type = $row->type;
        $this->selectedIndicator = $row->indicator_id;

        if ($row->details()->count() > 0) {
            $this->targetDetails = [];
            $details = $row->details()->get();
            foreach ($details as $detail) {
                $this->targetDetails[] = [
                    'name' => $detail->name,
                    'target_value' => $detail->target_value,
                    'type' => $detail->type
                ];
            }
        }

        $this->rowId = $rowId;



    }

    #[On('add-form')]
    public function addForm()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        $this->rowId = null;
        $this->target_value = null;
        $this->baseline_value = null;
        $this->type = null;
        $this->selectedIndicator = null;
        $this->financial_year_id = null;
        $this->project_id = null;

    }

    public function updatedType($value)
    {
        if ($value == 'detail') {
            $this->baseline_value = null;
            $this->target_value = null;

        }
    }
    public function mount()
    {

        // Initialize with one target detail if creating a new indicator
        $this->targetDetails[] = [
            'name' => '',
            'target_value' => '',
            'type' => 'number'
        ];


        $this->indicators = Indicator::get();
        $this->financial_years = FinancialYear::get();
        $this->projects = Project::get();
    }

    public function addTargetDetail()
    {
        $this->targetDetails[] = [
            'name' => '',
            'target_value' => '',
            'type' => 'number'
        ];
    }

    public function removeTargetDetail($index)
    {

        unset($this->targetDetails[$index]);
        $this->targetDetails = array_values($this->targetDetails); // Reindex array



    }



    public function save()
    {
        $rules = [
            'selectedIndicator' => 'required',
            'financial_year_id' => 'required|exists:financial_years,id',
            'project_id' => 'required|exists:projects,id',
            'target_value' => 'nullable|integer',
            'baseline_value' => 'nullable|integer',
            'type' => 'required|in:number,percentage,detail',
        ];

        if ($this->type === 'detail') {
            $rules['targetDetails'] = 'required';
            $rules['targetDetails.*.name'] = 'required|string';
            $rules['targetDetails.*.target_value'] = 'required|integer';
            $rules['targetDetails.*.type'] = 'required|in:number,percentage';
        }

        $this->validate($rules, messages: [
            'targetDetails.required' => 'Target details are required when type is "detail".',
        ]);

        try {
            // Save Indicator Target


            if ($this->rowId) {

                $indicatorTarget = IndicatorTarget::find($this->rowId)->update([

                    'target_value' => $this->target_value,
                    'baseline_value' => $this->baseline_value,
                    'type' => $this->type,
                ]);

                // Save Target Details if type is 'detail'
                if ($this->type === 'detail') {

                    $indicatorTarget = IndicatorTarget::find($this->rowId);
                    foreach ($this->targetDetails as $detail) {
                        $indicatorTarget->details()->delete();
                        $indicatorTarget->details()->create($detail);
                    }
                }
                $this->dispatch('refresh');
                session()->flash('success', 'Indicator target and details saved successfully.');

            } else {
                $indicatorTarget = IndicatorTarget::create([
                    'indicator_id' => $this->selectedIndicator,
                    'target_value' => $this->target_value == 0,
                    'baseline_value' => $this->baseline_value ?? 0,
                    'financial_year_id' => $this->financial_year_id,
                    'project_id' => $this->project_id,
                    'type' => $this->type,
                ]);

                // Save Target Details if type is 'detail'
                if ($this->type === 'detail') {
                    foreach ($this->targetDetails as $detail) {
                        $indicatorTarget->details()->create($detail);
                    }
                }
                $this->dispatch('refresh');
                session()->flash('success', 'Indicator target and details saved successfully.');
            }



        } catch (\Throwable $th) {

            $this->alert('error', 'Something went wrong');
            Log::error($th);
        }
    }


    public function render()
    {
        return view('livewire.admin.data.indicator-targets');
    }
}
