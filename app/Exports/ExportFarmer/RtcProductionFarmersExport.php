<?php

namespace App\Exports\ExportFarmer;

use App\Models\RtcProductionFarmer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class RtcProductionFarmersExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStrictNullComparison
{
    protected $rowNumber = 0; // Start counting from 1
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

        // Select only the columns to be included in the export
        return RtcProductionFarmer::select(
            'epa',
            'section',
            'district',
            'enterprise',
            'date_of_recruitment',
            'name_of_actor',
            'name_of_representative',
            'phone_number',
            'type',
            'approach',
            'sector',
            'mem_female_18_35',
            'mem_male_18_35',
            'mem_male_35_plus',
            'mem_female_35_plus',
            'group',
            'establishment_status',
            'is_registered',
            'registration_body',
            'registration_number',
            'registration_date',
            'emp_formal_female_18_35',
            'emp_formal_male_18_35',
            'emp_formal_male_35_plus',
            'emp_formal_female_35_plus',
            'emp_informal_female_18_35',
            'emp_informal_male_18_35',
            'emp_informal_male_35_plus',
            'emp_informal_female_35_plus',
            'number_of_plantlets_produced_cassava',
            'number_of_plantlets_produced_potato',
            'number_of_plantlets_produced_sweet_potato',
            'number_of_screen_house_vines_harvested',
            'number_of_screen_house_min_tubers_harvested',
            'number_of_sah_plants_produced',
            'is_registered_seed_producer',
            'registration_number_seed_producer',
            'registration_date_seed_producer',
            'uses_certified_seed',
            'market_segment_fresh',
            'market_segment_processed',
            'has_rtc_market_contract',
            'total_vol_production_previous_season',
            'prod_value_previous_season_total',
            'prod_value_previous_season_date_of_max_sales',
            'prod_value_previous_season_usd_rate',
            'prod_value_previous_season_usd_value',
            'total_vol_irrigation_production_previous_season',
            'irr_prod_value_previous_season_total',
            'irr_prod_value_previous_season_date_of_max_sales',
            'irr_prod_value_previous_season_usd_rate',
            'irr_prod_value_previous_season_usd_value',
            'sells_to_domestic_markets',
            'sells_to_international_markets',
            'uses_market_information_systems',
            'sells_to_aggregation_centers',
            'total_vol_aggregation_center_sales'
        )->get();
    }

    public function headings(): array
    {
        return [
            'ID', // Add ID as the first column heading
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
            'Number of Plantlets Produced Cassava',
            'Number of Plantlets Produced Potato',
            'Number of Plantlets Produced Sweet Potato',
            'Screen House Vines Harvested',
            'Screen House Min Tubers Harvested',
            'SAH Plants Produced',
            'Is Registered Seed Producer',
            'Seed Producer Registration Number',
            'Seed Producer Registration Date',
            'Uses Certified Seed',
            'Market Segment Fresh',
            'Market Segment Processed',
            'Has RTC Market Contract',
            'Total Volume Production Previous Season',
            'Production Value Previous Season Total',
            'Production Value Date of Max Sales',
            'Production Value USD Rate',
            'Production Value USD Value',
            'Total Volume Irrigation Production Previous Season',
            'Irrigation Production Value Total',
            'Irrigation Production Date of Max Sales',
            'Irrigation Production USD Rate',
            'Irrigation Production USD Value',
            'Sells to Domestic Markets',
            'Sells to International Markets',
            'Uses Market Information Systems',
            'Sells to Aggregation Centers',
            'Total Volume Aggregation Center Sales'
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++; // Increment ID for each row

        return array_merge(
            [$this->rowNumber], // Add ID column value
            array_values($row->toArray()) // Map remaining row values
        );
    }

    public function title(): string
    {
        return 'Production Farmers';
    }
}
