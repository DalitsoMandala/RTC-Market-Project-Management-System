<?php

namespace App\Livewire\tables;

use App\Models\Crop;
use App\Traits\UITrait;
use App\Models\GrossMargin;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Number;
use App\Models\GrossMarginDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    use UITrait;
    use ExportTrait;
    public bool $deferLoading = false;
    public $nameOfTable = 'Gross Margin';
    public $descriptionOfTable = 'Data for gross margins/profit';

    public $namedExport = 'gross';
    #[On('export-gross')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();
    }


    #[On('download-export')]
    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }

    public function setUp(): array
    {

        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
            Detail::make()
                ->view('components.gross-detail-component'),
        ];
    }

    public function datasource(): Builder
    {


        return GrossMarginDetail::query()->with(['grossMargin'])->join('gross_margins', function ($join) {
            $join->on('gross_margins.id', '=', 'gross_margin_details.gross_margin_id');
        })

            ->select([
                'gross_margin_details.*',
                'gross_margins.title',
                'gross_margins.enterprise',


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
            ->add('enterprise')
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
                ->searchable()->headerAttribute(styleAttr: 'min-width: 300px;'),

            Column::make('Enterprise', 'enterprise', 'gross_margins.enterprise')
                ->sortable()
                ->searchable(),

            Column::make('Gross Margin/Profit', 'gross_margin')

                ->withSum('SUM', true, false)

                ->withAvg('AVG', true, false)

                ->sortable()
                ->searchable()->headerAttribute(styleAttr: 'min-width: 300px;')->bodyAttribute('fw-bolder '),


            Column::make('Name of Producer', 'name_of_producer')
                ->sortable()
                ->searchable(),

            Column::make('Season', 'season')
                ->sortable()
                ->searchable(),

            Column::make('Season Dates', 'season_dates')
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
            Column::make('Selling Price Desc', 'selling_price_desc')
                ->sortable()
                ->searchable(),

            Column::make('Selling Price Qty', 'selling_price_qty')
                ->sortable()
                ->searchable(),

            Column::make('Selling Price Unit Price', 'selling_price_unit_price')
                ->sortable()
                ->searchable(),

            Column::make('Selling Price', 'selling_price')
                ->sortable()
                ->searchable(),

            Column::make('Income Price Desc', 'income_price_desc')
                ->sortable()
                ->searchable(),

            Column::make('Income Price Qty', 'income_price_qty')
                ->sortable()
                ->searchable(),

            Column::make('Income Price Unit Price', 'income_price_unit_price')
                ->sortable()
                ->searchable(),

            Column::make('Income Price', 'income_price')
                ->sortable()
                ->searchable(),

            Column::make('Total Valuable Costs', 'total_valuable_costs')
                ->sortable()
                ->searchable(),

            Column::make('Yield', 'yield')
                ->sortable()
                ->searchable(),

            Column::make('Break Even Yield', 'break_even_yield')
                ->sortable()
                ->searchable(),

            Column::make('Break Even Price', 'break_even_price')
                ->sortable()
                ->searchable(),



            Column::action('details')

        ];
    }

    public function summarizeFormat(): array
    {
        return [
            'gross_margin.{sum,avg,min,max}' => fn($value) =>  Number::currency($value, in: 'MWK'),
        ];
    }
    public function filters(): array
    {
        return [
            Filter::select('title', 'gross_margins.id')
                ->dataSource(GrossMargin::all())
                ->optionLabel('title')
                ->optionValue('id'),

            Filter::select('season', 'gross_margin_details.season')
                ->dataSource(GrossMarginDetail::select(['season'])->distinct()->get())
                ->optionLabel('season')
                ->optionValue('season'),

            Filter::select('season_dates', 'gross_margin_details.season_dates')
                ->dataSource(GrossMarginDetail::select(['season_dates'])->distinct()->get())
                ->optionLabel('season_dates')
                ->optionValue('season_dates'),

            Filter::select('enterprise', 'gross_margins.enterprise')
                ->dataSource(Crop::select(['name'])->distinct()->get())
                ->optionLabel('name')
                ->optionValue('name'),


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
                ->slot('View Valuable Costs <i class="bx bx-chevron-down"></i>')
                ->id()
                ->class('btn btn-warning btn-sm ')
                ->toggleDetail()
        ];
    }
}
