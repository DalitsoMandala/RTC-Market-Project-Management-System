<?php

namespace App\Livewire\Targets;

use Throwable;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Indicator;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ProgresSummaryExport;
use Illuminate\Support\Facades\Route;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Exports\AttendanceImport\AttendanceRegistersExport;

class SubmissionTargets extends Component
{
    use LivewireAlert;
    public $routePrefix;
    public $indicators;
    public $selectedIndicator;
    public $disaggregations = [];
    public $financialYears = [];
    public $selectedDisaggregation;
    public $selectedFinancialYear;
    public $targets = [];

    protected $rules = [
        'targets.*' => 'required',
        'targets.*.name' => 'required|string|distinct',
        'targets.*.value' => 'required|numeric|min:0',
        'selectedIndicator' => 'required',
        'selectedFinancialYear' => 'required'

    ];


    protected $messages = [
        'targets.*.name.required' => 'Target name required',
        'targets.*.value.required' => 'Target value required',
        'targets.*.value.numeric' => 'Target value must be numeric',
        'targets.*.name.distinct' => 'Target name must be unique',
        'targets.*.value.min' => 'Target value must be at least 0',
        'selectedIndicator.required' => 'Indicator required',
        'selectedFinancialYear.required' => 'Project Year required'



    ];
    public function addTarget()
    {

        $this->targets->push([
            'name' => null,
            'value' => null
        ]);
    }

    /**
     * Remove a target from the array
     */
    public function removeTarget($index)
    {
        //  unset($this->targets[$index]);
        //  $this->targets = array_values($this->targets); // Reindex the array
        $this->targets = $this->targets->forget($index)->values();
    }

    public function save()
    {


        try {
            $this->validate();
        } catch (Throwable $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }



        try {

            SubmissionTarget::where('financial_year_id', $this->selectedFinancialYear)
                ->where('indicator_id', $this->selectedIndicator)
                ->delete();

            foreach ($this->targets as $target) {
                SubmissionTarget::create([
                    'financial_year_id' => $this->selectedFinancialYear,
                    'indicator_id' => $this->selectedIndicator,
                    'target_name' => $target['name'],
                    'target_value' => $target['value']
                ]);
            }
            session()->flash('success', 'Targets saved successfully');


            $this->dispatch('refresh');
        } catch (\Throwable $e) {
            # code...
            session()->flash('error', 'Something went wrong');
            Log::error($e->getMessage());
        }
    }

    public function load()
    {
        $this->reset();
        $this->routePrefix = Route::current()->getPrefix();
        $this->indicators = Indicator::with('disaggregations')->get();
        $this->disaggregations = IndicatorDisaggregation::get();
        $this->financialYears = FinancialYear::get();
        $this->targets = collect([]);
    }

    public function mount()
    {
        $this->load();
    }




    public function updatedSelectedIndicator($value) {}

    #[On('update-targets')]

    public function putTargets()
    {
        if ($this->selectedIndicator && $this->selectedFinancialYear) {
            $this->targets = collect([]);
            $Submissiontargets = SubmissionTarget::where('financial_year_id', $this->selectedFinancialYear)
                ->where('indicator_id', $this->selectedIndicator)
                ->get();
            foreach ($Submissiontargets as $Submissiontarget) {

                $formatted = number_format($Submissiontarget->target_value, 2, '.', '');

                // Remove trailing .00 if necessary
                if (strpos($formatted, '.00') !== false) {
                    $formatted = rtrim($formatted, '0');
                    $formatted = rtrim($formatted, '.');
                }

                $this->targets->push([
                    'name' => $Submissiontarget->target_name,
                    'value' => $formatted
                ]);

                $this->disaggregations = collect();

                $this->disaggregations =  $this->indicators->where('id', $this->selectedIndicator)->first()->disaggregations;
            }
        }
    }

    public function render()
    {
        return view('livewire.targets.submission-targets');
    }
}