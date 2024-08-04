<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use App\Models\SubmissionPeriod;
use App\Helpers\IndicatorsContent;
use App\Models\FinancialYear;
use Illuminate\Support\Facades\Cache;
use Illuminate\Queue\SerializesModels;
use App\Models\IndicatorDisaggregation;
use App\Models\ReportingPeriodMonth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Mapper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, Batchable;
    public $reporting_period, $financial_year, $project;
    public $whichPeriod = null;
    public $whichYear = null;
    public $indicators = [];
    /**
     * Create a new job instance.
     */
    public function __construct(array $filtered)
    {
        //

        if (isset($filtered['reporting_period'])) {
            $this->reporting_period = $filtered['reporting_period'];
        }

        if (isset($filtered['financial_year'])) {
            $this->financial_year = $filtered['financial_year'];
        }

        if (isset($filtered['project'])) {
            $this->project = $filtered['project'];
        }

        if (isset($filtered['indicators'])) {
            $this->indicators = $filtered['indicators'];
        }

        // $this->reporting_period = $reporting_period;
        // $this->financial_year = $financial_year;
        // $this->indicators = $indicators;
        // $this->project = $project;
        Cache::put('report_', []);

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


        // $project = null; // Get project ID from request
        // $indicators = null; // Get indicators array from request

        // $builder = IndicatorDisaggregation::with(['indicator.project:id,name', 'indicator.class:indicator_id,class', 'indicator:id,indicator_no,indicator_name,project_id']);

        // // Apply conditions only if the variables are not null
        // if ($project) {
        //     $builder->whereHas('indicator.project', fn($query) => $query->where('id', $project));
        // }

        // if ($indicators) {
        //     $builder->whereHas('indicator', fn($query) => $query->whereIn('id', $indicators));
        // }



        // $builder->get()->transform(function ($query) {
        //     if ($query->indicator && $query->indicator->class) {
        //         $classModel = $query->indicator->class->class;
        //         if ($classModel) {
        //             $indicatorClass = new $classModel();
        //             $query->indicator['data'] = $indicatorClass->getDisaggregations();
        //         }
        //     }
        //     return $query;
        // });



        $builder = IndicatorDisaggregation::with(['indicator', 'indicator.project']);

        if ($this->project) {
            $builder->whereHas('indicator.project', fn($query) => $query->where('id', $this->project));
        }

        if ($this->indicators) {
            $builder->whereHas('indicator', fn($query) => $query->whereIn('id', $this->indicators));
        }

        $collection = collect();
        $count = 1;

        $builder->chunk(100, function ($disaggregations) use (&$collection, &$count) {
            foreach ($disaggregations as $disaggregation) {
                $item = $this->prepareItem($disaggregation, $count++);
                $collection->push($item);
            }
        });











        Cache::put('report_', $collection);


    }

    private function prepareItem($disaggregation, int $count): array
    {

        $year = null;
        $period = null;
        if ($this->reporting_period != null && $this->financial_year != null) {
            $submissionPeriod = SubmissionPeriod::where('month_range_period_id', $this->reporting_period)->where('financial_year_id', $this->financial_year)->pluck('id')->toArray();
            if (!empty($submissionPeriod)) {
                $year = FinancialYear::find($this->financial_year)->number;
                $period = ReportingPeriodMonth::find($this->reporting_period)->start_month . ' - ' . ReportingPeriodMonth::find($this->reporting_period)->end_month;
            }
        }

        $item = [
            'id' => $count,
            'name' => $disaggregation->name,
            'indicator_name' => $disaggregation->indicator->indicator_name,
            'project' => $disaggregation->indicator->project->name,
            'number' => $disaggregation->indicator->indicator_no,
            'indicator_id' => $disaggregation->indicator->id,
            'reporting_period' => $period ?? 'All Months',
            'financial_year' => $year ?? 'All Years'
        ];

        $content = new IndicatorsContent(name: $item['indicator_name'], number: $item['number']);
        $getContent = $content->content();

        if ($getContent['class'] !== null) {
            $indicator = new $getContent['class']($this->reporting_period, $this->financial_year);
            $item = $this->mapData($indicator->getDisaggregations(), $item);
        }

        return $item;
    }

    public function mapData(array $array, array $item): array
    {
        foreach ($array as $key => $record) {
            if ($key === $item['name']) {
                $item['value'] = $record;
                break; // Once found, no need to continue looping
            }
        }

        return $item;
    }
}
