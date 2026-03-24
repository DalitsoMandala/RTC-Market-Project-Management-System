<?php

namespace App\Livewire\tables;

use App\Models\GrossMarginData;
use App\Models\GrossMarginItemValue;
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
        return GrossMarginItemValue::query()->with(['grossMargin','categoryItem'])
        ->where('gross_margin_id', $this->row->id)
        ->join('gross_margins', function ($join) {
            $join->on('gross_margins.id', '=', 'gross_margin_item_values.gross_margin_id');

        })
        ->join('gross_margin_category_items', function ($join) {
            $join->on('gross_margin_category_items.id', '=', 'gross_margin_item_values.gross_margin_category_item_id');
        })
        ->select([
            'gross_margin_item_values.*',
            'gross_margins.name as gross_margin_name',
            'gross_margin_category_items.item_name as item_name',
            'gross_margin_category_items.unit as unit',

            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('gross_margin_name')
            ->add('item_name')
            ->add('unit')
            ->add('qty')
            ->add('unit_price')
            ->add('total')
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn'),
Column::make('Name of Producer', 'gross_margin_name','gross_margins.name'),
            Column::make('Item name', 'item_name')
                ->sortable()
                ->searchable(),

            Column::make('Unit', 'unit')
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
