<?php

namespace App\Livewire\tables;

use App\Models\GrossMarginData;
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

final class GrossMarginItemTable extends PowerGridComponent
{
    use WithExport;
public $row;
    public function setUp(): array
    {


        return [



        ];
    }

    public function datasource(): Builder
    {
        return GrossMarginData::query()->where('gross_margin_detail_id', $this->row->id)->select([
            'gross_margin_data.*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('gross_margin_detail_id')
            ->add('item_name')
            ->add('description')
            ->add('qty')
            ->add('unit_price')
            ->add('total')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn'),

            Column::make('Item name', 'item_name')
                ->sortable()
                ->searchable(),

            Column::make('Description', 'description')
                ->sortable()
                ->searchable(),

            Column::make('Qty', 'qty')
                ->sortable()
                ->searchable(),

            Column::make('Unit price', 'unit_price')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),



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
        $this->js('alert('.$rowId.')');
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
