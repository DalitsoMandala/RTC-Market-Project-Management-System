<?php

namespace App\Livewire\tables\RtcMarket;

use App\Models\User;
use App\Traits\ExportTrait;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\RtcProductionFarmer;
use App\Models\RpmFarmerConcAgreement;
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

final class RtcProductionFarmersConcAgreement extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    public bool $deferLoading = false;
    public function setUp(): array
    {


        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->includeViewOnTop('components.export-data')->showSearchInput(),
            Footer::make()
                ->showPerPage(10)
                ->pageName('contractual-agreement')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return RpmFarmerConcAgreement::query()->with('farmers')->whereHas('farmers', function ($model) use ($organisation_id) {

                $model->where('organisation_id', $organisation_id);

            });
        }
        return RpmFarmerConcAgreement::query()->with('farmers');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', fn($model) => $model->farmers->pf_id)
            ->add('rpm_farmer_id')
            ->add('actor_name', function ($model) {
                $farmer = $model->rpm_farmer_id;
                $row = RtcProductionFarmer::find($farmer);

                if ($row) {
                    return $row->name_of_actor;

                }
                return null;


            })
            ->add('date_recorded_formatted', fn($model) => Carbon::parse($model->date_recorded)->format('d/m/Y'))
            ->add('partner_name')
            ->add('country')
            ->add('date_of_maximum_sale_formatted', fn($model) => Carbon::parse($model->date_of_maximum_sale)->format('d/m/Y'))
            ->add('product_type')
            ->add('volume_sold_previous_period')
            ->add('financial_value_of_sales')
            ->add('created_at')
            ->add('submitted_by', function ($model) {
                $user = User::find($model->farmers->user_id);
                if ($user) {
                    $organisation = $user->organisation->name;
                    $name = $user->name;

                    return $name . " (" . $organisation . ")";
                }

            })
            ->add('updated_at');
    }
    protected function getDataForExport()
    {
        // Get the data as a collection
        return $this->datasource()->get();
    }
    public $namedExport = 'rpmfCA';
    #[On('export-rpmfCA')]
    public function startExport()
    {
        $this->execute($this->namedExport);
        $this->performExport();

    }



    public function downloadExport()
    {
        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }


    public function relationSearch(): array
    {

        return [
            'farmers' => [ // relationship on dishes model


                'name_of_actor',
                'pf_id'

            ],
            // 'user' => [
            //     'name',

            // ],

            // 'user.organisation' => [
            //     'name'
            // ]
        ];
    }

    public function columns(): array
    {
        return [

            Column::make('Actor ID', 'unique_id')->searchable(),
            Column::make('Actor Name', 'actor_name'),

            Column::make('Date recorded', 'date_recorded_formatted', 'date_recorded')
                ->sortable(),

            Column::make('Partner name', 'partner_name')
                ->sortable()
                ->searchable(),

            Column::make('Country', 'country')
                ->sortable()
                ->searchable(),

            Column::make('Date of maximum sale', 'date_of_maximum_sale_formatted', 'date_of_maximum_sale')
                ->sortable(),

            Column::make('Product type', 'product_type')
                ->sortable()
                ->searchable(),

            Column::make('Volume sold previous period', 'volume_sold_previous_period')
                ->sortable()
                ->searchable(),

            Column::make('Financial value of sales', 'financial_value_of_sales')
                ->sortable()
                ->searchable(),

            // Column::make('Submitted by', 'submitted_by')

            //     ->searchable(),

        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datepicker('date_recorded'),
            // Filter::datepicker('date_of_maximum_sale'),
        ];
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
