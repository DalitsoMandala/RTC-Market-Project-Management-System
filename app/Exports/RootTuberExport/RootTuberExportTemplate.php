<?php

namespace App\Exports\RootTuberExport;



use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RootTuberExportTemplate implements WithMultipleSheets, WithStrictNullComparison
{
  public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Raw Tuber Export' => new RawExportSheet($this->template),
            'Processed Tuber Export' => new ProcessedExportSheet($this->template),

        ];
    }
}
