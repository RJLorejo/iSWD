<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;


use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\ConsumerVerificationController;


use App\Http\Controllers\CustomerService\DashboardController as CustomerServiceDashboard;
use App\Http\Controllers\CustomerService\ComplaintController as CustomerServiceComplaintController;
use App\Http\Controllers\CustomerService\ConsumerController;
use App\Http\Controllers\CustomerService\ComplaintCategoryController;
use App\Http\Controllers\CustomerService\ComplaintVerificationController;
use App\Http\Controllers\CustomerService\DivisionController;


use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Manager\ComplaintController as ManagerComplaintController;
use App\Http\Controllers\Manager\MaintenanceReviewController;


use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Technician\ComplaintController as TechnicianComplaintController;
use App\Http\Controllers\Technician\MaintenanceReportController;
use App\Http\Controllers\Technician\MaintenanceHistoryController;


use App\Http\Controllers\Consumer\DashboardController as ConsumerDashboardController;
use App\Http\Controllers\Consumer\ComplaintController as ConsumerComplaintController;
use App\Http\Controllers\Consumer\Auth\LoginController as ConsumerLoginController;
use App\Http\Controllers\Consumer\Auth\RegisterController as ConsumerRegisterController;
use App\Http\Controllers\Consumer\ServiceAnnouncementController;
use App\Http\Controllers\Consumer\AIController;
use App\Http\Controllers\Consumer\RegistrationStatusController;
/*
|--------------------------------------------------------------------------
| Landing Page
|--------------------------------------------------------------------------
*/

Route::get('/', [LandingController::class, 'index'])
    ->name('landing');


/*
|--------------------------------------------------------------------------
| GENERAL AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
|
| These are shared by all authenticated users.
|
*/

Route::middleware('auth')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');


    /*
    |--------------------------------------------------------------------------
    | Global Search
    |--------------------------------------------------------------------------
    */

    Route::get('/search', [SearchController::class, 'index'])
        ->name('search');
});


/*
|--------------------------------------------------------------------------
| ADMINISTRATOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get('/dashboard', [AdminDashboard::class, 'index'])
            ->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | User Management
        |--------------------------------------------------------------------------
        */
        Route::get(
            '/users/print/report',
            [UserController::class, 'print']
        )->name('users.print');


        Route::resource('users', UserController::class);

        Route::post(
            '/users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset');

        Route::post(
            '/users/{user}/toggle',
            [UserController::class, 'toggle']
        )->name('users.toggle');




        /*
        |--------------------------------------------------------------------------
        | Departments
        |--------------------------------------------------------------------------
        */

        Route::resource('departments', DepartmentController::class);

        Route::post(
            '/departments/{department}/toggle',
            [DepartmentController::class, 'toggle']
        )->name('departments.toggle');


        /*
        |--------------------------------------------------------------------------
        | Positions
        |--------------------------------------------------------------------------
        */

        Route::resource('positions', PositionController::class);

        /*
|--------------------------------------------------------------------------
| Consumer Registration Verification
|--------------------------------------------------------------------------
*/

        Route::get(
            '/consumer-verifications',
            [ConsumerVerificationController::class, 'index']
        )->name('consumer-verifications.index');


        Route::get(
            '/consumer-verifications/{consumer}',
            [ConsumerVerificationController::class, 'show']
        )->name('consumer-verifications.show');


        Route::patch(
            '/consumer-verifications/{consumer}/approve',
            [ConsumerVerificationController::class, 'approve']
        )->name('consumer-verifications.approve');


        Route::patch(
            '/consumer-verifications/{consumer}/reject',
            [ConsumerVerificationController::class, 'reject']
        )->name('consumer-verifications.reject');
    });


/*
|--------------------------------------------------------------------------
| CUSTOMER SERVICE
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Customer Service'])
    ->prefix('customer-service')
    ->name('customer-service.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [CustomerServiceDashboard::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Consumers
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'consumers',
            ConsumerController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'complaints',
            CustomerServiceComplaintController::class
        );


        /*
        |--------------------------------------------------------------------------
        | Complaint Categories
        |--------------------------------------------------------------------------
        */

        Route::resource(
            'complaint-categories',
            ComplaintCategoryController::class
        )->except(['show']);


        Route::resource(
            'divisions',
            DivisionController::class
        )->except([
            'show',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Complaint Verification
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/complaint-verification',
            [ComplaintVerificationController::class, 'index']
        )->name('complaint-verification.index');


        Route::post(
            '/complaints/{complaint}/verify',
            [CustomerServiceComplaintController::class, 'verify']
        )->name('complaints.verify');


        Route::post(
            '/complaints/{complaint}/reject',
            [CustomerServiceComplaintController::class, 'reject']
        )->name('complaints.reject');
    });


/*
|--------------------------------------------------------------------------
| MAINTENANCE MANAGER / SUPERVISOR
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Maintenance Manager'])
    ->prefix('maintenance-manager')
    ->name('maintenance-manager.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [ManagerDashboard::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/complaints',
            [ManagerComplaintController::class, 'index']
        )->name('complaints.index');


        Route::get(
            '/complaints/{complaint}/report',
            [ManagerComplaintController::class, 'report']
        )->name('complaints.report');


        Route::get(
            '/complaints/{complaint}',
            [ManagerComplaintController::class, 'show']
        )->name('complaints.show');

        Route::post(
            '/complaints/{complaint}/assign',
            [ManagerComplaintController::class, 'assign']
        )->name('complaints.assign');


        /*
        |--------------------------------------------------------------------------
        | Maintenance Report Review
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-reviews',
            [MaintenanceReviewController::class, 'index']
        )->name('maintenance-reviews.index');

        Route::get(
            '/maintenance-reviews/{maintenanceReport}',
            [MaintenanceReviewController::class, 'show']
        )->name('maintenance-reviews.show');

        Route::post(
            '/maintenance-reviews/{maintenanceReport}/approve',
            [MaintenanceReviewController::class, 'approve']
        )->name('maintenance-reviews.approve');

        Route::post(
            '/maintenance-reviews/{maintenanceReport}/return',
            [MaintenanceReviewController::class, 'returnForCorrection']
        )->name('maintenance-reviews.return');
    });


/*
|--------------------------------------------------------------------------
| MAINTENANCE TECHNICIAN
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:Maintenance Technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [TechnicianDashboard::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Assigned Complaints
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/complaints',
            [TechnicianComplaintController::class, 'index']
        )->name('complaints.index');

        Route::get(
            '/complaints/{complaint}',
            [TechnicianComplaintController::class, 'show']
        )->name('complaints.show');


        /*
        |--------------------------------------------------------------------------
        | Maintenance Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-reports',
            [MaintenanceReportController::class, 'index']
        )->name('maintenance-reports.index');

        Route::get(
            '/complaints/{complaint}/maintenance-report',
            [MaintenanceReportController::class, 'create']
        )->name('maintenance-reports.create');

        Route::post(
            '/complaints/{complaint}/maintenance-report/start',
            [MaintenanceReportController::class, 'start']
        )->name('maintenance-reports.start');

        Route::post(
            '/complaints/{complaint}/maintenance-report',
            [MaintenanceReportController::class, 'store']
        )->name('maintenance-reports.store');

        Route::get(
            '/complaints/{complaint}/maintenance-report/view',
            [MaintenanceReportController::class, 'show']
        )->name('maintenance-reports.show');

        Route::get(
            '/complaints/{complaint}/maintenance-report/print',
            [MaintenanceReportController::class, 'printReport']
        )->name('maintenance-reports.print');


        /*
        |--------------------------------------------------------------------------
        | Maintenance Report Summary
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/reports/maintenance',
            [MaintenanceReportController::class, 'index']
        )->name('reports.maintenance');

        Route::get(
            '/reports/maintenance/print',
            [MaintenanceReportController::class, 'print']
        )->name('reports.maintenance.print');


        /*
        |--------------------------------------------------------------------------
        | Maintenance History
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-history',
            [MaintenanceHistoryController::class, 'index']
        )->name('maintenance-history.index');

        Route::get(
            '/maintenance-history/{complaint}',
            [MaintenanceHistoryController::class, 'show']
        )->name('maintenance-history.show');
    });


/*
|--------------------------------------------------------------------------
| CONSUMER AUTHENTICATION
|--------------------------------------------------------------------------
|
| Consumers have a separate login page.
| They still use the same users table and Consumer role.
|
*/

Route::middleware('guest')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Consumer Login
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/consumer/login',
        [ConsumerLoginController::class, 'create']
    )->name('consumer.login');

    Route::post(
        '/consumer/login',
        [ConsumerLoginController::class, 'store']
    )->name('consumer.login.store');


    /*
    |--------------------------------------------------------------------------
    | Consumer Registration
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/consumer/register',
        [ConsumerRegisterController::class, 'create']
    )->name('consumer.register');

    Route::post(
        '/consumer/register',
        [ConsumerRegisterController::class, 'store']
    )->name('consumer.register.store');
});


/*
|--------------------------------------------------------------------------
| CONSUMER PORTAL
|--------------------------------------------------------------------------
*/
/*
|--------------------------------------------------------------------------
| CONSUMER REGISTRATION STATUS
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:Consumer',
])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {

        Route::get(
            '/registration-status',
            [RegistrationStatusController::class, 'show']
        )->name('registration.status');

        Route::get(
            '/registration/correct',
            [RegistrationStatusController::class, 'edit']
        )->name('registration.edit');

        Route::put(
            '/registration/resubmit',
            [RegistrationStatusController::class, 'update']
        )->name('registration.update');
    });


/*
|--------------------------------------------------------------------------
| VERIFIED CONSUMER PORTAL
|--------------------------------------------------------------------------
*/

Route::middleware([
    'auth',
    'role:Consumer',
    'consumer.verified',
])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [ConsumerDashboardController::class, 'index']
        )->name('dashboard');


        Route::get(
            '/complaints',
            [ConsumerComplaintController::class, 'index']
        )->name('complaints.index');


        Route::get(
            '/complaints/create',
            [ConsumerComplaintController::class, 'create']
        )->name('complaints.create');


        Route::post(
            '/complaints',
            [ConsumerComplaintController::class, 'store']
        )->name('complaints.store');


        Route::get(
            '/complaints/{complaint}/edit',
            [ConsumerComplaintController::class, 'edit']
        )->name('complaints.edit');


        Route::put(
            '/complaints/{complaint}',
            [ConsumerComplaintController::class, 'update']
        )->name('complaints.update');


        Route::get(
            '/complaints/{complaint}',
            [ConsumerComplaintController::class, 'show']
        )->name('complaints.show');


        Route::get(
            '/announcements',
            [ServiceAnnouncementController::class, 'index']
        )->name('announcements.index');


        Route::get(
            '/announcements/{serviceAnnouncement}',
            [ServiceAnnouncementController::class, 'show']
        )->name('announcements.show');


        Route::get(
            '/ai-assistant',
            [AIController::class, 'index']
        )->name('ai.index');


        Route::post(
            '/ai-assistant/ask',
            [AIController::class, 'ask']
        )->name('ai.ask');
    });


require __DIR__ . '/auth.php';
