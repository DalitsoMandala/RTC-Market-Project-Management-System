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


        return GrossMargin::query()

            ->select([
                'gross_margins.*',



                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
            ]);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
->add('date_formatted', fn($model) => Carbon::parse($model->date)->format('d/m/Y'))
            ->add('sex')
            ->add('village')

            ->add('enterprise')
            ->add('name')
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
            ->add('gross_margin')
            ->add('total_harvest')
            ->add('selling_price')
            ->add('income')
            ->add('yield')
            ->add('break_even_yield')
            ->add('break_even_price')
            ->add('total_variable_cost')

        ;
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),


            Column::make('Date', 'date_formatted')
                ->sortable()
                ->searchable(),

            Column::make('Enterprise', 'enterprise', 'gross_margins.enterprise')
                ->sortable()
                ->searchable(),

            Column::make('Gross Margin/Profit', 'gross_margin')

                ->withSum('SUM', true, false)

                ->withAvg('AVG', true, false)

                ->sortable()
                ->searchable()->headerAttribute(styleAttr: 'min-width: 300px;')->bodyAttribute('fw-bolder '),


            Column::make('Name of Producer', 'name')
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
            Column::make('Village', 'village')
                ->sortable()
                ->searchable(),





            Column::make('Selling Price', 'selling_price')
                ->sortable()
                ->searchable(),



            Column::make('Income Price', 'income')
                ->sortable()
                ->searchable(),

            Column::make('Total Harvest', 'total_harvest')
                ->sortable()
                ->searchable(),

            Column::make('Total Valuable Costs', 'total_variable_cost')
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


            Filter::select('enterprise', 'gross_margins.enterprise')
                ->dataSource(GrossMargin::select(['enterprise'])->distinct()->get())
                ->optionLabel('enterprise')
                ->optionValue('enterprise'),


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
                ->slot('View Details  <i class="bx bx-chevron-down"></i>')
                ->id()
                ->class('btn btn-warning goLeft btn-sm ')
                ->toggleDetail(),

        ];
    }
}
