<?php

namespace App\Livewire\Internal\Cip;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\ReportingPeriod;
use App\Models\ReportingPeriodMonth;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

class SubPeriod extends Component
{
    use LivewireAlert;

    public $rowId;
    public $forms = [];

    public $status = true;
    #[Validate('required', as: 'start of submissions')]
    public $start_period;
    #[Validate('required|after:start_period', as: 'end of submissions')]
    public $end_period;
    #[Validate('required', message: 'The form field is required.')]
    public $selectedForm = [];

    public $months = [];
    public $financialYears = [];

    public $projects = [];
    #[Validate('required')]
    public $selectedMonth;
    #[Validate('required', as: 'project year')]
    public $selectedFinancialYear;
    #[Validate('required')]
    public $selectedProject;
    public $expired;

    public $indicators = [];

    #[Validate('required')]
    public $selectedIndicator;

    public $all;
    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->projects = Project::all();
        $this->financialYears = FinancialYear::all();
        $this->months = ReportingPeriodMonth::all();
        $this->indicators = Indicator::all();
        $this->forms = Form::all();
    }

    #[On('editData')]
    public function fillData($rowId)
    {
        $this->resetErrorBag();
        $this->rowId = $rowId;

        $submissionPeriod = SubmissionPeriod::findOrFail($rowId);
        $this->start_period = Carbon::parse($submissionPeriod->date_established)->format('Y-m-d');
        $this->end_period = Carbon::parse($submissionPeriod->date_ending)->format('Y-m-d');
        $this->status = $submissionPeriod->is_open;
        $this->selectedIndicator = $submissionPeriod->indicator_id;
        $this->selectedForm[] = $submissionPeriod->form_id;
        $this->selectedMonth = $submissionPeriod->month_range_period_id;
        $this->selectedFinancialYear = $submissionPeriod->financial_year_id;




        $form = Form::find($submissionPeriod->form_id);
        if ($form) {
            $project = $form->project;
            $this->selectedProject = $project->id;
            $this->updateProjectRelatedData($project);
        }

        $indicator = Indicator::find($this->selectedIndicator);
        if ($indicator) {
            $formIds = $indicator->forms->pluck('id');
            $this->all = $formIds;
            $this->forms = $formIds->isNotEmpty() ? Form::whereIn('id', $formIds)->get() : collect();
            $this->dispatch('changed-form', data: $formIds->toArray(), forms: $this->forms);
        }
    }

    public function updateProjectRelatedData($project)
    {


        $period = ReportingPeriod::findOrFail($project->reporting_period_id);
        $this->months = ReportingPeriodMonth::where('period_id', $period->id)->get();
        $this->financialYears = FinancialYear::where('project_id', $project->id)->get();
        $this->indicators = Indicator::where('project_id', $project->id)->get();

        $this->dispatch('update-indicator', data: $this->indicators, selected: $this->selectedIndicator);
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        $data = [
            'date_established' => $this->start_period,
            'date_ending' => $this->end_period,
            'is_open' => !$this->expired && $this->status,
            'form_id' => $this->selectedForm[0],
            'is_expired' => $this->expired ?? false,
            'month_range_period_id' => $this->selectedMonth,
            'financial_year_id' => $this->selectedFinancialYear,
            'indicator_id' => $this->selectedIndicator,
        ];

        try {
            if ($this->rowId) {
                $submissions = Submission::where('period_id', $this->rowId)->count();
                if ($submissions === 0) {
                    SubmissionPeriod::find($this->rowId)->update($data);
                    session()->flash('success', 'Updated Successfully');
                } else {
                    session()->flash('error', 'Cannot update this record because it has submissions.');
                }
            } else {
                // Check if any existing records have the same criteria and are not expired
                $exists = SubmissionPeriod::where('month_range_period_id', $this->selectedMonth)
                    ->where('financial_year_id', $this->selectedFinancialYear)
                    ->where('indicator_id', $this->selectedIndicator)
                    ->whereIn('form_id', $this->selectedForm)
                    ->where('is_expired', false)
                    ->exists();

                // Check if any form ID in the selected forms is already active
                $activeFormExists = SubmissionPeriod::whereIn('form_id', $this->selectedForm)
                    ->where('is_open', true)
                    ->exists();

                if ($exists) {
                    session()->flash('error', 'This record already exists.');
                } elseif ($activeFormExists) {
                    session()->flash('error', 'One of the selected forms is already active.');
                } else {
                    foreach ($this->selectedForm as $formId) {
                        SubmissionPeriod::create(array_merge($data, ['form_id' => $formId]));
                    }
                    session()->flash('success', 'Created Successfully');
                    return redirect()->to(url()->previous());
                }
            }
        } catch (Throwable $th) {
            dd($th);
            session()->flash('error', 'Something went wrong');
        }
    }

    public function updatedSelectedProject($value)
    {
        $project = Project::find($value);
        if ($project) {
            $this->updateProjectRelatedData($project);
        } else {
            $this->loadData();
        }
    }

    public function updated($property, $value)
    {
        if ($this->selectedProject && $this->selectedIndicator) {
            $indicator = Indicator::find($this->selectedIndicator);
            if ($indicator) {
                $formIds = $indicator->forms->pluck('id');
                $this->all = $formIds;
                $this->forms = $formIds->isNotEmpty() ? Form::whereIn('id', $formIds)->get() : collect();
                $this->dispatch('changed-form', data: $formIds->toArray(), forms: $this->forms);
            }
        }
    }

    public function updatedSelectedIndicator()
    {
        if (!$this->rowId) {
            $this->selectedForm = null;
        }
    }

    public function resetData()
    {
        $this->reset();
        $this->loadData();
        $this->resetErrorBag();
        $this->dispatch('update-indicator');
    }
    public function render()
    {

        return view('livewire.internal.cip.sub-period', [

        ]);
    }
}
