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
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make(),
            Footer::make()

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
            ->add('id')
            ->add('number')
            ->add('indicator_target', function ($model) {
                $indicatorTarget = $model->indicatorTargets;

                if ($indicatorTarget->isNotEmpty()) {
                    $target = $indicatorTarget->first();

                    // Simple null check
                    if ($target && $target->target_value !== null) {
                        $type = $target->type == 'percentage' ? '%' : '';
                        $value = $target->target_value . $type;

                        // Check if details exist and process them
                        if (!empty($target->details)) {
                            $details = collect($target->details);

                            if ($details->count() > 1) {
                                $detailValues = $details->map(function ($detail) {
                                    if ($detail) {
                                        $detailType = $detail->type == 'percentage' ? '%' : '';
                                        return $detail->target_value . $detailType . ' (' . $detail->name . ')';
                                    }
                                    return null;
                                })->filter()->implode(', ');

                                $value .= ', ' . $detailValues;
                            } else {
                                $singleDetail = $details->first();
                                if ($singleDetail) {
                                    $detailType = $singleDetail->type == 'percentage' ? '%' : '';
                                    $value .= ' ' . $singleDetail->target_value . $detailType . ' (' . $singleDetail->name . ')';
                                }
                            }
                        }

                        return $value;
                    }
                }

                return null;
            })



            ->add('organisations', function ($model) {
                $indicatorTarget = $model->indicatorTargets;

                if ($indicatorTarget->isNotEmpty()) {
                    $assignedTargets = $indicatorTarget->first()->assignedTargets;

                    if ($assignedTargets->isNotEmpty()) {
                        $html = '<ul class="list-group   rounded-0">';

                        foreach ($assignedTargets as $assignedTarget) {
                            $organisationName = $assignedTarget->organisation->name ?? 'Unknown';
                            $html .= '<li class="list-group-item text-nowrap">' . $organisationName . '</li>';
                        }

                        $html .= '</ul>';
                        return $html;
                    }
                }

                return null;
            })

            ->add('target_value', function ($model) {
                $indicatorTarget = $model->indicatorTargets;

                if ($indicatorTarget->isNotEmpty()) {
                    $assignedTargets = $indicatorTarget->first()->assignedTargets;

                    if ($assignedTargets->isNotEmpty()) {
                        $html = '<ul class="list-group rounded-0">';

                        foreach ($assignedTargets as $assignedTarget) {
                            $indicator = Indicator::find($this->indicator_id);
                            $currentValues = [];
                            $currentValue = 0;

                            if ($indicator) {
                                $class = $indicator->class->class;

                                if (class_exists($class)) {
                                    $classQuery = new $class(organisation_id: $assignedTarget->organisation_id, target_year_id: $model->id);
                                    $data = $classQuery->getDisaggregations();

                                    if ($assignedTarget->type == 'number' || $assignedTarget->type == 'percentage') {
                                        $type = $assignedTarget->type == 'percentage' ? '%' : '';
                                        $html .= '<li class="list-group-item text-nowrap">' . $assignedTarget->target_value . $type . '</li>';
                                    } else {
                                        $details = json_decode($assignedTarget->detail, true);

                                        foreach ($details as $detail) {
                                            $detailType = $detail['type'] == 'percentage' ? '%' : '';
                                            $currentValues[] = $detail['name'] . ': ' . $detail['target_value'] . $detailType;
                                        }
                                        $html .= '<li class="list-group-item text-nowrap">' . implode(', ', $currentValues) . '</li>';
                                    }
                                }
                            }
                        }

                        $html .= '</ul>';
                        return $html;
                    }
                }

                return null;
            })

            ->add('current_value', function ($model) {
                $indicatorTarget = $model->indicatorTargets;

                if ($indicatorTarget->isNotEmpty()) {
                    $assignedTargets = $indicatorTarget->first()->assignedTargets;

                    if ($assignedTargets->isNotEmpty()) {
                        $html = '<ul class="list-group rounded-0">';

                        foreach ($assignedTargets as $assignedTarget) {
                            $indicator = Indicator::find($this->indicator_id);
                            $currentValues = [];
                            $currentValue = 0;

                            if ($indicator) {
                                $class = $indicator->class->class;

                                if (class_exists($class)) {
                                    $classQuery = new $class(organisation_id: $assignedTarget->organisation_id, target_year_id: $model->id);
                                    $data = $classQuery->getDisaggregations();

                                    if ($assignedTarget->type == 'number' || $assignedTarget->type == 'percentage') {


                                        if ($assignedTarget->type == 'percentage') {
                                            $baselineValue = $indicatorTarget->first()->baseline_value;
                                            $annualValue = $data['Total'] ?? 0;
                                            $calculation = ($annualValue - $baselineValue) * 100 / $baselineValue;




                                        }




                                        $assignedTarget->update(['current_value' => $data['Total'] ?? 0]);
                                        $currentValue = $data['Total'] ?? 0;
                                        $html .= '<li class="list-group-item text-nowrap">' . $currentValue . '</li>';




                                    } else {
                                        $details = json_decode($assignedTarget->detail, true);
                                        $data = $classQuery->getDisaggregations();
                                        $DbArray = [];
                                        foreach ($details as $detail) {
                                            $detailType = $detail['type'] == 'percentage' ? '%' : '';
                                            foreach ($data as $name => $value) {

                                                if ($name == $detail['name']) {
                                                    $detail['current_value'] = $value;

                                                }
                                            }

                                            $DbArray[] = $detail;

                                            //$assignedTarget->update(['detail' => json_encode($details)]);
    
                                            $currentValues[] = $detail['name'] . ': ' . $detail['current_value'] . $detailType;


                                        }
                                        $assignedTarget->update(['detail' => json_encode($DbArray)]);



                                        $html .= '<li class="list-group-item text-nowrap">' . implode(', ', $currentValues) . '</li>';
                                    }
                                }
                            }
                        }

                        $html .= '</ul>';
                        return $html;
                    }
                }

                return null;
            })

            ->add('progress', function ($model) {
                $indicatorTarget = $model->indicatorTargets;

                if ($indicatorTarget->isNotEmpty()) {
                    $assignedTargets = $indicatorTarget->first()->assignedTargets;

                    if ($assignedTargets->isNotEmpty()) {
                        $html = '<ul class="list-group rounded-0">';

                        foreach ($assignedTargets as $assignedTarget) {

                            if ($assignedTarget->type == 'number' || $assignedTarget->type == 'percentage') {
                                $current = $assignedTarget->current_value ?? 0;
                                $target = $assignedTarget->target_value ?? 0;
                                $progress = $target == 0 ? 0 : ($current / $target) * 100;
                                $progress = min(100, floor($progress));

                                $progressData = '<span class="text-primary fw-bold">' . $progress . '%</span>';
                                $html .= '<li class="list-group-item text-nowrap">' . $progressData . '</li>';
                            } else {
                                $details = json_decode($assignedTarget->detail);
                                $progressForEachDetail = [];
                                foreach ($details as $detail) {
                                    $current = $detail->current_value ?? 0;
                                    $target = $detail->target_value ?? 0;
                                    $progress = $target == 0 ? 0 : ($current / $target) * 100;
                                    $progress = min(100, floor($progress));
                                    $progressForEachDetail[] = $detail->name . ': <span class="text-primary fw-bold">' . $progress . '%</span>';
                                    ;

                                }
                                $html .= '<li class="list-group-item text-nowrap ">' . implode(', ', $progressForEachDetail) . '</li>';

                            }
                        }



                        $html .= '</ul>';
                        return $html;
                    }
                }

                return null;
            })


            ->add('start_date_formatted', fn($model) => Carbon::parse($model->start_date)->format('d/m/Y'))
            ->add('end_date_formatted', fn($model) => Carbon::parse($model->end_date)->format('d/m/Y'))
            ->add('project', fn($model) => $model->project->name ?? 'Unknown Project')
            ->add('created_at')
            ->add('updated_at');
    }

    public function makeCalculations()
    {


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



            Column::make('Target value', 'target_value')
                ->sortable()
                ->searchable()->bodyAttribute('p-0'),

            Column::make('Current value', 'current_value')
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
