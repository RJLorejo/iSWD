<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Manager\DashboardController as ManagerDashboard;
use App\Http\Controllers\Supervisor\DashboardController as SupervisorDashboard;
use App\Http\Controllers\Technician\DashboardController as TechnicianDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Admin\DepartmentController;



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
    });

Route::middleware(['auth', 'role:Maintenance Manager'])
    ->group(function () {

        Route::get(
            '/manager/dashboard',
            [ManagerDashboard::class, 'index']
        )
            ->name('manager.dashboard');
    });

Route::middleware(['auth', 'role:Maintenance Supervisor'])
    ->group(function () {

        Route::get(
            '/supervisor/dashboard',
            [SupervisorDashboard::class, 'index']
        )
            ->name('supervisor.dashboard');
    });

Route::middleware(['auth', 'role:Maintenance Technician'])
    ->group(function () {

        Route::get(
            '/technician/dashboard',
            [TechnicianDashboard::class, 'index']
        )
            ->name('technician.dashboard');
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
