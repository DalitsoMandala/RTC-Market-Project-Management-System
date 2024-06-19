<?php

namespace App\Livewire\external\Tables;

use App\Models\Form;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Carbon\Carbon;
use id;
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
        $user = User::find($this->userId);
        $organisation_id = $user->organisation->id;

        // $data = Indicator::query()->with(['project', 'responsiblePeopleforIndicators', 'forms'])->whereHas('responsiblePeopleforIndicators', function (Builder $query) use ($organisation_id) {
        //     $query->where('organisation_id', $organisation_id);
        // });

        // $indicatorIds = $data->pluck('id');

        // $forms = Form::with('submissionPeriods', 'indicators', 'project')->whereHas('indicators', function (Builder $query) use ($indicatorIds) {
        //     $query->whereIn('indicators.id', $indicatorIds);
        // });

        //  $formIds = $forms->pluck('id');
        $submissionPeriods = SubmissionPeriod::with(['form']);

        $submissionPeriods->get()->transform(function ($item) use ($organisation_id) {
            // dd($item);

            $form = $item->form;

            $formwithIndicators = $form->with('indicators')->first();

            if ($formwithIndicators) {
                $indicators = $formwithIndicators->indicators;
                if ($indicators) {
                    foreach ($indicators as $indicator) {
                        $people = Indicator::find($indicator->id);
                        $responsiblePeople = $people->responsiblePeopleforIndicators;
                        $indicator_organisations = $responsiblePeople->where('organisation_id', $organisation_id);
                        if ($indicator_organisations->count() > 0) {

                        }
                    }

                }
            }

        });

        dd($submissionPeriods->get());
        return $submissionPeriods;

    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('name')
            ->add('name_formatted', function ($model) {

                $form = Form::find($model->form->id);

                $form_name = str_replace(' ', '-', strtolower($form->name));

                $project = str_replace(' ', '-', strtolower($form->project->name));

                return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';

            })
            ->add('type')
            ->add('open_for_submission', function ($model) {

                return ($model->is_open === 1 && $model->is_expired === 0) ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-danger">No</span>';

            })
            ->add('project_id')
            ->add('project', function ($model) {
                $form = Form::find($model->form->id);
                return $form->project->name;

            })

            ->add('submission_duration', function ($model) {

                $date_start = Carbon::parse($model->date_established)->format('d F Y') ?? null;
                $date_end = Carbon::parse($model->date_ending)->format('d F Y') ?? null;
                return "{$date_start} - {$date_end}";

            })

            ->add('remaining_days', function ($model) {
                $date = Carbon::create($model->date_ending);
                $now = Carbon::now();
                $date_end = $date->diffForHumans() ?? null;

                if ($date->isPast()) {

                    return "<span class='text-danger'>Expired!</span>";
                } else {
                    if ($model->is_expired === 1 && !$date->isPast()) {
                        return "<span class='text-danger'>Cancelled!</span>";
                    } else {
                        return "<b>{$date_end}</b>";
                    }

                }

            })
            ->add('submission_status', function ($model) {
                $userId = $this->userId;

                $submitted = Submission::where('user_id', $userId)->where('form_id', $model->id)->count();
                if ($submitted === 0) {
                    return '<span class="badge bg-danger">Not submitted</span>';
                } else {
                    return '<span class="badge bg-success">Submitted</span>';

                }
            })

            ->add('indicator_id')
            ->add('indicator', function ($model) {
                $indicator = Indicator::find($model->indicator_id);
                return $indicator->indicator_name;
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

            Column::make('Open for submission', 'open_for_submission')
            ,

            Column::make('Indicator', 'indicator'),

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
