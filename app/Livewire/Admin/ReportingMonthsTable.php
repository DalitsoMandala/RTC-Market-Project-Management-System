<?php

namespace App\Livewire\admin;

use App\Models\ReportingPeriodMonth;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ReportingMonthsTable extends PowerGridComponent
{
    use WithExport;
    public $reportingPeriodId;

    public function setUp(): array
    {
        //     $this->showCheckBox();

        return [

            Header::make(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),




        ];
    }

    public function datasource(): Builder
    {

        return ReportingPeriodMonth::query()->with('reportingPeriod')->where('reporting_period_id', $this->reportingPeriodId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('start_month')
            ->add('end_month')
            ->add('reporting_period_month_id')
            ->add('period', fn($model) => $model->reportingPeriod->name)
            ->add('type')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [

            Column::make('#', 'id'),
            Column::make('Start month', 'start_month')
                ->sortable()
                ->searchable(),

            Column::make('End month', 'end_month')
                ->sortable()
                ->searchable(),

            Column::make('Period', 'period'),

            Column::make('Period Type', 'type'),

        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [

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
