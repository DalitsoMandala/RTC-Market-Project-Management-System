<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Models\Recruitment;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
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

final class RecruitmentsTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public function setUp(): array
    {
        //  $this->showCheckBox();

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

        if ($user->hasAnyRole('external')) {

            return Recruitment::query()->with([
                'user',
                'user.organisation'
            ])->where('organisation_id', $organisation_id);
        }

        return Recruitment::query()->with([

            'user',
            'user.organisation'
        ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('rc_id')
            ->add('epa')
            ->add('section')
            ->add('district')
            ->add('enterprise')
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('group')
            ->add('approach')
            ->add('mem_female_18_35')
            ->add('mem_male_18_35')
            ->add('mem_male_35_plus')
            ->add('mem_female_35_plus')
            ->add('sector')
            ->add('category')
            ->add('establishment_status')
            ->add('is_registered')
            ->add('registration_body')
            ->add('registration_number')
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
            ->add('is_registered_seed_producer')
            ->add('registration_number_seed_producer')
            ->add('registration_date_seed_producer_formatted', fn($model) => Carbon::parse($model->registration_date_seed_producer)->format('d/m/Y'))
            ->add('uses_certified_seed')
            ->add('uuid')
            ->add('user_id')
            ->add('submitted_by', function ($model) {
                return $model->user->name . '(' . $model->user->organisation->name . ')';
            })
            ->add('submission_period_id')
            ->add('organisation_id')
            ->add('financial_year_id')
            ->add('period_month_id')
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }
    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }
    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Rc id', 'rc_id')
                ->sortable()
                ->searchable(),

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

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),

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
                ->sortable()
                ->searchable(),

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
                ->sortable()
                ->searchable(),

            Column::make('Registration number seed producer', 'registration_number_seed_producer')
                ->sortable()
                ->searchable(),

            Column::make('Registration date seed producer', 'registration_date_seed_producer_formatted', 'registration_date_seed_producer')
                ->sortable(),

            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),

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
