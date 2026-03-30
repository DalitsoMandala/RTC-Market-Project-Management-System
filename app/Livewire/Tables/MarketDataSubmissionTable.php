<?php

namespace App\Livewire\tables;

use App\Helpers\TruncateText;
use App\Models\MarketDataSubmission;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Jantinnerezo\LivewireAlert\LivewireAlert;
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

final class MarketDataSubmissionTable extends PowerGridComponent
{
    use WithExport;
    //Batch Submission Table
use LivewireAlert;
    public $filter;
    public $userId;
    public bool $showFilters = true;
    public $batch;
    public $row = 1;
    public string $sortField = 'id';
public $routePrefix;
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
            Header::make()->showSearchInput()->showToggleColumns(),
            Footer::make()
                ->showPerPage()->pageName('marketDataPage')
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $query = MarketDataSubmission::query()->with(['user.organisation', 'user']);

        $user = User::find(auth()->user()->id);
        $organisation_id = $user->organisation->id;

        if ($user->hasAnyRole('external')) {

            return $query->where('submitted_user_id', $user->id)->select([
                '*',
                DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
            ]);
        }

        return $query->select([
            '*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn')
        ]);
    }

    public function filters(): array
    {
        return [
            Filter::select('status_formatted', 'marketing_data_submissions.status')
                ->dataSource(function () {
                    $submission = MarketDataSubmission::select(['status'])->distinct();

                    return $submission->get()->map(function ($submission) {

                        return [
                            'status' => $submission->status === 'denied' ? 'disapproved' : $submission->status,
                            'value' => $submission->status
                        ];
                    });
                })
                ->optionLabel('status')
                ->optionValue('value'),


            //     Filter::inputText('batch_no_formatted', 'batch_no'),
            //      Filter::inputText('indicator')->filterRelation('period.indicator', 'indicator_name'),
            //      Filter::inputText('organisation_formatted')->filterRelation('user.organisation', 'name'),
        ];
    }
    public function relationSearch(): array
    {
        return [

            'user.organisation' => [
                'name',
            ],
            'user' => [
                'name'
            ],


        ];
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


                $text = $model->batch_no;

                $html = '';

                $html .= '

<div>
<div class="accordion accordion-flush" id="default-accordion-example-' . $model->batch_no . '_' . $model->id . '">
    <div class="border accordion-item custom-tooltip" title="show batch number">
        <h2 class=" accordion-header" id="headingOne" >
            <button class="p-1 accordion-button collapsed " style="font-size:0.85rem"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $model->batch_no . '_' . $model->id . '" aria-expanded="true" aria-controls="collapse-' . $model->batch_no . '_' . $model->id . '">
<i class="bx bx-dots-horizontal-rounded"></i>
            </button>
        </h2>
        <div id="collapse-' . $model->batch_no . '_' . $model->id . '" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#default-accordion-example-' . $model->batch_no . '_' . $model->id . '">
            <div class="accordion-body">
                ' . $text . '
            </div>
        </div>
        </div>

</div>
</div>
';
                return $html;
                // return $model->batch_no;
            })
            ->add('submitted_user_id')
            ->add('username', function ($model) {
                return User::find($model->submitted_user_id)?->name;
            })

            ->add('status')

            ->add('record_filter', function ($model) {})
            ->add('status_formatted', function ($model) {

                if ($model->status === 'approved') {
                    return '<span class="badge bg-success-subtle text-success">' . $model->status . '</span>';
                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning-subtle text-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-danger-subtle text-danger">disapproved</span>';
                }
            })

            ->add('period_id')
            ->add('comments')
            ->add('comments_truncated', function ($model) {


                $text = $model->comments;
                $trunc = new TruncateText($text, 30);
                $html = '';
                $disabled = !$model->comments ? 'pe-none opacity-25' : '';
                $html .= '

<div class="' . $disabled . '">
<div class="accordion accordion-flush" id="default-accordion-example-' . $model->id . '">
    <div class="border accordion-item custom-tooltip " title="show comments">
        <h2 class=" accordion-header" id="headingOne" >
            <button class="p-1 accordion-button collapsed " style="font-size:0.85rem"  type="button" data-bs-toggle="collapse" data-bs-target="#collapse-' . $model->id . '" aria-expanded="true" aria-controls="collapse-' . $model->id . '">
<i class="bx bx-dots-horizontal-rounded"></i>
            </button>
        </h2>
        <div id="collapse-' . $model->id . '" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#default-accordion-example-' . $model->id . '">
            <div class="accordion-body">
                ' . $text . '
            </div>
        </div>
        </div>

</div>
</div>
';
                return $html;
            })

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
            Column::make('Batch no', 'batch_no_formatted','submissions.batch_no')

                ->searchable(),
            Column::make('#', 'rn')->sortable()->hidden(),
            Column::make('File', 'file_link'),



            Column::make('SUBMITTED BY', 'username')->searchable(),

            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),
            Column::make('Comments', 'comments_truncated'),
            Column::make('Description', 'description')->searchable(),
            Column::make('Date of submission', 'date_of_submission', 'created_at')
                ->sortable(),

            Column::action('Action'),
            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

        ];
    }

    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
    }

    public function actions($row): array
    {

        return [
            Button::add('edit')
                ->slot('<i class="bx bx-analyse"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->can(allowed: User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->tooltip('Review Submission')
                ->dispatch('showMarket', [
                    'id' => $row->id,
                    'name' => 'view-market-modal',

                ]),


            Button::add('delete')
                ->slot('<i class="bx bx-trash-alt"></i>')
                ->id()
                ->class('btn btn-theme-red my-1 custom-tooltip btn-sm')
                ->can(
                    allowed: (User::find(auth()->user()->id)->hasAnyRole('manager') ||
                        User::find(auth()->user()->id)->hasAnyRole('admin'))
                )
                ->tooltip('Delete Data')
                ->dispatch('deleteMarketBatch', [
                    'id' => $row->id,
                    'name' => 'delete-market-modal'
                ]),

            Button::add('view-data')
                ->slot('<i class="bx bx-link"></i>')
                ->id()
                ->class('btn btn-warning my-1 custom-tooltip btn-sm')
                ->tooltip('View Data')
                ->dispatch('view-data-market', [
                    'batch_no' => $row->batch_no,


                ]),

        ];
    }
    #[On('view-data-market')]
    public function viewData($batch_no)
    {
        $routePrefix = $this->routePrefix;


        if (!$routePrefix  || !$batch_no) {
            $this->alert('error', 'Missing parameters to view data', [
                'position' => 'center',
                'timer' => 5000,
                'toast' => false
            ]);
            return;
        }

        $route = "{$routePrefix}/marketing/manage-data/{$batch_no}";
        return $this->redirect($route);
    }

    public function actionRules($row): array
    {
        $user = User::find(auth()->user()->id);

        return [
            //  Hide button edit for ID 1

            Rule::button('edit')
                ->when(fn($row) => $row->status === 'denied' || $row->status === 'disapproved')
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => $row->status === 'pending')
                ->disable(),

            Rule::button('delete')
                ->when(fn($row) => !$user->hasAnyRole('manager')
                    || !$user->hasAnyRole('admin'))
                ->disable(),

            Rule::rows()
                ->when(fn($row) => $row->batch_no === $this->batch)
                ->setAttribute('class', 'table-secondary'),

        ];
    }
}
