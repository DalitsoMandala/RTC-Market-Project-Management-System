<?php

namespace App\Livewire\tables;

use App\Models\MarketData;
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

final class MarketDataTable extends PowerGridComponent
{
    use WithExport;

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
        return MarketData::query()->select([
            'marketing_data.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

            ->add('entry_date_formatted', fn($model) => Carbon::parse($model->entry_date)->format('d/m/Y'))
            ->add('off_taker_name_vehicle_reg_number')

            ->add('trader_contact')
            ->add('buyer_location')
            ->add('variety_demanded')
            ->add('quality_size')
            ->add('quantity')
            ->add('units')
            ->add('estimated_demand_kg')
            ->add('agreed_price_per_kg')
            ->add('market_ordered_from')
            ->add('final_market')
            ->add('final_market_district')
            ->add('final_market_country')
            ->add('supply_frequency')
            ->add('estimated_total_value_mwk')
            ->add('estimated_total_value_usd')
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn'),

            Column::make('Entry date', 'entry_date_formatted', 'entry_date')
                ->sortable(),

            Column::make('Off taker name', 'off_taker_name_vehicle_reg_number')
                ->sortable()
                ->searchable(),

            Column::make('Trader contact', 'trader_contact')
                ->sortable()
                ->searchable(),

            Column::make('Buyer location', 'buyer_location')
                ->sortable()
                ->searchable(),

            Column::make('Variety demanded', 'variety_demanded')
                ->sortable()
                ->searchable(),

            Column::make('Quality size', 'quality_size')
                ->sortable()
                ->searchable(),

            Column::make('Quantity', 'quantity')
                ->sortable()
                ->searchable(),

            Column::make('Units', 'units')
                ->sortable()
                ->searchable(),

            Column::make('Estimated demand kg', 'estimated_demand_kg')
                ->sortable()
                ->searchable(),

            Column::make('Agreed price per kg', 'agreed_price_per_kg')
                ->sortable()
                ->searchable(),

            Column::make('Market ordered from', 'market_ordered_from')
                ->sortable()
                ->searchable(),

            Column::make('Final market', 'final_market')
                ->sortable()
                ->searchable(),

            Column::make('Final market district', 'final_market_district')
                ->sortable()
                ->searchable(),

            Column::make('Final market country', 'final_market_country')
                ->sortable()
                ->searchable(),

            Column::make('Supply frequency', 'supply_frequency')
                ->sortable()
                ->searchable(),

            Column::make('Estimated total value mk', 'estimated_total_value_mwk')
                ->sortable()
                ->searchable(),

            Column::make('Estimated total value usd', 'estimated_total_value_usd')
                ->sortable()
                ->searchable(),



            Column::action('')

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

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
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
