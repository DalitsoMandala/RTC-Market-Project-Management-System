<?php

namespace App\Livewire\External;

use Carbon\Carbon;
use App\Models\Form;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SystemReport;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SystemReportData;
use App\Models\ResponsiblePerson;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;

use Jantinnerezo\LivewireAlert\LivewireAlert;

class Dashboard extends Component
{
    public $data;
    public $project;
    public $indicatorCount;
    public $submissions;
    public $attendance;
    public $showContent = false;
    public $quickForms;
    public $pending;
    public $today;
    public $openSubmissions = 0;

    public $overviewIndicator;

    public function mount()
    {
        $user = User::find(auth()->id());
        $organisationId = $user->organisation->id;

        // Fetch indicator IDs associated with the user's organisation
        $myIndicators = ResponsiblePerson::where('organisation_id', $organisationId)
            ->whereHas('sources')  // Ensure 'sources' relationship exists
            ->pluck('indicator_id')
            ->toArray();

        // Query for open submission periods
        $this->openSubmissions = SubmissionPeriod::with([
            'form',
            'form.indicators'
        ])
            ->whereIn('indicator_id', $myIndicators)
            ->where('is_open', true)
            ->count();
    }


    public function render()
    {
        return view('livewire.external.dashboard', [
            'projects' => Project::all(),
        ]);
    }
}