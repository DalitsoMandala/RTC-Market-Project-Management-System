<?php

namespace App\Imports\rtcmarket;

use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Ramsey\Uuid\Uuid;

HeadingRowFormatter::default('none');
class RpmFarmerImportSheet1 implements ToCollection, WithHeadingRow// MAIN SHEET

{
    public $userId;
    public $file;
    public $expectedHeadings = [
        'ENTERPRISE',
        'DISTRICT',
        'EPA',
        'SECTION',
        'DATE OF RECRUITMENT',
        'NAME OF ACTOR',
        'NAME OF REPRESENTATIVE',
        'PHONE NUMBER',
        'TYPE',
        'APPROACH',
        'SECTOR',
        'NUMBER OF MEMBERS/TOTAL',
        'NUMBER OF MEMBERS/FEMALE 18-35YRS',
        'NUMBER OF MEMBERS/FEMALE 35YRS+',
        'NUMBER OF MEMBERS/MALE 18-35YRS',
        'NUMBER OF MEMBERS/MALE 35YRS+',
        'GROUP',
        'ESTABLISHMENT STATUS',
        'IS REGISTERED',
        'REGISTRATION DETAILS/REGISTRATION BODY',
        'REGISTRATION DETAILS/REGISTRATION NUMBER',
        'REGISTRATION DETAILS/REGISTRATION DATE',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS',
        'NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS',
        'NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+',
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

        $headings = $headings[0][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {

            throw new \Exception("Something went wrong. Please upload your data using the template file above");

        }

        try {
            $uuid = Uuid::uuid4()->toString();
            $main_data = [];

            foreach ($collection as $row) {

                $main_data[] = [
                    'location_data' => [
                        'enterprise' => $row['ENTERPRISE'],
                        'district' => $row['DISTRICT'],
                        'epa' => $row['EPA'],
                        'section' => $row['SECTION'],
                    ],
                    'date_of_recruitment' => $row['DATE OF RECRUITMENT'],
                    'name_of_actor' => $row['NAME OF ACTOR'],
                    'name_of_representative' => $row['NAME OF REPRESENTATIVE'],
                    'phone_number' => $row['PHONE NUMBER'],
                    'type' => $row['TYPE'],
                    'approach' => $row['APPROACH'],
                    'sector' => $row['SECTOR'],
                    'number_of_members' => json_encode([
                        'total' => $row['NUMBER OF MEMBERS/TOTAL'],
                        'female_18_35' => $row['NUMBER OF MEMBERS/FEMALE 18-35YRS'],
                        'female_35_plus' => $row['NUMBER OF MEMBERS/FEMALE 35YRS+'],
                        'male_18_35' => $row['NUMBER OF MEMBERS/MALE 18-35YRS'],
                        'male_35_plus' => $row['NUMBER OF MEMBERS/MALE 35YRS+'],
                    ]),
                    'group' => $row['GROUP'],
                    'establishment_status' => $row['ESTABLISHMENT STATUS'],
                    'is_registered' => $row['IS REGISTERED'],
                    'registration_details' => json_encode([
                        'registration_body' => $row['REGISTRATION DETAILS/REGISTRATION BODY'],
                        'registration_number' => $row['REGISTRATION DETAILS/REGISTRATION NUMBER'],
                        'registration_date' => $row['REGISTRATION DETAILS/REGISTRATION DATE'],
                    ]),
                    'number_of_employees' => json_encode([
                        'formal' => [
                            'total' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)'],
                            'female_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS'],
                            'female_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+'],
                            'male_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS'],
                            'male_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+'],
                        ],
                        'informal' => [
                            'total' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)'],
                            'female_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS'],
                            'female_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+'],
                            'male_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS'],
                            'male_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+'],
                        ],
                    ]),
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
                    'user_id' => $this->userId,
                    'uuid' => $uuid,
                ];
                //  dd($main_data);
            }

            session()->put('uuid', $uuid);

            session()->put('batch_data.main', $main_data);
            // session()->put('batch_data', $main_data);

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows on sheet 1." . $e->getMessage());
        }
    }
}
