<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\AttendanceRegister;
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

final class AttendanceRegisterTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data-att')->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return AttendanceRegister::query();
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('meetingTitle')
            ->add('meetingCategory')
            ->add('rtcCrop', function ($model) {
                $crops = json_decode($model->rtcCrop, true);
                return implode(', ', $crops);

            })
            ->add('venue')
            ->add('district')
            ->add('startDate_formatted', fn($model) => Carbon::parse($model->startDate)->format('d/m/Y'))
            ->add('endDate_formatted', fn($model) => Carbon::parse($model->endDate)->format('d/m/Y'))
            ->add('totalDays')
            ->add('name', function ($model) {
                return '
                <div class="d-flex">
                <img src="' . asset('assets/images/users/usr.png') . '" alt="" class="shadow avatar-sm rounded-circle me-2">
                    <span class="text-capitalize">' . strtolower($model->name) . '</span>
                    </div>';

            })
            ->add('sex')
            ->add('organization')
            ->add('designation')
            ->add('phone_number')
            ->add('email')
            ->add('created_at_formatted', function ($model) {
                return Carbon::parse($model->created_at)->format('d/m/Y');
            })
            ->add('updated_at');
    }

    #[On('export')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/attendance_register.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Meeting Title',
                'Meeting Category',
                'RTC Crop',
                'Venue',
                'District',
                'Start Date (Formatted)',
                'End Date (Formatted)',
                'Total Days',
                'Name',
                'Sex',
                'Organization',
                'Designation',
                'Phone Number',
                'Email',

            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $crops = json_decode($item->rtcCrop, true);
                $rtcCrop = implode(', ', $crops);

                $row = [
                    'id' => $item->id,
                    'meetingTitle' => $item->meetingTitle,
                    'meetingCategory' => $item->meetingCategory,
                    'rtcCrop' => $rtcCrop,
                    'venue' => $item->venue,
                    'district' => $item->district,
                    'startDate_formatted' => Carbon::parse($item->startDate)->format('d/m/Y'),
                    'endDate_formatted' => Carbon::parse($item->endDate)->format('d/m/Y'),
                    'totalDays' => $item->totalDays,
                    'name' => $item->name,
                    'sex' => $item->sex,
                    'organization' => $item->organization,
                    'designation' => $item->designation,
                    'phone_number' => $item->phone_number,
                    'email' => $item->email,

                ];

                $writer->addRow($row);
            }
        }

        // Close the writer and get the path of the file
        $writer->close();

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable(),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable(),

            Column::make('Organization', 'organization')
                ->sortable()
                ->searchable(),

            Column::make('Designation', 'designation')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Email', 'email')
                ->sortable()
                ->searchable(),

            Column::make('Date of Submission', 'created_at_formatted', 'created_at')
                ->sortable(),
            Column::make('Meeting Title', 'meetingTitle')
                ->sortable()
                ->searchable(),

            Column::make('Meeting Category', 'meetingCategory')
                ->sortable()
                ->searchable(),

            Column::make('Crops', 'rtcCrop')
                ->sortable()
                ->searchable(),

            Column::make('Venue', 'venue')
                ->sortable()
                ->searchable(),

            Column::make('District', 'district')
                ->sortable()
                ->searchable(),

            Column::make('Start Date', 'startDate_formatted', 'startDate')
                ->sortable(),

            Column::make('End Date', 'endDate_formatted', 'endDate')
                ->sortable(),

            Column::make('Total Days', 'totalDays')
                ->sortable()
                ->searchable(),






            // Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datepicker('startDate'),
            // Filter::datepicker('endDate'),
        ];
    }

    #[On('edit')]
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

    #[On('refresh-data')]
    public function refreshData(): void
    {

        $this->refresh();

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
