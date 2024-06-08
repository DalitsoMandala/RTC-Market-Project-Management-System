<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class RpmProcessorImportSheet2 implements ToCollection, WithHeadingRow// FOLLOW UP

{
    public $userId;public $file;
// follow up

    public function __construct($userId, $file)
    {
        $this->userId = $userId;
        $this->file = $file;
    }
    public $expectedHeadings = [
        'RECRUIT ID',
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

    public function collection(Collection $collection)
    {

        $headings = (new HeadingRowImport)->toArray($this->file);

        $headings = $headings[1][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {

            throw new \Exception("Something went wrong. Please upload your data using the template file above");

        }

        try {

            $main_data = [];

            foreach ($collection as $row) {

                $main_data[] = [
                    'rpm_processor_id' => $row['RECRUIT ID'],
                    'date_of_follow_up' => $row['DATE OF FOLLOW UP'],
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
                ];

            }

            session()->put('batch_data.followup', $main_data);

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows on sheet 2." . $e->getMessage());
        }
    }
}
