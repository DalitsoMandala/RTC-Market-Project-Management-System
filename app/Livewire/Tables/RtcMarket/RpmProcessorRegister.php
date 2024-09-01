<?php

namespace App\Livewire\tables\RtcMarket;

use Illuminate\Support\Carbon;

use Illuminate\Support\Collection;
use App\Models\RtcProductionProcessor;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\PowerGrid;
use PowerComponents\LivewirePowerGrid\Exportable;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;

final class RpmProcessorRegister extends PowerGridComponent
{
    public function datasource(): Builder
    {
        return RtcProductionProcessor::query()->where('is_registered', 1);
    }

    public function setUp(): array
    {


        return [

            Header::make(),
            Footer::make()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('name_of_actor')
            ->add('unique_id', function ($model) {
                return str_pad($model->id, 5, '0', STR_PAD_LEFT);
            })
            ->add('registration_details_body', fn($model) => json_decode($model->registration_details)->registration_body ?? null)
            ->add('registration_details_date', fn($model) => json_decode($model->registration_details) == null ? null : Carbon::parse(json_decode($model->registration_details)->registration_date)->format('d/m/Y'))
            ->add('registration_details_number', fn($model) => json_decode($model->registration_details)->registration_number ?? null);
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'unique_id', 'id')
                ->searchable()
                ->sortable(),

            Column::make('Name of actor', 'name_of_actor')
                ->searchable()
                ->sortable(),

            Column::make('Registration Details Body', 'registration_details_body'),

            Column::make('Registration Details Date', 'registration_details_date'),

            Column::make('Registration Details Number', 'registration_details_number'),

        ];
    }
}
