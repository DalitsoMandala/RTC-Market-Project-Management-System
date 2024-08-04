<?php

namespace App\Livewire\Tables;

use Throwable;
use App\Jobs\Mapper;
use App\Models\User;
use Illuminate\Bus\Batch;
use Livewire\Attributes\On;
use App\Jobs\readBigDataJob;
use App\Helpers\IndicatorsContent;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use App\Models\IndicatorDisaggregation;
use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\B1;
use PowerComponents\LivewirePowerGrid\Lazy;
use PowerComponents\LivewirePowerGrid\Cache;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use App\Helpers\rtc_market\indicators\Indicator_B2;
use Illuminate\Contracts\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Jobs\ExportJob;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class ReportingTable extends PowerGridComponent
{

    use WithExport;

    public $start_date, $end_date, $project, $indicators, $financial_year, $reporting_period, $data;
    public $collection = [];

    // public $showExporting = true;
    public function setUp(): array
    {



        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()

                ->showRecordCount(),
        ];



    }


    #[On('loaded')]

    public function setData($data)
    {

        $this->collection = $data;
        $this->refresh();
        $this->setUp = [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()

                ->showRecordCount(),
        ];
    }

    public function datasource(): Collection
    {

        return collect($this->collection);


    }


    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('number')
            ->add('indicator_name')
            ->add('name')
            ->add('project')
            ->add('value')
        ;
    }
    // public function filters(): array
    // {
    //     return [
    //         // Filter::datetimepicker('date_established'),
    //         // Filter::datetimepicker('date_ending'),
    //         // Filter::select('name', 'name')
    //         //     ->dataSource(function () {
    //         //         $submission = IndicatorDisaggregation::select(['name'])->distinct();

    //         //         return $submission->get();
    //         //     })
    //         //     ->optionLabel('name')
    //         //     ->optionValue('name')


    //     ];
    // }
    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Disaggregation', 'name')
                ->sortable(),
            Column::make('Value', 'value'),

            Column::make('Indicator Name', 'indicator_name')

                ->sortable(),

            Column::make('Indicator #', 'number')
                ->searchable()
                ->sortable(),

            Column::make('Project', 'project'),

        ];
    }

    #[On('filtered-data')]
    public function getData($data)
    {

        $this->project = $data['project_id'];
        $this->indicators = $data['indicators'];
        //$this->start_date = $data['start_date'];
        // $this->end_date = $data['end_date'];
        $this->reporting_period = $data['reporting_period'];
        $this->financial_year = $data['financial_year'];


    }

    #[On('reset-filters')]
    public function resetData()
    {
        $this->reset('project', 'indicators', 'reporting_period', 'financial_year');
        $this->refresh();
    }


}