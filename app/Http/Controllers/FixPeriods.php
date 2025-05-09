<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Indicator;
use Illuminate\Http\Request;
use App\Models\SubmissionPeriod;

class FixPeriods extends Controller
{
    //
    public function set()
    {

        function getFilteredIndicators(
            array $indicatorIds = [],
            array $formIds = [],
            array $organisationIds = []
        ) {
            // Start with the base query
            $query = Indicator::with([
                'organisation',
                'forms',
                'organisation.users',
                'organisation.users.roles',
            ]);

            // Filter by indicator IDs (if provided)
            if (!empty($indicatorIds)) {
                $query->whereIn('id', $indicatorIds);
            }

            // Filter by form IDs (if provided)
            if (!empty($formIds)) {
                $query->whereHas('forms', function ($q) use ($formIds) {
                    $q->whereIn('forms.id', $formIds);
                });
            }

            // Filter by organisation IDs (if provided)
            if (!empty($organisationIds)) {
                $query->whereHas('organisation', function ($q) use ($organisationIds) {
                    $q->whereIn('organisations.id', $organisationIds);
                });
            }

            // Execute the query
            $indicators = $query->get();

            // Transform the result into the desired structure
            $organisationCollection = collect();
            $indicators->each(function ($indicator) use ($organisationCollection) {
                $organisationCollection->push([
                    'indicator_id' => $indicator->id,
                    'indicator_name' => $indicator->indicator_name,
                    'organisations' => $indicator->organisation->map(function ($org) {
                        return [
                            'organisation_id' => $org->id,
                            'name' => $org->name,
                            'users' => $org->users->map(function ($user) {
                                return [
                                    'user_id' => $user->id,
                                    'name' => $user->name,
                                    'email' => $user->email,
                                    'roles' => $user->roles->pluck('name')->toArray(),
                                ];
                            }),
                        ];
                    }),
                    'forms' => $indicator->forms->map(function ($form) {
                        return [
                            'form_id' => $form->id,
                            'form_name' => $form->name,
                        ];
                    })
                ]);
            });

            return $organisationCollection;
        }

        $filteredIndicators = getFilteredIndicators();
        $date_established = Carbon::parse('2025-05-06')->format('Y-m-d');
        $date_ending = Carbon::parse('2025-06-06')->format('Y-m-d');
        if (Carbon::parse($date_ending)->format('H:i:s') === '00:00:00') {
            $date_ending = Carbon::parse($date_ending)
                ->setTime(23, 59, 0)  // Sets to 11:59:00 PM
                ->format('Y-m-d H:i:s'); // Convert back to string if needed
        }
        $month_range_period_id = 2;
        $financial_year_id = 2;

        foreach ($filteredIndicators as $indicator) {
            $organisation = $indicator['organisations'];
            $forms = $indicator['forms'];

            foreach ($forms as $form) {
                $period = SubmissionPeriod::create([
                    'form_id' => $form['form_id'],
                    'month_range_period_id' => $month_range_period_id,
                    'financial_year_id' => $financial_year_id,
                    'date_established' => $date_established,
                    'date_ending' => $date_ending,
                    'is_open' => true,
                    'is_expired' => false,
                    'indicator_id' => $indicator['indicator_id'],
                ]);

                foreach ($organisation as $org) {

                    $users = $org['users'];
                    foreach ($users as $user) {
                        $userRoles = collect($user['roles']);

                        if ($userRoles->contains('admin') || $userRoles->contains('project_manager')) {
                            continue;
                        }
                        $period->mailingList()->create([
                            'submission_period_id' => $period->id,
                            'user_id' => $user['user_id'],
                        ]);
                    }
                }
            }
        }
    }
}
