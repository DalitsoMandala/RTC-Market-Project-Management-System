<?php

namespace App\Livewire\tables;

use App\Models\JobProgress;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class JobProgressTable extends PowerGridComponent
{
    use WithExport;

    public $userId;

    public $count = 1;

    public $batch;

    public string $sortField = 'id';

    public string $sortDirection = 'desc';

    public function setUp(): array
    {
        $route = Route::current();
        $parameters = $route->parameters();
        $collection = collect($parameters);
        if ($collection->has('batch')) {
            $this->batch = $collection->get('batch');
        }

        $getProgresses = JobProgress::where('status', 'processing')->get();
        foreach ($getProgresses as $progress) {
            $getTime  = $progress->created_at;
            $time = Carbon::parse($getTime);
            if ($time->diffInHours() >= 1) {
                $progress->status = 'failed';
                $progress->save();
            }
        }

        return [
            Header::make(),
            Footer::make()
                ->showPerPage()
                ->pageName('pending-submissions')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return JobProgress::query()
            ->where('user_id', $this->userId)
            ->select([
                '*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('row', fn($model) => $this->count++)
            ->add('id')
            ->add('cache_key', function ($model) {
                $cacheKey = $model->cache_key;

                return $cacheKey;
            })
            ->add('form_name_formatted', function ($model) {
                $form = strtolower($model->form_name);

                return "<span class='text-uppercase'>{$form}</span>";
            })
            ->add('user_id')
            ->add('status')
            ->add('status_formatted', function ($model) {
                if ($model->status === 'completed') {
                    return '<span class="badge bg-success-subtle text-success">' . $model->status . '</span>';
                } else if ($model->status === 'failed') {
                    return '<span class="badge bg-danger-subtle text-danger">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-warning-subtle text-warning">' . $model->status . '</span>';
                }
            })
            ->add('progress', function ($model) {
                $progressColor = match (true) {
                    $model->progress >= 0 && $model->progress <= 49 => 'bg-danger',
                    $model->progress >= 50 && $model->progress <= 99 => 'bg-warning',
                    $model->progress === 100 => 'bg-success',
                    default => 'bg-success',  // Fallback for unexpected values
                };

                $html = "

                <div class='d-flex justify-content-between align-items-center'>
<div class='progress progress-sm bg-secondary-subtle w-100 me-3'>
<div class='progress-bar {$progressColor}' role='progressbar' style='width: {$model->progress}%' aria-valuenow='{$model->progress}' aria-valuemin='0' aria-valuemax='100'></div>
</div>
<span class='text-muted fw-bold'>{$model->progress}%</span>
</div>
";

                return $html;
            })
            ->add('is_finished', function ($model) {
                if ($model->status == 'completed') {
                    return '<i class="bx bx-check fs-2 text-success"></i>';
                } else if ($model->status = 'failed') {
                    return '<i class="bx bx-x fs-2 text-danger"></i>';
                }
            })
            ->add('error', function ($model) {
                if ($model->error) {
                    return "<span class='text-danger'>{$model->error}</span>";
                }
                return "<span class='text-success'>No error.</span>";
            })
            ->add('updated_at_formatted', fn($model) => Carbon::parse($model->created_at)->format('d/m/Y h:i A'))
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn')->sortable(),
            Column::make('UUID', 'cache_key')
                ->searchable(),
            Column::make('Type', 'form_name_formatted', 'form_name')
                ->searchable(),
            Column::make('Status', 'status_formatted')
                ->searchable(),

            Column::make('Error', 'error')->bodyAttribute(styleAttr: 'white-space: wrap;')
                ->searchable(),
            Column::make('Progress', 'progress')->bodyAttribute(styleAttr: 'width: 200px;')
                ->searchable(),
            Column::make('Uploaded at', 'updated_at_formatted')
                ->searchable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'status')
                ->dataSource(function () {
                    $submission = JobProgress::select(['status'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('status')
                ->optionValue('status'),
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

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            Rule::rows()
                ->when(fn($row) => $row->cache_key === $this->batch)
                ->setAttribute('class', 'table-secondary'),
        ];
    }
}
