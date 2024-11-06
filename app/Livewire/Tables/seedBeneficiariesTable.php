<?php

namespace App\Livewire\tables;

use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\SeedBeneficiary;
use Illuminate\Support\Facades\DB;
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

final class seedBeneficiariesTable extends PowerGridComponent
{
    use WithExport;

    public $crop;

    public string $tableName = 'seed_beneficiaries';

    public function setUp(): array
    {
        $this->showCheckBox();

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
        return SeedBeneficiary::query()->with('user')->where('crop', $this->crop)->join('users', function ($user) {
            $user->on('users.id', '=', 'seed_beneficiaries.user_id');
        })->select('seed_beneficiaries.*', 'users.name as user_name');
    }
    public function onUpdatedEditable(string|int $id, string $field, string $value): void
    {
        dd($id);
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('name_of_aedo')
            ->add('aedo_phone_number')
            ->add('date_formatted', fn($model) => Carbon::parse($model->date)->format('d/m/Y'))
            ->add('name_of_recipient')
            ->add('village')
            ->add('sex')
            ->add('age')
            ->add('marital_status')
            ->add('hh_head')
            ->add('household_size')
            ->add('children_under_5')
            ->add('variety_received')
            ->add('bundles_received')
            ->add('phone_or_national_id')
            ->add('crop')
            ->add('user_id')
            ->add('user', fn($model) => $model->user->name)
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('District', 'district', 'district')
                ->sortable()
                ->editOnClick(hasPermission: true)

                ->searchable(),

            Column::make('Epa', 'epa')
                ->sortable()
                ->searchable(),

            Column::make('Section', 'section')
                ->sortable()
                ->searchable(),

            Column::make('Name of aedo', 'name_of_aedo')
                ->sortable()
                ->searchable(),

            Column::make('Aedo phone number', 'aedo_phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Date', 'date_formatted', 'date')
                ->sortable(),

            Column::make('Name of recipient', 'name_of_recipient')
                ->sortable()
                ->searchable(),

            Column::make('Village', 'village')
                ->sortable()
                ->searchable(),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable(),

            Column::make('Age', 'age')
                ->sortable()
                ->searchable(),

            Column::make('Marital status', 'marital_status')
                ->sortable()
                ->searchable(),

            Column::make('Hh head', 'hh_head')
                ->sortable()
                ->searchable(),

            Column::make('Household size', 'household_size')
                ->sortable()
                ->searchable(),

            Column::make('Children under 5', 'children_under_5')
                ->sortable()
                ->searchable(),

            Column::make('Variety received', 'variety_received')
                ->sortable()
                ->searchable(),

            Column::make('Bundles received', 'bundles_received')
                ->sortable()
                ->searchable(),

            Column::make('Phone or national id', 'phone_or_national_id')
                ->sortable()
                ->searchable(),

            Column::make('Crop', 'crop')
                ->sortable()
                ->searchable(),

            Column::make('Submitted by', 'user', 'user_name')->sortable()->searchable(),
            Column::action('Action')

        ];
    }

    public function filters(): array
    {
        return [

        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('Edit: ' . $row->id)
                ->id()
                ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
                ->dispatch('edit', ['rowId' => $row->id])
        ];
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
