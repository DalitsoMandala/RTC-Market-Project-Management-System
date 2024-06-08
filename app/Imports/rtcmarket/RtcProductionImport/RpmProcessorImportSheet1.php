<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Ramsey\Uuid\Nonstandard\Uuid;

HeadingRowFormatter::default('none');
class RpmProcessorImportSheet1 implements ToCollection, WithHeadingRow
{public $userId;
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
        'APPROACH', // FOR PRODUCER ORGANIZATIONS ONLY
        'SECTOR',
        'NUMBER OF MEMBERS/TOTAL',
        'NUMBER OF MEMBERS/FEMALE 18-35YRS',
        'NUMBER OF MEMBERS/FEMALE 35YRS+',
        'NUMBER OF MEMBERS/MALE 18-35YRS',
        'NUMBER OF MEMBERS/MALE 35YRS+', // FOR PRODUCER ORGANIZATIONS ONLY
        'GROUP',
        'ESTABLISHMENT STATUS', // UPPERCASE FOR ENUM VALUES
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
        'MARKET SEGMENT/FRESH',
        'MARKET SEGMENT/PROCESSED', // MULTIPLE MARKET SEGMENTS (ARRAY OF STRINGS)
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
                    'location_data' => json_encode([
                        'enterprise' => $row['ENTERPRISE'],
                        'district' => $row['DISTRICT'],
                        'epa' => $row['EPA'],
                        'section' => $row['SECTION'],
                    ]),
                    'date_of_recruitment' => $row['DATE OF RECRUITMENT'],
                    'name_of_actor' => $row['NAME OF ACTOR'],
                    'name_of_representative' => $row['NAME OF REPRESENTATIVE'],
                    'phone_number' => $row['PHONE NUMBER'],
                    'type' => $row['TYPE'],
                    'approach' => $row['APPROACH'], // For producer organizations only
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
                        'formal_total' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES (TOTAL)'],
                        'formal_female_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 18-35YRS'],
                        'formal_female_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/FEMALE 35YRS+'],
                        'formal_male_18_35' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 18-35YRS'],
                        'formal_male_35_plus' => $row['NUMBER OF EMPLOYEES/FORMAL EMPLOYEES/MALE 35YRS+'],
                        'informal_total' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES (TOTAL)'],
                        'informal_female_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 18-35YRS'],
                        'informal_female_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/FEMALE 35YRS+'],
                        'informal_male_18_35' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 18-35YRS'],
                        'informal_male_35_plus' => $row['NUMBER OF EMPLOYEES/INFORMAL EMPLOYEES/MALE 35YRS+'],
                    ]),
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

            }

            session()->put('uuid', $uuid);

            session()->put('batch_data.main', $main_data);
            // session()->put('batch_data', $main_data);

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows on sheet 1." . $e->getMessage());
        }
    }}
