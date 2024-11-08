<?php

namespace App\Exports\HouseholdExport;

use App\Exports\HouseholdExport\MainFoodSheetExport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\HouseholdExport\HouseholdSheetExport;

class HouseholdRtcConsumptionTemplateExport implements WithMultipleSheets
{

    public $template = false;

    public function __construct($template = false)
    {
        $this->template = $template;
    }
    public function sheets(): array
    {
        return [
            new HouseholdSheetExport($this->template), // Sheet for household data
            new MainFoodSheetExport($this->template),  // Sheet for main food data
        ];
    }
}
