<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Pagination\Cursor;
use App\Models\AttendanceRegister;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\LazyCollection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Lazy;
use Illuminate\Database\Eloquent\Collection;
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

    use ExportTrait;
    public bool $deferLoading = false;
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [

            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {


        return AttendanceRegister::query();
    }


    public $namedExport = 'att';
    #[On('export-att')]
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
            ->add('att_id')
            ->add('meetingTitle')
            ->add('meetingCategory')
            ->add('rtcCrop_cassava', fn($model) => $model->rtcCrop_cassava ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('rtcCrop_potato', fn($model) => $model->rtcCrop_potato ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('rtcCrop_sweet_potato', fn($model) => $model->rtcCrop_sweet_potato ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('venue')
            ->add('district')
            ->add('startDate_formatted', fn($model) => Carbon::parse($model->startDate)->format('d/m/Y'))
            ->add('endDate_formatted', fn($model) => Carbon::parse($model->endDate)->format('d/m/Y'))
            ->add('totalDays')
            ->add('name', function ($model) {
                return '
                <div class="d-flex">
                <img src="' . asset('assets/images/users/usr.png') . '" alt="" class="shadow avatar-sm rounded-1 me-2">
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

            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
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

            Column::make('Person ID', 'att_id')->sortable()->searchable(),
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

            Column::make('Rtc Crop (Cassava)', 'rtcCrop_cassava')
                ->sortable()
                ->searchable(),

            Column::make('Rtc Crop (Potato)', 'rtcCrop_potato')
                ->sortable()
                ->searchable(),
            Column::make('Rtc Crop (Sweet potato)', 'rtcCrop_sweet_potato')
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


            Column::make('Submitted By', 'submitted_by')
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
