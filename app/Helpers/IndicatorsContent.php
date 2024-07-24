<?php

namespace App\Helpers;

use App\Models\Indicator;
use Illuminate\Support\Collection;


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
        if ($this->name && $this->number) {
            return $this->getContentByNameAndNumber();
        } elseif ($this->id) {
            return $this->getContentById();
        }

        return collect();
    }

    private function getContentByNameAndNumber(): Collection
    {
        $indicatorArray = self::indicatorArray();
        $indicatorCalculations = self::indicatorCalculations();

        $query = $indicatorArray->where('indicator_name', $this->name)
            ->where('indicator_no', $this->number)
            ->first();

        $classes = $indicatorCalculations->where('indicator_name', $this->name)
            ->first();

        if ($query) {
            $query['class'] = $classes['class'] ?? null;
        }

        return collect($query);
    }

    private function getContentById(): Collection
    {
        $indicatorArray = self::indicatorArray();
        $indicatorCalculations = self::indicatorCalculations();

        $query = $indicatorArray->where('id', $this->id)->first();
        $indicator = Indicator::find($this->id);

        if ($query && $indicator) {
            $classes = $indicatorCalculations->where('indicator_name', $indicator->indicator_name)
                ->first();
            $query['class'] = $classes['class'] ?? null;
        }

        return collect($query);
    }

    public static function indicatorCalculations(): Collection
    {

        return collect([
            [
                "indicator_name" => "Number of actors profitability engaged in commercialization of RTC",
                'class' => \App\Helpers\rtc_market\indicators\A1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities",
                'class' => \App\Helpers\rtc_market\indicators\B1::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in value of formal RTC exports",
                'class' => \App\Helpers\rtc_market\indicators\Indicator_B2::class,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage of value ($) of formal RTC imports substituted through local production",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of people consuming RTC and processed products",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage Increase in the volume of RTC produced",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in RTC investment",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of local RTC varieties suitable for domestic and export markets identified for promotion",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of potential market preferred RTC genotypes in the pipeline identified",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of new RTC technologies developed",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Percentage increase in adoption of new RTC technologies",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of economic studies conducted",
                'class' => null,
                'project_name' => 'RTC MARKET',
            ],
            [
                "indicator_name" => "Number of RTC and derived products recorded in official trade statistics",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of existing agricultural programs that integrate RTC into their programs",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of policy briefs developed and shared on RTC topics",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of market linkages between EGS and other seed class producers facilitated",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of private sector actors involved in production of RTC certified seed",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Area (ha) under seed multiplication",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Percentage seed multipliers with formal registration",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Volume of seed distributed within communities to enhance POs productivity",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of on-farm seed production technology demonstrations established",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of international learning visits for seed producers (OC)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Percentage business plans for the production of different classes of RTC seeds that are executed",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of stakeholder engagement events that focus on RTC development",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of registered seed producers accessing markets through online Market Information System (MIS)",
            ],
            [
                "indicator_name" => "Number of RTC actors linked to online Market Information System (MIS)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC products available on the Management Information System",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC actors that use certified seed",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of off-season irrigation demonstration sites established",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of demonstration sites for end-user preferred RTC varieties established",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of market opportunities identified for RTC actors",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of contractual arrangements facilitated for commercial farmers",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC actors supported to access funds from financial service providers",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of POs that have formal contracts with buyers",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC aggregation centers established",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC POs selling products through aggregation centers aggregation center",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Volume (MT) of RTC products sold through collective marketing efforts by POs",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of households reached with RTC nutrition interventions",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Frequency of RTC consumption by households per week (OC)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Percentage increase in households consuming RTCs as the main foodstuff (OC)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC utilization options (dishes) adopted by households (OC)",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of urban market promotions conducted",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of mass nutrition education campaigns conducted",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC value-added products promoted",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC actors with MBS certification for producing (or processing) RTC products",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of RTC value-added products developed for domestic markets",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of new RTC recipes/products adopted and branded by processors",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of domestic market opportunities identified for value-added products",
                'class' => null,
                'project_name' => '',
            ],
            [
                "indicator_name" => "Number of international market opportunities identified for value-added products",
                'class' => null,
                'project_name' => '',
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
                'Total',
                'Cassava',
                'Sweet potato',
                'Potato',
            ],

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
            "Percentage of value ($) of formal RTC imports substituted through local production" => [
                'Total',
                'Volume(Metric Tonnes)',
                'Financial value ($)',
                'Cassava',
                'Potato',
                'Sweet potato',
                'Formal',
            ],
            "Number of people consuming RTC and processed products" => ['Total', 'RTC actors and households', 'School feeding beneficiaries', 'Individuals from households reached with nutrition interventions'],
            "Percentage Increase in the volume of RTC produced" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Certified seed produce', 'Value added RTC products'],
            "Percentage increase in RTC investment" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of local RTC varieties suitable for domestic and export markets identified for promotion" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of potential market preferred RTC genotypes in the pipeline identified" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed'],
            "Number of new RTC technologies developed" => ['Total', 'Improved RTC variety', 'Seed production', 'Storage', 'Agronomic production', 'Post-harvest processing', 'Cassava', 'Potato', 'Sweet potato'],
            "Percentage increase in adoption of new RTC technologies" => ['Total', 'Improved RTC variety', 'Seed production', 'Storage', 'Agronomic production', 'Post-harvest processing', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of economic studies conducted" => ['Total'],
            "Number of RTC and derived products recorded in official trade statistics" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed'],
            "Number of policy briefs developed and shared on RTC topics" => ['Total'],
            "Number of existing agricultural programs that integrate RTC into their programs" => ['Total'],

            "Number of market linkages between EGS and other seed class producers facilitated" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of private sector actors involved in production of RTC certified seed" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Area (ha) under seed multiplication" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified'],
            "Percentage seed multipliers with formal registration" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified', 'POs', 'Individual farmers not in POs', 'Large scale farmers', 'Medium scale farmers'],
            "Volume of seed distributed within communities to enhance POs productivity" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Basic', 'Certified'],
            "Number of on-farm seed production technology demonstrations established" => ['Total'],

            "Number of international learning visits for seed producers (OC)" => ['Total'],
            "Percentage business plans for the production of different classes of RTC seeds that are executed" => ['Total', 'POs', 'SMEs', 'Large scale commercial farmers', 'Cassava', 'Potato', 'Sweet potato'],

            "Number of stakeholder engagement events that focus on RTC development" => ['Total', 'Seed production', 'Seed multiplication', 'Seed processing'],

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

            "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed'],
            "Number of RTC actors that use certified seed" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Farmers', 'Processors', 'Traders', 'Partner', 'Staff'],
            "Number of off-season irrigation demonstration sites established" => ['Total'],
            "Number of demonstration sites for end-user preferred RTC varieties established" => ['Total'],
            "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)" => ['Total'],

            "Number of market opportunities identified for RTC actors" => [
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

            "Number of RTC actors supported to access funds from financial service providers" => ['Total', 'Processors', 'Farmers', 'Large scale processors', 'SME', 'Loan', 'Input financing'],
            "Number of POs that have formal contracts with buyers" => ['Total', 'Fresh', 'Processed'],
            "Number of RTC POs selling products through aggregation centers aggregation center" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Volume (MT) of RTC products sold through collective marketing efforts by POs" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'Fresh', 'Processed'],
            "Number of households reached with RTC nutrition interventions" => ['Total'],
            "Frequency of RTC consumption by households per week (OC)" => ['Total'],
            "Percentage increase in households consuming RTCs as the main foodstuff (OC)" => ['Total'],
            "Number of RTC utilization options (dishes) adopted by households (OC)" => ['Total'],
            "Number of urban market promotions conducted" => ['Total'],
            "Number of mass nutrition education campaigns conducted" => ['Total'],
            "Number of RTC value-added products promoted" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of RTC actors with MBS certification for producing (or processing) RTC products" => ['Total', 'Cassava', 'Potato', 'Sweet potato', 'SMEs', 'Large scale commercial farms', 'POs'],
            "Number of RTC value-added products developed for domestic markets" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of new RTC recipes/products adopted and branded by processors" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of domestic market opportunities identified for value-added products" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
            "Number of international market opportunities identified for value-added products" => ['Total', 'Cassava', 'Potato', 'Sweet potato'],
        ]);

    }
    public static function indicatorArray(): Collection
    {
        return collect([
            [
                "id" => 1,
                "indicator_name" => "Number of actors profitability engaged in commercialization of RTC",
                "indicator_no" => "A1",
            ],
            [
                "id" => 2,
                "indicator_name" => "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities",
                "indicator_no" => "B1",
            ],
            [
                "id" => 3,
                "indicator_name" => "Percentage increase in value of formal RTC exports",
                "indicator_no" => "B2",
            ],
            [
                "id" => 4,
                "indicator_name" => "Percentage of value ($) of formal RTC imports substituted through local production",
                "indicator_no" => "B3",
            ],
            [
                "id" => 5,
                "indicator_name" => "Number of people consuming RTC and processed products",
                "indicator_no" => "B4",
            ],
            [
                "id" => 6,
                "indicator_name" => "Percentage Increase in the volume of RTC produced",
                "indicator_no" => "B5",
            ],
            [
                "id" => 7,
                "indicator_name" => "Percentage increase in RTC investment",
                "indicator_no" => "B6",
            ],
            [
                "id" => 8,
                "indicator_name" => "Number of local RTC varieties suitable for domestic and export markets identified for promotion",
                "indicator_no" => "1.1.1",
            ],
            [
                "id" => 9,
                "indicator_name" => "Number of potential market preferred RTC genotypes in the pipeline identified",
                "indicator_no" => "1.1.2",
            ],
            [
                "id" => 10,
                "indicator_name" => "Number of new RTC technologies developed",
                "indicator_no" => "1.1.3",
            ],
            [
                "id" => 11,
                "indicator_name" => "Percentage increase in adoption of new RTC technologies",
                "indicator_no" => "1.1.4",
            ],
            [
                "id" => 12,
                "indicator_name" => "Number of economic studies conducted",
                "indicator_no" => "1.2.1",
            ],
            [
                "id" => 13,
                "indicator_name" => "Number of RTC and derived products recorded in official trade statistics",
                "indicator_no" => "1.2.2",
            ],
            [
                "id" => 14,
                "indicator_name" => "Number of existing agricultural programs that integrate RTC into their programs",
                "indicator_no" => "1.3.1",
            ],
            [
                "id" => 15,
                "indicator_name" => "Number of policy briefs developed and shared on RTC topics",
                "indicator_no" => "1.3.1",
            ],
            [
                "id" => 16,
                "indicator_name" => "Number of market linkages between EGS and other seed class producers facilitated",
                "indicator_no" => "2.1.1",
            ],
            [
                "id" => 17,
                "indicator_name" => "Number of private sector actors involved in production of RTC certified seed",
                "indicator_no" => "2.2.1",
            ],
            [
                "id" => 18,
                "indicator_name" => "Area (ha) under seed multiplication",
                "indicator_no" => "2.2.2",
            ],
            [
                "id" => 19,
                "indicator_name" => "Percentage seed multipliers with formal registration",
                "indicator_no" => "2.2.3",
            ],
            [
                "id" => 20,
                "indicator_name" => "Volume of seed distributed within communities to enhance POs productivity",
                "indicator_no" => "2.2.4",
            ],
            [
                "id" => 21,
                "indicator_name" => "Number of on-farm seed production technology demonstrations established",
                "indicator_no" => "2.2.5",
            ],
            [
                "id" => 22,
                "indicator_name" => "Number of international learning visits for seed producers (OC)",
                "indicator_no" => "2.3.1",
            ],
            [
                "id" => 23,
                "indicator_name" => "Percentage business plans for the production of different classes of RTC seeds that are executed",
                "indicator_no" => "2.3.1",
            ],
            [
                "id" => 24,
                "indicator_name" => "Number of stakeholder engagement events that focus on RTC development",
                "indicator_no" => "2.3.2",
            ],
            [
                "id" => 25,
                "indicator_name" => "Number of registered seed producers accessing markets through online Market Information System (MIS)",
                "indicator_no" => "2.3.3",
            ],
            [
                "id" => 26,
                "indicator_name" => "Number of RTC actors linked to online Market Information System (MIS)",
                "indicator_no" => "2.3.4",
            ],
            [
                "id" => 27,
                "indicator_name" => "Number of RTC products available on the Management Information System",
                "indicator_no" => "2.3.5",
            ],
            [
                "id" => 28,
                "indicator_name" => "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production",
                "indicator_no" => "3.1.1",
            ],
            [
                "id" => 29,
                "indicator_name" => "Number of RTC actors that use certified seed",
                "indicator_no" => "3.2.1",
            ],
            [
                "id" => 30,
                "indicator_name" => "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)",
                "indicator_no" => "3.2.2",
            ],
            [
                "id" => 31,
                "indicator_name" => "Number of off-season irrigation demonstration sites established",
                "indicator_no" => "3.2.3",
            ],
            [
                "id" => 32,
                "indicator_name" => "Number of demonstration sites for end-user preferred RTC varieties established",
                "indicator_no" => "3.2.4",
            ],
            [
                "id" => 33,
                "indicator_name" => "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)",
                "indicator_no" => "3.2.5",
            ],
            [
                "id" => 34,
                "indicator_name" => "Number of market opportunities identified for RTC actors",
                "indicator_no" => "3.3.1",
            ],
            [
                "id" => 35,
                "indicator_name" => "Number of contractual arrangements facilitated for commercial farmers",
                "indicator_no" => "3.3.2",
            ],
            [
                "id" => 36,
                "indicator_name" => "Number of RTC actors supported to access funds from financial service providers",
                "indicator_no" => "3.4.1",
            ],
            [
                "id" => 37,
                "indicator_name" => "Number of POs that have formal contracts with buyers",
                "indicator_no" => "3.4.2",
            ],
            [
                "id" => 38,
                "indicator_name" => "Number of RTC aggregation centers established",
                "indicator_no" => "3.4.3",
            ],
            [
                "id" => 39,
                "indicator_name" => "Number of RTC POs selling products through aggregation centers aggregation center",
                "indicator_no" => "3.4.4",
            ],
            [
                "id" => 40,
                "indicator_name" => "Volume (MT) of RTC products sold through collective marketing efforts by POs",
                "indicator_no" => "3.4.5",
            ],
            [
                "id" => 41,
                "indicator_name" => "Number of households reached with RTC nutrition interventions",
                "indicator_no" => "3.5.1",
            ],
            [
                "id" => 42,
                "indicator_name" => "Frequency of RTC consumption by households per week (OC)",
                "indicator_no" => "3.5.2",
            ],
            [
                "id" => 43,
                "indicator_name" => "Percentage increase in households consuming RTCs as the main foodstuff (OC)",
                "indicator_no" => "3.5.3",
            ],
            [
                "id" => 44,
                "indicator_name" => "Number of RTC utilization options (dishes) adopted by households (OC)",
                "indicator_no" => "3.5.4",
            ],
            [
                "id" => 45,
                "indicator_name" => "Number of urban market promotions conducted",
                "indicator_no" => "3.5.5",
            ],
            [
                "id" => 46,
                "indicator_name" => "Number of mass nutrition education campaigns conducted",
                "indicator_no" => "3.5.6",
            ],
            [
                "id" => 47,
                "indicator_name" => "Number of RTC value-added products promoted",
                "indicator_no" => "4.1.1",
            ],
            [
                "id" => 48,
                "indicator_name" => "Number of RTC actors with MBS certification for producing (or processing) RTC products",
                "indicator_no" => "4.1.2",
            ],
            [
                "id" => 49,
                "indicator_name" => "Number of RTC value-added products developed for domestic markets",
                "indicator_no" => "4.1.3",
            ],
            [
                "id" => 50,
                "indicator_name" => "Number of new RTC recipes/products adopted and branded by processors",
                "indicator_no" => "4.1.4",
            ],
            [
                "id" => 51,
                "indicator_name" => "Number of domestic market opportunities identified for value-added products",
                "indicator_no" => "4.1.5",
            ],
            [
                "id" => 52,
                "indicator_name" => "Number of international market opportunities identified for value-added products",
                "indicator_no" => "4.1.6",
            ],
        ]);

    }

}
