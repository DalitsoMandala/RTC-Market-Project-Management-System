<?php

namespace App\Livewire\tables;

use Illuminate\Support\Carbon;
use App\Models\SubmissionReport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\Builder;
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

    public function datasource(): Collection
    {
        if (auth()->user()->organisation->name == 'CIP') {
            return SubmissionReport::with(['files', 'indicator', 'organisation', 'user', 'submissionPeriod', 'periodMonth'])->get();
        }
        return SubmissionReport::with(['files', 'indicator', 'organisation', 'user', 'submissionPeriod', 'periodMonth'])->whereHas(
            'organisation',
            function ($model) {
                $model->where('id', auth()->user()->organisation->id);
            }
        )->get();
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

                if ($model->file_name) {
                    $html = "
                       <a class='text-success custom-tooltip' title='download file' download='{$model->file_name}' href='" . asset('/storage/exports/' . $model->file_name) . "'>

                    <div class='d-flex align-items-center'>
                        <div class='flex-shrink-0 me-2'>
                            <div class='px-2 py-1 rounded-1 bg-success-subtle'>
                                <i class='fas fa-file-excel text-success'></i>
                            </div>
                        </div>
                        <div class='flex-grow-1 fw-bolder'>
                           {$model->file_name}

                        </div>
                    </div>

                        </a>";

                    return $html;
                }

                return null;
            })
            ->add('status')
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Indicator id', 'indicator_id'),
            Column::make('User id', 'user_id'),


            Column::make('Submission period id', 'submission_period_id'),
            Column::make('Organisation id', 'organisation_id'),
            Column::make('Project year id', 'financial_year_id'),

            Column::make('Uuid', 'uuid')

                ->searchable(),

            Column::make('Files', 'file'),


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
