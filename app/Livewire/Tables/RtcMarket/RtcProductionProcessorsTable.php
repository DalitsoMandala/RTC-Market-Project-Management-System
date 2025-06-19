<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use App\Models\RtcProductionProcessor;
use App\Traits\UITrait;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Spatie\SimpleExcel\SimpleExcelWriter;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RtcProductionProcessorsTable extends PowerGridComponent
{
    use WithExport;

    use ExportTrait;
    use UITrait;
    public $routePrefix;
    public function setUp(): array
    {
        //    $this->showCheckBox();
        // $columns = $this->columns();
        // $getMap = [];
        // foreach ($columns as $column) {
        //     $getMap[$column->title] = $column->dataField;
        // }
        // dd($getMap);
        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(10)
                ->pageName('processors')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        //  $this->showCheckBox();

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = RtcProductionProcessor::query()->with([

            'user',
            'user.organisation'
        ])->select([
            'rtc_production_processors.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {

            return $query->where('organisation_id', $organisation_id);
        }
        return $query;
    }


    public $namedExport = 'rpmp';
    #[On('export-rpmp')]
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

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('pp_id')
            ->add('enterprise')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('date_of_followup_formatted', fn($model) => $model->date_of_followup === null ? null : Carbon::parse($model->date_of_followup)->format('d/m/Y'))
            ->add('has_rtc_market_contract', fn($model) => $this->booleanUI($model->has_rtc_market_contract, $model->has_rtc_market_contract == 1, true))
            ->add('total_vol_production_previous_season', function ($model) {

                return $model->total_vol_production_previous_season ?? 0;
            })
            ->add('total_production_value_previous_season_total', function ($model) {

                return $model->prod_value_previous_season_total ?? 0;
            })

            ->add('total_production_value_previous_season_usd', function ($model) {

                return $model->prod_value_previous_season_usd_value ?? 0;
            })
            ->add('market_segment_fresh', function ($model) {

                return $this->booleanUI($model->market_segment_fresh, $model->market_segment_fresh == 1, true);
            })
            ->add('market_segment_processed', function ($model) {
                return $this->booleanUI($model->market_segment_processed, $model->market_segment_processed == 1, true);
            })

            ->add('total_vol_production_previous_season', fn($model) => $model->total_vol_production_previous_season ?? null)
            ->add('total_vol_production_previous_season_produce', fn($model) => $model->total_vol_production_previous_season_produce ?? null)
            ->add('total_vol_production_previous_season_seed', fn($model) => $model->total_vol_production_previous_season_seed ?? null)
            ->add('total_vol_production_previous_season_cuttings', fn($model) => $model->total_vol_production_previous_season_cuttings ?? null)
            ->add('total_vol_production_previous_season_seed_bundle', fn($model) => $model->total_vol_production_previous_season_seed_bundle ?? null)
            ->add('prod_value_previous_season_total', fn($model) => $model->prod_value_previous_season_total ?? null)
            ->add('prod_value_previous_season_produce', fn($model) => $model->prod_value_previous_season_produce ?? null)
            ->add('prod_value_previous_season_seed', fn($model) => $model->prod_value_previous_season_seed ?? null)
            ->add('prod_value_previous_season_seed_bundle', fn($model) => $model->prod_value_previous_season_seed_bundle ?? null)
            ->add('prod_value_previous_season_cuttings', fn($model) => $model->prod_value_previous_season_cuttings ?? null)
            ->add('prod_value_produce_prevailing_price', fn($model) => $model->prod_value_produce_prevailing_price ?? null)
            ->add('prod_value_seed_prevailing_price', fn($model) => $model->prod_value_seed_prevailing_price ?? null)
            ->add('prod_value_cuttings_prevailing_price', fn($model) => $model->prod_value_cuttings_prevailing_price ?? null)
            ->add('prod_value_previous_season_usd_rate', fn($model) => $model->prod_value_previous_season_usd_rate ?? null)
            ->add('prod_value_previous_season_usd_value', fn($model) => $model->prod_value_previous_season_usd_value ?? null)

            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('sells_to_domestic_markets', fn($model) => $this->booleanUI(
                $model->sells_to_domestic_markets,
                $model->sells_to_domestic_markets == 1,
                true
            ))
            ->add('sells_to_international_markets', fn($model) => $this->booleanUI(
                $model->sells_to_international_markets,
                $model->sells_to_international_markets == 1,
                true
            ))
            ->add('uses_market_information_systems', fn($model) => $this->booleanUI(
                $model->uses_market_information_systems,
                $model->uses_market_information_systems == 1,
                true
            ))

            ->add('total_vol_aggregation_center_sales')

            ->add('sells_to_aggregation_centers', function ($model) {
                return $this->booleanUI(
                    $model->sells_to_aggregation_centers,
                    $model->sells_to_aggregation_centers == 1,
                    true
                );
            })


            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }
            })

            ->add('submitted_by', function ($model) {
                $user = User::find($model->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }
            })
            // ->add('aggregation_centers_specify', function ($model) {
            //     $aggregation_centers = json_decode($model->aggregation_centers);
            //     return $aggregation_centers->specify ?? null;
            // })
            // ->add('aggregation_center_sales');

        ;
    }


    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }



    public function columns(): array
    {
        return [
            // Column::action('Action'),
            Column::make('ID', 'rn')->sortable(),
            Column::make('Processor ID', 'pp_id')->searchable()
                ->sortable(),
            Column::make('Date of follow up', 'date_of_followup_formatted')
                ->sortable()->searchable(),
            Column::make('Enterprise', 'enterprise',)->searchable()->sortable(),
            Column::make('District', 'district',)->sortable()->searchable(),
            Column::make('EPA', 'epa')->sortable()->searchable(),
            Column::make('Section', 'section')->sortable()->searchable(),
            Column::make('Market segment (Fresh)', 'market_segment_fresh')
                ->sortable(),
            Column::make('Market segment (Processed)', 'market_segment_processed')
                ->sortable(),
            Column::make('Has RTC Contractual Agreement', 'has_rtc_market_contract', 'has_rtc_market_contract')
                ->sortable(),
            Column::make('Volume of production (Produce)', 'total_vol_production_previous_season_produce'),
            Column::make('Volume of production (Seed)', 'total_vol_production_previous_season_seed'),
            Column::make('Volume of production (Cuttings)', 'total_vol_production_previous_season_cuttings'),
            Column::make('Volume of production Seed Bundle', 'total_vol_production_previous_season_seed_bundle'),
            Column::make('Total volume of production (Metric Tonnes)', 'total_vol_production_previous_season'),
            Column::make('Value of Production (Produce)', 'prod_value_previous_season_produce'),
            Column::make('Value of Production (Produce Prevailing Price)', 'prod_value_produce_prevailing_price'),
            Column::make('Value of Production (Seed)', 'prod_value_previous_season_seed'),
            Column::make('Value of Production (Seed Prevailing Price)', 'prod_value_seed_prevailing_price'),
            Column::make('Value of Production (Cuttings)', 'prod_value_previous_season_cuttings'),
            Column::make('Value of Production (Cuttings Prevailing Price)', 'prod_value_cuttings_prevailing_price'),
            Column::make('Value of Production Seed Bundle', 'prod_value_previous_season_seed_bundle'),
            Column::make('Total Value of Production (Metric Tonnes)', 'prod_value_previous_season_total'),
            Column::make('Total Value of Production (USD Rate)', 'prod_value_previous_season_usd_rate'),
            Column::make('Total Value of Production (USD)', 'prod_value_previous_season_usd_value'),
            Column::make('Sells to domestic markets', 'sells_to_domestic_markets')
                ->sortable(),

            Column::make('Sells to international markets', 'sells_to_international_markets')
                ->sortable(),

            Column::make('Uses market information systems', 'uses_market_information_systems')
                ->sortable(),


            Column::make('Sells to aggregation centers', 'sells_to_aggregation_centers')
                ->sortable(),

            Column::make('Total Volume of Aggregation Center Sales', 'total_vol_aggregation_center_sales')
                ->sortable(),

            Column::make('Submitted by', 'submitted_by')

                ->searchable(),





        ];
    }

    public function filters(): array
    {
        return [];
    }


    #[On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS AND TRADERS')->first();

    //     $form_name = str_replace(' ', '-', strtolower($form->name));
    //     $project = str_replace(' ', '-', strtolower($form->project->name));

    //     $route = '' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $row->id . '';

    //     return [


    //         Button::add('add-follow-up')

    //             ->render(function ($model) use ($route) {
    //                 return Blade::render(<<<HTML
    //         <a  href="$route" data-bs-toggle="tooltip" data-bs-title="add follow up" class="btn btn-warning" ><i class="bx bxs-add-to-queue"></i></a>
    //         HTML);
    //             })

    //         ,
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
