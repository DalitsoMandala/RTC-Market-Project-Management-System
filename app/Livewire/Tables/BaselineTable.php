<?php

namespace App\Livewire\Tables;

use App\Models\Baseline;
use App\Models\BaselineDataMultiple;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class BaselineTable extends PowerGridComponent
{
    use WithExport;
    use LivewireAlert;

    public string $tableName = 'baselinesTable';
    public bool $showErrorBag = true;

    public function setUp(): array
    {
        return [
            PowerGrid::header()->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Baseline::query()
            ->join('indicators', 'baseline_data.indicator_id', '=', 'indicators.id')
            ->where('indicators.is_active', 1)
            ->select('baseline_data.*');
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('indicator_id')
            ->add('indicator_no', fn ($model) => $model->indicator?->indicator_no)
            ->add('indicator_name', fn ($model) => $model->indicator?->indicator_name)
            ->add('is_active', fn ($model) => $model->indicator?->is_active)
            ->add('baseline_value', function ($model) {
                if ($model->baseline_is_multiple) {
                    $forms = '';
                    foreach ($model->baselineMultiple as $value) {
                        $forms .= $this->getBaselineInputHtml(
                            value: $value->baseline_value,
                            name: $value->name,
                            unit: $value->unit_type,
                            id: $value->id,
                            type: 'multiple'
                        );
                    }
                    return $forms;
                }

                return $this->getBaselineInputHtml(
                    value: $model->baseline_value,
                    name: null,
                    unit: null,
                    id: $model->id,
                    type: 'single'
                );
            });
    }

    /**
     * Helper method to generate clean Alpine.js form inputs for single/multiple values.
     */
    protected function getBaselineInputHtml($value, ?string $name, ?string $unit, $id, string $type): string
    {
        $labelHtml = $name ? '<label><span x-text="baseline_name" class="text-uppercase fw-bolder"></span> (<span x-text="unit"></span>)</label>' : '';

        return <<<HTML
        <form class="mb-3 needs-validation" novalidate
            x-data="{
                baseline_value: '{$value}',
                initial_value: '{$value}',
                baseline_name: '{$name}',
                unit: '{$unit}',
                base_id: '{$id}',
                error: '',
                validate() {
                    this.error = '';
                    let val = this.baseline_value.trim();
                    if (!val) {
                        this.error = 'Value cannot be empty.';
                        return false;
                    }
                    if (isNaN(val)) {
                        this.error = 'Value must be a number.';
                        return false;
                    }
                    return true;
                },
                submitForm() {
                    if (this.validate()) {
                        \$dispatch('submit-form', { value: this.baseline_value, id: this.base_id, type: '{$type}' });
                        this.initial_value = this.baseline_value;
                    }
                }
            }"
            @submit.prevent.debounce.600ms="submitForm">

            {$labelHtml}

            <input type="text"
                readonly
                wire:loading.attr="disabled"
                wire:loading.class="bg-secondary-subtle"
                class="form-control"
                name="baseline_value_{$id}"
                x-model="baseline_value"
                :class="{ 'is-invalid': error || (baseline_value == '' || baseline_value == null) }">

            <div x-show="error" x-text="error" class="mt-1 text-danger"></div>
        </form>
        HTML;
    }

    #[On('submit-form')]
    public function save($value, $id, $type): void
    {
        if ($type === 'multiple') {
            BaselineDataMultiple::find($id)?->update([
                'baseline_value' => $value,
            ]);
        } else {
            Baseline::find($id)?->update([
                'baseline_value' => $value,
            ]);
        }

        $this->dispatch('refresh');
    }

    public function columns(): array
    {
        return [
            Column::action(''),
            Column::make('#', 'id'),
            Column::make('Indicator #', 'indicator_no')->searchable(),
            Column::make('Indicator', 'indicator_name')->searchable(),
            Column::make('Baseline value', 'baseline_value'),
        ];
    }

    public function relationSearch(): array
    {
        return [
            'indicator' => [
                'indicator_name',
                'indicator_no',
            ],
        ];
    }

    public function filters(): array
    {
        return [];
    }

    #[On('refresh')]
    public function edit(): void
    {
        $this->js('window.location.reload()'); // or use built-in PG refresh mechanism if supported
    }

    public function actions($row): array
    {
        return [
            Button::add('edit')
                ->slot('<i class="bx bx-pen"></i>')
                ->id()
                ->can(auth()->user()?->hasAnyRole('admin'))
                ->tooltip('Edit Record')
                ->class('btn btn-warning goUp btn-sm custom-tooltip')
                ->dispatch('editData', ['indicator_id' => $row->indicator_id, 'name' => 'view-baseline-modal']),
        ];
    }
}
