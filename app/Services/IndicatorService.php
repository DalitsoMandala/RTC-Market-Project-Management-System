<?php
namespace App\Services;

use App\Models\Indicator;
use Illuminate\Support\Str;

class IndicatorService
{
    protected $indicatorMap = [];

    public function __construct()
    {
        $this->indicatorMap = Indicator::with('project')
            ->where('is_active', 1)
            ->get()
            ->mapWithKeys(function ($indicator) {
                $projectKey  = $indicator->project->name;
                $projectSlug = Str::slug($projectKey);
                $indicatorNo = strtolower(str_replace('.', '', $indicator->indicator_no));

                return [
                    $indicator->indicator_name => [
                        $projectKey => "indicators.{$projectSlug}.indicator-{$indicatorNo}",
                    ],
                ];
            })
            ->toArray();
    }

    /**
     * Get component mapping by indicator name.
     */
    public function getComponent($indicatorName, $projectName)
    {
        return $this->indicatorMap[$indicatorName][$projectName] ?? null;
    }

    /**
     * Search and get the component mapping using the indicator number/code
     * (Supports formats like 'A1', '223', or dot notation like '1.4.6', '1.1.1').
     */
    public function getComponentByNumber($indicatorNumber, $projectName)
    {
        $cleanNumber = str_replace('.', '', strtolower($indicatorNumber));

        foreach ($this->indicatorMap as $indicatorName => $projects) {
            foreach ($projects as $project => $mapping) {
                if ($project === $projectName) {
                    $parts = explode('-', $mapping);
                    $code  = end($parts);

                    if (strtolower($code) === $cleanNumber) {
                        return $mapping;
                    }
                }
            }
        }

        return null;
    }

    /**
     * Search and get the indicator name using the indicator number/code
     * (Supports formats like 'A1', '223', or dot notation like '1.4.6', '1.1.1').
     */
    public function getIndicatorNameByNumber($indicatorNumber)
    {
        $cleanNumber = str_replace('.', '', strtolower($indicatorNumber));

        foreach ($this->indicatorMap as $indicatorName => $projects) {
            foreach ($projects as $project => $mapping) {
                $parts = explode('-', $mapping);
                $code  = end($parts);

                if (strtolower($code) === $cleanNumber) {
                    return $indicatorName;
                }
            }
        }

        return null;
    }
}