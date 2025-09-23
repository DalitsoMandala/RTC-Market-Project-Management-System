<?php

namespace App\Livewire\tables;

use Nette\Utils\Html;
use App\Models\Baseline;
use Livewire\Attributes\On;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\BaselineDataMultiple;
use Illuminate\Support\Facades\Blade;

use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
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

final class BaselineTable extends PowerGridComponent
{
    use WithExport;
    use LivewireAlert;

    public bool $showErrorBag = true;

    public $html;
    public $count = 1;
    public $newValue;
    public $oldValue;
    public function setUp(): array
    {
        // $this->showCheckBox();

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
        return Baseline::query()->with('indicator');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id', fn($model) => $this->count++)
            ->add('indicator_id')
            ->add('indicator_no', function ($model) {

                return $model->indicator?->indicator_no;
            })
            ->add('indicator_name', function ($model) {
                return $model->indicator?->indicator_name;
            })
            ->add('baseline_value', function ($model) {
                if ($model->baseline_is_multiple) {
                    $multipleValues = $model->baselineMultiple; // Assuming this returns a collection of multiple values.
                    $forms = '';

                    foreach ($multipleValues as $value) {

                        $forms .= <<<HTML
                        <form class="mb-3 needs-validation" novalidate
                            x-data="{
                                baseline_value: '{$value->baseline_value}',
                                initial_value: '{$value->baseline_value}',
                                baseline_name:'{$value->name}',
                                unit: '{$value->unit_type}',
                                base_id: '{$value->id}',
                                error: '',
                                validate() {
                                    this.error = ''; // Clear existing errors
                                    let value = this.baseline_value.trim();

                                    // Validate the input value
                                    if (!value) {
                                        this.error = 'Value cannot be empty.';
                                        return false;
                                    }
                                    if (isNaN(value)) {
                                        this.error = 'Value must be a number.';
                                        return false;
                                    }
                                    return true;
                                },
                                submitForm() {
                                    if (this.validate()) {
                                        \$dispatch('submit-form', { value: this.baseline_value, id: this.base_id, type:'multiple' });
                                        this.initial_value = this.baseline_value;

                                    }
                                }
                            }"
                            @submit.prevent.debounce.600ms="submitForm">

                            <label for="" ><span x-text="baseline_name" class="text-uppercase fw-bolder"></span> (<span x-text="unit"></span>)

                            </label>
                            <!-- Input Field -->
                            <input type="text"
                            readonly
                                wire:loading.attr="disabled"
                                wire:loading.class="bg-secondary-subtle"
                                class="form-control"
                                name="baseline_value_{$value->id}"
                                x-model="baseline_value"
                                :class="{
                                    'is-invalid': error || (baseline_value =='' || baseline_value == null) ,

                                }">

                            <!-- Error Message -->
                            <div x-show="error" x-text="error" class="mt-1 text-danger"></div>


                        </form>
                        HTML;
                    }

                    return $forms;
                }

                // Handle the single value case as before.
                $baselineValue = $model->baseline_value;

                return <<<HTML
                <form class="needs-validation" novalidate
                    x-data="{
                        baseline_value: '{$baselineValue}',
                        initial_value: '{$baselineValue}',
                        base_id: '{$model->id}',
                        error: '',
                        validate() {
                            this.error = ''; // Clear existing errors
                            let value = this.baseline_value.trim();

                            // Validate the input value
                            if (!value) {
                                this.error = 'Value cannot be empty.';
                                return false;
                            }
                            if (isNaN(value)) {
                                this.error = 'Value must be a number.';
                                return false;
                            }
                            return true;
                        },
                        submitForm() {
                            if (this.validate()) {
                                \$dispatch('submit-form', { value: this.baseline_value, id: this.base_id, type:'single' });
                                this.initial_value = this.baseline_value;
                            }


                        }
                    }"
                    @submit.prevent.debounce.600ms="submitForm">

                    <!-- Input Field -->
                    <input type="text"
                        wire:loading.attr="disabled"
                        wire:loading.class="bg-secondary-subtle"
                        class="form-control"
                        readonly
                        name="baseline_value"
                        x-model="baseline_value"
                        :class="{
                            'is-invalid': error,

                        }">

                    <!-- Error Message -->
                    <div x-show="error" x-text="error" class="mt-1 text-danger"></div>


                </form>
                HTML;
            });
    }


    #[On('submit-form')]
    public function save($value, $id, $type)
    {


        if ($type == 'multiple') {

            BaselineDataMultiple::find($id)->update([
                'baseline_value' => $value
            ]);


            $this->dispatch('refresh');


            return;
        }
        Baseline::find($id)->update([
            'baseline_value' => $value
        ]);



        $this->dispatch('refresh');
    }


    public function columns(): array
    {
        return [
            Column::make('#', 'id'),
            Column::make('Indicator #', 'indicator_no')
                ->searchable(),
            Column::make('Indicator', 'indicator_name')
                ->searchable(),
            Column::make('Baseline value', 'baseline_value'),
            Column::action('Action')
        ];
    }

    public function relationSearch(): array
    {
        return [
            'indicator' => [
                'indicator_name',
                'indicator_no'
            ],
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[\Livewire\Attributes\On('refresh')]
    public function edit(): void
    {
        $this->refresh();
    }



    public function actions($row): array{
        return [
            Button::add('edit')
            ->slot('<i class="bx bx-pen"></i>')
            ->id()
            ->tooltip('Edit Record')
            ->class('btn btn-warning goUp btn-sm custom-tooltip')
            ->dispatch('editData', ['indicator_id' => $row->indicator_id, 'name' => 'view-baseline-modal']),

            Button::add('reset')
            ->slot('<i class="bx bx-refresh"></i>')
            ->id()
            ->tooltip('Reset Record')
            ->class('btn btn-secondary goUp btn-sm custom-tooltip')
            ->dispatch('resetData', ['id' => $row->id]),

        ];
    }

    /*
     * public function actionRules($row): array
     * {
     *    return [
     *         // Hide button edit for ID 1
     *         Rule::button('edit')
     *             ->when(fn($row) => $row->id === 1)
     *             ->hide(),
     *     ];
     * }
     */
}
