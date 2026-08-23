<?php
namespace App\Livewire\tables;

use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class IndicatorSourcesTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);

        if ($user->hasAnyRole(['admin'])) {
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
                $data = $model->forms->pluck('name');

                return implode(', ', $data->toArray());
            })
            ->add('type_of_submission')
            ->add('form_id')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'id'),
            Column::make('Indicator #', 'indicator_no')->searchable(),
            Column::make('Indicator', 'indicator', 'indicator_name')->searchable()->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('Organisation', 'organisation')->searchable()->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),

            Column::make('Assigned Forms', 'forms')->headerAttribute(styleAttr: "min-width:300px;")
                ->bodyAttribute(styleAttr: "white-space:wrap")->searchable(),

            Column::action(''),
        ];
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
    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {}

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-warning btn-sm goUp custom-tooltip')
                ->tooltip('Edit')
                ->can(User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->dispatch('showModal', [
                    'rowId' => $row->id,
                    'name'  => 'view-modal',
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
