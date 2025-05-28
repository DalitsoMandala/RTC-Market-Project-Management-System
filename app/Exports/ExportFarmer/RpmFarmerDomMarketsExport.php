<?php

namespace App\Exports\ExportFarmer;

use Carbon\Carbon;
use App\Models\RpmFarmerDomMarket;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmFarmerDomMarketsExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{

    use ExportStylingTrait;
    use FormEssentials;
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Farmers Form']['Domestic Markets'];
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns we want to include, excluding 'ID', 'Status', 'Created At', and 'Updated At'
        $data = RpmFarmerDomMarket::select(
            'rpm_farmer_id',
            'date_recorded',
            'crop_type',
            'market_name',
            'district',
            'date_of_maximum_sale',
            'product_type',
            'volume_sold_previous_period',
            'financial_value_of_sales'
        )->get();

        $data->transform(function ($row) {
            $row->date_recorded = Carbon::parse($row['date_recorded'])->format('d-m-Y');
            $row->date_of_maximum_sale = Carbon::parse($row['date_of_maximum_sale'])->format('d-m-Y');
            return $row;
        });

        return $data;
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
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

                $this->setDataValidations($options, 'G3', $sheet);

                $options = [

                    'Cassava',
                    'Sweet potato',
                    'Potato'
                ];
                $this->setDataValidations($options, 'C3', $sheet);
            },
        ];
    }
    public function title(): string
    {
        return 'Domestic Markets';
    }
}
