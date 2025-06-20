<?php

namespace App\Livewire\tables;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\SubmissionReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
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
        $query = SubmissionReport::with(['files', 'indicator', 'organisation', 'user', 'submissionPeriod', 'periodMonth'])->where('status', 'approved');
        $user = User::find(auth()->user()->id);
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
            ->add('indicator_id', fn($model) => $model->indicator->indicator_name)
            ->add('user_id', fn($model) => $model->user->name)
            ->add('data')
            ->add('submission_period_id', function ($model) {
                $start = Carbon::parse($model->submissionPeriod->date_established)->format('d-m-Y H:i A');
                $end = Carbon::parse($model->submissionPeriod->date_ending)->format('d-m-Y H:i A');
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
                            <div class='px-2 py-1 rounded-1 bg-success-subtle'>
                                <i class='fas fa-file-excel text-success'></i>
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
            Column::make('Id', 'id'),
            Column::make('Indicator', 'indicator_id'),
            Column::make('Project year', 'financial_year_id'),

            Column::make('File', 'file'),

            Column::make('Submitted By', 'user_id'),

            Column::make('Date of Submission', 'date_of_submission'),
        ];
    }

    public function filters(): array
    {
        return [];
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
