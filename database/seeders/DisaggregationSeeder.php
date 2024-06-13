<?php

namespace Database\Seeders;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use Illuminate\Database\Seeder;

class DisaggregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $disaggregations = [
            'A1' => ['Total', 'Female', 'Male', 'Youth (18-35 yrs)', 'Not youth (35yrs+)', 'Farmers', 'Processors', 'Traders', 'Employees on RTC establishment', 'Cassava', 'Potato', 'Sweet potato', 'New establishment', 'Old establishment'],
            'B1' => ['Total', 'Cassava', 'Sweet potato', 'Potato'],
            'B2' => ['Total',
                'Volume (Metric Tonnes)',
                'Financial value ($)',
                'Formal exports',
                'Informal exports',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Raw',
                'Processed',
            ],
            'B3' => [
                'Total',
                'Value: Volume(Metric Tonnes)',
                'Financial value($)',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Formal',
            ],

            'B4' => [
                'Total',
                'RTC actors and households',
                'School feeding beneficiaries',
            ],

            'B5' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
            ],
            '1.2.2' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed',
            ],

            '2.2.1' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
            ],

            '2.2.2' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Basic',
                'Certified',
            ],
            '2.2.3' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Basic',
                'Certified',
                'PO\'s',
                'Individual farmers not in POs',
            ],

            '2.3.4' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Domestic markets',
                'International markets',
                'Individual farmers not in POs',
                'POs',
                'Large scale commercial farmers',
            ],

            '3.1.1' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed',
            ],

            '3.2.1' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
            ],

            '3.2.5' => ['Total'],

            '3.4.2' => [
                'Total',
                'Fresh',
                'Processed',
            ],
            '3.4.4' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
            ],

            '3.4.5' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed',
            ],

            '3.5.2' => ['Total'],
            '3.5.3' => ['Total'],
            '3.5.4' => ['Total'],

            '4.1.2' => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'SME’s',
                'Large scale commercial farms',
                'PO\'s',
            ],

        ];

        // foreach ($disaggregations['A1'] as $data) {
        //     $indicator = Indicator::where('indicator_no', 'A1')->first();
        //     IndicatorDisaggregation::create([
        //         'name' => $data,
        //         'indicator_id' => $indicator->id,
        //     ]);

        // }

        foreach ($disaggregations as $indicatorNo => $dataList) {
            $indicator = Indicator::where('indicator_no', $indicatorNo)->first();

            if ($indicator) {
                foreach ($dataList as $data) {
                    IndicatorDisaggregation::create([
                        'name' => $data,
                        'indicator_id' => $indicator->id,
                    ]);
                }
            } else {
                // Handle case where indicator with given number ($indicatorNo) is not found
                // This could be logging an error or some other appropriate action
                // Example: Log::error("Indicator with number {$indicatorNo} not found.");
            }
        }

    }
}
