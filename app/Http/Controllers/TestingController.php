<?php

namespace App\Http\Controllers;

use App\Exports\Reports\ReportSheet;
use App\Exports\RootTuberExport\RootTuberExportTemplate;
use App\Exports\RootTuberImport\RootTuberImportTemplate;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Helpers\UsdReCalculations;
use App\Jobs\SendExpiredPeriodNotificationJob;

use App\Jobs\sendReminderToUserJob;
use App\Models\FinancialYear;
use App\Models\GrossMarginCategory;
use App\Models\GrossMarginCategoryItem;
use App\Models\Indicator;
use App\Models\MailingList;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ReportStatus;
use App\Models\ResponsiblePerson;
use App\Models\RtcProductionFarmer;
use App\Models\RtcProductionProcessor;
use App\Models\SubmissionPeriod;
use App\Models\SubmissionTarget;
use App\Models\User;
use App\Notifications\SubmissionPeriodsEndingSoon;
use App\Notifications\SubmissionReminder;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use App\Traits\IndicatorsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;

class TestingController extends Controller
{
    use IndicatorsTrait;

    public function testSubmissions()
    {
        return $this->notifyExpiredSubmissionPeriods();
    }
    public function addNewRole()
    {
        Role::firstOrCreate([
            'name' => 'monitor',
        ]);

        $user = new User();
        $user->name = 'monitor';
        $user->email = 'info@monitor.com';
        $user->password = Hash::make('password');
        $user->phone_number = '1234567890';
        $user->organisation_id = 1;
        $user->save();

        $user->assignRole('monitor');

        return response()->json(['status' => 'success']);
    }


    public function fix()
    {
        //Production
        ini_set('max_execution_time', 0); // Infinite execution time
        set_time_limit(0); // Infinite execution time
        $production = RtcProductionFarmer::query()
        ->where('prod_value_previous_season_usd_rate',0)
        ->where('prod_value_previous_season_usd_value',0)
        ->where('status', 'approved');
        $class = new UsdReCalculations();
        return    $class->checkRowsThatHaveNoUsdValue($production, true);
    }
    public function fix2()
    {
        //Production
        ini_set('max_execution_time', 0); // Infinite execution time
        set_time_limit(0); // Infinite execution time
        $production = RtcProductionProcessor::query()
           ->where('prod_value_previous_season_usd_rate',0)
        ->where('prod_value_previous_season_usd_value',0)
        ->where('status', 'approved');
        $class = new UsdReCalculations();
        return    $class->checkRowsThatHaveNoUsdValue($production, false);
    }
    public function test()
    {

        // $data = [
        //     'categories' => [
        //         'Seed (Mbeu/Variety)' => [],
        //         'Land Preparation & Planting' => [
        //             'Rent (Lendi ya malo)' => 'Acre',
        //             'Land clearing (Kusosa/kutchetcha m\'munda kapena m\'dimba)' => 'Acre',
        //             'Ploughing (Kugaula/kutipula)' => 'Acre',
        //             'Ridging (Kukonza mizere)' => 'Acre',

        //         ],

        //         'Agricultural Operations' => [
        //             'Planting (Kudzala mbeu)' => 'Acre',
        //             'First weeding (Kupalira koyamba)' => 'Acre',
        //             'Second weeding (Kapalira kachiwiri)' => 'Acre',
        //             'Basal fertilizer (Feteleza oyamba)' => 'Acre',
        //             'Top dressing fertilizer (Feteleza wachiwiri)' => 'Acre',
        //             'Manure (Manyowa)' => 'Acre',
        //             'Manure transport (Transipoti yotutira manyowa)' => 'Acre',
        //             'Banding (Kukwezera/Kubandira)' => 'Acre',
        //         ],
        //         'Pest/Livestock/Theft control' => [
        //             'Fencing (Kumanga mpanda)' => 'Each',
        //             'Guards (Kulipira alonda)' => 'Labour/Materials',
        //             'Pesticides' => 'Acre',
        //             'Fungicides' => 'Acre',
        //             'Hiring knapsack sprayers' => 'Each',
        //             'Spraying (Kupopera mankhwala)' => 'Acre',

        //         ],
        //         'Harvesting (Kukolora)' => [
        //             'Sacks (Matumba)' => 'Bag',
        //             'Labour for harvesting (Aganyu okumba/odula)' => 'Kg/bundle',
        //             'Labour for packing (Aganyu opakira mmatumba)' => 'Kg/bundle',
        //             'Labour for loading and offloading (Aganyu okweza ndi kutsitsa matumba)' => 'Kg/bundle',
        //             'Transport for harvest (Transipoti yotutila zokolola)' => 'Trip',

        //         ],
        //     ]


        // ];

        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // GrossMarginCategoryItem::truncate();
        // GrossMarginCategory::truncate();
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // foreach ($data['categories'] as $category => $items) {
        //     $cat =   GrossMarginCategory::create([
        //         'name' => $category,

        //     ]);
        //     foreach ($items as $item => $unit) {



        //         GrossMarginCategoryItem::create([
        //             'gross_margin_category_id' => $cat->id,
        //             'item_name' => $item,
        //             'unit' => $unit,
        //         ]);
        //     }
        // }
    }


    public function export()
    {
        $user = auth()->user();
        Excel::store(new ReportSheet($user), 'public.xlsx', 'public');


        // return Excel::download(new ReportSheet($user), 'report.xlsx');
    }

    public function correctPeriods()
    {
        $periods = ReportingPeriodMonth::with(['reportingPeriod'])->whereHas('reportingPeriod', function ($q) {
            $q->where('name', 'QUARTERLY');
        })->get();
        foreach ($periods as $period) {
            if ($period->type == 'QUARTER 1') {
                $period->update([
                    'start_month' => 'MAY',
                    'end_month' => 'JULY'
                ]);
            }
            if ($period->type == 'QUARTER 2') {
                $period->update([
                    'start_month' => 'AUGUST',
                    'end_month' => 'OCTOBER'
                ]);
            }
            if ($period->type == 'QUARTER 3') {
                $period->update([
                    'start_month' => 'NOVEMBER',
                    'end_month' => 'JANUARY'
                ]);
            }
            if ($period->type == 'QUARTER 4') {
                $period->update([
                    'start_month' => 'FEBRUARY',
                    'end_month' => 'APRIL'
                ]);
            }
        }
    }

    public function downloadTemplates()
    {
        return Excel::download(new RootTuberImportTemplate(true), 'import.xlsx');
    }
    use IndicatorsTrait;
    public function notify()
    {
        //return  $this->getEndingSoonSubmissionPeriods();

    }
}
