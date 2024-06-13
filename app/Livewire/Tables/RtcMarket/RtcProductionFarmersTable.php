<?php

namespace App\Livewire\Tables\RtcMarket;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class RtcProductionFarmersTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //  $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('rtc_production_farmers');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('sector')
            ->add('number_of_members')

            ->add('female_18', function ($model) {

                return json_decode($model->number_of_members)->female_18_35;
            })
            ->add('number_of_members_total', function ($model) {

                return json_decode($model->number_of_members)->total;
            })

            ->add('number_of_members_male_18_35', function ($model) {

                return json_decode($model->number_of_members)->male_18_35;
            })

            ->add('number_of_members_male_35_plus', function ($model) {

                return json_decode($model->number_of_members)->male_35_plus;
            })
            ->add('number_of_members_female_35_plus', function ($model) {

                return json_decode($model->number_of_members)->female_35_plus;
            })
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered')
            ->add('registration_details')

            ->add('registration_details_body', fn($model) => json_decode($model->registration_details)->registration_body)
            ->add('registration_details_date', fn($model) => json_decode($model->registration_details)->registration_date)
            ->add('registration_details_number', fn($model) => json_decode($model->registration_details)->registration_number)
            ->add('number_of_employees')
            ->add('area_under_cultivation')

            ->add('area_under_cultivation_total', fn($model) => json_decode($model->area_under_cultivation)->total)
            ->add('area_under_cultivation_variety_1', fn($model) => json_decode($model->area_under_cultivation)->variety_1)
            ->add('area_under_cultivation_variety_2', fn($model) => json_decode($model->area_under_cultivation)->variety_2)
            ->add('area_under_cultivation_variety_3', fn($model) => json_decode($model->area_under_cultivation)->variety_3)
            ->add('area_under_cultivation_variety_4', fn($model) => json_decode($model->area_under_cultivation)->variety_4)
            ->add('area_under_cultivation_variety_5', fn($model) => json_decode($model->area_under_cultivation)->variety_5)
            ->add('number_of_plantlets_produced')
            ->add('number_of_plantlets_produced_potato', fn($model) => json_decode($model->number_of_plantlets_produced)->potato)
            ->add('number_of_plantlets_produced_cassava', fn($model) => json_decode($model->number_of_plantlets_produced)->cassava)
            ->add('number_of_plantlets_produced_sw_potato', fn($model) => json_decode($model->number_of_plantlets_produced)->sweet_potato)

            ->add('number_of_screen_house_vines_harvested')
            ->add('number_of_screen_house_min_tubers_harvested')
            ->add('number_of_sah_plants_produced')
            ->add('area_under_basic_seed_multiplication')
            ->add('basic_seed_multiplication_total', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->total)
            ->add('basic_seed_multiplication_variety_1', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_1)
            ->add('basic_seed_multiplication_variety_2', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_2)
            ->add('basic_seed_multiplication_variety_3', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_3)
            ->add('basic_seed_multiplication_variety_4', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_4)
            ->add('basic_seed_multiplication_variety_5', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_5)
            ->add('basic_seed_multiplication_variety_6', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_6)
            ->add('basic_seed_multiplication_variety_7', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_7)
            ->add('area_under_certified_seed_multiplication')
            ->add('area_under_certified_seed_multiplication_total', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->total)
            ->add('area_under_certified_seed_multiplication_variety_1', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_1)
            ->add('area_under_certified_seed_multiplication_variety_2', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_2)
            ->add('area_under_certified_seed_multiplication_variety_3', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_3)
            ->add('area_under_certified_seed_multiplication_variety_4', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_4)
            ->add('area_under_certified_seed_multiplication_variety_5', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_5)
            ->add('area_under_certified_seed_multiplication_variety_6', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_6)
            ->add('area_under_certified_seed_multiplication_variety_7', fn($model) => json_decode($model->area_under_basic_seed_multiplication)->variety_7)
            ->add('is_registered_seed_producer')
            ->add('seed_service_unit_registration_details')
            ->add('service_unit_date')
            ->add('service_unit_number')
            ->add('uses_certified_seed')
            ->add('market_segment')
            ->add('has_rtc_market_contract')
            ->add('total_production_previous_season')
            ->add('total_production_value_previous_season')
            ->add('total_irrigation_production_previous_season')
            ->add('total_irrigation_production_value_previous_season')
            ->add('sells_to_domestic_markets')
            ->add('sells_to_international_markets')
            ->add('uses_market_information_systems')
            ->add('market_information_systems')
            ->add('aggregation_centers')
            ->add('aggregation_center_sales')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),

            Column::make('Name of representative', 'name_of_representative')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable(),

            Column::make('Approach', 'approach')
                ->sortable()
                ->searchable(),

            Column::make('Sector', 'sector')
                ->sortable()
                ->searchable(),

            Column::make('Number of members/total', 'number_of_members_total')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 18-35', 'number_of_members_male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 18-35', 'female_18')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 35+', 'number_of_members_male_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 35+', 'number_of_members_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Group', 'group')
                ->sortable()
                ->searchable(),

            Column::make('Establishment status', 'establishment_status')
                ->sortable()
                ->searchable(),

            Column::make('Is registered', 'is_registered')
                ->sortable()
                ->searchable(),

            Column::make('Registration details/Body', 'registration_details_body')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/date', 'registration_details_date')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/number', 'registration_details_number')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation', 'area_under_cultivation')
                ->sortable()
                ->searchable(),

            Column::make('Number of plantlets produced', 'number_of_plantlets_produced')
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

            Column::make('Area under basic seed multiplication', 'area_under_basic_seed_multiplication')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication', 'area_under_certified_seed_multiplication')
                ->sortable()
                ->searchable(),

            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details', 'seed_service_unit_registration_details')
                ->sortable()
                ->searchable(),

            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),

            Column::make('Market segment', 'market_segment')
                ->sortable()
                ->searchable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),

            Column::make('Total production previous season', 'total_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season', 'total_production_value_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production previous season', 'total_irrigation_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production value previous season', 'total_irrigation_production_value_previous_season')
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

            Column::make('Market information systems', 'market_information_systems')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation centers', 'aggregation_centers')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation center sales', 'aggregation_center_sales')
                ->sortable()
                ->searchable(),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('date_of_recruitment'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),
        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }
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