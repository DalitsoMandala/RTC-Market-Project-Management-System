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
use Exception;
use Illuminate\Support\Collection;

class ReportExport extends ReportExportTemplate implements FromCollection, WithTitle, WithHeadings, WithStyles, WithMapping
{
    private string $sheetName;
    private string $type;
    private string $crop;
    private array $data;
    private ?string $lastIndicator = null;
    private array $lopTargets;
    private ?Project $project;


    public function __construct(string $sheetName, string $type = 'crop', array $data = [])
    {
        $this->sheetName = $sheetName;
        $this->type = $type;
        $this->data = $data;
        $this->project = $this->getProject();
        $this->lopTargets = $this->getLopTargets();
    }

    /**
     * Get the RTC MARKET project with error handling
     */
    private function getProject(): ?Project
    {
        try {
            return Project::where('name', 'RTC MARKET')->first();
        } catch (Exception $e) {
            Log::error('Failed to fetch project', [
                'error' => $e->getMessage(),
                'sheet' => $this->sheetName
            ]);
            return null;
        }
    }

    /**
     * Get Life of Project targets with proper error handling
     */
    public function getLopTargets(): array
    {
        try {
            $indicators = Indicator::with('disaggregations')->get()->keyBy('id');
            $submissionTargets = SubmissionTarget::select([
                'indicator_id',
                'target_name',
                'target_value',
                'financial_year_id',
            ])->get();

            $collection = [];

            foreach ($submissionTargets as $target) {
                $indicator = $indicators[$target->indicator_id] ?? null;
                if (!$indicator) {
                    continue;
                }

                if (!isset($collection[$indicator->id])) {
                    $collection[$indicator->id] = [];
                }

                foreach ($indicator->disaggregations as $disaggregation) {
                    $name = $disaggregation->name;

                    if (!isset($collection[$indicator->id][$name])) {
                        $collection[$indicator->id][$name] = 0;
                    }

                    if ($target->target_name === $name) {
                        $collection[$indicator->id][$name] += (float) $target->target_value;
                    }
                }
            }

            return $collection;
        } catch (Exception $e) {
            Log::error('Failed to get LOP targets', [
                'error' => $e->getMessage(),
                'sheet' => $this->sheetName
            ]);
            return [];
        }
    }

    public function collection(): Collection
    {
        try {
            return IndicatorDisaggregation::with([
                'indicator',
                'indicator.baseline',
                'indicator.baseline.baselineMultiple',
                'indicator.submissionTargets'
            ])
                ->orderBy('indicator_id')
                ->get();
        } catch (Exception $e) {
            Log::error('Failed to fetch indicator disaggregations', [
                'error' => $e->getMessage(),
                'sheet' => $this->sheetName
            ]);
            return collect();
        }
    }

    /**
     * Get yearly targets with validation and error handling
     */
    private function getYearlyTargets(?string $disaggregation, ?string $indicatorName, $organisationId = null): array
    {
        try {
            $yearTargets = [
                'Year1' => null,
                'Year2' => null,
                'Year3' => null,
                'Year4' => null
            ];

            if (!$disaggregation || !$indicatorName) {
                return $yearTargets;
            }

            $indicator = Indicator::where('indicator_name', $indicatorName)->first();
            if (!$indicator) {
                Log::warning('Indicator not found', ['indicator_name' => $indicatorName]);
                return $yearTargets;
            }

            $financialYears = FinancialYear::where('project_id', $this->project->id)->get();

            foreach ($financialYears as $year) {
                $financialYear = $year->number;
                $target = SubmissionTarget::with('organisationTargets')->where([
                    'target_name' => $disaggregation,
                    'financial_year_id' => $year->id,
                    'indicator_id' => $indicator->id,
                ])->first();

                if ($target && $target->organisationTargets->isNotEmpty()) {
                    $target = $target->organisationTargets->where('organisation_id', $organisationId)->first();
                }else{
                    Log::warning('Target not found', [
                        'target_name' => $disaggregation,
                        'financial_year_id' => $year->id,
                        'indicator_id' => $indicator->id
                    ]);

                }


                $yearTargets['Year' . $financialYear] = $target->value ?? null;
            }

            return $yearTargets;
        } catch (Exception $e) {
            Log::error('Failed to get yearly targets', [
                'error' => $e->getMessage(),
                'disaggregation' => $disaggregation,
                'indicator_name' => $indicatorName,
                'sheet' => $this->sheetName
            ]);
            return [
                'Year1' => null,
                'Year2' => null,
                'Year3' => null,
                'Year4' => null
            ];
        }
    }

    /**
     * Apply filters with validation
     */
    public function applyFilters(
         $reporting_period_id = null,
         $financial_year_id = null,
         $organisation_id = null,
         $enterprise = null,
         $indicator_id = null
    ): Builder {
        try {





            $query = SystemReport::with('data')
                ->where('indicator_id', $indicator_id)
                ->where('project_id', $this->project->id)
                ->when($reporting_period_id, fn($q) => $q->where('reporting_period_id', $reporting_period_id))
                ->when($financial_year_id, fn($q) => $q->where('financial_year_id', $financial_year_id))
                ->when($organisation_id, fn($q) => $q->where('organisation_id', $organisation_id))
                ->when($enterprise, function ($q) use ($enterprise) {
                    return $enterprise === 'All'
                        ? $q->whereNull('crop')
                        : $q->where('crop', $enterprise);
                });

            return $query;
        } catch (Exception $e) {
            Log::error('Failed to apply filters', [
                'error' => $e->getMessage(),
                'indicator_id' => $indicator_id,
                'sheet' => $this->sheetName
            ]);
            throw $e;

        }
    }

    /**
     * Get yearly values with error handling
     */
    public function getYearlyValues(
        ?string $crop,
        $organisationId,
      $financialYearId,
       $indicatorId
    ): array {
        try {
            $crop = $crop ?? 'All';

            $reportIds = $this->applyFilters(
                reporting_period_id: null,
                financial_year_id: $financialYearId,
                organisation_id: $organisationId,
                enterprise: $crop,
                indicator_id: $indicatorId
            );
            $reportIds = $reportIds->pluck('id');

            $reportData = SystemReportData::whereIn('system_report_id', $reportIds)->get();
            $groupedData = $reportData->groupBy('name');

            $disaggregationNames = IndicatorDisaggregation::where('indicator_id', $indicatorId)
                ->pluck('name')
                ->unique();

            $data = $disaggregationNames->mapWithKeys(
                fn($name) => [$name => $groupedData->has($name) ? $groupedData[$name]->sum('value') : 0]
            );

            return $data->toArray();
        } catch (Exception $e) {
            Log::error('Failed to get yearly values', [
                'error' => $e->getMessage(),
                'financial_year_id' => $financialYearId,
                'indicator_id' => $indicatorId,
                'sheet' => $this->sheetName
            ]);
            return [];
        }
    }

    public function map($item): array
    {
        try {
            $indicatorNo = '';
            $indicatorName = '';
            $baselineValue = '';
            $indicatorId = $item->indicator?->id ?? '';

            // Only print indicator info once per group
            if ($item->indicator?->indicator_name !== $this->lastIndicator) {
                $indicatorNo = $item->indicator?->indicator_no ?? '';
                $indicatorName = $item->indicator?->indicator_name ?? '';

                $this->lastIndicator = $item->indicator?->indicator_name;
            }

            $baselineValue = $this->getBaselineValue($item, $indicatorNo);


            if(!$item->indicator) {
                return [];
            }
           return  $this->getSheetData($item, $indicatorNo, $indicatorName, $baselineValue, $indicatorId);

             return [];
        } catch (Exception $e) {
            Log::error('Failed to map data for export', [
                'error' => $e->getMessage(),
                'item_id' => $item->id ?? 'unknown',
                'sheet' => $this->sheetName
            ]);
            return [];
        }
    }

    /**
     * Extract baseline value calculation
     */
    private function getBaselineValue($item, string $indicatorNo): string
    {
        $baseline = $item->indicator?->baseline;
        if (!$baseline) {
            return '';
        }

        if ($baseline->baseline_is_multiple == 1) {
            $match = $baseline->baselineMultiple->firstWhere('name', $item->name);
            return $match?->baseline_value ?? '';
        } else {
            // Single baseline: only show once (on first disaggregation)
            return $indicatorNo !== '' ? $baseline->baseline_value : '';
        }
    }

    /**
     * Route to appropriate sheet data handler
     */
  /**
 * Route to proper handler based on sheet name
 */
private function getSheetData(
    $item,
    string $indicatorNo,
    string $indicatorName,
    string $baselineValue,
    string $indicatorId
): array {
    // Define crops and organizations that need handling
    $crops = [
        'Consolidated'   => 'All',
        'Sweet potato'   => 'Sweet potato',
        'Cassava'        => 'Cassava',
        'Potato'         => 'Potato',
    ];

    $organisations = [
        'IITA', 'ACE', 'DCD', 'DAES', 'MINISTRY OF TRADE',
        'TRADELINE', 'DARS', 'RTCDT', 'LUANAR',
    ];

    // Handle crop-based sheets
    if (isset($crops[$this->sheetName])) {
        return $this->consolidatedData(
            $item,
            $indicatorNo,
            $indicatorName,
            $baselineValue,
            [
                'crop' => $crops[$this->sheetName],
                'organisationId' => null,
                'indicatorId' => $indicatorId,
            ]
        );
    }

    // Handle organization-based sheets (future logic placeholder)
    if (in_array($this->sheetName, $organisations, true)) {
        $organisationId = Organisation::where('name', $this->sheetName)->first()->id ?? null;
        return $this->organisationalData(
            $item,
            $indicatorNo,
            $indicatorName,
            $baselineValue,
            [
                'crop' => null,
                'organisationId' => $organisationId,
                'indicatorId' => $indicatorId,
            ]);
    }

    // Default: empty dataset
    return [];
}


    public function calculatePercentage(float $numerator, float $denominator): float
    {
        try {
            $numerator = (float)$numerator;
            $denominator = (float)$denominator;

            if ($denominator == 0) {
                return 0.0;
            }

            return round(($numerator / $denominator) * 100, 2);
        } catch (Exception $e) {
            Log::warning('Percentage calculation failed', [
                'error' => $e->getMessage(),
                'numerator' => $numerator,
                'denominator' => $denominator
            ]);
            return 0.0;
        }
    }

    /**
     * Populate yearly values with error handling
     */
    public function populateYearlyValues(?string $crop,  $organisationId, $indicatorId): array
    {
        try {
            if (!$this->project) {
                throw new Exception('Project not available');
            }

            $getYears = FinancialYear::where('project_id', $this->project->id)->get();

            return $getYears->mapWithKeys(function ($year) use ($crop, $organisationId, $indicatorId) {
                return ['Year' . $year->number => $this->getYearlyValues($crop, $organisationId, $year->id, $indicatorId)];
            })->toArray();
        } catch (Exception $e) {
            Log::error('Failed to populate yearly values', [
                'error' => $e->getMessage(),
                'indicator_id' => $indicatorId,
                'sheet' => $this->sheetName
            ]);
            return [];
        }
    }

    public function sumOfYearlyData(array $yearlyData): array
    {
        try {
            return collect($yearlyData)
                ->reduce(function ($carry, $yearData) {
                    foreach ($yearData as $key => $value) {
                        $carry[$key] = ($carry[$key] ?? 0) + (float)($value ?? 0);
                    }
                    return $carry;
                }, []);
        } catch (Exception $e) {
            Log::error('Failed to sum yearly data', [
                'error' => $e->getMessage(),
                'sheet' => $this->sheetName
            ]);
            return [];
        }
    }

    public function organisationalData($item, $indicatorNo, $indicatorName, $baselineValue, array $yearlyData = []): array
    {
        try {

            $yearData = $this->populateYearlyValues(...array_values($yearlyData));
            $sumYearlyData = $this->sumOfYearlyData($yearData);

            $yearlyTargets = $this->getYearlyTargets($item->name, $item->indicator->indicator_name, $yearlyData['organisationId'] ?? null);
       //     Log::info("Indicator-".$yearlyData['indicatorId'], $yearData);
            return [
                $indicatorNo, // Indicator No
                $indicatorName, // Indicator Name
                $baselineValue, // Baseline
                $item->name, // Disaggregation
                (string) ($yearlyTargets['Year1'] ?? ''), // Year 1 target
                (string) ($yearData['Year1'][$item->name] ?? ''), // Year 1 achieved
                (string) $this->calculatePercentage($yearData['Year1'][$item->name] ?? 0, $yearlyTargets['Year1'] ?? 0) . '%', // Year 1 % Achieved
                (string) ($yearlyTargets['Year2'] ?? ''), // Year 2 target
                (string) ($yearData['Year2'][$item->name] ?? ''), // Year 2 achieved
                (string) $this->calculatePercentage($yearData['Year2'][$item->name] ?? 0, $yearlyTargets['Year2'] ?? 0). '%', // Year 2 % Achieved
                (string) ($yearlyTargets['Year3'] ?? ''), // Year 3 target
                (string) ($yearData['Year3'][$item->name] ?? ''), // Year 3 achieved
                (string) $this->calculatePercentage($yearData['Year3'][$item->name] ?? 0, $yearlyTargets['Year3'] ?? 0). '%', // Year 3 % Achieved
                (string) ($yearlyTargets['Year4'] ?? ''), // Year 4 target
                (string) ($yearData['Year4'][$item->name] ?? ''), // Year 4 achieved
                (string) $this->calculatePercentage($yearData['Year4'][$item->name] ?? 0, $yearlyTargets['Year4'] ?? 0). '%', // Year 4 % Achieved
                (string) ($this->lopTargets[$yearlyData['indicatorId']][$item->name] ?? ''), // LOP targets
                (string) ($sumYearlyData[$item->name] ?? ''), // Achievement to Date
                (string) $this->calculatePercentage($sumYearlyData[$item->name] ?? 0, $this->lopTargets[$yearlyData['indicatorId']][$item->name] ?? 0). '%', // Achievement % to Date
                (string) '', // Comments
            ];
        } catch (Exception $e) {
            Log::error('Failed to generate consolidated data', [
                'error' => $e->getMessage(),
                'indicator_id' => $yearData,
                'sheet' => $this->sheetName
            ]);
            return array_fill(0, 20, 'Error');
        }
    }

    public function consolidatedData($item, $indicatorNo, $indicatorName, $baselineValue, array $yearlyData = []): array
    {
        try {

            $yearData = $this->populateYearlyValues(...array_values($yearlyData));
            $sumYearlyData = $this->sumOfYearlyData($yearData);

            $yearlyTargets = $this->getYearlyTargets($item->name, $item->indicator->indicator_name ?? null);
       //     Log::info("Indicator-".$yearlyData['indicatorId'], $yearData);
            return [
                $indicatorNo, // Indicator No
                $indicatorName, // Indicator Name
                $baselineValue, // Baseline
                $item->name, // Disaggregation
                (string) ($yearlyTargets['Year1'] ?? ''), // Year 1 target
                (string) ($yearData['Year1'][$item->name] ?? ''), // Year 1 achieved
                (string) $this->calculatePercentage($yearData['Year1'][$item->name] ?? 0, $yearlyTargets['Year1'] ?? 0) . '%', // Year 1 % Achieved
                (string) ($yearlyTargets['Year2'] ?? ''), // Year 2 target
                (string) ($yearData['Year2'][$item->name] ?? ''), // Year 2 achieved
                (string) $this->calculatePercentage($yearData['Year2'][$item->name] ?? 0, $yearlyTargets['Year2'] ?? 0). '%', // Year 2 % Achieved
                (string) ($yearlyTargets['Year3'] ?? ''), // Year 3 target
                (string) ($yearData['Year3'][$item->name] ?? ''), // Year 3 achieved
                (string) $this->calculatePercentage($yearData['Year3'][$item->name] ?? 0, $yearlyTargets['Year3'] ?? 0). '%', // Year 3 % Achieved
                (string) ($yearlyTargets['Year4'] ?? ''), // Year 4 target
                (string) ($yearData['Year4'][$item->name] ?? ''), // Year 4 achieved
                (string) $this->calculatePercentage($yearData['Year4'][$item->name] ?? 0, $yearlyTargets['Year4'] ?? 0). '%', // Year 4 % Achieved
                (string) ($this->lopTargets[$yearlyData['indicatorId']][$item->name] ?? ''), // LOP targets
                (string) ($sumYearlyData[$item->name] ?? ''), // Achievement to Date
                (string) $this->calculatePercentage($sumYearlyData[$item->name] ?? 0, $this->lopTargets[$yearlyData['indicatorId']][$item->name] ?? 0). '%', // Achievement % to Date
                (string) '', // Comments
            ];
        } catch (Exception $e) {
            Log::error('Failed to generate consolidated data', [
                'error' => $e->getMessage(),
                'indicator_id' => $yearData,
                'sheet' => $this->sheetName
            ]);
            return array_fill(0, 20, 'Error');
        }
    }

    public function title(): string
    {
        return $this->sheetName;
    }
}

