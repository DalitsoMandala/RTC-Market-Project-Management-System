<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;

use Illuminate\Support\Carbon;

use Illuminate\Support\Collection;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Storage;
use App\Models\RpmFarmerAreaCultivation;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmFarmerCultivation extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RpmFarmerAreaCultivation::query()->with('farmers')->whereHas('farmers', function ($model) use ($organisation_id) {

                $model->where('organisation_id', $organisation_id);

            });
        }

        return RpmFarmerAreaCultivation::query()->with('farmers');

    }
    public $namedExport = 'rpmfFC';
    #[On('export-rpmfFC')]
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

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage(10)
                ->pageName('cultivation')
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => $model->farmers->pf_id)
            ->add('id')

            ->add('name_of_actor', function ($model) {
                if (!$model->farmers) {

                    return null;
                }
                return $model->farmers->name_of_actor;
            })
            ->add('area')
            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })
            ->add('variety');

    }

    public function columns(): array
    {
        return [


            Column::make('Actor ID', 'unique_id', 'pf_id')
                ->searchable()
            ,

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
            ,

            Column::make('Variety', 'variety')->sortable()->searchable(),

            Column::make('Area (Number of acres)', 'area')->sortable()->searchable(),



        ];
    }
}
