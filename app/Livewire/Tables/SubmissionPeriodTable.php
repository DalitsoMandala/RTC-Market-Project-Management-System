<?php

namespace App\Livewire\Tables;


use App\Models\Form;
use App\Models\SubmissionPeriod;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Rule;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class SubmissionPeriodTable extends PowerGridComponent
{
    use WithExport;

    public function setUp(): array
    {
      //  $this->showCheckBox();

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
        return DB::table('submission_periods');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);

                switch ($form->name) {
                    case 'HOUSEHOLD CONSUMPTION FORM':
                        return '<a  href="forms/household-rtc-consumption/view" >' . $form->name . '</a>';

                        break;

                    default:
                        # code...
                        return '<a  href="#" >' . $form->name . '</a>';

                        break;
                }

            })

            ->add('date_established_formatted', fn($model) => Carbon::parse($model->date_established)->format('d/m/Y'))
            ->add('date_ending_formatted', fn($model) => Carbon::parse($model->date_ending)->format('d/m/Y'))
            ->add('is_open')
            ->add('is_open_toggle', function ($model) {
                $open = $model->is_open === 1 ? 'bg-success' : 'bg-secondary';
                $is_open = $model->is_open === 1 ? 'Open' : 'Closed';

                return '<span class="badge ' . $open . ' "> ' . $is_open . '</span>';
            })
            ->add('is_expired')
            ->add('is_expired_toggle', function ($model) {
                $open = $model->is_expired === 1 ? 'bg-danger' : 'bg-secondary';
                $is_expired = $model->is_expired === 1 ? 'Yes' : 'No';

                return '<span class="badge ' . $open . ' "> ' . $is_expired . '</span>';
            })
            ->add('check_expiry', function ($model) {
                $getDate = Carbon::create($model->date_ending);
                if ($getDate->isPast()) {
                    SubmissionPeriod::find($model->id)->update([
                        'is_expired' => 1,
                        'is_open' => 0,
                    ]);
                }
            })
            ->add('created_at')
            ->add('updated_at');
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id'),
            Column::make('Form', 'form_name'),

            Column::make('Start of Submissions', 'date_established_formatted', 'date_established')
                ->sortable(),

            Column::make('End of Submissions', 'date_ending_formatted', 'date_ending')
                ->sortable(),

            Column::make('Status', 'is_open_toggle')
                ->sortable()
                ->searchable(),

            Column::make('Cancelled/Expired', 'is_expired_toggle')
                ->sortable()
                ->searchable(),

            Column::action('Action'),

        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datetimepicker('date_established'),
            // Filter::datetimepicker('date_ending'),
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
                ->dispatch('editData', ['rowId' => $row->id]),

        ];
    }

    public function actionRules($row): array
    {
        return [
// Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->is_expired === 1)
                ->disable(),
        ];
    }

}
