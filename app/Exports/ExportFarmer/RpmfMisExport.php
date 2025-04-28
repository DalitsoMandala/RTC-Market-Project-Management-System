<?php

namespace App\Exports\ExportFarmer;

use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\RpmFarmerMarketInformationSystem;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfMisExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    use FormEssentials;
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Farmers Form']['Market Information Systems'];
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns we want to include, excluding 'ID'
        return RpmFarmerMarketInformationSystem::select('name', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Exclude 'ID' from the headings
        return [
            array_keys($this->validationTypes),
            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return 'Market Information Systems';
    }
}
