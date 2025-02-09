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
            foreach ($indiccatorDisaggregation as $disaggregation) {
                foreach ($submissionTargetsDisaggregations as $subTarget) {
                    if ($subTarget->indicator_id == $disaggregation->indicator_id && $subTarget->target_name == $disaggregation->name) {


                        $exists = $collectDisaggregations->contains(function ($item) use ($disaggregation) {
                            return $item['Indicator'] === $disaggregation->indicator->indicator_name &&
                                $item['Disaggregation'] === $disaggregation->name;
                        });

                        if (!$exists) {
                            $collectDisaggregations->push([
                                "Indicator Number" => $disaggregation->indicator->indicator_no,
                                "Indicator" => $disaggregation->indicator->indicator_name,
                                "Disaggregation" => $disaggregation->name,
                                "Y1 Achieved" => null,
                                "Y2 Target" => null,
                                "Y2 Achieved" =>  null,
                            ]);
                        }
                    }
                }
            }


            return collect([$collectDisaggregations]);
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