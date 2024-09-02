<?php

namespace App\Livewire\Tables\RtcMarket;

use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RtcProductionFarmersFollowU extends PowerGridComponent
{
    use WithExport;
    public bool $deferLoading = false;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data-farmers-follow-up'),
            Footer::make()
                ->showPerPage()
                ->pageName('farmers-follow-up')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return RpmFarmerFollowUp::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => str_pad($model->rpm_farmer_id, 5, '0', STR_PAD_LEFT))
            ->add('rpm_farmer_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_farmer_id;
                $row = RtcProductionFarmer::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })

            ->add('date_of_follow_up')
            ->add('date_of_follow_up_formatted', fn($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Yes' : 'No';
            })


            ->add('area_under_cultivation_total', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->total ?? null;
            })

            ->add('number_of_plantlets_produced_potato', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->potato ?? 0;
            })
            ->add('number_of_plantlets_produced_cassava', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->cassava ?? 0;
            })
            ->add('number_of_plantlets_produced_sw_potato', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->sweet_potato ?? 0;
            })
            ->add('number_of_screen_house_vines_harvested', fn($model) => $model->number_of_screen_house_vines_harvested ?? 0)
            ->add('number_of_screen_house_min_tubers_harvested', fn($model) => $model->number_of_screen_house_min_tubers_harvested ?? 0)
            ->add('number_of_sah_plants_produced', fn($model) => $model->number_of_sah_plants_produced ?? 0)
            ->add('basic_seed_multiplication_total', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication, true);

                if ($basic_seed_multiplication) {
                    return collect($basic_seed_multiplication)->sum(function ($item) {
                        return (float) $item['area']; // Cast to integer to ensure proper summation
                    });
                }

                return 0; // or return 0 if you prefer a zero total instead of null
            })

            ->add('is_registered_seed_producer', fn($model) => $model->is_registered_seed_producer == 1 ? 'Yes' : 'No')

            ->add('uses_certified_seed', fn($model) => $model->uses_certified_seed == 1 ? 'Yes' : 'No')

            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')

            ->add('total_vol_production_previous_season', function ($model) {
                $total_production_volume_previous_season = json_decode($model->total_vol_production_previous_season);
                return $total_production_volume_previous_season ?? 0;
            })
            ->add('total_production_value_previous_season_total', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->value ?? 0;
            })

            ->add('total_production_value_previous_season_usd', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->total ?? 0;
            })

            ->add('total_production_value_previous_season_date', function ($model) {
                $total_production_value_previous_season_date = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season_date->date_of_maximum_sales === null ? null : Carbon::parse($total_production_value_previous_season_date->date_of_maximum_sales)->format('d/m/Y');
            })
            ->add('usd_rate', function ($model) {
                $usd_rate = json_decode($model->total_production_value_previous_season);
                return $usd_rate->rate ?? 0;
            })

            ->add('total_vol_irrigation_production_previous_season', function ($model) {
                $total_vol_irrigation_production_previous_season = json_decode($model->total_vol_irrigation_production_previous_season);
                return $total_vol_irrigation_production_previous_season ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_total', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->value ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_usd', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->total ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_date', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->date_of_maximum_sales === null ? null : Carbon::parse($total_irrigation_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y');
            })

            ->add('usd_rate_irrigation', function ($model) {
                $usd_rate = json_decode($model->total_irrigation_production_value_previous_season);
                return $usd_rate->rate ?? 0;
            })
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? 'Yes' : 'No')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? 'Yes' : 'No')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? 'Yes' : 'No')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('sells_to_aggregation_centers', function ($model) {
                return $model->sells_to_aggregation_centers == 1 ? 'Yes' : 'No';
            })

        ;
    }

    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }
    #[On('export-followup')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/rtc_production_and_marketing_farmers_followup.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Farmer ID',
                'Actor Name',

                'Enterprise',
                'District',
                'EPA',
                'Section',
                'Group Name',
                'Date of Follow Up',
                'Date of Follow Up (Formatted)',
                'Area Under Cultivation',
                'Area Under Cultivation Variety 1',
                'Area Under Cultivation Variety 2',
                'Area Under Cultivation Variety 3',
                'Area Under Cultivation Variety 4',
                'Area Under Cultivation Variety 5',

                'Number of Plantlets Produced Potato',
                'Number of Plantlets Produced Cassava',
                'Number of Plantlets Produced Sweet Potato',
                'Number of Screen House Vines Harvested',
                'Number of Screen House Min Tubers Harvested',
                'Number of SAH Plants Produced',

                'Basic Seed Multiplication Total',
                'Basic Seed Multiplication Variety 1',
                'Basic Seed Multiplication Variety 2',
                'Basic Seed Multiplication Variety 3',
                'Basic Seed Multiplication Variety 4',
                'Basic Seed Multiplication Variety 5',
                'Basic Seed Multiplication Variety 6',
                'Basic Seed Multiplication Variety 7',

                'Certified Seed Multiplication Total',
                'Certified Seed Multiplication Variety 1',
                'Certified Seed Multiplication Variety 2',
                'Certified Seed Multiplication Variety 3',
                'Certified Seed Multiplication Variety 4',
                'Certified Seed Multiplication Variety 5',
                'Certified Seed Multiplication Variety 6',
                'Certified Seed Multiplication Variety 7',
                'Is Registered Seed Producer',
                'Seed Service Unit Registration Details Date',
                'Seed Service Unit Registration Details Number',
                'Service Unit Date',
                'Service Unit Number',
                'Uses Certified Seed'
            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $location = json_decode($item->location_data);
                $area_under_cultivation = json_decode($item->area_under_cultivation);
                $number_of_plantlets_produced = json_decode($item->number_of_plantlets_produced);
                $basic_seed_multiplication = json_decode($item->area_under_basic_seed_multiplication);
                $certified_seed_multiplication = json_decode($item->area_under_certified_seed_multiplication);
                $seed_service_unit_registration_details = json_decode($item->seed_service_unit_registration_details);

                $row = [
                    'id' => $item->id,
                    'rpm_farmer_id' => $item->rpm_farmer_id,
                    'actor_name' => RtcProductionFarmer::find($item->rpm_farmer_id)->name_of_actor ?? null,
                    // 'location_data' => $item->location_data,
                    'enterprise' => $location->enterprise ?? null,
                    'district' => $location->district ?? null,
                    'epa' => $location->epa ?? null,
                    'section' => $location->section ?? null,
                    'group_name' => $location->group_name ?? null,
                    'date_of_follow_up' => $item->date_of_follow_up,
                    'date_of_follow_up_formatted' => Carbon::parse($item->date_of_follow_up)->format('d/m/Y'),
                    'area_under_cultivation' => $area_under_cultivation->total ?? 0,
                    'area_under_cultivation_variety_1' => $area_under_cultivation->variety_1 ?? null,
                    'area_under_cultivation_variety_2' => $area_under_cultivation->variety_2 ?? null,
                    'area_under_cultivation_variety_3' => $area_under_cultivation->variety_3 ?? null,
                    'area_under_cultivation_variety_4' => $area_under_cultivation->variety_4 ?? null,
                    'area_under_cultivation_variety_5' => $area_under_cultivation->variety_5 ?? null,
                    //  'number_of_plantlets_produced' => $item->number_of_plantlets_produced,
                    'number_of_plantlets_produced_potato' => $number_of_plantlets_produced->potato ?? null,
                    'number_of_plantlets_produced_cassava' => $number_of_plantlets_produced->cassava ?? null,
                    'number_of_plantlets_produced_sw_potato' => $number_of_plantlets_produced->sweet_potato ?? null,
                    'number_of_screen_house_vines_harvested' => $item->number_of_screen_house_vines_harvested ?? null,
                    'number_of_screen_house_min_tubers_harvested' => $item->number_of_screen_house_min_tubers_harvested ?? null,
                    'number_of_sah_plants_produced' => $item->number_of_sah_plants_produced ?? null,
                    //   'area_under_basic_seed_multiplication' => $item->area_under_basic_seed_multiplication,
                    'basic_seed_multiplication_total' => $basic_seed_multiplication->total ?? null,
                    'basic_seed_multiplication_variety_1' => $basic_seed_multiplication->variety_1 ?? null,
                    'basic_seed_multiplication_variety_2' => $basic_seed_multiplication->variety_2 ?? null,
                    'basic_seed_multiplication_variety_3' => $basic_seed_multiplication->variety_3 ?? null,
                    'basic_seed_multiplication_variety_4' => $basic_seed_multiplication->variety_4 ?? null,
                    'basic_seed_multiplication_variety_5' => $basic_seed_multiplication->variety_5 ?? null,
                    'basic_seed_multiplication_variety_6' => $basic_seed_multiplication->variety_6 ?? null,
                    'basic_seed_multiplication_variety_7' => $basic_seed_multiplication->variety_7 ?? null,
                    //    'area_under_certified_seed_multiplication' => $item->area_under_certified_seed_multiplication,
                    'certified_seed_multiplication_total' => $certified_seed_multiplication->total ?? null,
                    'certified_seed_multiplication_variety_1' => $certified_seed_multiplication->variety_1 ?? null,
                    'certified_seed_multiplication_variety_2' => $certified_seed_multiplication->variety_2 ?? null,
                    'certified_seed_multiplication_variety_3' => $certified_seed_multiplication->variety_3 ?? null,
                    'certified_seed_multiplication_variety_4' => $certified_seed_multiplication->variety_4 ?? null,
                    'certified_seed_multiplication_variety_5' => $certified_seed_multiplication->variety_5 ?? null,
                    'certified_seed_multiplication_variety_6' => $certified_seed_multiplication->variety_6 ?? null,
                    'certified_seed_multiplication_variety_7' => $certified_seed_multiplication->variety_7 ?? null,
                    'is_registered_seed_producer' => $item->is_registered_seed_producer == 1 ? 'Yes' : 'No',
                    'seed_service_unit_registration_details_date' => $seed_service_unit_registration_details->registration_date === null ? null : Carbon::parse($seed_service_unit_registration_details->registration_date)->format('d/m/Y'),
                    'seed_service_unit_registration_details_number' => $seed_service_unit_registration_details->registration_number ?? null,
                    'service_unit_date' => Carbon::parse($item->service_unit_date)->format('d/m/Y'),
                    'service_unit_number' => $item->service_unit_number,
                    'uses_certified_seed' => $item->uses_certified_seed == 1 ? 'Yes' : 'No',
                ];

                $writer->addRow($row);
            }
        }

        // Close the writer and get the path of the file
        $writer->close();

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }
    public function columns(): array
    {
        return [
            Column::make('Farmer id', 'unique_id')->sortable(),
            Column::make('Actor Name', 'actor_name'),

            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),

            Column::make('Is registered', 'is_registered')
                ->sortable()
                ->searchable(),


            Column::make('Has RTC Contractual Agreement', 'has_rtc_market_contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),



            Column::make('Number of plantlets produced/cassava', 'number_of_plantlets_produced_cassava', 'number_of_plantlets_produced->cassava')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/potato', 'number_of_plantlets_produced_potato', 'number_of_plantlets_produced->potato')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/sweet potato', 'number_of_plantlets_produced_sw_potato', 'number_of_plantlets_produced->sweet_potato')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house vines harvested', 'number_of_screen_house_vines_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house min tubers harvested', 'number_of_screen_house_min_tubers_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of sah plants produced', 'number_of_sah_plants_produced')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/total', 'basic_seed_multiplication_total', 'basic_seed_multiplication->total')
                ->sortable()
                ->searchable(),




            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),


            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),


            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),

            Column::make('Total production volume previous season', 'total_vol_production_previous_season', 'total_vol_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total', 'total_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total production value previous season/total ($)', 'total_production_value_previous_season_usd', 'total_production_value_previous_season->total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date', 'total_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),

            Column::make('USD Rate of Production Value', 'usd_rate', 'total_production_value_previous_season->rate')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total', 'total_irrigation_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total irrigation production value previous season/total ($)', 'total_irrigation_production_value_previous_season_usd', 'total_irrigation_production_value_previous_season->total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date', 'total_irrigation_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),


            Column::make('USD Rate of irrigation Production Value', 'usd_rate_irrigation', 'total_irrigation_production_value_previous_season->rate')
                ->sortable()
                ->searchable(),

            Column::make('Sells to domestic markets', 'sells_to_domestic_markets')
                ->sortable()
                ->searchable(),

            Column::make('Sells to international markets', 'sells_to_international_markets')
                ->sortable()
                ->searchable(),

            Column::make('Uses market information systems', 'uses_market_information_systems')
                ->sortable()
                ->searchable(),


            Column::make('Sells to aggregation centers', 'sells_to_aggregation_centers')
                ->sortable()
                ->searchable(),



        ];
    }

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_follow_up', ),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
