<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Query\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final class RtcProductionFarmersTable extends PowerGridComponent
{
    use WithExport;
    public $routePrefix;
    public bool $deferLoading = false;
    public function setUp(): array
    {
        //  $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()
                // ->showSearchInput()
                ->includeViewOnTop('components.export-data-farmers')
            ,
            Footer::make()
                ->showPerPage()
                ->pageName('farmers')
                ->showRecordCount(),
        ];
    }

    public function datasource(): EloquentBuilder
    {

        return RtcProductionFarmer::query();


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
            ->add('enterprise', function ($model) {
                $data = json_decode($model->location_data);
                return $data->enterprise ?? null;
            })
            ->add('district', function ($model) {
                $data = json_decode($model->location_data);
                return $data->district ?? null;
            })
            ->add('epa', function ($model) {
                $data = json_decode($model->location_data);
                return $data->epa ?? null;
            })
            ->add('section', function ($model) {
                $data = json_decode($model->location_data);
                return $data->section ?? null;
            })
            ->add('sector')
            ->add('number_of_members_total', function ($model) {
                $number_of_members = json_decode($model->number_of_members);

                if (is_null($number_of_members)) {
                    return 0; // or any default value you consider appropriate
                }

                return ($number_of_members->female_18_35 ?? 0) +
                    ($number_of_members->male_18_35 ?? 0) +
                    ($number_of_members->male_35_plus ?? 0) +
                    ($number_of_members->female_35_plus ?? 0);
            })
            ->add('number_of_members_female_18_35', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->female_18_35 ?? 0;
            })
            ->add('number_of_members_male_18_35', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->male_18_35 ?? 0;
            })
            ->add('number_of_members_male_35_plus', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->male_35_plus ?? 0;
            })
            ->add('number_of_members_female_35_plus', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->female_35_plus ?? 0;
            })
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Yes' : 'No';
            })
            ->add('registration_details')
            ->add('registration_details_body', fn($model) => json_decode($model->registration_details) == null ? null : json_decode($model->registration_details)->registration_body)
            ->add('registration_details_date', function ($model) {
                $registration_details = json_decode($model->registration_details);

                if (is_null($registration_details)) {
                    return null;
                }

                return Carbon::parse(json_decode($model->registration_details)->registration_date)->format('d/m/Y');
            })
            ->add('registration_details_number', fn($model) => json_decode($model->registration_details) == null ? null : json_decode($model->registration_details)->registration_number)
            ->add('number_of_employees_formal_female_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->female_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->male_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->male_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_female_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->female_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_total', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);

                if (is_null($number_of_employees) || !isset($number_of_employees->formal)) {
                    return 0; // or any default value you consider appropriate
                }

                return ($number_of_employees->formal->female_18_35 ?? 0) +
                    ($number_of_employees->formal->male_18_35 ?? 0) +
                    ($number_of_employees->formal->male_35_plus ?? 0) +
                    ($number_of_employees->formal->female_35_plus ?? 0);
            })
            ->add('number_of_employees_informal_female_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->female_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->male_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->male_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_female_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->female_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_total', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);

                if (is_null($number_of_employees) || !isset($number_of_employees->informal)) {
                    return 0; // or any default value you consider appropriate
                }

                return ($number_of_employees->informal->female_18_35 ?? 0) +
                    ($number_of_employees->informal->male_18_35 ?? 0) +
                    ($number_of_employees->informal->male_35_plus ?? 0) +
                    ($number_of_employees->informal->female_35_plus ?? 0);
            })
            ->add('area_under_cultivation_total', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->total ?? null;
            })
            ->add('area_under_cultivation_variety_1', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->variety_1 ?? null;
            })
            ->add('area_under_cultivation_variety_2', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->variety_2 ?? null;
            })
            ->add('area_under_cultivation_variety_3', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->variety_3 ?? null;
            })
            ->add('area_under_cultivation_variety_4', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->variety_4 ?? null;
            })
            ->add('area_under_cultivation_variety_5', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation);
                return $area_under_cultivation->variety_5 ?? null;
            })
            ->add('number_of_plantlets_produced_potato', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->potato ?? null;
            })
            ->add('number_of_plantlets_produced_cassava', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->cassava ?? null;
            })
            ->add('number_of_plantlets_produced_sw_potato', function ($model) {
                $number_of_plantlets_produced = json_decode($model->number_of_plantlets_produced);
                return $number_of_plantlets_produced->sweet_potato ?? null;
            })
            ->add('number_of_screen_house_vines_harvested', fn($model) => $model->number_of_screen_house_vines_harvested ?? null)
            ->add('number_of_screen_house_min_tubers_harvested', fn($model) => $model->number_of_screen_house_min_tubers_harvested ?? null)
            ->add('number_of_sah_plants_produced', fn($model) => $model->number_of_sah_plants_produced ?? null)
            ->add('basic_seed_multiplication_total', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->total ?? null;
            })
            ->add('basic_seed_multiplication_variety_1', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_1 ?? null;
            })
            ->add('basic_seed_multiplication_variety_2', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_2 ?? null;
            })
            ->add('basic_seed_multiplication_variety_3', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_3 ?? null;
            })
            ->add('basic_seed_multiplication_variety_4', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_4 ?? null;
            })
            ->add('basic_seed_multiplication_variety_5', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_5 ?? null;
            })
            ->add('basic_seed_multiplication_variety_6', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_6 ?? null;
            })
            ->add('basic_seed_multiplication_variety_7', function ($model) {
                $basic_seed_multiplication = json_decode($model->area_under_basic_seed_multiplication);
                return $basic_seed_multiplication->variety_7 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_total', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->total ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_1', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_1 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_2', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_2 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_3', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_3 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_4', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_4 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_5', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_5 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_6', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_6 ?? null;
            })
            ->add('area_under_certified_seed_multiplication_variety_7', function ($model) {
                $certified_seed_multiplication = json_decode($model->area_under_certified_seed_multiplication);
                return $certified_seed_multiplication->variety_7 ?? null;
            })
            ->add('is_registered_seed_producer', fn($model) => $model->is_registered_seed_producer == 1 ? 'Yes' : 'No')
            ->add('seed_service_unit_registration_details_date', function ($model) {

                $seed_service_unit_registration_details = json_decode($model->seed_service_unit_registration_details);

                if (is_null($seed_service_unit_registration_details)) {
                    return null;
                }

                return Carbon::parse(json_decode($model->seed_service_unit_registration_details)->registration_date)->format('d/m/Y');
            })
            ->add('seed_service_unit_registration_details_number', fn($model) => json_decode($model->seed_service_unit_registration_details)->registration_number ?? null)
            ->add('uses_certified_seed', fn($model) => $model->uses_certified_seed == 1 ? 'Yes' : 'No')
            ->add('market_segment_fresh', function ($model) {
                $market_segment = json_decode($model->market_segment);
                return $market_segment->fresh ?? null;
            })
            ->add('market_segment_processed', function ($model) {
                $market_segment = json_decode($model->market_segment);
                return $market_segment->processed ?? null;
            })
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')
            ->add('total_production_value_previous_season_total', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->total ?? 0;
            })
            ->add('total_production_value_previous_season_date', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->date_of_maximum_sales === null ? null : Carbon::parse($total_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y');
            })
            ->add('total_irrigation_production_value_previous_season_total', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->total ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_date', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->date_of_maximum_sales === null ? null : Carbon::parse($total_irrigation_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y');
            })
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? 'Yes' : 'No')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? 'Yes' : 'No')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? 'Yes' : 'No')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('aggregation_centers_response', function ($model) {
                $aggregation_centers = json_decode($model->aggregation_centers);
                return $aggregation_centers->response == 1 ? 'Yes' : 'No';
            })
            ->add('aggregation_centers_specify', function ($model) {
                $aggregation_centers = json_decode($model->aggregation_centers);
                return $aggregation_centers->specify ?? null;
            })
            ->add('aggregation_center_sales');
    }


    public function columns(): array
    {
        return [
            Column::action('Action')->bodyAttribute('text-nowrap '),
            Column::make('Id', 'id'),
            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),

            Column::make('Enterprise', 'enterprise', 'location_data->enterprise')->sortable(),
            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa', 'location_data->epa')->sortable(),
            Column::make('Section', 'section', 'location_data->section')->sortable(),


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


            Column::make('Number of members/Male 18-35', 'number_of_members_male_18_35', 'number_of_members->male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 18-35', 'number_of_members_female_18_35', 'number_of_members->female_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 35+', 'number_of_members_male_35_plus', 'number_of_members->male_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 35+', 'number_of_members_female_35_plus', 'number_of_members->female_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/total', 'number_of_members_total', 'number_of_members->total')
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

            Column::make('Registration details/Body', 'registration_details_body', 'registration_details->registration_body')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/date', 'registration_details_date', 'registration_details->registration_date')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/number', 'registration_details_number', 'registration_details->number')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Female 18-35', 'number_of_employees_formal_female_18_35', 'number_of_employees->formal->female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 18-35', 'number_of_employees_formal_male_18_35', 'number_of_employees->formal->male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 35 Plus', 'number_of_employees_formal_male_35_plus', 'number_of_employees->formal->male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Female 35 Plus', 'number_of_employees_formal_female_35_plus', 'number_of_employees->formal->female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Formal', 'number_of_employees_formal_total', 'number_of_employees->formal->total')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 18-35', 'number_of_employees_informal_female_18_35', 'number_of_employees->informal->female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 18-35', 'number_of_employees_informal_male_18_35', 'number_of_employees->informal->male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 35 Plus', 'number_of_employees_informal_male_35_plus', 'number_of_employees->informal->male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 35 Plus', 'number_of_employees_informal_female_35_plus', 'number_of_employees->informal->female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Informal', 'number_of_employees_informal_total', 'number_of_employees->informal->total')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/total', 'area_under_cultivation_total', 'area_under_cultivation', )
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 1', 'area_under_cultivation_variety_1', 'area_under_cultivation->variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 2', 'area_under_cultivation_variety_2', 'area_under_cultivation->variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 3', 'area_under_cultivation_variety_3', 'area_under_cultivation->variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 4', 'area_under_cultivation_variety_4', 'area_under_cultivation->variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation/variety 5', 'area_under_cultivation_variety_5', 'area_under_cultivation->variety_5')
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

            Column::make('Area under basic seed multiplication/variety', 'basic_seed_multiplication_variety_1', 'basic_seed_multiplication->variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 2', 'basic_seed_multiplication_variety_2', 'basic_seed_multiplication->variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 3', 'basic_seed_multiplication_variety_3', 'basic_seed_multiplication->variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 4', 'basic_seed_multiplication_variety_4', 'basic_seed_multiplication->variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 5', 'basic_seed_multiplication_variety_5', 'basic_seed_multiplication->variety_5')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 6', 'basic_seed_multiplication_variety_6', 'basic_seed_multiplication->variety_6')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/variety 7', 'basic_seed_multiplication_variety_7', 'basic_seed_multiplication->variety_7')
                ->sortable()
                ->searchable(),



            Column::make('Area under certified seed multiplication/total', 'area_under_certified_seed_multiplication_total', 'area_under_certified_seed_multiplication->total')

                ->searchable(),

            Column::make('Area under certified seed multiplication/variety', 'area_under_certified_seed_multiplication_variety_1', 'area_under_certified_seed_multiplication->variety_1')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 2', 'area_under_certified_seed_multiplication_variety_2', 'area_under_certified_seed_multiplication->variety_2')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 3', 'area_under_certified_seed_multiplication_variety_3', 'area_under_certified_seed_multiplication->variety_3')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 4', 'area_under_certified_seed_multiplication_variety_4', 'area_under_certified_seed_multiplication->variety_4')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 5', 'area_under_certified_seed_multiplication_variety_5', 'area_under_certified_seed_multiplication->variety_5')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 6', 'area_under_certified_seed_multiplication_variety_6', 'area_under_certified_seed_multiplication->variety_6')
                ->sortable()
                ->searchable(),

            Column::make('Area under certified seed multiplication/variety 7', 'area_under_certified_seed_multiplication_variety_7', 'area_under_certified_seed_multiplication->variety_7')
                ->sortable()
                ->searchable(),



            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details/reg. date', 'seed_service_unit_registration_details_date', 'seed_service_unit_registration_details->registration_date')
                ->sortable()
                ->searchable(),

            Column::make('Seed service unit registration details/ reg. number', 'seed_service_unit_registration_details_number', 'seed_service_unit_registration_details->registration_number')
                ->sortable()
                ->searchable(),

            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),

            Column::make('Market segment/fresh', 'market_segment_fresh')
            ,


            Column::make('Market segment/processed', 'market_segment_processed')
            ,

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),



            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total', 'total_production_value_previous_season->total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date', 'total_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),



            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total', 'total_irrigation_production_value_previous_season->total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date', 'total_irrigation_production_value_previous_season->date_of_maximum_sales')
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

            Column::make('Aggregation centers/Response', 'aggregation_centers_response')
                ->sortable()
                ->searchable(),


            Column::make('Aggregation centers/Specify', 'aggregation_centers_specify')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation center sales', 'aggregation_center_sales')
                ->sortable()
                ->searchable(),



        ];
    }




    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }

    #[On('export-farmers')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/rtc_production_and_marketing_farmers.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Date of recruitment',
                'Name of actor',
                'Name of representative',
                'Phone number',
                'Type',
                'Approach',
                'Enterprise',
                'District',
                'EPA',
                'Section',
                'Sector',
                'Number of members/Male 18-35',
                'Number of members/Female 18-35',
                'Number of members/Male 35+',
                'Number of members/Female 35+',
                'Number of members/Total',
                'Group',
                'Establishment status',
                'Is registered',
                'Registration details/Body',
                'Registration details/Date',
                'Registration details/Number',
                'Number of Employees Formal Female 18-35',
                'Number of Employees Formal Male 18-35',
                'Number of Employees Formal Male 35 Plus',
                'Number of Employees Formal Female 35 Plus',
                'Total Number of Employees Formal',
                'Number of Employees Informal Female 18-35',
                'Number of Employees Informal Male 18-35',
                'Number of Employees Informal Male 35 Plus',
                'Number of Employees Informal Female 35 Plus',
                'Total Number of Employees Informal',
                'Area under cultivation/total',
                'Area under cultivation/variety 1',
                'Area under cultivation/variety 2',
                'Area under cultivation/variety 3',
                'Area under cultivation/variety 4',
                'Area under cultivation/variety 5',
                'Number of plantlets produced/cassava',
                'Number of plantlets produced/potato',
                'Number of plantlets produced/sweet potato',
                'Number of screen house vines harvested',
                'Number of screen house min tubers harvested',
                'Number of sah plants produced',
                'Area under basic seed multiplication/total',
                'Area under basic seed multiplication/variety 1',
                'Area under basic seed multiplication/variety 2',
                'Area under basic seed multiplication/variety 3',
                'Area under basic seed multiplication/variety 4',
                'Area under basic seed multiplication/variety 5',
                'Area under basic seed multiplication/variety 6',
                'Area under basic seed multiplication/variety 7',
                'Area under certified seed multiplication/total',
                'Area under certified seed multiplication/variety 1',
                'Area under certified seed multiplication/variety 2',
                'Area under certified seed multiplication/variety 3',
                'Area under certified seed multiplication/variety 4',
                'Area under certified seed multiplication/variety 5',
                'Area under certified seed multiplication/variety 6',
                'Area under certified seed multiplication/variety 7',
                'Is registered seed producer',
                'Seed service unit registration details/reg. date',
                'Seed service unit registration details/ reg. number',
                'Uses certified seed',
                'Market segment/fresh',
                'Market segment/processed',
                'Has rtc market contract',
                'Total production value previous season/total',
                'Total production value previous season/date of max. sales',
                'Total irrigation production value previous season/total',
                'Total irrigation production value previous season/date of max. sales',
                'Sells to domestic markets',
                'Sells to international markets',
                'Uses market information systems',
                'Market information systems',
                'Aggregation centers/Response',
                'Aggregation centers/Specify',
                'Aggregation center sales',
            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $location = json_decode($item->location_data);
                $main_food = json_decode($item->main_food_data);
                $employees = json_decode($item->number_of_employees);
                $area_under_cultivation = json_decode($item->area_under_cultivation);
                $members = json_decode($item->number_of_members);
                $registration_details = json_decode($item->registration_details);
                $basic_seed_multiplication = json_decode($item->area_under_basic_seed_multiplication);
                $certified_seed_multiplication = json_decode($item->area_under_certified_seed_multiplication);
                $market_segment = json_decode($item->market_segment);
                $aggregation_centers = json_decode($item->aggregation_centers);
                $seed_service_unit_registration_details = json_decode($item->seed_service_unit_registration_details);

                $row = [
                    'id' => $item->id,
                    'date_of_recruitment_formatted' => Carbon::parse($item->date_of_recruitment)->format('d/m/Y'),
                    'name_of_actor' => $item->name_of_actor,
                    'name_of_representative' => $item->name_of_representative,
                    'phone_number' => $item->phone_number,
                    'type' => $item->type,
                    'approach' => $item->approach,
                    'enterprise' => $location->enterprise ?? null,
                    'district' => $location->district ?? null,
                    'epa' => $location->epa ?? null,
                    'section' => $location->section ?? null,
                    'sector' => $item->sector,
                    'number_of_members_male_18_35' => $members->male_18_35 ?? 0,
                    'number_of_members_female_18_35' => $members->female_18_35 ?? 0,
                    'number_of_members_male_35_plus' => $members->male_35_plus ?? 0,
                    'number_of_members_female_35_plus' => $members->female_35_plus ?? 0,
                    'number_of_members_total' => ($members->male_18_35 ?? 0) +
                        ($members->female_18_35 ?? 0) +
                        ($members->male_35_plus ?? 0) +
                        ($members->female_35_plus ?? 0),
                    'group' => $item->group,
                    'establishment_status' => $item->establishment_status,
                    'is_registered' => $item->is_registered == 1 ? 'Yes' : 'No',
                    'registration_details_body' => $registration_details->registration_body ?? null,
                    'registration_details_date' => $registration_details == null ? null : Carbon::parse($registration_details->registration_date)->format('d/m/Y'),
                    'registration_details_number' => $registration_details->registration_number ?? null,
                    'number_of_employees_formal_female_18_35' => $employees->formal->female_18_35 ?? 0,
                    'number_of_employees_formal_male_18_35' => $employees->formal->male_18_35 ?? 0,
                    'number_of_employees_formal_male_35_plus' => $employees->formal->male_35_plus ?? 0,
                    'number_of_employees_formal_female_35_plus' => $employees->formal->female_35_plus ?? 0,
                    'number_of_employees_formal_total' => ($employees->formal->female_18_35 ?? 0) +
                        ($employees->formal->male_18_35 ?? 0) +
                        ($employees->formal->male_35_plus ?? 0) +
                        ($employees->formal->female_35_plus ?? 0),
                    'number_of_employees_informal_female_18_35' => $employees->informal->female_18_35 ?? 0,
                    'number_of_employees_informal_male_18_35' => $employees->informal->male_18_35 ?? 0,
                    'number_of_employees_informal_male_35_plus' => $employees->informal->male_35_plus ?? 0,
                    'number_of_employees_informal_female_35_plus' => $employees->informal->female_35_plus ?? 0,
                    'number_of_employees_informal_total' => ($employees->informal->female_18_35 ?? 0) +
                        ($employees->informal->male_18_35 ?? 0) +
                        ($employees->informal->male_35_plus ?? 0) +
                        ($employees->informal->female_35_plus ?? 0),
                    'area_under_cultivation_total' => $area_under_cultivation->total ?? null,
                    'area_under_cultivation_variety_1' => $area_under_cultivation->variety_1 ?? null,
                    'area_under_cultivation_variety_2' => $area_under_cultivation->variety_2 ?? null,
                    'area_under_cultivation_variety_3' => $area_under_cultivation->variety_3 ?? null,
                    'area_under_cultivation_variety_4' => $area_under_cultivation->variety_4 ?? null,
                    'area_under_cultivation_variety_5' => $area_under_cultivation->variety_5 ?? null,
                    'number_of_plantlets_produced_cassava' => json_decode($item->number_of_plantlets_produced)->cassava ?? null,
                    'number_of_plantlets_produced_potato' => json_decode($item->number_of_plantlets_produced)->potato ?? null,
                    'number_of_plantlets_produced_sw_potato' => json_decode($item->number_of_plantlets_produced)->sweet_potato ?? null,
                    'number_of_screen_house_vines_harvested' => $item->number_of_screen_house_vines_harvested ?? null,
                    'number_of_screen_house_min_tubers_harvested' => $item->number_of_screen_house_min_tubers_harvested ?? null,
                    'number_of_sah_plants_produced' => $item->number_of_sah_plants_produced ?? null,
                    'basic_seed_multiplication_total' => $basic_seed_multiplication->total ?? null,
                    'basic_seed_multiplication_variety_1' => $basic_seed_multiplication->variety_1 ?? null,
                    'basic_seed_multiplication_variety_2' => $basic_seed_multiplication->variety_2 ?? null,
                    'basic_seed_multiplication_variety_3' => $basic_seed_multiplication->variety_3 ?? null,
                    'basic_seed_multiplication_variety_4' => $basic_seed_multiplication->variety_4 ?? null,
                    'basic_seed_multiplication_variety_5' => $basic_seed_multiplication->variety_5 ?? null,
                    'basic_seed_multiplication_variety_6' => $basic_seed_multiplication->variety_6 ?? null,
                    'basic_seed_multiplication_variety_7' => $basic_seed_multiplication->variety_7 ?? null,
                    'area_under_certified_seed_multiplication_total' => $certified_seed_multiplication->total ?? null,
                    'area_under_certified_seed_multiplication_variety_1' => $certified_seed_multiplication->variety_1 ?? null,
                    'area_under_certified_seed_multiplication_variety_2' => $certified_seed_multiplication->variety_2 ?? null,
                    'area_under_certified_seed_multiplication_variety_3' => $certified_seed_multiplication->variety_3 ?? null,
                    'area_under_certified_seed_multiplication_variety_4' => $certified_seed_multiplication->variety_4 ?? null,
                    'area_under_certified_seed_multiplication_variety_5' => $certified_seed_multiplication->variety_5 ?? null,
                    'area_under_certified_seed_multiplication_variety_6' => $certified_seed_multiplication->variety_6 ?? null,
                    'area_under_certified_seed_multiplication_variety_7' => $certified_seed_multiplication->variety_7 ?? null,
                    'is_registered_seed_producer' => $item->is_registered_seed_producer == 1 ? 'Yes' : 'No',
                    'seed_service_unit_registration_details_date' => $seed_service_unit_registration_details == null ? null : Carbon::parse($seed_service_unit_registration_details->registration_date)->format('d/m/Y'),
                    'seed_service_unit_registration_details_number' => $seed_service_unit_registration_details->registration_number ?? null,
                    'uses_certified_seed' => $item->uses_certified_seed == 1 ? 'Yes' : 'No',
                    'market_segment_fresh' => $market_segment->fresh ?? null,
                    'market_segment_processed' => $market_segment->processed ?? null,
                    'has_rtc_market_contract' => $item->has_rtc_market_contract == 1 ? 'Yes' : 'No',
                    'total_production_value_previous_season_total' => json_decode($item->total_production_value_previous_season)->total ?? 0,
                    'total_production_value_previous_season_date' => json_decode($item->total_production_value_previous_season) == null ? null : Carbon::parse(json_decode($item->total_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y'),
                    'total_irrigation_production_value_previous_season_total' => json_decode($item->total_irrigation_production_value_previous_season)->total ?? 0,
                    'total_irrigation_production_value_previous_season_date' => json_decode($item->total_irrigation_production_value_previous_season) == null ? null : Carbon::parse(json_decode($item->total_irrigation_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y'),
                    'sells_to_domestic_markets' => $item->sells_to_domestic_markets == 1 ? 'Yes' : 'No',
                    'sells_to_international_markets' => $item->sells_to_international_markets == 1 ? 'Yes' : 'No',
                    'uses_market_information_systems' => $item->uses_market_information_systems == 1 ? 'Yes' : 'No',
                    'market_information_systems' => $item->market_information_systems ?? null,
                    'aggregation_centers_response' => $aggregation_centers->response == 1 ? 'Yes' : 'No',
                    'aggregation_centers_specify' => $aggregation_centers->specify ?? null,
                    'aggregation_center_sales' => $item->aggregation_center_sales ?? null,
                ];

                $writer->addRow($row);
            }
        }

        // Close the writer and get the path of the file
        $writer->close();

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function filters(): array
    {
        return [
            Filter::inputText('enterprise', 'location_data->enterprise'),
            Filter::inputText('section', 'location_data->section'),
            Filter::inputText('epa', 'location_data->epa'),
            Filter::inputText('district', 'location_data->district'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function openModal($id)
    {

        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        return redirect()->to('' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $id . '');
    }


    public function actions($row): array
    {
        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $route = '' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $row->id . '';

        return [
            Button::add('add-follow-up')

                ->render(function ($model) use ($route) {
                    return Blade::render(<<<HTML
            <a href="$route"  data-bs-toggle="tooltip" data-bs-title="add follow up" class="btn btn-primary" >Add <i class="bx bx-plus"></i></a>
            HTML);
                })

            ,


        ];

    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            // Rule::button('add-follow-up')
            //     ->when(fn($row) => !(RpmFarmerFollowUp::find($row->id)))
            //     ->hide(),
        ];
    }

}
