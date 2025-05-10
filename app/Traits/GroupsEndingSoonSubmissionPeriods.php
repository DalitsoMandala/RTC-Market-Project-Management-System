<?php

namespace App\Traits;

use App\Models\SubmissionPeriod;
use Illuminate\Support\Carbon;

trait GroupsEndingSoonSubmissionPeriods
{
    /**
     * Get submission periods ending soon grouped by user.
     *
     * @param int $days Number of days ahead to consider "ending soon" (default 3)
     * @return array
     */
    public function getSubmissionPeriodsEndingSoonGroupedByUser($days = 3)
    {
        $now = Carbon::now();

        $periods = SubmissionPeriod::with(['form', 'mailingList.user', 'indicator'])
            ->whereBetween('date_ending', [
                $now->copy()->startOfDay(),  // 1 day from now (tomorrow)
                $now->copy()->addDays($days)->endOfDay()   // 3 days from now
            ])
            ->where('is_open', true)
            ->where('is_expired', false)->get();



        $userGrouped = [];

        foreach ($periods as $period) {
            if (!$period->form) {
                continue;
            }

            $endDate = Carbon::parse($period->end_date);

            if ($endDate->diffInDays($now, false) > - ($days + 1)) {
                foreach ($period->mailingList as $mailing) {
                    if (!$mailing->user) continue;

                    $user = $mailing->user;
                    $userId = $user->id;

                    if (!isset($userGrouped[$userId])) {
                        $userGrouped[$userId] = [
                            'name' => $user->name,
                            'email' => $user->email,
                            'user_id' => $user->id,
                            'forms' => [],
                        ];
                    }

                    $formId = $period->form_id;
                    $formName = $period->form->name;

                    if (!isset($userGrouped[$userId]['forms'][$formId])) {
                        $userGrouped[$userId]['forms'][$formId] = [
                            'form_name' => $formName,
                            'periods' => [],
                        ];
                    }

                    $userGrouped[$userId]['forms'][$formId]['periods'][] = [
                        'start' => $this->getFormattedDate($period->date_established),
                        'end' => $this->getFormattedDate($period->date_ending), // $period->date_ending,
                        'indicator_id' => $period->indicator_id,
                        'indicator_name' => $period->indicator->indicator_name,
                        'period_id' => $period->id,
                    ];
                }
            }
        }

        return $userGrouped;
    }

    private function getFormattedDate($date): string
    {

        return Carbon::parse($date)->format('d/m/Y h:i:A');
    }
}