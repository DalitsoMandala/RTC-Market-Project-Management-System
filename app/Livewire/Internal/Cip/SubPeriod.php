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
    #[Validate('required')]
    public $start_period;
    #[Validate('required')]
    public $end_period;
    #[Validate('required', message: 'The form field is required.')]
    public $selectedForm = [];

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

    public $indicators = [];

    #[Validate('required')]
    public $selectedIndicator;
    public function setData($id)
    {
        $this->resetErrorBag();

    }

    public function save()
    {
        try {
            $this->validate();
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        $this->resetErrorBag();
        try {

            if ($this->rowId) {
                $submissions = Submission::where('period_id', $this->rowId)->count();
                if ($submissions === 0) {

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
                    session()->flash('error', 'Sorry you can not update this record right now because it has submissions.');
                }

            } else {

                $exists = SubmissionPeriod::where('month_range_period_id', $this->selectedMonth)
                    ->where('financial_year_id', $this->selectedFinancialYear)
                    ->where('indicator_id', $this->selectedIndicator)
                    ->whereIn('form_id', $this->selectedForm)
                    ->where('is_expired', false)
                    ->exists();

                if ($exists) {
                    session()->flash('error', 'This record already exists see the table below!');
                } else {

                    $find = SubmissionPeriod::whereIn('form_id', $this->selectedForm)->where('month_range_period_id', $this->selectedMonth)
                        ->where('financial_year_id', $this->selectedFinancialYear)
                        ->where('indicator_id', $this->selectedIndicator)
                        ->where('is_open', true)->first();

                    if ($find) {

                        session()->flash('error', 'This record already exists see the table below!');

                    } else {

                        foreach ($this->selectedForm as $formId) {
                            SubmissionPeriod::create([
                                'date_established' => $this->start_period,
                                'date_ending' => $this->end_period,
                                'is_open' => $this->status,
                                'form_id' => $formId,
                                'month_range_period_id' => $this->selectedMonth,
                                'financial_year_id' => $this->selectedFinancialYear,
                                'indicator_id' => $this->selectedIndicator,
                            ]);
                        }

                        session()->flash('success', 'Created Successfully');
                        return redirect()->to(url()->previous());
                    }
                }

            }



        } catch (Throwable $th) {

            session()->flash('error', 'something went wrong');

        }

    }

    public function loadData()
    {

        $this->projects = Project::get();
        $this->financialYears = FinancialYear::get();
        $this->months = ReportingPeriodMonth::get();
        $this->indicators = Indicator::get();
        $this->forms = Form::get();
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

        $form = SubmissionPeriod::find($rowId)->form_id;
        $financialYear = SubmissionPeriod::find($rowId)->financial_year_id;
        if ($form) {

            $formDetails = Form::find($form);

            if ($formDetails) {
                $project = $formDetails->project->id;

                $this->selectedProject = $project;

                $project = Project::find($this->selectedProject);

                if ($project) {

                    $period = ReportingPeriod::where('id', $project->reporting_period_id)->first();
                    $this->months = $this->months->where('period_id', $period->id);

                    $this->financialYears = $this->financialYears->where('project_id', $project->id);
                    $indicators = Indicator::where('project_id', $project->id)->get();
                    $this->dispatch('select-indicator', data: $indicators, selected: $this->selectedIndicator);

                }
                $this->selectedMonth = $monthRange;
                $this->selectedFinancialYear = $financialYear;
                $indicator = Indicator::find($this->selectedIndicator);

                if ($indicator) {
                    // Get the IDs of the forms associated with the indicator
                    $formIds = $indicator->forms->pluck('id');

                    // Assign the form IDs to the class property $this->all
                    $this->all = $formIds;

                    if ($formIds->isNotEmpty()) {

                        $this->forms = Form::get();

                        $this->forms = $this->forms->whereIn('id', $formIds);

                    } else {
                        // Handle empty formIds, reset or clear $this->forms as needed
                        $this->forms = collect(); // Or handle as appropriate
                    }
                }
                $this->selectedForm = $form;

            }
        }

    }

    public function updatedselectedProject($value)
    {

        $project = Project::find($value);

        if ($project) {

            $period = ReportingPeriod::where('id', $project->reporting_period_id)->first();

            $this->months = $this->months->where('period_id', $period->id);
            $today = Carbon::today();
            $currentMonthYear = $today->format('Y-m');

            $this->financialYears = $this->financialYears->filter(function ($financialYear) use ($currentMonthYear) {
                $startMonthYear = Carbon::parse($financialYear->start_date)->format('Y-m');
                $endMonthYear = Carbon::parse($financialYear->end_date)->format('Y-m');

                return $currentMonthYear >= $startMonthYear && $currentMonthYear <= $endMonthYear;
            });

            $indicators = Indicator::where('project_id', $value)->get();
            $this->dispatch('update-indicator', data: $indicators, selected: null);

        } else {

            $this->loadData();
        }

    }

    public $all;
    public function updated($property, $value)
    {
        if ($this->selectedProject && $this->selectedIndicator) {
            // Fetch the indicator by its ID
            $indicator = Indicator::find($this->selectedIndicator);

            if ($indicator) {
                // Get the IDs of the forms associated with the indicator
                $formIds = $indicator->forms->pluck('id');

                // Assign the form IDs to the class property $this->all
                $this->all = $formIds;

                if ($formIds->isNotEmpty()) {

                    $this->forms = Form::whereIn('id', $formIds)->get()->toArray();
                    $selectedForm = $formIds->toArray();
                    // $this->selectedForm = $formIds->toArray();

                } else {
                    // Handle empty formIds, reset or clear $this->forms as needed
                    $this->forms = collect(); // Or handle as appropriate
                }

                $this->dispatch('changed-form', data: $selectedForm, forms: $this->forms);
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
        $this->dispatch('update-indicator');
        $this->resetErrorBag();

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