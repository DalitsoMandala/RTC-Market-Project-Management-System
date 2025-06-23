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
            $submissionTargetsDisaggregations = SubmissionTarget::all();
            $collectDisaggregations = collect();


            $indiccatorDisaggregation->each(function ($disaggregation) use (&$collectDisaggregations) {
                $collectDisaggregations->push([
                    "Indicator Number" => $disaggregation->indicator->indicator_no,
                    "Indicator" => $disaggregation->indicator->indicator_name,
                    "Disaggregation" => $disaggregation->name,
                    "Y1 Target" => null,
                    "Y1 Achieved" => null,
                    "Y2 Target" => null,
                    "Y2 Achieved" =>  null,
                    "Y3 Target" => null,
                    "Y3 Achieved" => null,
                    "Y4 Target" => null,
                    "Y4 Achieved" => null

                ]);
            });



            return collect([$collectDisaggregations]);
        }
    }
    public function headings(): array
    {
        return [
            "Indicator Number",
            "Indicator",
            "Disaggregation",
            "Y1 Target",
            "Y1 Achieved",
            "Y2 Target",
            "Y2 Achieved",
            "Y3 Target",
            "Y3 Achieved",
            "Y4 Target",
            "Y4 Achieved"

        ];
    }

    public function title(): string
    {
        return 'Progress summary';
    }
}
