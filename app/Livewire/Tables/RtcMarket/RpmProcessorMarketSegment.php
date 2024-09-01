<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorMarketSegment extends PowerGridComponent
{
    public function datasource(): Builder
    {
        return RtcProductionProcessor::query()->whereNotNull('market_segment');
    }
    public function setUp(): array
    {


        return [

            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('name_of_actor')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('market_segment_fresh', function ($model) {
                $arr = json_decode($model->market_segment);
                $segment = collect($arr);
                if ($segment->contains('FRESH')) {
                    return 'FRESH';
                }

                return null;

            })

            ->add('market_segment_processed', function ($model) {
                $arr = json_decode($model->market_segment);
                $segment = collect($arr);
                if ($segment->contains('PROCESSED')) {

                    return 'PROCESSED';
                }

                return null;

            })


        ;

    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'unique_id', 'id')
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
