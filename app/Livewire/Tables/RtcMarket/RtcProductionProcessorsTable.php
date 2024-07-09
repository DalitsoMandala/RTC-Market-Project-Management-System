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
        $this->showCheckBox();

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
        return DB::table('rtc_production_processors');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('location_data')
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('sector')
            ->add('number_of_members')
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered')
            ->add('registration_details')
            ->add('number_of_employees')
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
            ->add('uuid')
            ->add('user_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::action('Action'),
            Column::make('Id', 'id'),
            Column::make('Location data', 'location_data')
                ->sortable()
                ->searchable(),

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

            Column::make('Number of members', 'number_of_members')
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

            Column::make('Registration details', 'registration_details')
                ->sortable()
                ->searchable(),

            Column::make('Number of employees', 'number_of_employees')
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

            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),

            Column::make('User id', 'user_id'),
            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->sortable()
                ->searchable(),

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
        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $route = '' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $row->id . '';

        return [


            Button::add('add-follow-up')

                ->render(function ($model) use ($route) {
                    return Blade::render(<<<HTML
            <a href="$route" data-bs-toggle="tooltip" data-bs-title="add follow up" class="btn btn-primary" ><i class="bx bxs-add-to-queue"></i></a>
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
