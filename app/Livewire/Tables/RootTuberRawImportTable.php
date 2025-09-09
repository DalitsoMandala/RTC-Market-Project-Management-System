<?php

namespace App\Livewire\tables;

use Illuminate\Database\Query\Builder;
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

final class RootTuberRawImportTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('raw_tuber_imports');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('uuid')
            ->add('entry_border')
            ->add('reg_date_formatted', fn ($model) => Carbon::parse($model->reg_date)->format('d/m/Y'))
            ->add('tpin')
            ->add('importer_name')
            ->add('year')
            ->add('hs_code')
            ->add('tariff_description')
            ->add('commercial_description')
            ->add('package_kind')
            ->add('packages')
            ->add('origin')
            ->add('exporter')
            ->add('netweight_kgs')
            ->add('foreign_currency')
            ->add('currency')
            ->add('exchange_rate')
            ->add('value_for_duty_mwk')
            ->add('user_id')
            ->add('organisation_id')
            ->add('description')
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),

            Column::make('Entry border', 'entry_border')
                ->sortable()
                ->searchable(),

            Column::make('Reg date', 'reg_date_formatted', 'reg_date')
                ->sortable(),

            Column::make('Tpin', 'tpin')
                ->sortable()
                ->searchable(),

            Column::make('Importer name', 'importer_name')
                ->sortable()
                ->searchable(),

            Column::make('Year', 'year')
                ->sortable()
                ->searchable(),

            Column::make('Hs code', 'hs_code')
                ->sortable()
                ->searchable(),

            Column::make('Tariff description', 'tariff_description')
                ->sortable()
                ->searchable(),

            Column::make('Commercial description', 'commercial_description')
                ->sortable()
                ->searchable(),

            Column::make('Package kind', 'package_kind')
                ->sortable()
                ->searchable(),

            Column::make('Packages', 'packages')
                ->sortable()
                ->searchable(),

            Column::make('Origin', 'origin')
                ->sortable()
                ->searchable(),

            Column::make('Exporter', 'exporter')
                ->sortable()
                ->searchable(),

            Column::make('Netweight kgs', 'netweight_kgs')
                ->sortable()
                ->searchable(),

            Column::make('Foreign currency', 'foreign_currency')
                ->sortable()
                ->searchable(),

            Column::make('Currency', 'currency')
                ->sortable()
                ->searchable(),

            Column::make('Exchange rate', 'exchange_rate')
                ->sortable()
                ->searchable(),

            Column::make('Value for duty mwk', 'value_for_duty_mwk')
                ->sortable()
                ->searchable(),

            Column::make('User id', 'user_id'),
            Column::make('Organisation id', 'organisation_id'),
            Column::make('Description', 'description')
                ->sortable()
                ->searchable(),

            Column::make('Status', 'status')
                ->sortable()
                ->searchable(),

            Column::make('Created at', 'created_at_formatted', 'created_at')
                ->sortable(),

            Column::make('Created at', 'created_at')
                ->sortable()
                ->searchable(),

            Column::make('Updated at', 'updated_at_formatted', 'updated_at')
                ->sortable(),

            Column::make('Updated at', 'updated_at')
                ->sortable()
                ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
            Filter::datepicker('reg_date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert('.$rowId.')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: '.$row->id)
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
