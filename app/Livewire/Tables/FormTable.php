<?php

namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\User;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

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

        $user = User::find(auth()->user()->id);

        // if ($user->hasAnyRole('external')) {
        //     $responsiblePeople = ResponsiblePerson::where('organisation_id', $user->organisation->id)
        //         ->with('sources.form')
        //         ->get();

        //     $forms = $responsiblePeople->flatMap(function ($person) {
        //         return $person->sources->pluck('form');
        //     })->unique();

        //     $formIds = $forms->pluck('id');
        //     return Form::query()->with('project', 'indicators')->where('name', '!=', 'SEED DISTRIBUTION REGISTER')->whereIn('id', $formIds);
        // }




        return Form::query()->with('project', 'indicators')->where('name', '!=', 'REPORT FORM');
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
            ->add('id', function ($model) {})
            ->add('name')
            ->add('name_formatted', function ($model) {

                $form_name = str_replace(' ', '-', strtolower($model->name));
                $project = str_replace(' ', '-', strtolower($model->project->name));
                if ($model->name == 'REPORT FORM') {
                    return '<a class="pe-none text-muted"  href="forms/' . $project . '/' . $form_name . '/view" >REPORTS</a>';
                } else

                    return '<a class="text-decoration-underline"  href="forms/' . $project . '/' . $form_name . '/view" >' . $model->name . '</a>';
            })
            ->add('type')
            ->add('project_id')
            ->add('project', function ($model) {
                return $model->project->name;
            })
            ->add('followup', function ($model) {


                $form = Form::find($model->id);
                $user = Auth::user();
                $organisation = $user->organisation;
                $routePrefix = Route::current()->getPrefix();
                $form_name = str_replace(' ', '-', strtolower($form->name));
                $project = str_replace(' ', '-', strtolower($form->project->name));

                $route = $routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/';
                $projectManager = User::find(auth()->user()->id)->hasAllRoles(['internal', 'cip', 'project_manager']) ? 'disabled' : '';

                if ($form->name === 'RTC PRODUCTION AND MARKETING FORM FARMERS' || $form->name === 'RTC PRODUCTION AND MARKETING FORM PROCESSORS') {
                    return '<a class="btn btn-warning btn-sm ' . $projectManager . ' "  href="' . $route . '" >Add Follow up <i class="bx bx-chevron-right"></i></a>';
                }

                return null;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [

            Column::make('Name', 'name_formatted', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Project', 'project',)
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->hidden()
                ->searchable(),


            Column::make('Action', 'followup'),

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
        return [];
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
    //             ->slot('<i class="bx bx-pen"></i> Edit')
    //             ->id()
    //             ->class('btn btn-warning')
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
