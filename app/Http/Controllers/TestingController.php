<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use Illuminate\Http\Request;
use App\Models\SubmissionPeriod;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\SubmissionReminder;
use App\Jobs\SendExpiredPeriodNotificationJob;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;

class TestingController extends Controller
{
    use GroupsEndingSoonSubmissionPeriods;

    public function create()
    {


        //   $this->getSubmissionPeriodsEndingSoonGroupedByUser(3);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
