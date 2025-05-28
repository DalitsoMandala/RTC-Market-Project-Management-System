<?php

namespace App\Exports\ExportProcessor;

use Carbon\Carbon;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Models\RpmProcessorConcAgreement;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmProcessorConcAgreementsExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    protected $rowNumber = 0;

    public $template;
    use \App\Traits\FormEssentials;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Processors Form']['Contractual Agreements'];
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);  // Return an empty collection if the template is not provided.
        }
        $data = RpmProcessorConcAgreement::get();
        $data->transform(function ($row) {
            $row->date_recorded = Carbon::parse($row['date_recorded'])->format('d-m-Y');
            $row->date_of_maximum_sale = Carbon::parse($row['date_of_maximum_sale'])->format('d-m-Y');
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

                $options = [

                    'Seed',
                    'Ware',
                    'Value added products',
                    'Fresh'
                ];

                $this->setDataValidations($options, 'F3', $sheet);
            },
        ];
    }

    public function map($row): array
    {

        return [

            $row->rpm_processor_id,
            $row->date_recorded,
            $row->partner_name,
            $row->country,
            $row->date_of_maximum_sale,
            $row->product_type,
            $row->volume_sold_previous_period,
            $row->financial_value_of_sales
        ];
    }

    public function title(): string
    {
        return 'Contractual Agreements';
    }
}
