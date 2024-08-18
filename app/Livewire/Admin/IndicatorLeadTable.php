<?php

namespace App\Livewire\admin;

use App\Models\Indicator;
use Illuminate\Support\Carbon;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
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

final class IndicatorLeadTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

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
        return Indicator::with(['organisation']);
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
            ->add('indicator', fn($model) => $model->indicator_name)
            ->add('type_of_submission')
            ->add('form_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator'),
            Column::make('Organisation', 'organisation')->searchable(),




            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('indicator', 'indicators.indicator_name')
                ->dataSource(Indicator::distinct()->get())
                ->optionValue('indicator_name')
                ->optionLabel('indicator_name')
            ,

            Filter::inputText('organisation')
                ->filterRelation('organisation', 'name')
        ];
    }
    public function relationSearch(): array
    {
        return [

            'organisation' => [ // relationship on dishes model
                'name', // column enabled to search

            ],





        ];
    }
    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {

    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-primary goUp')
                ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-modal']),

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
