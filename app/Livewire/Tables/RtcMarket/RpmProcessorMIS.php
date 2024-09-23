<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\RtcProductionProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use App\Models\RpmProcessorMarketInformationSystem;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorMIS extends PowerGridComponent
{
    use WithExport;
    use \App\Traits\ExportTrait;
    public function datasource(): Builder
    {
        return RpmProcessorMarketInformationSystem::query()->with('processors');
    }
    public $namedExport = 'rpmpMIS';
    #[On('export-rpmpMIS')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }
    public function relationSearch(): array
    {
        return [

            'processors' => [
                'pp_id',
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
                ->showPerPage(5)
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

            ->add('name_of_actor', function ($model) {
                if (!$model->processors) {

                    return null;
                }
                return $model->processors->name_of_actor;
            })
            ->add('unique_id', function ($model) {
                return $model->processors->pp_id;
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
            Column::make(' ID', 'id', )
                ->sortable()
            ,
            Column::make('Actor ID', 'unique_id', )
                ->searchable()
            ,

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
            ,

            Column::make('Name of Market Information systems', 'name')
                ->searchable()
                ->sortable(),
        ];
    }
}
