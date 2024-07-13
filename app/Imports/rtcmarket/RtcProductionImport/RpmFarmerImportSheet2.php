<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Exceptions\SheetImportException;
use App\Exceptions\UserErrorException;
use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Maatwebsite\Excel\Validators\Failure;

HeadingRowFormatter::default('none');
class RpmFarmerImportSheet2 implements ToCollection, WithHeadingRow, WithValidation, SkipsOnFailure
{
    public $userId;
    public $file;
    // follow up
    public $expectedHeadings = [
        'RECRUIT ID',
        'ENTERPRISE',
        'GROUP NAME',
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

            throw new UserErrorException("Something went wrong. Please upload your data using the template file above");

        }


        foreach ($collection as $row) {




            $main_data[] = [
                'rpm_farmer_id' => $row['RECRUIT ID'],
                'location_data' => json_encode([
                    'enterprise' => $row['ENTERPRISE'],
                    'district' => $row['DISTRICT'],
                    'epa' => $row['EPA'],
                    'section' => $row['SECTION'],
                    'group_name' => $row['GROUP NAME'],
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
                // 'market_segment' => json_encode([
                //     'fresh' => $row['MARKET SEGMENT/FRESH'],
                //     'processed' => $row['MARKET SEGMENT/PROCESSED'],
                // ]),
                // 'has_rtc_market_contract' => $row['HAS RTC MARKET CONTRACT'],
                // 'total_vol_production_previous_season' => $row['TOTAL VOLUME PRODUCTION PREVIOUS SEASON'],
                // 'total_production_value_previous_season' => json_encode([
                //     'total' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/TOTAL'],
                //     'date_of_maximum_sales' => $row['TOTAL VALUE PRODUCTION PREVIOUS SEASON (FINANCIAL VALUE-MWK)/DATE OF MAXIMUM SALES'],
                // ]),
                // 'total_vol_irrigation_production_previous_season' => $row['TOTAL VOLUME IRRIGATION PRODUCTION PREVIOUS SEASON'],
                // 'total_irrigation_production_value_previous_season' => json_encode([
                //     'total' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/TOTAL'],
                //     'date_of_maximum_sales' => $row['TOTAL IRRIGATION PRODUCTION VALUE PREVIOUS SEASON/DATE OF MAXIMUM SALES'],
                // ]),
                // 'sells_to_domestic_markets' => $row['SELLS TO DOMESTIC MARKETS'],
                // 'sells_to_international_markets' => $row['SELLS TO INTERNATIONAL MARKETS'],
                // 'uses_market_information_systems' => $row['USES MARKET INFORMATION SYSTEMS'],
                // 'market_information_systems' => $row['MARKET INFORMATION SYSTEMS'],
                // 'aggregation_centers' => json_encode([
                //     'response' => $row['AGGREGATION CENTERS/RESPONSE'],
                //     'specify' => $row['AGGREGATION CENTERS/SPECIFY'],
                // ]),
                // 'aggregation_center_sales' => $row['TOTAL AGGREGATION CENTER SALES VOLUME'],
                //  'user_id' => $this->userId,
                //'uuid' => session()->get('uuid'),
            ];

        }

        session()->put('batch_data.followup', $main_data);


    }
    public function rules(): array
    {

        $main_data = [];
        $getBatchMainData = session()->get('batch_data');
        $ids = array();
        if (!empty($getBatchMainData['main'])) {
            $ids = collect($getBatchMainData['main'])->pluck('#')->toArray();

        }
        return [
            'RECRUIT ID' => ['required', 'integer', Rule::in($ids)],
            'ENTERPRISE' => ['required', 'string'],
            'GROUP NAME' => ['required', 'string'],
            'DISTRICT' => ['required', 'string'],
            'EPA' => ['required', 'string'],
            'SECTION' => ['required', 'string'],
            'AREA UNDER CULTIVATION/TOTAL' => ['nullable', 'numeric'],
            'AREA UNDER CULTIVATION/VARIETY 1 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CULTIVATION/VARIETY 2 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CULTIVATION/VARIETY 3 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CULTIVATION/VARIETY 4 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CULTIVATION/VARIETY 5 (SPECIFY)' => ['nullable', 'string'],
            'NUMBER OF PLANTLETS PRODUCED/CASSAVA' => ['nullable', 'numeric'],
            'NUMBER OF PLANTLETS PRODUCED/POTATO' => ['nullable', 'numeric'],
            'NUMBER OF PLANTLETS PRODUCED/SWEET POTATO' => ['nullable', 'numeric'],
            'NUMBER OF SCREEN HOUSE VINES HARVESTED' => ['nullable', 'numeric'],
            'NUMBER OF SCREEN HOUSE MIN TUBERS HARVESTED' => ['nullable', 'numeric'],
            'NUMBER OF SAH PLANTS PRODUCED' => ['nullable', 'numeric'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/TOTAL' => ['nullable', 'numeric'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 1 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 2 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 3 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 4 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 5 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 6 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER BASIC SEED MULTIPLICATION (NUMBER OF ACRES)/VARIETY 7 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/TOTAL' => ['nullable', 'numeric'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 1 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 2 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 3 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 4 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 5 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 6 (SPECIFY)' => ['nullable', 'string'],
            'AREA UNDER CERTIFIED SEED MULTIPLICATION/VARIETY 7 (SPECIFY)' => ['nullable', 'string'],
            'IS REGISTERED SEED PRODUCER' => ['nullable', 'in:YES,NO'],
            'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION NUMBER' => ['nullable', 'string'],
            'REGISTRATION DETAILS (SEED SERVICES UNIT)/REGISTRATION DATE' => ['nullable', 'date'],
            'USES CERTIFIED SEED' => ['nullable', 'in:YES,NO'],
        ];
    }


    public function onFailure(Failure ...$failures)
    {

        $errors = [];
        foreach ($failures as $failure) {
            $errors[] = [
                'row' => $failure->row(),
                'attribute' => $failure->attribute(),
                'errors' => $failure->errors(),
                'values' => $failure->values(),
            ];
        }
        throw new SheetImportException('RTC_FARM_FLUP', $errors);

    }
}
