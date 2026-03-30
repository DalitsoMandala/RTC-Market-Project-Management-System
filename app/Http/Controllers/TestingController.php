<?php

namespace App\Http\Controllers;

use App\Exports\Reports\ReportSheet;
use App\Exports\RootTuberExport\RootTuberExportTemplate;
use App\Exports\RootTuberImport\RootTuberImportTemplate;
use App\Exports\rtcmarket\HouseholdExport\HrcExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionFarmerWorkbookExport;
use App\Exports\rtcmarket\RtcProductionExport\RtcProductionProcessorWookbookExport;
use App\Exports\rtcmarket\SchoolConsumptionExport\SrcExport;
use App\Helpers\ExchangeRateHelper;
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
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\Permission\Models\Role;
use EnsureNumericTrait;

class TestingController extends Controller
{
    use IndicatorsTrait;

    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (!$date || !$mwkValue) {
            return ['rate' => 0, 'usd_value' => 0];
        }

        try {
            $exchangeHelper = new ExchangeRateHelper();
            $rate = $exchangeHelper->getRate(null, $date);

            $usdValue = $rate ? round($mwkValue / $rate, 2) : 0;

            return [
                'rate' => $rate,
                'usd_value' => $usdValue
            ];
        } catch (\Exception $e) {

            Log::error("Exchange rate calc error: " . $e->getMessage());

            return ['rate' => 0, 'usd_value' => 0];
        }
    }
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

    private function removePrevailingPrice($total, $prevailingPrice)
    {
        if ($prevailingPrice == 0) {
            return $total;
        }
        return $total / $prevailingPrice;
    }

    private function sumValues(array $values): float
    {
        return collect($values)
            ->map(fn($v) => (float) ($v ?? 0))
            ->sum();
    }
    private function processProduction($modelClass, $includeIrrigation = false)
    {
        $modelClass::query()
            ->where('status', 'approved')
            ->chunkById(100, function ($records) use ($includeIrrigation) {

                foreach ($records as $record) {

                    DB::transaction(function () use ($record, $includeIrrigation) {

                        // --- MAIN PRODUCTION ---
                        $prodTotal = $this->sumValues([
                            $record->prod_value_previous_season_produce,
                            $record->prod_value_previous_season_seed,
                            $record->prod_value_previous_season_cuttings
                        ]);

                        $prodUsd = $this->calculateUsdValue(
                            $record->prod_value_previous_season_date_of_max_sales,
                            $prodTotal
                        );

                        $updateData = [
                            'prod_value_previous_season_total' => $prodTotal,
                            'prod_value_previous_season_usd_rate' => $prodUsd['rate'],
                            'prod_value_previous_season_usd_value' => $prodUsd['usd_value'],
                        ];

                        // --- IRRIGATION (ONLY FOR FARMERS) ---
                        if ($includeIrrigation) {

                            $irrTotal = $this->sumValues([
                                $this->removePrevailingPrice(
                                    $record->irr_prod_value_previous_season_produce,
                                    $record->irr_prod_value_produce_prevailing_price
                                ),
                                $this->removePrevailingPrice(
                                    $record->irr_prod_value_previous_season_seed,
                                    $record->irr_prod_value_seed_prevailing_price
                                ),
                                $this->removePrevailingPrice(
                                    $record->irr_prod_value_previous_season_cuttings,
                                    $record->irr_prod_value_cuttings_prevailing_price
                                ),
                            ]);

                            $irrUsd = $this->calculateUsdValue(
                                $record->irr_prod_value_previous_season_date_of_max_sales,
                                $irrTotal
                            );

                            $updateData = array_merge($updateData, [
                                'irr_prod_value_previous_season_total' => $irrTotal,
                                'irr_prod_value_previous_season_usd_rate' => $irrUsd['rate'],
                                'irr_prod_value_previous_season_usd_value' => $irrUsd['usd_value'],
                            ]);
                        }

                        $record->update($updateData);
                    });
                }
            });
    }
    public function recalculations()
    {
        try {
            $this->processProduction(RtcProductionFarmer::class, true);
            $this->processProduction(RtcProductionProcessor::class, false);

            return response()->json([
                'status' => 'success',
                'message' => 'Recalculations completed successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error("Recalculation error: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred during recalculations.'
            ]);
        }
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
