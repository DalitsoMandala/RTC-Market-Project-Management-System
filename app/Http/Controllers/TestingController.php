<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\SubmissionReminder;
use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use App\Notifications\SubmissionPeriodsEndingSoon;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;
use App\Jobs\sendReminderToUserJob;

class TestingController extends Controller
{
    use GroupsEndingSoonSubmissionPeriods;

    public function create()
    {

        $sendExpired = true;

        $users =   $this->getUserWithDeadlines(3, $sendExpired);

        if ($sendExpired) {
            foreach ($users as $userId => $userData) {


                $indicators = $userData['user']->organisation->indicatorResponsiblePeople->map(function ($indicator) {

                    return Indicator::find($indicator->indicator_id)->indicator_name;
                })->flatten();


                $userData['indicators'] = $indicators;

                Bus::chain([
                    new SendExpiredPeriodNotificationJob($userData['user'], $userData)
                ])->dispatch();
            }
        } else {
        }
    }


    public function store(Request $request)
    {
        //

        $dateEstablished = Carbon::now()->startOfDay()->format('Y-m-d H:i:s');  // Today's date
        $dateEnding = Carbon::now()->addDays(2)->format('Y-m-d H:i:s');  // Date one month from today
        $indicatorData = Indicator::with('forms', 'disaggregations', 'organisation')->get();
        foreach ($indicatorData as $index => $indicator) {
            $indicatorDis = $indicator->disaggregations->first();
            $organisations = $indicator->organisation;
            $users = $organisations->pluck('users')->flatten();


            foreach (FinancialYear::all() as $financialYear) {
                if ($indicator->forms->count() > 0) {
                    foreach ($indicator->forms as $form) {
                        if ($financialYear->id == 2) {
                            $period = SubmissionPeriod::create([
                                'form_id' => $form->id,
                                'date_established' => $dateEstablished,
                                'date_ending' => $dateEnding,
                                'month_range_period_id' => 1,
                                'financial_year_id' => $financialYear->id,
                                'indicator_id' => $indicator->id,
                                'is_open' => true,
                                'is_expired' => false,
                            ]);


                            foreach ($users as $key => $user) {
                                if ($key >= 3) {
                                    break;
                                }

                                MailingList::create(

                                    [
                                        'user_id' => $user->id,
                                        'submission_period_id' => $period->id
                                    ]
                                );
                            }
                            $target = SubmissionTarget::where('indicator_id', $indicator->id)
                                ->where('target_name', $indicatorDis->name)
                                ->where('financial_year_id', $financialYear->id)
                                ->first();
                            if (!$target) {
                                SubmissionTarget::create([
                                    'financial_year_id' => $financialYear->id,
                                    'indicator_id' => $indicator->id,
                                    'target_name' => $indicatorDis->name,
                                    'target_value' => rand(50, 100) * 10,
                                ]);
                            }
                        }
                    }
                }
            }
        }
    }
}
