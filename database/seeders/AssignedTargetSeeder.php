<?php

namespace Database\Seeders;

use App\Models\AssignedTarget;
use App\Helpers\AmountSplitter;
use App\Models\IndicatorTarget;
use Illuminate\Database\Seeder;
use App\Models\ResponsiblePerson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignedTargetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $target = IndicatorTarget::find(53);

        // if ($target) {
        //     AssignedTarget::create([
        //         'indicator_target_id' => $target->id,
        //         'organisation_id' => 1,
        //         'target_value' => 3000,
        //         'current_value' => 0,
        //         'type' => 'number',

        //     ]);
        //     AssignedTarget::create([
        //         'indicator_target_id' => $target->id,
        //         'organisation_id' => 3,
        //         'target_value' => 2000,
        //         'type' => 'number',
        //         'current_value' => 0,
        //     ]);

        //     AssignedTarget::create([
        //         'indicator_target_id' => $target->id,
        //         'organisation_id' => 4,
        //         'target_value' => 1000,
        //         'type' => 'number',
        //         'current_value' => 0,

        //     ]);
        //     AssignedTarget::create([
        //         'indicator_target_id' => $target->id,
        //         'organisation_id' => 5,
        //         'target_value' => 4000,
        //         'type' => 'number',
        //         'current_value' => 0,
        //     ]);

        // }

        $indicators = IndicatorTarget::with('details')->get();

        $indicators->each(function ($indicatorTarget) {
            $people = ResponsiblePerson::where('indicator_id', $indicatorTarget->indicator_id)->get();

            if ($indicatorTarget->target_value !== null && $indicatorTarget->type !== 'detail') {
                $this->assignDirectTargets($indicatorTarget, $people);
            } elseif ($indicatorTarget->target_value === null && $indicatorTarget->type === 'detail') {
                $this->assignDetailTargets($indicatorTarget, $people);
            }
        });

    }

    // Function to assign direct targets
    protected function assignDirectTargets($indicatorTarget, $people)
    {
        $splits = (new AmountSplitter($people->count(), $indicatorTarget->target_value))->split();

        $people->each(function ($person, $index) use ($splits, $indicatorTarget) {
            AssignedTarget::create([
                'organisation_id' => $person->organisation_id,
                'target_value' => $splits[$index],
                'indicator_target_id' => $indicatorTarget->id,
                'type' => $indicatorTarget->type,
                'current_value' => 0,
            ]);
        });
    }

    // Function to assign detail targets
    protected function assignDetailTargets($indicatorTarget, $people)
    {
        $names = $this->generateDetailTargets($indicatorTarget->details, $people);

        $people->each(function ($organisation) use ($names, $indicatorTarget) {
            $filteredNames = $this->filterOrganisationTargets($names, $organisation);

            $finalArray = $filteredNames->map(function ($item, $key) {
                return [
                    'name' => $key,
                    'target_value' => $item['target_value'],
                    'type' => $item['type'],
                    'current_value' => 0,
                ];
            })->toArray();

            AssignedTarget::create([
                'organisation_id' => $organisation->organisation_id,
                'target_value' => 0,
                'indicator_target_id' => $indicatorTarget->id,
                'type' => 'detail',
                'detail' => json_encode(array_values($finalArray)),
                'current_value' => 0,
            ]);
        });
    }

    // Function to generate detail targets
    protected function generateDetailTargets($targetDetails, $people)
    {
        return $targetDetails->mapWithKeys(function ($targetDetail) use ($people) {
            $splits = (new AmountSplitter($people->count(), $targetDetail->target_value))->split();

            $temp = $people->map(function ($person, $index) use ($splits, $targetDetail) {
                return [
                    'organisation_id' => $person->organisation_id,
                    'target_value' => $splits[$index],
                    'type' => $targetDetail->type,
                ];
            });

            return [$targetDetail->name => $temp];
        });
    }

    // Function to filter organisation targets
    protected function filterOrganisationTargets($names, $organisation)
    {
        return $names->map(function ($collection) use ($organisation) {
            return $collection->firstWhere('organisation_id', $organisation->organisation_id);
        });
    }

}
