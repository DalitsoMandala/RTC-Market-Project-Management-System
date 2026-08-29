<?php
namespace App\Imports\MarketingLog;

use App\Models\JobProgress;
use App\Models\ProductionMarketingLog;
use App\Models\User;
use App\Traits\excelDateFormat;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');

class MarketingLogImport implements
ToModel,
WithHeadingRow,
WithValidation,
SkipsOnFailure,
WithStartRow
{
    use RegistersEventListeners;
    use Importable, SkipsFailures;
    use excelDateFormat;
    protected array $data;
    protected string $cacheKey;
    protected $totalRows = 0;

    public function __construct(array $data, string $cacheKey, int $totalRows)
    {
        $this->cacheKey  = $cacheKey;
        $this->totalRows = $totalRows;
        $this->data      = $data;
    }

    public function model(array $row)
    {
        /*
        |--------------------------------------------------------------------------
        | Determine status
        |--------------------------------------------------------------------------
        */

        $user = User::find($this->data['user_id']);

        $status = 'pending';

        if ($user && $user->hasAnyRole('manager|admin')) {
            $status = 'approved';
        }
        $usdValue = 0;
        $usdRate  = 0;
        $calc     = $this->calculateUsdValue($row['Date Recorded'], $row['Selling price'] ?? 0);
        $usdValue = $calc['usd_value'];
        $usdRate  = $calc['rate'];
        /*
        |--------------------------------------------------------------------------
        | Create Production & Marketing record
        |--------------------------------------------------------------------------
        */

        $productionMarketing = ProductionMarketingLog::create([

            // Location
            'district'              => $row['District'] ?? null,
            'epa'                   => $row['EPA'] ?? null,
            'section'               => $row['Section'] ?? null,

            // Production information
            'enterprise'            => $row['Crop'] ?? null,
            'group_name'            => $row['Name of group'] ?? null,
            'type_of_farming'       => $row['Type of farming'] ?? null,
            'season'                => $row['Season'] ?? null,

            // Group chair
            'group_chair_name'      => $row['Name of group Chair'] ?? null,
            'group_chair_contact'   => $row['Contact of group chair'] ?? null,

            // Farmer
            'farmer_name'           => $row['Name of farmer'] ?? null,
            'farmer_id_phone'       => $row['ID No/Phone No.'] ?? null,
            'sex'                   => $row['Sex'] ?? null,
            'age'                   => $row['Age'] ?? null,

            // Production
            'area_grown_acre'       => $row['Area grown (acre)'] ?? null,
            'variety'               => strtolower($row['Variety']) ?? null,
            'harvesting_units'      => $row['Harvesting units'] ?? null,
            'unit_weight_kg'        => $row['Unit weight (Kg)'] ?? null,
            'qty'                   => $row['QTY'] ?? null,

            // Marketing
            'selling_price'         => $row['Selling price'] ?? null,
            'main_buyer'            => $row['Main buyer'] ?? null,

            // Seed
            'seed_class'            => $row['Seed Class'] ?? null,
            'date_recorded'         => Carbon::parse($row['Date Recorded'])->format('Y-m-d') ?? null,
            'production_value_usd'  => $usdValue,
            'production_value_rate' => $usdRate,

            /*
            |--------------------------------------------------------------------------
            | System fields
            |--------------------------------------------------------------------------
            */

            'uuid'                  => $this->data['batch_no'],

            'user_id'               => $this->data['user_id'],

            'organisation_id'       => $this->data['organisation_id'],

            'submission_period_id'  => $this->data['submission_period_id'],

            'financial_year_id'     => $this->data['financial_year_id'],

            'period_month_id'       => $this->data['period_month_id'],

            'status'                => $status,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Cache Excel ID -> Database ID
        |--------------------------------------------------------------------------
        */

        // Cache::put(
        //     "production_marketing_id_mapping_{$this->cacheKey}_{$row['ID']}",
        //     $productionMarketing->id,
        //     now()->addMinutes(30)
        // );

        /*
        |--------------------------------------------------------------------------
        | Update Job Progress
        |--------------------------------------------------------------------------
        */

        $jobProgress = JobProgress::where(
            'cache_key',
            $this->cacheKey
        )->first();

        if ($jobProgress) {

            $jobProgress->increment('processed_rows');

            $progress = (
                $jobProgress->processed_rows /
                $jobProgress->total_rows
            ) * 100;

            $jobProgress->update([
                'progress' => round($progress),
            ]);
        }

        return $productionMarketing;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation failures
    |--------------------------------------------------------------------------
    */

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {

            $errorMessage =
            "Validation Error on sheet 'Production and Marketing Log' - " .
            "Row {$failure->row()}, " .
            "Field '{$failure->attribute()}': " .
            implode(', ', $failure->errors());

            Log::error($errorMessage, [
                'row'       => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors'    => $failure->errors(),
                'values'    => $failure->values(),

            ]);

            throw new \App\Exceptions\UserErrorException(
                $errorMessage
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Prepare data before validation
    |--------------------------------------------------------------------------
    */

    public function prepareForValidation(array $row)
    {
        $row['EPA']           = $row['EPA'] ?? '';
        $row['Section']       = $row['Section'] ?? '';
        $row['District']      = $row['District'] ?? '';
        $row['Sex']           = trim($row['Sex']) ?? '';
        $row['Date Recorded'] = $this->convertExcelDate($row['Date Recorded']);
        return $row;
    }

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    */

    public function rules(): array
    {
        return [

            'District'               => 'nullable|string|max:255',

            'EPA'                    => 'nullable|string|max:255',

            'Section'                => 'nullable|string|max:255',

            'Crop'                   => [
                'required',
                'string',
                'max:255',
                'in:Cassava,Potato,Sweet potato',
            ],

            'Name of group'          => 'nullable|string|max:255',

            'Type of farming'        => 'nullable|string|in:Seed,Table Potato,Cuttings',

            'Season'                 => 'nullable|string|max:255',

            'Name of group Chair'    => 'nullable|string|max:255',

            'Contact of group chair' => 'nullable|max:255',

            'Name of farmer'         => 'nullable|string|max:255',

            'ID No/Phone No.'        => 'nullable|max:255',

            'Sex'                    => [
                'nullable',
                'string',
                'max:255',
                'in:Male,Female',
            ],

            'Age'                    => 'nullable|numeric|min:0|max:120',

            'Area grown (acre)'      => 'nullable|numeric|min:0',

            'Variety'                => 'nullable|string|max:255',

            'Harvesting units'       => 'nullable|string|max:255',

            'Unit weight (Kg)'       => 'nullable|numeric|min:0',

            'QTY'                    => 'nullable|numeric|min:0',

            'Selling price'          => 'nullable|numeric|min:0',

            'Main buyer'             => 'nullable|string|max:255',

            'Seed Class'             => 'required_if:Type of farming,Seed|nullable|string|max:255',

            'Date Recorded'          => 'required|date|date_format:d-m-Y',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Starting row
    |--------------------------------------------------------------------------
    */
    private function calculateUsdValue(?string $date, ?float $mwkValue): array
    {
        if (! $date || ! $mwkValue) {
            return ['rate' => 0, 'usd_value' => 0];
        }

        try {
            $helper   = new \App\Helpers\ExchangeRateHelper();
            $rate     = $helper->getRate($mwkValue, $date);
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
