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
                //   dd($query->toRawSql());
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

        $filteredIndicators = getFilteredIndicators(
            indicatorIds: [1],
            formIds: [1],
            organisationIds: [2]

        );
        return response()->json($filteredIndicators);

        foreach ($filteredIndicators as $indicator) {
            $organisation = $indicator['organisations'];
            $forms = $indicator['forms'];

            foreach ($forms as $form) {


                foreach ($organisation as $org) {

                    $users = $org['users'];
                    foreach ($users as $user) {
                        $userRoles = collect($user['roles']);

                        if ($userRoles->contains('admin') || $userRoles->contains('project_manager')) {
                            continue;
                        }
                    }
                }
            }
        }
    }
}
