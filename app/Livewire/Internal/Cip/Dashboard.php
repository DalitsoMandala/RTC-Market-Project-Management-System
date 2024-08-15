<?php

namespace App\Livewire\Internal\Cip;

use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\indicator_A1;
use App\Models\AttendanceRegister;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Lazy;
use Livewire\Component;

class Dashboard extends Component
{

    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;
    public $showContent = false;
    public $quickForms;
    public function mount()
    {


    }

    public function loadData()
    {
        $this->loadIndicatorData();
        $this->loadSubmissionData();
        $this->loadAttendanceData();
        $this->loadQuickFormsData();
        $this->showContent = true;
    }

    private function loadIndicatorData()
    {
        $indicatorA1 = new Indicator_A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();

        if ($indicator) {
            $this->fill([
                'indicatorCount' => Indicator::count(),
            ]);

            $this->data = [
                'actors' => $indicatorA1->getDisaggregations(),
                'name' => $indicator->indicator_name,
            ];
        } else {
            // Handle the case where the indicator is not found
            $this->data = [
                'actors' => [],
                'name' => '',
            ];
        }
    }

    private function loadSubmissionData()
    {
        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('year', 'month')
            ->get()
            ->toArray();
    }

    private function loadAttendanceData()
    {
        $this->attendance = AttendanceRegister::take(5)->get();
    }

    private function loadQuickFormsData()
    {
        $this->quickForms = Form::with(['project', 'indicators'])
            ->whereNot('name', 'REPORT FORM')
            ->take(5)
            ->get();
    }



    public function render()
    {
        return view('livewire.internal.cip.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}
