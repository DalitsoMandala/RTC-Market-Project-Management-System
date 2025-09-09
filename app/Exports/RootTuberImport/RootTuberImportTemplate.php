<?php

namespace App\Exports\RootTuberImport;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RootTuberImportTemplate implements WithMultipleSheets, WithStrictNullComparison
{
     public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            'Raw Tuber Import' => new RawImportSheet($this->template),
            'Processed Tuber Import' => new ProcessedImportSheet($this->template),

        ];
    }
}
