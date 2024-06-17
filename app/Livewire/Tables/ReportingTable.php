<?php

namespace App\Livewire\Tables;

use App\Helpers\rtc_market\indicators\A1;
use App\Helpers\rtc_market\indicators\B1;
use App\Helpers\rtc_market\indicators\Indicator_2_2_1;
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

    public $start_date, $end_date, $project, $indicators;

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
                $collection->push([
                    'id' => $count++,
                    'name' => $disaggregation->name,
                    'indicator_name' => $disaggregation->indicator->indicator_name,
                    'project' => $disaggregation->indicator->project->name,
                    'number' => $disaggregation->indicator->indicator_no,
                    'indicator_id' => $disaggregation->indicator->id,
                ]);
            }
        });

        $finalCollection = $collection->transform(function ($item) {
            switch ($item['number']) {
                case 'A1':
                    $indicator = new A1($this->start_date, $this->end_date);
                    $item = $this->mapData($indicator->getDisaggregations(), $item);
                    break;
                case 'B1':
                    $indicator = new B1($this->start_date, $this->end_date);
                    $item = $this->mapData($indicator->getDisaggregations(), $item);
                    break;
                case '2.2.1':
                    $indicator = new Indicator_2_2_1($this->start_date, $this->end_date);
                    $item = $this->mapData($indicator->getDisaggregations(), $item);
                    break;
            }

            return $item;
        });

        return $finalCollection;
    }

    public function mapData($array, $item)
    {
        $disaggregations = $array;
        foreach ($disaggregations as $key => $record) {
            $key === $item['name'] ? $item['value'] = $record : 0;
        }

        return $item;

    }
    public function setUp(): array
    {
        // $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
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

            Column::make('Indicator #', 'number')
                ->searchable()
                ->sortable(),

            Column::make('Indicator Name', 'indicator_name')
                ->hidden()
                ->sortable(),

            Column::make('Disaggregation', 'name')
                ->sortable(),

            Column::make('Project', 'project'),

            Column::make('Value', 'value'),

        ];
    }

    #[On('filtered-data')]
    public function getData($data)
    {

        $this->project = $data['project_id'];
        $this->indicators = $data['indicators'];
        $this->start_date = $data['start_date'];
        $this->end_date = $data['end_date'];
    }
}