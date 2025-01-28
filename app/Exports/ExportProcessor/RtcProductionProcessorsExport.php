<?php

namespace App\Exports\ExportProcessor;

use Carbon\Carbon;
use App\Models\RtcProductionProcessor;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionProcessorsExport implements FromCollection, WithHeadings, WithTitle, WithMapping, WithStrictNullComparison
{
    protected $rowNumber = 0;

    public $template;

    public function __construct($template)
    {

        $this->template = $template;
    }
    public function collection()
    {
        if ($this->template) {
            return collect([]);  // Return an empty collection if the template is not provided.
        }
        $data = RtcProductionProcessor::get();
        $data->transform(function ($row) {
            $row->date_of_recruitment = Carbon::parse($row['date_of_recruitment'])->format('d-m-Y');
            $row->registration_date = Carbon::parse($row['registration_date'])->format('d-m-Y');
            $row->prod_value_previous_season_date_of_max_sales = Carbon::parse($row['prod_value_previous_season_date_of_max_sales'])->format('d-m-Y');
            return $row;
        });

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'EPA',
            'Section',
            'District',
            'Enterprise',
            'Date of Recruitment',
            'Name of Actor',
            'Name of Representative',
            'Phone Number',
            'Type',
            'Approach',
            'Sector',
            'Members Female 18-35',
            'Members Male 18-35',
            'Members Male 35+',
            'Members Female 35+',
            'Group',
            'Establishment Status',
            'Is Registered',
            'Registration Body',
            'Registration Number',
            'Registration Date',
            'Employees Formal Female 18-35',
            'Employees Formal Male 18-35',
            'Employees Formal Male 35+',
            'Employees Formal Female 35+',
            'Employees Informal Female 18-35',
            'Employees Informal Male 18-35',
            'Employees Informal Male 35+',
            'Employees Informal Female 35+',
            'Market Segment Fresh',
            'Market Segment Processed',
            'Has RTC Market Contract',
            'Total Volume Production Previous Season',
            'Production Value Previous Season Total',
            'Production Value Date of Max Sales',
            'USD Rate',
            'USD Value',
            'Sells to Domestic Markets',
            'Sells to International Markets',
            'Uses Market Info Systems',
            'Sells to Aggregation Centers',
            'Total Volume Aggregation Center Sales'
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $row->epa,
            $row->section,
            $row->district,
            $row->enterprise,
            $row->date_of_recruitment,
            $row->name_of_actor,
            $row->name_of_representative,
            $row->phone_number,
            $row->type,
            $row->approach,
            $row->sector,
            $row->mem_female_18_35,
            $row->mem_male_18_35,
            $row->mem_male_35_plus,
            $row->mem_female_35_plus,
            $row->group,
            $row->establishment_status,
            $row->is_registered,
            $row->registration_body,
            $row->registration_number,
            $row->registration_date,
            $row->emp_formal_female_18_35,
            $row->emp_formal_male_18_35,
            $row->emp_formal_male_35_plus,
            $row->emp_formal_female_35_plus,
            $row->emp_informal_female_18_35,
            $row->emp_informal_male_18_35,
            $row->emp_informal_male_35_plus,
            $row->emp_informal_female_35_plus,
            $row->market_segment_fresh,
            $row->market_segment_processed,
            $row->has_rtc_market_contract,
            $row->total_vol_production_previous_season,
            $row->prod_value_previous_season_total,
            $row->prod_value_previous_season_date_of_max_sales,
            $row->prod_value_previous_season_usd_rate,
            $row->prod_value_previous_season_usd_value,
            $row->sells_to_domestic_markets,
            $row->sells_to_international_markets,
            $row->uses_market_information_systems,
            $row->sells_to_aggregation_centers,
            $row->total_vol_aggregation_center_sales,
        ];
    }

    public function title(): string
    {
        return 'Production Processors';
    }
}
