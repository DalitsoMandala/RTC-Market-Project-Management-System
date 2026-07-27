<?php
namespace App\Exports\AggExport;

use App\Models\FinancialYear;
use App\Models\IndicatorDisaggregation;
use App\Models\ReportingPeriodMonth;
use App\Models\SubmissionTarget;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class AggExportSheet implements FromCollection, WithHeadings, WithTitle, WithEvents, WithStrictNullComparison
{
    use ExportStylingTrait;
    public $template        = false;
    protected const PROJECT = 'RTC MARKET';

    public function __construct(bool $template)
    {
        $this->template = $template;
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet         = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                // Make the first row (header) bold

                // Set background color for the second row (A2:ZZ2)
                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => '000000'], // Red text
                        'bold'  => true,
                    ],
                    'fill' => [
                        'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFC5'], // Pink background
                    ],
                ]);
            },
        ];
    }
    public function collection()
    {
        //

        if ($this->template) {
            $data                     = [];
            $indiccatorDisaggregation = IndicatorDisaggregation::with(['indicator'])
                ->join('indicators', 'indicator_disaggregations.indicator_id', '=', 'indicators.id')
                ->select('indicator_disaggregations.*', 'indicators.indicator_no', 'indicators.indicator_name')
                ->orderBy('indicators.id')->get();
            $submissionTargetsDisaggregations = SubmissionTarget::all();
            $financialYears                   = FinancialYear::with('project')->whereHas('project', fn($q) => $q->where('name', self::PROJECT))->get();
            $reportingPeriodMonths            = ReportingPeriodMonth::with('project')
                ->where('type', '!=', 'UNSPECIFIED')
                ->whereHas('reportingPeriod', fn($q) => $q->where('name', 'QUARTERLY'))
                ->get();

            $collectDisaggregations = collect();

            $indiccatorDisaggregation->each(function ($disaggregation) use (&$collectDisaggregations) {
                $collectDisaggregations->push([

                    // "Indicator"      => "(" . $disaggregation->indicator->indicator_no . ") " . $disaggregation->indicator->indicator_name,
                    "Indicator Number" => $disaggregation->indicator->indicator_no,
                    "Indicator Name"   => $disaggregation->indicator->indicator_name,
                    "Disaggregation"   => $disaggregation->name,

                ]);
            });

            return collect([$collectDisaggregations]);
        }
    }
    public function headings(): array
    {
        return [

            "Indicator Number",
            "Indicator Name",
            "Disaggregation",
            ...FinancialYear::with('project')->whereHas('project', fn($q) => $q->where('name', self::PROJECT))
                ->get()
                ->flatMap(function ($financialYear) {
                    return ReportingPeriodMonth::with('project')
                        ->where('type', '!=', 'UNSPECIFIED')
                        ->whereHas('reportingPeriod', fn($q) => $q->where('name', 'QUARTERLY'))
                        ->get()
                        ->map(function ($reportingPeriodMonth) use ($financialYear) {
                            return "Year" . $financialYear->number . "_" . $reportingPeriodMonth->type;
                        });
                })
                ->toArray(),

        ];
    }

    public function title(): string
    {
        return 'Aggregated Report';
    }
}
