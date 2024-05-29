<?php

namespace App\Livewire\external;

use App\Models\Form;
use App\Models\Submission;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class FormTable extends PowerGridComponent
{
    use WithExport;
    public $userId;
    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            Exportable::make('export')
                ->striped()
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),
            Header::make()->showSearchInput(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {

        return Form::with(['submissionPeriods', 'indicators'])->whereHas('submissionPeriods', function ($query) {
            $query->where('is_open', 1);
        })->whereHas('indicators', function ($query) {
            $userId = $this->userId;

            $query->whereHas('responsiblePeople', function ($query) use ($userId) {
                $query->where('user_id', $userId); // Filter responsible people by user ID
            });

        });
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_formatted', function ($model) {
                switch ($model->name) {
                    case 'HOUSEHOLD CONSUMPTION FORM':
                        return '<a  href="forms/household-rtc-consumption/view" >' . $model->name . '</a>';

                        break;

                    default:
                        # code...
                        return '<a  href="#" >' . $model->name . '</a>';

                        break;
                }

            })
            ->add('type')
            ->add('indicators', function ($model) {
                $indicators = $model->indicators->pluck('indicator_no')->toArray();

                return implode(',', $indicators);
            })
            ->add('project_id')
            ->add('project', function ($model) {
                return $model->project->name;
            })

            ->add('submission_duration', function ($model) {
                $date_start = Carbon::parse($model->submissionPeriods->first()->date_established)->format('d F Y') ?? null;
                $date_end = Carbon::parse($model->submissionPeriods->first()->date_ending)->format('d F Y') ?? null;
                return "{$date_start} - {$date_end}";

            })

            ->add('remaining_days', function ($model) {
                $date = Carbon::create($model->submissionPeriods->first()->date_ending);
                $now = Carbon::now();
                $date_end = $date->diffForHumans() ?? null;

                if ($date->isPast()) {
                    return "<span class='text-danger'>Expired!</span>";
                } else {
                    return "<b>{$date_end}</b>";

                }

            })
            ->add('submission_status', function ($model) {
                $userId = $this->userId;

                $period = $model->submissionPeriods->first();
                $submitted = Submission::where('period_id', $period->id)->where('user_id', $userId)->where('form_id', $model->id)->count();
                if ($submitted === 0) {
                    return '<span class="badge bg-danger">Not submitted</span>';
                } else {
                    return '<span class="badge bg-success">Submitted</span>';

                }
            })
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Name', 'name_formatted', 'name')
                ->sortable()
                ->searchable(),
            Column::make('Project', 'project')
                ->sortable(),

            Column::make('Indicators', 'indicators')
            ,
            Column::make('Submission Dates', 'submission_duration')
            ,

            Column::make('Time remaining', 'remaining_days'),

            Column::make('Submission status', 'submission_status'),

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
