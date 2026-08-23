<?php
namespace App\Livewire\Tables;

use App\Models\Form;
use App\Models\Organisation;
use App\Models\User;
use App\Traits\GroupsEndingSoonSubmissionPeriods;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
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
    use GroupsEndingSoonSubmissionPeriods;
    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('external')) {
            return Form::query()->with('project', 'indicators', 'indicators.responsiblePeopleforIndicators')
                ->whereHas('indicators.responsiblePeopleforIndicators', function ($query) use ($user) {
                    $query->where('organisation_id', $user->organisation->id);
                })
                ->select([
                    '*',
                    DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
                ]);
        }

        return Form::query()->with('project', 'indicators')->select([
            '*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
        ]);
    }
    public function relationSearch(): array
    {
        return [
            'project' => [ // relationship on dishes model
                'name',
            ],
        ];
    }

    #[On('hideModal')]
    public function resetform()
    {
        $this->refresh();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', function ($model) {})
            ->add('name')
            ->add('name_formatted', function ($model) {

                $form_name = str_replace(' ', '-', strtolower($model->name));
                $project   = str_replace(' ', '-', strtolower($model->project->name));
                // if ($model->name == 'REPORT FORM') {
                //     return '<a class="pe-none text-muted"  href="forms/' . $project . '/' . $form_name . '/view" >REPORTS</a>';
                // }

                return '<a class="text-decoration-underline custom-tooltip text-body" title="View Form Data"  href="forms/' . $project . '/' . $form_name . '/view" >' . ($model->name) . '</a>';
            })
            ->add('type')
            ->add('project_id')
            ->add('project', function ($model) {
                return $model->project->name;
            })
            ->add('followup', function ($model) {

                $form         = Form::find($model->id);
                $user         = Auth::user();
                $organisation = $user->organisation;
                $routePrefix  = Route::current()->getPrefix();
                $form_name    = str_replace(' ', '-', strtolower($form->name));
                $project      = str_replace(' ', '-', strtolower($form->project->name));

                $route          = $routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/';
                $projectManager = User::find(auth()->user()->id)->hasAllRoles(['manager', 'project_manager']) ? 'disabled' : '';

                if ($form->name === 'RTC PRODUCTION AND MARKETING FORM FARMERS' || $form->name === 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS') {
                    return '<a class="btn text-body btn-sm custom-tooltip" title="Add follow up"  href="' . $route . '" ><i class="bx bx-plus-circle"></i> </a>';
                }

                return '<a class="btn text-body btn-sm disabled custom-tooltip" title="Add follow up"  href="' . $route . '" ><i class="bx bx-plus-circle"></i></a>';
            })
            ->add('is_active', function ($row) {
                $checked = $row->is_active ? 'checked' : '';
                return '
                <div class="square-switch d-flex align-items-stretch">
                    <input type="checkbox" id="square-switch-' . $row->id . '" switch="none" ' . $checked . ' wire:change="toggleIndicatorStatus(' . $row->id . ')">
                    <label class="mb-0" for="square-switch-' . $row->id . '" data-on-label="Yes" data-off-label="No"></label>
                </div>
            ';
            })
            ->add('created_at')
            ->add('updated_at');
    }

    #[On('toggleIndicatorStatus')]
    public function toggleIndicatorStatus($formId): void
    {
        $form = Form::find($formId);

        if ($form) {
            $form->is_active = ! $form->is_active;
            $form->save();
        }
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn')->sortable(),

            Column::make('Name', 'name_formatted', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Project', 'project', )
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->hidden()
                ->searchable(),

            Column::make('Active', 'is_active')
                ->sortable()
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
        return [];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
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
