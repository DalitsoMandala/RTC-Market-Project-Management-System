<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmFarmerMarketSegment extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public bool $withSortStringNumber = true;
    public function datasource(): Builder
    {
        return RtcProductionFarmer::query()->select('name_of_actor', 'pf_id', 'market_segment_fresh', 'market_segment_processed');
    }

    public $namedExport = 'rpmfMS';
    #[On('export-rpmfMS')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function setUp(): array
    {


        return [

            Header::make()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('pf_id')

            ->add('name_of_actor')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('market_segment_fresh', function ($model) {
                $arr = json_decode($model->market_segment);
                $segment = collect($arr);
                if ($segment->contains('Fresh')) {
                    return 'Fresh';
                }

                return null;

            })

            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })

            ->add('market_segment_processed', function ($model) {
                $arr = json_decode($model->market_segment);
                $segment = collect($arr);
                if ($segment->contains('Processed')) {

                    return 'Processed';
                }

                return null;

            })


        ;

    }

    public function columns(): array
    {
        return [
            Column::make('Actor ID', 'pf_id')
                ->searchable()
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
                ->sortable(),

            Column::make('Market Segment/Fresh', 'market_segment_fresh'),

            Column::make('Market Segment/Processed', 'market_segment_processed'),

        ];
    }
}
