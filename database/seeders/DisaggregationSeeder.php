<?php

namespace Database\Seeders;

use App\Helpers\rtc_market\indicatorBuilder;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class DisaggregationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //

        $map = indicatorBuilder::builder();

        $newDisaggregations = [
            "A1" => ["Number of actors profitability engaged in commercialization of RTC" => ['Total', 'Female', 'Male', 'Youth (18-35 yrs)', 'Not youth (35yrs+)', 'Farmers', 'Processors', 'Traders', 'Employees on RTC establishment', 'Cassava', 'Potato', 'Sweet potato', 'New establishment', 'Old establishment']],
            "B1" => ["Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities" => ['Total', 'Cassava', 'Sweet potato', 'Potato']],
            "B2" => [
                "Percentage increase in value of formal RTC exports" => [
                    'Total',
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
            ],
            "B3" => ["Percentage of value ($) of formal RTC imports substituted through local production" => ['Total', 'Value: Volume(Metric Tonnes)', 'Financial value($)', 'Cassava', 'Potato', 'Sweet potato', 'Formal']],
            "B4" => ["Number of people consuming RTC and processed products" => ['Total', 'RTC actors and households', 'School feeding beneficiaries', 'Individuals from households reached with nutrition interventions']],
            "B5" => ["Percentage Increase in the volume of RTC produced" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Certified seed produce', 'Value added RTC products']],
            "B6" => ["Percentage increase in RTC investment" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "1.1.1" => ["Number of local RTC varieties suitable for domestic and export markets identified for promotion" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "1.1.2" => ["Number of potential market preferred RTC genotypes in the pipeline identified" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed']],
            "1.1.3" => ["Number of new RTC technologies developed" => ['Total', 'Improved RTC variety', 'Seed production', 'Storage', 'Agronomic production', 'Post-harvest processing', 'Cassava', 'Potato', 'Sweet potato']],
            "1.1.4" => ["Percentage increase in adoption of new RTC technologies" => ['Total', 'Improved RTC variety', 'Seed production', 'Storage', 'Agronomic production', 'Post-harvest processing', 'Cassava', 'Potato', 'Sweet potato']],
            "1.2.1" => ["Number of economic studies conducted" => ['Total']],
            "1.2.2" => ["Number of RTC and derived products recorded in official trade statistics" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed']],
            "1.3.1" => [
                "Number of policy briefs developed and shared on RTC topics" => ['Total'],
                "Number of existing agricultural programs that integrate RTC into their programs" => ['Total'],
            ],
            "2.1.1" => ["Number of market linkages between EGS and other seed class producers facilitated" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "2.2.1" => ["Number of private sector actors involved in production of RTC certified seed" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "2.2.2" => ["Area (ha) under seed multiplication" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified']],
            "2.2.3" => ["Percentage seed multipliers with formal registration" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified', 'POs', 'Individual farmers not in POs', 'Large scale farmers', 'Medium scale farmers']],
            "2.2.4" => ["Volume of seed distributed within communities to enhance POs productivity" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified']],
            "2.2.5" => ["Number of on-farm seed production technology demonstrations established" => ['Total']],
            "2.3.1" => [
                "Number of international learning visits for seed producers (OC)" => ['Total'],
                "Percentage business plans for the production of different classes of RTC seeds that are executed" => ['Total', 'POs', 'SME', 'Large scale commercial farmers', 'Cassava', 'Potato', 'Sweet potato'],
            ],
            "2.3.2" => ["Number of stakeholder engagement events that focus on RTC development" => ['Total', 'Seed production', 'Seed multiplication', 'Seed processing']],
            "2.3.3" => [
                "Number of registered seed producers accessing markets through online Market Information System (MIS)" => [
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
            ],
            "2.3.4" => [
                "Number of RTC actors linked to online Market Information System (MIS)" => [
                    'Total',
                    'Cassava',
                    'Potato',
                    'Sweet potato',
                    'Farmers',
                    'Traders',
                    'Transporters',
                    'Individual farmers not in POs',
                    'POs',
                    'Large scale commercial farmers',
                ],
            ],

            "2.3.5" => [
                "Number of RTC products available on the Management Information System" => [
                    'Total',
                    'Cassava',
                    'Potato',
                    'Sweet potato',
                    'Seed',
                    'Produce',
                    'Value added products',


                ],
            ],
            "3.1.1" => ["Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed']],
            "3.2.1" => ["Number of RTC actors that use certified seed" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "3.2.2" => ["Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Farmers', 'Processors', 'Traders', 'Partner', 'Staff']],
            "3.2.3" => ["Number of off-season irrigation demonstration sites established" => ['Total']],
            "3.2.4" => ["Number of demonstration sites for end-user preferred RTC varieties established" => ['Total']],
            "3.2.5" => ["Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)" => ['Total']],
            "3.3.1" => [
                "Number of market opportunities identified for RTC actors" => [
                    'Domestic markets',
                    'International markets',
                    'Imports',
                    'Exports',
                    'Seed',
                    'Produce',
                    'Value added products',
                ],
            ],
            "3.3.2" => [
                "Number of contractual arrangements facilitated for commercial farmers" => [
                    'Total',
                    'Cassava',
                    'Potato',
                    'Sweet potato',
                ],
            ],
            "3.4.1" => ["Number of RTC actors supported to access funds from financial service providers" => ['Total', 'Processors', 'Farmers', 'Large scale processors', 'SME', 'Loan', 'Input financing']],
            "3.4.2" => ["Number of POs that have formal contracts with buyers" => ['Total', 'Fresh', 'Processed']],
            "3.4.4" => ["Number of RTC POs selling products through aggregation centers aggregation center" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "3.4.5" => ["Volume (MT) of RTC products sold through collective marketing efforts by POs" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed']],
            "3.5.1" => ["Number of households reached with RTC nutrition interventions" => ['Total']],
            "3.5.2" => ["Frequency of RTC consumption by households per week (OC)" => ['Total']],
            "3.5.3" => ["Percentage increase in households consuming RTCs as the main foodstuff (OC)" => ['Total']],
            "3.5.4" => ["Number of RTC utilization options (dishes) adopted by households (OC)" => ['Total']],
            "3.5.5" => ["Number of urban market promotions conducted" => ['Total']],
            "3.5.6" => ["Number of mass nutrition education campaigns conducted" => ['Total']],
            "4.1.1" => ["Number of RTC value-added products promoted" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "4.1.2" => ["Number of RTC actors with MBS certification for producing (or processing) RTC products" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'SMEs', 'Large scale commercial farms', 'PO\'s']],
            "4.1.3" => ["Number of RTC value-added products developed for domestic markets" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "4.1.4" => ["Number of new RTC recipes/products adopted and branded by processors" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "4.1.5" => ["Number of domestic market opportunities identified for value-added products" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
            "4.1.6" => ["Number of international market opportunities identified for value-added products" => ['Total', 'Cassava', 'Potato', 'Sweet potato']],
        ];


        foreach ($newDisaggregations as $indicatorNo => $data) {
            $number = $indicatorNo;

            // Check if $data contains multiple keys
            if (is_array($data) && count($data) > 1) {
                foreach ($data as $name => $values) {
                    $indicator = Indicator::where('indicator_name', $name)->where('indicator_no', $number)->first();

                    if ($indicator) {
                        foreach ($values as $disag) {
                            IndicatorDisaggregation::create([
                                'name' => $disag,
                                'indicator_id' => $indicator->id,
                            ]);
                        }
                    } else {
                        Log::error("Indicator with number {$number} not found.");
                    }
                }
            } else {
                $name = key($data);
                $dataList = array_values($data)[0];
                $indicator = Indicator::where('indicator_name', $name)->where('indicator_no', $number)->first();

                if ($indicator) {
                    foreach ($dataList as $disag) {
                        IndicatorDisaggregation::create([
                            'name' => $disag,
                            'indicator_id' => $indicator->id,
                        ]);
                    }
                } else {
                    Log::error("Indicator with number {$number} not found.");
                }
            }
        }


    }
}