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
use App\Http\Controllers\CustomerService\ProfileController as CustomerServiceProfileController;

use App\Http\Controllers\Manager\ComplaintController as ManagerComplaintController;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;

use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Technician\ComplaintController as TechnicianComplaintController;
use App\Http\Controllers\Technician\MaintenanceReportController;



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


        Route::get('/customer-service/profile', [CustomerServiceProfileController::class, 'show'])
            ->name('profile.show');

        Route::get('/customer-service/profile/edit', [CustomerServiceProfileController::class, 'edit'])
            ->name('profile.edit');

        Route::put('/customer-service/profile', [ProfileController::class, 'update'])
            ->name('profile.update');

        Route::put('/customer-service/profile/password', [ProfileController::class, 'updatePassword'])
            ->name('profile.password');
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


