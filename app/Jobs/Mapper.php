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
        Cache::put('report_status', []);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {



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











        Cache::put('report_status', $collection);
    }

    private function prepareItem($disaggregation, int $count): array
    {

        $year = null;
        $period = null;
        if ($this->financial_year != null) {

            $year = FinancialYear::find($this->financial_year)->number;
        }
        if ($this->reporting_period != null) {


            $period = ReportingPeriodMonth::find($this->reporting_period)->start_month . ' - ' . ReportingPeriodMonth::find($this->reporting_period)->end_month;
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
