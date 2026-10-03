<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
use App\Models\Department;
use App\Models\Position;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | User Statistics
        |--------------------------------------------------------------------------
        */

        $totalUsers = User::count();

        $activeUsers = User::where('is_active', true)->count();

        $inactiveUsers = User::where('is_active', false)->count();


        /*
        |--------------------------------------------------------------------------
        | Organization Statistics
        |--------------------------------------------------------------------------
        */

        $totalDepartments = Department::count();

        $activeDepartments = Department::where(
            'is_active',
            true
        )->count();

        $totalPositions = Position::count();

        $activePositions = Position::where(
            'is_active',
            true
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Consumer Statistics
        |--------------------------------------------------------------------------
        */

        $totalConsumers = Consumer::count();

        $activeConsumers = Consumer::where(
            'is_active',
            true
        )->count();


        /*
        |--------------------------------------------------------------------------
        | Pending Consumer Verifications
        |--------------------------------------------------------------------------
        */

        $pendingVerifications = Consumer::query()
            ->where(
                'registration_source',
                'Self Registration'
            )
            ->where(
                'verification_status',
                'Pending Verification'
            )
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Recent Consumer Registrations
        |--------------------------------------------------------------------------
        */

        $recentRegistrations = Consumer::query()
            ->with([
                'user',
                'address',
            ])
            ->where(
                'registration_source',
                'Self Registration'
            )
            ->latest()
            ->take(5)
            ->get();


        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'inactiveUsers',
            'totalDepartments',
            'activeDepartments',
            'totalPositions',
            'activePositions',
            'totalConsumers',
            'activeConsumers',
            'pendingVerifications',
            'recentRegistrations',
        ));
    }
}
