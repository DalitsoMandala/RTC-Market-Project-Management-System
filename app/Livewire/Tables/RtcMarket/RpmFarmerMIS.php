<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use App\Models\RpmFarmerMarketInformationSystem;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmFarmerMIS extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;


        $query = RpmFarmerMarketInformationSystem::query()->with('farmers')->select([
            'rpmf_mis.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {

            return $query->whereHas('farmers', function ($model) use ($organisation_id) {

                $model->where('organisation_id', $organisation_id);
            });
        }
        return $query;
    }
    public $namedExport = 'rpmfMIS';
    #[On('export-rpmfMIS')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }
    public function relationSearch(): array
    {
        return [

            'farmers' => [
                'pf_id',
                'name_of_actor'

            ],

            // 'user.organisation' => [
            //     'name'
            // ]

        ];
    }


    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }


    public function setUp(): array
    {


        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

            ->add('name_of_actor', function ($model) {
                if (!$model->farmers) {

                    return null;
                }
                return $model->farmers->name_of_actor;
            })
            ->add('unique_id', function ($model) {
                return $model->farmers->pf_id;
            })
            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }
            })

            ->add('name', function ($model) {
                return $model->name;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'rn')->sortable(),
            Column::make('Farmer ID', 'unique_id',)
                ->searchable(),


            Column::make('Name of Market Information systems', 'name')
                ->searchable()
                ->sortable(),
        ];
    }
}
