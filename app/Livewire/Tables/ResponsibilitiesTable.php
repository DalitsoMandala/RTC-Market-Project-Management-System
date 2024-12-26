<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use App\Models\Source;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
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

final class ResponsibilitiesTable extends PowerGridComponent
{
    use WithExport;

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

        return ResponsiblePerson::with([
            'indicator',
            'organisation'
        ]);
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
                $sources = Source::where('person_id', $model->id)->pluck('form_id')->toArray();
                $forms = Form::whereIn('id', $sources)->pluck('name')->toArray();
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








            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[On('closeModal')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i> Edit')
                ->id()
                ->class('btn btn-warning')
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name' => 'view-people-modal'
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
