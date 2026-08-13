<?php
namespace App\Livewire\Tables;

use App\Models\Cgiar_Project;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as ModelBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class IndicatorTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public $count = 1;

    public function setUp(): array
    {
        return [
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
    public function datasource(): ?ModelBuilder
    {
        $user = User::find($this->userId);

        if ($user->hasAnyRole(['admin'])) {
            return Indicator::query()->with([
                'project',
                'disaggregations',
                'responsiblePeopleforIndicators.organisation',
                'forms',
                'class',
            ])->leftJoin('indicator_classes', 'indicator_classes.indicator_id', '=', 'indicators.id')
                ->select([
                    'indicators.*',
                    'indicator_classes.class as file_location',
                    DB::Raw('ROW_NUMBER() OVER (ORDER BY indicators.id) AS rn'),
                ])
            // 1st: Active items (1) first, Inactive items (0) at the end
                ->orderBy('indicators.is_active', 'desc')
            // 2nd: Letters first, then numbers
                ->orderByRaw("CASE WHEN indicators.indicator_no REGEXP '^[a-zA-Z]' THEN 0 ELSE 1 END ASC")
            // 3rd: Indicator number sorting
                ->orderBy('indicators.indicator_no', 'asc');

        } else if ($user->hasAnyRole(['manager', 'monitor', 'project_manager', 'staff'])) {
            return Indicator::query()->with([
                'project',
                'disaggregations',
                'responsiblePeopleforIndicators.organisation',
                'forms',
                'class',
            ])->leftJoin('indicator_classes', 'indicator_classes.indicator_id', '=', 'indicators.id')
                ->where('indicators.is_active', 1)
                ->select([
                    'indicators.*',
                    'indicator_classes.class as file_location',
                    DB::Raw('ROW_NUMBER() OVER (ORDER BY indicators.id) AS rn'),
                ])

            // 1st: Active items (1) first, Inactive items (0) at the end
                ->orderBy('indicators.is_active', 'desc')
            // 2nd: Letters first, then numbers
                ->orderByRaw("CASE WHEN indicators.indicator_no REGEXP '^[a-zA-Z]' THEN 0 ELSE 1 END ASC")
            // 3rd: Indicator number sorting
                ->orderBy('indicators.indicator_no', 'asc');

        } else {
            $organisation_id = $user->organisation->id;

            $data = Indicator::query()->with([
                'project',
                'responsiblePeopleforIndicators',
                'disaggregations',
                'forms',
            ])->whereHas('responsiblePeopleforIndicators', function ($query) use ($organisation_id) {
                $query->where('organisation_id', $organisation_id);
            })->where('indicators.is_active', 1);

            return $data->select([
                'indicators.*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY indicators.id) AS rn'),
            ])
            // 1st: Active items (1) first, Inactive items (0) at the end
                ->orderBy('indicators.is_active', 'desc')
            // 2nd: Letters first, then numbers
                ->orderByRaw("CASE WHEN indicators.indicator_no REGEXP '^[a-zA-Z]' THEN 0 ELSE 1 END ASC")
            // 3rd: Indicator number sorting
                ->orderBy('indicators.indicator_no', 'asc');
        }
    }

    public function relationSearch(): array
    {
        return [
            'project'                                     => ['name'],
            'disaggregations'                             => ['name'],
            'responsiblePeopleforIndicators.organisation' => ['name'],
            'forms'                                       => ['name'],
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', fn($model) => $this->count++)
            ->add('rn')
            ->add('indicator_no')
            ->add('indicator_no_bold', fn($model) => '<b>' . $model->indicator_no . '</b>')
            ->add('indicator_name')
            ->add('name_link', function ($model) {
                $user = User::find($this->userId);
                if ($user->hasAnyRole('manager')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('cip-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('admin')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('admin-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('project_manager')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('project_manager-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('staff')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('cip-staff-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('monitor')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('monitor-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else {
                    return '<a class="text-decoration-underline custom-tooltip" title="View Indicator" href="' . route('external-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                }
            })
            ->add('project_id')
            ->add('project_name', fn($model) => $model->project->name ?? '')
            ->add('cgiar_project', fn($model) => Cgiar_Project::find($model->project_id)->name ?? null)
            ->add('lead_partner', function ($model) {
                $orgIds = $model->responsiblePeopleforIndicators->pluck('organisation_id');
                $orgs   = Organisation::whereIn('id', $orgIds)->get();
                return implode(', ', $orgs->pluck('name')->toArray());
            })
            ->add('sources', function ($model) {
                $forms = $model->forms->pluck('name')->toArray();
                return ucfirst(strtolower(implode(', ', $forms)));
            })
            ->add('disaggregations', function ($model) {
                $disaggregations = $model->disaggregations;
                if ($disaggregations) {
                    return implode(', ', $disaggregations->pluck('name')->toArray());
                }
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
            ->add('file_location', fn($model) => $model->file_location)
            ->add('created_at')
            ->add('updated_at');
    }

    public function filters(): array
    {
        return [
            Filter::select('name_link', 'id')
                ->dataSource(fn() => Indicator::select(['indicator_no', 'indicator_name', 'id'])->distinct()->get()->map(function ($indicator) {
                    return [
                        'id'    => $indicator->id,
                        'label' => "({$indicator->indicator_no}) " . $indicator->indicator_name,
                        'indicator_no'   => $indicator->indicator_no,
                        'indicator_name' => $indicator->indicator_name,
                    ];
                }))
                ->optionLabel('label')
                ->optionValue('id'),
        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function columns(): array
    {
        $columns = [

            Column::make('Indicator #', 'indicator_no_bold', 'indicator_no')->sortable()->searchable(),
            Column::make('Indicator', 'name_link', 'indicator_name')
                ->bodyAttribute(styleAttr: "white-space:wrap")
                ->headerAttribute(styleAttr: "min-width:350px;")

                ->searchable(),

            Column::make('Disaggregations', 'disaggregations')
                ->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('File Location', 'file_location')->searchable()->hidden(),
            $columns[] = Column::action('Actions')->hidden(),
        ];

        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('admin')) {
            $columns[] = Column::action('Actions');
            $columns[] = Column::make('Active', 'is_active')
                ->sortable()
                ->searchable();
        }

        return $columns;
    }

    #[On('toggleIndicatorStatus')]
    public function toggleIndicatorStatus($indicatorId): void
    {
        $indicator = Indicator::find($indicatorId);

        if ($indicator) {
            $indicator->is_active = ! $indicator->is_active;
            $indicator->save();
        }
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-sm btn-warning')
                ->dispatch('openIndicatorModal', ['indicatorId' => $row->id]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash"></i>')
                ->id()
                ->class('btn btn-sm btn-danger')
                ->dispatch('openDeleteIndicatorModal', ['indicatorId' => $row->id]),
        ];
    }

    public function actionRules($row): array
    {
        return [
            Rule::button('edit')
                ->when(function ($row) {
                    $user = User::find(auth()->user()->id);
                    if ($user->hasAnyRole('external')) {
                        return true;
                    } else {
                        return false;
                    }
                })
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => true)
                ->disable(),
        ];
    }
}
