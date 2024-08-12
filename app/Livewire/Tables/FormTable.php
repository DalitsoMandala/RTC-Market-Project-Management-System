<?php

namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class FormTable extends PowerGridComponent
{
    use WithExport;
    public $organisation;
    public $num = 1;


    public function setUp(): array
    {

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


        return Form::query()->with('project', 'indicators')->where('name', '!=', 'REPORT FORM')->where('name', '!=', 'SEED DISTRIBUTION REGISTER');
    }
    public function relationSearch(): array
    {
        return [
            'project' => [ // relationship on dishes model
                'name'
            ],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_formatted', function ($model) {

                $form_name = str_replace(' ', '-', strtolower($model->name));
                $project = str_replace(' ', '-', strtolower($model->project->name));
                if ($model->name == 'REPORT FORM') {
                    return '<a class="pe-none text-muted"  href="forms/' . $project . '/' . $form_name . '/view" >' . $model->name . '</a>';
                } else
                    if ($model->name == 'ATTENDANCE REGISTER') {
                        return '<a class="text-decoration-underline"  href="forms/' . $project . '/' . $form_name . '" >' . $model->name . '</a>';
                    } else {
                        return '<a class="text-decoration-underline"  href="forms/' . $project . '/' . $form_name . '/view" >' . $model->name . '</a>';
                    }


            })
            ->add('type')
            ->add('project_id')
            ->add('project', function ($model) {
                return $model->project->name;
            })

            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name_formatted', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Project', 'project', )
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->hidden()
                ->searchable(),

            // Column::make('Project id', 'project_id'),
            // Column::make('Created at', 'created_at_formatted', 'created_at')
            //     ->sortable(),

            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

            // Column::make('Updated at', 'updated_at_formatted', 'updated_at')
            //     ->sortable(),

            // Column::make('Updated at', 'updated_at')
            //     ->sortable()
            //     ->searchable(),
            //  Column::action('Action')
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('<i class="bx bx-pen"></i>')
    //             ->id()
    //             ->class('btn btn-primary')
    //             ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-form-modal'])
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
