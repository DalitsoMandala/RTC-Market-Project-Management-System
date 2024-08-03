<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class RtcProductionProcessorsTable extends PowerGridComponent
{
    use WithExport;
    public $routePrefix;
    public function setUp(): array
    {
        //    $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('rtc_production_processors');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('enterprise', function ($model) {
                $data = json_decode($model->location_data);
                return $data->enterprise;
            })
            ->add('district', function ($model) {
                $data = json_decode($model->location_data);
                return $data->district;
            })
            ->add('epa', function ($model) {
                $data = json_decode($model->location_data);

                return $data->epa;
            })
            ->add('section', function ($model) {
                $data = json_decode($model->location_data);
                return $data->section;
            })
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('sector')
            ->add('number_of_members_total', function ($model) {

                return json_decode($model->number_of_members)->total ?? 0;
            })
            ->add('number_of_members_female_18_35', function ($model) {

                return json_decode($model->number_of_members)->female_18_35 ?? 0;
            })

            ->add('number_of_members_male_18_35', function ($model) {

                return json_decode($model->number_of_members)->male_18_35 ?? 0;
            })

            ->add('number_of_members_male_35_plus', function ($model) {

                return json_decode($model->number_of_members)->male_35_plus ?? 0;
            })
            ->add('number_of_members_female_35_plus', function ($model) {

                return json_decode($model->number_of_members)->female_35_plus ?? 0;
            })
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Yes' : 'No';
            })
            ->add('registration_details')
            ->add('registration_details_body', fn($model) => json_decode($model->registration_details)->registration_body ?? null)
            ->add('registration_details_date', fn($model) => json_decode($model->registration_details)->registration_date ?? null)
            ->add('registration_details_number', fn($model) => json_decode($model->registration_details)->registration_number ?? null)
            ->add('formal_employees_female_18_35', function ($model) {
                return json_decode($model->number_of_employees)->female_18_35 ?? 0;
            })
            ->add('formal_employees_female_35_plus', function ($model) {
                return json_decode($model->number_of_employees)->female_35_plus ?? 0;
            })
            ->add('formal_employees_male_18_35', function ($model) {
                return json_decode($model->number_of_employees)->male_18_35 ?? 0;
            })
            ->add('formal_employees_male_35_plus', function ($model) {
                return json_decode($model->number_of_employees)->male_35_plus ?? 0;
            })
            ->add('informal_employees_total', function ($model) {
                return json_decode($model->number_of_employees)->total ?? 0;
            })
            ->add('informal_employees_female_18_35', function ($model) {
                return json_decode($model->number_of_employees)->female_18_35 ?? 0;
            })
            ->add('informal_employees_female_35_plus', function ($model) {
                return json_decode($model->number_of_employees)->female_35_plus ?? 0;
            })
            ->add('informal_employees_male_18_35', function ($model) {
                return json_decode($model->number_of_employees)->male_18_35 ?? 0;
            })
            ->add('informal_employees_male_35_plus', function ($model) {
                return json_decode($model->number_of_employees)->male_35_plus ?? 0;
            })
            ->add('market_segment_fresh', fn($model) => json_decode($model->market_segment)->fresh ?? null)
            ->add('market_segment_processed', fn($model) => json_decode($model->market_segment)->processed ?? null)
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')
            ->add('total_vol_production_previous_season', fn($model) => $model->total_vol_production_previous_season ?? 0)
            ->add('total_production_value_previous_season_total', fn($model) => json_decode($model->total_production_value_previous_season)->total ?? 0)
            ->add('total_production_value_previous_season_date', fn($model) => Carbon::parse(json_decode($model->total_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y') ?? null)
            ->add('total_vol_irrigation_production_previous_season', fn($model) => $model->total_vol_irrigation_production_previous_season ?? 0)
            ->add('total_irrigation_production_value_previous_season_total', fn($model) => json_decode($model->total_irrigation_production_value_previous_season)->total ?? 0)
            ->add('total_irrigation_production_value_previous_season_date', fn($model) => Carbon::parse(json_decode($model->total_irrigation_production_value_previous_season)->date_of_maximum_sales)->format('d/m/Y') ?? null)
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? 'Yes' : 'No')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? 'Yes' : 'No')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? 'Yes' : 'No')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('aggregation_centers_response', fn($model) => json_decode($model->aggregation_centers)->response == 1 ? 'Yes' : 'No' ?? null)
            ->add('aggregation_centers_specify', fn($model) => json_decode($model->aggregation_centers)->specify ?? null)
            ->add('aggregation_center_sales');
    }

    public function columns(): array
    {
        return [
            Column::action('Action'),
            Column::make('Id', 'id'),

            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),
            Column::make('Enterprise', 'enterprise', 'location_data->enterprise'),
            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa'),
            Column::make('Section', 'section'),
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
            Column::make('Number of members/Female 18-35', 'number_of_members_female_18_35')
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



            Column::make('Formal employees/Total', 'formal_employees_total')
                ->sortable()
                ->searchable(),
            Column::make('Formal employees/Female 18-35', 'formal_employees_female_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Formal employees/Female 35+', 'formal_employees_female_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Formal employees/Male 18-35', 'formal_employees_male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Formal employees/Male 35+', 'formal_employees_male_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Informal employees/Total', 'informal_employees_total')
                ->sortable()
                ->searchable(),
            Column::make('Informal employees/Female 18-35', 'informal_employees_female_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Informal employees/Female 35+', 'informal_employees_female_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Informal employees/Male 18-35', 'informal_employees_male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Informal employees/Male 35+', 'informal_employees_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Market segment/fresh', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment/processed', 'market_segment_processed')
                ->sortable()
                ->searchable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),

            Column::make('Total production previous season', 'total_vol_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production previous season', 'total_vol_irrigation_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date')
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

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_recruitment'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $route = '' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $row->id . '';

        return [


            Button::add('add-follow-up')

                ->render(function ($model) use ($route) {
                    return Blade::render(<<<HTML
            <a  href="$route" data-bs-toggle="tooltip" data-bs-title="add follow up" class="btn btn-primary" ><i class="bx bxs-add-to-queue"></i></a>
            HTML);
                })

            ,
        ];
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
