<?php

namespace App\Livewire\Targets;

use Throwable;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\Organisation;
use App\Models\FinancialYear;
use App\Models\SubmissionTarget;
use Livewire\Attributes\Validate;
use App\Models\OrganisationTarget;
use Illuminate\Support\Facades\DB;
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
    public $organisations = [];
    public $organisationTargets = [];
    public $organisationTargetsModel = [];
    public $editingTargets = false;
    public $rowId;
    public $selectedSubmissionTarget;


    protected $rules = [
        'targets.*' => 'required',
        'targets.*.name' => 'required|string|distinct',
        'targets.*.value' => 'required|numeric|min:0',
        'selectedIndicator' => 'required',
        'selectedFinancialYear' => 'required',



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

        $this->targets[] = [
            'name' => null,
            'value' => null,
            'restricted' => false
        ];
    }
    /**
     * Update the row id and get targets
     */

    #[On('update-targets')]
    public function getTargets($value)
    {

        $organisationTargets = OrganisationTarget::with('organisation')
            ->where('submission_target_id', $value['id'])
            ->get();
        $this->selectedSubmissionTarget = $value['id'];

        $financialYear = $value['financial_year']['id'];
        $indicator = $value['indicator']['id'];
        $organisations = Indicator::find($indicator)?->organisation;

        $targetsData = [];

        foreach ($organisations as $organisation) {
            // Default values
            $targetValue = 0;
            $database = false;

            // Check if we have matching organisation targets
            foreach ($organisationTargets as $organisationTarget) {
                if ($organisationTarget->organisation_id == $organisation->id) {
                    $targetValue = $organisationTarget->value; // or whatever field contains the value
                    $database = true;
                    break; // No need to check further if we found a match
                }
            }

            $targetsData[] = [
                'name' => $organisation->name,
                'id' => $organisation->id,
                'value' => $targetValue,
                'database' => $database
            ];
        }

        $this->organisationTargets = $targetsData;
    }

    /**
     * Remove a target from the array
     */
    public function removeTarget($index)
    {

        unset($this->targets[$index]);
        $this->targets = array_values($this->targets); // Reindex the array



    }

    public function saveTargets()
    {
        try {

            $this->validate([
                'organisationTargets.*.value' => 'required|numeric|min:0'
            ], [
                'organisationTargets.*.value.numeric' => 'Target value must be numeric',
                'organisationTargets.*.value.min' => 'Target value must be at least 0',
                'organisationTargets.*.value.required' => 'Target value required'


            ]);
        } catch (Throwable $e) {
            $this->dispatch('show-alert', data: [
                'type' => 'error', // success, error, info, warning
                'message' => 'There are errors in the form.'
            ]);
            throw $e;
        }


        try {
            DB::beginTransaction();
            foreach ($this->organisationTargets as $target) {

                $organisationTarget = OrganisationTarget::where('submission_target_id', $this->selectedSubmissionTarget)
                    ->where('organisation_id', $target['id'])
                    ->first();

                if ($organisationTarget) {
                    $organisationTarget->update([
                        'value' => $target['value']
                    ]);
                } else {
                    OrganisationTarget::create([
                        'submission_target_id' => $this->selectedSubmissionTarget,
                        'organisation_id' => $target['id'],
                        'value' => $target['value']
                    ]);
                }
            }

            DB::commit();
            $this->dispatch('show-alert', data: [
                'type' => 'success', // success, error, info, warning
                'message' => 'Targets saved successfully.'
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            $this->dispatch('show-alert', data: [
                'type' => 'error', // success, error, info, warning
                'message' => 'There was an error saving the targets.'
            ]);
        }
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

            $submissionTargets = SubmissionTarget::where('financial_year_id', $this->selectedFinancialYear)
                ->where('indicator_id', $this->selectedIndicator)
                ->get();

            foreach ($this->targets as $target) {

                if ($target['restricted']) {
                    $submissionTarget = SubmissionTarget::where('financial_year_id', $this->selectedFinancialYear)
                        ->where('indicator_id', $this->selectedIndicator)
                        ->where('target_name', $target['name'])
                        ->first();

                    if ($submissionTarget) {
                        $submissionTarget->update([
                            'target_value' => $target['value']
                        ]);
                    }
                } else {
                    SubmissionTarget::create([
                        'financial_year_id' => $this->selectedFinancialYear,
                        'indicator_id' => $this->selectedIndicator,
                        'target_name' => $target['name'],
                        'target_value' => $target['value']
                    ]);
                }
                $this->targets = [];
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

            $this->disaggregations =  $this->indicators->where('id', $this->selectedIndicator)->first()->disaggregations->flatten();
            // dd($this->disaggregations);
            $this->organisations = Indicator::find($this->selectedIndicator)->organisation ?? collect([]);
            $submissionTargets = SubmissionTarget::with('organisationTargets')->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->get();


            if ($submissionTargets->count() > 0) {
                $this->targets = [];
                foreach ($submissionTargets as $target) {
                    $this->targets[] = [
                        'name' => $target->target_name,
                        'value' => $target->target_value,
                        'restricted' => true
                    ];
                }
                $this->editingTargets = true;
            } else {
                $this->targets = [
                    [
                        'name' => null,
                        'value' => null,
                        'restricted' => false
                    ]
                ];
                $this->editingTargets = false;
            }
        }
    }

    public function oldTargets($items)
    {

        $submissionTarget = SubmissionTarget::with('organisationTargets')->where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $items['selectedFinancialYear'])
            ->where('target_name', $items['mainTarget']['name'])
            ->where('target_value', $items['mainTarget']['value'])
            ->first();

        if ($submissionTarget) {
            $organisationTargets = $submissionTarget->organisationTargets;
            $getOrganisationTargetValue = $organisationTargets->where('organisation_id', $items['organisationTarget']['organisation_id'])->first();
            return $getOrganisationTargetValue->value ?? 0;
        }

        return 0;
    }

    public function render()
    {
        return view('livewire.targets.submission-targets');
    }
}