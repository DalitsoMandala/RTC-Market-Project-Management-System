<?php

namespace App\Livewire\tables;

use App\Models\Form;
use App\Models\User;
use App\Models\Submission;
use App\Helpers\TruncateText;
use Illuminate\Support\Carbon;
use App\Models\AdditionalReport;
use App\Models\Organisation;
use App\Models\ProgressSubmission;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Facades\DB;
use App\Models\ReportingPeriodMonth;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
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

final class AdditionalReportTable extends PowerGridComponent
{
    //Batch Submission Table
    use WithExport;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public $batch;
    public $row = 1;
    public string $sortField = 'id';

    public string $sortDirection = 'desc';
    public function setUp(): array
    {
        // $this->showCheckBox();
        $route = Route::current();
        $parameters = $route->parameters();
        $collection = collect($parameters);
        if ($collection->has('batch')) {
            $this->batch = $collection->get('batch');
        }
        return [
            // Exportable::make('export')
            //     ->striped()
            //     ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = ProgressSubmission::query()->where('status', 'active');

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        // if ($user->hasAnyRole('external')) {

        //     return $query->where('user_id', $user->id)->select([
        //         '*',
        //         DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        //     ]);
        // }

        return $query->select([
            'progress_submissions.*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'status')
                ->dataSource(function () {
                    $submission = Submission::select(['status'])->distinct();

                    return $submission->get();
                })
                ->optionLabel('status')
                ->optionValue('status'),


            //     Filter::inputText('batch_no_formatted', 'batch_no'),
            //      Filter::inputText('indicator')->filterRelation('period.indicator', 'indicator_name'),
            //      Filter::inputText('organisation_formatted')->filterRelation('user.organisation', 'name'),
        ];
    }
    public function relationSearch(): array
    {
        return [];
    }
    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('row_num', function () {
                return $this->row++;
            })
            ->add('batch_no')
            ->add('batch_no_formatted', function ($model) {

                return $model->batch_no;
            })
            ->add('user_id')
            ->add('username', function ($model) {
                return User::find($model->submitted_user_id)->name;
            })

            ->add('report_organisation')
            ->add('organisation_formatted', function ($model) {

                $organisation = Organisation::find($model->report_organisation_id);

                return $organisation->name;
            })
            ->add('status')

            ->add('description')

            ->add('file_link', function ($model) {

                if ($model->file_link) {
                    $html = "
                       <a class='text-success custom-tooltip' title='download file' download='{$model->file_link}' href='" . asset('/storage/imports/' . $model->file_link) . "'>

                    <div class='d-flex align-items-center'>
                        <div class='flex-shrink-0 me-2'>
                            <div class='px-2 py-1 rounded-1 bg-success-subtle'>
                                <i class='fas fa-file-excel text-success'></i>
                            </div>
                        </div>
                        <div class='flex-grow-1 fw-bolder'>
                           {$model->file_link}

                        </div>
                    </div>

                        </a>";

                    return $html;
                }

                return null;
            })
            ->add('date_of_submission', fn($model) => $model->created_at != null ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s') : null)
            ->add('updated_at');
    }


    public function columns(): array
    {

        return [
            Column::make('#', 'rn')->sortable(),
            Column::make('File', 'file_link'),

            Column::make('Batch no', 'batch_no_formatted')

                ->searchable(),

            Column::make('SUBMITTED BY', 'username')->searchable(),

            Column::make('Responsible Organisation', 'organisation_formatted')->searchable(),


            Column::make('Description', 'description')->searchable(),

            Column::make('Date of submission', 'date_of_submission', 'created_at')
                ->sortable(),
            Column::action('Action'),
            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

        ];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actions($row): array
    {

        return [


            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can(
                    allowed: (User::find(auth()->user()->id)->hasAnyRole('manager') ||
                        User::find(auth()->user()->id)->hasAnyRole('admin'))
                )
                ->tooltip('Delete Data')
                ->dispatch('deleteBatch', [
                    'id' => $row->id,
                    'name' => 'delete-progress-modal'
                ]),
        ];
    }

    public function actionRules($row): array
    {
        $user = User::find(auth()->user()->id);

        return [
            //  Hide button edit for ID 1

            // Rule::button('edit')
            //     ->when(fn($row) => $row->status === 'denied')
            //     ->disable(),

            // Rule::button('delete')
            //     ->when(fn($row) => $row->status === 'pending')
            //     ->disable(),

            // Rule::button('delete')
            //     ->when(fn($row) => !$user->hasAnyRole('manager')
            //         || !$user->hasAnyRole('admin'))
            //     ->disable(),

            // Rule::rows()
            //     ->when(fn($row) => $row->batch_no === $this->batch)
            //     ->setAttribute('class', 'table-secondary'),

        ];
    }
}
