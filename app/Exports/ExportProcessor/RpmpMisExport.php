<?php

namespace App\Exports\ExportProcessor;

use App\Models\RpmpMis;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Livewire\tables\RtcMarket\RpmProcessorMIS;
use App\Models\RpmProcessorMarketInformationSystem;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmpMisExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        return RpmProcessorMarketInformationSystem::select(
            'name',
            'rpmp_id'
        )->get();
    }

    public function headings(): array
    {
        return [
            'MIS Name',
            'Processor ID'
        ];
    }

    public function title(): string
    {
        return 'Market Information Systems';
    }
}
