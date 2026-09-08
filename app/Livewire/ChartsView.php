<?php
namespace App\Livewire;

use App\Models\Indicator;
use App\Models\Project;
use App\Models\ReportingPeriodMonth;
use App\Models\SubmissionTarget;
use App\Models\SystemReportData;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Component;

class ChartsView extends Component
{
    use LivewireAlert;
    public $data;

    public $projectData;
    public function save()
    {}

    public function mount()
    {
        //  dd($this->getCurrentData());
    }

    #[On('updateChartData')]
    public function updateCharts($data)
    {
        // Access parameters as needed
        $this->data = $data;
    }

    private function getCurrentData()
    {
        // Retrieve required foreign keys upfront to prevent query chaining issues
        $indicatorId         = Indicator::where('indicator_no', 'A1')->value('id');
        $projectId           = Project::where('name', 'RTC MARKET')->value('id');
        $unspecifiedPeriodId = ReportingPeriodMonth::where('type', '!=', 'UNSPECIFIED')->value('id');

        // Handle missing relational records early
        if (! $indicatorId || ! $projectId || ! $unspecifiedPeriodId) {
            return [];
        }

        $systemReport = SystemReportData::whereHas('systemReport', function ($query) use ($indicatorId, $projectId, $unspecifiedPeriodId) {
            $query->whereNull('crop')
                ->where('indicator_id', $indicatorId)
                ->where('project_id', $projectId)
                ->where('reporting_period_id', '!=', $unspecifiedPeriodId);

        })
            ->join('system_reports', 'system_report_data.system_report_id', '=', 'system_reports.id')
            ->selectRaw('system_report_data.name as report_name, SUM(system_report_data.value) as total_value')
            ->groupBy('system_report_data.name')
            ->pluck('total_value', 'report_name')
            ->toArray();
        dd($systemReport);
    }

    private function getLopTargets(): array
    {
        try {
            $indicators        = Indicator::where('is_active', true)->where('indicator_no', 'A1')->with('disaggregations')->get()->keyBy('id');
            $submissionTargets = SubmissionTarget::select([
                'indicator_id',
                'target_name',
                'target_value',
                'financial_year_id',
            ])->get();

            $collection = [];

            foreach ($submissionTargets as $target) {
                $indicator = $indicators[$target->indicator_id] ?? null;
                if (! $indicator) {
                    continue;
                }

                if (! isset($collection[$indicator->id])) {
                    $collection[$indicator->id] = [];
                }

                foreach ($indicator->disaggregations as $disaggregation) {
                    $name = $disaggregation->name;

                    if (! isset($collection[$indicator->id][$name])) {
                        $collection[$indicator->id][$name] = 0;
                    }

                    if ($target->target_name === $name) {
                        $collection[$indicator->id][$name] += (float) $target->target_value;
                    }
                }
            }

            return $collection;
        } catch (\Exception $e) {
            Log::error('Failed to get LOP targets', [
                'error' => $e->getMessage(),

            ]);
            return [];
        }
    }
    public function render()
    {
        return view('livewire.charts-view');
    }
}
