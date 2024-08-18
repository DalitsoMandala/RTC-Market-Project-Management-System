<?php

use App\Models\User;
use Ramsey\Uuid\Uuid;
use App\Jobs\RandomNames;
use App\Models\Indicator;
use Illuminate\Http\Request;
use App\Models\FinancialYear;
use App\Models\AssignedTarget;
use App\Helpers\AmountSplitter;
use App\Models\IndicatorTarget;
use App\Models\ResponsiblePerson;
use App\Helpers\IndicatorsContent;
use App\Livewire\Internal\Cip\Forms;
use Illuminate\Support\Facades\Route;
use App\Livewire\Internal\Cip\Reports;
use App\Livewire\Internal\Cip\Targets;
use App\Notifications\JobNotification;
use App\Models\IndicatorDisaggregation;
use App\Livewire\External\ViewIndicator;
use App\Livewire\Internal\Cip\Dashboard;
use App\Livewire\Internal\Cip\SubPeriod;
use App\Livewire\Internal\Cip\Indicators;
use App\Livewire\Internal\Cip\Assignments;
use App\Livewire\Internal\Cip\Submissions;
use App\Http\Controllers\TestingController;
use App\Livewire\Internal\Cip\ViewIndicators;
use App\Livewire\Internal\Cip\ViewSubmissions;
use App\Helpers\rtc_market\indicators\indicator_B2;
use App\Helpers\rtc_market\indicators\indicator_B4;
use App\Helpers\rtc_market\indicators\indicator_B5;
use App\Helpers\rtc_market\indicators\indicator_B6;
use App\Helpers\rtc_market\indicators\indicator_1_1_1;
use App\Helpers\rtc_market\indicators\indicator_2_2_2;
use App\Helpers\rtc_market\indicators\indicator_3_1_1;
use App\Helpers\rtc_market\indicators\indicator_3_5_4;
use App\Livewire\External\Dashboard as ExternalDashboard;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add as RTCMAddData;
use App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View as RTCMViewData;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData as HRCAddData;
use App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData as HRCViewData;
use App\Livewire\Internal\Cip\SubPeriodStaff;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));

// Test route (empty)
// Route::get('/test', function (Request $request) {
//     $indicators = IndicatorTarget::with('details')->get();

//     $indicators->each(function ($indicatorTarget) {
//         $people = ResponsiblePerson::where('indicator_id', $indicatorTarget->indicator_id)->get();

//         if ($indicatorTarget->target_value !== null && $indicatorTarget->type !== 'detail') {
//             $splits = (new AmountSplitter($people->count(), $indicatorTarget->target_value))->split();

//             $targets = $people->map(function ($person, $index) use ($splits, $indicatorTarget) {
//                 $data = [
//                     'organisation_id' => $person->organisation_id,
//                     'target_value' => $splits[$index],
//                     'indicator_target_id' => $indicatorTarget->id,
//                     'type' => $indicatorTarget->type,
//                     'current_value' => 0,
//                 ];

//                 //      AssignedTarget::create($data);


//             });



//             // Process $targets as needed
//         } elseif ($indicatorTarget->target_value === null && $indicatorTarget->type === 'detail') {
//             $targetDetails = $indicatorTarget->details;
//             $names = $targetDetails->mapWithKeys(function ($targetDetail) use ($people) {
//                 $splits = (new AmountSplitter($people->count(), $targetDetail->target_value))->split();

//                 $temp = $people->map(function ($person, $index) use ($splits, $targetDetail) {
//                     return [
//                         'organisation_id' => $person->organisation_id,
//                         'target_value' => $splits[$index],
//                         'type' => $targetDetail->type,
//                     ];
//                 });

//                 return [$targetDetail->name => $temp];
//             });


//             foreach ($people as $organisation) {
//                 $filteredNames = $names->map(function ($collection) use ($organisation) {
//                     return $collection->filter(function ($item) use ($organisation) {
//                         return $item['organisation_id'] === $organisation->organisation_id;
//                     })->first();
//                 });
//                 $finalArray = [];
//                 foreach ($filteredNames as $key => $item) {

//                     $finalArray[] = [
//                         'name' => $key,
//                         'target_value' => $item['target_value'],
//                         'type' => $item['type'],
//                     ];
//                 }
//                 AssignedTarget::create([
//                     'organisation_id' => $organisation->organisation_id,
//                     'target_value' => 0,
//                     'indicator_target_id' => $indicatorTarget->id,
//                     'type' => 'detail',
//                     'detail' => json_encode($finalArray),
//                     'current_value' => 0,
//                 ]);

//             }


//         }
//     });
// });

// TestingController route
Route::get('/export/{name}', [TestingController::class, 'index']);

// Profile route
Route::get('/profile', \App\Livewire\Profile\Details::class)
    ->middleware(['auth'])
    ->name('profile');

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin-dashboard');
    Route::get('/users', \App\Livewire\Admin\Users\ListUsers::class)->name('admin-users');
    // Route::get('/organisations', \App\Livewire\Admin\ManageOrganisations::class)->name('admin-organisations');
    // Route::get('/indicator-responsibilities', \App\Livewire\Admin\ManageResponsibilities::class)->name('admin-responsibilities');
    Route::get('/system-setup', \App\Livewire\Admin\System\Setup::class)->name('admin-setup');
    Route::get('/organisation-list', \App\Livewire\Admin\System\OrganisationList::class)->name('admin-organisation-list');
    Route::get('/cgiar-projects', \App\Livewire\Admin\Data\CgiarProjects::class)->name('admin-cgiar-projects');
    Route::get('/projects', \App\Livewire\Admin\Data\Projects::class)->name('admin-projects');
    Route::get('/reporting-periods', \App\Livewire\Admin\Data\ReportingPeriod::class)->name('admin-period');

});

// CIP Internal routes
Route::middleware(['auth', 'role:organiser'])->prefix('cip')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('cip-internal-dashboard');
    Route::get('/indicators', Indicators::class)->name('cip-internal-indicators');
    Route::get('/indicators/view/{id}', ViewIndicators::class)->name('cip-internal-indicator-view');
    Route::get('/forms', Forms::class)->name('cip-internal-forms');
    Route::get('/submissions', Submissions::class)->name('cip-internal-submissions');
    Route::get('/submissions/view/{batch_no}', ViewSubmissions::class)->name('cip-internal-submission-view');
    Route::get('/reports', Reports::class)->name('cip-internal-reports');
    Route::get('/submission-period', SubPeriod::class)->name('cip-internal-submission-period');

    Route::get('/indicators-and-leads', Assignments::class)->name('cip-leads');
    Route::get('/indicators-targets', Targets::class)->name('cip-targets');

    // Form routes
    $formPrefix = '/forms/{project}';
    $randId = Uuid::uuid4()->toString();
    Route::get($formPrefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData::class);
    Route::get($formPrefix . '/household-consumption-form/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
    Route::get($formPrefix . '/household-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
    Route::get($formPrefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/followup/{id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\AddFollowUp::class);


    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/followup/{id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\AddFollowUp::class);




    Route::get($formPrefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);


    Route::get($formPrefix . '/attendance-register/', App\Livewire\Forms\RtcMarket\AttendanceRegister\Add::class);
    //    / Route::get($formPrefix . '/attendance-register/view', App\Livewire\Forms\RtcMarket\AttendanceRegister\View::class);

});

// CIP Internal routes
Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(function () {
    Route::get('/dashboard', \App\Livewire\Internal\Staff\Dashboard::class)->name('cip-staff-dashboard');
    Route::get('/indicators', \App\Livewire\Internal\Staff\Indicators::class)->name('cip-staff-indicators');
    Route::get('/indicators/view/{id}', \App\Livewire\Internal\Staff\ViewIndicators::class)->name('cip-staff-indicator-view');
    Route::get('/forms', \App\Livewire\Internal\Staff\Forms::class)->name('cip-staff-forms');
    Route::get('/submissions', \App\Livewire\Internal\Staff\Submissions::class)->name('cip-staff-submissions');

    Route::get('/reports', \App\Livewire\Internal\Staff\Reports::class)->name('cip-staff-reports');
    Route::get('/submission-period', \App\Livewire\Internal\Staff\SubPeriod::class)->name('cip-staff-submission-period');

    // Form routes
    $formPrefix = '/forms/{project}';
    $randId = Uuid::uuid4()->toString();
    Route::get($formPrefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\AddData::class);
    Route::get($formPrefix . '/household-consumption-form/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
    Route::get($formPrefix . '/household-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\ViewData::class);
    Route::get($formPrefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/view', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/followup/{id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\AddFollowUp::class);


    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/followup/{id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\AddFollowUp::class);




    Route::get($formPrefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);


    Route::get($formPrefix . '/attendance-register/', App\Livewire\Forms\RtcMarket\AttendanceRegister\Add::class);
    //    / Route::get($formPrefix . '/attendance-register/view', App\Livewire\Forms\RtcMarket\AttendanceRegister\View::class);

});

// External routes
Route::middleware(['auth', 'role:external'])->prefix('external')->group(function () {
    Route::get('/dashboard', ExternalDashboard::class)->name('external-dashboard');
    Route::get('/indicators', \App\Livewire\External\Indicators::class)->name('external-indicators');
    Route::get('/indicators/view/{id}', ViewIndicator::class)->name('external-indicator-view');
    Route::get('/forms', \App\Livewire\External\Forms::class)->name('external-forms');
    Route::get('/submissions', \App\Livewire\External\Submissions::class)->name('external-submissions');
    Route::get('/submission-periods', \App\Livewire\External\SubmissionPeriods::class)->name('external-submission-period');
    // Form routes
    $formPrefix = '/forms/{project}';
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/household-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', HRCAddData::class);
    Route::get($formPrefix . '/household-consumption-form/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/{batch}/view', HRCViewData::class);
    Route::get($formPrefix . '/household-consumption-form/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}/{uuid}', App\Livewire\Forms\RtcMarket\HouseholdRtcConsumption\Upload::class);
    Route::get($formPrefix . '/aggregate/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\Reports\Add::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', RTCMAddData::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionFarmers\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-farmers/{batch}/view', RTCMViewData::class);

    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Add::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/upload/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\Upload::class);
    Route::get($formPrefix . '/rtc-production-and-marketing-form-processors/{batch}/view', App\Livewire\Forms\RtcMarket\RtcProductionProcessors\View::class);

    Route::get($formPrefix . '/school-rtc-consumption-form/add/{form_id}/{indicator_id}/{financial_year_id}/{month_period_id}/{submission_period_id}', App\Livewire\Forms\RtcMarket\SchoolConsumption\Add::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
    Route::get($formPrefix . '/school-rtc-consumption-form/{batch}/view', App\Livewire\Forms\RtcMarket\SchoolConsumption\View::class);
});


Route::middleware(['auth', 'role:donor'])->prefix('executive')->group(function () {
    Route::get('/dashboard', \App\Livewire\Donor\Dashboard::class)->name('donor-dashboard');
    Route::get('/indicators', \App\Livewire\Donor\Indicators::class)->name('donor-indicators');
    Route::get('/indicators/view/{id}', \App\Livewire\Donor\ViewIndicator::class)->name('donor-indicator-view');

});
// Authentication routes
require __DIR__ . '/auth.php';
