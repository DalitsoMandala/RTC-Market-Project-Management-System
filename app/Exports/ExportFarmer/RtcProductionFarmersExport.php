<?php

namespace App\Exports\ExportFarmer;

use Carbon\Carbon;
use App\Models\RtcProductionFarmer;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionFarmersExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStrictNullComparison, ShouldAutoSize, WithEvents
{



    use ExportStylingTrait;




    protected $rowNumber = 0; // Start counting from 1
    public $template;
    protected $validationTypes = [
        'ID' => 'Required, Unique,  Number',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'District' => 'Required, Text',
        'Enterprise' => 'Required, Text',
        'Date of Recruitment' => 'Date (dd-mm-yyyy)',
        'Name of Actor' => 'Text',
        'Name of Representative' => 'Text',
        'Phone Number' => 'Text',
        'Type' => 'Text, (Choose one option)',
        'Approach' => 'Text, (Choose one option)',
        'Sector' => 'Text, (Choose one option)',
        'Members Female 18-35' => 'Number (>=0)',
        'Members Male 18-35' => 'Number (>=0)',
        'Members Male 35+' => 'Number (>=0)',
        'Members Female 35+' => 'Number (>=0)',
        'Group' => 'Text, (Choose one option)',
        'Establishment Status' => 'New/Old, (Choose one option)',
        'Is Registered' => 'Boolean (1/0, true/false)',
        'Registration Body' => 'Text',
        'Registration Number' => 'Text',
        'Registration Date' => 'Date (dd-mm-yyyy)',
        'Employees Formal Female 18-35' => 'Number (>=0)',
        'Employees Formal Male 18-35' => 'Number (>=0)',
        'Employees Formal Male 35+' => 'Number (>=0)',
        'Employees Formal Female 35+' => 'Number (>=0)',
        'Employees Informal Female 18-35' => 'Number (>=0)',
        'Employees Informal Male 18-35' => 'Number (>=0)',
        'Employees Informal Male 35+' => 'Number (>=0)',
        'Employees Informal Female 35+' => 'Number (>=0)',
        'Number of Plantlets Produced Cassava' => 'Number (>=0)',
        'Number of Plantlets Produced Potato' => 'Number (>=0)',
        'Number of Plantlets Produced Sweet Potato' => 'Number (>=0)',
        'Screen House Vines Harvested' => 'Number (>=0)',
        'Screen House Min Tubers Harvested' => 'Number (>=0)',
        'SAH Plants Produced' => 'Number (>=0)',
        'Is Registered Seed Producer' => 'Boolean (1/0, true/false)',
        'Seed Producer Registration Number' => 'Text',
        'Seed Producer Registration Date' => 'Date (dd-mm-yyyy)',
        'Uses Certified Seed' => 'Boolean (1/0, true/false)',
        'Market Segment Fresh' => 'Boolean (1/0, true/false)',
        'Market Segment Processed' => 'Boolean (1/0, true/false)',
        'Has RTC Market Contract' => 'Boolean (1/0, true/false)',
        'Total Volume Production Previous Season' => 'Number (>=0)',
        'Production Value Previous Season Total' => 'Number (>=0)',
        'Production Value Date of Max Sales' => 'Date (dd-mm-yyyy)',
        'Production Value USD Rate' => 'Number (>=0)',
        'Production Value USD Value' => 'Number (>=0)',
        'Total Volume Irrigation Production Previous Season' => 'Number (>=0)',
        'Irrigation Production Value Total' => 'Number (>=0)',
        'Irrigation Production Date of Max Sales' => 'Date (dd-mm-yyyy)',
        'Irrigation Production USD Rate' => 'Number (>=0)',
        'Irrigation Production USD Value' => 'Number (>=0)',
        'Sells to Domestic Markets' => 'Boolean (1/0, true/false)',
        'Sells to International Markets' => 'Boolean (1/0, true/false)',
        'Uses Market Information Systems' => 'Boolean (1/0, true/false)',
        'Sells to Aggregation Centers' => 'Boolean (1/0, true/false)',
        'Total Volume Aggregation Center Sales' => 'Number (>=0)',
    ];

    public function __construct($template)
    {
        $this->template = $template;
    }

    public function collection()
    {

        if ($this->template) {
            return collect([]);
        }

        // Select only the columns to be included in the export
        $data = RtcProductionFarmer::select(
            'id',
            'epa',
            'section',
            'district',
            'enterprise',
            'date_of_recruitment',
            'name_of_actor',
            'name_of_representative',
            'phone_number',
            'type',
            'approach',
            'sector',
            'mem_female_18_35',
            'mem_male_18_35',
            'mem_male_35_plus',
            'mem_female_35_plus',
            'group',
            'establishment_status',
            'is_registered',
            'registration_body',
            'registration_number',
            'registration_date',
            'emp_formal_female_18_35',
            'emp_formal_male_18_35',
            'emp_formal_male_35_plus',
            'emp_formal_female_35_plus',
            'emp_informal_female_18_35',
            'emp_informal_male_18_35',
            'emp_informal_male_35_plus',
            'emp_informal_female_35_plus',
            'number_of_plantlets_produced_cassava',
            'number_of_plantlets_produced_potato',
            'number_of_plantlets_produced_sweet_potato',
            'number_of_screen_house_vines_harvested',
            'number_of_screen_house_min_tubers_harvested',
            'number_of_sah_plants_produced',
            'is_registered_seed_producer',
            'registration_number_seed_producer',
            'registration_date_seed_producer',
            'uses_certified_seed',
            'market_segment_fresh',
            'market_segment_processed',
            'has_rtc_market_contract',
            'total_vol_production_previous_season',
            'prod_value_previous_season_total',
            'prod_value_previous_season_date_of_max_sales',
            'prod_value_previous_season_usd_rate',
            'prod_value_previous_season_usd_value',
            'total_vol_irrigation_production_previous_season',
            'irr_prod_value_previous_season_total',
            'irr_prod_value_previous_season_date_of_max_sales',
            'irr_prod_value_previous_season_usd_rate',
            'irr_prod_value_previous_season_usd_value',
            'sells_to_domestic_markets',
            'sells_to_international_markets',
            'uses_market_information_systems',
            'sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales'
        )->get();

        $data->transform(function ($row) {
            $row->date_of_recruitment = Carbon::parse($row['date_of_recruitment'])->format('d-m-Y');
            $row->registration_date = Carbon::parse($row['registration_date'])->format('d-m-Y');
            $row->prod_value_previous_season_date_of_max_sales = Carbon::parse($row['prod_value_previous_season_date_of_max_sales'])->format('d-m-Y');
            $row->irr_prod_value_previous_season_date_of_max_sales = Carbon::parse($row['irr_prod_value_previous_season_date_of_max_sales'])->format('d-m-Y');
            $row->registration_date_seed_producer = Carbon::parse($row['registration_date_seed_producer'])->format('d-m-Y');
            return $row;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            [
                'ID', // Add ID as the first column heading
                'EPA',
                'Section',
                'District',
                'Enterprise',
                'Date of Recruitment',
                'Name of Actor',
                'Name of Representative',
                'Phone Number',
                'Type',
                'Approach',
                'Sector',
                'Members Female 18-35',
                'Members Male 18-35',
                'Members Male 35+',
                'Members Female 35+',
                'Group',
                'Establishment Status',
                'Is Registered',
                'Registration Body',
                'Registration Number',
                'Registration Date',
                'Employees Formal Female 18-35',
                'Employees Formal Male 18-35',
                'Employees Formal Male 35+',
                'Employees Formal Female 35+',
                'Employees Informal Female 18-35',
                'Employees Informal Male 18-35',
                'Employees Informal Male 35+',
                'Employees Informal Female 35+',
                'Number of Plantlets Produced Cassava',
                'Number of Plantlets Produced Potato',
                'Number of Plantlets Produced Sweet Potato',
                'Screen House Vines Harvested',
                'Screen House Min Tubers Harvested',
                'SAH Plants Produced',
                'Is Registered Seed Producer',
                'Seed Producer Registration Number',
                'Seed Producer Registration Date',
                'Uses Certified Seed',
                'Market Segment Fresh',
                'Market Segment Processed',
                'Has RTC Market Contract',
                'Total Volume Production Previous Season',
                'Production Value Previous Season Total',
                'Production Value Date of Max Sales',
                'Production Value USD Rate',
                'Production Value USD Value',
                'Total Volume Irrigation Production Previous Season',
                'Irrigation Production Value Total',
                'Irrigation Production Date of Max Sales',
                'Irrigation Production USD Rate',
                'Irrigation Production USD Value',
                'Sells to Domestic Markets',
                'Sells to International Markets',
                'Uses Market Information Systems',
                'Sells to Aggregation Centers',
                'Total Volume Aggregation Center Sales'
            ],
            array_values($this->validationTypes)
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++; // Increment ID for each row

        return array_merge(
            [$this->rowNumber], // Add ID column value
            array_values($row->toArray()) // Map remaining row values
        );
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
                    'Producer Organization (PO)',
                    'Large scale farm',


                ]; // Includes an empty option


                $this->setDataValidations($dropdownOptions, 'J3', $sheet);

                $dropdownOptions = [
                    '',
                    'Collective production only',
                    'Collective marketing only',
                    'Knowledge Sharing only',
                    'Collective producing, marketing and knowledge sharing',
                    'NA'

                ];

                $this->setDataValidations($dropdownOptions, 'K3', $sheet);

                $dropdownOptions = [
                    'Private',
                    'Public'
                ];


                $this->setDataValidations($dropdownOptions, 'L3', $sheet);

                $dropdownOptions = [
                    'Early generation seed producer',
                    'Seed multiplier',
                    'RTC producer'

                ];

                $this->setDataValidations($dropdownOptions, 'Q3', $sheet);


                $dropdownOptions = [
                    'New',
                    'Old'
                ];

                $this->setDataValidations($dropdownOptions, 'R3', $sheet);
            },
        ];
    }


    public function title(): string
    {
        return 'Production Farmers';
    }
}
