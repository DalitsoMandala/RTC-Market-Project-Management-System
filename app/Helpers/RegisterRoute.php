<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Route;

class RegisterRoute
{


   public  function registerFormRoutes($prefix, $role)
    {
        // Route::get($prefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData::class);
        // Route::get($prefix . '/household-consumption-form/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
        // Route::get($prefix . '/household-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
        // Route::get($prefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
        Route::get($prefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);
        Route::get($prefix . '/report-form/view', App\Livewire\Forms\RtcMarket\Reports\View::class);

        Route::get($prefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-farmers/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-farmers/followup', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\AddFollowUp::class);

        Route::get($prefix . '/rtc-production-and-marketing-form-processors-and-traders/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-processors-and-traders/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-processors-and-traders/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-processors-and-traders/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
        Route::get($prefix . '/rtc-production-and-marketing-form-processors-and-traders/followup', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\AddFollowUp::class);

        // Route::get($prefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
        // Route::get($prefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
        // Route::get($prefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
        // Route::get($prefix . '/school-rtc-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Upload::class);


        Route::get($prefix . '/rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcConsumption\Add::class);
        Route::get($prefix . '/rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\RtcConsumption\View::class);
        Route::get($prefix . '/rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\RtcConsumption\View::class);
        Route::get($prefix . '/rtc-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcConsumption\Upload::class);

        Route::get($prefix . '/attendance-register/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\AttendanceRegister\Add::class);
        Route::get($prefix . '/attendance-register/view', App\Livewire\Forms\RtcMarket\AttendanceRegister\View::class);
        Route::get($prefix . '/attendance-register/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\AttendanceRegister\Upload::class);

        Route::get($prefix . '/rtc-actor-recruitment-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcRecruitment\Add::class)->name($prefix . '-rtc-actor-recruitment-form.add');
        Route::get($prefix . '/rtc-actor-recruitment-form/view', App\Livewire\Forms\RtcMarket\RtcRecruitment\View::class);
        Route::get($prefix . '/rtc-actor-recruitment-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcRecruitment\Upload::class);

        Route::get($prefix . '/seed-distribution-register/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\OtherForms\SeedBeneficiaries\Add::class);
        Route::get($prefix . '/seed-distribution-register/view', App\Livewire\OtherForms\SeedBeneficiaries\View::class);
        Route::get($prefix . '/seed-distribution-register/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\OtherForms\SeedBeneficiaries\Upload::class);
    }
}
