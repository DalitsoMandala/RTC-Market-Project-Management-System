<?php
namespace App\Imports\AggImport;

use App\Exceptions\ExcelValidationException;
use App\Models\AggregatedReport;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\JobProgress;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Traits\FormEssentials;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');
class AggImportSheet implements ToModel, WithHeadingRow, WithValidation, WithChunkReading, SkipsOnFailure
{
    use Importable, RegistersEventListeners;
    use FormEssentials;

    public $organisation_id;
    public $user_id;

    public $uuid;
    public $crop;
    protected const PROJECT                      = 'RTC MARKET';
    protected ?Collection $financialYears        = null;
    protected ?Collection $reportingPeriodMonths = null;
    protected ?int $projectId                    = null;
    public function __construct($user_id, $organisation_id, $uuid, $crop = null)
    {

        $this->user_id         = $user_id;
        $this->organisation_id = $organisation_id;
        $this->uuid            = $uuid;
        $this->crop            = $crop;
    }

    public function model(array $row)
    {
        // 1. Fetch relations statically/eagerly instead of querying each row
        $indicator = Indicator::with('disaggregations')
            ->where('indicator_name', $row['Indicator Name'])
            ->first();

        if (! $indicator) {
            return null;
        }

        // 2. Cache metadata on first row run so queries aren't repeated for subsequent rows
        $this->projectId ??= Project::where('name', self::PROJECT)->value('id');

        $this->financialYears ??= FinancialYear::whereHas('project', fn($q) => $q->where('name', self::PROJECT))->get();

        $this->reportingPeriodMonths ??= ReportingPeriodMonth::where('type', '!=', 'UNSPECIFIED')
            ->whereHas('reportingPeriod', fn($q) => $q->where('name', 'QUARTERLY'))
            ->get();

        // 3. Match disaggregation using Collection methods instead of a explicit foreach loop
        $disaggregation = $indicator->disaggregations
            ->firstWhere('name', $row['Disaggregation']);

        if (! $disaggregation) {
            return null;
        }

        // 4. Iterate over cached memory collections
        foreach ($this->financialYears as $financialYear) {
            foreach ($this->reportingPeriodMonths as $reportingPeriodMonth) {
                $columnKey = "Year{$financialYear->number}_{$reportingPeriodMonth->type}";

                if (isset($row[$columnKey]) && $row[$columnKey] >= 0) {
                    $conditions = [
                        'financial_year_id'   => $financialYear->id,
                        'reporting_period_id' => $reportingPeriodMonth->id,
                        'organisation_id'     => $this->organisation_id,
                        'project_id'          => $this->projectId,
                        'crop'                => $this->crop,
                        'indicator_id'        => $indicator->id,
                    ];

                    $report = AggregatedReport::firstOrCreate($conditions);

                    $report->data()->upsert(
                        [
                            [
                                'aggregated_report_id' => $report->id,
                                'name'                 => $disaggregation->name,
                                'value'                => $row[$columnKey],
                                'created_at'           => now(),
                                'updated_at'           => now(),
                            ],
                        ],
                        ['aggregated_report_id', 'name'],
                        ['value', 'updated_at']
                    );
                }
            }
        }

        $jobProgress = JobProgress::where('cache_key', $this->uuid)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return null;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Aggregated Report' - Row {$failure->row()}, Field '{$failure->attribute()}': " .
            implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new ExcelValidationException($errorMessage);
        }
    }

    public function rules(): array
    {
        $collection = collect($this->AggregatedReportColumns())->map(function ($column) {

            return [
                $column => 'nullable|numeric|min:0',
            ];

        })->filter(function ($value, $key) {

            return array_key_first($value) !== 'Indicator Number' && array_key_first($value) !== 'Indicator Name' && array_key_first($value) !== 'Disaggregation';
        })->flatMap(function ($value, $key) {

            return $value;
        })->toArray();

        return [

            "Indicator Number" => ['required'], // Allow null values
            'Indicator Name'   => 'required|string',
            'Disaggregation'   => 'required|string',
            ...$collection,

        ];
    }

    public function chunkSize(): int
    {
        return 1000; // Process 1000 rows at a time
    }
}
