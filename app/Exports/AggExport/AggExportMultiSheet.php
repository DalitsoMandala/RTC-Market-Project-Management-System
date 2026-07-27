<?php
namespace App\Exports\AggExport;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AggExportMultiSheet implements WithMultipleSheets
{

    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Aggregated Report' => new AggExportSheet($this->template), // Import only "Sheet1"
        ];
    }
}
