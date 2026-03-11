<?php

namespace App\Helpers;

use App\Models\Indicator;
use App\Models\IndicatorDisaggregation;
use App\Models\OrganisationTarget;
use App\Models\Project;
use App\Models\SubmissionTarget;
use Illuminate\Database\Eloquent\Builder;

class DisaggregationAppend
{
    public string $indicator_name;
    public array $data;

    public bool $add = true;
    public function __construct(string $indicator_name, $data = [], bool $add = true)
    {
        $this->indicator_name = $indicator_name;
        $this->data = $data;
        $this->add = $add;

    }
    public static function getIndicators(): Builder
    {
        return Indicator::query()->with('disaggregations');
    }

    public function findIndicator()
    {
        return self::getIndicators()
            ->where('indicator_name', $this->indicator_name)
            ->first();
    }

    private function sync()
    {
        $indicator = $this->findIndicator();

        if (!$indicator) {
            return null;
        }

        $existingDisaggregations = $indicator->disaggregations->pluck('name')->toArray();
        $disToBeAdded = $this->data;

        // Only add if $add is true AND data is different from existing
        if ($this->add) {
            // Find disaggregations that exist in $disToBeAdded but not in $existingDisaggregations
            $newDisaggregations = array_diff($disToBeAdded, $existingDisaggregations);

            foreach ($newDisaggregations as $dis) {
                $indicator->disaggregations()->create(['name' => $dis]);
            }

            return [
                'added' => $newDisaggregations,
                'existing' => $existingDisaggregations,
                'skipped' => array_intersect($disToBeAdded, $existingDisaggregations)
            ];
        }

        return $existingDisaggregations;
    }

    private function remove()
    {
        $indicator = $this->findIndicator();

        if (!$indicator) {
            return null;
        }

        $existingDisaggregations = $indicator->disaggregations->pluck('name')->toArray();
        $disToBeRemoved = $this->data;

        // Only remove if $add is false AND data exists in existing disaggregations
        if (!$this->add) {
            // Find disaggregations that exist in both arrays
            $disToDelete = array_intersect($disToBeRemoved, $existingDisaggregations);

            foreach ($disToDelete as $dis) {
                $indicator->disaggregations()->where('name', $dis)->delete();
            }

            return [
                'removed' => $disToDelete,
                'remaining' => array_diff($existingDisaggregations, $disToDelete),
                'not_found' => array_diff($disToBeRemoved, $existingDisaggregations)
            ];
        }

        return $existingDisaggregations;
    }

    // Alternative approach: Single method to handle both add and remove
    public function updateDisaggregations()
    {
        $indicator = $this->findIndicator();

        if (!$indicator) {
            return null;
        }

        $existingDisaggregations = $indicator->disaggregations->pluck('name')->toArray();
        $newDisaggregations = $this->data;

        if ($this->add) {
            // Add only new disaggregations that don't already exist
            $toAdd = array_diff($newDisaggregations, $existingDisaggregations);

            foreach ($toAdd as $dis) {
                $indicator->disaggregations()->create(['name' => $dis]);
            }

            return [
                'action' => 'add',
                'added' => array_values($toAdd),
                'existing' => $existingDisaggregations,
                'skipped' => array_values(array_intersect($newDisaggregations, $existingDisaggregations))
            ];
        } else {
            // Remove only disaggregations that exist
            $toRemove = array_intersect($newDisaggregations, $existingDisaggregations);

            foreach ($toRemove as $dis) {
                $indicator->disaggregations()->where('name', $dis)->delete();
            }

            return [
                'action' => 'remove',
                'removed' => array_values($toRemove),
                'remaining' => array_values(array_diff($existingDisaggregations, $toRemove)),
                'not_found' => array_values(array_diff($newDisaggregations, $existingDisaggregations))
            ];
        }
    }
    private function restoreToOriginal()
    {
        $originalData = $this->databaseArray();
        $indicator = $this->findIndicator();

        if (!$indicator) {
            return null;
        }

        $originalDisaggregations = $originalData[$this->indicator_name] ?? [];
        $currentDisaggregations = $indicator->disaggregations->pluck('name')->toArray();

        // Find disaggregations to add (in original but not in current)
        $toAdd = array_diff($originalDisaggregations, $currentDisaggregations);

        // Find disaggregations to remove (in current but not in original)
        $toRemove = array_diff($currentDisaggregations, $originalDisaggregations);

        // Add missing disaggregations
        foreach ($toAdd as $dis) {
            $indicator->disaggregations()->create(['name' => $dis]);
        }

        // Remove extra disaggregations
        foreach ($toRemove as $dis) {
            $indicator->disaggregations()->where('name', $dis)->delete();
        }

        return [
            'restored' => true,
            'added' => array_values($toAdd),
            'removed' => array_values($toRemove),
            'current' => $originalDisaggregations
        ];
    }


    public function databaseArray()
    {
        return [
            "Number of actors profitability engaged in commercialization of RTC" => [
                "Total",
                "Female",
                "Male",
                "Youth (18-35 yrs)",
                "Not youth (35yrs+)",
                "Farmers",
                "Processors",
                "Traders",
                "Employees on RTC establishment",
                "Cassava",
                "Potato",
                "Sweet potato",
                "New establishment",
                "Old establishment",
                "Aggregators",
                "Transporters"
            ],

            "Percentage Increase in income ($ value) for RTC actors due to engagement in RTC activities" => [
                "Total (% Percentage)",
                "Cassava",
                "Sweet potato",
                "Potato",
                "Farmers",
                "Processors",
                "Aggregators",
                "Traders",
                "Transporters"
            ],

            "Percentage increase in value of formal RTC exports" => [
                "Total (% Percentage)",
                "Volume (Metric Tonnes)",
                "Financial value ($)",
                "(Formal) Cassava",
                "(Formal) Potato",
                "(Formal) Sweet potato",
                "(Informal) Cassava",
                "(Informal) Potato",
                "(Informal) Sweet potato",
                "Raw",
                "Processed",
                "Value of exports"
            ],

            "Percentage of value ($) of formal RTC imports substituted through local production" => [
                "Total (% Percentage)",
                "Volume (Metric Tonnes)",
                "Financial value ($)",
                "(Formal) Cassava",
                "(Formal) Potato",
                "(Formal) Sweet potato"
            ],

            "Number of people consuming RTC and processed products" => [
                "Total",
                "RTC actors and households",
                "School feeding beneficiaries",
                "Individuals from households reached with nutrition interventions"
            ],

            "Percentage Increase in the volume of RTC produced" => [
                "Total (% Percentage)",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Percentage increase in RTC investment" => [
                "Total (% Percentage)",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of local RTC varieties suitable for domestic and export markets identified for promotion" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of potential market preferred RTC genotypes in the pipeline identified" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Fresh",
                "Processed"
            ],

            "Number of new RTC technologies developed" => [
                "Total",
                "Improved RTC variety",
                "Seed production",
                "Storage",
                "Agronomic production",
                "Post-harvest processing",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Percentage increase in adoption of new RTC technologies" => [
                "Total (% Percentage)",
                "Improved RTC variety",
                "Seed production",
                "Storage",
                "Agronomic production",
                "Post-harvest processing",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of economic studies conducted" => [
                "Total"
            ],

            "Number of RTC and derived products recorded in official trade statistics" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Fresh",
                "Processed"
            ],

            "Number of existing agricultural programs that integrate RTC into their programs" => [
                "Total"
            ],

            "Number of policy briefs developed and shared on RTC topics" => [
                "Total"
            ],

            "Number of market linkages between EGS and other seed class producers facilitated" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of private sector actors involved in production of RTC certified seed" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Area (ha) under seed multiplication" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Basic",
                "Certified"
            ],

            "Percentage seed multipliers with formal registration" => [
                "Total (% Percentage)",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Basic",
                "Certified",
                "POs",
                "Individual farmers not in POs",
                "Registered",
                "Seed multipliers",
                "Large scale",
                "Small scale"
            ],

            "Volume of seed distributed within communities to enhance POs productivity" => [
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of on-farm seed production technology demonstrations established" => [
                "Total"
            ],

            "Number of international learning visits for seed producers (OC)" => [
                "Total"
            ],

            "Number of business plans for the production of different classes of RTC seeds that are executed" => [
                "Total",
                "POs",
                "SMEs",
                "Large scale commercial farmers",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Basic",
                "Certified",
                "Business plans executed",
                "Business plans submitted"
            ],

            "Number of stakeholder engagement events that focus on RTC development" => [
                "Total",
                "Seed production",
                "Seed multiplication",
                "Seed processing"
            ],

            "Number of registered seed producers accessing markets through online Market Information System (MIS)" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Domestic markets",
                "International markets",
                "Individual farmers not in POs",
                "POs",
                "Large scale commercial farmers"
            ],

            "Number of RTC actors linked to online Market Information System (MIS)" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Farmers",
                "Traders",
                "Transporters",
                "Individual farmers not in POs",
                "POs",
                "Large scale commercial farmers",
                "Registered",
                "Not registered",
                "PO's",
                "Individual farmers not in PO's",
                "Large scalecommercial farmers"
            ],

            "Number of RTC products available on the Management Information System" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Seed",
                "Produce",
                "Value added products"
            ],

            "Number of Large scale producer organizations (POs) and private sector commercial farms involved in RTC production" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Fresh",
                "Processed"
            ],

            "Number of RTC actors that use certified seed" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of individuals trained in RTC related topics (seed multiplication, production, processing, entrepreneurship etc.)" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Farmers",
                "Processors",
                "Traders",
                "Partner",
                "Staff",
                "Aggregators",
                "Transporters"
            ],

            "Number of off-season irrigation demonstration sites established" => [
                "Total"
            ],

            "Number of demonstration sites for end-user preferred RTC varieties established" => [
                "Total"
            ],

            "Percentage increase in irrigated off-season RTC production by POs and commercial farmers (from baseline)" => [
                "Total (% Percentage)",
                "Total"
            ],

            "Number of market opportunities identified for RTC actors" => [
                "Total",
                "Domestic markets",
                "International markets",
                "Imports",
                "Exports",
                "Seed",
                "Produce",
                "Value added products"
            ],

            "Number of contractual arrangements facilitated for commercial farmers" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of RTC actors supported to access funds from financial service providers" => [
                "Total",
                "Processors",
                "Farmers",
                "Large scale processors",
                "SME",
                "Loan",
                "Input financing"
            ],

            "Number of POs that have formal contracts with buyers" => [
                "Total",
                "Fresh",
                "Processed"
            ],

            "Number of RTC aggregation centers established" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of RTC POs selling products through aggregation centers" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Volume (MT) of RTC products sold through collective marketing efforts by POs" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "Fresh",
                "Processed"
            ],

            "Number of households reached with RTC nutrition interventions" => [
                "Total"
            ],

            "Frequency of RTC consumption by households per week (OC)" => [
                "Total"
            ],

            "Percentage increase in households consuming RTCs as the main foodstuff (OC)" => [
                "Total (% Percentage)",
                "Total"
            ],

            "Number of RTC utilization options (dishes) adopted by households (OC)" => [
                "Total"
            ],

            "Number of urban market promotions conducted" => [
                "Total"
            ],

            "Number of mass nutrition education campaigns conducted" => [
                "Total"
            ],

            "Number of RTC value-added products promoted" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of RTC actors with MBS certification for producing (or processing) RTC products" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato",
                "SMEs",
                "Large scale commercial farms",
                "POs"
            ],

            "Number of RTC value-added products developed for domestic markets" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of new RTC recipes/products adopted and branded by processors" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of domestic market opportunities identified for value-added products" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ],

            "Number of international market opportunities identified for value-added products" => [
                "Total",
                "Cassava",
                "Potato",
                "Sweet potato"
            ]
        ];
    }
}
