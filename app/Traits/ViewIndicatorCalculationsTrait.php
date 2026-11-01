<?php

namespace App\Traits;

use App\Models\IndicatorDisaggregation;
use App\Models\PercentageIncreaseIndicator;
use App\Models\SystemReport;
use App\Models\SystemReportData;
use App\Services\ProjectService;
use Illuminate\Support\Collection;

trait ViewIndicatorCalculationsTrait
{
    /** Summed volume/hectare field. */
    protected const TOTAL_KEY = 'Total';

    /** Percentage field — stored once on the 'UNSPECIFIED' period report, never summed. */
    protected const PERCENTAGE_KEY = 'Total (% Percentage)';

    public $rowId;
    public $data = [];
    public $indicator_no;
    public $indicator_id, $project_id;
    public $indicator_name;

    public $total;

    public $selectedProjectYear = [];
    public $projectYears = [];
    public $selectedOrganisation = 1;
    public $reportingPeriods = [];

    public $reporting_period;
    public $financial_year;
    public $organisation;
    public $crop;

    public function calculations(): void
    {
        $organisationId  = $this->organisation['id'];
        $financialYearId = $this->financial_year['id'];
        $projectId       = ProjectService::app_project()->pluck('id')->first();

        // 1. Fetch the relevant reports
        $reportQuery = SystemReport::query()
            ->where('indicator_id', $this->indicator_id)
            ->where('project_id', $projectId)
            ->where('financial_year_id', $financialYearId)
            ->where('crop', $this->crop);

        if ($organisationId != 0) {
            $reportQuery->where('organisation_id', $organisationId);
        }

        $reportIds = $reportQuery->pluck('id');

        if ($reportIds->isEmpty()) {
            $this->data = [];
            $this->total = 0;

            return;
        }

        // 2–3. Raw counts per disaggregation (percentage is taken, not summed)
        $this->data = $this->disaggregationTotals($reportIds);

        // 4. The percentage is not a sum — overwrite it with the pre-calculated
        //    (weighted) growth from the tracker table, when a record exists.
        if (($growth = $this->trackedGrowthPercentage($organisationId)) !== null) {
            $this->data[self::PERCENTAGE_KEY] = $growth;
        }

        // 5. Final assignment
        $this->total = $this->data[self::TOTAL_KEY] ?? 0;
    }

    /**
     * Sum each disaggregation across the matching reports, except the
     * percentage field, which is read as a single stored value.
     */
    protected function disaggregationTotals(Collection $reportIds): array
    {
        $groupedData = SystemReportData::query()
            ->whereIn('system_report_id', $reportIds)
            ->get(['name', 'value'])
            ->groupBy('name');

        return IndicatorDisaggregation::query()
            ->where('indicator_id', $this->indicator_id)
            ->pluck('name')
            ->unique()
            ->mapWithKeys(function ($name) use ($groupedData) {
                $rows = $groupedData->get($name);

                if ($rows === null) {
                    return [$name => 0];
                }

                if ($name === self::PERCENTAGE_KEY) {
                    return [$name => $rows->first()->value ?? 0];
                }

                return [$name => $rows->sum('value')];
            })
            ->all();
    }

    /**
     * Global (organisation 0) rows are stored with a null organisation_id;
     * organisation-specific rows are stored with their own id.
     */
    protected function trackedGrowthPercentage(int $organisationId)
    {
        $query = PercentageIncreaseIndicator::query()
            ->where('indicator_id', $this->indicator_id)
            ->where('financial_year_id', $this->financial_year['id']);

        $organisationId != 0
            ? $query->where('organisation_id', $organisationId)
            : $query->whereNull('organisation_id');

        return optional($query->first())->growth_percentage;
    }

    public function mount(): void
    {
        $this->calculations();
    }
}
