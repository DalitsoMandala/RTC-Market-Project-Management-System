<?php

namespace App\Exports;

use App\Exports\AttendanceImport\AttendanceRegistersExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Models\SeedBeneficiary;

class SeedBeneficiariesExport implements WithMultipleSheets
{
    public $template = false;
    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Potato' => new CropSheetExport('Potato', $this->template),
            'OFSP' => new CropSheetExportOFSP('OFSP', $this->template),
            //       'Trainings' => new AttendanceTrainingExport($this->template, 'Trainings'),
            // 'Mother Plot Hosts' => new MotherPlotsExport($this->template),
            // 'Cassava Tots' => new CassavaTotExport($this->template),
            'Cassava' => new CropSheetExportCassava('Cassava', $this->template),
        ];
    }
}
