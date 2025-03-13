<?php

namespace App\Livewire\Tables;

use DB;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\Cgiar_Project;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use Illuminate\Contracts\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Illuminate\Database\Eloquent\Builder as ModelBuilder;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IndicatorTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public $count = 1;
    public function setUp(): array
    {
        //   $this->showCheckBox();

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

    public function datasource(): ?ModelBuilder
    {
        $user = User::find($this->userId);
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('admin') || $user->hasAnyRole('project_manager') || $user->hasAnyRole('staff')) {
            return Indicator::query()->with([
                'project',
                'disaggregations',
                'responsiblePeopleforIndicators.organisation',
                'forms'
            ])->select([
                'indicators.*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
        } else {
            //responsiblePeopleforIndicators are organisations reponsible for these indicators
            $user = User::find($this->userId);
            $organisation_id = $user->organisation->id;

            $data = Indicator::query()->with([
                'project',
                'responsiblePeopleforIndicators',
                'disaggregations',
                'forms'
            ])->whereHas('responsiblePeopleforIndicators', function ($query) use ($organisation_id) {
                $query->where('organisation_id', $organisation_id);
            });
            return $data->select([
                'indicators.*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
            // return Indicator::query()->with(['project', 'responsiblePeopleforIndicators']);

        }
    }

    public function relationSearch(): array
    {
        return [
            'project' => [ // relationship on dishes model
                'name', // column enabled to search

            ],
            'disaggregations' => [ // relationship on dishes model
                'name', // column enabled to search

            ],

            'responsiblePeopleforIndicators.organisation' => [ // relationship on dishes model
                'name', // column enabled to search

            ],
            'forms' => [ // relationship on dishes model
                'name', // column enabled to search

            ],



        ];
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', fn($model) => $this->count++)
            ->add('rn')
            ->add('indicator_no')
            ->add('indicator_no_bold', function ($model) {

                return '<b>' . $model->indicator_no . '</b>';
            })
            ->add('indicator_name')
            ->add('name_link', function ($model) {
                $user = User::find($this->userId);
                if ($user->hasAnyRole('manager')) {

                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator" href="' . route('cip-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('admin')) {

                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator" href="' . route('admin-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('project_manager')) {
                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator"  href="' . route('project_manager-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('staff')) {
                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator"  href="' . route('cip-staff-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else {
                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator"  href="' . route('external-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                }
            })
            ->add('project_id')
            ->add('project_name', fn($model) => $model->project->name)
            ->add('cgiar_project', function ($model) {
                return Cgiar_Project::find($model->project_id)->name ?? null;
            })

            ->add('lead_partner', function ($model) {

                $orgIds = $model->responsiblePeopleforIndicators->pluck('organisation_id');

                $orgs = Organisation::whereIn('id', $orgIds)->get();
                $orgNames = $orgs->pluck('name')->toArray();
                $orgNames = implode(', ', $orgNames);
                return $orgNames;
            })

            ->add('sources', function ($model) {
                $forms = $model->forms->pluck('name')->toArray();

                $formNames = ucfirst(strtolower(implode(', ', $forms)));
                return $formNames;
            })

            ->add('disaggregations', function ($model) {
                $disaggregations = $model->disaggregations;

                if ($disaggregations) {
                    $implode = $disaggregations->pluck('name')->toArray();
                    return (implode(', ', $implode));
                }
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {

        $showActionColumn = false; // Set this variable based on your condition

        $columns = [
            Column::make('#', 'rn')->sortable(),
            Column::make('Indicator #', 'indicator_no_bold', 'indicator_no')

                ->searchable(),
            Column::make('Indicator', 'name_link', 'indicator_name')
                ->sortable()
                ->searchable(),
            Column::make('Project name', 'project_name'),
            Column::make('Lead partner', 'lead_partner'),
            Column::make('Disaggregations', 'disaggregations')->headerAttribute(styleAttr: "min-width:350px;")
                ->bodyAttribute(styleAttr: "white-space:wrap")
        ];

        $user = Auth::user();
        if ($user->hasAnyRole('manager')) {
            $columns[] = Column::make('Sources', 'sources');
            //   $columns[] = Column::action('Action');
        } else {


            //    $columns[] = Column::action('Action')->hidden();
        }

        return $columns;
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
    //             ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-indicator-modal']),
    //     ];
    // }

    public function actionRules($row): array
    {
        return [

            Rule::button('edit')
                ->when(function ($row) {
                    $user = Auth::user();

                    if ($user->hasAnyRole('external')) {

                        return true;
                    } else {
                        return false;
                    }
                })
                ->disable(),


        ];
    }
}
