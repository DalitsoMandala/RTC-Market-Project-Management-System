<?php

namespace App\Livewire\tables;

use App\Models\Baseline;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use Illuminate\Support\Facades\Blade;

final class BaselineTable extends PowerGridComponent
{
    use WithExport;
    public bool $showErrorBag = true;
public $baseline_value;
    public function setUp(): array
    {
        // $this->showCheckBox();

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
        return Baseline::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_id')
            ->add('indicator', function ($model) {
                return $model->indicator->indicator_name;
            })
            ->add('baseline_value', function ($model) {
             return Blade::render("<div>$model->baseline_value</div>");
            })

            ;
    }

    public function rules()
    {
        return [
            'baseline_value.*' => ['required', 'numeric','min:0'],
        ];
    }
    protected function validationAttributes()
    {
        return [
            'baseline_value.*'       => 'Baseline value',

        ];
    }


    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {

        $this->validate();



    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator'),
            Column::make('Baseline value', 'baseline_value')
                ->sortable()
                ->searchable()
                ->editOnClick(
                            hasPermission: auth()->check(),
                    fallback: '- empty -'
                ),

        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function edit(): void {}


    // public function actions($row): array{
    //     return [
    //         Button::add('edit')
    //         ->slot('<i class="bx bx-pen"></i> Edit')
    //         ->id()
    //         ->tooltip('Edit Record')
    //         ->class('btn btn-warning goUp btn-sm my-1')
    //         ->dispatch('editData', ['rowId' => $row->id]),


    //     ];
    // }

    /*
     * public function actionRules($row): array
     * {
     *    return [
     *         // Hide button edit for ID 1
     *         Rule::button('edit')
     *             ->when(fn($row) => $row->id === 1)
     *             ->hide(),
     *     ];
     * }
     */
}