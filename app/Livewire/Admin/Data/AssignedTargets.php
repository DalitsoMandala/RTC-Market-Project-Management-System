<?php

namespace App\Livewire\Admin\Data;

use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Models\AssignedTarget;
use App\Models\IndicatorTarget;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class AssignedTargets extends Component
{
    use LivewireAlert;

    public $indicator_id;
    public $organisation_id;
    public $target_value;
    public $current_value = 0;
    public $type;
    public $detail = [];

    public $indicatorTargets = [];
    public $organisations = [];

    public $lop_target_value;
    public $lop_type;

    public $lop_details;

    public $rowId;

    public $showDelete = false;

    public $financial_years = [];
    public $projects = [];

    public $selectedProject, $selectedFinancialYear;
    #[On('show-form')]
    public function setData($rowId, $indicator_id)
    {
        $this->resetValidation();
        $this->resetErrorBag();
        $row = IndicatorTarget::find($rowId);
        $assign = $row->assignedTargets;


        $this->lop_target_value = $row->target_value;
        $this->lop_type = $row->type;
        if ($this->lop_type == 'detail') {
            $details = $row->details()->get();
            $this->lop_details = '';
            foreach ($details as $detail) {
                $detail->type = $detail->type == 'percentage' ? '%' : '';
                $this->lop_details .= $detail->name . ' (' . $detail->target_value . $detail->type . ') <br/> ';

            }

        }

        $this->rowId = $rowId;

        $this->selectedFinancialYear = $row->financial_year_id;
        $this->selectedProject = $row->project_id;

        if ($this->selectedFinancialYear && $this->selectedProject && $this->indicator_id) {

            $indicator = Indicator::find($this->indicator_id);
            if ($indicator) {
                $organisations = $indicator->organisation->pluck('id');
                $this->organisations = Organisation::whereIn('id', $organisations)->get();
            }
        }

    }


    public function updated($property)
    {

        if ($this->selectedFinancialYear && $this->selectedProject && $this->indicator_id) {

            $indicator = Indicator::find($this->indicator_id);
            if ($indicator) {
                $organisations = $indicator->organisation->pluck('id');
                $this->organisations = Organisation::whereIn('id', $organisations)->get();
            }
        }
    }
    public function updatedOrganisationId()
    {
        $assignedTargets = AssignedTarget::where('indicator_target_id', $this->rowId)->where('organisation_id', $this->organisation_id)->first();

        if ($assignedTargets) {
            if ($assignedTargets->detail) {
                $this->type = 'detail';
                $this->detail = [];
                $json = json_decode($assignedTargets->detail, true);
                foreach ($json as $value) {
                    $this->detail[] = ['name' => $value['name'], 'target_value' => $value['target_value'], 'type' => $value['type']];
                }

            } else {
                $this->target_value = $assignedTargets->target_value;
                $this->current_value = $assignedTargets->current_value;
                $this->type = $assignedTargets->type;
            }

        }


    }

    #[On('add-form')]
    public function addForm()
    {
        $this->resetValidation();
        $this->resetErrorBag();

        $this->resetForm();

    }

    public function deleteData()
    {

        if ($this->showDelete) {
            $assignedTargets = AssignedTarget::where('indicator_target_id', $this->rowId);
            $assignedTargets->delete();
            session()->flash('success', 'Assigned target successfully deleted!');
            $this->resetForm();
        } else {
            session()->flash('error', 'Please turn on the switch to delete');
        }

        $this->dispatch('refresh');

    }

    public function resetForm()
    {
        $this->indicator_id = null;
        $this->organisation_id = null;
        $this->target_value = null;
        $this->current_value = 0;
        $this->type = null;
        $this->rowId = null;
        $this->detail[] = ['name' => '', 'target_value' => '', 'type' => 'number'];
        $this->lop_details = '';
        $this->lop_target_value = null;
        $this->lop_type = null;
        $this->showDelete = false;
        $this->selectedFinancialYear = null;
        $this->selectedProject = null;

    }
    public function save()
    {
        $rules = [
            'indicator_id' => 'required|exists:indicator_targets,id',
            'organisation_id' => 'required|exists:organisations,id',
            'target_value' => 'required|integer',
            'current_value' => 'nullable|integer',
            'type' => 'required|in:number,percentage,detail',
        ];

        if ($this->type === 'detail') {
            $rules['detail'] = 'required|array|min:1';
            $rules['detail.*.name'] = 'required|string';
            $rules['detail.*.value'] = 'required|integer';
            $rules['detail.*.type'] = 'required|in:number,percentage';
        }

        $this->validate($rules, attributes: [
            'detail' => 'target details',
            'type' => 'target type',
            'indicator_id' => 'target',
            'organisation_id' => 'organisation',
            'detail.*.name' => 'target detail name',
            'detail.*.value' => 'target detail value',
            'detail.*.type' => 'target detail type',
        ]);

        try {
            if ($this->type == 'detail') {
                $this->target_value = 0; // Reset target_value when type is 'detail'
            }

            // Check if this is an update or a new create
            $existingAssignedTarget = AssignedTarget::where('indicator_target_id', $this->rowId)
                ->where('organisation_id', $this->organisation_id)
                ->first();

            if ($existingAssignedTarget) {
                // Update the existing assigned target
                $existingAssignedTarget->update([
                    'target_value' => $this->target_value,
                    'current_value' => $this->current_value,
                    'type' => $this->type,
                    'detail' => $this->type == 'detail' ? json_encode($this->detail) : null,
                ]);
                session()->flash('success', 'Assigned target updated successfully.');
            } else {
                // Create a new assigned target
                AssignedTarget::create([
                    'organisation_id' => $this->organisation_id,
                    'indicator_target_id' => $this->rowId,
                    'target_value' => $this->target_value,
                    'current_value' => $this->current_value,
                    'type' => $this->type,
                    'detail' => $this->type == 'detail' ? json_encode($this->detail) : null,
                ]);
                session()->flash('success', 'Assigned target saved successfully.');
            }

            $this->dispatch('refresh');

        } catch (\Throwable $th) {
            Log::error($th);
            session()->flash('error', 'An error occurred while saving the assigned target.');
        }
    }


    public function mount()
    {
        $this->detail[] = ['name' => '', 'target_value' => '', 'type' => 'number'];
        $this->indicatorTargets = IndicatorTarget::with('indicator')->get();

        $this->organisations = Organisation::get();

        $this->financial_years = FinancialYear::get();
        $this->projects = Project::get();
    }

    public function addDetail()
    {
        $this->detail[] = ['name' => '', 'target_value' => '', 'type' => 'number'];
    }

    public function removeDetail($index)
    {
        if (count($this->detail) > 1) {
            unset($this->detail[$index]);
            $this->detail = array_values($this->detail); // Reindex array
        }
    }
    public function render()
    {
        return view('livewire.admin.data.assigned-targets');
    }
}
