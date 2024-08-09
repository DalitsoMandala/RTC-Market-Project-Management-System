<?php

namespace App\Livewire\tables;

use App\Models\AssignedTarget;
use App\Models\User;
use App\Models\Indicator;
use App\Models\FinancialYear;
use Illuminate\Support\Carbon;
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

final class TargetProgressTable extends PowerGridComponent
{
    use WithExport;
    public $project_id;
    public $indicator_id;
    public bool $deferLoading = true;
    public function setUp(): array
    {


        return [

            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function targetCalculations()
    {


    }

    public function datasource(): Builder
    {


        $query = FinancialYear::query()->with([
            'indicatorTargets' => function ($model) {
                return $model->where('indicator_id', $this->indicator_id)->where('project_id', $this->project_id);

            },
            'project',
            'indicatorTargets.assignedTargets',
            'indicatorTargets.details',
            'indicatorTargets.assignedTargets.organisation',
        ]);





        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', )
            ->add('number')
            ->add('indicator_target', function ($model) {
                $Indicator_target = $model->indicatorTargets;


                if ($Indicator_target->isNotEmpty()) {

                    if ($Indicator_target[0]->target_value != null) {

                        $type = $Indicator_target[0]->type == 'percentage' ? '%' : '';
                        return $Indicator_target[0]->target_value . $type;
                    } else {
                        $type = $Indicator_target[0]->details->type == 'percentage' ? '%' : '';
                        return $Indicator_target[0]->details->target_value . $type;
                    }
                }

                return null;

            })

            ->add('organisations', function ($model) {
                $Indicator_target = $model->indicatorTargets;

                if ($Indicator_target->isNotEmpty() && $Indicator_target->first()->assignedTargets->isNotEmpty()) {

                    $assignedTargets = $Indicator_target->first()->assignedTargets;
                    $html = '<ul class="list-group list-group-flush p-0">';

                    foreach ($assignedTargets as $assignedTarget) {
                        $html .= '
                            <li class="list-group-item">' . $assignedTarget->organisation->name . '</li>  
                      ';

                    }
                    $html .= '  </ul>';
                    return $html;
                }
            })

            ->add('current_value', function ($model) {
                $Indicator_target = $model->indicatorTargets;

                if ($Indicator_target->isNotEmpty() && $Indicator_target->first()->assignedTargets->isNotEmpty()) {

                    $assignedTargets = $Indicator_target->first()->assignedTargets;
                    $html = '<ul class="list-group list-group-flush p-0">';

                    foreach ($assignedTargets as $assignedTarget) {

                        $indicatir = Indicator::find($this->indicator_id);
                        $currentValue = 0;

                        if ($indicatir) {

                            $class = $indicatir->class->class;

                            $classQuery = new $class(organisation_id: $assignedTarget->organisation_id, target_year_id: $model->id); // indicator helper classes in helpers
                            $totals = $classQuery->getDisaggregations()['Total'];
                            if ($assignedTarget->type == 'number') {

                                AssignedTarget::find($assignedTarget->id)->update([
                                    'current_value' => $totals
                                ]);
                                $currentValue = $totals;
                            }


                        }
                        $html .= '
                            <li class="list-group-item">' . $currentValue . '</li>  
                      ';

                    }
                    $html .= '  </ul>';
                    return $html;
                }
            })


            ->add('target_value', function ($model) {
                $Indicator_target = $model->indicatorTargets;

                if ($Indicator_target->isNotEmpty() && $Indicator_target->first()->assignedTargets->isNotEmpty()) {

                    $assignedTargets = $Indicator_target->first()->assignedTargets;
                    $html = '<ul class="list-group list-group-flush p-0">';

                    foreach ($assignedTargets as $assignedTarget) {
                        $html .= '
                            <li class="list-group-item">' . $assignedTarget->target_value . '</li>  
                      ';

                    }
                    $html .= '  </ul>';
                    return $html;
                }
            })

            ->add('progress', function ($model) {
                $Indicator_target = $model->indicatorTargets;

                if ($Indicator_target->isNotEmpty() && $Indicator_target->first()->assignedTargets->isNotEmpty()) {

                    $assignedTargets = $Indicator_target->first()->assignedTargets;
                    $html = '<ul class="list-group list-group-flush p-0">';

                    foreach ($assignedTargets as $assignedTarget) {
                        $assigned = AssignedTarget::find($assignedTarget->id);
                        $current = $assigned->current_value;
                        $target = $assigned->target_value;
                        $progress = $target == 0 ? 0 : ($current / $target) * 100;
                        $progress = floor($progress) >= 100 ? 100 : floor($progress);

                        $progressData = '<span class="text-primary fw-bold">' . floor($progress) . '%</span>';
                        $html .= '
                            <li class="list-group-item">' . $progressData . '</li>  
                      ';

                    }
                    $html .= '  </ul>';
                    return $html;
                }
            })
            ->add('start_date_formatted', fn($model) => Carbon::parse($model->start_date)->format('d/m/Y'))
            ->add('end_date_formatted', fn($model) => Carbon::parse($model->end_date)->format('d/m/Y'))
            ->add('project', function ($model) {
                return $model->project->name;
            })
            ->add('created_at')
            ->add('updated_at');
    }



    public function columns(): array
    {
        return [
            Column::make('Project', 'project'),
            Column::make('Project Year', 'number')
                ->sortable()
                ->searchable(),

            Column::make('Indicator Target', 'indicator_target')
                ->sortable()
                ->searchable(),
            Column::make('Assigned partners', 'organisations')
                ->sortable()
                ->searchable()->bodyAttribute('p-0'),

            Column::make('Current value', 'current_value')
                ->sortable()
                ->searchable()->bodyAttribute('p-0'),

            Column::make('Target value', 'target_value')
                ->sortable()
                ->searchable()->bodyAttribute('p-0'),

            Column::make('Progress', 'progress')
                ->sortable()
                ->searchable()->bodyAttribute('p-0'),

            // Column::make('Start date', 'start_date_formatted', 'start_date')
            //     ->sortable(),

            // Column::make('End date', 'end_date_formatted', 'end_date')
            //     ->sortable(),




        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('start_date'),
            Filter::datepicker('end_date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
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
