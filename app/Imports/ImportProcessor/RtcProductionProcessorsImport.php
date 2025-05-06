<?php

namespace App\Imports\ImportProcessor;

use App\Models\JobProgress;
use App\Models\RtcProductionProcessor;
use App\Models\User;
use App\Traits\excelDateFormat;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class RtcProductionProcessorsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithStartRow
{
    protected $data;
    protected $cacheKey;
    protected $totalRows = 0;

    protected const BUNDLE_MULTIPLIER = 4;  // KG per Bundle

    public function __construct($data, $cacheKey, $totalRows)
    {
        $this->data = $data;
        $this->cacheKey = $cacheKey;
        $this->totalRows = $totalRows;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $errorMessage = "Validation Error on sheet 'Production Processors' - Row {$failure->row()}, Field '{$failure->attribute()}': "
                . implode(', ', $failure->errors());

            Log::error($errorMessage);
            throw new \App\Exceptions\UserErrorException($errorMessage);
        }
    }

    public function model(array $row)
    {
        // Create a new RtcProductionProcessor record
        $user = User::find($this->data['user_id']);
        $status = 'pending';
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin')) {
            $status = 'approved';
        }

        $prodCalc = $this->calculateUsdValue($row['Production Value Date of Max Sales'], $row['Production Value Total']);

        $processorRecord = RtcProductionProcessor::create([
            'group_name' => $row['Group Name'],
            'date_of_followup' => \Carbon\Carbon::parse($row['Date Of Follow Up'])->format('Y-m-d'),
            'epa' => $row['EPA'],
            'section' => $row['Section'],
            'district' => $row['District'],
            'enterprise' => $row['Enterprise'],
            'market_segment_fresh' => $row['Market Segment Fresh'],
            'market_segment_processed' => $row['Market Segment Processed'],
            'has_rtc_market_contract' => $row['Has RTC Market Contract'],
            'total_vol_production_previous_season' => $row['Total Volume Production'],
            'total_vol_production_previous_season_produce' => $row['Total Volume Production Produce'],
            'total_vol_production_previous_season_seed' => $row['Total Volume Production Seeed'],
            'total_vol_production_previous_season_cuttings' => $row['Total Volume Production Cuttings'],
            'prod_value_previous_season_usd_rate' => $prodCalc['rate'],
            'prod_value_previous_season_usd_value' => $prodCalc['usd_value'],
            'prod_value_previous_season_seed' => $row['Production Value Seed'],
            'prod_value_previous_season_cuttings' => $row['Production Value Cuttings'],
            'prod_value_produce_prevailing_price' => $row['Production Value Produce Prevailing Price'],
            'prod_value_seed_prevailing_price' => $row['Production Value Seed Prevailing Price'],
            'prod_value_cuttings_prevailing_price' => $row['Production Value Cuttings Prevailing Price'],
            'prod_value_previous_season_date_of_max_sales' => \Carbon\Carbon::parse($row['Production Value Date of Max Sales'])->format('Y-m-d'),
            'prod_value_previous_season_usd_rate' => $row['Production Value USD Rate'],
            'prod_value_previous_season_usd_value' => $row['Production Value USD Value'],
            'sells_to_domestic_markets' => $row['Sells to Domestic Markets'],
            'sells_to_international_markets' => $row['Sells to International Markets'],
            'uses_market_information_systems' => $row['Uses Market Info Systems'],
            'sells_to_aggregation_centers' => $row['Sells to Aggregation Centers'],
            'total_vol_aggregation_center_sales' => $row['Total Volume Aggregation Center Sales'],
            'uuid' => $this->data['batch_no'],
            'user_id' => $this->data['user_id'],
            'organisation_id' => $this->data['organisation_id'],
            'submission_period_id' => $this->data['submission_period_id'],
            'financial_year_id' => $this->data['financial_year_id'],
            'period_month_id' => $this->data['period_month_id'],
            'status' => $status,
            'total_vol_production_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Total Volume Production Seeed'] / self::BUNDLE_MULTIPLIER) : 0,
            'prod_value_previous_season_seed_bundle' => $row['Enterprise'] != 'Potato' ? ($row['Production Value Seed'] / self::BUNDLE_MULTIPLIER) : 0,

        ]);

        // Cache the mapping of 'ID' to primary key
        Cache::put("processor_id_mapping_{$this->cacheKey}_{$row['ID']}", $processorRecord->id, now()->addMinutes(30));

        // Update JobProgress tracking
        $jobProgress = JobProgress::where('cache_key', $this->cacheKey)->first();
        if ($jobProgress) {
            $jobProgress->increment('processed_rows');
            $progress = ($jobProgress->processed_rows / $this->totalRows) * 100;
            $jobProgress->update(['progress' => round($progress)]);
        }

        return $processorRecord;
    }

    use excelDateFormat;

    public function prepareForValidation(array $row)
    {
        $row['Date Of Follow Up'] = $this->convertExcelDate($row['Date Of Follow Up'], $row);
        $row['Production Value Date of Max Sales'] = $row['Date Of Follow Up'];
        if ($row['Enterprise'] && $row['Enterprise'] != 'Potato') {
            /** Convert the bundles to metric tonnes and use the multiplier */
            $row['Total Volume Production Seeed'] = $this->convertToMetricTonnes($row['Total Volume Production Seeed']);
        }
        $row['Total Volume Production'] = ($row['Total Volume Production Produce'] ?? 0) + ($row['Total Volume Production Seeed'] ?? 0) + ($row['Total Volume Production Cuttings'] ?? 0);

        $row['Production Value Total'] = $this->calculateTotalProduction(
            $row['Total Volume Production Produce'],
            $row['Production Value Produce Prevailing Price'],
            $row['Total Volume Production Seeed'],
            $row['Production Value Seed Prevailing Price'],
            $row['Total Volume Production Cuttings'],
            $row['Production Value Cuttings Prevailing Price']
        );
        $row['Production Value USD Rate'] = 0;  // for now
        $row['Production Value USD Value'] = 0;  // for now
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
            'Market Segment Fresh' => 'nullable|boolean',
            'Market Segment Processed' => 'nullable|boolean',
            'Has RTC Market Contract' => 'nullable|boolean',
            'Total Volume Production' => 'nullable|numeric|min:0',
            'Total Volume Production Produce' => 'nullable|numeric|min:0',
            'Total Volume Production Seeed' => 'nullable|numeric|min:0',
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
            'Sells to Domestic Markets' => 'nullable|boolean',
            'Sells to International Markets' => 'nullable|boolean',
            'Uses Market Info Systems' => 'nullable|boolean',
            'Sells to Aggregation Centers' => 'nullable|boolean',
            'Total Volume Aggregation Center Sales' => 'nullable|numeric|min:0'
        ];
    }
    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (!$date || !$mwkValue) {
            return ['rate' => null, 'usd_value' => null];
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
    public function startRow(): int
    {
        return 3;
    }
}
