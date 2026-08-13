<?php
namespace App\Livewire\admin;

use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class OrganisationFormsTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Indicator::with([
            'organisation',
            'forms',
            'responsiblePeopleforIndicators',
            // 'responsiblePeopleforIndicators.sources'
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('organisation_id')
            ->add('organisation', function ($model) {
                $data = $model->organisation->pluck('name');

                return implode(', ', $data->toArray());
            })
            ->add('indicator_id')
            ->add('indicator_no', fn($model) => $model->indicator_no)
            ->add('indicator', fn($model) => $model->indicator_name)
            ->add('forms', function ($model) {
                $data = $model->forms->pluck('name');

                return implode(', ', $data->toArray());
            })
            ->add('type_of_submission')
            ->add('form_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id'),
            Column::make('Indicator #', 'indicator_no')->searchable(),
            Column::make('Indicator', 'indicator', 'indicator_name')->searchable()->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('Organisation', 'organisation')->searchable()->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),

            Column::make('Assigned Forms', 'forms')->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap")->searchable(),

            Column::action(''),
        ];
    }

    public function filters(): array
    {
        return [];
    }
    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {}

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning btn-sm goUp custom-tooltip')
                ->tooltip('Edit')
                ->can(User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name'  => 'view-modal',
                ]),

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
