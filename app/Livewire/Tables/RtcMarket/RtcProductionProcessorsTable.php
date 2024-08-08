<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Query\Builder;
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
    public $routePrefix;
    public function setUp(): array
    {
        //    $this->showCheckBox();

        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data-processors'),
            Footer::make()
                ->showPerPage()
                ->pageName('processors')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return DB::table('rtc_production_processors');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('enterprise', function ($model) {
                $data = json_decode($model->location_data);
                return $data->enterprise ?? null;
            })
            ->add('district', function ($model) {
                $data = json_decode($model->location_data);
                return $data->district ?? null;
            })
            ->add('epa', function ($model) {
                $data = json_decode($model->location_data);
                return $data->epa ?? null;
            })
            ->add('section', function ($model) {
                $data = json_decode($model->location_data);
                return $data->section ?? null;
            })
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('sector')
            ->add('number_of_members_total', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                if (is_null($number_of_members)) {
                    return 0;
                }
                return ($number_of_members->female_18_35 ?? 0) +
                    ($number_of_members->male_18_35 ?? 0) +
                    ($number_of_members->male_35_plus ?? 0) +
                    ($number_of_members->female_35_plus ?? 0);
            })
            ->add('number_of_members_female_18_35', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->female_18_35 ?? 0;
            })
            ->add('number_of_members_male_18_35', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->male_18_35 ?? 0;
            })
            ->add('number_of_members_male_35_plus', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->male_35_plus ?? 0;
            })
            ->add('number_of_members_female_35_plus', function ($model) {
                $number_of_members = json_decode($model->number_of_members);
                return $number_of_members->female_35_plus ?? 0;
            })
            ->add('group')
            ->add('establishment_status')
            ->add('is_registered', function ($model) {
                return $model->is_registered == 1 ? 'Yes' : 'No';
            })
            ->add('registration_details')
            ->add('registration_details_body', fn($model) => json_decode($model->registration_details)->registration_body ?? null)
            ->add('registration_details_date', fn($model) => json_decode($model->registration_details) == null ? null : Carbon::parse(json_decode($model->registration_details)->registration_date)->format('d/m/Y'))
            ->add('registration_details_number', fn($model) => json_decode($model->registration_details)->registration_number ?? null)
            ->add('number_of_employees_formal_female_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->female_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->male_18_35 ?? 0;
            })
            ->add('number_of_employees_formal_male_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->male_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_female_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->formal->female_35_plus ?? 0;
            })
            ->add('number_of_employees_formal_total', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                if (is_null($number_of_employees) || !isset($number_of_employees->formal)) {
                    return 0;
                }
                return ($number_of_employees->formal->female_18_35 ?? 0) +
                    ($number_of_employees->formal->male_18_35 ?? 0) +
                    ($number_of_employees->formal->male_35_plus ?? 0) +
                    ($number_of_employees->formal->female_35_plus ?? 0);
            })
            ->add('number_of_employees_informal_female_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->female_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_18_35', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->male_18_35 ?? 0;
            })
            ->add('number_of_employees_informal_male_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->male_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_female_35_plus', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                return $number_of_employees->informal->female_35_plus ?? 0;
            })
            ->add('number_of_employees_informal_total', function ($model) {
                $number_of_employees = json_decode($model->number_of_employees);
                if (is_null($number_of_employees) || !isset($number_of_employees->informal)) {
                    return 0;
                }
                return ($number_of_employees->informal->female_18_35 ?? 0) +
                    ($number_of_employees->informal->male_18_35 ?? 0) +
                    ($number_of_employees->informal->male_35_plus ?? 0) +
                    ($number_of_employees->informal->female_35_plus ?? 0);
            })
            ->add('market_segment_fresh', fn($model) => json_decode($model->market_segment)->fresh ?? null)
            ->add('market_segment_processed', fn($model) => json_decode($model->market_segment)->processed ?? null)
            ->add('has_rtc_market_contract', fn($model) => $model->has_rtc_market_contract == 1 ? 'Yes' : 'No')
            ->add('total_production_value_previous_season_total', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->total ?? 0;
            })
            ->add('total_production_value_previous_season_date', function ($model) {
                $total_production_value_previous_season = json_decode($model->total_production_value_previous_season);
                return $total_production_value_previous_season->date_of_maximum_sales == null ? null : Carbon::parse($total_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y');
            })
            ->add('total_irrigation_production_value_previous_season_total', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->total ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_date', function ($model) {
                $total_irrigation_production_value_previous_season = json_decode($model->total_irrigation_production_value_previous_season);
                return $total_irrigation_production_value_previous_season->date_of_maximum_sales == null ? null : Carbon::parse($total_irrigation_production_value_previous_season->date_of_maximum_sales)->format('d/m/Y');
            })
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? 'Yes' : 'No')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? 'Yes' : 'No')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? 'Yes' : 'No')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('aggregation_centers_response', function ($model) {
                $aggregation_centers = json_decode($model->aggregation_centers);
                return $aggregation_centers->response == 1 ? 'Yes' : 'No' ?? null;
            })
            ->add('aggregation_centers_specify', function ($model) {
                $aggregation_centers = json_decode($model->aggregation_centers);
                return $aggregation_centers->specify ?? null;
            })
            ->add('aggregation_center_sales');
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
                    'id' => $item->id,
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
            Column::action('Action'),
            Column::make('Id', 'id'),

            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),
            Column::make('Enterprise', 'enterprise', 'location_data->enterprise'),
            Column::make('District', 'district', 'location_data->district')->sortable(),
            Column::make('EPA', 'epa'),
            Column::make('Section', 'section'),
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

            Column::make('Number of members/total', 'number_of_members_total')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 18-35', 'number_of_members_male_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 18-35', 'number_of_members_female_18_35')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Male 35+', 'number_of_members_male_35_plus')
                ->sortable()
                ->searchable(),
            Column::make('Number of members/Female 35+', 'number_of_members_female_35_plus')
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

            Column::make('Registration details/Body', 'registration_details_body')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/date', 'registration_details_date')
                ->sortable()
                ->searchable(),
            Column::make('Registration details/number', 'registration_details_number')
                ->sortable()
                ->searchable(),



            Column::make('Number of Employees Formal Female 18-35', 'number_of_employees_formal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 18-35', 'number_of_employees_formal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Male 35 Plus', 'number_of_employees_formal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Formal Female 35 Plus', 'number_of_employees_formal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Formal', 'number_of_employees_formal_total')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 18-35', 'number_of_employees_informal_female_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 18-35', 'number_of_employees_informal_male_18_35')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Male 35 Plus', 'number_of_employees_informal_male_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Number of Employees Informal Female 35 Plus', 'number_of_employees_informal_female_35_plus')
                ->sortable()
                ->searchable(),

            Column::make('Total Number of Employees Informal', 'number_of_employees_informal_total')
                ->sortable()
                ->searchable(),

            Column::make('Market segment/fresh', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment/processed', 'market_segment_processed')
                ->sortable()
                ->searchable(),

            Column::make('Has rtc market contract', 'has_rtc_market_contract')
                ->sortable()
                ->searchable(),


            Column::make('Total production value previous season/total', 'total_production_value_previous_season_total')
                ->sortable()
                ->searchable(),

            Column::make('Total production value previous season/date of max. sales', 'total_production_value_previous_season_date')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date')
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

            Column::make('Market information systems', 'market_information_systems')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation centers/Response', 'aggregation_centers_response')
                ->sortable()
                ->searchable(),


            Column::make('Aggregation centers/Specify', 'aggregation_centers_specify')
                ->sortable()
                ->searchable(),

            Column::make('Aggregation center sales', 'aggregation_center_sales')
                ->sortable()
                ->searchable(),




        ];
    }

    public function filters(): array
    {
        return [
            //  Filter::datepicker('date_of_recruitment'),
        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function actions($row): array
    {
        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM PROCESSORS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        $route = '' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $row->id . '';

        return [


            Button::add('add-follow-up')

                ->render(function ($model) use ($route) {
                    return Blade::render(<<<HTML
            <a  href="$route" data-bs-toggle="tooltip" data-bs-title="add follow up" class="btn btn-primary" ><i class="bx bxs-add-to-queue"></i></a>
            HTML);
                })

            ,
        ];
    }



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
