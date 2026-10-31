<?php

namespace App\Livewire\admin;

use App\Models\ReportingPeriod;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ReportingPeriodTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //   $this->showCheckBox();

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),

                 Detail::make()
                ->view('components.powergrid-components.reporting-period-details')
        ];
    }

    public function datasource(): Builder
    {
        return ReportingPeriod::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
             Column::action('')
               ,
            Column::make('#', 'id'),
            Column::make('Name', 'name')
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

    public function actions($row): array
    {
        return [
     Button::add('detail')
                ->slot('<i class="bx bx-show"></i>')
                ->class('btn btn-warning btn-sm custom-tooltip')
                ->tooltip('View Details')
                ->toggleDetail($row),
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
