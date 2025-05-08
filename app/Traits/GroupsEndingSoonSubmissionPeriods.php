<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Carbon;
use App\Models\SubmissionPeriod;

trait GroupsEndingSoonSubmissionPeriods
{
    /**
     * Get submission periods ending soon grouped by user.
     *
     * @param int $days Number of days ahead to consider "ending soon" (default 3)
     * @return array
     */

    public function getSubmissionPeriodsEndingSoonGroupedByIndicator(int $days = 3, bool $checkExpired = false)
    {
        $now = Carbon::now();

        $query = SubmissionPeriod::with([
            'form',
            'mailingList.user',
            'indicator.responsiblePeopleforIndicators'
        ])->where('is_open', true)
            ->where('is_expired', false);

        // Filter by date
        if ($checkExpired) {
            $query->where('date_ending', '<=', $now->endOfDay());
        } else {
            $query->whereBetween('date_ending', [
                $now->startOfDay(),
                $now->copy()->addDays($days)->endOfDay(),
            ]);
        }

        $periods = $query->get();

        // Collect formatted data
        $collection = collect();

        foreach ($periods as $period) {
            if (!$period->form || !$period->indicator) {
                continue;
            }

            $endDate = Carbon::parse($period->end_date);
            if ($endDate->diffInDays($now, false) <= $days) {
                foreach ($period->mailingList as $mailing) {
                    $user = $mailing->user;
                    if (!$user) continue;

                    $collection->push([
                        'indicator_id' => $period->indicator_id,
                        'indicator_name' => $period->indicator->indicator_name,
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_email' => $user->email,
                        'form_id' => $period->form_id,
                        'form_name' => $period->form->name,
                        'period_id' => $period->id,
                        'start' => $this->getFormattedDate($period->date_established),
                        'end' => $this->getFormattedDate($period->date_ending),
                    ]);
                }
            }
        }

        // Group by indicator and structure data
        return $collection
            ->groupBy('indicator_id')
            ->map(function ($group) {
                return [
                    'indicator_id' => $group->first()['indicator_id'],
                    'indicator_name' => $group->first()['indicator_name'],
                    'users' => $group->groupBy('user_id')->map(function ($userGroup) {
                        return [
                            'user_id' => $userGroup->first()['user_id'],
                            'name' => $userGroup->first()['user_name'],
                            'email' => $userGroup->first()['user_email'],
                            'forms' => $userGroup->unique('form_id')->values()->map(function ($item) {
                                return [
                                    'form_id' => $item['form_id'],
                                    'form_name' => $item['form_name'],
                                ];
                            }),
                        ];
                    })->values(),
                    'periods' => $group->unique(function ($item) {
                        return $item['start'] . '-' . $item['end'];
                    })->values()->map(function ($item) {
                        return [
                            'start' => $item['start'],
                            'end' => $item['end'],
                            'period_id' => $item['period_id'],
                        ];
                    }),
                ];
            })->values();
    }

    public function getUserWithDeadlines(int $days, bool $checkExpired = false)
    {
        $groupedPeriods = $this->getSubmissionPeriodsEndingSoonGroupedByIndicator($days, $checkExpired);

        $usersWithDeadlines = [];
        $temp_periods = [];
        foreach ($groupedPeriods as $indicatorData) {
            $indicatorName = $indicatorData['indicator_name'];
            $periods = $indicatorData['periods'];
            $temp_periods = array_merge($temp_periods, $periods->toArray());

            foreach ($indicatorData['users'] as $userData) {
                $user = User::find($userData['user_id']);
                if (!$user) continue;

                $userId = $user->id;

                if (!isset($usersWithDeadlines[$userId])) {
                    $usersWithDeadlines[$userId] = [
                        'user' => $user,
                        'indicators' => [],
                        'periods' => $periods,
                    ];
                }

                $usersWithDeadlines[$userId]['indicators'][] = $indicatorName;
            }
        }
        foreach ($temp_periods as $period) {
            $submissionPeriod = SubmissionPeriod::find($period['period_id']);

            if ($submissionPeriod) {
                $submissionPeriod->update([
                    'is_expired' => true,
                    'is_open' => false
                ]);

                $submissionPeriod->mailingList()->delete();
            }
        }

        return $usersWithDeadlines;
    }



    // public function getSubmissionPeriodsEndingSoonGroupedByIndicator($days = 3, $checkExpired = false)
    // {
    //     $now = Carbon::now();

    //     $periods = SubmissionPeriod::with(['form', 'mailingList.user', 'indicator', 'indicator.responsiblePeopleforIndicators'])

    //         ->where('is_open', true)
    //         ->where('is_expired', false);




    //     if ($checkExpired === false) {
    //         $periods = $periods->whereBetween(
    //             'date_ending',
    //             [
    //                 now()->startOfDay(),
    //                 now()->addDays($days)->endOfDay()

    //             ]
    //         );
    //     } else {
    //         $periods = $periods->where('date_ending', '<=', $now->endOfDay());
    //     }

    //     $periods = $periods->get();

    //     $collection = collect();

    //     $periods->each(function ($period) use ($collection, $now, $days) {
    //         if (!$period->form || !$period->indicator) {
    //             return;
    //         }

    //         $endDate = Carbon::parse($period->end_date);
    //         if ($endDate->diffInDays($now, false) > - ($days + 1)) {
    //             $indicatorId = $period->indicator_id;
    //             $indicatorName = $period->indicator->indicator_name;

    //             foreach ($period->mailingList as $mailing) {
    //                 if (!$mailing->user) continue;

    //                 $collection->push([
    //                     'indicator_id' => $indicatorId,
    //                     'indicator_name' => $indicatorName,
    //                     'user_id' => $mailing->user->id,
    //                     'user_name' => $mailing->user->name,
    //                     'user_email' => $mailing->user->email,
    //                     'form_id' => $period->form_id,
    //                     'form_name' => $period->form->name,
    //                     'period_id' => $period->id,
    //                     'start' => $this->getFormattedDate($period->date_established),
    //                     'end' => $this->getFormattedDate($period->date_ending),
    //                 ]);
    //             }
    //         }
    //     });

    //     return $collection
    //         ->groupBy('indicator_id')
    //         ->map(function ($group) {
    //             return [
    //                 'indicator_id' => $group->first()['indicator_id'],
    //                 'indicator_name' => $group->first()['indicator_name'],
    //                 'users' => $group
    //                     ->groupBy('user_id')
    //                     ->map(function ($userGroup) {
    //                         return [
    //                             'user_id' => $userGroup->first()['user_id'],
    //                             'name' => $userGroup->first()['user_name'],
    //                             'email' => $userGroup->first()['user_email'],
    //                             'forms' => $userGroup->map(function ($item) {
    //                                 return [
    //                                     'form_id' => $item['form_id'],
    //                                     'form_name' => $item['form_name'],
    //                                 ];
    //                             })->unique('form_id')->values(),
    //                         ];
    //                     })->values(),
    //                 'periods' => $group->map(function ($item) {
    //                     return [
    //                         'start' => $item['start'],
    //                         'end' => $item['end'],
    //                         'period_id' => $item['period_id'],
    //                     ];
    //                 })->unique(function ($item) {
    //                     return $item['start'] . '-' . $item['end'];
    //                 })->values(),
    //             ];
    //         })->values();
    // }

    // public function getUserWithDeadlines($days, $checkExpired = false)
    // {
    //     $groupedPeriods = $this->getSubmissionPeriodsEndingSoonGroupedByIndicator($days, $checkExpired);
    //     $usersDeadlines = [];

    //     foreach ($groupedPeriods as $period) {
    //         $indicator = $period['indicator_name'];

    //         foreach ($period['users'] as $userData) {
    //             $user = User::find($userData['user_id']);
    //             if (!$user) continue;

    //             $userId = $user->id;

    //             if (!isset($usersDeadlines[$userId])) {
    //                 $usersDeadlines[$userId] = [
    //                     'user' => $user,
    //                     'indicators' => [],
    //                     'periods' => $period['periods']
    //                 ];
    //             }

    //             $usersDeadlines[$userId]['indicators'][] = $indicator;
    //         }
    //     }

    //     return $usersDeadlines;
    // }


    private function getFormattedDate($date): string
    {

        return Carbon::parse($date)->format('d/m/Y h:i:A');
    }
}
