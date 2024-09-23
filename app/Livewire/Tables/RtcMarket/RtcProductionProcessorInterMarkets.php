<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorInterMarket;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RtcProductionProcessorInterMarkets extends PowerGridComponent
{
    use WithExport;
    use \App\Traits\ExportTrait;
    public bool $deferLoading = false;
    public function setUp(): array
    {


        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(5)
                ->pageName('international-markets')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return RpmProcessorInterMarket::query()->with('processors');
    }


    public $namedExport = 'rpmpIM';
    #[On('export-rpmpIM')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => $model->processors->pp_id)->add('rpm_processor_id')

            ->add('actor_name', function ($model) {
                $processor = $model->rpm_processor_id;
                $row = RtcProductionProcessor::find($processor);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })
            ->add('date_recorded_formatted', fn($model) => Carbon::parse($model->date_recorded)->format('d/m/Y'))
            ->add('crop_type')
            ->add('market_name')
            ->add('country')
            ->add('date_of_maximum_sale_formatted', fn($model) => Carbon::parse($model->date_of_maximum_sale)->format('d/m/Y'))
            ->add('product_type')
            ->add('volume_sold_previous_period')
            ->add('financial_value_of_sales')
            ->add('created_at')
            ->add('submitted_by', function ($model) {
                $user = User::find($model->processors->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })
            ->add('updated_at');
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }


    public function columns(): array
    {
        return [
            Column::make('id', 'id')->sortable(),
            Column::make('Actor ID', 'unique_id')->searchable(),
            Column::make('Actor Name', 'actor_name'),

            Column::make('Date recorded', 'date_recorded_formatted', 'date_recorded')
                ->sortable(),

            Column::make('Crop type', 'crop_type')
                ->sortable()
                ->searchable(),

            Column::make('Market name', 'market_name')
                ->sortable()
                ->searchable(),

            Column::make('Country', 'country')
                ->sortable()
                ->searchable(),

            Column::make('Date of maximum sale', 'date_of_maximum_sale_formatted', 'date_of_maximum_sale')
                ->sortable(),

            Column::make('Product type', 'product_type')
                ->sortable()
                ->searchable(),

            Column::make('Volume sold previous period', 'volume_sold_previous_period')
                ->sortable()
                ->searchable(),

            Column::make('Financial value of sales', 'financial_value_of_sales')
                ->sortable()
                ->searchable(),


            // Column::make('Submitted by', 'submitted_by')
            //     ->searchable(),


        ];
    }

    public function relationSearch(): array
    {
        return [

            'user' => [
                'name',

            ],

            'user.organisation' => [
                'name'
            ]

        ];
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: ' . $row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id]),
    //     ];
    // }

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
