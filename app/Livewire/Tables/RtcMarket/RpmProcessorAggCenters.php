<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Models\RpmProcessorAggregationCenter;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorAggCenters extends PowerGridComponent
{
    public string $tableName = 'rpmProcessorAggCenters';
    use WithExport;
    use ExportTrait;
    public $rowId;
    public function datasource(): Builder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = RpmProcessorAggregationCenter::query()->when($this->rowId, function ($query) {
            $query->where('rpmp_aggregation_centers.rpmp_id', $this->rowId);
        })->with('processors')->select([
            'rpmp_aggregation_centers.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {

            return $query->whereHas('processors', function ($model) use ($organisation_id) {

                $model->where('organisation_id', $organisation_id);
            });
        }

        return $query;
    }
    public $namedExport = 'rpmpAC';
    #[On('export-rpmpAC')]
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

            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage(10)
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')

->add('group_name', function ($model) {
                return $model->processors->group_name;
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

                   return null;
            })

            ->add('name', function ($model) {
                return $model->name;
            });
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn')->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Processor ID', 'unique_id',)
                ->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),

       Column::make('Group name', 'group_name')
                ->sortable()
                ->searchable()
                ->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),


            Column::make('Name of Aggregation centers', 'name')
                ->searchable()
                ->sortable(),
        ];
    }
}
