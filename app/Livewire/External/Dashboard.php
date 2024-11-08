<?php

namespace App\Livewire\External;

use data;
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
use Livewire\Attributes\Validate;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use App\Helpers\rtc_market\indicators\A1;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Helpers\rtc_market\indicators\indicator_A1;

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

    public $openSubmissions = 0;
    public function mount()
    {

        $user = User::find(auth()->user()->id);

        $organisation_id = $user->organisation->id;
        $myIndicators = ResponsiblePerson::where('organisation_id', $organisation_id)
            ->whereHas('sources')  // Ensure that the relationship 'sources' exists
            ->pluck('indicator_id')
            ->toArray();


        $query = SubmissionPeriod::with([
            'form',
            'form.indicators'
        ])->whereIn('indicator_id', $myIndicators)->where('is_open', true);


        // // Query SubmissionPeriods with the necessary relationships
        // $query = SubmissionPeriod::with(['form', 'form.indicators'])
        //     ->whereHas('form.indicators.responsiblePeopleforIndicators', function (Builder $query) use ($organisation_id) {
        //         $query->where('organisation_id', $organisation_id);
        //     })

        // ;

        $this->openSubmissions = $query->count();


    }

    public function loadData()
    {

        $indicatorA1 = new Indicator_A1();
        $indicator = Indicator::where('indicator_no', 'A1')->first();
        $organisation = auth()->user()->organisation;
        $currentDate = Carbon::now();
        $record = FinancialYear::
            whereDate('start_date', '<=', $currentDate)  // Current date is on or after start_date
            ->whereDate('end_date', '>=', $currentDate)    // Current date is on or before end_date
            ->where('project_id', 1)

            ->first();


        $reportId = SystemReport::where('indicator_id', $indicator->id)
            ->where('project_id', 1)
            //      ->where('organisation_id', $organisation->id)
            ->where('financial_year_id', $record->id)
            ->pluck('id');



        if ($reportId->isNotEmpty()) {
            // Retrieve and group data by 'name'
            $data = SystemReportData::whereIn('system_report_id', $reportId)->get();
            $groupedData = $data->groupBy('name');

            // Sum each group's values
            $summedGroups = $groupedData->map(function ($group) {
                return $group->sum('value'); // Assuming 'value' is the field to be summed
            });



            // Store the results
            $this->data = $summedGroups;

        }



        if ($indicator && $this->data->isNotEmpty()) {
            $this->fill([
                'indicatorCount' => Indicator::count(),
            ]);

            $this->data = [
                'actors' => $this->data->toArray(),
                'name' => $indicator->indicator_name,
            ];
        } else {
            // Handle the case where the indicator is not found
            $this->data = [
                'actors' => [],
                'name' => '',
            ];
        }
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
        $this->quickForms = Form::with([
            'project',
            'indicators'
        ])
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
