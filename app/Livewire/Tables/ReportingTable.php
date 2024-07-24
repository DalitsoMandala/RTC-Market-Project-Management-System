<?php

namespace App\Livewire\Tables;

use App\Helpers\IndicatorsContent;
use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\B1;
use App\Helpers\rtc_market\indicators\Indicator_B2;
use App\Models\IndicatorDisaggregation;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

final class ReportingTable extends PowerGridComponent
{

    public $start_date, $end_date, $project, $indicators, $financial_year, $reporting_period;
    public $showExporting = true;
    public bool $deferLoading = true;
    public function datasource(): ?Collection
    {
        $builder = IndicatorDisaggregation::with(['indicator', 'indicator.project']);

        if ($this->project) {
            $builder = $builder->whereHas('indicator.project', function ($query) {
                $query->where('id', $this->project);
            });
        }

        if ($this->indicators) {
            $builder = $builder->whereHas('indicator', function ($query) {
                $query->whereIn('id', $this->indicators);
            });
        }

        $collection = collect();
        $count = 1;

        $builder->chunk(100, function ($disaggregations) use (&$collection, &$count) {
            foreach ($disaggregations as $disaggregation) {
                $item = [
                    'id' => $count++,
                    'name' => $disaggregation->name,
                    'indicator_name' => $disaggregation->indicator->indicator_name,
                    'project' => $disaggregation->indicator->project->name,
                    'number' => $disaggregation->indicator->indicator_no,
                    'indicator_id' => $disaggregation->indicator->id,
                ];

                $content = new IndicatorsContent(name: $item['indicator_name'], number: $item['number']);
                $getContent = $content->content();
                if ($getContent['class'] !== null) {
                    $indicator = new $getContent['class']($this->reporting_period, $this->financial_year);
                    $item = $this->mapData($indicator->getDisaggregations(), $item);
                }

                $collection->push($item);
            }
        });

        return $collection;
    }

    public function mapData($array, $item)
    {
        foreach ($array as $key => $record) {
            if ($key === $item['name']) {
                $item['value'] = $record;
                break; // Once found, no need to continue looping
            }
        }

        return $item;
    }

    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->queues(6)
                ->onQueue('reports')
                ->onConnection('database')
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
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
    public function filters(): array
    {
        return [
            // Filter::datetimepicker('date_established'),
            // Filter::datetimepicker('date_ending'),
            Filter::select('name', 'name')
                ->dataSource(function () {
                    $submission = IndicatorDisaggregation::select(['name'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('name')
                ->optionValue('name')
            ,

        ];
    }
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

    // switch ($item['number']) {
    //     // case 'A1':
    //     //  $indicator = new A1($this->reporting_period, $this->financial_year);
    //     //$item = $this->mapData($indicator->getDisaggregations(), $item);
    //     //break;
    //     // case 'B1':
    //     //     $indicator = new B1($this->reporting_period, $this->financial_year);
    //     //     $item = $this->mapData($indicator->getDisaggregations(), $item);
    //     //     break;

    //     // case 'B2':
    //     //     $indicator = new Indicator_B2($this->reporting_period, $this->financial_year);
    //     //     $item = $this->mapData($indicator->getDisaggregations(), $item);
    //     //     break;
    //     // case '2.2.1':
    //     //     $indicator = new Indicator_2_2_1($this->start_date, $this->end_date);
    //     //     $item = $this->mapData($indicator->getDisaggregations(), $item);
    //     //     break;
    // }
}