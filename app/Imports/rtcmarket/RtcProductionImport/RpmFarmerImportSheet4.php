<?php

namespace App\Imports\rtcmarket\RtcProductionImport;

use App\Helpers\ImportValidateHeading;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\HeadingRowImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

HeadingRowFormatter::default('none');
class RpmFarmerImportSheet4 implements ToCollection, WithHeadingRow// DOM MARKETS

{public $userId;public $file;
    public $expectedHeadings = ['RECRUIT ID',
        'DATE RECORDED',
        'CROP TYPE',
        'MARKET NAME',
        'DISTRICT',
        'DATE OF MAXIMUM SALE',
        'PRODUCT TYPE',
        'VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)',
        'FINANCIAL VALUE OF SALES',
    ];
    public function __construct($userId, $file)
    {
        $this->userId = $userId;
        $this->file = $file;
    }
    public function collection(Collection $collection)
    {

        $headings = (new HeadingRowImport)->toArray($this->file);

        $headings = $headings[3][0];

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {
            throw new \Exception("Something went wrong. Please upload your data using the template file above");

        }

        try {

            $main_data = [];

            foreach ($collection as $row) {

                $main_data[] = [
                    'rpm_farmer_id' => $row['RECRUIT ID'],
                    'date_recorded' => $row['DATE RECORDED'],
                    'crop_type' => $row['CROP TYPE'],
                    'market_name' => $row['MARKET NAME'],
                    'district' => $row['DISTRICT'],
                    'date_of_maximum_sale' => $row['DATE OF MAXIMUM SALE'],
                    'product_type' => $row['PRODUCT TYPE'],
                    'volume_sold_previous_period' => $row['VOLUME SOLD PREVIOUS PERIOD (METRIC TONNES)'],
                    'financial_value_of_sales' => $row['FINANCIAL VALUE OF SALES'],
                    // 'user_id' => $this->userId,
                    //  'uuid' => session()->get('uuid'),
                ];

            }

            session()->put('batch_data.market', $main_data);

        } catch (\Throwable $e) {
            throw new \Exception("Something went wrong. There was some errors on some rows on sheet 4." . $e->getMessage());
        }
    }}
