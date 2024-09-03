<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\RtcProductionFarmer;
use Illuminate\Support\Carbon;

use Illuminate\Support\Collection;

use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmFarmerCultivation extends PowerGridComponent
{
    public function datasource(): Builder
    {


        return RtcProductionFarmer::query()->whereNotNull('area_under_cultivation');

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
            ->add('area', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation, true);
                $imploded = [];
                foreach ($area_under_cultivation as $values) {

                    foreach ($values as $key => $value) {
                        if ($key == 'area') {

                            $imploded[] = $value . ' acres';
                        }

                    }

                }

                return implode(', ', $imploded);

            })

            ->add('variety', function ($model) {
                $area_under_cultivation = json_decode($model->area_under_cultivation, true);
                $imploded = [];
                foreach ($area_under_cultivation as $values) {

                    foreach ($values as $key => $value) {
                        if ($key == 'variety') {

                            $imploded[] = $value;
                        }

                    }

                }

                return implode(', ', $imploded);

            });

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
