<?php

namespace App\Livewire;

use App\Helpers\TruncateText;
use App\Models\Form;
use App\Models\Partner;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SubmissionTable extends PowerGridComponent
{
    use WithExport;

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
        return DB::table('submissions');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('batch_no')
            ->add('batch_no_formatted', fn($model) => '<a  href="' . route('cip-internal-submission-view', $model->batch_no) . '" >' . $model->batch_no . '</a>')
            ->add('user_id')
            ->add('username', function ($model) {
                return User::find($model->user_id)->name;

            })
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);
                return $form->name;
            })
            ->add('organisation')
            ->add('organisation_formatted', function ($model) {

                $user = User::find($model->user_id);
                if ($user->hasRole('external')) {
                    $partner = Partner::where('user_id', $user->id)->first() ?? null;

                    return $partner->organisation_name ?? null;
                }
            })
            ->add('status')
            ->add('status_formatted', function ($model) {

                if ($model->status === 'approved') {
                    return '<span class="badge bg-success">' . $model->status . '</span>';

                } else if ($model->status === 'pending') {
                    return '<span class="badge bg-warning">' . $model->status . '</span>';
                } else {
                    return '<span class="badge bg-danger">' . $model->status . '</span>';
                }

            })

            ->add('period_id')
            ->add('reporting_period', function ($model) {

                $period = SubmissionPeriod::find($model->period_id);
                if ($period) {
                    return Carbon::parse($period->date_established)->format('d F Y') . '-' . Carbon::parse($period->date_ended)->format('d F Y');
                }

            })
            ->add('comments')
            ->add('comments_truncated', function ($model) {
                $text = $model->comments;
                $trunc = new TruncateText($text, 30);

                return $trunc->truncate();

            })
            ->add('created_at')
            ->add('date_of_submission', fn($model) => $model->created_at != null ? Carbon::parse($model->created_at)->format('Y-m-d H:i:s') : null)
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Batch no', 'batch_no_formatted')
                ->sortable()
                ->searchable(),

            Column::make('Name', 'username'),

            Column::make('Organisation', 'organisation_formatted'),
            Column::make('Form name', 'form_name'),
            Column::make('Status', 'status_formatted')
                ->sortable()
                ->searchable(),

            Column::make('Submission Period', 'reporting_period')
                ->sortable()
                ->searchable(),

            Column::make('Comments', 'comments_truncated'),

            Column::make('Date of submission', 'date_of_submission', 'created_at')
                ->sortable(),

            // Column::make('Created at', 'created_at')
            //     ->sortable()
            //     ->searchable(),

            Column::action('Action'),
        ];
    }

    public function filters(): array
    {
        return [
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
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->class('btn btn-primary')
                ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-submission-modal']),
        ];
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
