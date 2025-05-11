<?php

namespace App\Imports\ImportFarmer;

use App\Models\JobProgress;
use App\Models\RtcProductionFarmer;
use App\Models\User;
use App\Traits\excelDateFormat;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RtcProductionFarmersImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    use RegistersEventListeners;
    use Importable, SkipsFailures;

    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;

    protected const BUNDLE_MULTIPLIER = 4;  // KG per Bundle

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
        $this->data = $data;
    }

    public function model(array $row)
    {
        // Create RtcProductionFarmer record without storing the 'ID' column in the database

        $user = User::find($this->data['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }


        $prodCalc = $this->calculateUsdValue($row['Production Value Date of Max Sales'], $row['Production Value Total']);
        $irrCalc = $this->calculateUsdValue($row['Irrigation Production Value Date of Max Sales'], $row['Irrigation Production Value Total']);

        $farmerRecord = RtcProductionFarmer::create([
            'group_name' => $row['Group Name'],
            'date_of_followup' => \Carbon\Carbon::parse($row['Date Of Follow Up'])->format('Y-m-d'),
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'number_of_plantlets_produced_cassava' => $row['Number of Plantlets Produced Cassava'] ?? 0,
            'number_of_plantlets_produced_potato' => $row['Number of Plantlets Produced Potato'] ?? 0,
            'number_of_plantlets_produced_sweet_potato' => $row['Number of Plantlets Produced Sweet Potato'] ?? 0,
            'number_of_screen_house_vines_harvested' => $row['Screen House Vines Harvested'] ?? 0,
            'number_of_screen_house_min_tubers_harvested' => $row['Screen House Min Tubers Harvested'] ?? 0,
            'number_of_sah_plants_produced' => $row['SAH Plants Produced'] ?? 0,
            'is_registered_seed_producer' => $row['Is Registered Seed Producer'],
            'uses_certified_seed' => $row['Uses Certified Seed'],
            'market_segment_fresh' => $row['Market Segment Fresh'],
            'market_segment_processed' => $row['Market Segment Processed'],
            'market_segment_seed' => $row['Market Segment Seed'],
            'market_segment_cuttings' => $row['Market Segment Cuttings'],
            'has_rtc_market_contract' => $row['Has RTC Market Contract'],
            'total_vol_production_previous_season' => $row['Total Volume Production'] ?? 0,
            'total_vol_production_previous_season_produce' => $row['Total Volume Production Produce'] ?? 0,
            'total_vol_production_previous_season_seed' => $row['Total Volume Production Seed'] ?? 0,
            'total_vol_production_previous_season_cuttings' => $row['Total Volume Production Cuttings'] ?? 0,
            'prod_value_previous_season_total' => $row['Production Value Total'] ?? 0,
            'prod_value_previous_season_produce' => $row['Production Value Produce'] ?? 0,
            'prod_value_previous_season_seed' => $row['Production Value Seed'] ?? 0,
            'prod_value_previous_season_cuttings' => $row['Production Value Cuttings'] ?? 0,
            'prod_value_produce_prevailing_price' => $row['Production Value Produce Prevailing Price'] ?? 0,
            'prod_value_seed_prevailing_price' => $row['Production Value Seed Prevailing Price'] ?? 0,
            'prod_value_cuttings_prevailing_price' => $row['Production Value Cuttings Prevailing Price'] ?? 0,
            'prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Production Value Date of Max Sales'])->format('Y-m-d'),
            'prod_value_previous_season_usd_rate' => $prodCalc['rate'] ?? 0,
            'prod_value_previous_season_usd_value' => $prodCalc['usd_value'] ?? 0,
            'total_vol_irrigation_production_previous_season' => $row['Total Volume Irrigation Production'] ?? 0,
            'total_vol_irrigation_production_previous_season_produce' => $row['Total Volume Irrigation Production Produce'] ?? 0,
            'total_vol_irrigation_production_previous_season_seed' => $row['Total Volume Irrigation Production Seeed'] ?? 0,
            'total_vol_irrigation_production_previous_season_cuttings' => $row['Total Volume Irrigation Production Cuttings'] ?? 0,
            'irr_prod_value_previous_season_total' => $row['Irrigation Production Value Total'] ?? 0,
            'irr_prod_value_previous_season_produce' => $row['Irrigation Production Value Produce'] ?? 0,
            'irr_prod_value_previous_season_seed' => $row['Irrigation Production Value Seed'] ?? 0,
            'irr_prod_value_previous_season_cuttings' => $row['Irrigation Production Value Cuttings'] ?? 0,
            'irr_prod_value_produce_prevailing_price' => $row['Irrigation Production Value Produce Prevailing Price'] ?? 0,
            'irr_prod_value_seed_prevailing_price' => $row['Irrigation Production Value Seed Prevailing Price'] ?? 0,
            'irr_prod_value_cuttings_prevailing_price' => $row['Irrigation Production Value Cuttings Prevailing Price'] ?? 0,
            'irr_prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Irrigation Production Value Date of Max Sales'])->format('Y-m-d'),
            'irr_prod_value_previous_season_usd_rate' => $irrCalc['rate'] ?? 0,
            'irr_prod_value_previous_season_usd_value' => $irrCalc['usd_value'] ?? 0,
            'sells_to_domestic_markets' => $row['Sells to Domestic Markets'] ?? 0,
            'sells_to_international_markets' => $row['Sells to International Markets'] ?? 0,
            'uses_market_information_systems' => $row['Uses Market Information Systems'] ?? 0,
            'sells_to_aggregation_centers' => $row['Sells to Aggregation Centers'] ?? 0,
            'total_vol_aggregation_center_sales' => $row['Total Volume Aggregation Center Sales'] ?? 0,
            'uuid' => $this->data['batch_no'],
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => $status,
            'total_vol_production_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Total Volume Production Seed'] / self::BUNDLE_MULTIPLIER) : 0,
            'prod_value_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Production Value Seed'] / self::BUNDLE_MULTIPLIER) : 0,
            'total_vol_irrigation_production_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Total Volume Irrigation Production Seeed'] / self::BUNDLE_MULTIPLIER) : 0,
            'irr_prod_value_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Irrigation Production Value Seed'] / self::BUNDLE_MULTIPLIER) : 0,
        ]);

        // Cache the mapping of 'ID' to primary key

        Cache::put("farmer_id_mapping1_{$this->cacheKey}_{$row['ID']}", $farmerRecord->id, now()->addMinutes(30));
        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $jobProgress->total_rows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }
        Log::info('Processed Farmer : ' . Cache::get("farmer_id_mapping1_{$this->cacheKey}_{$row['ID']}", $farmerRecord->id));
        return $farmerRecord;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Production Farmers' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    use excelDateFormat;

    public function prepareForValidation(array $row)
    {

        if (!empty($row['Date Of Follow Up'])) {
            $row['Date Of Follow Up'] = $this->convertExcelDate($row['Date Of Follow Up']);
        }
        if (!empty($row['Seed Producer Registration Date'])) {
            $row['Seed Producer Registration Date'] = $this->convertExcelDate($row['Seed Producer Registration Date']);
        }


        $row['Production Value Date of Max Sales'] = $row['Date Of Follow Up'];
        $row['Irrigation Production Value Date of Max Sales'] = $row['Date Of Follow Up'];

        if ($row['Enterprise'] && $row['Enterprise'] != 'Potato') {
            /** Convert the bundles to metric tonnes and use the multiplier */
            $row['Total Volume Production Seed'] = $this->convertToMetricTonnes($row['Total Volume Production Seed']);
            $row['Total Volume Irrigation Production Seeed'] = $this->convertToMetricTonnes($row['Total Volume Irrigation Production Seeed']);
        }

        $row['Total Volume Production'] = ($row['Total Volume Production Produce'] ?? 0) + ($row['Total Volume Production Seed'] ?? 0) + ($row['Total Volume Production Cuttings'] ?? 0);
        $row['Total Volume Irrigation Production'] = ($row['Total Volume Irrigation Production Produce'] ?? 0) + ($row['Total Volume Irrigation Production Seeed'] ?? 0) + ($row['Total Volume Irrigation Production Cuttings'] ?? 0);

        $row['Production Value Total'] = $this->calculateTotalProduction(
            $row['Production Value Produce'],
            $row['Production Value Produce Prevailing Price'],
            $row['Production Value Seed'],
            $row['Production Value Seed Prevailing Price'],
            $row['Production Value Cuttings'],
            $row['Production Value Cuttings Prevailing Price']
        );

        $row['Irrigation Production Value Total'] = $this->calculateTotalProduction(
            $row['Irrigation Production Value Produce'],
            $row['Irrigation Production Value Produce Prevailing Price'],
            $row['Irrigation Production Value Seed'],
            $row['Irrigation Production Value Seed Prevailing Price'],
            $row['Irrigation Production Value Cuttings'],
            $row['Irrigation Production Value Cuttings Prevailing Price']
        );
        $row['Production Value USD Rate'] = 0;  // for now
        $row['Production Value USD Value'] = 0;  // for now
        $row['Irrigation Production Value USD Rate'] = 0;  // for now
        $row['Irrigation Production Value USD Value'] = 0;  // for now

        return $row;
    }

    public function convertToMetricTonnes($value)
    {
        return ($value ?? 0) * self::BUNDLE_MULTIPLIER;
    }

    public function calculateTotalProduction($produce, $producePrevailingPrice, $seed, $seedPrevailingPrice, $cuttings, $cuttingsPrevailingPrice)
    {
        $totalProduction = (($produce ?? 0) * ($producePrevailingPrice ?? 0))
            + (($seed ?? 0) * ($seedPrevailingPrice ?? 0))
            + (($cuttings ?? 0) * ($cuttingsPrevailingPrice ?? 0));
        return $totalProduction;
    }

    public function rules(): array
    {
        return [
            'Group Name' => 'required|string|max:255',
            'Date Of Follow Up' => 'required|date|date_format:d-m-Y',
            'EPA' => 'required|string|max:255',
            'Section' => 'required|string|max:255',
            'District' => 'required|string|max:255',
            'Enterprise' => 'required|string|max:255',
            'Number of Plantlets Produced Cassava' => 'required|numeric|min:0',
            'Number of Plantlets Produced Potato' => 'required|numeric|min:0',
            'Number of Plantlets Produced Sweet Potato' => 'required|numeric|min:0',
            'Screen House Vines Harvested' => 'required|numeric|min:0',
            'Screen House Min Tubers Harvested' => 'required|numeric|min:0',
            'SAH Plants Produced' => 'required|numeric|min:0',
            'Is Registered Seed Producer' => 'nullable|boolean',
            'Uses Certified Seed' => 'nullable|boolean',
            'Market Segment Fresh' => 'nullable|boolean',
            'Market Segment Processed' => 'nullable|boolean',
            'Market Segment Seed' => 'nullable|boolean',
            'Market Segment Cuttings' => 'nullable|boolean',
            'Has RTC Market Contract' => 'nullable|boolean',
            'Total Volume Production' => 'nullable|numeric|min:0',
            'Total Volume Production Produce' => 'nullable|numeric|min:0',
            'Total Volume Production Seed' => 'nullable|numeric|min:0',
            'Total Volume Production Cuttings' => 'nullable|numeric|min:0',
            'Production Value Total' => 'nullable|numeric|min:0',
            'Production Value Produce' => 'nullable|numeric|min:0',
            'Production Value Seed' => 'nullable|numeric|min:0',
            'Production Value Cuttings' => 'nullable|numeric|min:0',
            'Production Value Produce Prevailing Price' => 'nullable|numeric|min:0',
            'Production Value Seed Prevailing Price' => 'nullable|numeric|min:0',
            'Production Value Cuttings Prevailing Price' => 'nullable|numeric|min:0',
            'Total Volume Irrigation Production' => 'nullable|numeric|min:0',
            'Total Volume Irrigation Production Produce' => 'nullable|numeric|min:0',
            'Total Volume Irrigation Production Seeed' => 'nullable|numeric|min:0',
            'Total Volume Irrigation Production Cuttings' => 'nullable|numeric|min:0',
            'Irrigation Production Value Total' => 'nullable|numeric|min:0',
            'Irrigation Production Value Produce' => 'nullable|numeric|min:0',
            'Irrigation Production Value Seed' => 'nullable|numeric|min:0',
            'Irrigation Production Value Cuttings' => 'nullable|numeric|min:0',
            'Irrigation Production Value Produce Prevailing Price' => 'nullable|numeric|min:0',
            'Irrigation Production Value Seed Prevailing Price' => 'nullable|numeric|min:0',
            'Irrigation Production Value Cuttings Prevailing Price' => 'nullable|numeric|min:0',
            'Sells to Domestic Markets' => 'nullable|boolean',
            'Sells to International Markets' => 'nullable|boolean',
            'Uses Market Information Systems' => 'nullable|boolean',
            'Sells to Aggregation Centers' => 'nullable|boolean',
            'Total Volume Aggregation Center Sales' => 'nullable|numeric|min:0'
        ];
    }

    public function startRow(): int
    {
        return 3;
    }

    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (!$date || !$mwkValue) {
            return ['rate' => 0, 'usd_value' => 0];
        }

        try {
            $helper = new \App\Helpers\ExchangeRateHelper();
            $rate = $helper->getRate($mwkValue, $date);
            $usdValue = $rate ? round($mwkValue / $rate, 2) : 0;
            return ['rate' => $rate, 'usd_value' => $usdValue];
        } catch (\Exception $e) {
            Log::error("Exchange rate calc error: " . $e->getMessage());
            return ['rate' => 0, 'usd_value' => 0];
        }
    }
}
