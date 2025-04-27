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
        'Group Name' => 'Required, Text',
        'Date Of Follow Up' => 'Required, Date (dd-mm-yyyy)',
        'EPA' => 'Required, Text',
        'Section' => 'Required, Text',
        'District' => 'Required, Text',
        'Enterprise' => 'Required, Text',
        'Number of Plantlets Produced Cassava' => 'Number (>=0)',
        'Number of Plantlets Produced Potato' => 'Number (>=0)',
        'Number of Plantlets Produced Sweet Potato' => 'Number (>=0)',
        'Screen House Vines Harvested' => 'Number (>=0)',
        'Screen House Min Tubers Harvested' => 'Number (>=0)',
        'SAH Plants Produced' => 'Number (>=0)',
        'Is Registered Seed Producer' => 'Boolean (1/0)',
        'Seed Producer Registration Number' => 'Text',
        'Seed Producer Registration Date' => 'Date (dd-mm-yyyy)',
        'Uses Certified Seed' => 'Boolean (1/0)',

        'Market Segment Fresh' => 'Boolean (1/0)',
        'Market Segment Processed' => 'Boolean (1/0)',
        'Market Segment Seed' => 'Boolean (1/0)',
        'Market Segment Cuttings' => 'Boolean (1/0)',
        'Has RTC Market Contract' => 'Boolean (1/0)',

        'Total Volume Production Produce' => 'Number (>=0), (in MT)',
        'Total Volume Production Seed Type' => 'Required, Choose One (Metric Tonnes/Bundles)',
        'Total Volume Production Seeed' => 'Number (>=0), (in MT or Bundles depending on Seed Type)',
        'Total Volume Production Cuttings' => 'Number (>=0), (in MT)',
        //'Total Volume Production' => 'Number (>=0)',

        'Production Value Produce' => 'Number (>=0),(in MT)',
        'Production Value Produce Prevailing Price' => 'Number (>=0)',
        'Production Value Seed Type' => 'Required, Choose One (Metric Tonnes/Bundles)',
        'Production Value Seed' => 'Number (>=0), (in MT or Bundles depending on Seed Type)',
        'Production Value Seed Prevailing Price' => 'Number (>=0)',
        'Production Value Cuttings' => 'Number (>=0), (in MT)',
        'Production Value Cuttings Prevailing Price' => 'Number (>=0)',
        // 'Production Value Total' => 'Number (>=0)',
        // 'Production Value Date of Maximum Sales' => 'Date (dd-mm-yyyy)',

        /**
         * Production Value USD can be calculated or manually entered
         */
        // 'Production Value USD Rate' => 'Number (>=0)',
        // 'Production Value USD Financial Value' => 'Number (>=0)',


        'Total Volume Irrigation Production Produce' => 'Number (>=0), (in MT)',
        'Total Volume Irrigation Production Seed Type' => 'Required, Choose One (Metric Tonnes/Bundles)',
        'Total Volume Irrigation Production Seeed' => 'Number (>=0), (in MT or Bundles depending on Seed Type)',
        'Total Volume Irrigation Production Cuttings' => 'Number (>=0), (in MT)',
        //'Total Volume Irrigation Production' => 'Number (>=0)',

        'Irrigation Production Value Produce' => 'Number (>=0),(in MT)',
        'Irrigation Production Value Produce Prevailing Price' => 'Number (>=0)',
        'Irrigation Production Value Seed Type' => 'Required, Choose One (Metric Tonnes/Bundles)',
        'Irrigation Production Value Seed' => 'Number (>=0), (in MT or Bundles depending on Seed Type)',
        'Irrigation Production Value Seed Prevailing Price' => 'Number (>=0)',
        'Irrigation Production Value Cuttings' => 'Number (>=0), (in MT)',
        'Irrigation Production Value Cuttings Prevailing Price' => 'Number (>=0)',
        // 'Irrigation Production Value Total' => 'Number (>=0)',
        // 'Irrigation Production Value Date of Maximum Sales' => 'Date (dd-mm-yyyy)',

        /**
         * Irrigation Production Value USD can be calculated or manually entered
         */
        // 'Irrigation Production Value USD Rate' => 'Number (>=0)',
        // 'Irrigation Production Value USD Financial Value' => 'Number (>=0)',

        'Sells to Domestic Markets' => 'Boolean (1/0)',
        'Sells to International Markets' => 'Boolean (1/0)',
        'Uses Market Information Systems' => 'Boolean (1/0)',
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

            array_keys($this->validationTypes),
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


                $dropdownOptions = [

                    'Potato',
                    'Sweet potato',
                    'Cassava'
                ];
                $this->setDataValidations($dropdownOptions, 'G3', $sheet);

                $dropdownOptions = [

                    'Metric Tonnes',
                    'Bundles',

                ];
                $this->setDataValidations($dropdownOptions, 'X3', $sheet);

                $dropdownOptions = [

                    'Metric Tonnes',
                    'Bundles',

                ];
                $this->setDataValidations($dropdownOptions, 'AC3', $sheet);

                $dropdownOptions = [

                    'Metric Tonnes',
                    'Bundles',

                ];
                $this->setDataValidations($dropdownOptions, 'AK3', $sheet);

                $dropdownOptions = [

                    'Metric Tonnes',
                    'Bundles',

                ];
                $this->setDataValidations($dropdownOptions, 'AP3', $sheet);
            },
        ];
    }


    public function title(): string
    {
        return 'Production Farmers';
    }
}