<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\Project;
use App\Models\Recruitment;
use App\Models\User;
use App\Traits\BatchTrait;
use App\Traits\ExportTrait;
use App\Traits\UITrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

final class RecruitmentsTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    use UITrait;
    use BatchTrait;

    public function setUp(): array
    {
        //  $this->showCheckBox();
        if ($this->getBatch()) {
            $this->search = $this->getBatch();
        }
        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')
                ->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }
    public $namedExport = 'recruits';

    #[On('export-recruits')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }
    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = Recruitment::query()->with([
            'user' => fn($q) => $q->withTrashed(),
            'user.organisation'
        ])->select([
            'recruitments.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {

            return $query->where('organisation_id', $organisation_id);
        }

        return $query;
    }


    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('rn')
            ->add('rc_id')
            ->add('epa', fn($model) => $model->epa ?? null)
            ->add('section', fn($model) => $model->section ?? null)
            ->add('district', fn($model) => $model->district ?? null)
            ->add('enterprise', fn($model) => $model->enterprise ?? null)
            ->add('date_of_recruitment_formatted', fn($model) => $model->date_of_recruitment ? Carbon::parse($model->date_of_recruitment)->format('d/m/Y') : null)
            ->add('name_of_actor', fn($model) => ucwords(strtolower($model->name_of_actor)) ?? null)
            ->add('name_of_representative', fn($model) => $model->name_of_representative ?? null)
            ->add('phone_number', fn($model) => $model->phone_number ?? null)
            ->add('type', fn($model) => $model->type ?? null)
            ->add('group', fn($model) => $model->group ?? null)
            ->add('approach', fn($model) => $model->approach ?? null)
            ->add('mem_female_18_35')
            ->add('mem_male_18_35')
            ->add('mem_male_35_plus')
            ->add('mem_female_35_plus')
            ->add('sector', fn($model) => $model->sector ?? null)
            ->add('category', fn($model) => $model->sector ?? null)
            ->add('establishment_status', fn($model) => $this->booleanUI(
                $model->establishment_status,
                $model->establishment_status === 'New'
            ))
            ->add('is_registered', fn($model) => $this->booleanUI(
                $model->is_registered,
                $model->is_registered == 1,
                true
            ))
            ->add('registration_body', fn($model) => $model->registration_body ?? null)
            ->add('registration_number', fn($model) => $model->registration_number ?? null)
            ->add('registration_date_formatted', fn($model) => Carbon::parse($model->registration_date)->format('d/m/Y'))
            ->add('emp_formal_female_18_35')
            ->add('emp_formal_male_18_35')
            ->add('emp_formal_male_35_plus')
            ->add('emp_formal_female_35_plus')
            ->add('emp_informal_female_18_35')
            ->add('emp_informal_male_18_35')
            ->add('emp_informal_male_35_plus')
            ->add('emp_informal_female_35_plus')
            ->add('area_under_cultivation')
            ->add('is_registered_seed_producer', fn($model) => $this->booleanUI(
                $model->is_registered_seed_producer,
                $model->is_registered_seed_producer == 1,
                true
            ))
            ->add('uses_certified_seed', fn($model) => $this->booleanUI(
                $model->uses_certified_seed,
                $model->uses_certified_seed == 1,
                true
            ))
            ->add('uuid')
            ->add('user_id')
            ->add('submitted_by', function ($model) {
                return $model->user?->name . '(' . $model->user?->organisation->name . ')';
            })
            ->add('submission_period_id')
            ->add('organisation_id')
            ->add('financial_year_id')
            ->add('period_month_id')
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }

    #[On('download-export')]
    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function actions($row): array
    {
        $user = User::find(auth()->user()->id);
        $project = Project::where('name', 'RTC Market')->first();
        $link = '/forms/' . str_replace(' ', '-', strtolower($project->name)) . '/rtc-actor-recruitment-form/edit/' . $row->id . '/' . $row->uuid;


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
    public function columns(): array
    {
        return [
            Column::action('')->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('#', 'rn')->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Recruitment id', 'rc_id')
                ->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col')
                ->searchable(),
            Column::make('Name of actor', 'name_of_actor')->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col')
                ->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col')
                ->searchable(),
            Column::make('UUID', 'uuid')->searchable()->hidden(),
            Column::make('Epa', 'epa')
                ->sortable()
                ->searchable(),

            Column::make('Section', 'section')
                ->sortable()
                ->searchable(),

            Column::make('District', 'district')
                ->sortable()
                ->searchable(),

            Column::make('Enterprise', 'enterprise')
                ->sortable()
                ->searchable(),

            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable(),




            Column::make('Name of representative', 'name_of_representative')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable(),

            Column::make('Group', 'group')
                ->sortable()
                ->searchable(),

            Column::make('Approach', 'approach')
                ->sortable()
                ->searchable(),

            Column::make('Mem female 18 35', 'mem_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Mem male 18 35', 'mem_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Mem male 35 plus', 'mem_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Mem female 35 plus', 'mem_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Sector', 'sector')
                ->sortable()
                ->searchable(),

            Column::make('Category', 'category')
                ->sortable()
                ->searchable(),

            Column::make('Establishment status', 'establishment_status')
                ->sortable()
                ->searchable(),

            Column::make('Is registered', 'is_registered')
                ->sortable(),

            Column::make('Registration body', 'registration_body')
                ->sortable()
                ->searchable(),

            Column::make('Registration number', 'registration_number')
                ->sortable()
                ->searchable(),

            Column::make('Registration date', 'registration_date_formatted', 'registration_date')
                ->sortable(),

            Column::make('Emp formal female 18 35', 'emp_formal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Emp formal male 18 35', 'emp_formal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Emp formal male 35 plus', 'emp_formal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Emp formal female 35 plus', 'emp_formal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Emp informal female 18 35', 'emp_informal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Emp informal male 18 35', 'emp_informal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Emp informal male 35 plus', 'emp_informal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Emp informal female 35 plus', 'emp_informal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Area under cultivation', 'area_under_cultivation')
                ->sortable()
                ->searchable(),

            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable(),


            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable(),

            Column::make('Submitted by', 'submitted_by')

                ->searchable(),





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

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: ' . $row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
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
