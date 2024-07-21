<?php

namespace App\Livewire\IndicatorTargets;

use App\Helpers\IndicatorsContent;
use App\Livewire\Tables\IndicatorTable;
use App\Models\AssignedTarget;
use App\Models\FinancialYear;
use App\Models\Indicator;
use App\Models\IndicatorTarget;
use App\Models\Organisation;
use App\Models\ResponsiblePerson;
use App\Models\TargetDetail;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class View extends Component
{
    use LivewireAlert;
    public $project_id;
    public $indicator_id;

    public $financial_years = [];
    public $organisations = [];

    public $targets = [];
    public $currentYear;

    public $data;

    public function save()
    {


    }

    public function mount()
    {
        $this->financial_years = FinancialYear::where('project_id', $this->project_id)->get();
        $people = ResponsiblePerson::where('indicator_id', $this->indicator_id)->pluck('organisation_id');
        $this->organisations = Organisation::whereIn('id', $people)->get();
        $myOrganisation = Auth::user()->organisation->id;
        $today = Carbon::now();


        $date = FinancialYear::where('project_id', $this->project_id)->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)->first();
        if ($date) {
            $this->currentYear = $date->number;
        } else {
            $this->currentYear = null;
        }


        $target = IndicatorTarget::where('indicator_id', $this->indicator_id)
            ->where('project_id', $this->project_id)
            ->get();

        $data = $target->map(function ($model) {
            $financialYear = FinancialYear::find($model->financial_year_id);
            $model->financial_year = $financialYear->number;
            $model->start_date = $financialYear->start_date;
            $model->end_date = $financialYear->end_date;
            $assignedTargets = AssignedTarget::where('indicator_target_id', $model->id)->get();
            if ($assignedTargets->isNotEmpty()) {
                $model->data = $assignedTargets->map(function ($assignedTarget) {
                    $assignedTarget->organisation = Organisation::find($assignedTarget->organisation_id)->name;

                    foreach ($this->getTargets() as $targetModel) {
                        if ($targetModel['organisation'] == $assignedTarget->organisation) {
                            $assigned = $assignedTarget->id;
                            AssignedTarget::find($assigned)->update([
                                'current_value' => $targetModel['Total'],
                            ]);
                            $assignedTarget->current_value = $targetModel['Total'];

                        }

                    }



                    return $assignedTarget;
                });


            } else {
                $model->data = [];
            }

            return $model;
        });

        $this->data = $data;

    }


    public function getTargets()
    {


        $content = $this->getIndicatorClasses($this->indicator_id);

        if ($content['class'] != null) {

            $class = new $content['class'];
            $data = $class->getCurrentTargets($this->financial_years->pluck('id'), $this->organisations->pluck('id'));


            return $data;

        }

        return [];
    }

    public function getIndicatorClasses($indicator_id): Collection
    {
        $indicatorContent = new IndicatorsContent(id: $indicator_id);

        return $indicatorContent->content();


    }

    // public function getTarget($project_id, $financial_year_id, $indicator_id, $organisations)
    // {
    //     $target = IndicatorTarget::where('project_id', $project_id)->where('financial_year_id', $financial_year_id)->where('indicator_id', $indicator_id)->first();
    //     if ($target) {

    //         if ($target->type == 'detail') {
    //             $tg = TargetDetail::where('indicator_target_id', $target->id);

    //         } else {

    //             $result = [];
    //             foreach ($organisations as $organisation) {
    //                 $assigned = AssignedTarget::where('organisation_id', $organisation->id)->where('indicator_target_id', $target->id)->first();

    //                 if ($assigned) {
    //                     $result[] = [
    //                         'financial_year' => $financial_year_id,
    //                         'name' => $organisation->name,
    //                         'given' => $assigned->target_value,
    //                         'current' => $assigned->current_value,
    //                     ];

    //                 }

    //             }

    //             return $result;


    //         }
    //     } else {
    //         return [];
    //     }
    // }


    public function render()
    {
        return view('livewire.indicator-targets.view');
    }
}
