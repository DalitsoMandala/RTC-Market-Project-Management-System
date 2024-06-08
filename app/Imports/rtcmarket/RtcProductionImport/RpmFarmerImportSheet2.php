<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class RpmFarmerImportSheet2 implements ToCollection, WithHeadingRow// FOLLOW UP

{
    public $userId;public $file;
// follow up
    public $expectedHeadings = [
        'RECRUIT ID',
        'ENTERPRISE',
        'DISTRICT',
        'EPA',
        'SECTION',
        'AREA UNDER CULTIVATION/TOTAL',
        'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)',
        'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)',
        'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)',
        'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)',
        'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)',
        'NUMBER OF PLANTLETS PRODUCED/CASSAVA',
        'NUMBER OF PLANTLETS PRODUCED/POTATO',
        'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO',
        'NUMBER OF SCREEN HOUSE VINES HARVESTED',
        'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED',
        'NUMBER OF SAH PLANTS PRODUCED',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)',
        'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)',
        'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)',
        'IS REGISTERED SEED PRODUCER',
        'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER',
        'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE',
        'USES CERTIFIED SEED',
        'MARKET SEGMENT/FRESH',
        'MARKET SEGMENT/PROCESSED',
        'HAS RTC MARKET CONTRACT',
        'TOTAL PRODUCTION PREVIOUS SEASON',
        'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL',
        'TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES',
        'TOTAL IRRIGATION PRODUCTION PREVIOUS SEASON',
        'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL',
        'TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES',
        'SELLS TO DOMESTIC MARKETS',
        'SELLS TO INTERNATIONAL MARKETS',
        'USES MARKET INFORMATION SYSTEMS',
        'MARKET INFORMATION SYSTEMS',
        'SELLS TO AGGREGATION CENTERS',
        'AGGREGATION CENTERS/RESPONSE',
        'AGGREGATION CENTERS/SPECIFY',
        'TOTAL AGGREGATION CENTER SALES VOLUME',
    ];

    public function __construct($userId, $file)
    {
        $this->userId = $userId;
        $this->file = $file;
    }

    public function collection(Collection $collection)
    {

        $headings = (new HeadingRowImport)->toArray($this->file);

        $headings = $headings[1][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {
            dd($missingHeadings);
            throw new \Exception("Something went wrong. Please upload your data using the template file above");

        }

        try {

            $main_data = [];

            foreach ($collection as $row) {
                $main_data[] = [
                    'rpm_farmer_id' => $row['RECRUIT ID'],
                    'location_data' => json_encode([
                        'enterprise' => $row['ENTERPRISE'],
                        'district' => $row['DISTRICT'],
                        'epa' => $row['EPA'],
                        'section' => $row['SECTION'],
                    ]),
                    'date_of_follow_up' => $row['DATE OF FOLLOW UP'],
                    'area_under_cultivation' => json_encode([
                        'total' => $row['AREA UNDER CULTIVATION/TOTAL'],
                        'variety_1' => $row['AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)'],
                        'variety_2' => $row['AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)'],
                        'variety_3' => $row['AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)'],
                        'variety_4' => $row['AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)'],
                        'variety_5' => $row['AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)'],
                    ]),
                    'number_of_plantlets_produced' => json_encode([
                        'cassava' => $row['NUMBER OF PLANTLETS PRODUCED/CASSAVA'],
                        'potato' => $row['NUMBER OF PLANTLETS PRODUCED/POTATO'],
                        'sweet_potato' => $row['NUMBER OF PLANTLETS PRODUCED/SWEET POTATO'],
                    ]),
                    'number_of_screen_house_vines_harvested' => $row['NUMBER OF SCREEN HOUSE VINES HARVESTED'],
                    'number_of_screen_house_min_tubers_harvested' => $row['NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED'],
                    'number_of_sah_plants_produced' => $row['NUMBER OF SAH PLANTS PRODUCED'],
                    'area_under_basic_seed_multiplication' => json_encode([
                        'total' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL'],
                        'variety_1' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)'],
                        'variety_2' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)'],
                        'variety_3' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)'],
                        'variety_4' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)'],
                        'variety_5' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)'],
                        'variety_6' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)'],
                        'variety_7' => $row['AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)'],
                    ]),
                    'area_under_certified_seed_multiplication' => json_encode([
                        'total' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL'],
                        'variety_1' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)'],
                        'variety_2' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)'],
                        'variety_3' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)'],
                        'variety_4' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)'],
                        'variety_5' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)'],
                        'variety_6' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)'],
                        'variety_7' => $row['AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)'],
                    ]),
                    'is_registered_seed_producer' => $row['IS REGISTERED SEED PRODUCER'],
                    'seed_service_unit_registration_details' => json_encode([
                        'registration_number' => $row['REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER'],
                        'registration_date' => $row['REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE'],
                    ]),
                    'uses_certified_seed' => $row['USES CERTIFIED SEED'],
                    'market_segment' => json_encode([
                        'fresh' => $row['MARKET SEGMENT/FRESH'],
                        'processed' => $row['MARKET SEGMENT/PROCESSED'],
                    ]),
                    'has_rtc_market_contract' => $row['HAS RTC MARKET CONTRACT'],
                    'total_production_previous_season' => $row['TOTAL PRODUCTION PREVIOUS SEASON'],
                    'total_production_value_previous_season' => json_encode([
                        'total' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL'],
                        'date_of_maximum_sales' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES'],
                    ]),
                    'total_irrigation_production_previous_season' => $row['TOTAL IRRIGATION PRODUCTION PREVIOUS SEASON'],
                    'total_irrigation_production_value_previous_season' => json_encode([
                        'total' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL'],
                        'date_of_maximum_sales' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES'],
                    ]),
                    'sells_to_domestic_markets' => $row['SELLS TO DOMESTIC MARKETS'],
                    'sells_to_international_markets' => $row['SELLS TO INTERNATIONAL MARKETS'],
                    'uses_market_information_systems' => $row['USES MARKET INFORMATION SYSTEMS'],
                    'market_information_systems' => $row['MARKET INFORMATION SYSTEMS'],
                    'aggregation_centers' => json_encode([
                        'response' => $row['AGGREGATION CENTERS/RESPONSE'],
                        'specify' => $row['AGGREGATION CENTERS/SPECIFY'],
                    ]),
                    'aggregation_center_sales' => $row['TOTAL AGGREGATION CENTER SALES VOLUME'],
                    //  'user_id' => $this->userId,
                    //'uuid' => session()->get('uuid'),
                ];

            }

            session()->put('batch_data.followup', $main_data);

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows on sheet 2." . $e->getMessage());
        }
    }
}
