<?php

namespace App\Livewire;

use App\Models\Form;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
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
        return DB::table('submission_periods');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('form_id')
            ->add('form_name', function ($model) {
                $form = Form::find($model->form_id);
                return $form->name;
            })
            ->add('date_established_formatted', fn($model) => Carbon::parse($model->date_established)->format('d/m/Y H:i:s'))
            ->add('date_ending_formatted', fn($model) => Carbon::parse($model->date_ending)->format('d/m/Y H:i:s'))
            ->add('is_open')
            ->add('is_open_toggle', function ($model) {

                return '<div class=" form-check form-switch" dir="ltr">
                                                        <input type="checkbox" class="form-check-input" id="customSwitchsizesm" checked="">

                                    </div>';
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

            Column::make('Is open', 'is_open_toggle')
                ->sortable()
                ->searchable(),

            Column::action('Action')

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
                ->dispatch('showModal', ['rowId' => $row->id, 'name' => 'view-submission-modal'])
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