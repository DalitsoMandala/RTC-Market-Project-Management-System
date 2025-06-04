<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use App\Models\ReportStatus;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Traits\IndicatorsTrait;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\DB;
use App\Jobs\sendReminderToUserJob;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Artisan;
use App\Notifications\SubmissionReminder;
use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use App\Notifications\SubmissionPeriodsEndingSoon;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;

class TestingController extends Controller
{

    use IndicatorsTrait;
    public function create()
    {
        $this->getEndingSoonSubmissionPeriods();
    }
    public function test()
    {

        // $crops = ['Cassava', 'Potato', 'Sweet potato'];
        // $newData = [];


        // foreach ($crops as $crop) {
        //     $data = new \App\Helpers\rtc_market\indicators\indicator_B4(financial_year: 2, organisation_id: 1);
        //     $newData[$crop] = $data->getDisaggregations();
        // }


        //  return response()->json($newData);
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // 🔴 Disable FIRST

        ReportStatus::find(1)->update([
            'status' => 'completed',
            'progress' => 100
        ]);

        DB::table('system_report_data')->truncate();  // 🔁 Truncate child table first
        DB::table('system_reports')->truncate();
        DB::table('additional_report')->truncate();

        Artisan::call('update:information');

        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // ✅ Re-enable
    }
}
