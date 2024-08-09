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

        $submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->where('user_id', auth()->user()->id)
            ->groupBy('year', 'month')
        ;

        $this->submissions = $submissions->get()->toArray();

        $this->attendance = AttendanceRegister::get()->take(5);
        $this->quickForms = Form::with(['project', 'indicators'])->whereNot('name', 'REPORT FORM')->get()->take(5);


        $this->showContent = true;

        $this->pending = $submissions->get()->where('status', 'pending')->count();
        $this->today = $submissions->get()->where('created_at', '>=', date('Y-m-d'))->count();
    }

    public function render()
    {
        return view('livewire.external.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}