<?php

namespace App\Exports\ExportProcessor;

use App\Models\RpmProcessorConcAgreement;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmProcessorConcAgreementsExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStrictNullComparison
{
    protected $rowNumber = 0;

    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        return RpmProcessorConcAgreement::all();
    }

    public function headings(): array
    {
        return [

            'Processor ID',
            'Date Recorded',
            'Partner Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ];
    }

    public function map($row): array
    {

        return [

            $row->rpm_processor_id,
            $row->date_recorded,
            $row->partner_name,
            $row->country,
            $row->date_of_maximum_sale,
            $row->product_type,
            $row->volume_sold_previous_period,
            $row->financial_value_of_sales
        ];
    }

    public function title(): string
    {
        return 'Contractual Agreements';
    }
}
