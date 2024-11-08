<?php

namespace App\Exports\HouseholdExport;

use App\Models\MainFoodHrc;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class MainFoodSheetExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function headings(): array
    {
        return [
            'Household ID',
            'Main Food Name',
        ];
    }

    public function collection(): Collection
    {
        if ($this->template) {
            return collect([]);
        }
        return MainFoodHrc::select([
            'hrc_id',
            'name',
        ])->get();
    }

    public function title(): string
    {
        return 'Main Food Data';
    }
}
