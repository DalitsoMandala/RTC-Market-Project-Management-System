<?php

namespace App\Livewire\tables;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\FarmerSeedRegistration;
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

final class FarmerSeedRegistrationTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = FarmerSeedRegistration::query()->with([
            'productionFarmers'
        ])->join('rtc_production_farmers', function ($join) {
            $join->on('rtc_production_farmers.id', '=', 'farmer_seed_registrations.id');
        })->select([
            'farmer_seed_registrations.*',
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
            ->add('id')
            ->add('farmer_id')
            ->add('variety')
            ->add('reg_date_formatted', fn($model) => Carbon::parse($model->reg_date)->format('d/m/Y'))
            ->add('reg_no')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'rn')->sortable(),
            Column::make('Farmer id', 'farmer_id'),
            Column::make('Variety', 'variety')
                ->sortable()
                ->searchable(),

            Column::make('Reg date', 'reg_date_formatted', 'reg_date')
                ->sortable(),

            Column::make('Reg no', 'reg_no')
                ->sortable()
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
