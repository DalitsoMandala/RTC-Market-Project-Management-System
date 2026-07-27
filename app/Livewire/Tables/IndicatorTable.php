<?php
namespace App\Livewire\Tables;

use App\Models\Cgiar_Project;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder as ModelBuilder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
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
        if ($user->hasAnyRole('manager') || $user->hasAnyRole('monitor') || $user->hasAnyRole('admin') || $user->hasAnyRole('project_manager') || $user->hasAnyRole('staff')) {
            return Indicator::query()->with([
                'project',
                'disaggregations',
                'responsiblePeopleforIndicators.organisation',
                'forms', 'class',
            ])->join('indicator_classes', 'indicator_classes.indicator_id', '=', 'indicators.id')->select([
                'indicators.*',
                'indicator_classes.class as file_location',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
            ]);
        } else {
            //responsiblePeopleforIndicators are organisations reponsible for these indicators
            $user            = User::find($this->userId);
            $organisation_id = $user->organisation->id;

            $data = Indicator::query()->with([
                'project',
                'responsiblePeopleforIndicators',
                'disaggregations',
                'forms',
            ])->whereHas('responsiblePeopleforIndicators', function ($query) use ($organisation_id) {
                $query->where('organisation_id', $organisation_id);
            });
            return $data->select([
                'indicators.*',

                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
            ]);
            // return Indicator::query()->with(['project', 'responsiblePeopleforIndicators']);

        }
    }

    public function relationSearch(): array
    {
        return [
            'project'                                     => [ // relationship on dishes model
                'name',                                            // column enabled to search

            ],
            'disaggregations'                             => [ // relationship on dishes model
                'name',                                            // column enabled to search

            ],

            'responsiblePeopleforIndicators.organisation' => [ // relationship on dishes model
                'name',                                            // column enabled to search

            ],
            'forms'                                       => [ // relationship on dishes model
                'name',                                            // column enabled to search

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

                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('cip-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('admin')) {

                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator" href="' . route('admin-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('project_manager')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator"  href="' . route('project_manager-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('staff')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator"  href="' . route('cip-staff-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
                } else if ($user->hasAnyRole('monitor')) {
                    return '<a class="text-decoration-underline text-body custom-tooltip" title="View Indicator"  href="' . route('monitor-indicator-view', $model->id) . '" >' . $model->indicator_name . '</a>';
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

                $orgs     = Organisation::whereIn('id', $orgIds)->get();
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
            ->add('file_location', function ($model) {
                $location = $model->file_location;

                return $model->file_location;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function ensureRtcMarketPathExists(string $location, ?string $fileName = null, string $defaultContent = ''): string
    {
        // 1. Convert backslashes to standard forward slashes
        $cleanPath = str_replace('\\', '/', $location);

        // 2. Remove leading 'App/' or 'app/' if passed as a namespace
        $relativePath = preg_replace('#^app/#i', '', $cleanPath);

        // 3. Break path into segments for Laravel's app_path()
        $segments = explode('/', $relativePath);

        // 4. Resolve absolute OS-safe directory path
        $directoryPath = app_path(...$segments);

        // 5. Create directory recursively if it doesn't exist
        if (! File::exists($directoryPath)) {
            File::makeDirectory($directoryPath, 0755, true);
        }

        // 6. Return directory path if no file name was requested
        if (! $fileName) {
            return $directoryPath;
        }

        // 7. Resolve absolute OS-safe file path
        $filePath = app_path(...array_merge($segments, [$fileName]));

        // 8. Create file if it doesn't exist
        if (! File::exists($filePath)) {
            File::put($filePath, $defaultContent);
        }

        return $filePath;
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
                ->optionValue('id')

            ,
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
    //             ->slot('<i class="bx bx-pen"></i> Edit')
    //             ->id()
    //             ->class('btn btn-warning')
    //             ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-indicator-modal']),
    //     ];
    // }
    public function columns(): array
    {
        $columns = [
            Column::make('#', 'rn')->sortable(),
            Column::make('Indicator #', 'indicator_no_bold', 'indicator_no')->searchable(),
            Column::make('Indicator', 'name_link', 'indicator_name')
                ->bodyAttribute(styleAttr: "white-space:wrap")
                ->headerAttribute(styleAttr: "min-width:350px;")
                ->sortable()
                ->searchable(),

            Column::make('Disaggregations', 'disaggregations')
                ->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('File Location', 'file_location')->searchable(),
        ];

        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole('manager')) {
            $columns[] = Column::make('Sources', 'sources');
        }

        // Only show actions to roles allowed to edit/delete
        if ($user->hasAnyRole('manager', 'admin')) {
            $columns[] = Column::action('Actions');
        }

        return $columns;
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
            // ->attributes(['onclick' => "return confirm('Delete this indicator?')"])
            //->dispatch('deleteIndicator', ['indicatorId' => $row->id]),
            // If your PowerGrid version doesn't support ->openModal() confirmation,
            // dispatch straight to a JS confirm() instead, e.g.:
            // ->dispatch('confirmDeleteIndicator', ['indicatorId' => $row->id])
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

        ];
    }
}
