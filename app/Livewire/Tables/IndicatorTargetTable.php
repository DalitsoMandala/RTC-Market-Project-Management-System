<?php

namespace App\Livewire\tables;

use App\Models\User;
use App\Models\Indicator;
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

final class IndicatorTargetTable extends PowerGridComponent
{
    use WithExport;
    public $financialYear;
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
        $organisation_id = $user->organisation->id;


        $query = Indicator::with([
            'submissionTargets',
            'submissionTargets.organisationTargets',
            'submissionTargets.organisationTargets.organisation',
        ])->whereHas('submissionTargets', function ($q) {
            $q->where('financial_year_id', $this->financialYear);
        })->select([
            'indicators.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);

        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_no')
            ->add('indicator_name')
            ->add('year_targets', function ($model) {
                $targets = $model->submissionTargets;
                dd($targets);
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [

            Column::make('Indicator no', 'indicator_no')

                ->searchable(),

            Column::make('Indicator name', 'indicator_name')
                ->sortable()
                ->searchable(),


            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->sortable()
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
