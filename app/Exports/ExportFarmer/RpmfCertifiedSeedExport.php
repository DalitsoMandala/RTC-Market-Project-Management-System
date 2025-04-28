<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerCertifiedSeed;
use App\Traits\ExportStylingTrait;
use App\Traits\FormEssentials;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmfCertifiedSeedExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    use FormEssentials;
    public $template;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Farmers Form']['Certified Seed'];
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns 'variety', 'area', and 'rpmf_id' (Farmer ID)
        return RpmFarmerCertifiedSeed::select('variety', 'area', 'rpmf_id')->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return [
            array_keys($this->validationTypes),
            array_values($this->validationTypes)
        ];
    }


    public function title(): string
    {
        return 'Certified Seed';
    }
}
