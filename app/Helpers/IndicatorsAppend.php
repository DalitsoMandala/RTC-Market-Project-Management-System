<?php

namespace App\Helpers;

use App\Models\Indicator;
use App\Models\IndicatorClass;
use App\Models\IndicatorDisaggregation;
use App\Models\OrganisationTarget;
use App\Models\Project;
use App\Models\SubmissionTarget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IndicatorsAppend
{
    protected string $project = 'RTC Market';

    public function addIndicator(
        string $indicator_name,
        string $indicator_no,
        array $disaggregations = [],
        bool $copy = false,
        $copyIndicatorName = null,
        $delete = false,
        $class = null
    ) {
        try {
            DB::beginTransaction();

            // Handle delete operation
            if ($delete) {
                $indicator = Indicator::where('indicator_name', $indicator_name)->first();

                if (!$indicator) {
                    throw new \Exception('Indicator not found for deletion: ' . $indicator_name);
                }

                $indicator->delete(); // This will trigger the model events

                DB::commit();
                return response()->json(['message' => 'Indicator deleted successfully'], 200);
            }

            // Check if indicator already exists (for create operation)
            $existingIndicator = Indicator::where('indicator_name', $indicator_name)->first();
            if ($existingIndicator) {
                throw new \Exception('Indicator already exists');
            }

            // Get project with error handling
            $project = Project::where('name', $this->project)->first();
            if (!$project) {
                throw new \Exception('Project not found: ' . $this->project);
            }

            // Create indicator
            $indicator = Indicator::create([
                'indicator_name' => $indicator_name,
                'indicator_no' => $indicator_no,
                'project_id' => $project->id
            ]);

            // Create disaggregations
            foreach ($disaggregations as $indicatorName) {
                IndicatorDisaggregation::create([
                    'name'         => $indicatorName,
                    'indicator_id' => $indicator->id,
                ]);
            }

            // Create classes
            if (!$class) {

                throw new \Exception('Class not found');
            }

            IndicatorClass::firstOrCreate([
                'indicator_id' => $indicator->id,
                'class' => $class
            ]);

            // Handle copy if needed
            if ($copy && $copyIndicatorName) {
                $this->copyIndicatorTargets($copyIndicatorName, $indicator->id);
            }

            DB::commit();

            // Optional: Load relationships if needed for response
            if ($copy) {
                $indicator->load('submissionTargets.organisationTargets');
            }

            return $indicator;
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            Log::error('Failed to process indicator operation: ' . $e->getMessage(), [
                'indicator_name' => $indicator_name,
                'operation' => $delete ? 'delete' : ($copy ? 'copy' : 'create'),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw with cleaner message
            return $e->getMessage();
        }
    }

    /**
     * Extract copying logic to a separate method for better readability
     */
    private function copyIndicatorTargets(string $sourceIndicatorName, int $newIndicatorId)
    {
        $sourceIndicator = Indicator::where('indicator_name', $sourceIndicatorName)->first();

        if (!$sourceIndicator) {
            throw new \Exception('Source indicator not found: ' . $sourceIndicatorName);
        }

        $submissionTargets = SubmissionTarget::with('organisationTargets')
            ->where('indicator_id', $sourceIndicator->id)
            ->get();

        foreach ($submissionTargets as $submissionTarget) {
            $subTarget = SubmissionTarget::create([
                'indicator_id' => $newIndicatorId,
                'target_name' => $submissionTarget->target_name,
                'target_value' => $submissionTarget->target_value,
                'financial_year_id' => $submissionTarget->financial_year_id
            ]);

            foreach ($submissionTarget->organisationTargets as $orgTarget) {
                OrganisationTarget::create([
                    'submission_target_id' => $subTarget->id,
                    'organisation_id' => $orgTarget->organisation_id,
                    'value' => $orgTarget->value,
                ]);
            }
        }
    }
}
