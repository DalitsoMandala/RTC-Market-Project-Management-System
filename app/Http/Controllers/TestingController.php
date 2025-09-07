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
use App\Models\GrossMarginCategory;
use Illuminate\Support\Facades\Bus;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\GrossMarginCategoryItem;
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

    public function test()
    {

        $data = [
            'categories' => [
  'Seed (Mbeu/Variety)' => [],
                'Land Preparation & Planting' => [
                    'Rent (Lendi ya malo)' => 'Acre',
                    'Land clearing (Kusosa/kutchetcha m\'munda kapena m\'dimba)' => 'Acre',
                    'Ploughing (Kugaula/kutipula)' => 'Acre',
                    'Ridging (Kukonza mizere)' => 'Acre',

                ],

                'Agricultural Operations' => [
                    'Planting (Kudzala mbeu)' => 'Acre',
                    'First weeding (Kupalira koyamba)' => 'Acre',
                    'Second weeding (Kapalira kachiwiri)' => 'Acre',
                    'Basal fertilizer (Feteleza oyamba)' => 'Acre',
                    'Top dressing fertilizer (Feteleza wachiwiri)' => 'Acre',
                    'Manure (Manyowa)' => 'Acre',
                    'Manure transport (Transipoti yotutira manyowa)' => 'Acre',
                    'Banding (Kukwezera/Kubandira)' => 'Acre',
                ],
                'Pest/Livestock/Theft control' => [
                    'Fencing (Kumanga mpanda)' => 'Each',
                    'Guards (Kulipira alonda)' => 'Labour/Materials',
                    'Pesticides' => 'Acre',
                    'Fungicides' => 'Acre',
                    'Hiring knapsack sprayers' => 'Each',
                    'Spraying (Kupopera mankhwala)' => 'Acre',

                ],
                'Harvesting (Kukolora)' => [
                    'Sacks (Matumba)' => 'Bag',
                    'Labour for harvesting (Aganyu okumba/odula)' => 'Kg/bundle',
                    'Labour for packing (Aganyu opakira mmatumba)' => 'Kg/bundle',
                    'Labour for loading and offloading (Aganyu okweza ndi kutsitsa matumba)' => 'Kg/bundle',
                    'Transport for harvest (Transipoti yotutila zokolola)' => 'Trip',

                ],
            ]


        ];

        foreach ($data['categories'] as $category => $items) {
            $cat =   GrossMarginCategory::firstOrCreate([
                'name' => $category,

            ]);
            foreach ($items as $item => $unit) {



                GrossMarginCategoryItem::firstOrCreate([
                    'gross_margin_category_id' => $cat->id,
                    'item_name' => $item,
                    'unit' => $unit,
                ]);
            }
        }
    }
}
