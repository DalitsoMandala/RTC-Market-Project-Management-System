<?php

namespace App\Exports\HouseholdExport;

use Carbon\Carbon;
use App\Traits\ExportStylingTrait;
use Illuminate\Support\Collection;
use App\Models\HouseholdRtcConsumption;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class HouseholdSheetExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{

    use ExportStylingTrait;
    protected $uuid;
    protected $userId;
    protected $submissionPeriodId;
    protected $organisationId;
    protected $financialYearId;
    protected $reportingPeriodMonthId;
    protected $status;
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }

    protected $validationTypes = [
        'ID' => 'Required, Unique, Number',
        'EPA' => 'Required, Text (255)',
        'Section' => 'Text (255)',
        'District' => 'Required, Text (255)',
        'Enterprise' => 'Required, Text (255)',
        'Date of Assessment' => 'Date (dd-mm-yyyy)',
        'Actor Type (Farmer, Trader, etc.)' => 'Text (255), (Choose one option)',
        'RTC Group/Platform' => 'Text (255)',
        'Producer Organisation' => 'Text (255)',
        'Actor Name' => 'Text (255)',
        'Age Group' => 'Text (255)',
        'Sex' => 'Male/Female/1/2',
        'Phone Number' => 'Text (255)',
        'Household Size' => 'Number (>=0)',
        'Under 5 in Household' => 'Number (>=0)',
        'RTC Consumers (Total)' => 'Number (>=0)',
        'RTC Consumers - Potato' => 'Number (>=0)',
        'RTC Consumers - Sweet Potato' => 'Number (>=0)',
        'RTC Consumers - Cassava' => 'Number (>=0)',
        'RTC Consumption Frequency' => 'Number (0-365)',
    ];

    public function headings(): array
    {
        return [
            [
                'ID',
                'EPA',
                'Section',
                'District',
                'Enterprise',
                'Date of Assessment',
                'Actor Type (Farmer, Trader, etc.)',
                'RTC Group/Platform',
                'Producer Organisation',
                'Actor Name',
                'Age Group',
                'Sex',
                'Phone Number',
                'Household Size',
                'Under 5 in Household',
                'RTC Consumers (Total)',
                'RTC Consumers - Potato',
                'RTC Consumers - Sweet Potato',
                'RTC Consumers - Cassava',
                'RTC Consumption Frequency',
            ],
            array_values($this->validationTypes)


        ];
    }

    public function collection(): Collection
    {

        if ($this->template) {
            return collect([]);
        }
        $data = HouseholdRtcConsumption::select([
            'id',
            'epa',
            'section',
            'district',
            'enterprise',
            'date_of_assessment',
            'actor_type',
            'rtc_group_platform',
            'producer_organisation',
            'actor_name',
            'age_group',
            'sex',
            'phone_number',
            'household_size',
            'under_5_in_household',
            'rtc_consumers',
            'rtc_consumers_potato',
            'rtc_consumers_sw_potato',
            'rtc_consumers_cassava',
            'rtc_consumption_frequency',

            // Exclude hidden fields and Household ID
        ])->get();

        $data->transform(function ($row) {
            $row->date_of_assessment = Carbon::parse($row->date_of_assessment)->format('d-m-Y');
            return $row;
        });
        return $data;
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestColumn = $sheet->getHighestColumn();
                // Make the first row (header) bold
                $sheet->getStyle("A1:{$highestColumn}1")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                    ],
                ]);

                // Set background color for the second row (A2:ZZ2)
                $sheet->getStyle("A2:{$highestColumn}2")->applyFromArray([
                    'font' => [
                        'color' => ['rgb' => 'FF0000'], // Red text
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFC5'], // Pink background
                    ],

                ]);

                $sheet = $event->sheet->getDelegate();

                // Define the dropdown options
                $dropdownOptions = [
                    '',
                    'Farmer',
                    'Aggregator',
                    'Transporter',
                    'Processor',
                    //   'Employees on RTC establishment',
                    'Individuals from nutrition interventions',
                    'Other'
                ]; // Includes an empty option

                $this->setDataValidations($dropdownOptions, 'G3', $sheet);
            },
        ];
    }

    public function title(): string
    {
        return 'Household Data';
    }
}
