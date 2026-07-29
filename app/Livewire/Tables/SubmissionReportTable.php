<?php
namespace App\Livewire\tables;

use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SubmissionReportTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
        //  $this->showCheckBox();

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
        $query = SubmissionReport::with(['files', 'indicator', 'organisation', 'user', 'submissionPeriod', 'periodMonth'])
            ->whereHas('indicator', function ($query) {
                $query->where('is_active', 1);
            })
            ->where('status', 'approved')->select([
            'submission_reports.*',
            DB::Raw('ROW_NUMBER() OVER (ORDER BY id) AS rn'),
        ]);
        $user         = User::find(auth()->user()->id);
        $organisation = $user->organisation->id;

        if ($user->hasAnyRole('external')) {
            $query->whereHas(
                'organisation',
                function ($model) use ($organisation) {
                    $model->where('id', $organisation);
                }
            )->where('status', 'approved');
        }
        return $query;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_id')
            ->add('indicator', function ($model) {

                return $model?->indicator?->indicator_no . ' - ' . $model?->indicator?->indicator_name;
            })
            ->add('user_id', fn($model) => $model->user->name)
            ->add('data')
            ->add('submission_period_id', function ($model) {
                $start = Carbon::parse($model->submissionPeriod->date_established)->format('d-m-Y H:i A');
                $end   = Carbon::parse($model->submissionPeriod->date_ending)->format('d-m-Y H:i A');
                return "{$start} - {$end}";
            })
            ->add('organisation_id', function ($model) {
                return $model->organisation->name;
            })
            ->add('financial_year_id', function ($model) {
                return $model->financialYear->number;
            })

            ->add('uuid')
            ->add('file', function ($model) {
                $file = Submission::where('batch_no', $model->uuid)->first();

                if ($file) {

                    $html = "
                       <a class='text-success custom-tooltip' title='download file' download='{$file->file_link}' href='" . asset('/storage/exports/' . $file->file_link) . "'>

                    <div class='d-flex align-items-center'>
                        <div class='flex-shrink-0 me-2'>
                            <div class='px-2 py-1 border rounded-1 bg-light'>
                              <img src='" . asset('assets/images/icons/sheet.png') . "'' width='20' height='20' alt='Excel Icon'>
                            </div>
                        </div>
                        <div class='flex-grow-1 fw-bolder'>
                           {$file->file_link}

                        </div>
                    </div>

                        </a>";

                    return $html;
                }

                return null;
            })
            ->add('status')
            ->add('created_at')
            ->add('date_of_submission', function ($model) {
                return Carbon::parse($model->created_at)->format('d/m/Y');
            })

            ->add('file_link', function ($model) {

                if ($model->file_link) {
                    return '<a  data-bs-toggle="tooltip" data-bs-title="download file" download="' . $model->file_link . '" href="' . asset('/storage/imports') . '/' . $model->file_link . '"><i class="fas fa-file-excel"></i>' . $model->file_link . '</a>';
                }

                return '<a href="#" data-bs-toggle="tooltip" data-bs-title="no file" class="disabled text-muted" ><i class="fas fa-file-excel"></i></a>';
            })
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('#', 'rn')->sortable()->headerAttribute('table-sticky-col')
                ->bodyAttribute('table-sticky-col'),
            Column::make('File', 'file')->headerAttribute('table-sticky-col')
                ->bodyAttribute('table-sticky-col'),
            Column::make('Indicator', 'indicator')->headerAttribute(styleAttr: "min-width:350px;")
                ->bodyAttribute(styleAttr: "white-space:wrap"),
            Column::make('Project year', 'financial_year_id'),
            Column::make('Submitted By', 'user_id'),
            Column::make('Date of Submission', 'date_of_submission', 'created_at')->sortable(),
        ];
    }

    public function filters(): array
    {
        return [
            Filter::select('indicator')
                ->dataSource(Indicator::where('is_active', 1)->get()->map(function ($indicator) {
                    return [
                        'indicator'      => $indicator->indicator_no . ' - ' . $indicator->indicator_name,
                        'id'             => $indicator->id,
                        'indicator_no'   => $indicator->indicator_no,
                        'indicator_name' => $indicator->indicator_name,
                    ];
                }))
                ->optionLabel('indicator')
                ->optionValue('id')
                ->builder(function ($query, $value) {

                    $query->where('submission_reports.indicator_id', $value);
                }),

            Filter::select('financial_year_id', 'financial_year_id')
                ->dataSource(function () {
                    $submission = FinancialYear::select(['id', 'number'])->distinct();

                    return $submission->get()->map(function ($submission) {

                        return [
                            'number' => 'Year ' . $submission->number,
                            'id'     => $submission->id,
                        ];
                    });
                })
                ->optionLabel('number')
                ->optionValue('id'),
        ];
    }

    public function relationSearch(): array
    {
        return [
            'indicator'     => [ // relationship on dishes model
                'indicator_name',    // column enabled to search

            ],
            'user'          => [ // relationship on dishes model
                'name',              // column enabled to search

            ],
            'organisation'  => [ // relationship on dishes model
                'name',              // column enabled to search

            ],
            'financialYear' => [ // relationship on dishes model
                'number',            // column enabled to search

            ],

        ];
    }

    #[\Livewire\Attributes\On('edit')]
    public function edit($rowId): void
    {
        $this->js('alert(' . $rowId . ')');
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