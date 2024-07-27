<?php

namespace App\Livewire\External;

use App\Helpers\rtc_market\indicators\A1;
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
    use LivewireAlert;
    #[Validate('required')]
    public $variable;

    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;

    public $attendance;

    public $quickForms;
    public function mount()
    {
        $a1 = new A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $this->fill([
            'indicatorCount' => Indicator::count(),
        ]);
        $this->data = [
            'actors' => $a1->getDisaggregations(),
            'name' => $indicator->indicator_name,
        ];

        $this->submissions = Submission::select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('COUNT(*) as total')
        )
            ->groupBy('year', 'month')
            ->get()->toArray();

        $this->attendance = AttendanceRegister::get()->take(5);
        $this->quickForms = Form::with(['project', 'indicators'])->whereNot('name', 'REPORT FORM')->get()->take(5);

    }
    public function render()
    {
        return view('livewire.external.dashboard', [
            'projects' => Project::get(),
        ]);
    }
}