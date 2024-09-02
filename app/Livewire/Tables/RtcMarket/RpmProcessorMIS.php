<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorMIS extends PowerGridComponent
{

    public function datasource(): Collection
    {
        // Initialize the changedArray as an empty collection
        $changedArray = collect();

        // Use the query builder to fetch data lazily and transform it directly
        RtcProductionProcessor::query()
            ->where('uses_market_information_systems', 1)
            ->lazy() // Lazy loading for memory efficiency
            ->each(function ($item) use (&$changedArray) {
                $names = json_decode($item->market_information_systems) ?? [];

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
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => str_pad($model->rpm_processor_id, 5, '0', STR_PAD_LEFT))
            ->add('rpm_processor_id')
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
            Column::make('Processor ID', 'unique_id', 'id')
                ->searchable()
                ->sortable(),


            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
                ->sortable(),

            Column::make('Name of Market information System', 'name')
                ->searchable()
                ->sortable(),
        ];
    }
}
