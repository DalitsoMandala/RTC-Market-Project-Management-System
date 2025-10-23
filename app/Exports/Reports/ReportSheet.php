<?php

namespace App\Exports\Reports;

use App\Models\Crop;
use App\Models\Organisation;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ReportSheet implements WithMultipleSheets
{
    public array $sheets;

    public function __construct()
    {

        Auth::check() == false ? abort(403) : '';


    }
    public function sheets(): array
    {
     return [
        'Consolidated' => new ReportExport('Consolidated'),
     ];
    }
}
