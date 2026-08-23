<?php
namespace App\Traits;

use App\Models\AttendanceRegister;
use App\Models\Indicator;
use App\Models\SubmissionReport;
use Illuminate\Database\Eloquent\Builder;

trait InterventionsTrait
{
    //
    public function builder345(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '3.4.5')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }

    public function builder346(): Builder
    {
        $indicatorId = Indicator::where('indicator_no', '3.4.6')->first()->id;
        return $this->applyFilters(SubmissionReport::query()->where('indicator_id', $indicatorId), true);
    }
    public function builderAttendance(): Builder
    {
        return $this->applyFilters(
            AttendanceRegister::query()->where('status', 'approved')
        );
    }

    public function householdsData(): array
    {
        $builder = $this->builder()->selectRaw('SUM(household_size) as household_size')->first();

        return ['household_size' => (int) $builder->household_size ?? 0];
    }
    public function indicator347Data(): array
    {
        $builder = $this->builder()->selectRaw('SUM(household_size - child_under_school_fd) as caregroups')->first();

        return ['caregroups' => (int) $builder->caregroups ?? 0];
    }

    public function nutritionTrainingsData(): array
    {
        $builder = $this->builderAttendance()->where('meetingCategory', 'Nutrition Training')->selectRaw('COUNT(*) as nutrition_trainings')->first();

        return ['nutrition_trainings' => (int) $builder->nutrition_trainings ?? 0];
    }

    public function indicator345Data()
    {
        return $this->getTotalReport($this->builder345(), self::pullTotals('3.4.5'))->toArray();
    }

    public function indicator346Data()
    {
        return $this->getTotalReport($this->builder346(), self::pullTotals('3.4.6'))->toArray();
    }
}
