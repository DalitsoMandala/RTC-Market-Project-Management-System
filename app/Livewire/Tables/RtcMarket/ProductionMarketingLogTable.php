<?php
namespace App\Livewire\tables\RtcMarket;

use App\Models\ProductionMarketingLog;
use App\Models\Project;
use App\Models\User;
use App\Traits\BatchTrait;
use App\Traits\ExportTrait;
use App\Traits\UITrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class ProductionMarketingLogTable extends PowerGridComponent
{
    use WithExport;
    use ExportTrait;
    use UITrait;
    use BatchTrait;
    public bool $multiSort = true;
    public $namedExport    = 'production_marketing';
    public function setUp(): array
    {
        // $this->showCheckBox();
        if ($this->getBatch()) {
            $this->search = $this->getBatch();
        }
        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage(10)
                ->pageName('production_marketing')
                ->showRecordCount(),
        ];
    }

    #[On('export-production_marketing')]
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

    public function actions($row): array
    {
        $user    = User::find(auth()->user()->id);
        $project = Project::where('name', 'RTC Market')->first();
        $link    = '/forms/' . str_replace(' ', '-', strtolower($project->name)) . '/production-and-marketing-log-form/edit/' . $row->id . '/' . $row->uuid;

        switch ($user->roles()->first()->name) {
            case 'admin':
                $link = "/admin" . $link;
                break;

            case 'staff':
                $link = "/staff" . $link;
                break;
            case 'manager':
                $link = "/manager" . $link;
                break;

            case 'monitor':
                $link = "/monitor" . $link;
                break;
        }

        // Define the user variable once to avoid repeated calls
        $user = auth()->user();

        // Use dedicated role checks
        $isAdmin = $user->hasAnyRole(['admin', 'manager']);
        $isStaff = $user->hasAnyRole('staff');

        // Logic for staff permission (Only their own records)
        $canEdit = $isAdmin || ($isStaff && $user->id === $row->user_id);

        return [
            Button::add('edit')
                ->can($canEdit)
                ->render(function () use ($link) {
                    // Using a simple return is faster than compiling a Blade view for a single button
                    return '<a href="' . $link . '" class="btn btn-warning btn-sm" title="Edit Record"><i class="bx bx-pen"></i></a>';
                }),
        ];
    }

    public function datasource(): Builder
    {
        $user            = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query           = ProductionMarketingLog::query()->with(
            [
                'user' => fn($q) => $q->withTrashed(),
                'user.organisation',
            ]
        )->select([
            'production_marketing_logs.*',
            DB::raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
        ]);
        if ($user->hasAnyRole('external')) {
            return $query->where('organisation_id', $organisation_id);
        }
        return $query;

    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('prod_market_id')

            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('enterprise')
            ->add('group_name')
            ->add('type_of_farming')
            ->add('season')
            ->add('group_chair_name')
            ->add('group_chair_contact')
            ->add('farmer_name')
            ->add('farmer_id_phone')
            ->add('sex')
            ->add('age')
            ->add('area_grown_acre')
            ->add('variety', fn($row) => ucfirst(trim($row->variety))) //$row->variety
            ->add('harvesting_units')
            ->add('unit_weight_kg')
            ->add('qty')
            ->add('selling_price')
            ->add('main_buyer')
            ->add('uuid')
            ->add('user_id')
            ->add('submission_period_id')
            ->add('organisation_id')
            ->add('financial_year_id')
            ->add('period_month_id')
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::action('')->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('#', 'rn')->sortable()->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Farmer ID', 'prod_market_id')
                ->sortable()->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Group name', 'group_name')
                ->sortable()
                ->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),

            Column::make('UUID', 'uuid')->searchable()->hidden(),
            Column::make('District', 'district')
                ->sortable()
                ->searchable(),

            Column::make('Epa', 'epa')
                ->sortable()
                ->searchable(),

            Column::make('Section', 'section')
                ->sortable()
                ->searchable(),

            Column::make('Enterprise', 'enterprise')
                ->sortable()
                ->searchable(),

            Column::make('Type of farming', 'type_of_farming')
                ->sortable()
                ->searchable(),

            Column::make('Season', 'season')
                ->sortable()
                ->searchable(),

            Column::make('Group chair name', 'group_chair_name')
                ->sortable()
                ->searchable(),

            Column::make('Group chair contact', 'group_chair_contact')
                ->sortable()
                ->searchable(),

            Column::make('Farmer name', 'farmer_name')
                ->sortable()
                ->searchable(),

            Column::make('Farmer id phone', 'farmer_id_phone')
                ->sortable()
                ->searchable(),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable(),

            Column::make('Age', 'age')
                ->sortable()
                ->searchable(),

            Column::make('Area grown acre', 'area_grown_acre')
                ->sortable()
                ->searchable(),

            Column::make('Variety', 'variety')
                ->sortable()
                ->searchable(),

            Column::make('Harvesting units', 'harvesting_units')
                ->sortable()
                ->searchable(),

            Column::make('Unit weight kg', 'unit_weight_kg')
                ->sortable()
                ->searchable(),

            Column::make('Qty', 'qty')
                ->sortable()
                ->searchable(),

            Column::make('Selling price', 'selling_price')
                ->sortable()
                ->searchable(),

            Column::make('Main buyer', 'main_buyer')
                ->sortable()
                ->searchable(),

        ];
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

    public function actionRules($row): array
    {
        return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => true)
                ->disable(),
        ];
    }

}
