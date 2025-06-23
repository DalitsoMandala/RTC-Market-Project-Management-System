<?php

namespace App\Livewire\tables;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\SeedBeneficiary;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class SeedBeneficiariesOFSPTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public $crop;


    public $namedExport = 'seedBeneficiaries';
    public $nameOfTable = 'Seed Beneficiaries';
    public $descriptionOfTable = "Data from seed beneficiaries form";

    public function setUp(): array
    {


        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->pageName("{$this->crop}_page")
                ->showRecordCount(),
        ];
    }




    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = SeedBeneficiary::query()->with('user')->where('crop', 'OFSP')->join('users', function ($user) {
            $user->on('users.id', '=', 'seed_beneficiaries.user_id');
        })->select([
            'seed_beneficiaries.*',
            'users.name as user_name',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {
            $query->where('seed_beneficiaries.organisation_id', $organisation_id);
        }
        return $query;
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
            ->add('group_name')
            ->add('village')
            ->add('sex', function ($model) {
                return $model->sex === 1 ? 'Male' : 'Female';
            })
            ->add('age')
            ->add('marital_status', function ($model) {
                $status = match ($model->marital_status) {
                    1 => 'Single',
                    2 => 'Married',
                    3 => 'Widowed',
                    4 => 'Separated',
                    5 => 'Polygamy',
                    default => 'Single'
                };


                return $status;
            })
            ->add('hh_head', function ($model) {
                $head = match ($model->hh_head) {
                    1 => 'MHH',
                    2 => 'FHH',
                    3 => 'CHH',
                    default => 'MHH'
                };
                return $head;
            })
            ->add('household_size')
            ->add('children_under_5')
            ->add('variety_received')
            ->add('variety')
            ->add('bundles_received')
            ->add('phone_or_national_id')
            ->add('season_type')
            ->add('crop')
            ->add('user_id')
            ->add('user', fn($model) => $model->user->name)
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn')->sortable(),
            Column::make('District', 'district', 'district')
                ->sortable()


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


            Column::make('Group Name', 'group_name')
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

            Column::make('Variety received', 'variety_received')->searchable(),

            Column::make('Bundles received', 'bundles_received')
                ->sortable(),

            Column::make('Phone', 'phone_number')
                // ->sortable()
                ->searchable(),
            Column::make('National id', 'national_id')
                //  ->sortable()
                ->searchable(),
            Column::make('Season Type', 'season_type')

                ->searchable(),

            // Column::make('Crop', 'crop')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Submitted by', 'user', 'user_name')->sortable()->searchable(),
            Column::action('')

        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('hideModal')]
    public function edit(): void
    {
        $this->refresh();
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('my-2 btn btn-warning btn-sm custom-tooltip')
                ->tooltip('Edit')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('admin') || User::find(auth()->user()->id)->hasAnyRole('manager'))

                ->dispatch('edit-showModal', [
                    'id' => $row->id,
                    'name' => 'view-detail-modal'
                ]),

            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 btn-sm custom-tooltip')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('admin') || User::find(auth()->user()->id)->hasAnyRole('manager'))
                ->tooltip('Delete')
                ->dispatch('deleteRecord', [
                    'id' => $row->id,
                    'name' => 'delete-detail-modal'
                ]),
        ];
    }
}
