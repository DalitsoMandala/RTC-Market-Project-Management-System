<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Support\Carbon;

use Illuminate\Support\Collection;
use App\Models\RtcProductionFarmer;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmFarmerCertified extends PowerGridComponent
{
    public function datasource(): Collection
    {
        $changedArray = collect();
        $data = RtcProductionFarmer::query()->whereNotNull('area_under_certified_seed_multiplication')
            ->lazy() // Lazy loading for memory efficiency
            ->each(function ($item) use (&$changedArray) {
                $names = json_decode($item->area_under_certified_seed_multiplication) ?? [];

                foreach ($names as $name_data) {
                    $changedArray->push([
                        'id' => $item->id,
                        'name_of_actor' => $item->name_of_actor,
                        'variety' => $name_data->variety,
                        'area' => $name_data->area,
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
            ->add('area')

            ->add('variety');
    }

    public function columns(): array
    {
        return [
            Column::make('Farmer ID', 'unique_id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
                ->sortable(),

            Column::make('Variety', 'variety'),

            Column::make('Area (Number of acres)', 'area'),



        ];
    }
}
