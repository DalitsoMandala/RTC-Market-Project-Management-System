<?php

namespace App\Traits;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Indicator;
use App\Models\Submission;
use App\Models\MailingList;
use App\Models\SubmissionPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Mail\SubmissionReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Mail\SubmissionNotificationMail;
use Throwable;

trait IndicatorsTrait
{
    public function IndicatorCollection(array $indicatorIds = []): Collection
    {
        try {
            $query = Indicator::with([
                'organisation',
                'forms',
                'organisation.users',
                'organisation.users.roles',
            ]);

            if (!empty($indicatorIds)) {
                $query->whereIn('id', $indicatorIds);
            }


            $indicators = $query->get();

            $organisationCollection = collect();

            $indicators->each(function ($indicator) use ($organisationCollection) {
                try {
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
                } catch (Throwable $e) {
                    Log::error('Indicator transformation failed', [
                        'indicator_id' => $indicator->id ?? null,
                        'error' => $e->getMessage()
                    ]);
                }
            });

            return $organisationCollection;
        } catch (Throwable $e) {
            Log::critical('IndicatorCollection failed', [
                'indicatorIds' => $indicatorIds,
                'error' => $e->getMessage()
            ]);

            return collect(); // fail safe
        }
    }

    public function getIndicators($indicatorIds = [])
    {
        try {
            return $this->IndicatorCollection($indicatorIds);
        } catch (Throwable $e) {
            Log::error('getIndicators failed', [
                'indicatorIds' => $indicatorIds,
                'error' => $e->getMessage()
            ]);

            return collect();
        }
    }

    public function getIndicatorsByOrganisation($indicatorIds = [], $organisationIds = [])
    {
        try {
            $indicators = $this->getIndicators($indicatorIds);

            return $indicators->filter(function ($indicator) use ($organisationIds) {
                return $indicator['organisations']->contains(function ($organisation) use ($organisationIds) {
                    return in_array($organisation['organisation_id'], $organisationIds);
                });
            });
        } catch (Throwable $e) {
            Log::error('getIndicatorsByOrganisation failed', [
                'indicatorIds' => $indicatorIds,
                'organisationIds' => $organisationIds,
                'error' => $e->getMessage()
            ]);

            return collect();
        }
    }

    public function getEndingSoonSubmissionPeriods()
    {
        try {
            $now = Carbon::now();
            $startDate = Carbon::now()->startOfDay();
            $endDate = Carbon::now()->copy()->addDays(2)->endOfDay();

            $dates = SubmissionPeriod::query()
                ->selectRaw('ROW_NUMBER() OVER (ORDER BY date_established) AS rn ,COUNT(id) as count, date_established, date_ending, is_open,is_expired,financial_year_id,month_range_period_id,bypass_notification')
                ->where('bypass_notification', false)
                ->where('is_expired', false)
                ->whereBetween('date_ending', [$startDate, $endDate])
                ->groupBy(
                    'date_established',
                    'date_ending',
                    'is_open',
                    'is_expired',
                    'financial_year_id',
                    'month_range_period_id',
                    'bypass_notification'
                )
                ->get();

            foreach ($dates as $period) {
                try {
                    $endingDate = Carbon::parse($period->date_ending);
                    $daysLeft = $now->diffInDays($endingDate, false);

                    if ($daysLeft >= 0) {
                        $reminderType = match (true) {
                            $daysLeft >= 2 && $daysLeft < 3 => '3rd day reminder',
                            $daysLeft >= 1 && $daysLeft < 2 => '2nd day reminder',
                            $daysLeft >= 0 && $daysLeft < 1 => 'Last day reminder',
                            default => null,
                        };

                        if ($reminderType) {
                            $this->sendReminder($period->toArray(), $reminderType);
                        }
                    }
                } catch (Throwable $e) {
                    Log::warning('Reminder processing failed', [
                        'period' => $period->toArray(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::critical('getEndingSoonSubmissionPeriods failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function notifyExpiredSubmissionPeriods()
    {
        try {
            $submissionPeriods = SubmissionPeriod::query()
                ->selectRaw('
                    ROW_NUMBER() OVER (ORDER BY date_established) AS rn,
                    COUNT(id) AS count,
                    date_established,
                    date_ending,
                    is_open,
                    is_expired,
                    financial_year_id,
                    month_range_period_id
                ')
                ->groupBy(
                    'date_established',
                    'date_ending',
                    'is_open',
                    'is_expired',
                    'financial_year_id',
                    'month_range_period_id'
                )
                ->get();

            foreach ($submissionPeriods as $period) {
                try {
                    if ($period->date_ending && Carbon::parse($period->date_ending)->isPast()) {

                        $updated = SubmissionPeriod::where('date_established', $period->date_established)
                            ->where('date_ending', $period->date_ending)
                            ->where('bypass_notification', false)
                            ->where('is_expired', false)
                            ->update([
                                'is_expired' => true,
                                'is_open'    => false,
                                'is_restricted' => false
                            ]);

                        if ($updated) {
                            $this->sendNotification($period->toArray(), 'expired');
                        }
                    }
                } catch (Throwable $e) {
                    Log::error('Failed processing expired period', [
                        'period' => $period->toArray(),
                        'error' => $e->getMessage()
                    ]);
                }
            }
        } catch (Throwable $e) {
            Log::critical('notifyExpiredSubmissionPeriods failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendNotification(array $period, $notificationType)
    {
        try {
            $users = User::with('roles')
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['staff', 'external']);
                })
                ->get();

            $users->each(function ($user) use ($period, $notificationType) {
                try {
                    Mail::to($user->email)
                        ->bcc(config('app.debug_email'))
                        ->send(new SubmissionNotificationMail($period, $notificationType, $user));
                    usleep(1000000); // 1s delay to prevent mail server overload
                } catch (Throwable $e) {
                    Log::error('Mail send failed (notification)', [
                        'email' => $user->email,
                        'error' => $e->getMessage()
                    ]);
                }
            });
        } catch (Throwable $e) {
            Log::critical('sendNotification failed', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function sendReminder(array $period, $reminderType)
    {
        try {
            $users = User::with('roles')
                ->whereHas('roles', function ($query) {
                    $query->whereIn('name', ['staff', 'external']);
                })
                ->where('is_active', true)
                ->get();

            $debugEmail = config('app.debug_email');

            Log::info('Reminder job started', [
                'users_count' => $users->count(),
                'debug_email' => $debugEmail
            ]);

            $users->each(function ($user) use ($period, $reminderType, $debugEmail) {

                try {
                    // 🔴 1. Validate user email
                    if (empty($user->email)) {
                        Log::warning('Skipping user with no email', [
                            'user_id' => $user->id
                        ]);
                        return;
                    }

                    // 🔴 2. Validate debug email
                    if (empty($debugEmail)) {
                        Log::warning('Debug email is NULL in config');
                    }

                    Log::info('Sending reminder', [
                        'user_id' => $user->id,
                        'email' => $user->email
                    ]);

                    $mail = Mail::to($user->email);

                    // 🔴 Safe BCC
                    if (!empty($debugEmail)) {
                        $mail->bcc($debugEmail);
                    }

                    $mail->send(
                        new SubmissionReminderMail($period, $reminderType, $user)
                    );
                        usleep(1000000); // 3s delay to prevent mail server overload
                } catch (Throwable $e) {
                    Log::error('Mail send failed (reminder)', [
                        'user_id' => $user->id ?? null,
                        'email' => $user->email ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString() // 🔥 critical for debugging
                    ]);
                }
            });
        } catch (Throwable $e) {
            Log::critical('sendReminder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
