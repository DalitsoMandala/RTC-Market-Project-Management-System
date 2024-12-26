<?php

namespace App\Livewire\admin;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use Illuminate\Support\Carbon;
use App\Models\ResponsiblePerson;
use App\Models\Source;
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

final class OrganisationFormsTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {


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
        return Indicator::with([
            'organisation',
            'forms',
            'responsiblePeopleforIndicators',
            'responsiblePeopleforIndicators.sources'
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
                $sources = $model->responsiblePeopleforIndicators->pluck('id');

                $assigned = Source::whereIn('person_id', $sources)->get();

                $arrayMap = [];

                foreach ($assigned as $source) {
                    $form = Form::find($source->form_id)->name;
                    $person = ResponsiblePerson::where('id', $source->person_id)
                        ->where('indicator_id', $model->id)
                        ->first();
                    $organisation = $person->organisation->name;

                    // Append the form name to the corresponding organisation in the array map
                    $arrayMap[$organisation][] = $form;
                }

                // Generate the HTML for the Bootstrap list group
                $html = '<ul class="list-group">';

                foreach ($arrayMap as $organisation => $forms) {
                    $html .= '<li class="list-group-item"><b>' . $organisation . '</b> (' . implode(', ', $forms) . ')</li>';
                }

                $html .= '</ul>';

                return $html;
            })


            ->add('type_of_submission')
            ->add('form_id')
            ->add('created_at')
            ->add('updated_at');
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator #', 'indicator_no'),
            Column::make('Indicator', 'indicator'),
            Column::make('Organisation', 'organisation')->searchable(),

            Column::make('Assigned Forms', 'forms')->searchable(),



            Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [];
    }
    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i> Edit')
                ->id()
                ->class('btn btn-warning goUp')
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name' => 'view-modal'
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
