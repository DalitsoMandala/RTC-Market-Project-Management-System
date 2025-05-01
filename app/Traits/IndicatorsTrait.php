<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\MailingList;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Collection;
use App\Mail\SubmissionReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionNotificationMail;

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


    public function getEndingSoonSubmissionPeriods()
    {
        $now = Carbon::now();
        $startDate = $now->copy()->addDays(1)->startOfDay();
        $endDate = $now->copy()->addDays(3)->endOfDay();

        $submissionPeriods = SubmissionPeriod::query()
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY date_established) AS rn ,COUNT(id) as count, date_established, date_ending, is_open,is_expired,financial_year_id,month_range_period_id')
            ->whereBetween('date_ending', [$startDate, $endDate])
            ->groupBy('date_established', 'date_ending', 'is_open', 'is_expired', 'financial_year_id', 'month_range_period_id');
        $dates = $submissionPeriods->get();

        foreach ($dates as $period) {
            $endingDate = Carbon::parse($period->date_ending);

            // Calculate full calendar days remaining (more accurate for reminders)
            $daysLeft = $now->diffInDays($endingDate, false); // false = don't return absolute value

            // Only proceed if daysLeft is positive (in the future)
            if ($daysLeft >= 0) {
                $reminderType = match (true) {
                    $daysLeft >= 2 && $daysLeft < 3 => '3rd day reminder', // Between 2-3 days left
                    $daysLeft >= 1 && $daysLeft < 2 => '2nd day reminder',  // Between 1-2 days left
                    $daysLeft >= 0 && $daysLeft < 1 => 'Last day reminder', // Less than 1 day left
                    default => null,
                };


                if ($reminderType) {
                    $this->sendReminder($period->toArray(), $reminderType);
                }
            }
        }
    }


    public function notifyExpiredSubmissionPeriods()
    {
        $now = Carbon::now();

        $submissionPeriods = SubmissionPeriod::query()
            ->selectRaw('ROW_NUMBER() OVER (ORDER BY date_established) AS rn ,COUNT(id) as count, date_established, date_ending, is_open,is_expired,financial_year_id,month_range_period_id')
            ->groupBy('date_established', 'date_ending', 'is_open', 'is_expired', 'financial_year_id', 'month_range_period_id');
        $dates = $submissionPeriods->get();


        foreach ($dates as $period) {
            $endingDate = Carbon::parse($period->date_ending);

            if ($endingDate->isPast()) {
                // Mark as expired

               $update = SubmissionPeriod::where('date_ending', $period->date_ending)
                    ->where('date_established', $period->date_established)->where('is_expired', false)->update(['is_expired' => true]);

                    if($update){
                        $this->sendNotification($period->toArray(), 'expired');
                    }

            }
        }
    }

    public function sendNotification(array $period, $notificationType)
    {
        $users = User::with('roles')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['staff', 'external']);
        })->get();
        $users->each(function ($user) use ($period, $notificationType) {
            Mail::to($user->email)->send(new SubmissionNotificationMail($period, $notificationType, $user));
        });
    }


    public function sendReminder(array $period, $reminderType)
    {

        $users = User::with('roles')->whereHas('roles', function ($query) {
            $query->whereIn('name', ['staff', 'external']);
        })->get();

        $users->each(function ($user) use ($period, $reminderType) {
            Mail::to($user->email)->send(new SubmissionReminderMail($period, $reminderType, $user));
        });
    }
}
