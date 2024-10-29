<?php

namespace App\Exports\HouseholdExport;

use App\Exports\HouseholdExport\MainFoodSheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\HouseholdExport\HouseholdSheetExport;

class HouseholdRtcConsumptionTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new HouseholdSheetExport(), // Sheet for household data
            new MainFoodSheetExport(),  // Sheet for main food data
        ];
    }
}
