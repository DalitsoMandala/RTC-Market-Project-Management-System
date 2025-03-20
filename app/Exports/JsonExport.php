<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class JsonExport implements FromCollection,  WithTitle,  ShouldAutoSize
{
    /**
     * @return \Illuminate\Support\Collection
     */
    protected $data;
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function collection()
    {
        //

        return collect([]);
    }


    public function title(): string
    {
        return 'Report Export';
    }
}
