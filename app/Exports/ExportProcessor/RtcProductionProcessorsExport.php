<?php

namespace App\Exports\ExportProcessor;

use Carbon\Carbon;
use App\Traits\ExportStylingTrait;
use App\Models\RtcProductionProcessor;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionProcessorsExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStrictNullComparison, ShouldAutoSize, WithEvents
{
    use ExportStylingTrait;
    protected $rowNumber = 0;

    public $template;

    public $validationTypes = [
        'ID' => 'Required, Number',
        'Group Name' => 'Required, Text',
        'Date Of Follow Up' => 'Required, Date (dd-mm-yyyy)',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'District' => 'Required, Text',
        'Enterprise' => 'Required, Text',
        // 'Date of Recruitment' => 'Date (dd-mm-yyyy)',
        // 'Name of Actor' => 'Text',
        // 'Name of Representative' => 'Text',
        // 'Phone Number' => 'Text',
        // 'Type' => 'Text, (Choose One)',
        // 'Approach' => 'Text, (Choose One)',
        // 'Sector' => 'Text, (Choose One)',
        // 'Members Female 18-35' => 'Number (>=0)',
        // 'Members Male 18-35' => 'Number (>=0)',
        // 'Members Male 35+' => 'Number (>=0)',
        // 'Members Female 35+' => 'Number (>=0)',
        // 'Group' => 'Text',
        // 'Establishment Status' => 'New/Old (Choose One)',
        // 'Is Registered' => 'Boolean (1/0)',
        // 'Registration Body' => 'Text',
        // 'Registration Number' => 'Text',
        // 'Registration Date' => 'Date (dd-mm-yyyy)',
        // 'Employees Formal Female 18-35' => 'Number (>=0)',
        // 'Employees Formal Male 18-35' => 'Number (>=0)',
        // 'Employees Formal Male 35+' => 'Number (>=0)',
        // 'Employees Formal Female 35+' => 'Number (>=0)',
        // 'Employees Informal Female 18-35' => 'Number (>=0)',
        // 'Employees Informal Male 18-35' => 'Number (>=0)',
        // 'Employees Informal Male 35+' => 'Number (>=0)',
        // 'Employees Informal Female 35+' => 'Number (>=0)',
        'Market Segment Fresh' => 'Boolean (1/0)',
        'Market Segment Processed' => 'Boolean (1/0)',
        'Has RTC Market Contract' => 'Boolean (1/0)',
        'Total Volume Production Previous Season' => 'Number (>=0)',
        'Production Value Previous Season Total' => 'Number (>=0)',
        'Production Value Date of Max Sales' => 'Date (dd-mm-yyyy)',
        'USD Rate' => 'Number (>=0)',
        'USD Value' => 'Number (>=0)',
        'Sells to Domestic Markets' => 'Boolean (1/0)',
        'Sells to International Markets' => 'Boolean (1/0)',
        'Uses Market Info Systems' => 'Boolean (1/0)',
        'Sells to Aggregation Centers' => 'Boolean (1/0)',
        'Total Volume Aggregation Center Sales' => 'Number (>=0)',
    ];

    public function __construct($template)
    {

        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);  // Return an empty collection if the template is not provided.
        }
        $data = RtcProductionProcessor::get();
        $data->transform(function ($row) {
            $row->date_of_recruitment = Carbon::parse($row['date_of_recruitment'])->format('d-m-Y');
            $row->registration_date = Carbon::parse($row['registration_date'])->format('d-m-Y');
            $row->prod_value_previous_season_date_of_max_sales = Carbon::parse($row['prod_value_previous_season_date_of_max_sales'])->format('d-m-Y');
            return $row;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            // [
            //     'ID',
            //     'EPA',
            //     'Section',
            //     'District',
            //     'Enterprise',
            //     'Date of Recruitment',
            //     'Name of Actor',
            //     'Name of Representative',
            //     'Phone Number',
            //     'Type',
            //     'Approach',
            //     'Sector',
            //     'Members Female 18-35',
            //     'Members Male 18-35',
            //     'Members Male 35+',
            //     'Members Female 35+',
            //     'Group',
            //     'Establishment Status',
            //     'Is Registered',
            //     'Registration Body',
            //     'Registration Number',
            //     'Registration Date',
            //     'Employees Formal Female 18-35',
            //     'Employees Formal Male 18-35',
            //     'Employees Formal Male 35+',
            //     'Employees Formal Female 35+',
            //     'Employees Informal Female 18-35',
            //     'Employees Informal Male 18-35',
            //     'Employees Informal Male 35+',
            //     'Employees Informal Female 35+',
            //     'Market Segment Fresh',
            //     'Market Segment Processed',
            //     'Has RTC Market Contract',
            //     'Total Volume Production Previous Season',
            //     'Production Value Previous Season Total',
            //     'Production Value Date of Max Sales',
            //     'USD Rate',
            //     'USD Value',
            //     'Sells to Domestic Markets',
            //     'Sells to International Markets',
            //     'Uses Market Info Systems',
            //     'Sells to Aggregation Centers',
            //     'Total Volume Aggregation Center Sales'
            // ],
            array_keys($this->validationTypes),
            array_values($this->validationTypes)

        ];
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

                // // Define the dropdown options
                // $dropdownOptions = [

                //     'Producer Organization (PO)',
                //     'Large scale farm',
                //     'Small medium enterprise (SME)'


                // ]; // Includes an empty option


                // $this->setDataValidations($dropdownOptions, 'J3', $sheet);

                // $dropdownOptions = [

                //     'Collective production only',
                //     'Collective marketing only',
                //     'Knowledge Sharing only',
                //     'Collective producing, marketing and knowledge sharing',
                //     'NA'

                // ];

                // $this->setDataValidations($dropdownOptions, 'K3', $sheet);

                // $dropdownOptions = [
                //     'Private',
                //     'Public'
                // ];


                // $this->setDataValidations($dropdownOptions, 'L3', $sheet);

                // $dropdownOptions = [
                //     'Other',

                // ];

                // $this->setDataValidations($dropdownOptions, 'Q3', $sheet);


                // $dropdownOptions = [
                //     'New',
                //     'Old'
                // ];

                // $this->setDataValidations($dropdownOptions, 'R3', $sheet);

                $dropdownOptions = [

                    'Potato',
                    'Sweet potato',
                    'Cassava'
                ];
                $this->setDataValidations($dropdownOptions, 'G3', $sheet);
            },
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $row->epa,
            $row->section,
            $row->district,
            $row->enterprise,
            $row->date_of_recruitment,
            $row->name_of_actor,
            $row->name_of_representative,
            $row->phone_number,
            $row->type,
            $row->approach,
            $row->sector,
            $row->mem_female_18_35,
            $row->mem_male_18_35,
            $row->mem_male_35_plus,
            $row->mem_female_35_plus,
            $row->group,
            $row->establishment_status,
            $row->is_registered,
            $row->registration_body,
            $row->registration_number,
            $row->registration_date,
            $row->emp_formal_female_18_35,
            $row->emp_formal_male_18_35,
            $row->emp_formal_male_35_plus,
            $row->emp_formal_female_35_plus,
            $row->emp_informal_female_18_35,
            $row->emp_informal_male_18_35,
            $row->emp_informal_male_35_plus,
            $row->emp_informal_female_35_plus,
            $row->market_segment_fresh,
            $row->market_segment_processed,
            $row->has_rtc_market_contract,
            $row->total_vol_production_previous_season,
            $row->prod_value_previous_season_total,
            $row->prod_value_previous_season_date_of_max_sales,
            $row->prod_value_previous_season_usd_rate,
            $row->prod_value_previous_season_usd_value,
            $row->sells_to_domestic_markets,
            $row->sells_to_international_markets,
            $row->uses_market_information_systems,
            $row->sells_to_aggregation_centers,
            $row->total_vol_aggregation_center_sales,
        ];
    }

    public function title(): string
    {
        return 'Production Processors';
    }
}