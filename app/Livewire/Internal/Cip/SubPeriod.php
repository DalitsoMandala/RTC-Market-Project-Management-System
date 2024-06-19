<?php

namespace App\Livewire\Internal\Cip;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\ReportingPeriod;
use App\Models\ReportingPeriodMonth;
use App\Models\SubmissionPeriod;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class SubPeriod extends Component
{
    use LivewireAlert;

    public $rowId;
    public $forms = [];

    public $status = true;
    #[Validate('required')]
    public $start_period;
    #[Validate('required')]
    public $end_period;
    #[Validate('required', message: 'The form field is required.')]
    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];
    #[Validate('required')]
    public $selectedMonth;
    #[Validate('required')]
    public $selectedFinancialYear;
    #[Validate('required')]
    public $selectedProject;
    public $expired;

    public $indicators;

    #[Validate('required')]
    public $selectedIndicator;
    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {

        $this->validate();
        $this->resetErrorBag();
        try {

            if ($this->rowId) {

                SubmissionPeriod::find($this->rowId)->update([
                    'date_established' => $this->start_period,
                    'date_ending' => $this->end_period,
                    'is_open' => $this->expired === true ? false : $this->status,
                    'form_id' => $this->selectedForm,
                    'is_expired' => $this->expired ?? false,
                    'month_range_period_id' => $this->selectedMonth,
                    'financial_year_id' => $this->selectedFinancialYear,
                    'indicator_id' => $this->selectedIndicator,

                ]);
                session()->flash('success', 'Updated Successfully');

            } else {

                $find = SubmissionPeriod::where('form_id', $this->selectedForm)->where('month_range_period_id', $this->selectedMonth)
                    ->where('financial_year_id', $this->selectedFinancialYear)
                    ->where('indicator_id', $this->selectedIndicator)
                    ->where('is_open', true)->first();

                if ($find) {

                    session()->flash('error', 'This record already exists see the table below!');

                } else {

                    SubmissionPeriod::create([
                        'date_established' => $this->start_period,
                        'date_ending' => $this->end_period,
                        'is_open' => $this->status,
                        'form_id' => $this->selectedForm,
                        'month_range_period_id' => $this->selectedMonth,
                        'financial_year_id' => $this->selectedFinancialYear,
                        'indicator_id' => $this->selectedIndicator,
                    ]);
                    session()->flash('success', 'Created Successfully');

                }

            }

            $this->dispatch('refresh');

        } catch (\Throwable $th) {

            session()->flash('error', 'something went wrong');

        }
        $this->reset();
        $this->loadData();
    }

    public function loadData()
    {

        $this->forms = Form::get();
        $this->projects = Project::get();
        $this->financialYears = FinancialYear::get();
        $this->months = ReportingPeriodMonth::get();
        $this->indicators = Indicator::get();
    }
    #[On('editData')]
    public function fillData($rowId)
    {

        $this->resetErrorBag();

        $this->rowId = $rowId;
        $monthRange = SubmissionPeriod::find($rowId)->month_range_period_id;
        $this->start_period = Carbon::parse(SubmissionPeriod::find($rowId)->date_established)->format('Y-m-d');
        $this->end_period = Carbon::parse(SubmissionPeriod::find($rowId)->date_ending)->format('Y-m-d');
        $this->status = SubmissionPeriod::find($rowId)->is_open === 1 ? true : false;
        $this->selectedIndicator = SubmissionPeriod::find($rowId)->indicator_id;

        $this->dispatch('update-indicator', $this->selectedIndicator);
        $form = SubmissionPeriod::find($rowId)->form_id;
        $financialYear = SubmissionPeriod::find($rowId)->financial_year_id;
        if ($form) {

            $formDetails = Form::find($form);

            if ($formDetails) {
                $project = $formDetails->project->id;

                $this->selectedProject = $project;
                $this->selectedForm = $form;
                $project = Project::find($this->selectedProject);

                if ($project) {
                    $period = ReportingPeriod::where('id', $project->reporting_period_id)->first();
                    $this->months = $this->months->where('period_id', $period->id);

                }

                if ($project) {
                    $this->financialYears = $this->financialYears->where('project_id', $project->id);

                }
                $this->selectedMonth = $monthRange;
                $this->selectedFinancialYear = $financialYear;
            }
        }

    }

    public function updatedselectedProject($value)
    {

        $forms = Form::where('project_id', $value)->get();

        if ($forms) {
            $this->forms = $forms;
        } else {
            $this->forms = [];
        }

        $project = Project::find($value);

        if ($project) {
            $period = ReportingPeriod::where('id', $project->reporting_period_id)->first();

            $this->months = $this->months->where('period_id', $period->id);

        }

        if ($project) {
            $this->financialYears = $this->financialYears->where('project_id', $project->id);

        }

    }

    public function resetData()
    {
        $this->reset();
        $this->loadData();

    }

    public function mount()
    {
        $this->loadData();

    }
    public function render()
    {

        return view('livewire.internal.cip.sub-period', [

        ]);
    }
}
