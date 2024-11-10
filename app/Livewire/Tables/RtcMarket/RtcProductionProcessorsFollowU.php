<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmprocessorFollowUp;
use Illuminate\Support\Facades\DB;

use App\Models\RtcProductionProcessor;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RtcProductionProcessorsFollowU extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public function setUp(): array
    {
        //  $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(5)
                ->pageName('processors-followup')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RpmProcessorFollowUp::query()->with('processors', 'user', 'user.organisation')
                ->whereHas('user.organisation', function ($model) use ($organisation_id) {

                    $model->where('id', $organisation_id);

                })
                ->join('rtc_production_processors', function ($model) {
                    $model->on('rtc_production_processors.id', '=', 'rpm_processor_follow_ups.rpm_processor_id');
                })->select([
                        'rpm_processor_follow_ups.*',
                        'rtc_production_processors.pp_id'
                    ]);

        }
        return RpmProcessorFollowUp::query()->with('processors', 'user', 'user.organisation')

            ->join('rtc_production_processors', function ($model) {
                $model->on('rtc_production_processors.id', '=', 'rpm_processor_follow_ups.rpm_processor_id');
            })->select([
                    'rpm_processor_follow_ups.*',
                    'rtc_production_processors.pp_id'
                ]);
    }

    public $namedExport = 'rpmpFU';
    #[On('export-rpmpFU')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }



    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => $model->processors->pp_id)
            ->add('rpm_processor_id')
            ->add('actor_name', function ($model) {
                $processor = $model->rpm_processor_id;
                $row = RtcProductionProcessor::find($processor);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })

            ->add('date_of_follow_up')
            ->add('date_of_follow_up_formatted', fn($model) => Carbon::parse($model->date_of_follow_up)->format('d/m/Y'))->add('market_segment_fresh', fn($model) => json_decode($model->market_segment)->fresh ?? null)
            ->add('market_segment_fresh', function ($model) {

                return $model->market_segment_fresh ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
            ->add('market_segment_processed', function ($model) {
                return $model->market_segment_processed ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')

            ->add('total_vol_production_previous_season', function ($model) {

                return $model->total_vol_production_previous_season ?? 0;
            })
            ->add('total_production_value_previous_season_total', function ($model) {

                return $model->prod_value_previous_season_total ?? 0;
            })

            ->add('total_production_value_previous_season_usd', function ($model) {

                return $model->prod_value_previous_season_usd_value ?? 0;
            })

            ->add('total_production_value_previous_season_date', function ($model) {

                return $model->prod_value_previous_season_date_of_max_sales === null ? null : Carbon::parse($model->prod_value_previous_season_date_of_max_sales)->format('d/m/Y');
            })
            ->add('usd_rate', function ($model) {

                return $model->prod_value_previous_season_usd_rate ?? 0;
            })


            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('sells_to_aggregation_centers', function ($model) {
                return $model->sells_to_aggregation_centers == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })
        ;
    }
    public function relationSearch(): array
    {
        return [
            'processors' => [
                'pp_id',
                'name_of_actor'
            ],
            'user' => [
                'name',

            ],

            'user.organisation' => [
                'name'
            ]

        ];
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }


    #[On('export-followup')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/rtc_production_and_marketing_processors_follow_up.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Processor ID',
                'Actor Name',
                'Group Name',
                'Date of Follow Up (Formatted)',
                'Market Segment Fresh',
                'Market Segment Processed',
                'Has RTC Market Contract',
                'Total Vol Production Previous Season',
                'Total Production Value Previous Season Total',
                'Total Production Value Previous Season Date',
                'Total Vol Irrigation Production Previous Season',
                'Total Irrigation Production Value Previous Season Total',
                'Total Irrigation Production Value Previous Season Date',
                'Sells to Domestic Markets',
                'Sells to International Markets',
                'Uses Market Information Systems',
                'Market Information Systems',
                'Aggregation Centers Response',
                'Aggregation Centers Specify',
                'Aggregation Center Sales',
            ]);

        // Chunk the data and process each chunk
        $chunks = array_chunk($data->all(), 1000);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $item) {
                $location_data = json_decode($item->location_data);
                $market_segment = json_decode($item->market_segment);
                $total_production_value_previous_season = json_decode($item->total_production_value_previous_season);
                $total_irrigation_production_value_previous_season = json_decode($item->total_irrigation_production_value_previous_season);
                $aggregation_centers = json_decode($item->aggregation_centers);

                $processor = $item->rpm_processor_id;
                $row = RtcProductionProcessor::find($processor);
                $actor_name = $row ? $row->name_of_actor : null;

                $row = [
                    'id' => $item->id,
                    'rpm_processor_id' => $item->rpm_processor_id,
                    'actor_name' => $actor_name,
                    'group_name' => $location_data->group_name ?? null,
                    'date_of_follow_up_formatted' => Carbon::parse($item->date_of_follow_up)->format('d/m/Y'),
                    'market_segment_fresh' => $market_segment->fresh ?? null,
                    'market_segment_processed' => $market_segment->processed ?? null,
                    'has_rtc_market_contract' => $item->has_rtc_market_contract == 1 ? 'Yes' : 'No',
                    'total_vol_production_previous_season' => $item->total_vol_production_previous_season ?? 0,
                    'total_production_value_previous_season_total' => $total_production_value_previous_season->total ?? 0,
                    'total_production_value_previous_season_date' => $total_production_value_previous_season->date_of_maximum_sales ? Carbon::parse($total_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y') : null,
                    'total_vol_irrigation_production_previous_season' => $item->total_vol_irrigation_production_previous_season ?? 0,
                    'total_irrigation_production_value_previous_season_total' => $total_irrigation_production_value_previous_season->total ?? 0,
                    'total_irrigation_production_value_previous_season_date' => $total_irrigation_production_value_previous_season->date_of_maximum_sales ? Carbon::parse($total_irrigation_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y') : null,
                    'sells_to_domestic_markets' => $item->sells_to_domestic_markets == 1 ? 'Yes' : 'No',
                    'sells_to_international_markets' => $item->sells_to_international_markets == 1 ? 'Yes' : 'No',
                    'uses_market_information_systems' => $item->uses_market_information_systems == 1 ? 'Yes' : 'No',
                    'market_information_systems' => $item->market_information_systems ?? null,
                    'aggregation_centers_response' => $aggregation_centers->response == 1 ? 'Yes' : 'No',
                    'aggregation_centers_specify' => $aggregation_centers->specify ?? null,
                    'aggregation_center_sales' => $item->aggregation_center_sales ?? null,
                ];

                $writer->addRow($row);
            }
        }

        // Close the writer and get the path of the file
        $writer->close();

        // Return the file for download
        return response()->download($path)->deleteFileAfterSend(true);
    }

    public function columns(): array
    {
        return [
            Column::make('Processor ID', 'unique_id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Actor Name', 'actor_name'),

            Column::make('Market segment (Fresh)', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment (Processed)', 'market_segment_processed')
                ->sortable()
                ->searchable(),


            Column::make('Date of follow up', 'date_of_follow_up_formatted', 'date_of_follow_up')
                ->sortable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),
            Column::make('Total production volume previous season', 'total_vol_production_previous_season', 'total_vol_production_previous_season')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total', 'total_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total production value previous season/total ($)', 'total_production_value_previous_season_usd', 'total_production_value_previous_season->total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date', 'total_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),

            Column::make('USD Rate of Production Value', 'usd_rate', 'total_production_value_previous_season->rate')
                ->sortable()
                ->searchable(),


            Column::make('Sells to domestic markets', 'sells_to_domestic_markets')
                ->sortable()
                ->searchable(),

            Column::make('Sells to international markets', 'sells_to_international_markets')
                ->sortable()
                ->searchable(),

            Column::make('Uses market information systems', 'uses_market_information_systems')
                ->sortable()
                ->searchable(),


            Column::make('Sells to aggregation centers', 'sells_to_aggregation_centers')
                ->sortable()
                ->searchable(),

            Column::make('Submitted by', 'submitted_by')

                ->searchable(),



        ];
    }

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_follow_up'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     return [
    //         Button::add('edit')
    //             ->slot('Edit: '.$row->id)
    //             ->id()
    //             ->class('pg-btn-white dark:ring-pg-primary-600 dark:border-pg-primary-600 dark:hover:bg-pg-primary-700 dark:ring-offset-pg-primary-800 dark:text-pg-primary-300 dark:bg-pg-primary-700')
    //             ->dispatch('edit', ['rowId' => $row->id])
    //     ];
    // }

    /*
    public function actionRules($row): array
    {
       return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->id === 1)
                ->hide(),
        ];
    }
    */
}
