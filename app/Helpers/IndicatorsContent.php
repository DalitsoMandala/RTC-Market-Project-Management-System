<?php

namespace App\Helpers;


use App\Models\Indicator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class IndicatorsContent
{
    public $id;
    public $name;
    public $number;

    public function __construct($id = null, $name = null, $number = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->number = $number;
    }

    public function content(): Collection
    {
        $indicatorArray = $this->getCachedIndicatorArray();
        $indicatorCalculations = $this->getCachedIndicatorCalculations();

        if ($this->name && $this->number) {
            return $this->getContentByNameAndNumber($indicatorArray, $indicatorCalculations);
        }

        if ($this->id) {
            return $this->getContentById($indicatorArray, $indicatorCalculations);
        }
        if ($this->name) {
            return $this->getContentByName($indicatorArray, $indicatorCalculations);
        }
        return collect();
    }


    private function getContentByNameAndNumber(Collection $indicatorArray, Collection $indicatorCalculations): Collection
    {
        $query = $indicatorArray->where('indicator_name', $this->name)
            ->where('indicator_no', $this->number)
            ->first();

        if ($query) {
            $query['class'] = $this->getClassForIndicator($indicatorCalculations, $this->name);
        }

        return collect($query);
    }

    private function getContentByName(Collection $indicatorArray, Collection $indicatorCalculations): Collection
    {
        $query = $indicatorArray->where('indicator_name', $this->name)

            ->first();

        if ($query) {
            $query['class'] = $this->getClassForIndicator($indicatorCalculations, $this->name);
        }

        return collect($query);
    }


    private function getClassForIndicator(Collection $indicatorCalculations, string $indicatorName): ?string
    {
        $classes = $indicatorCalculations->where('indicator_name', $indicatorName)->first();
        return $classes['class'] ?? null;
    }


    private function getCachedIndicatorCalculations(): Collection
    {
        return Cache::remember('indicator_calculations', 3600, function () {
            return self::indicatorCalculations();
        });
    }

    private function getCachedIndicatorArray(): Collection
    {
        return Cache::remember('indicator_array', 3600, function () {
            return self::indicatorArray();
        });
    }
    private function getContentById(Collection $indicatorArray, Collection $indicatorCalculations): Collection
    {
        $query = $indicatorArray->where('id', $this->id)->first();

        if ($query) {
            $indicator = Indicator::find($this->id);
            if ($indicator) {
                $query['class'] = $this->getClassForIndicator($indicatorCalculations, $indicator->indicator_name);
            } else {
                Log::warning("No indicator found for ID: {$this->id}");
            }
        } else {
            Log::warning("No indicator data found for ID: {$this->id}");
        }

        return collect($query);
    }

    public static function indicatorCalculations(): Collection
    {
        return collect([
            [

                "indicator_name" => "Number of actors profitability engaged in commercialization of RTC",
                'class' => \App\Helpers\rtc_market\indicators\indicator_A1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in value of formal RTC exports",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage of value ($) of formal RTC imports substituted through local production",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of people consuming RTC and processed products",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage Increase in the volume of RTC produced",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in RTC investment",
                'class' => \App\Helpers\rtc_market\indicators\indicator_B6::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of local RTC varieties suitable for domestic and export markets identified for promotion",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of potential market preferred RTC genotypes in the pipeline identified",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_1_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of new RTC technologies developed",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_1_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in adoption of new RTC technologies",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_1_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of economic studies conducted",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_2_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC and derived products recorded in official trade statistics",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_2_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of existing agricultural programs that integrate RTC into their programs",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_3_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of policy briefs developed and shared on RTC topics",
                'class' => \App\Helpers\rtc_market\indicators\indicator_1_3_1_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of market linkages between EGS and other seed class producers facilitated",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of private sector actors involved in production of RTC certified seed",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_2_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Area (ha) under seed multiplication",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_2_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage seed multipliers with formal registration",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_2_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Volume of seed distributed within communities to enhance POs productivity",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_2_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of on-farm seed production technology demonstrations established",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_2_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of international learning visits for seed producers (OC)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage business plans for the production of different classes of RTC seeds that are executed",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_1_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of stakeholder engagement events that focus on RTC development",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of registered seed producers accessing markets through online Market Information System (MIS)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC actors linked to online Market Information System (MIS)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC products available on the Management Information System",
                'class' => \App\Helpers\rtc_market\indicators\indicator_2_3_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC actors that use certified seed",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_2_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_2_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of off-season irrigation demonstration sites established",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_2_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of demonstration sites for end-user preferred RTC varieties established",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_2_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_2_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of market opportunities identified for RTC actors",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_3_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of contractual arrangements facilitated for commercial farmers",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_3_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC actors supported to access funds from financial service providers",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_4_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of POs that have formal contracts with buyers",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_4_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC aggregation centers established",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_4_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC POs selling products through aggregation centers",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_4_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Volume (MT) of RTC products sold through collective marketing efforts by POs",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_4_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of households reached with RTC nutrition interventions",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Frequency of RTC consumption by households per week (OC)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in households consuming RTCs as the main foodstuff (OC)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC utilization options (dishes) adopted by households (OC)",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of urban market promotions conducted",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of mass nutrition education campaigns conducted",
                'class' => \App\Helpers\rtc_market\indicators\indicator_3_5_6::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC value-added products promoted",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC actors with MBS certification for producing (or processing) RTC products",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC value-added products developed for domestic markets",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_3::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of new RTC recipes/products adopted and branded by processors",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_4::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of domestic market opportunities identified for value-added products",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_5::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of international market opportunities identified for value-added products",
                'class' => \App\Helpers\rtc_market\indicators\indicator_4_1_6::class,
                'project_name' => 'RTC MARKET',
            ],
        ]);
    }

    public static function indicatorDisaggregation(): Collection
    {
        return collect([
            "Number of actors profitability engaged in commercialization of RTC" => [
                'Total',
                'Female',
                'Male',
                'Youth (18-35 yrs)',
                'Not youth (35yrs+)',
                'Farmers',
                'Processors',
                'Traders',
                'Employees on RTC establishment',
                'Cassava',
                'Potato',
                'Sweet potato',
                'New establishment',
                'Old establishment',
            ],
            "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities" => [
                'Total (% Percentage)',
                'Cassava',
                'Sweet potato',
                'Potato',
            ],
            "Percentage increase in value of formal RTC exports" => [
                'Total (% Percentage)',
                'Volume (Metric Tonnes)',
                'Financial value ($)',

                '(Formal) Cassava',
                '(Formal) Potato',
                '(Formal) Sweet potato',
                // 'Formal exports',
                '(Informal) Cassava',
                '(Informal) Potato',
                '(Informal) Sweet potato',
                // 'Informal exports',
                'Raw',
                'Processed',
            ],
            "Percentage of value ($) of formal RTC imports substituted through local production" => [
                'Total (% Percentage)',
                'Volume(Metric Tonnes)',
                'Financial value ($)',
                '(Formal) Cassava',
                '(Formal) Potato',
                '(Formal) Sweet potato',
                //'Formal imports'
            ],
            "Number of people consuming RTC and processed products" => [
                'Total',
                'RTC actors and households',
                'School feeding beneficiaries',
                'Individuals from households reached with nutrition interventions'
            ],
            "Percentage Increase in the volume of RTC produced" => [
                'Total (% Percentage)',
                'Cassava',
                'Potato',
                'Sweet potato',
                //  'Certified seed produce',
                //  'Value added RTC products'
            ],
            "Percentage increase in RTC investment" => [
                'Total (% Percentage)',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of local RTC varieties suitable for domestic and export markets identified for promotion" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of potential market preferred RTC genotypes in the pipeline identified" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed'
            ],
            "Number of new RTC technologies developed" => [
                'Total',
                'Improved RTC variety',
                'Seed production',
                'Storage',
                'Agronomic production',
                'Post-harvest processing',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Percentage increase in adoption of new RTC technologies" => [
                'Total (% Percentage)',
                'Improved RTC variety',
                'Seed production',
                'Storage',
                'Agronomic production',
                'Post-harvest processing',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of economic studies conducted" => ['Total'],
            "Number of RTC and derived products recorded in official trade statistics" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed'
            ],
            "Number of policy briefs developed and shared on RTC topics" => ['Total'],
            "Number of existing agricultural programs that integrate RTC into their programs" => ['Total'],
            "Number of market linkages between EGS and other seed class producers facilitated" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of private sector actors involved in production of RTC certified seed" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Area (ha) under seed multiplication" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Basic',
                'Certified'
            ],
            "Percentage seed multipliers with formal registration" => [
                'Total (% Percentage)',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Basic',
                'Certified',
                'POs',
                'Individual farmers not in POs',
                // 'Large scale farmers',
                //  'Medium scale farmers'
            ],
            "Volume of seed distributed within communities to enhance POs productivity" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                //   'Basic',
                // 'Certified'
            ],
            "Number of on-farm seed production technology demonstrations established" => ['Total'],
            "Number of international learning visits for seed producers (OC)" => ['Total'],
            "Percentage business plans for the production of different classes of RTC seeds that are executed" => [
                'Total (% Percentage)',
                'POs',
                'SMEs',
                'Large scale commercial farmers',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Basic',
                'Certified'
            ],
            "Number of stakeholder engagement events that focus on RTC development" => [
                'Total',
                'Seed production',
                'Seed multiplication',
                'Seed processing'
            ],
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
            "Number of RTC products available on the Management Information System" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Seed',
                'Produce',
                'Value added products',
            ],
            "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed'
            ],
            "Number of RTC actors that use certified seed" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Farmers',
                'Processors',
                'Traders',
                'Partner',
                'Staff'
            ],
            "Number of off-season irrigation demonstration sites established" => ['Total'],
            "Number of demonstration sites for end-user preferred RTC varieties established" => ['Total'],
            "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)" => ['Total (% Percentage)', 'Total'],
            "Number of market opportunities identified for RTC actors" => [
                'Total',
                'Domestic markets',
                'International markets',
                'Imports',
                'Exports',
                'Seed',
                'Produce',
                'Value added products',
            ],
            "Number of contractual arrangements facilitated for commercial farmers" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
            ],
            "Number of RTC actors supported to access funds from financial service providers" => [
                'Total',
                'Processors',
                'Farmers',
                'Large scale processors',
                'SME',
                'Loan',
                'Input financing'
            ],
            "Number of POs that have formal contracts with buyers" => [
                'Total',
                'Fresh',
                'Processed'
            ],

            "Number of RTC aggregation centers established" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of RTC POs selling products through aggregation centers" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Volume (MT) of RTC products sold through collective marketing efforts by POs" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Fresh',
                'Processed'
            ],
            "Number of households reached with RTC nutrition interventions" => ['Total'],
            "Frequency of RTC consumption by households per week (OC)" => ['Total'],
            "Percentage increase in households consuming RTCs as the main foodstuff (OC)" => ['Total (% Percentage)', 'Total'],
            "Number of RTC utilization options (dishes) adopted by households (OC)" => ['Total'],
            "Number of urban market promotions conducted" => ['Total'],
            "Number of mass nutrition education campaigns conducted" => ['Total'],
            "Number of RTC value-added products promoted" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of RTC actors with MBS certification for producing (or processing) RTC products" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato',
                'SMEs',
                'Large scale commercial farms',
                'POs'
            ],
            "Number of RTC value-added products developed for domestic markets" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of new RTC recipes/products adopted and branded by processors" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of domestic market opportunities identified for value-added products" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
            "Number of international market opportunities identified for value-added products" => [
                'Total',
                'Cassava',
                'Potato',
                'Sweet potato'
            ],
        ]);
    }

    public static function indicatorArray(): Collection
    {
        return collect([
            [
                "id" => 1,
                "indicator_name" => "Number of actors profitability engaged in commercialization of RTC",
                "indicator_no" => "A1",
                "partners" => [
                    'CIP',
                    'IITA',
                    'DAES',
                    'DCD'
                ]
            ],
            [
                "id" => 2,
                "indicator_name" => "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities",
                "indicator_no" => "B1",
                "partners" => [
                    'CIP',
                    'IITA',
                    'DAES',
                    'DCD'
                ]
            ],
            [
                "id" => 3,
                "indicator_name" => "Percentage increase in value of formal RTC exports",
                "indicator_no" => "B2",
                "partners" => [
                    'MINISTRY OF TRADE',
                    'CIP'
                ]
            ],
            [
                "id" => 4,
                "indicator_name" => "Percentage of value ($) of formal RTC imports substituted through local production",
                "indicator_no" => "B3",
                "partners" => ['MINISTRY OF TRADE']
            ],
            [
                "id" => 5,
                "indicator_name" => "Number of people consuming RTC and processed products",
                "indicator_no" => "B4",
                "partners" => [
                    'CIP',
                    'IITA',
                    'DAES'
                ]
            ],
            [
                "id" => 6,
                "indicator_name" => "Percentage Increase in the volume of RTC produced",
                "indicator_no" => "B5",
                "partners" => [
                    'CIP',
                    'IITA',
                    'DAES'
                ]
            ],
            [
                "id" => 7,
                "indicator_name" => "Percentage increase in RTC investment",
                "indicator_no" => "B6",
                "partners" => ['CIP']
            ],
            [
                "id" => 8,
                "indicator_name" => "Number of local RTC varieties suitable for domestic and export markets identified for promotion",
                "indicator_no" => "1.1.1",
                "partners" => [
                    'CIP',
                    'IITA',
                    'TRADELINE',
                    'MINISTRY OF TRADE'
                ]
            ],
            [
                "id" => 9,
                "indicator_name" => "Number of potential market preferred RTC genotypes in the pipeline identified",
                "indicator_no" => "1.1.2",
                "partners" => ['DARS']
            ],
            [
                "id" => 10,
                "indicator_name" => "Number of new RTC technologies developed",
                "indicator_no" => "1.1.3",
                "partners" => ['DARS']
            ],
            [
                "id" => 11,
                "indicator_name" => "Percentage increase in adoption of new RTC technologies",
                "indicator_no" => "1.1.4",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA',
                    'RTCDT',
                    'DARS'
                ]
            ],
            [
                "id" => 12,
                "indicator_name" => "Number of economic studies conducted",
                "indicator_no" => "1.2.1",
                "partners" => [
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 13,
                "indicator_name" => "Number of RTC and derived products recorded in official trade statistics",
                "indicator_no" => "1.2.2",
                "partners" => ['MINISTRY OF TRADE']
            ],
            [
                "id" => 14,
                "indicator_name" => "Number of existing agricultural programs that integrate RTC into their programs",
                "indicator_no" => "1.3.1",
                "partners" => ['RTCDT']
            ],
            [
                "id" => 15,
                "indicator_name" => "Number of policy briefs developed and shared on RTC topics",
                "indicator_no" => "1.3.1",
                "partners" => ['RTCDT']
            ],
            [
                "id" => 16,
                "indicator_name" => "Number of market linkages between EGS and other seed class producers facilitated",
                "indicator_no" => "2.1.1",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 17,
                "indicator_name" => "Number of private sector actors involved in production of RTC certified seed",
                "indicator_no" => "2.2.1",
                "partners" => [
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 18,
                "indicator_name" => "Area (ha) under seed multiplication",
                "indicator_no" => "2.2.2",
                "partners" => [
                    'DAES',
                    'IITA',
                    'CIP'
                ]
            ],
            [
                "id" => 19,
                "indicator_name" => "Percentage seed multipliers with formal registration",
                "indicator_no" => "2.2.3",
                "partners" => [
                    'DAES',
                    'IITA',
                    'CIP'
                ]
            ],
            [
                "id" => 20,
                "indicator_name" => "Volume of seed distributed within communities to enhance POs productivity",
                "indicator_no" => "2.2.4",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 21,
                "indicator_name" => "Number of on-farm seed production technology demonstrations established",
                "indicator_no" => "2.2.5",
                "partners" => ['DAES']
            ],
            [
                "id" => 22,
                "indicator_name" => "Number of international learning visits for seed producers (OC)",
                "indicator_no" => "2.3.1",
                "partners" => [
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 23,
                "indicator_name" => "Percentage business plans for the production of different classes of RTC seeds that are executed",
                "indicator_no" => "2.3.1",
                "partners" => [
                    'TRADELINE',
                    'RCDT'
                ]
            ],
            [
                "id" => 24,
                "indicator_name" => "Number of stakeholder engagement events that focus on RTC development",
                "indicator_no" => "2.3.2",
                "partners" => ['ACE']
            ],
            [
                "id" => 25,
                "indicator_name" => "Number of registered seed producers accessing markets through online Market Information System (MIS)",
                "indicator_no" => "2.3.3",
                "partners" => [
                    'ACE',
                    'TRADELINE'
                ]
            ],
            [
                "id" => 26,
                "indicator_name" => "Number of RTC actors linked to online Market Information System (MIS)",
                "indicator_no" => "2.3.4",
                "partners" => [
                    'ACE',
                    'DAES'
                ]
            ],
            [
                "id" => 27,
                "indicator_name" => "Number of RTC products available on the Management Information System",
                "indicator_no" => "2.3.5",
                "partners" => [
                    'ACE',
                    'DAES'
                ]
            ],
            [
                "id" => 28,
                "indicator_name" => "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production",
                "indicator_no" => "3.1.1",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 29,
                "indicator_name" => "Number of RTC actors that use certified seed",
                "indicator_no" => "3.2.1",
                "partners" => ['CIP']
            ],
            [
                "id" => 30,
                "indicator_name" => "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)",
                "indicator_no" => "3.2.2",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA',
                    'ACE',
                    'MINISTRY OF TRADE'
                ]
            ],
            [
                "id" => 31,
                "indicator_name" => "Number of off-season irrigation demonstration sites established",
                "indicator_no" => "3.2.3",
                "partners" => ['DAES']
            ],
            [
                "id" => 32,
                "indicator_name" => "Number of demonstration sites for end-user preferred RTC varieties established",
                "indicator_no" => "3.2.4",
                "partners" => ['DAES']
            ],
            [
                "id" => 33,
                "indicator_name" => "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)",
                "indicator_no" => "3.2.5",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 34,
                "indicator_name" => "Number of market opportunities identified for RTC actors",
                "indicator_no" => "3.3.1",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 35,
                "indicator_name" => "Number of contractual arrangements facilitated for commercial farmers",
                "indicator_no" => "3.3.2",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 36,
                "indicator_name" => "Number of RTC actors supported to access funds from financial service providers",
                "indicator_no" => "3.4.1",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 37,
                "indicator_name" => "Number of POs that have formal contracts with buyers",
                "indicator_no" => "3.4.2",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 38,
                "indicator_name" => "Number of RTC aggregation centers established",
                "indicator_no" => "3.4.3",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 39,
                "indicator_name" => "Number of RTC POs selling products through aggregation centers",
                "indicator_no" => "3.4.4",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 40,
                "indicator_name" => "Volume (MT) of RTC products sold through collective marketing efforts by POs",
                "indicator_no" => "3.4.5",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 41,
                "indicator_name" => "Number of households reached with RTC nutrition interventions",
                "indicator_no" => "3.5.1",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 42,
                "indicator_name" => "Frequency of RTC consumption by households per week (OC)",
                "indicator_no" => "3.5.2",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 43,
                "indicator_name" => "Percentage increase in households consuming RTCs as the main foodstuff (OC)",
                "indicator_no" => "3.5.3",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 44,
                "indicator_name" => "Number of RTC utilization options (dishes) adopted by households (OC)",
                "indicator_no" => "3.5.4",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 45,
                "indicator_name" => "Number of urban market promotions conducted",
                "indicator_no" => "3.5.5",
                "partners" => [
                    'DAES',
                    'CIP',
                    'IITA'
                ]
            ],
            [
                "id" => 46,
                "indicator_name" => "Number of mass nutrition education campaigns conducted",
                "indicator_no" => "3.5.6",
                "partners" => ['DAES']
            ],
            [
                "id" => 47,
                "indicator_name" => "Number of RTC value-added products promoted",
                "indicator_no" => "4.1.1",
                "partners" => [
                    'CIP',
                    'IITA',
                    'RTCDT'
                ]
            ],
            [
                "id" => 48,
                "indicator_name" => "Number of RTC actors with MBS certification for producing (or processing) RTC products",
                "indicator_no" => "4.1.2",
                "partners" => [
                    'RTCDT',
                    'MINISTRY OF TRADE'
                ]
            ],
            [
                "id" => 49,
                "indicator_name" => "Number of RTC value-added products developed for domestic markets",
                "indicator_no" => "4.1.3",
                "partners" => ['MINISTRY OF TRADE']
            ],
            [
                "id" => 50,
                "indicator_name" => "Number of new RTC recipes/products adopted and branded by processors",
                "indicator_no" => "4.1.4",
                "partners" => [
                    'CIP',
                    'IITA',
                    'DAES'
                ]
            ],
            [
                "id" => 51,
                "indicator_name" => "Number of domestic market opportunities identified for value-added products",
                "indicator_no" => "4.1.5",
                "partners" => ['TRADELINE']
            ],
            [
                "id" => 52,
                "indicator_name" => "Number of international market opportunities identified for value-added products",
                "indicator_no" => "4.1.6",
                "partners" => ['TRADELINE']
            ]
        ]);
    }
}