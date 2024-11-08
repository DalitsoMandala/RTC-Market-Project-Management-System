<?php

namespace App\Exports\ExportFarmer;

use App\Models\RpmFarmerConcAgreement;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmFarmerConcAgreementsExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
{
    public $template;

    public function __construct($template)
    {
        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);
        }
        // Select only the columns we want to include, excluding 'ID', 'Status', 'Created At', and 'Updated At'
        return RpmFarmerConcAgreement::select(
            'rpm_farmer_id',
            'date_recorded',
            'partner_name',
            'country',
            'date_of_maximum_sale',
            'product_type',
            'volume_sold_previous_period',
            'financial_value_of_sales'
        )->get();
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return [
            'Farmer ID',
            'Date Recorded',
            'Partner Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ];
    }

    public function title(): string
    {
        return 'Contractual Agreements';
    }
}
