<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\SchoolRtcConsumption;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
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

final class SchoolConsumptionTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return SchoolRtcConsumption::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('school_name', function ($model) {
                $data = json_decode($model->location_data);
                return $data->school_name;
            })
            ->add('district', function ($model) {
                $data = json_decode($model->location_data);
                return $data->district;
            })
            ->add('epa', function ($model) {
                $data = json_decode($model->location_data);

                return $data->epa;
            })
            ->add('section', function ($model) {
                $data = json_decode($model->location_data);
                return $data->section;
            })
            ->add('date_formatted', fn($model) => Carbon::parse($model->date)->format('d/m/Y'))
            ->add('crop')
            ->add('male_count')
            ->add('female_count')
            ->add('total')
            ->add('uuid')
            ->add('user_id')
            ->add('created_at')
            ->add('updated_at');
    }

    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }
    #[On('export')]
    public function export()
    {

        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/school_consumption.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'School Name',
                'District',
                'EPA',
                'Section',
                'Date (Formatted)',
                'Crop',
                'Male Count',
                'Female Count',
                'Total',
                'UUID',
                'User ID',

            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $location_data = json_decode($item->location_data);

                $row = [
                    'id' => $item->id,
                    'school_name' => $location_data->school_name ?? null,
                    'district' => $location_data->district ?? null,
                    'epa' => $location_data->epa ?? null,
                    'section' => $location_data->section ?? null,
                    'date_formatted' => Carbon::parse($item->date)->format('d/m/Y'),
                    'crop' => $item->crop,
                    'male_count' => $item->male_count,
                    'female_count' => $item->female_count,
                    'total' => $item->total,
                    'uuid' => $item->uuid,
                    'user_id' => $item->user_id,

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
            Column::make('School Name', 'school_name', 'location_data->school_name')->sortable(),
            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa', 'location_data->epa')->sortable(),
            Column::make('Section', 'section', 'location_data->section')->sortable(),

            Column::make('Date', 'date_formatted', 'date')
                ->sortable(),

            Column::make('Crop', 'crop')
                ->sortable()
                ->searchable(),

            Column::make('Male count', 'male_count')
                ->sortable()
                ->searchable(),

            Column::make('Female count', 'female_count')
                ->sortable()
                ->searchable(),

            Column::make('Total', 'total')
                ->sortable()
                ->searchable(),

            Column::make('Uuid', 'uuid')
                ->sortable()
                ->searchable(),




        ];
    }

    public function filters(): array
    {
        return [
            //    Filter::datepicker('date'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

}
