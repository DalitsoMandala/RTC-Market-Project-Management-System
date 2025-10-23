<?php

namespace App\Exports\Reports;

use App\Models\Project;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SubmissionTarget;
use App\Models\SystemReportData;
use Illuminate\Support\Facades\Log;
use App\Models\IndicatorDisaggregation;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Database\Eloquent\Builder;


class ReportExport extends ReportExportTemplate  implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize, WithMapping
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $sheetName;
    public $type;
    public $crop;
    public array $data;
    private $lastIndicator = null;
    public function __construct($sheetName, $type = 'crop', array $data = [])
    {

        $this->sheetName = $sheetName;
    }
    public function collection()
    {
        //
        return IndicatorDisaggregation::with([
            'indicator',
            'indicator.baseline',
            'indicator.baseline.baselineMultiple',
            'indicator.submissionTargets'
        ])

            ->orderBy('indicator_id')
            ->get();
    }

    private function getYearlyTargets(
        $disaggregation = null,
        $indicatorName = null
    ) {

        $yearTargets = [
            'Year1' => null,
            'Year2' => null,
            'Year3' => null,
            'Year4' => null
        ];
        foreach (FinancialYear::all() as $year) {
            $financialYear = $year->number;
            $financialYearId = FinancialYear::where('number', $financialYear)->first()->id;
            $indicatorNameId = Indicator::where('indicator_name', $indicatorName)->first()->id;
            $target = SubmissionTarget::where([
                'target_name' => $disaggregation,
                'financial_year_id' => $financialYearId,
                'indicator_id' => $indicatorNameId,

            ])->first();

            $yearTargets['Year' . $financialYear] = $target->target_value ?? null;
        }

        return $yearTargets;
    }


    public function applyFilters($reporting_period_id = null, $financial_year_id = null, $organisation_id = null, $enterprise = null, $indicator_id = null): Builder
    {
        $project = Project::where('name', 'LIKE', '%RTC MARKET%')->first();
        $query = SystemReport::with('data')
            ->where('indicator_id', $indicator_id)
            ->where('project_id', $project->id)
            ->when($reporting_period_id, fn($q) => $q->where('reporting_period_id', $reporting_period_id))
            ->when($financial_year_id, fn($q) => $q->where('financial_year_id', $financial_year_id))
            ->when($organisation_id, fn($q) => $q->where('organisation_id', $organisation_id))
            ->when($enterprise, function ($q) use ($enterprise) {
                if ($enterprise === 'All') {
                    return $q->whereNull('crop');
                } else {
                    return $q->where('crop', $enterprise);
                }
            });
        return $query;
    }
    public function getYearlyValues($crop = null, $organisationId = null, $financialYearId,  $indicatorId)
    {



        $crop = $crop ?? 'All';

        $reportIds = $this->applyFilters(null, $financialYearId, $organisationId, $crop, $indicatorId);

        $reportIds = $reportIds->pluck('id');

        $reportData = SystemReportData::whereIn('system_report_id', $reportIds)->get();
        $groupedData = $reportData->groupBy('name');
        $disaggregationNames = IndicatorDisaggregation::where('indicator_id', $indicatorId)->pluck('name')->unique();
        $data = $disaggregationNames->mapWithKeys(fn($name) => [$name => $groupedData->has($name) ? $groupedData[$name]->sum('value') : null]);



        return  $data->toArray();
    }
    public function map($item): array
    {
        $indicatorNo = '';
        $indicatorName = '';
        $baselineValue = '';


        // 👇 Only print indicator info once per group
        if ($item->indicator?->indicator_name !== $this->lastIndicator) {
            $indicatorNo = $item->indicator?->indicator_no;
            $indicatorName = $item->indicator?->indicator_name;
            $this->lastIndicator = $item->indicator?->indicator_name;
        }

        // 👇 Handle baseline (single or multiple)
        $baseline = $item->indicator?->baseline;

        if ($baseline) {
            // if multiple baselines
            if ($baseline->baseline_is_multiple == 1) {
                // find matching baseline using disaggregation name
                $match = $baseline->baselineMultiple
                    ->firstWhere('name', $item->name);

                $baselineValue = $match?->baseline_value ?? '';
            } else {
                // single baseline: only show once (on first disaggregation)
                if ($indicatorNo !== '') { // means this is the first row for that indicator
                    $baselineValue = $baseline->baseline_value;
                } else {
                    $baselineValue = ''; // subsequent rows are blank
                }
            }
        }




        switch ($this->sheetName) {
            case 'Consolidated':
                return $this->consolidatedData(
                    $item,
                    $indicatorNo,
                    $indicatorName,
                    $baselineValue,
                    [
                        'crop' => 'All',
                        'organisationId' => null,
                        'financialYearId' => null

                    ]
                );
                break;
            case 'Sweet potato':

                break;
            case 'Cassava':

                break;
            case 'Potato':

                break;
            case 'IITA':

                break;
            case 'ACE':

                break;
            case 'DCD':

                break;
            case 'DAES':
                break;
            case 'MINISTRY OF TRADE':

                break;
            case 'TRADELINE':

                break;

            case 'DARS':

                break;

            case 'RTCDT':

                break;
            case 'LUANAR':

                break;

            default:
                return [];
                break;
        }
        return [];
    }

    public function calculatePercentage($numerator, $denominator): float
    {
        $numerator = (float)$numerator;
        $denominator = (float)$denominator;

        if ($denominator == 0) {
            return 0.0;
        }

        return round(($numerator / $denominator) * 100, 2);
    }

    public function populateYearlyValues($crop = null, $organisationId = null, $indicatorId)
    {
        $getYears = FinancialYear::where('project_id', Project::where('name', 'LIKE', '%RTC MARKET%')->first()->id)->get();
        return $getYears->mapWithKeys(function ($year) use ($crop, $organisationId, $indicatorId) {
            return ['Year' . $year->number => $this->getYearlyValues($crop, $organisationId, $year->id, $indicatorId)];
        })->toArray();
    }

    public function sumOfYearlyData(array $yearlyData): array
    {
        return collect($yearlyData)
            ->reduce(function ($carry, $yearData) {
                foreach ($yearData as $key => $value) {
                    $carry[$key] = ($carry[$key] ?? 0) + (float)$value;
                }
                return $carry;
            }, []);
    }

    public function consolidatedData($item, $indicatorNo, $indicatorName, $baselineValue, array $yearlyData = [
        'crop' => null,
        'organisationId' => null,
        'indicatorId' => null
    ]): array
    {
        $yearData = $this->populateYearlyValues(...array_values($yearlyData));
        $sumYearlyData = $this->sumOfYearlyData($yearData);

        $data =
            [
                $indicatorNo, //Indicator No
                $indicatorName, //Indicator Name
                $baselineValue, //Baseline
                $item->name, //Disaggregation
                (string) $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year1'], //Year 1 target
                (string) $yearData['Year1'][$item->name], //Year 1 achieved
                (string) $this->calculatePercentage($yearData['Year1'][$item->name], $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year1']), // Year 1 % Achieved
                (string) $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year2'], //Year 3 target
                (string) $yearData['Year2'][$item->name], //Year 2 achieved
                (string) $this->calculatePercentage($yearData['Year2'][$item->name], $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year2']), //Year 2 % Achieved
                (string) $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year3'], //Year 3 target
                (string) $yearData['Year3'][$item->name], //Year 3 achieved
                (string) $this->calculatePercentage($yearData['Year3'][$item->name], $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year3']), //Year 3 % Achieved
                (string) $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year4'], //Year 4 target
                (string) $yearData['Year4'][$item->name], //Year 4 achieved
                (string) $this->calculatePercentage($yearData['Year4'][$item->name], $this->getYearlyTargets($item->name, $item->indicator->indicator_name)['Year4']), //Year 4 % Achieved
                (string) '0', // LOP targets
                (string) $sumYearlyData[$item->name], // Achievement to Date
                (string) '', // Achievement % to Date
                (string) '', // Comments




            ];

        //  dd($data);

        return $data;
    }
    public function title(): string
    {
        return  $this->sheetName;
    }
}
