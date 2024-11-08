<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\SeedBeneficiary;

class SeedBeneficiariesExport implements WithMultipleSheets
{
    public $template = false;
    public function __construct($template)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Potato' => new CropSheetExport('Potato'),
            'OFSP' => new CropSheetExport('OFSP'),
            'Cassava' => new CropSheetExport('Cassava'),
        ];
    }
}
