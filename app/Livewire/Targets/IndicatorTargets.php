<?php

namespace App\Livewire\Targets;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\OrganisationTarget;
use App\Models\SubmissionTarget;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;
use Throwable;

class IndicatorTargets extends Component
{
    public string $routePrefix = '';

    // Main form (indicator + project year -> list of disaggregation targets)
    public $selectedIndicator     = '';
    public $selectedFinancialYear = '';
    public array $targets         = [];
    public bool $editingTargets   = false;

    // Modal (partner allocations for one submission target)
    public ?int $selectedSubmissionTarget = null;
    public array $organisationTargets     = [];

    /* ---------------------------------------------------------------
     | Lifecycle
     |---------------------------------------------------------------*/

    public function mount(): void
    {
        $this->routePrefix = Route::current()?->getPrefix() ?? '';
    }

    public function render()
    {
        return view('livewire.targets.indicator-targets');
    }

    /* ---------------------------------------------------------------
     | Computed data (cached per request, never serialised to the browser)
     |---------------------------------------------------------------*/

    #[Computed]
    public function indicators(): Collection
    {
        return Indicator::with(['disaggregations', 'organisation'])
            ->where('is_active', true)
            ->get();
    }

    #[Computed]
    public function financialYears(): Collection
    {
        return FinancialYear::get();
    }

    #[Computed]
    public function disaggregations(): Collection
    {
        if (! $this->selectedIndicator) {
            return collect();
        }

        return $this->indicators
            ->firstWhere('id', (int) $this->selectedIndicator)
            ?->disaggregations ?? collect();
    }

    /* ---------------------------------------------------------------
     | Validation
     |---------------------------------------------------------------*/

    protected function rules(): array
    {
        return [
            'selectedIndicator'     => 'required',
            'selectedFinancialYear' => 'required',
            'targets.*.name'        => 'required|string|distinct',
            'targets.*.value'       => 'required|numeric|min:0',
        ];
    }

    protected function messages(): array
    {
        return [
            'selectedIndicator.required'     => 'Indicator required',
            'selectedFinancialYear.required' => 'Project Year required',
            'targets.*.name.required'        => 'Target name required',
            'targets.*.name.distinct'        => 'Target name must be unique',
            'targets.*.value.required'       => 'Target value required',
            'targets.*.value.numeric'        => 'Target value must be numeric',
            'targets.*.value.min'            => 'Target value must be at least 0',
        ];
    }

    /* ---------------------------------------------------------------
     | Main form: indicator targets
     |---------------------------------------------------------------*/

    public function updatedSelectedIndicator(): void
    {
        $this->resetValidation();
        $this->loadTargetsForSelection();
    }

    public function updatedSelectedFinancialYear(): void
    {
        $this->resetValidation();
        $this->loadTargetsForSelection();
    }

    /** Re-load whatever is saved in the database for the current selection. */
    public function restoreTargets(): void
    {
        $this->validate([
            'selectedIndicator'     => 'required|exists:indicators,id',
            'selectedFinancialYear' => 'required|exists:financial_years,id',
        ]);

        $this->loadTargetsForSelection();
    }

    /** Kept for any other component that dispatches `put-targets`. */
    #[On('put-targets')]
    public function putTargets($indicatorId, $financialYearId): void
    {
        $this->selectedIndicator     = $indicatorId;
        $this->selectedFinancialYear = $financialYearId;

        $this->loadTargetsForSelection();
    }

    public function addTarget(): void
    {
        $this->targets[] = $this->blankTarget();
    }

    public function removeTarget(int $index): void
    {
        unset($this->targets[$index]);
        $this->targets = array_values($this->targets);
    }

    public function save(): void
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            session()->flash('validation_error', 'There are errors in the form.');
            throw $e;
        }

        try {
            DB::transaction(function () {
                foreach ($this->targets as $target) {
                    SubmissionTarget::updateOrCreate(
                        [
                            'financial_year_id' => $this->selectedFinancialYear,
                            'indicator_id'      => $this->selectedIndicator,
                            'target_name'       => $target['name'],
                        ],
                        ['target_value' => $target['value']]
                    );
                }
            });
        } catch (Throwable $e) {
            report($e);
            session()->flash('error', 'Something went wrong');

            return;
        }

        session()->flash('success', 'Targets saved successfully');

        $this->loadTargetsForSelection();
        $this->dispatch('refresh'); // refreshes the PowerGrid table
    }

    private function loadTargetsForSelection(): void
    {
        $this->targets        = [];
        $this->editingTargets = false;

        if (! $this->selectedIndicator || ! $this->selectedFinancialYear) {
            return;
        }

        $saved = SubmissionTarget::query()
            ->where('indicator_id', $this->selectedIndicator)
            ->where('financial_year_id', $this->selectedFinancialYear)
            ->get(['target_name', 'target_value']);

        if ($saved->isEmpty()) {
            $this->targets = [$this->blankTarget()];

            return;
        }

        $this->targets = $saved
            ->map(fn ($t) => ['name' => $t->target_name, 'value' => $t->target_value])
            ->all();

        $this->editingTargets = true;
    }

    private function blankTarget(): array
    {
        return ['name' => null, 'value' => null, 'restricted' => false];
    }

    /* ---------------------------------------------------------------
     | Modal: partner allocations
     |---------------------------------------------------------------*/

    /** Fired by the PowerGrid "view" button. */
    #[On('modify-targets')]
    public function openAssignTargets(int $rowId): void
    {
        $submissionTarget = SubmissionTarget::with(['organisationTargets', 'indicator.organisation'])
            ->findOrFail($rowId);

        $existing = $submissionTarget->organisationTargets->keyBy('organisation_id');

        $this->selectedSubmissionTarget = $submissionTarget->id;

        $this->organisationTargets = ($submissionTarget->indicator->organisation ?? collect())
            ->map(fn ($organisation) => [
                'id'    => $organisation->id,
                'name'  => $organisation->name,
                'value' => $existing->get($organisation->id)?->value ?? 0,
            ])
            ->values()
            ->all();

        $this->resetValidation();
        $this->dispatch('open-targets-modal');
    }

    public function saveTargets(): void
    {
        try {
            $this->validate([
                'organisationTargets.*.value' => 'required|numeric|min:0',
            ], [
                'organisationTargets.*.value.required' => 'Target value required',
                'organisationTargets.*.value.numeric'  => 'Target value must be numeric',
                'organisationTargets.*.value.min'      => 'Target value must be at least 0',
            ]);
        } catch (ValidationException $e) {
            $this->alert('error', 'There are errors in the form.');
            throw $e;
        }

        try {
            DB::transaction(function () {
                foreach ($this->organisationTargets as $target) {
                    OrganisationTarget::updateOrCreate(
                        [
                            'submission_target_id' => $this->selectedSubmissionTarget,
                            'organisation_id'      => $target['id'],
                        ],
                        ['value' => $target['value']]
                    );
                }
            });
        } catch (Throwable $e) {
            report($e);
            $this->alert('error', 'There was an error saving the targets.');

            return;
        }

        $this->alert('success', 'Targets saved successfully.');
        $this->dispatch('targets-saved');
    }

    private function alert(string $type, string $message): void
    {
        // $type: success | error | info | warning
        $this->dispatch('show-alert', data: ['type' => $type, 'message' => $message]);
    }
}
