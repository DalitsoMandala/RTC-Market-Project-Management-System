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
        $this->getCurrentData();

    }

    #[On('updateChartData')]
    public function updateCharts($data)
    {
        // Access parameters as needed
        $this->data = $data;
    }

    private function getCurrentData()
    {
        // 1. Retrieve required foreign keys upfront
        $indicatorId         = Indicator::where('indicator_no', 'A1')->value('id');
        $projectId           = Project::where('name', 'RTC MARKET')->value('id');
        $unspecifiedPeriodId = ReportingPeriodMonth::where('type', '!=', 'UNSPECIFIED')->value('id');

        if (! $indicatorId || ! $projectId || ! $unspecifiedPeriodId) {
            return;
        }

        // 2. Query total aggregated value for 'Total' name
        $totalValue = SystemReportData::whereHas('systemReport', function ($query) use ($indicatorId, $projectId, $unspecifiedPeriodId) {
            $query->whereNull('crop')
                ->where('indicator_id', $indicatorId)
                ->where('project_id', $projectId)
                ->where('reporting_period_id', '!=', $unspecifiedPeriodId);
        })
            ->where('name', 'Total') // Query only 'Total' directly instead of pulling & filtering all names
            ->sum('value');

        // 3. Fetch LOP Target specifically for indicator A1 and 'Total' disaggregation
        $lopTargets = $this->getLopTargets();
        $lopTarget  = $lopTargets[$indicatorId]['Total'] ?? 0;

        // 4. Assign project data state
        $this->projectData = [
            'actual' => (float) $totalValue ?? 0,
            'lop'    => (float) $lopTarget ?? 0,
        ];
    }

    private function getLopTargets(): array
    {
        try {
            // Retrieve A1 indicator with its disaggregations key-mapped
            $indicators = Indicator::where('is_active', true)
                ->where('indicator_no', 'A1')
                ->with('disaggregations')
                ->get()
                ->keyBy('id');

            if ($indicators->isEmpty()) {
                return [];
            }

            // Fetch targets only belonging to active A1 indicators
            $submissionTargets = SubmissionTarget::whereIn('indicator_id', $indicators->keys())
                ->select(['indicator_id', 'target_name', 'target_value'])
                ->get();

            $collection = [];

            foreach ($submissionTargets as $target) {
                $indicatorId = $target->indicator_id;
                $targetName  = $target->target_name;

                if (! isset($collection[$indicatorId])) {
                    $collection[$indicatorId] = [];
                }

                if (! isset($collection[$indicatorId][$targetName])) {
                    $collection[$indicatorId][$targetName] = 0;
                }

                $collection[$indicatorId][$targetName] += (float) $target->target_value;
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
