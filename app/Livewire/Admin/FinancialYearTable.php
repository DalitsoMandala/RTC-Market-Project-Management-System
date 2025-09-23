<?php

namespace App\Livewire\admin;

use Livewire\Attributes\On;
use App\Models\FinancialYear;
use App\Traits\UITrait;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class FinancialYearTable extends PowerGridComponent
{
    use WithExport;
    use UITrait;
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
        return FinancialYear::query()->with('project');
    }


    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('number')
            ->add('start_date_formatted', fn($model) => Carbon::parse($model->start_date)->format('d/m/Y'))
            ->add('end_date_formatted', fn($model) => Carbon::parse($model->end_date)->format('d/m/Y'))
            ->add('project_id')
            ->add('project', fn($model) => $model->project->name)
            ->add('status', function ($model) {
                if ($model->status == 'active') {
                    return "<span class='badge bg-success-subtle text-success'>{$model->status}</span>";
                } else {

                    return "<span class='badge bg-secondary-subtle text-secondary'>{$model->status}</span>";
                }
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Year', 'number')
                ->sortable()
                ->searchable(),

            Column::make('Start date', 'start_date_formatted', 'start_date')
                ->sortable(),

            Column::make('End date', 'end_date_formatted', 'end_date')
                ->sortable(),

            Column::make('Project', 'project'),
            Column::make('Status', 'status'),

            Column::action('')

        ];
    }


    #[On('set-active')]
    public function checkYear($rowId)
    {
        $row =  FinancialYear::find($rowId)?->update([
            'status' => 'active'
        ]);


        if ($row) {
            FinancialYear::where('id', '!=', $rowId)->update([

                'status' => 'inactive'
            ]);
        }
        $this->dispatch('show-alert', data: [
            'type' => 'success',  // success, error, info, warning
            'message' => 'Successfully updated.'
        ]);
    }
    public function actions($row): array
    {
        return [
            Button::add('Active')
                ->slot('<i class="bx bx-check " title="Make active"></i>')
                ->id()
                ->tooltip('Make active')
                ->class('btn btn-sm btn-success custom-tooltip')
                ->dispatch('set-active', ['rowId' => $row->id]),
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
}
