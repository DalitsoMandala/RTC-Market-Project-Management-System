<?php

namespace App\Imports\rtcmarket\SchoolConsumptionImport;

use App\Helpers\ImportValidateHeading;
use Exception;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;
use Ramsey\Uuid\Uuid;

HeadingRowFormatter::default('none');
class SrcImport implements ToCollection, WithHeadingRow, WithEvents
{
    use Importable, RegistersEventListeners;
    public $userId;
    public $sheetNames = [];

    public $file;

    public function __construct($userId, $sheets, $file = null)
    {
        $this->userId = $userId;
        $this->sheetNames = $sheets;
        $this->file = $file;
    }
    protected $expectedSheetNames = [
        'SCHOOL_CONSUMPTION',

    ];

    private $failures = [];
    protected $expectedHeadings = [
        'ENTERPRISE',
        'DISTRICT',
        'EPA',
        'SECTION',
        'DATE',
        'CROP',
        'MALES',
        'FEMALE',
        'TOTAL',
    ];
    public function collection(Collection $collection)
    {
        $headings = $collection->first()->keys()->toArray();

        // Check if the headings match the expected headings
        $missingHeadings = ImportValidateHeading::validateHeadings($headings, $this->expectedHeadings);

        if (count($missingHeadings) > 0) {
            dd($missingHeadings);
            throw new Exception("Something went wrong. Please upload your data using the template file above");

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
                    'date' => $row['DATE'],
                    'crop' => $row['CROP'],
                    'male_count' => $row['MALES'],
                    'female_count' => $row['FEMALE'],
                    'total' => $row['TOTAL'],
                    'user_id' => $this->userId,
                    'uuid' => $uuid,
                ];
            }

            session()->put('uuid', $uuid);
            session()->put('batch_data', $main_data);

        } catch (\Throwable $e) {
            throw new Exception("Something went wrong. There was some errors on some rows." . $e->getMessage());
        }
    }

    public function registerEvents(): array
    {
        return [
            // Handle by a closure.
            BeforeImport::class => function (BeforeImport $event) {
                // dd($event);
                $diff = ImportValidateHeading::validateHeadings($this->sheetNames, $this->expectedSheetNames);

                if (count($diff) > 0) {
                    session()->flash('error-import', "File contains invalid sheets!");
                    throw new Exception("File contains invalid sheets!");

                }

            },

        ];
    }
}