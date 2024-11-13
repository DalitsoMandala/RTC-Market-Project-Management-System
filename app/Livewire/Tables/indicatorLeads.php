<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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

final class indicatorLeads extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //   $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Collection
    {

        return ResponsiblePerson::with([
            'indicator',
            'organisation',
            'sources.form'
        ])->get();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('organisation_id')
            ->add('organisation', fn($model) => Organisation::find($model->organisation_id)->name)
            ->add('indicator_id')
            ->add('indicator', fn($model) => Indicator::find($model->indicator_id)->indicator_name)
            ->add('forms', function ($model) {

                $responsiblePeople = ResponsiblePerson::where('indicator_id', $model->indicator_id)
                    ->where('organisation_id', $model->organisation_id)
                    ->first();

                $formIds = $responsiblePeople->sources->pluck('form_id')->toArray();
                $forms = Form::whereIn('id', $formIds)->pluck('name')->toArray();
                return implode(', ', $forms);
            })
            ->add('type_of_submission')
            ->add('aggregate_type')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Organisation', 'organisation'),
            Column::make('Indicator', 'indicator'),
            Column::make('Forms', 'forms'),


        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datetimepicker('date_established'),
            // Filter::datetimepicker('date_ending'),
            Filter::inputText('indicator')

                ->filterRelation('indicator', 'indicator_name')
            ,

            Filter::inputText('organisation')

                ->filterRelation('organisation', 'name')
            ,



        ];
    }

    public function relationSearch(): array
    {
        return [
            'indicator' => [ // relationship on dishes model
                'indicator_name', // column enabled to search
                // nested relation and column enabled to search
            ],
            'organisation' => [
                'name',
            ],

            'sources.forms' => [
                'name',
            ]
            ,
        ];
    }
    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('console.log(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('<i class="bx bx-pen"></i>')
    //             ->id()
    //             ->tooltip('Edit Record')
    //             ->class('btn btn-warning my-1')
    //             ->dispatch('editData', ['rowId' => $row->id]),
    //     ];
    // }

    public function updated()
    {

        $this->dispatch('reload-tooltips');
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
