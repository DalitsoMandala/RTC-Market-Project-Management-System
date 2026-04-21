<?php

namespace App\Livewire\tables;

use App\Models\Project;
use App\Models\SeedBeneficiary;
use App\Models\User;
use App\Traits\BatchTrait;
use App\Traits\ExportTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SeedBeneficiariesOFSPTable extends PowerGridComponent
{

    use WithExport;
    use ExportTrait;
    use BatchTrait;
    public $crop;

    protected $table_name = 'seed-distribution-register';
    public $namedExport = 'seedBeneficiaries';
    public string $tableName = 'seed-distribution-register-ofsp';
    public function __construct()
    {
        $this->excelData['crop_type'] = 'Sweet potato';
    }
    public function setUp(): array
    {


        if ($this->getBatch() && $this->getCropType() && $this->getCropType() == 'Sweet potato') {

            $this->search = $this->getBatch();
        }
        $crop = str_replace(' ', '-', strtolower($this->crop));
        return [

            Header::make()->showSearchInput()->includeViewOnTop('components.export-data'),
            Footer::make()
                ->showPerPage()
                ->pageName("{$crop}_page")
                ->showRecordCount(),
        ];
    }




    public function datasource(): Builder
    {

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;
        $query = SeedBeneficiary::query()->with('user')->where('crop', 'Sweet potato')->join('users', function ($user) {
            $user->on('users.id', '=', 'seed_beneficiaries.user_id');
        })->select([
            'seed_beneficiaries.*',
            'users.name as user_name',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
        if ($user->hasAnyRole('external')) {
            $query->where('seed_beneficiaries.organisation_id', $organisation_id);
        }
        return $query;
    }

    #[On('export-seedBeneficiaries')]
    public function startExport()
    {

        $this->execute($this->namedExport);
        $this->performExport();
    }

    #[On("download-export_{crop}")]
    public function downloadExport()
    {

        return Storage::download('public/exports/' . $this->namedExport . '_' . $this->exportUniqueId . '.xlsx');
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('district')
            ->add('epa')
            ->add('section')
            ->add('name_of_aedo')
            ->add('aedo_phone_number')
            ->add('date_formatted', fn($model) => Carbon::parse($model->date)->format('d/m/Y'))
            ->add('name_of_recipient')
            ->add('group_name')
            ->add('village')
            ->add('sex', function ($model) {
                return $model->sex === 1 ? 'Male' : 'Female';
            })
            ->add('age')
            ->add('marital_status', function ($model) {
                $status = match ($model->marital_status) {
                    1 => 'Single',
                    2 => 'Married',
                    3 => 'Widowed',
                    4 => 'Separated',
                    5 => 'Polygamy',
                    default => 'Single'
                };


                return $status;
            })
            ->add('hh_head', function ($model) {
                $head = match ($model->hh_head) {
                    1 => 'MHH',
                    2 => 'FHH',
                    3 => 'CHH',
                    default => 'MHH'
                };
                return $head;
            })
            ->add('household_size')
            ->add('children_under_5')
            ->add('variety_received')
            ->add('variety')
            ->add('bundles_received')
            ->add('phone_or_national_id')
            ->add('season_type')
            ->add('crop')
            ->add('user_id')
            ->add('user', fn($model) => $model->user->name)
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::action('')->bodyAttribute('table-sticky-col')->headerAttribute('table-sticky-col'),
            Column::make('#', 'rn')->sortable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),

            Column::make('Beneficiary ID', 'sd_id')->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('Name of recipient', 'name_of_recipient')
                ->sortable()
                ->searchable()->bodyAttribute('table-sticky-col')
                ->headerAttribute('table-sticky-col'),
            Column::make('District', 'district', 'district')
                ->sortable()
                ->searchable(),

            Column::make('Epa', 'epa')
                ->sortable()
                ->searchable(),

            Column::make('Section', 'section')
                ->sortable()
                ->searchable(),

            Column::make('Name of aedo', 'name_of_aedo')
                ->sortable()
                ->searchable(),

            Column::make('Aedo phone number', 'aedo_phone_number')
                ->sortable()
                ->searchable(),

            Column::make('Date', 'date_formatted', 'date')
                ->sortable(),



            Column::make('Group Name', 'group_name')
                ->sortable()
                ->searchable(),

            Column::make('Village', 'village')
                ->sortable()
                ->searchable(),

            Column::make('Sex', 'sex')
                ->sortable()
                ->searchable(),

            Column::make('Age', 'age')
                ->sortable()
                ->searchable(),

            Column::make('Marital status', 'marital_status')
                ->sortable()
                ->searchable(),

            Column::make('Hh head', 'hh_head')
                ->sortable()
                ->searchable(),

            Column::make('Household size', 'household_size')
                ->sortable()
                ->searchable(),

            Column::make('Children under 5', 'children_under_5')
                ->sortable()
                ->searchable(),

            Column::make('Variety received', 'variety_received')->searchable(),

            Column::make('Bundles received', 'bundles_received')
                ->sortable(),

            Column::make('Phone', 'phone_number')
                // ->sortable()
                ->searchable(),
            Column::make('National id', 'national_id')
                //  ->sortable()
                ->searchable(),
            Column::make('Season Type', 'season_type')

                ->searchable(),

            // Column::make('Crop', 'crop')
            //     ->sortable()
            //     ->searchable(),

            Column::make('Submitted by', 'user', 'user_name')->sortable()->searchable(),


        ];
    }
    public function actions($row): array
    {
        $user = User::find(auth()->user()->id);
        $project = Project::where('name', 'RTC Market')->first();
        $link = '/forms/' . str_replace(' ', '-', strtolower($project->name)) . '/' . $this->table_name . '/edit/' . $row->id . '/' . $row->uuid;


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

        return [
            Button::add('edit')
                ->render(function () use ($link) {
                    // Generate the URL in PHP first
                    return Blade::render(<<<HTML
                <a href="{{ \$link }}" class="btn btn-warning btn-sm " data-bs-title="Edit Record" ><i class="bx bx-pen"></i></a>
            HTML, ['link' => $link]);
                })
        ];
    }
    public function filters(): array
    {
        return [];
    }

    #[On('hideModal')]
    public function edit(): void
    {
        $this->refresh();
    }
}
