<?php

namespace App\Livewire\tables;

use Illuminate\Database\Query\Builder;
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

final class JobProgressTable extends PowerGridComponent
{
    use WithExport;

    public $userId;

    public function setUp(): array
    {


        return [

            Header::make(),
            Footer::make()
                ->showPerPage()->pageName('pending-submissions')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('job_progress')->where('user_id', $this->userId);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('cache_key')
            ->add('form_name')
            ->add('user_id')
            ->add('status', function ($model) {
                if ($model->status === 'completed') {
                    return '<span class="badge bg-success">' . $model->status . '</span>';
                } else if ($model->status === 'failed') {
                    return '<span class="badge bg-theme-red">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-warning">' . $model->status . '</span>';
                }
            })
            ->add('progress', function ($model) {
                return '
<div class="mb-1 text-center text-warning fw-medium">' . $model->progress . '%</div>


';
            })
            ->add('is_finished', function ($model) {

                if ($model->status == 'completed') {
                    return '<i class="bx bx-check fs-2 text-success"></i>';
                } else if ($model->status = 'failed') {
                    return '<i class="bx bx-x fs-2 text-danger"></i>';
                }
            })
            ->add('error')
            ->add('created_at', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y h:i A'))
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('UUID', 'cache_key')

                ->searchable(),

            Column::make('Form name', 'form_name')

                ->searchable(),


            Column::make('Status', 'status')

                ->searchable(),

            Column::make('Progress', 'progress')

                ->searchable(),




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
