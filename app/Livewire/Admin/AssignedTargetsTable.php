<?php

namespace App\Livewire\admin;

use App\Models\Organisation;
use Illuminate\Support\Carbon;
use App\Models\IndicatorTarget;
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

final class AssignedTargetsTable extends PowerGridComponent
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
        return IndicatorTarget::query()->with([
            'details',
            'assignedTargets'
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('indicator_id')
            ->add('indicator', fn($model) => $model->indicator->indicator_name)
            ->add('financial_year_id')
            ->add('financial_year', fn($model) => $model->financialYear->number)
            ->add('project_id')
            ->add('project', fn($model) => $model->project->name)
            ->add('assigned_targets', function ($model) {
                $targets = $model->assignedTargets;
                $html = '<ul class="list-group">';
                if ($targets->count() > 0) {
                    $data = $targets->map(function ($target) {
                        $organisation = Organisation::find($target->organisation_id);
                        $target->organisation = $organisation->name;
                        if ($target->detail != null) {
                            $json = json_decode($target->detail, true);
                            $result = '';
                            foreach ($json as $dt) {
                                $dt['type'] = $dt['type'] == 'percentage' ? '%' : '';
                                $result .= $dt['name'] . ' (' . $dt['target_value'] . $dt['type'] . ') ';
                            }

                            $target->detail = $result;
                        }

                        return $target;

                    });

                    foreach ($data as $dt) {
                        $dt->type = $dt->type == 'percentage' ? '%' : '';
                        if ($dt->detail != null) {
                            $html .= '<li class="list-group-item">' . $dt->organisation . ': ' . $dt->detail . '</li>';
                        } else {
                            $html .= '<li class="list-group-item">' . $dt->organisation . ': ' . $dt->target_value . $dt->type . '</li>';
                        }

                    }

                    $html .= '</ul>';

                }
                return $html;
            })

        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator'),
            Column::make('Financial year', 'financial_year'),
            Column::make('Project', 'project'),

            Column::make('Assigned Targets', 'assigned_targets'),
            Column::action('Action')

        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function edit(): void
    {

    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i> Edit')
                ->id()
                ->class('btn btn-warning goUp')
                ->dispatch('show-form', [
                    'rowId' => $row->id,
                    'indicator_id' => $row->indicator_id
                ])
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
