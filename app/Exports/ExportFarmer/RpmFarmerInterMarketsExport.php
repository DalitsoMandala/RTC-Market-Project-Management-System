<?php

namespace App\Exports\ExportFarmer;

use Carbon\Carbon;
use App\Models\RpmFarmerInterMarket;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RpmFarmerInterMarketsExport implements FromCollection, WithHeadings, WithTitle, WithStrictNullComparison
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
        $data = RpmFarmerInterMarket::select(
            'rpm_farmer_id',
            'date_recorded',
            'crop_type',
            'market_name',
            'country',
            'date_of_maximum_sale',
            'product_type',
            'volume_sold_previous_period',
            'financial_value_of_sales'
        )->get();

        $data->transform(function ($row) {
            $row->date_recorded = Carbon::parse($row['date_recorded'])->format('d-m-Y');
            $row->date_of_maximum_sale = Carbon::parse($row['date_of_maximum_sale'])->format('d-m-Y');
            return $row;
        });
        return $data;
    }

    public function headings(): array
    {
        // Only include the specified columns in the headings
        return [
            'Farmer ID',
            'Date Recorded',
            'Crop Type',
            'Market Name',
            'Country',
            'Date of Maximum Sale',
            'Product Type',
            'Volume Sold Previous Period',
            'Financial Value of Sales'
        ];
    }

    public function title(): string
    {
        return 'International Markets';
    }
}
