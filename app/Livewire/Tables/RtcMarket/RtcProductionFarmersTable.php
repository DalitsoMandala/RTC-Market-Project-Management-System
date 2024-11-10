<?php

namespace App\Livewire\Tables\RtcMarket;

use App\Models\Form;
use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use App\Models\RpmFarmerFollowUp;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Storage;
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
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

final class RtcProductionFarmersTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public $routePrefix;
    public bool $deferLoading = false;
    public function setUp(): array
    {
        //  $this->showCheckBox();

        return [

            Header::make()->includeViewOnTop('components.export-data')
                ->showSearchInput()
            //     ->includeViewOnTop('components.export-data-farmers')
            ,
            Footer::make()
                ->showPerPage(5)
                ->pageName('farmers')
                ->showRecordCount(),
        ];
    }

    public function datasource(): EloquentBuilder
    {
        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RtcProductionFarmer::query()->with([
                'user',
                'user.organisation'
            ])->where('organisation_id', $organisation_id);

        }

        return RtcProductionFarmer::query()->with([

            'user',
            'user.organisation'
        ]);


    }


    public $namedExport = 'rpmf';
    #[On('export-rpmf')]
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
            ->add('unique_id', fn($model) => $model->pf_id)
            ->add('date_of_recruitment_formatted', fn($model) => Carbon::parse($model->date_of_recruitment)->format('d/m/Y'))
            ->add('name_of_actor')
            ->add('name_of_representative')
            ->add('phone_number')
            ->add('type')
            ->add('approach')
            ->add('enterprise', function ($model) {

                return $model->enterprise;
            })
            ->add('district', function ($model) {

                return $model->district;
            })
            ->add('epa', function ($model) {

                return $model->epa;
            })
            ->add('section', function ($model) {

                return $model->section;
            })
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
            ->add('area_under_cultivation_total', function ($model) {

                return $model->cultivatedArea()->sum('area') ?? 0;
            })

            ->add('number_of_plantlets_produced_potato', function ($model) {

                return $model->number_of_plantlets_produced_potato ?? 0;
            })
            ->add('number_of_plantlets_produced_cassava', function ($model) {

                return $model->number_of_plantlets_produced_cassava;
            })
            ->add('number_of_plantlets_produced_sw_potato', function ($model) {

                return $model->number_of_plantlets_produced_sweet_potato;
            })
            ->add('number_of_screen_house_vines_harvested', fn($model) => $model->number_of_screen_house_vines_harvested ?? 0)
            ->add('number_of_screen_house_min_tubers_harvested', fn($model) => $model->number_of_screen_house_min_tubers_harvested ?? 0)
            ->add('number_of_sah_plants_produced', fn($model) => $model->number_of_sah_plants_produced ?? 0)


            ->add('is_registered_seed_producer', fn($model) => $model->is_registered_seed_producer == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('seed_service_unit_registration_details_date', function ($model) {


                if (is_null($model->registration_date_seed_producer)) {
                    return null;
                }

                return Carbon::parse($model->registration_date_seed_producer)->format('d/m/Y');

            })
            ->add('seed_service_unit_registration_details_number', fn($model) => $model->registration_number_seed_producer ?? null)
            ->add('uses_certified_seed', fn($model) => $model->uses_certified_seed == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
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

            ->add('total_vol_irrigation_production_previous_season', function ($model) {

                return $model->total_vol_irrigation_production_previous_season ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_total', function ($model) {
                return $model->irr_prod_value_previous_season_total ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_usd', function ($model) {
                return $model->irr_prod_value_previous_season_usd_value ?? 0;
            })
            ->add('total_irrigation_production_value_previous_season_date', function ($model) {
                return $model->irr_prod_value_previous_season_date_of_max_sales === null ? null : Carbon::parse($model->irr_prod_value_previous_season_date_of_max_sales)->format('d/m/Y');
            })

            ->add('usd_rate_irrigation', function ($model) {

                return $model->irr_prod_value_previous_season_usd_rate ?? 0;
            })
            ->add('sells_to_domestic_markets', fn($model) => $model->sells_to_domestic_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('sells_to_international_markets', fn($model) => $model->sells_to_international_markets == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('uses_market_information_systems', fn($model) => $model->uses_market_information_systems == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>')
            ->add('market_information_systems', fn($model) => $model->market_information_systems ?? null)
            ->add('sells_to_aggregation_centers', function ($model) {
                return $model->sells_to_aggregation_centers == 1 ? '<i class="bx bx-check text-success fs-4"></i>' : '<i class="bx bx-x text-danger fs-4"></i>';
            })
            ->add('area_basic_seed', function ($model) {

                return $model->basicSeed()->sum('area') ?? 0;
            })

            ->add('area_certified_seed', function ($model) {

                return $model->certifiedSeed()->sum('area') ?? 0;
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


    public function columns(): array
    {
        return [

            Column::make('Actor ID', 'unique_id', 'pf_id')->sortable()->searchable(),
            Column::make('Date of recruitment', 'date_of_recruitment_formatted', 'date_of_recruitment')
                ->sortable()->searchable(),

            Column::make('Name of actor', 'name_of_actor')
                ->sortable()
                ->searchable(),

            Column::make('Enterprise', 'enterprise')->sortable()->searchable(),
            Column::make('District', 'district')->sortable()->searchable(),
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



            Column::make('Number of plantlets produced/cassava', 'number_of_plantlets_produced_cassava', 'number_of_plantlets_produced->cassava')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/potato', 'number_of_plantlets_produced_potato', 'number_of_plantlets_produced->potato')
                ->sortable()
                ->searchable(),
            Column::make('Number of plantlets produced/sweet potato', 'number_of_plantlets_produced_sw_potato', 'number_of_plantlets_produced->sweet_potato')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house vines harvested', 'number_of_screen_house_vines_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of screen house min tubers harvested', 'number_of_screen_house_min_tubers_harvested')
                ->sortable()
                ->searchable(),

            Column::make('Number of sah plants produced', 'number_of_sah_plants_produced')
                ->sortable()
                ->searchable(),

            Column::make('Area under basic seed multiplication/total', 'area_basic_seed')
                ->sortable()
                ->searchable(),


            Column::make('Area under certified seed multiplication/total', 'area_certified_seed')
                ->sortable()
                ->searchable(),

            Column::make('Is registered seed producer', 'is_registered_seed_producer')
                ->sortable()
                ->searchable(),


            Column::make('Uses certified seed', 'uses_certified_seed')
                ->sortable()
                ->searchable(),


            Column::make('Market segment (Fresh)', 'market_segment_fresh')
                ->sortable()
                ->searchable(),


            Column::make('Market segment (Processed)', 'market_segment_processed')
                ->sortable()
                ->searchable(),


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


            Column::make('Total irrigation production value previous season/total', 'total_irrigation_production_value_previous_season_total', 'total_irrigation_production_value_previous_season->value')
                ->sortable()
                ->searchable(),
            Column::make('Total irrigation production value previous season/total ($)', 'total_irrigation_production_value_previous_season_usd', 'total_irrigation_production_value_previous_season->total')
                ->sortable()
                ->searchable(),


            Column::make('Total irrigation production value previous season/date of max. sales', 'total_irrigation_production_value_previous_season_date', 'total_irrigation_production_value_previous_season->date_of_maximum_sales')
                ->sortable()
                ->searchable(),


            Column::make('USD Rate of irrigation Production Value', 'usd_rate_irrigation', 'total_irrigation_production_value_previous_season->rate')
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

    public function relationSearch(): array
    {
        return [

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



    public function filters(): array
    {
        return [

        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
    }

    public function openModal($id)
    {

        $form = Form::where('name', 'RTC PRODUCTION AND MARKETING FORM FARMERS')->first();

        $form_name = str_replace(' ', '-', strtolower($form->name));
        $project = str_replace(' ', '-', strtolower($form->project->name));

        return redirect()->to('' . $this->routePrefix . '/forms/' . $project . '/' . $form_name . '/followup/' . $id . '');
    }




    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            // Rule::button('add-follow-up')
            //     ->when(fn($row) => !(RpmFarmerFollowUp::find($row->id)))
            //     ->hide(),
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'your-custom-table-id',  // Set your custom HTML ID here
            // You can add other HTML attributes as needed
        ];
    }

}
