<?php

namespace App\Livewire\Tables;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class IndicatorDetailTable extends PowerGridComponent
{
    public string $tableName = 'IndicatorDetailTable';
    use WithExport;
    public $populatedData = array();
    public $name;
    public $excludeKeys = [];
    public function datasource(): ?Collection
    {


        $data = $this->populatedData;

        $count = 1;
        $newData = collect();

        foreach ($data as $key => $value) {
            if (in_array($key, $this->excludeKeys)) {
                continue;
            }
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
           PowerGrid::exportable($this->name)
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
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
            Column::make('#', 'id')
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
