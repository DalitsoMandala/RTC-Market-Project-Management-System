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
    use \App\Traits\FormEssentials;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Processors Form']['Production Processors'];
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
