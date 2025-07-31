<?php

namespace App\Livewire\tables;

use App\Models\GrossMargin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\GrossMarginDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class GrossMarginTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
            Detail::make()
                ->view('components.gross-detail-component'),
        ];
    }

    public function datasource(): Builder
    {
        return GrossMarginDetail::query()->with(['grossMargin', 'items'])->join('gross_margins', function ($join) {
            $join->on('gross_margins.id', '=', 'gross_margin_details.gross_margin_id');
        })->leftJoin('gross_margin_data', function ($join) {
            $join->on('gross_margin_data.gross_margin_detail_id', '=', 'gross_margin_details.id');
        })

            ->select([
                'gross_margin_details.*',
                'gross_margins.title',
                'gross_margin_data.total as gross_total',

                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
            ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('title', function ($model) {
                return $model->grossMargin->title;
            })
            ->add('name_of_producer')
            ->add('season')
            ->add('district')
            ->add('gender')
            ->add('phone_number')
            ->add('gps_s')
            ->add('gps_e')
            ->add('elevation')
            ->add('type_of_produce')
            ->add('epa')
            ->add('section')
            ->add('ta')
            ->add('total', function ($model) {

                $data = $model->items->sum('total');
                return $data;
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Title', 'title')
                ->sortable()
                ->searchable()->headerAttribute(styleAttr: 'min-width: 300px;')
               ,
            Column::make('Gross Margin', 'gross_total', 'gross_margin_data.total')

                ->withSum('SUM', true, true)

                ->withAvg('AVG', true, true)

                ->sortable()
                ->searchable()->headerAttribute(classAttr:'table-sticky-col',styleAttr: 'min-width: 300px;'),


            Column::make('Name of Producer', 'name_of_producer')
                ->sortable()
                ->searchable(),

            Column::make('Season', 'season')
                ->sortable()
                ->searchable(),

            Column::make('District', 'district')
                ->sortable()
                ->searchable(),

            Column::make('Gender', 'gender')
                ->sortable()
                ->searchable(),

            Column::make('Phone Number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('GPS S', 'gps_s')
                ->sortable()
                ->searchable(),

            Column::make('GPS E', 'gps_e')
                ->sortable()
                ->searchable(),

            Column::make('Elevation', 'elevation')
                ->sortable()
                ->searchable(),

            Column::make('Type of Produce', 'type_of_produce')
                ->sortable()
                ->searchable(),

            Column::make('EPA', 'epa')
                ->sortable()
                ->searchable(),

            Column::make('Section', 'section')
                ->sortable()
                ->searchable(),

            Column::make('TA', 'ta')
                ->sortable()
                ->searchable(),





            Column::action('details')

        ];
    }

    public function summarizeFormat(): array
    {
        return [
            'gross_total.{sum,avg,min,max}' => fn($value) => Number::currency($value, in: 'MWK'),
        ];
    }
    public function filters(): array
    {
        return [
            Filter::select('title', 'gross_margins.id')
                ->dataSource(GrossMargin::all())
                ->optionLabel('title')
                ->optionValue('id'),
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
            Button::add('details')
                ->slot('View Details <i class="bx bx-chevron-down"></i>')
                ->id()
                ->class('btn btn-warning btn-sm ')
                ->toggleDetail()
        ];
    }
}
