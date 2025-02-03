<?php

namespace App\Exports;

use App\Models\FinancialYear;
use App\Models\IndicatorDisaggregation;
use App\Models\SubmissionTarget;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ProgressSummaryExportSheet implements FromCollection, WithHeadings, WithTitle
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public $template = false;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        //

        if ($this->template) {
            $data = [];
            $indiccatorDisaggregation = IndicatorDisaggregation::with(['indicator'])->get();
            foreach ($indiccatorDisaggregation as $disaggregation) {
                $year1 = FinancialYear::where('number', 1)->where('project_id', 1)->first();


                $findSubmissionTargeForYearOne = SubmissionTarget::where('financial_year_id', $year1->id)
                    ->where('indicator_id', $disaggregation->indicator->id)
                    ->where('target_name', $disaggregation->name)->first();
                $target = null;
                if ($findSubmissionTargeForYearOne) {
                    $target = $findSubmissionTargeForYearOne->target_value;
                }

                $data[] = [
                    "Indicator Number" =>  $disaggregation->indicator->indicator_no,
                    "Indicator" =>  $disaggregation->indicator->indicator_name,
                    "Disaggregation" => $disaggregation->name,
                    //  "Y1 Target" => $target,
                    "Y1 Achieved" => null,
                    "Y2 Target" => null,
                    "Y2 Achieved" =>  null,
                ];
            }
            return collect([$data]);
        }
    }
    public function headings(): array
    {
        return [
            "Indicator Number",
            "Indicator",
            "Disaggregation",
            //  "Y1 Target",
            "Y1 Achieved",
            "Y2 Target",
            "Y2 Achieved",
        ];
    }

    public function title(): string
    {
        return 'Progress summary';
    }
}