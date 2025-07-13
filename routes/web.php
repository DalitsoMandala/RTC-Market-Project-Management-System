<?php


use App\Models\Indicator;

use App\Livewire\Internal\Cip\Forms;

use Illuminate\Support\Facades\Route;

use App\Livewire\Internal\Cip\Reports;

use App\Livewire\Internal\Cip\Targets;

use App\Livewire\External\ViewIndicator;
use App\Livewire\Internal\Cip\Dashboard;
use App\Livewire\Internal\Cip\SubPeriod;
use App\Livewire\Internal\Cip\Indicators;
use App\Livewire\Internal\Cip\Assignments;
use App\Livewire\Internal\Cip\Submissions;

use App\Livewire\Internal\Cip\ViewIndicators;

use App\Livewire\External\Dashboard as ExternalDashboard;

use App\Models\SeedBeneficiary;

// Redirect root to login
Route::get('/', fn() => redirect()->route('login'));




Route::get('/logout', function () {


    return abort(404);
});



Route::get('/download-templates', [App\Http\Controllers\FormsExportController::class, 'export'])->name('download-templates');



function registerFormRoutes($prefix, $role)
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
// Profile route
Route::get('/profile', \App\Livewire\Profile\Details::class)
    ->middleware(['auth'])
    ->name('profile');

// Admin routes
Route::middleware([
    'auth',
    'role:admin',
    'verified'

])->prefix('admin')->group(function () {
    Route::get('/dashboard', \App\Livewire\Admin\Dashboard::class)->name('admin-dashboard');
    Route::get('/users', \App\Livewire\Admin\Users\ListUsers::class)->name('admin-users');
    Route::get('/system-setup', \App\Livewire\Admin\System\Setup::class)->name('admin-setup');
    Route::get('/cgiar-projects', \App\Livewire\Admin\Data\CgiarProjects::class)->name('admin-cgiar-projects');
    Route::get('/projects', \App\Livewire\Admin\Data\Projects::class)->name('admin-projects');
    Route::get('/reporting-periods', \App\Livewire\Admin\Data\ReportingPeriod::class)->name('admin-period');
    Route::get('/indicators', \App\Livewire\Admin\Data\Indicators::class)->name('admin-indicators');
    Route::get('/indicators/view/{id}', \App\Livewire\Admin\Data\ViewIndicators::class)->where('id', '[0-9]+')->name('admin-indicator-view');
    Route::get('/indicators/lead-partners', \App\Livewire\Admin\Data\LeadPartners::class)->name('admin-leads');
    Route::get('/indicators/sources', \App\Livewire\Admin\Data\IndicatorSources::class)->name('admin-sources');
    Route::get('/indicators-targets', \App\Livewire\Admin\Data\IndicatorTargets::class)->name('admin-indicators-targets');
    Route::get('/assigned-targets', \App\Livewire\Admin\Data\AssignedTargets::class)->name('admin-assigned-targets');
    Route::get('/forms', \App\Livewire\Admin\Operations\Forms::class)->name('admin-forms');
    Route::get('/submissions/{batch?}', \App\Livewire\Admin\Operations\Submissions::class)->name('admin-submissions');
    Route::get('/reports', \App\Livewire\Admin\Operations\Reports::class)->name('admin-reports');
    Route::get('/baseline/{baselineDataId?}', App\Livewire\Baseline\UpdateBaselineData::class)->name('admin-baseline');
    Route::get('/submission-period', \App\Livewire\Admin\Operations\SubmissionPeriod::class)->name('admin-submission-period');
    Route::get('/queues-monitor', \App\Livewire\Admin\Operations\Jobs::class)->name('admin-jobs');
    Route::get('/targets', App\Livewire\Targets\View::class)->name('admin-targets');
    Route::get('/standard-targets', App\Livewire\Targets\SubmissionTargets::class);
    Route::get('user-roles', \App\Livewire\Admin\Users\UserRoles::class)->name('admin-user-roles');
    Route::get('/marketing/manage-data', \App\Livewire\Internal\Cip\Markets\ManageData::class)->name('admin-markets-manage-data');
    Route::get('marketing/submit-data', \App\Livewire\Internal\Cip\Markets\SubmitData::class)->name('admin-markets-submit-data');
    // Form routes
    registerFormRoutes('/forms/{project}', 'admin');
});

// CIP Internal routes
Route::middleware([
    'auth',
    'role:manager',
    'check_baseline'
])->prefix('cip')->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('cip-dashboard');
    Route::get('/indicators', Indicators::class)->name('cip-indicators');
    Route::get('/indicators/view/{id}', ViewIndicators::class)->where('id', '[0-9]+')->name('cip-indicator-view');
    Route::get('/forms', Forms::class)->name('cip-forms');
    Route::get('/submissions/{batch?}', Submissions::class)->name('cip-submissions');
    Route::get('/reports', Reports::class)->name('cip-reports');
    Route::get('/submission-period', SubPeriod::class)->name('cip-submission-period');
    Route::get('/targets', App\Livewire\Targets\View::class)->name('cip-targets-view');
    Route::get('/standard-targets', App\Livewire\Targets\SubmissionTargets::class);
    Route::get('/indicators-and-leads', Assignments::class)->name('cip-leads');
    Route::get('/indicators-targets', Targets::class)->name('cip-targets');
    Route::get('/baseline/{baselineDataId?}', App\Livewire\Baseline\UpdateBaselineData::class)->where('id', '[0-9]+')->name('cip-baseline');
    Route::get('/marketing/manage-data', \App\Livewire\Internal\Cip\Markets\ManageData::class)->name('cip-markets-manage-data');
    Route::get('marketing/submit-data', \App\Livewire\Internal\Cip\Markets\SubmitData::class)->name('cip-markets-submit-data');
    registerFormRoutes('/forms/{project}', 'manager');
});

// CIP Internal routes
Route::middleware([
    'auth',
    'role:staff',

])->prefix('staff')->group(function () {
    Route::get('/dashboard', \App\Livewire\Internal\Staff\Dashboard::class)->name('cip-staff-dashboard');
    Route::get('/indicators', \App\Livewire\Internal\Staff\Indicators::class)->name('cip-staff-indicators');
    Route::get('/indicators/view/{id}', \App\Livewire\Internal\Staff\ViewIndicators::class)->where('id', '[0-9]+')->name('cip-staff-indicator-view');
    Route::get('/forms', \App\Livewire\Internal\Staff\Forms::class)->name('cip-staff-forms');
    Route::get('/submissions/{batch?}', \App\Livewire\Internal\Staff\Submissions::class)->name('cip-staff-submissions');
    Route::get('/targets', App\Livewire\Targets\View::class);

    Route::get('/reports', \App\Livewire\Internal\Staff\Reports::class)->name('cip-staff-reports');
    Route::get('/submission-period', \App\Livewire\Internal\Staff\SubPeriod::class)->name('cip-staff-submission-period');


    registerFormRoutes('/forms/{project}', 'staff');
});


Route::middleware([
    'auth',
    'role:project_manager',

])->prefix('cip/project-manager')->group(function () {
    Route::get('/dashboard', \App\Livewire\Internal\Manager\Dashboard::class)->name('project_manager-dashboard');
    Route::get('/indicators', \App\Livewire\Internal\Manager\Indicators::class)->name('project_manager-indicators');
    Route::get('/indicators/view/{id}', \App\Livewire\Internal\Manager\ViewIndicator::class)->where('id', '[0-9]+')->name('project_manager-indicator-view');
    Route::get('/forms', \App\Livewire\Internal\Manager\Forms::class)->name('project_manager-forms');
    Route::get('/reports', \App\Livewire\Internal\Manager\Reports::class)->name('project_manager-reports');
    Route::get('/targets', App\Livewire\Targets\View::class);
    Route::get('/marketing/manage-data', \App\Livewire\Internal\Cip\Markets\ManageData::class)->name('project_manager-markets-manage-data');
    Route::get('marketing/submit-data', \App\Livewire\Internal\Cip\Markets\SubmitData::class)->name('project_manager-markets-submit-data');
    registerFormRoutes('/forms/{project}', 'project_manager');
});
// External routes
Route::middleware([
    'auth',
    'role:external',

])->prefix('external')->group(function () {
    Route::get('/dashboard', ExternalDashboard::class)->name('external-dashboard');
    Route::get('/indicators', \App\Livewire\External\Indicators::class)->name('external-indicators');
    Route::get('/indicators/view/{id}', ViewIndicator::class)->where('id', '[0-9]+')->name('external-indicator-view');
    Route::get('/forms', \App\Livewire\External\Forms::class)->name('external-forms');
    Route::get('/submissions/{batch?}', \App\Livewire\External\Submissions::class)->name('external-submissions');
    Route::get('/submission-periods', \App\Livewire\External\SubmissionPeriods::class)->name('external-submission-period');
    Route::get('/reports', \App\Livewire\External\Reports::class)->name('external-reports');
    //   Route::get('/targets', App\Livewire\Targets\View::class)->name('external-targets');
    //  Route::get('/standard-targets', App\Livewire\Targets\SubmissionTargets::class);

    registerFormRoutes('/forms/{project}', 'external');
});


// Authentication routes
require __DIR__ . '/auth.php';
