<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\LandingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;

/*
|--------------------------------------------------------------------------
| Customer Service
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\CustomerService\DashboardController as CustomerServiceDashboard;
use App\Http\Controllers\CustomerService\ComplaintController as CustomerServiceComplaintController;
use App\Http\Controllers\CustomerService\ConsumerController;
use App\Http\Controllers\CustomerService\ComplaintCategoryController;
use App\Http\Controllers\CustomerService\ComplaintVerificationController;

/*
|--------------------------------------------------------------------------
| Maintenance Manager
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Manager\ComplaintController as ManagerComplaintController;
use App\Http\Controllers\Manager\MaintenanceReviewController;

/*
|--------------------------------------------------------------------------
| Technician
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Technician\ComplaintController as TechnicianComplaintController;
use App\Http\Controllers\Technician\MaintenanceReportController;
use App\Http\Controllers\Technician\MaintenanceHistoryController;

/*
|--------------------------------------------------------------------------
| Consumer
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Consumer\DashboardController as ConsumerDashboardController;
use App\Http\Controllers\Consumer\ComplaintController as ConsumerComplaintController;
use App\Http\Controllers\Consumer\Auth\LoginController as ConsumerLoginController;
use App\Http\Controllers\Consumer\Auth\RegisterController as ConsumerRegisterController;


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
        | Complaint Actions
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/complaints/{complaint}/start',
            [TechnicianComplaintController::class, 'start']
        )->name('complaints.start');

        Route::post(
            '/complaints/{complaint}/complete',
            [TechnicianComplaintController::class, 'complete']
        )->name('complaints.complete');


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

Route::middleware(['auth', 'role:Consumer'])
    ->prefix('consumer')
    ->name('consumer.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | Dashboard
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/dashboard',
            [ConsumerDashboardController::class, 'index']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | Complaints
        |--------------------------------------------------------------------------
        */

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


        Route::get('/complaints/{complaint}/edit', [
            ConsumerComplaintController::class,
            'edit'
        ])->name('complaints.edit');

        Route::put('/complaints/{complaint}', [
            ConsumerComplaintController::class,
            'update'
        ])->name('complaints.update');


        Route::get(
            '/complaints/{complaint}',
            [ConsumerComplaintController::class, 'show']
        )->name('complaints.show');
    });



require __DIR__ . '/auth.php';
