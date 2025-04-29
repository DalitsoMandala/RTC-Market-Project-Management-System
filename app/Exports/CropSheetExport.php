<?php

namespace App\Exports;

use Carbon\Carbon;
use App\Models\SeedBeneficiary;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class CropSheetExport implements FromCollection, WithHeadings, WithTitle, WithEvents, ShouldAutoSize
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
        $this->validationTypes = $this->forms['Seed Beneficiaries Form']['Potato'];
    }



    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        $data = SeedBeneficiary::where('crop', $this->cropType)->select(
            //  'crop',
            'district',
            'epa',
            'section',
            'name_of_aedo',
            'aedo_phone_number',
            'date',
            'name_of_recipient',
            'group_name',
            'village',
            'sex',
            'age',
            'marital_status',
            'hh_head',
            'household_size',
            'children_under_5',
            'variety_received',
            'bundles_received',
            'national_id',
            'phone_number',
            'signed',
            'year',
            'season_type'

        )->get();


        $data->transform(function ($row) {
            $row->date = Carbon::parse($row->date)->format('d-m-Y');
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
