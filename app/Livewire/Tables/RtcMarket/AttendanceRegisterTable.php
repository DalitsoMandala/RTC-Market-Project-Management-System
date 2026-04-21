<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\AttendanceRegister;
use App\Models\Project;
use App\Models\User;
use App\Traits\BatchTrait;
use App\Traits\ExportTrait;
use App\Traits\UITrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\Cursor;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\LazyCollection;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\Lazy;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use Spatie\SimpleExcel\SimpleExcelWriter;

final class AttendanceRegisterTable extends PowerGridComponent
{
    use WithExport;
    use UITrait;
    use ExportTrait;
    use BatchTrait;
    protected $table_name = 'attendance-register';
    public bool $deferLoading = false;
    public bool $multiSort = false;
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

        if ($this->getBatch()) {
            $this->search = $this->getBatch();
        }
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = AttendanceRegister::query()->with([
            'user' => fn($q) => $q->withTrashed(),
            'user.organisation',
        ])->select([
            'attendance_registers.*',
            DB::raw(' ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {
            $query->where('organisation_id', $organisation_id);
        }

        return $query;
    }


    public $namedExport = 'att';
    #[On('export-att')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }


    #[On('download-export')]
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
            ->add('rtcCrop_cassava', fn($model) => $this->booleanUI($model->rtcCrop_cassava, $model->rtcCrop_cassava == 1, true))
            ->add('rtcCrop_potato', fn($model) => $this->booleanUI($model->rtcCrop_potato, $model->rtcCrop_potato == 1, true))
            ->add('rtcCrop_sweet_potato', fn($model) => $this->booleanUI($model->rtcCrop_sweet_potato, $model->rtcCrop_sweet_potato == 1, true))
            ->add('venue', fn($model) => $model->venue ?? null)
            ->add('district', fn($model) => $model->district ?? null)
            ->add('startDate_formatted', fn($model) => Carbon::parse($model->startDate)->format('d/m/Y'))
            ->add('endDate_formatted', fn($model) => Carbon::parse($model->endDate)->format('d/m/Y'))
            ->add('totalDays', function ($model) {
                return $model->totalDays;
            })
            ->add('name', function ($model) {
                return $model->name;
            })
            ->add('sex')
            ->add('organization')
            ->add('designation', fn($model) => $model->designation ?? '-')
            ->add('phone_number', fn($model) => $model->phone_number ?? '-')
            ->add('email', fn($model) => $model->email ?? '-')
            ->add('created_at_formatted', function ($model) {
                return Carbon::parse($model->created_at)->format('d/m/Y');
            })

            ->add('submitted_by', function ($model) {
                return $model->user?->name . '(' . $model->user?->organisation->name . ')';
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
Column::action('')->bodyAttribute('table-sticky-col')->headerAttribute('table-sticky-col'),
            Column::make('#', 'rn')->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),

            Column::make('Attendee ID', 'att_id')->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('UUID', 'uuid')->searchable()->hidden(),
            Column::make('Name', 'name')
                ->sortable()
                ->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
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

            Column::make('Cassava', 'rtcCrop_cassava')
                ->sortable()
                ->searchable(),

            Column::make('Potato', 'rtcCrop_potato')
                ->sortable()
                ->searchable(),
            Column::make('Sweet potato', 'rtcCrop_sweet_potato')
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
            //    Filter::datepicker('date'),

            Filter::select('rtcCrop_cassava', 'rtcCrop_cassava')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })->optionLabel('name')->optionValue('value'),

            Filter::select('rtcCrop_potato', 'rtcCrop_potato')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })
                ->optionLabel('name')
                ->optionValue('value'),

            Filter::select('rtcCrop_sweet_potato', 'rtcCrop_sweet_potato')
                ->dataSource(function () {
                    return [
                        ['name' => 'Yes', 'value' => 1],
                        ['name' => 'No', 'value' => 0],
                    ];
                })
                ->optionLabel('name')
                ->optionValue('value'),

        ];
    }

    #[On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

public function actions($row): array
    {
        $user = User::find(auth()->user()->id);
        $project = Project::where('name', 'RTC Market')->first();
        $link = '/forms/' . str_replace(' ', '-', strtolower($project->name)) . '/'.$this->table_name.'/edit/' . $row->id . '/' . $row->uuid;


        switch ($user->roles()->first()->name) {
            case 'admin':
                $link = "/admin" . $link;
                break;

            case 'staff':
                $link = "/staff" . $link;
                break;
            case 'manager':
                $link = "/manager" . $link;
                break;

            case 'monitor':
                $link = "/monitor" . $link;
                break;
        }
     // Define the user variable once to avoid repeated calls
        $user = auth()->user();

        // Use dedicated role checks
        $isAdmin = $user->hasAnyRole(['admin', 'manager']);
        $isStaff = $user->hasAnyRole('staff');

        // Logic for staff permission (Only their own records)
        $canEdit = $isAdmin || ($isStaff && $user->id === $row->user_id);

        return [
            Button::add('edit')
                ->can($canEdit)
                ->render(function () use ($link) {
                    // Using a simple return is faster than compiling a Blade view for a single button
                    return '<a href="' . $link . '" class="btn btn-warning btn-sm" title="Edit Record"><i class="bx bx-pen"></i></a>';
                })
        ];
    }

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
