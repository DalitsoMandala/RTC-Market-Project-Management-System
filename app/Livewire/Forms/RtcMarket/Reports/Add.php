<?php

namespace App\Livewire\Forms\RtcMarket\Reports;

use App\Models\Form;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Livewire\Component;
use App\Models\Indicator;
use App\Models\Submission;
use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionReport;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Log;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UserErrorException;
use App\Models\IndicatorDisaggregation;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use App\Notifications\ManualDataAddedNotification;

class Add extends Component
{
    use LivewireAlert;
    public $openSubmission = false;
    public $enterprise;

    public $period;

    public $forms = [];

    public $selectedForm;

    public $months = [];
    public $financialYears = [];

    public $projects = [];

    public $selectedMonth;

    public $selectedFinancialYear;

    public $selectedProject;

    public $submissionPeriodId;
    public $selectedIndicator;
    public $indicatorName;

    public $inputs = [];

    public $formData = [];

    public function mount($form_id, $indicator_id, $financial_year_id, $month_period_id, $submission_period_id)
    {

        if ($form_id == null || $indicator_id == null || $financial_year_id == null || $month_period_id == null || $submission_period_id == null) {

            abort(404);

        }

        $findForm = Form::find($form_id);
        $findIndicator = Indicator::find($indicator_id);
        $findFinancialYear = FinancialYear::find($financial_year_id);
        $findMonthPeriod = ReportingPeriodMonth::find($month_period_id);
        $findSubmissionPeriod = SubmissionPeriod::find($submission_period_id);
        if ($findForm == null || $findIndicator == null || $findFinancialYear == null || $findMonthPeriod == null || $findSubmissionPeriod == null) {

            abort(404);

        } else {
            $this->selectedForm = $findForm->id;
            $this->selectedIndicator = $findIndicator->id;
            $this->selectedFinancialYear = $findFinancialYear->id;
            $this->selectedMonth = $findMonthPeriod->id;
            $this->submissionPeriodId = $findSubmissionPeriod->id;
            //check submission period

            $submissionPeriod = SubmissionPeriod::where('form_id', $this->selectedForm)
                ->where('indicator_id', $this->selectedIndicator)
                ->where('financial_year_id', $this->selectedFinancialYear)
                ->where('month_range_period_id', $this->selectedMonth)
                ->where('is_open', true)
                ->first();
            $this->indicatorName = $findIndicator->indicator_name;
            if ($submissionPeriod) {

                $this->openSubmission = true;

                $user = Auth::user();

                $this->inputs = IndicatorDisaggregation::where('indicator_id', $indicator_id)
                    ->get();




            } else {
                $this->openSubmission = false;

            }
        }

    }



    #[On('save')]
    public function save($data)
    {
        $currentUser = Auth::user();
        $uuid = Uuid::uuid4()->toString();
        $user = User::find($currentUser->id);

        $checkSubmission = Submission::where('period_id', $this->submissionPeriodId)
            ->where('batch_type', 'aggregate')
            ->where(function ($query) {
                $query->where('status', '=', 'pending')
                    ->orWhere('status', '=', 'approved');
            })
            ->where('user_id', auth()->user()->id)->first();

        if ($checkSubmission) {
            session()->flash('error', 'You have already submitted your aggregate data for this period!');
            $this->dispatch('notify');
        } else {

            if (($user->hasAnyRole('internal') && $user->hasAnyRole('organiser')) || $user->hasAnyRole('admin')) {

                try {

                    $submission = Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'user_id' => $currentUser->id,
                        'status' => 'approved',
                        //         'data' => json_encode($data),
                        'batch_type' => 'aggregate',
                        'is_complete' => 1,
                        'period_id' => $this->submissionPeriodId,
                        'table_name' => 'reports',

                    ]);
                    $user = User::find($submission->user_id);
                    $period = SubmissionPeriod::find($submission->period_id);

                    SubmissionReport::create([
                        'indicator_id' => $this->selectedIndicator,
                        'submission_id' => $submission->id,
                        'financial_year_id' => $this->selectedFinancialYear,
                        'submission_period_id' => $this->submissionPeriodId,
                        'period_month_id' => $period->month_range_period_id,
                        'organisation_id' => $user->organisation->id,
                        'user_id' => $user->id,
                        'status' => 'approved',
                        'data' => json_encode($data),
                        'uuid' => $uuid
                    ]);




                    session()->flash('success', 'Successfully submitted!');
                    $this->redirect(route('cip-internal-submissions') . '#aggregate-submission');
                    //    $this->redirect(route('rtc-market-hrc', ['project' => 'rtc_market']));

                } catch (UserErrorException $e) {
                    // Log the actual error for debugging purposes
                    Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            } else if ($user->hasAnyRole('external') || $user->hasAnyRole('staff')) {

                try {
                    $submission = Submission::create([
                        'batch_no' => $uuid,
                        'form_id' => $this->selectedForm,
                        'period_id' => $this->submissionPeriodId,
                        'user_id' => $currentUser->id,
                        //'data' => json_encode($data),
                        'batch_type' => 'aggregate',
                        //   'status' => 'approved',
                        'is_complete' => 1,
                        'table_name' => 'reports',

                    ]);

                    $user = User::find($submission->user_id);
                    $period = SubmissionPeriod::find($submission->period_id);

                    SubmissionReport::create([
                        'indicator_id' => $this->selectedIndicator,
                        'submission_id' => $submission->id,
                        'financial_year_id' => $this->selectedFinancialYear,
                        'submission_period_id' => $this->submissionPeriodId,
                        'period_month_id' => $period->month_range_period_id,
                        'organisation_id' => $user->organisation->id,
                        'user_id' => $user->id,
                        'data' => json_encode($data),
                        'uuid' => $uuid
                    ]);




                    session()->flash('success', 'Successfully submitted!');
                    $this->redirect(route('external-submissions') . '#aggregate-submission');
                } catch (UserErrorException $e) {
                    // Log the actual error for debugging purposes
                    Log::error('Submission error: ' . $e->getMessage());

                    // Provide a generic error message to the user
                    session()->flash('error', 'An error occurred while submitting your data. Please try again later.');
                }

            }

        }


    }

    public function render()
    {
        return view('livewire.forms.rtc-market.reports.add');
    }
}
