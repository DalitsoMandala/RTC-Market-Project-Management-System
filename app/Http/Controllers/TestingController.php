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
use App\Traits\IndicatorsTrait;

class TestingController extends Controller
{

    use IndicatorsTrait;
    public function create()
    {
        $this->getEndingSoonSubmissionPeriods();
    }
}
