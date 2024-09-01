<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\RtcProductionProcessor;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorAggCenters extends PowerGridComponent
{
    public function datasource(): Collection
    {
        // Initialize the changedArray as an empty collection
        $changedArray = collect();

        // Use the query builder to fetch data lazily and transform it directly
        RtcProductionProcessor::query()
            ->where('sells_to_aggregation_centers', 1)
            ->lazy() // Lazy loading for memory efficiency
            ->each(function ($item) use (&$changedArray) {
                $names = json_decode($item->aggregation_centers) ?? [];

                foreach ($names as $name_data) {
                    $changedArray->push([
                        'id' => $item->id,
                        'name_of_actor' => $item->name_of_actor,
                        'name' => $name_data->name,
                    ]);
                }
            });

        return $changedArray;
    }

    public function setUp(): array
    {


        return [

            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('name_of_actor')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })


            ->add('name', function ($model) {
                return $model->name;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'unique_id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
                ->sortable(),

            Column::make('Name of Aggregation centers', 'name')
                ->searchable()
                ->sortable(),
        ];
    }
}
