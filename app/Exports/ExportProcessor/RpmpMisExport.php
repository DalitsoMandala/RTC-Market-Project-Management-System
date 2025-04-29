<?php

namespace App\Exports\ExportProcessor;

use App\Models\RpmpMis;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use App\Livewire\tables\RtcMarket\RpmProcessorMIS;
use App\Models\RpmProcessorMarketInformationSystem;
use App\Traits\ExportStylingTrait;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmpMisExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison, WithEvents, ShouldAutoSize
{
    use ExportStylingTrait;
    public $template;
    use \App\Traits\FormEssentials;
    protected $validationTypes = [];

    public function __construct($template)
    {
        $this->template = $template;
        $this->validationTypes = $this->forms['Rtc Production Processors Form']['Market Information Systems'];
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);  // Return an empty collection if the template is not provided.
        }
        return RpmProcessorMarketInformationSystem::select(
            'name',
            'rpmp_id'
        )->get();
    }

    public function headings(): array
    {
        return [
            [
                'MIS Name',
                'Processor ID'
            ],
            array_values($this->validationTypes)
        ];
    }

    public function title(): string
    {
        return 'Market Information Systems';
    }
}
