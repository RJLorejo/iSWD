<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;

use App\Http\Controllers\CustomerService\DashboardController;
use App\Http\Controllers\CustomerService\ComplaintController;
use App\Http\Controllers\CustomerService\ConsumerController;
use App\Http\Controllers\CustomerService\ComplaintCategoryController;
use App\Http\Controllers\CustomerService\ComplaintVerificationController;

use App\Http\Controllers\Manager\ComplaintController as ManagerComplaintController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;

use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Technician\ComplaintController as TechnicianComplaintController;
use App\Http\Controllers\Technician\MaintenanceReportController;
use App\Http\Controllers\Technician\MaintenanceHistoryController;



Route::get('/', [LandingController::class, 'index'])
    ->name('landing');

Route::middleware(['auth', 'role:Administrator'])
    ->group(function () {

        Route::get(
            '/admin/dashboard',
            [AdminDashboard::class, 'index']
        )
            ->name('admin.dashboard');
    });

Route::middleware(['auth', 'role:Administrator'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::resource('users', UserController::class);

        Route::post(
            'users/{user}/reset-password',
            [UserController::class, 'resetPassword']
        )->name('users.reset');

        Route::post(
            'users/{user}/toggle',
            [UserController::class, 'toggle']
        )->name('users.toggle');
    });

Route::prefix('admin')
    ->middleware(['auth', 'role:Administrator'])
    ->name('admin.')
    ->group(function () {

        Route::resource('departments', DepartmentController::class);

        Route::post(
            'departments/{department}/toggle',
            [DepartmentController::class, 'toggle']
        )->name('departments.toggle');

        Route::resource('positions', PositionController::class);
    });



Route::middleware(['auth', 'role:Maintenance Manager'])
    ->prefix('maintenance-manager')
    ->name('maintenance-manager.')
    ->group(function () {

        Route::get(
            '/dashboard',
            [ManagerDashboard::class, 'index']
        )->name('dashboard');


        // Complaints waiting for technician assignment
        Route::get(
            '/complaints',
            [ManagerComplaintController::class, 'index']
        )->name('complaints.index');

        // View complaint details
        Route::get(
            '/complaints/{complaint}',
            [ManagerComplaintController::class, 'show']
        )->name('complaints.show');

        // Assign complaint to maintenance technician
        Route::post(
            '/complaints/{complaint}/assign',
            [ManagerComplaintController::class, 'assign']
        )->name('complaints.assign');
    });





Route::middleware(['auth', 'role:Customer Service'])

    ->prefix('customer-service')

    ->name('customer-service.')

    ->group(function () {

        Route::get(
            '/dashboard',
            [DashboardController::class, 'index']
        )
            ->name('dashboard');

        Route::resource(
            'consumers',
            ConsumerController::class
        );

        Route::resource(
            'complaints',
            ComplaintController::class
        );
        Route::resource(
            'complaint-categories',
            ComplaintCategoryController::class
        )->except(['show']);


        Route::post(
            '/complaints/{complaint}/verify',
            [ComplaintController::class, 'verify']
        )->name('complaints.verify');



        Route::post(
            '/complaints/{complaint}/reject',
            [ComplaintController::class, 'reject']
        )->name('complaints.reject');


        Route::get(
            '/complaint-verification',
            [ComplaintVerificationController::class, 'index']
        )->name('complaint-verification.index');



    });



Route::middleware(['auth', 'role:Maintenance Technician'])
    ->prefix('technician')
    ->name('technician.')
    ->group(function () {

        Route::get(
            '/technician/dashboard',
            [TechnicianDashboard::class, 'index']
        )
            ->name('dashboard');

        Route::get(
            '/complaints',
            [TechnicianComplaintController::class, 'index']
        )->name('complaints.index');

        Route::get(
            '/complaints/{complaint}',
            [TechnicianComplaintController::class, 'show']
        )->name('complaints.show');

        Route::post(
            '/complaints/{complaint}/start',
            [TechnicianComplaintController::class, 'start']
        )->name('complaints.start');

        Route::post(
            '/complaints/{complaint}/complete',
            [TechnicianComplaintController::class, 'complete']
        )->name('complaints.complete');

        // Maintenance Report
        Route::get('/reports/maintenance', [
            MaintenanceReportController::class,
            'index'
        ])->name('reports.maintenance');

        Route::get('/reports/maintenance/print', [
            MaintenanceReportController::class,
            'print'
        ])->name('reports.maintenance.print');


        /*
        |--------------------------------------------------------------------------
        | Maintenance Reports
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-reports',
            [
                MaintenanceReportController::class,
                'index'
            ]
        )->name(
            'maintenance-reports.index'
        );

        Route::get(
            '/complaints/{complaint}/maintenance-report',
            [
                MaintenanceReportController::class,
                'create'
            ]
        )->name(
            'maintenance-reports.create'
        );

        Route::post(
            '/complaints/{complaint}/maintenance-report/start',
            [
                MaintenanceReportController::class,
                'start'
            ]
        )->name(
            'maintenance-reports.start'
        );

        Route::post(
            '/complaints/{complaint}/maintenance-report',
            [
                MaintenanceReportController::class,
                'store'
            ]
        )->name(
            'maintenance-reports.store'
        );

        Route::get(
            '/complaints/{complaint}/maintenance-report/view',
            [
                MaintenanceReportController::class,
                'show'
            ]
        )->name(
            'maintenance-reports.show'
        );

        Route::get(
            '/complaints/{complaint}/maintenance-report/print',
            [
                MaintenanceReportController::class,
                'printReport'
            ]
        )->name(
            'maintenance-reports.print'
        );

        Route::get(
            '/maintenance-reports/print',
            [
                MaintenanceReportController::class,
                'print'
            ]
        )->name(
            'maintenance-reports.print-summary'
        );

                /*
        |--------------------------------------------------------------------------
        | Maintenance History
        |--------------------------------------------------------------------------
        */

        Route::get(
            '/maintenance-history',
            [
                MaintenanceHistoryController::class,
                'index',
            ]
        )->name(
            'maintenance-history.index'
        );

        Route::get(
            '/maintenance-history/{complaint}',
            [
                MaintenanceHistoryController::class,
                'show',
            ]
        )->name(
            'maintenance-history.show'
        );
    });



Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'show'])
        ->name('profile.show');

    Route::get('/profile/edit', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::put('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])
        ->name('profile.password');
});


Route::middleware(['auth'])->group(function () {

    Route::get('/search', [SearchController::class, 'index'])
        ->name('search');
});



require __DIR__ . '/auth.php';



//admin pass: Admin@12345
//cs pass: 6dEY8nz5Mq
//mm pass: C9fIpt9fm5
//mt pass: hPeVeLSvMr

// //Step 1 — Technician Maintenance Report ← NEXT

// Step 2 — Maintenance History / Timeline

// Step 3 — Before/After Photos

// Step 4 — Maintenance Materials & Parts

// Step 5 — Technician Work Report validation

// Step 6 — Supervisor review/approval

// Step 7 — Knowledge Repository

// Step 8 — AI Repair Case Recommendation
