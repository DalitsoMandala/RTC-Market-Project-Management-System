<?php

namespace Database\Seeders;

use App\Helpers\IndicatorsContent;
use App\Models\Indicator;
use App\Models\Organisation;
use Illuminate\Database\Seeder;
use App\Models\ResponsiblePerson;
use Illuminate\Support\Collection;

class IndicatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the indicator array from IndicatorsContent
        $indicatorArray = IndicatorsContent::indicatorArray();

        // First loop to insert indicators into the database
        foreach ($indicatorArray as $indicator) {
            $createdIndicator = Indicator::create([
                'indicator_no'   => $indicator['indicator_no'],
                'indicator_name' => $indicator['indicator_name'],
                'project_id'     => 1 // Rtc Market,
            ]);

            // Second loop to associate the partners with the created indicators
            if (isset($indicator['partners']) && is_array($indicator['partners'])) {
                foreach ($indicator['partners'] as $partnerName) {
                    // Find the organisation by name
                    $organisation = Organisation::where('name', $partnerName)->first();

                    if ($organisation) {
                        // Create the ResponsiblePerson record linking indicator and organisation
                        ResponsiblePerson::create([
                            'indicator_id'    => $createdIndicator->id,
                            'organisation_id' => $organisation->id,
                        ]);
                    }
                }
            }
        }
    }
}
