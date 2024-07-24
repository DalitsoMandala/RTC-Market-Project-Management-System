<?php

namespace App\Livewire\tables;

use App\Models\Indicator;
use App\Models\IndicatorTarget;
use App\Models\TargetDetail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class IndicatorTargetsTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

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
        return IndicatorTarget::query()->with('details');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator', fn($model) => Indicator::find($model->indicator_id)->indicator_name)
            ->add('target_total', function ($model) {

                if ($model->target_value === null) {
                    $details = $model->details;
                    if ($details) {
                        $arr = $details->select([
                            'name',
                            'target_value',
                            'type',
                        ]);
                        $imploded = [];
                        foreach ($arr as $sep) {
                            if ($sep['type'] == 'percentage') {
                                $sep['type'] = '%';
                            } else if ($sep['type'] == 'number') {
                                $sep['type'] = '';
                            }

                            $imploded[] = $sep['name'] . '-' . $sep['target_value'] . '' . $sep['type'];

                        }
                        return implode(', ', $imploded);
                    }


                }
                if ($model->type == 'percentage') {
                    $model->type = '%';
                } else if ($model->type == 'number') {
                    $model->type = '';
                }

                return $model->target_value . '' . $model->type;

            })

            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator ', 'indicator'),
            Column::make('Target', 'target_total'),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id]),
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
