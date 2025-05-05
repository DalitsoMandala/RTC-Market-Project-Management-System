<?php

namespace App\Services;

class IndicatorService
{
    protected $indicatorMap = [
        'Number of actors profitability engaged in commercialization of RTC' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-a1',
        ],
        'Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b1',
        ],
        'Percentage increase in value of formal RTC exports' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b2',
        ],
        'Percentage of value ($) of formal RTC imports substituted through local production' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b3',
        ],
        'Number of people consuming RTC and processed products' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b4',
        ],
        'Percentage Increase in the volume of RTC produced' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b5',
        ],
        'Percentage increase in RTC investment' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-b6',
        ],
        'Number of local RTC varieties suitable for domestic and export markets identified for promotion' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-111',
        ],
        'Number of potential market preferred RTC genotypes in the pipeline identified' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-112',
        ],
        'Number of new RTC technologies developed' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-113',
        ],
        'Percentage increase in adoption of new RTC technologies' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-114',
        ],
        'Number of economic studies conducted' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-121',
        ],
        'Number of RTC and derived products recorded in official trade statistics' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-122',
        ],
        'Number of existing agricultural programs that integrate RTC into their programs' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-131',
        ],
        'Number of policy briefs developed and shared on RTC topics' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-1312',
        ],
        'Number of market linkages between EGS and other seed class producers facilitated' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-211',
        ],
        'Number of private sector actors involved in production of RTC certified seed' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-221',
        ],
        'Area (ha) under seed multiplication' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-222',
        ],
        'Percentage seed multipliers with formal registration' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-223',
        ],
        'Volume of seed distributed within communities to enhance POs productivity' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-224',
        ],
        'Number of on-farm seed production technology demonstrations established' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-225',
        ],
        'Number of international learning visits for seed producers (OC)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-231',
        ],
        'Number of business plans for the production of different classes of RTC seeds that are executed' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-2312',
        ],
        'Number of stakeholder engagement events that focus on RTC development' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-232',
        ],
        'Number of registered seed producers accessing markets through online Market Information System (MIS)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-233',
        ],
        'Number of RTC actors linked to online Market Information System (MIS)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-234',
        ],
        'Number of RTC products available on the Management Information System' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-235',
        ],
        'Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-311',
        ],
        'Number of RTC actors that use certified seed' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-321',
        ],
        'Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-322',
        ],
        'Number of off-season irrigation demonstration sites established' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-323',
        ],
        'Number of demonstration sites for end-user preferred RTC varieties established' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-324',
        ],
        'Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-325',
        ],
        'Number of market opportunities identified for RTC actors' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-331',
        ],
        'Number of contractual arrangements facilitated for commercial farmers' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-332',
        ],
        'Number of RTC actors supported to access funds from financial service providers' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-341',
        ],
        'Number of POs that have formal contracts with buyers' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-342',
        ],
        'Number of RTC aggregation centers established' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-343',
        ],
        'Number of RTC POs selling products through aggregation centers' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-344',
        ],
        'Volume (MT) of RTC products sold through collective marketing efforts by POs' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-345',
        ],
        'Number of households reached with RTC nutrition interventions' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-351',
        ],
        'Frequency of RTC consumption by households per week (OC)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-352',
        ],
        'Percentage increase in households consuming RTCs as the main foodstuff (OC)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-353',
        ],
        'Number of RTC utilization options (dishes) adopted by households (OC)' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-354',
        ],
        'Number of urban market promotions conducted' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-355',
        ],
        'Number of mass nutrition education campaigns conducted' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-356',
        ],
        'Number of RTC value-added products promoted' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-411',
        ],
        'Number of RTC actors with MBS certification for producing (or processing) RTC products' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-412',
        ],
        'Number of RTC value-added products developed for domestic markets' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-413',
        ],
        'Number of new RTC recipes/products adopted and branded by processors' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-414',
        ],
        'Number of domestic market opportunities identified for value-added products' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-415',
        ],
        'Number of international market opportunities identified for value-added products' => [
            'RTC MARKET' => 'indicators.rtc-market.indicator-416',
        ],
    ];


    public function getComponent($indicatorName, $projectName)
    {

        return $this->indicatorMap[$indicatorName][$projectName] ?? null;
    }
}
