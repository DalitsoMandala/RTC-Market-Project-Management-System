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
    public $routePrefix;
    public function setUp(): array
    {
        //    $this->showCheckBox();

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

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RtcProductionProcessor::query()->with([

                'user',
                'user.organisation'
            ])->where('organisation_id', $organisation_id);

        }
        return RtcProductionProcessor::query()->with([

            'user',
            'user.organisation'
        ]);
    }


    public $namedExport = 'rpmp';
    #[On('export-rpmp')]
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
            ->add('pp_id')
            ->add('enterprise')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('sector')
            ->add('number_of_members_total', function ($model) {



                return ($model->mem_female_18_35 ?? 0) +
                    ($model->mem_male_18_35 ?? 0) +
                    ($model->mem_male_35_plus ?? 0) +
                    ($model->mem_female_35_plus ?? 0);
            })
            ->add('number_of_members_female_18_35', function ($model) {

                return $model->mem_female_18_35 ?? 0;
            })
            ->add('number_of_members_male_18_35', function ($model) {

                return $model->mem_male_18_35 ?? 0;
            })
            ->add('number_of_members_male_35_plus', function ($model) {

                return $model->mem_male_35_plus ?? 0;
            })
            ->add('number_of_members_female_35_plus', function ($model) {

                return $model->mem_female_35_plus ?? 0;
            })
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Registered' : 'Not registered';
            })

            ->add('registration_body')
            ->add('registration_date', function ($model) {

                if (is_null($model->registration_date)) {
                    return null;
                }

                return Carbon::parse($model->registration_date)->format('d/m/Y');
            })
            ->add('registration_number')
            ->add('number_of_employees_formal_female_18_35', function ($model) {

                return $model->emp_formal_female_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_18_35', function ($model) {

                return $model->emp_formal_male_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_35_plus', function ($model) {

                return $model->emp_formal_male_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_female_35_plus', function ($model) {

                return $model->emp_formal_female_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_total', function ($model) {



                return ($model->emp_formal_female_18_35 ?? 0) +
                    ($model->emp_formal_male_18_35 ?? 0) +
                    ($model->emp_formal_male_35_plus ?? 0) +
                    ($model->emp_formal_female_35_plus ?? 0);
            })
            ->add('number_of_employees_informal_female_18_35', function ($model) {

                return $model->emp_informal_female_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_18_35', function ($model) {

                return $model->emp_informal_male_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_35_plus', function ($model) {

                return $model->emp_informal_male_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_female_35_plus', function ($model) {

                return $model->emp_informal_female_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_total', function ($model) {



                return ($model->emp_informal_female_18_35 ?? 0) +
                    ($model->emp_informal_male_18_35 ?? 0) +
                    ($model->emp_informal_male_35_plus ?? 0) +
                    ($model->emp_informal_female_35_plus ?? 0);
            })

            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')
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

                return $model->market_segment_fresh ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
            ->add('market_segment_processed', function ($model) {
                return $model->market_segment_processed ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
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

    #[On('export-processors')]
    public function export()
    {
        // Get data for export
        $data = $this->getDataForExport();

        // Define the path for the Excel file
        $path = storage_path('app/public/rtc_production_and_marketing_processors.xlsx');

        // Create the writer and add the header
        $writer = SimpleExcelWriter::create($path)
            ->addHeader([
                'Id',
                'Enterprise',
                'District',
                'EPA',
                'Section',
                'Date of Recruitment (Formatted)',
                'Name of Actor',
                'Name of Representative',
                'Phone Number',
                'Type',
                'Approach',
                'Sector',
                'Number of Members Total',
                'Number of Members Female 18-35',
                'Number of Members Male 18-35',
                'Number of Members Male 35 Plus',
                'Number of Members Female 35 Plus',
                'Group',
                'Establishment Status',
                'Is Registered',

                'Registration Details Body',
                'Registration Details Date',
                'Registration Details Number',
                'Number of Employees Formal Female 18-35',
                'Number of Employees Formal Male 18-35',
                'Number of Employees Formal Male 35 Plus',
                'Number of Employees Formal Female 35 Plus',
                'Number of Employees Formal Total',
                'Number of Employees Informal Female 18-35',
                'Number of Employees Informal Male 18-35',
                'Number of Employees Informal Male 35 Plus',
                'Number of Employees Informal Female 35 Plus',
                'Number of Employees Informal Total',
                'Market Segment Fresh',
                'Market Segment Processed',
                'Has RTC Market Contract',
                'Total Production Value Previous Season Total',
                'Total Production Value Previous Season Date',
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
                $number_of_members = json_decode($item->number_of_members);
                $number_of_employees = json_decode($item->number_of_employees);
                $registration_details = json_decode($item->registration_details);
                $market_segment = json_decode($item->market_segment);
                $aggregation_centers = json_decode($item->aggregation_centers);
                $total_production_value_previous_season = json_decode($item->total_production_value_previous_season);
                $total_irrigation_production_value_previous_season = json_decode($item->total_irrigation_production_value_previous_season);

                $row = [
                    'id' => str_pad($item->id, 5, '0', STR_PAD_LEFT),
                    'enterprise' => $location_data->enterprise ?? null,
                    'district' => $location_data->district ?? null,
                    'epa' => $location_data->epa ?? null,
                    'section' => $location_data->section ?? null,
                    'date_of_recruitment_formatted' => Carbon::parse($item->date_of_recruitment)->format('d/m/Y'),
                    'name_of_actor' => $item->name_of_actor,
                    'name_of_representative' => $item->name_of_representative,
                    'phone_number' => $item->phone_number,
                    'type' => $item->type,
                    'approach' => $item->approach,
                    'sector' => $item->sector,
                    'number_of_members_total' => ($number_of_members->female_18_35 ?? 0) + ($number_of_members->male_18_35 ?? 0) + ($number_of_members->male_35_plus ?? 0) + ($number_of_members->female_35_plus ?? 0),
                    'number_of_members_female_18_35' => $number_of_members->female_18_35 ?? 0,
                    'number_of_members_male_18_35' => $number_of_members->male_18_35 ?? 0,
                    'number_of_members_male_35_plus' => $number_of_members->male_35_plus ?? 0,
                    'number_of_members_female_35_plus' => $number_of_members->female_35_plus ?? 0,
                    'group' => $item->group,
                    'establishment_status' => $item->establishment_status,
                    'is_registered' => $item->is_registered == 1 ? 'Yes' : 'No',

                    'registration_details_body' => $registration_details->registration_body ?? null,
                    'registration_details_date' => $registration_details->registration_date ?? null,
                    'registration_details_number' => $registration_details->registration_number ?? null,
                    'number_of_employees_formal_female_18_35' => $number_of_employees->formal->female_18_35 ?? 0,
                    'number_of_employees_formal_male_18_35' => $number_of_employees->formal->male_18_35 ?? 0,
                    'number_of_employees_formal_male_35_plus' => $number_of_employees->formal->male_35_plus ?? 0,
                    'number_of_employees_formal_female_35_plus' => $number_of_employees->formal->female_35_plus ?? 0,
                    'number_of_employees_formal_total' => ($number_of_employees->formal->female_18_35 ?? 0) + ($number_of_employees->formal->male_18_35 ?? 0) + ($number_of_employees->formal->male_35_plus ?? 0) + ($number_of_employees->formal->female_35_plus ?? 0),
                    'number_of_employees_informal_female_18_35' => $number_of_employees->informal->female_18_35 ?? 0,
                    'number_of_employees_informal_male_18_35' => $number_of_employees->informal->male_18_35 ?? 0,
                    'number_of_employees_informal_male_35_plus' => $number_of_employees->informal->male_35_plus ?? 0,
                    'number_of_employees_informal_female_35_plus' => $number_of_employees->informal->female_35_plus ?? 0,
                    'number_of_employees_informal_total' => ($number_of_employees->informal->female_18_35 ?? 0) + ($number_of_employees->informal->male_18_35 ?? 0) + ($number_of_employees->informal->male_35_plus ?? 0) + ($number_of_employees->informal->female_35_plus ?? 0),
                    'market_segment_fresh' => $market_segment->fresh ?? null,
                    'market_segment_processed' => $market_segment->processed ?? null,
                    'has_rtc_market_contract' => $item->has_rtc_market_contract == 1 ? 'Yes' : 'No',
                    'total_production_value_previous_season_total' => $total_production_value_previous_season->total ?? 0,
                    'total_production_value_previous_season_date' => $total_production_value_previous_season->date_of_maximum_sales ?? null,
                    'total_irrigation_production_value_previous_season_total' => $total_irrigation_production_value_previous_season->total ?? 0,
                    'total_irrigation_production_value_previous_season_date' => $total_irrigation_production_value_previous_season->date_of_maximum_sales ?? null,
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
            // Column::action('Action'),

            Column::make('Processor ID', 'pp_id')->searchable()
                ->sortable(),

            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable()->searchable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),
            Column::make('Enterprise', 'enterprise', )->searchable()->sortable(),
            Column::make('District', 'district', )->sortable()->searchable(),
            Column::make('EPA', 'epa')->sortable()->searchable(),
            Column::make('Section', 'section')->sortable()->searchable(),
            Column::make('Name of representative', 'name_of_representative')
                ->sortable()
                ->searchable(),

            Column::make('Phone number', 'phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Type', 'type')
                ->sortable()
                ->searchable(),

            Column::make('Approach', 'approach')
                ->sortable()
                ->searchable(),

            Column::make('Sector', 'sector')
                ->sortable()
                ->searchable(),

            Column::make('Number of members/Male 18-35', 'number_of_members_male_18_35', 'number_of_members->male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 18-35', 'number_of_members_female_18_35', 'number_of_members->female_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 35+', 'number_of_members_male_35_plus', 'number_of_members->male_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 35+', 'number_of_members_female_35_plus', 'number_of_members->female_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/total', 'number_of_members_total', 'number_of_members->total')
                ->sortable()
                ->searchable(),

            Column::make('Group', 'group')
                ->sortable()
                ->searchable(),

            Column::make('Establishment status', 'establishment_status')
                ->sortable()
                ->searchable(),

            Column::make('Is registered', 'is_registered')
                ->sortable()
                ->searchable(),

            Column::make('Registration body', 'registration_body')
                ->sortable()
                ->searchable(),
            Column::make('Registration date', 'registration_date')
                ->sortable()
                ->searchable(),
            Column::make('Registration number', 'registration_number')
                ->sortable()
                ->searchable(),



            Column::make('Number of Employees Formal Female 18-35', 'number_of_employees_formal_female_18_35', 'number_of_employees->emp_formal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 18-35', 'number_of_employees_formal_male_18_35', 'number_of_employees->emp_formal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 35 Plus', 'number_of_employees_formal_male_35_plus', 'number_of_employees->emp_formal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Female 35 Plus', 'number_of_employees_formal_female_35_plus', 'number_of_employees->emp_formal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Formal', 'number_of_employees_formal_total', 'number_of_employees->emp_formal_total')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 18-35', 'number_of_employees_informal_female_18_35', 'number_of_employees->emp_informal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 18-35', 'number_of_employees_informal_male_18_35', 'number_of_employees->emp_informal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 35 Plus', 'number_of_employees_informal_male_35_plus', 'number_of_employees->emp_informal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 35 Plus', 'number_of_employees_informal_female_35_plus', 'number_of_employees->emp_informal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Informal', 'number_of_employees_informal_total', 'number_of_employees->emp_informal_total')
                ->sortable()
                ->searchable(),


            Column::make('Has RTC Contractual Agreement', 'has_rtc_market_contract', 'has_rtc_market_contract')
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
            Column::make('Market segment (Fresh)', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment (Processed)', 'market_segment_processed')
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

        ];
    }


    #[On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    // public function actions($row): array
    // {
    //     $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS')->first();

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
