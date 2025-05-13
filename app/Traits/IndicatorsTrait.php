<?php

namespace App\Traits;

use App\Models\Indicator;
use Illuminate\Support\Collection;

trait IndicatorsTrait
{
    //

    public function IndicatorCollection(array $indicatorIds = []): Collection
    {

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



    public function getIndicators($indicatorIds = [])
    {
        return $this->IndicatorCollection($indicatorIds);
    }


    public function getIndicatorsByOrganisation($indicatorIds = [], $organisationIds = [])
    {
        $indicators = $this->getIndicators($indicatorIds);
        $filteredIndicators = $indicators->filter(function ($indicator) use ($organisationIds) {
            return $indicator['organisations']->contains(function ($organisation) use ($organisationIds) {
                return in_array($organisation['organisation_id'], $organisationIds);
            });
        });
        return $filteredIndicators;
    }
}
