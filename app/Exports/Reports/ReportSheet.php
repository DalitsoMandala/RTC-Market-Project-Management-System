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

        $crops = Crop::where('name', '!=', 'Cassava')->pluck('name')->toArray();

        $organisations = Organisation::get()->transform(function ($org) {
            if ($org->name == 'CIP') {
                $org->name = 'Sweet potato + Potato';
            }
            return $org;
        })->pluck('name')->toArray();
        $this->sheets = [...$crops, ...$organisations];
        if (auth()->user()->hasAnyRole('external')) {
            $filterByOrganisation = collect(
                $this->sheets
            )->filter(function ($sheet) {

                return $sheet == Auth::user()->organisation->name;
            })->toArray();
            $this->sheets = $filterByOrganisation;
        }
    }
    public function sheets(): array
    {
       $sheets = [];

       foreach ($this->sheets as $sheet) {

        if($sheet == 'Cassava' || $sheet == 'Sweet potato' || $sheet == 'Potato' || $sheet == 'Sweet potato + Potato' )
        {
            $sheets[] = new ReportExport($sheet,'crop',[]);
        }else{
            $sheets[] = new ReportExport($sheet,'organisation',[]);
        }

       }


        return $sheets;
    }
}
