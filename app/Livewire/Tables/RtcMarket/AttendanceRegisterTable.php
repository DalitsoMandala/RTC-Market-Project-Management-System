<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class AttendanceRegisterTable extends PowerGridComponent
{
    use WithExport;

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
        return DB::table('attendance_registers');
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
            ->add('created_at', function ($model) {
                return Carbon::parse($model->created_at)->format('d/m/Y');
            })
            ->add('updated_at');
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

            Column::make('Date of Submission', 'created_at', 'created_at')
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