<?php

namespace App\Livewire\External;

use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\indicator_A1;
use App\Models\AttendanceRegister;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Project;
use App\Models\Submission;
use data;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Dashboard extends Component
{
    #[Lazy]
    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;
    public $showContent = false;
    public $quickForms;

    public $pending;

    public $today;
    public function mount()
    {


    }

    public function loadData()
    {

        $a1 = new indicator_A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->fill([
            'indicatorCount' => Indicator::count(),
        ]);
        $this->data = [
            'actors' => $a1->getDisaggregations(),
            'name' => $indicator->indicator_name,
        ];

        $submissionsQuery = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('user_id', auth()->user()->id)
            ->groupBy('year', 'month');

        // Fetch grouped submissions data
        $this->submissions = $submissionsQuery->get()->toArray();

        // Count pending submissions
        $this->pending = Submission::where('user_id', auth()->user()->id)
            ->where('status', 'pending')
            ->count();

        // Count today's submissions
        $this->today = Submission::where('user_id', auth()->user()->id)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        // Retrieve the latest 5 attendance records
        $this->attendance = AttendanceRegister::latest()->take(5)->get();

        // Retrieve the latest 5 quick forms excluding 'REPORT FORM'
        $this->quickForms = Form::with(['project', 'indicators'])
            ->where('name', '!=', 'REPORT FORM')
            ->latest()
            ->take(5)
            ->get();

        $this->showContent = true;

    }

    public function render()
    {
        return view('livewire.external.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}