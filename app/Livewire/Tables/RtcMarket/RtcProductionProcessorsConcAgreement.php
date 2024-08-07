<?php

namespace App\Livewire\tables\RtcMarket;

use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionProcessor;
use App\Models\RpmProcessorConcAgreement;
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

final class RtcProductionProcessorsConcAgreement extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {


        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data-processors-conc'),
            Footer::make()
                ->showPerPage()
                ->pageName('processors-contractual-agreement')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return RpmProcessorConcAgreement::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rpm_processor_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_processor_id;
                $row = RtcProductionProcessor::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })
            ->add('date_recorded_formatted', fn($model) => Carbon::parse($model->date_recorded)->format('d/m/Y'))
            ->add('partner_name')
            ->add('country')
            ->add('date_of_maximum_sale_formatted', fn($model) => Carbon::parse($model->date_of_maximum_sale)->format('d/m/Y'))
            ->add('product_type')
            ->add('volume_sold_previous_period')
            ->add('financial_value_of_sales')
            ->add('created_at')
            ->add('updated_at');
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }
    #[On('export-conc')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/rtc_production_and_marketing_processors-contractual-agreement.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Processor ID',
                'Actor Name',
                'Date Recorded (Formatted)',
                'Partner Name',
                'Country',
                'Date of Maximum Sale (Formatted)',
                'Product Type',
                'Volume Sold Previous Period',
                'Financial Value of Sales',

            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $processor = $item->rpm_processor_id;
                $row = RtcProductionProcessor::find($processor);
                $actor_name = $row ? $row->name_of_actor : null;

                $row = [
                    'id' => $item->id,
                    'rpm_processor_id' => $item->rpm_processor_id,
                    'actor_name' => $actor_name,
                    'date_recorded_formatted' => Carbon::parse($item->date_recorded)->format('d/m/Y'),
                    'partner_name' => $item->partner_name,
                    'country' => $item->country,
                    'date_of_maximum_sale_formatted' => Carbon::parse($item->date_of_maximum_sale)->format('d/m/Y'),
                    'product_type' => $item->product_type,
                    'volume_sold_previous_period' => $item->volume_sold_previous_period,
                    'financial_value_of_sales' => $item->financial_value_of_sales,

                ];

                $writer->addRow($row);
            }
        }

        // Close the writer and get the path of the file
        $writer->close();

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }


    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Actor Name', 'actor_name'),
            Column::make('Actor id', 'rpm_processor_id'),
            Column::make('Date recorded', 'date_recorded_formatted', 'date_recorded')
                ->sortable(),

            Column::make('Partner name', 'partner_name')
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
            // Filter::datepicker('date_recorded'),
            // Filter::datepicker('date_of_maximum_sale'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
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
