<?php
namespace App\Livewire\Tables;

use App\Models\FinancialYear;
use App\Models\Form;
use App\Models\Indicator;
use App\Models\Organisation;
use App\Models\ReportingPeriodMonth;
use App\Models\ResponsiblePerson;
use App\Models\SubmissionPeriod;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Detail;
use PowerComponents\LivewirePowerGrid\Facades\Filter;
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
    public $currentRoutePrefix;
    public string $sortField  = 'date_established';
    public string $primaryKey = 'rn';

    public function setUp(): array
    {

        // $this->timeout();

        return [

            Header::make(),
            Footer::make()
                ->showPerPage(10)
                ->showRecordCount(),

            Detail::make()
                ->view('components.submission-period-detail'),
        ];
    }

    public function datasource(): Builder
    {
        $sub = SubmissionPeriod::query()
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY date_established) AS rn ,COUNT(id) as count, date_established, date_ending, is_open,is_expired,is_restricted,financial_year_id,reporting_period_month_id,bypass_notification')
            ->groupBy('date_established', 'date_ending', 'is_open', 'is_expired', 'financial_year_id', 'reporting_period_month_id', 'is_restricted', 'bypass_notification')
            ->orderBy('financial_year_id', 'desc')
            ->orderBy('is_open', 'desc');

        return $sub;
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('form_id')
            ->add('form_name', function ($model) {

                //  return $form->name;
                // $form = Form::find($model->form_id);

                // $form_name = str_replace(' ', '-', strtolower($form->name));
                // $project = str_replace(' ', '-', strtolower($form->project->name));

                // return '<a  href="forms/' . $project . '/' . $form_name . '/view" >' . $form->name . '</a>';
            })
            ->add('financial_year', function ($model) {
                return FinancialYear::find($model->financial_year_id)?->number;
                //   ReportingPeriodMonth::find($model->reporting_period_month_id)->;
            })

            ->add('indicator_id')
            ->add('indicator', function ($model) {
                // $indicator = Indicator::find($model->indicator_id);
                // return $indicator->indicator_name;
            })

            ->add('indicator_no', function ($model) {
                // $indicator = Indicator::find($model->indicator_id);
                // return $indicator->indicator_no;
            })

            ->add('assigned', function ($model) {
                // $indicator = Indicator::find($model->indicator_id);

                // $checkIds = $indicator->responsiblePeopleforIndicators->pluck('organisation_id');

                // $oganisations = Organisation::whereIn('id', $checkIds)->pluck('name');
                // return implode(', ', $oganisations->toArray());
            })

            ->add('month_range', function ($model) {
                return ReportingPeriodMonth::find($model->reporting_period_month_id)->start_month . '-' . ReportingPeriodMonth::find($model->reporting_period_month_id)->end_month;

                // return $model->reportingMonths->start_month . '-' . $model->reportingMonths->end_month;
                //   ReportingPeriodMonth::find($model->reporting_period_month_id)->;
            })
            ->add('date_established_formatted', fn($model) => Carbon::parse($model->date_established)->format('d/m/Y'))
            ->add('date_ending_formatted', fn($model) => Carbon::parse($model->date_ending)->format('d/m/Y'))
            ->add('submission_dates', function ($model) {
                return Carbon::parse($model->date_established)->format('d/m/Y') . ' - ' . Carbon::parse($model->date_ending)->format('d/m/Y');
            })
            ->add('is_open')
            ->add('is_open_toggle', function ($model) {
                $user       = User::find(auth()->user()->id);
                $restricted = $model->is_restricted && ! $user->hasAnyRole(['manager', 'admin']);

                if ($restricted) {
                    $model->is_open = 0;
                }
                $open    = $model->is_open === 1 ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary';
                $is_open = $model->is_open === 1 ? 'Open' : 'Closed';

                return '<span class="badge ' . $open . ' "> ' . $is_open . '</span>';
            })
            ->add('is_expired')
            ->add('is_expired_toggle', function ($model) {

                $user       = User::find(auth()->user()->id);
                $restricted = $model->is_restricted && ! $user->hasAnyRole(['manager', 'admin']);

                if ($restricted) {
                    $model->is_expired = 1;
                }
                $open       = $model->is_expired === 1 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success';
                $is_expired = $model->is_expired === 1 ? 'Yes' : 'No';

                return '<span class="badge ' . $open . ' "> ' . $is_expired . '</span>';
            })

        // ->add('submissions', fn($model) => '<span class=" fw-bold">' . SubmissionPeriod::find($model->id)->submissions->count() . '</span>')

        // ->add('submission_aggregate', fn($model) => SubmissionPeriod::find($model->id)->submissions->where('batch_type', 'aggregate')->count())
        // ->add('submission_manual', fn($model) => SubmissionPeriod::find($model->id)->submissions->where('batch_type', 'manual')->count())
            ->add('created_at')
            ->add('model_data', fn($model) => array_merge($model->toArray(), ['routePrefix' => $this->currentRoutePrefix]))
            ->add('updated_at');
    }

    public function relationSearch(): array
    {
        return [
            'forms'           => [ // relationship on dishes model
                'name',                // column enabled to search

            ],
            'reportingMonths' => [ // relationship on dishes model
                'start_date',          // column enabled to search
                'end_date',            // column enabled to search
            ],

        ];
    }

    public function columns(): array
    {
        return [

            Column::make('#', 'rn')->sortable(),

            Column::make('Start of Submissions', 'date_established_formatted', 'date_established')
                ->sortable(),

            Column::make('End of Submissions', 'date_ending_formatted', 'date_ending')
                ->sortable(),

            Column::make('Months', 'month_range'),

            Column::make('Financial Year', 'financial_year')

                ->searchable(),

            Column::make('Status', 'is_open_toggle', 'is_open')
                ->sortable()
                ->searchable(),

            Column::make('Expired', 'is_expired_toggle', 'is_expired')
                ->sortable()
                ->searchable(),

            Column::action(''),

        ];
    }

    public function filters(): array
    {
        return [
            // Filter::datetimepicker('date_established'),
            // Filter::datetimepicker('date_ending'),
            Filter::select('form_name', 'form_id')
                ->dataSource(Form::all())
                ->optionLabel('name')
                ->optionValue('id'),

            Filter::select('indicator', 'indicator_id')
                ->dataSource(Indicator::all())
                ->optionLabel('indicator_name')
                ->optionValue('id'),

            Filter::select('date_established', 'date_established')
                ->dataSource(SubmissionPeriod::select(['date_established'])->distinct()->get()->map(function ($model) {
                    return [
                        'date_established' => $model->date_established,
                        'formatted'        => Carbon::parse($model->date_established)->format('d/m/Y'),
                    ];
                }))
                ->optionLabel('formatted')
                ->optionValue('date_established'),

            Filter::select('date_ending', 'date_ending')
                ->dataSource(SubmissionPeriod::select(['date_ending'])->distinct()->get()->map(function ($model) {

                    return [
                        'date_ending' => $model->date_ending,
                        'formatted'   => Carbon::parse($model->date_ending)->format('d/m/Y'),
                    ];
                }))
                ->optionLabel('formatted')
                ->optionValue('date_ending'),

            Filter::select('financial_year', 'financial_year_id')
                ->dataSource(SubmissionPeriod::select(['financial_year_id'])->distinct()->get()->map(function ($model) {

                    return [
                        'financial_year' => $model->financial_year_id,
                        'formatted'      => 'Year ' . FinancialYear::find($model->financial_year_id)?->number, //$model->financial_year,
                    ];
                }))
                ->optionLabel('formatted')
                ->optionValue('financial_year'),

            Filter::select('month_range', 'reporting_period_month_id')
                ->dataSource(SubmissionPeriod::select(['reporting_period_month_id'])->distinct()->get()->map(function ($model) {

                    return [
                        'month_range' => $model->reporting_period_month_id,
                        'formatted'   => ReportingPeriodMonth::find($model->reporting_period_month_id)->start_month . '-' . ReportingPeriodMonth::find($model->reporting_period_month_id)->end_month,
                    ];
                }))
                ->optionLabel('formatted')
                ->optionValue('month_range'),
        ];
    }

    #[On('refresh')]
    public function refreshData(): void
    {
        $this->refresh();
        $this->dispatch('reload-tooltips');
    }

    // #[On('timeout')]
    // public function timeout()
    // {
    //     SubmissionPeriod::where('date_ending', '<=', Carbon::now())->update([
    //         'is_expired' => 1,
    //         'is_open' => 0
    //     ]);
    // }

    #[On('toggle-detail')]
    public function setDetail($row)
    {

        //  $this->toggleDetail();
    }

    public function actions($row): array
    {

        $user       = User::find(auth()->user()->id);
        $restricted = $row->is_restricted && ! $user->hasAnyRole(['manager', 'admin']);

        return [

            Button::add('detail')
                ->slot('<i class="bx bx-show"></i>')
                ->class('btn btn-warning btn-sm custom-tooltip')
                ->tooltip('View Details')
                ->toggleDetail($row->rn),
            // ->dispatch('toggle-detail', ['row' => $row]),

            Button::add('schedule')
                ->slot('<i class="bx bx-pen"></i>')
                ->can(User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
                ->class('btn btn-secondary btn-sm custom-tooltip')
                ->tooltip('Edit Schedule')
                ->dispatch('edit-period', ['data' => $row]),

            Button::add('restricted')
                ->slot('<i class="bx bx-lock"></i>')
                ->class('btn btn-danger pe-none btn-sm custom-tooltip')
                ->can($row->is_restricted == '1' && ! $restricted)
                ->tooltip('Restricted Mode'),
            // Button::add('delete')
            //     ->slot('<i class="bx bx-trash-alt"></i>')
            //     ->can(User::find(auth()->user()->id)->hasAnyRole('manager') || User::find(auth()->user()->id)->hasAnyRole('admin'))
            //     ->class('btn btn-danger btn-sm custom-tooltip')
            //     ->tooltip('Delete')
            //     ->dispatch('delete-period', ['data' => $row])
        ];
    }
    // public function actionsFromView($row): View
    // {
    //     return view('livewire.submission-view', ['row' => $row]);
    // }

    public function actionRules($row): array
    {
        $user           = Auth::user();
        $organisationId = $user->organisation->id;
        $indicator      = Indicator::find($row->indicator_id);

        $responsiblePeople = ResponsiblePerson::where('indicator_id', $row->indicator_id)
            ->where('organisation_id', $organisationId)
            ->first();

        // Check if the organisation has responsible people
        $hasResponsiblePeople = $responsiblePeople !== null;

        $currentDate     = Carbon::now();
        $establishedDate = $row->date_established;
        $endDate         = $row->end_date;

        $startDate = Carbon::parse($establishedDate);
        $endDate   = Carbon::parse($endDate);

        $withinDateRange = $currentDate->between($startDate, $endDate);
        $user            = User::find(auth()->user()->id);
        $restricted      = $row->is_restricted && ! $user->hasAnyRole(['manager', 'admin']);
        $row->is_expired = $restricted ? false : true;

        return [
            // Hide button edit for ID 1
            Rule::button('edit')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0 || $restricted)
                ->disable(),

            Rule::button('detail')
                ->when(fn($row) => $row->is_expired === 1 || $row->is_open === 0 || $restricted)
                ->disable(),

        ];
    }

    public function updated()
    {

        $this->dispatch('reload-tooltips');
    }
}
