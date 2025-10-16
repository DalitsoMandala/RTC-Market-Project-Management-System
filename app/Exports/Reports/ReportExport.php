<?php

namespace App\Exports\Reports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ReportExport extends ReportExportTemplate  implements FromCollection, WithTitle, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */

    public $sheetName;
    public $type;
    public array $data;

    public function __construct($sheetName, $type = 'crop', array $data = [])
    {

        $this->sheetName = $sheetName;
        $this->type = $type;
        $this->data = $data;
    }
    public function collection()
    {
        //
        return collect([]);
    }

    public function title(): string
    {
        return  $this->sheetName;
    }
}
