<?php

namespace App\Livewire\tables;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IndicatorDetailTable extends PowerGridComponent
{
    use WithExport;
    public $populatedData = array();
    public $name;
    public function datasource(): ?Collection
    {

        $data = $this->populatedData;

        $count = 1;
        $newData = collect();

        foreach ($data as $key => $value) {
            $newData->push([
                'id' => $count,
                'name' => $key,
                'value' => $value,
            ]);

            $count++;
        }




        return $newData;
    }

    public function setUp(): array
    {


        return [
            Exportable::make($this->name)
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage(20)
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('value')
        ;
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name', 'name')
                ->searchable()
                ->sortable(),

            Column::make('Value', 'value')
                ->searchable()
                ->sortable(),


        ];
    }
}