<?php

namespace App\Exports;

use App\Models\SeedBeneficiary;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class CropSheetExportCassava extends CropSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    use FormEssentials;

    protected $cropType;
    public $template = false;
    protected $validationTypes = [];

    public function __construct(string $cropType, $template = false)
    {
        $this->cropType = $cropType;
        $this->template = $template;
        $this->validationTypes = $this->forms['Seed Beneficiaries Form']['Cassava'];
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
                        'color' => ['rgb' => 'FF0000'],  // Red text
                        'bold' => true,
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFFFC5'],  // Pink background
                    ],
                ]);

                $sheet = $event->sheet->getDelegate();
                $dropdownOptions = [
                    'Single', 'Married', 'Divorced', 'Widowed', 'Separated', 'Polygamy'
                ];
                $this->setDataValidations($dropdownOptions, 'L3', $sheet);
                $dropdownOptions = [
                    'FHH', 'MHH', 'CHH'
                ];
                $this->setDataValidations($dropdownOptions, 'M3', $sheet);

                $dropdownOptions = [
                    'Male', 'Female'
                ];
                $this->setDataValidations($dropdownOptions, 'P3', $sheet);

                $dropdownOptions = [
                    'Caregroup', 'School feeding', 'Commercial'
                ];
                $this->setDataValidations($dropdownOptions, 'R3', $sheet);

                $dropdownOptions = [
                    'Mother', 'Baby', 'Ordinary demonstration'
                ];
                $this->setDataValidations($dropdownOptions, 'S3', $sheet);
                $dropdownOptions = [
                    'Rainfed',
                    'Winter',
                ];
                $this->setDataValidations($dropdownOptions, 'W3', $sheet);
            },
        ];
    }

    public function title(): string
    {
        return $this->cropType;
    }
}
