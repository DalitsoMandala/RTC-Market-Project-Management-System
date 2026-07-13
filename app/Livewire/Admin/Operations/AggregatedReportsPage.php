<?php
namespace App\Livewire\Admin\Operations;

use App\Models\AggregatedReport;
use App\Models\AggregatedReportData;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\User;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithPagination;

class AggregatedReportsPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // ── Table filters ─────────────────────────────────────────────────────────
    public string $search                = '';
    public string $filterIndicator       = '';
    public string $filterOrganisation    = '';
    public string $filterFinancialYear   = '';
    public string $filterReportingPeriod = '';
    public string $filterDisaggregation  = '';
    public string $filterCrop            = '';

    // ── Sorting ───────────────────────────────────────────────────────────────
    public string $sortField     = 'id';
    public string $sortDirection = 'desc';
    public int $perPage          = 25;

    // ── Inline edit (value only) ──────────────────────────────────────────────
    public ?int $editingId  = null;
    public mixed $editValue = null;

    // ── Create form ───────────────────────────────────────────────────────────
    public bool $showCreateForm          = false;
    public string $formIndicatorId       = '';
    public string $formDisaggregationId  = '';
    public string $formOrganisationId    = '';
    public string $formFinancialYearId   = '';
    public string $formReportingPeriodId = '';
    public string $formCrop              = '';
    public mixed $formValue              = 0;

    // Holds a duplicate record found during submission
    public ?int $duplicateId = null;
    public ?int $deleteId    = null;

    // ── Validation rules ──────────────────────────────────────────────────────
    protected function formRules(): array
    {
        return [
            'formIndicatorId'       => 'required|exists:indicators,id',
            'formDisaggregationId'  => 'required|exists:indicator_disaggregations,id',
            'formOrganisationId'    => 'required|exists:organisations,id',
            'formFinancialYearId'   => 'required|exists:financial_years,id',
            'formReportingPeriodId' => 'required|exists:reporting_period_months,id',
            'formCrop'              => 'nullable|string|max:100',
            'formValue'             => 'required|numeric|min:0',
        ];
    }

    protected function formMessages(): array
    {
        return [
            'formIndicatorId.required'       => 'Please select an indicator.',
            'formDisaggregationId.required'  => 'Please select a disaggregation.',
            'formOrganisationId.required'    => 'Please select an organisation.',
            'formFinancialYearId.required'   => 'Please select a financial year.',
            'formReportingPeriodId.required' => 'Please select a reporting period.',
            'formValue.required'             => 'Value is required.',
            'formValue.numeric'              => 'Value must be a number.',
        ];
    }

    // ── Lifecycle ─────────────────────────────────────────────────────────────
    public function updatingSearch()
    {
        $this->resetPage();
    }
    public function updatingFilterIndicator()
    {
        $this->resetPage();
    }
    public function updatingFilterOrganisation()
    {
        $this->resetPage();
    }
    public function updatingFilterFinancialYear()
    {
        $this->resetPage();
    }
    public function updatingFilterReportingPeriod()
    {
        $this->resetPage();
    }
    public function updatingFilterDisaggregation()
    {
        $this->resetPage();
    }
    public function updatingFilterCrop()
    {
        $this->resetPage();
    }

    public function updatedPage()
    {
        $this->dispatch('scroll-to-table');
    }

    // ── Sorting ───────────────────────────────────────────────────────────────
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField     = $field;
            $this->sortDirection = 'asc';
        }
    }

    // ── Filter helpers ────────────────────────────────────────────────────────
    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'filterIndicator',
            'filterOrganisation',
            'filterFinancialYear',
            'filterReportingPeriod',
            'filterDisaggregation',
            'filterCrop',
        ]);
        $this->resetPage();
    }

    // ── Inline edit ───────────────────────────────────────────────────────────
    public function startEdit(int $id): void
    {
        $this->editingId = $id;
        $this->editValue = AggregatedReportData::findOrFail($id)->value;
    }

    public function cancelEdit(): void
    {
        $this->editingId = null;
        $this->editValue = null;
        $this->resetErrorBag();
    }

    // ── Create form ───────────────────────────────────────────────────────────
    public function toggleCreateForm(): void
    {
        $this->showCreateForm = ! $this->showCreateForm;
        $this->resetCreateForm();
    }

    public function submitCreate(): void
    {
        $this->validate($this->formRules(), $this->formMessages());

        // Duplicate check — same combination already exists?
        $existing = AggregatedReport::where([
            'indicator_id'        => $this->formIndicatorId,
            'organisation_id'     => $this->formOrganisationId,
            'financial_year_id'   => $this->formFinancialYearId,
            'reporting_period_id' => $this->formReportingPeriodId,
        ])->when($this->formCrop, fn($q) => $q->where('crop', $this->formCrop))
            ->first();

        if ($existing) {
            $this->duplicateId = $existing->id;
            return; // blade will show the duplicate warning
        }

        $record = AggregatedReport::create([
            'indicator_id'        => $this->formIndicatorId,
            'organisation_id'     => $this->formOrganisationId,
            'financial_year_id'   => $this->formFinancialYearId,
            'reporting_period_id' => $this->formReportingPeriodId,
            'crop'                => $this->formCrop ?: null,
        ]);

        AggregatedReportData::create([
            'aggregated_report_id' => $record->id,
            'value'                => $this->formValue,
        ]);

        $this->resetCreateForm();
        $this->showCreateForm = false;
        session()->flash('success', 'Record created successfully.');
    }

    /**
     * Jump to the duplicate row in the table so the user can edit it.
     * Sets all filters to surface that record and triggers the edit mode.
     */
    public function jumpToDuplicate(): void
    {
        if (! $this->duplicateId) {
            return;
        }

        $record = AggregatedReport::findOrFail($this->duplicateId);

        // Apply filters to surface just this row
        $this->filterIndicator       = (string) $record->indicator_id;
        $this->filterOrganisation    = (string) $record->organisation_id;
        $this->filterFinancialYear   = (string) $record->financial_year_id;
        $this->filterReportingPeriod = (string) $record->reporting_period_id;
        $this->filterDisaggregation  = $record->indicatorDisaggregation->name ?? '';
        $this->filterCrop            = $record->crop ?? '';

        $this->resetPage();
        $this->showCreateForm = false;
        $this->duplicateId    = null;
        $this->resetCreateForm();

        // Auto-open edit mode for that row after re-render
        $this->startEdit($record->id);

        $this->dispatch('scroll-to-table');
    }

    private function resetCreateForm(): void
    {
        $this->reset([
            'formIndicatorId',
            'formDisaggregationId',
            'formOrganisationId',
            'formFinancialYearId',
            'formReportingPeriodId',
            'formCrop',
            'formValue',
        ]);
        $this->formValue   = 0;
        $this->duplicateId = null;
        $this->resetErrorBag();
    }

    // ── Delete ────────────────────────────────────────────────────────────────
    public function deleteRecord(int $id): void
    {
        AggregatedReport::findOrFail($id)->delete();

        // If we happened to be editing this row, clean that state up
        if ($this->editingId === $id) {
            $this->cancelEdit();
        }

        session()->flash('success', 'Record deleted successfully.');
    }

    // ── REPLACE requestSave() ─────────────────────────────────────────────────────
    // Old version dispatched 'open-confirm-modal' as a window event.
    // New version dispatches 'showModal' with the modal id, matching your x-modal pattern.

    public function requestSave(): void
    {
        $this->validate(['editValue' => 'required|numeric|min:0']);
        $this->dispatch('showModal', name: 'confirm-save-modal');
    }

    // ── REPLACE confirmSave() ─────────────────────────────────────────────────────
    // Now hides the modal via 'hideModal' after saving.

    public function confirmSave(): void
    {
        $this->validate([
            'editValue' => 'required|numeric|min:0',
        ]);

        $reportData = AggregatedReportData::findOrFail($this->editingId);
        $report     = AggregatedReport::findOrFail($reportData->aggregated_report_id);

        // Always update the edited record
        $reportData->update([
            'value' => $this->editValue,
        ]);

        // If this is a crop-specific value, update all matching crop records
        if ($report->crop) {
            AggregatedReport::where([
                'indicator_id'        => $report->indicator_id,
                'organisation_id'     => $report->organisation_id,
                'financial_year_id'   => $report->financial_year_id,
                'reporting_period_id' => $report->reporting_period_id,
                'crop'                => $report->crop,
            ])
                ->first()?->data()
                ->where('name', $report->crop)
                ->update([
                    'value' => $this->editValue,
                ]);
        }

        $this->cancelEdit();
        $this->dispatch('hideModal', name: 'confirm-save-modal');
        session()->flash('success', 'Value updated successfully.');
    }

    // ── ADD requestDelete() ───────────────────────────────────────────────────────
    // Called by the trash button. Stores the id and opens the delete modal.

    public function requestDelete(int $id): void
    {
        $this->deleteId = $id; // add  public ?int $deleteId = null;  to your properties
        $this->dispatch('showModal', name: 'confirm-delete-modal');
    }

    // ── REPLACE deleteRecord() ────────────────────────────────────────────────────
    // Now uses $this->deleteId (set by requestDelete) instead of receiving the id
    // from Alpine, and hides the modal after deletion.

    public function confirmDelete(): void
    {
        if (! $this->deleteId) {
            return;
        }

        AggregatedReportData::findOrFail($this->deleteId)->delete();

        if ($this->editingId === $this->deleteId) {
            $this->cancelEdit();
        }

        $this->deleteId = null;
        $this->dispatch('hideModal', name: 'confirm-delete-modal');
        session()->flash('success', 'Record deleted successfully.');
    }

    // ── ADD to your class properties ─────────────────────────────────────────────
    // public ?int $deleteId = null;

    // ─── Render ───────────────────────────────────────────────────────────────
    public function render()
    {
        $query = AggregatedReportData::with([
            'systemReport',
            'systemReport.indicator',
            'systemReport.reportingPeriod',
            'systemReport.financialYear',
            'systemReport.organisation',
            'systemReport.project',
        ])->whereHas('systemReport.project', fn($q) => $q->where('name', 'RTC MARKET'))->join('aggregated_reports', 'aggregated_report_data.aggregated_report_id', '=', 'aggregated_reports.id')
            ->select('aggregated_report_data.*', 'aggregated_reports.indicator_id', 'aggregated_reports.organisation_id', 'aggregated_reports.financial_year_id', 'aggregated_reports.reporting_period_id', 'aggregated_reports.crop');

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas(
                    'indicator',
                    fn($i) =>
                    $i->where('indicator_name', 'like', "%{$this->search}%")
                        ->orWhere('indicator_no', 'like', "%{$this->search}%")
                )->orWhereHas(
                    'organisation',
                    fn($o) =>
                    $o->where('name', 'like', "%{$this->search}%")
                );
            });
        }

        // Filters
        if ($this->filterIndicator) {
            $query->where('indicator_id', $this->filterIndicator);
        }

        if ($this->filterOrganisation) {
            $query->where('organisation_id', $this->filterOrganisation);
        }

        if ($this->filterFinancialYear) {
            $query->where('financial_year_id', $this->filterFinancialYear);
        }

        if ($this->filterReportingPeriod) {
            $query->where('reporting_period_id', $this->filterReportingPeriod);
        }

        if ($this->filterDisaggregation) {
            $query->where('name', $this->filterDisaggregation);
        }

        if ($this->filterCrop) {
            if ($this->filterCrop === 'null') {
                $query->whereNull('crop');
            } else {
                $query->where('crop', $this->filterCrop);
            }
        }

        // Sort
        $sortMap = [
            'organisation'   => fn($q, $d)   => $q
                ->leftJoin('organisations', 'aggregated_reports.organisation_id', '=', 'organisations.id')
                ->orderBy('organisations.name', $d),

            'financial_year' => fn($q, $d) => $q
                ->leftJoin('financial_years', 'aggregated_reports.financial_year_id', '=', 'financial_years.id')
                ->orderBy('financial_years.number', $d),

            'crop'           => fn($q, $d)           => $q->orderBy('aggregated_reports.crop', $d),
            'value'          => fn($q, $d)          => $q->orderBy('aggregated_report_data.value', $d), // qualify — ambiguous otherwise
        ];

        if (isset($sortMap[$this->sortField])) {

            $sortMap[$this->sortField]($query, $this->sortDirection);
        } else {
            $query->latest();
        }

        $rows = $query->paginate($this->perPage);

        return view('livewire.admin.operations.aggregated-reports-page', [
            'rows'                 => $rows,
            'indicators'           => Indicator::orderBy('indicator_no')->get(),
            'disaggregations'      => IndicatorDisaggregation::distinct('name')->orderBy('name')->pluck('name'),
            'organisations'        => Organisation::orderBy('name')->get(),
            'financialYears'       => FinancialYear::orderBy('number')->get(),
            'reportingPeriods'     => ReportingPeriodMonth::with('reportingPeriod')->orderBy('start_month')->get()
                ->map(fn($rp) => [
                    'id'   => $rp->id,
                    'name' => $rp->start_month . '–' . $rp->end_month,
                ])->toArray(),
            // For create form dropdowns (full models needed)
            'disaggregationModels' => IndicatorDisaggregation::orderBy('name')->get()->unique('name'),
        ]);
    }

}
