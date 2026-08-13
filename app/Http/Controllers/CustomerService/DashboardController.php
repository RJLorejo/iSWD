<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Consumer;
use App\Models\ServiceConnection;

class DashboardController extends Controller
{
    public function index()
    {
        return view('customer-service.dashboard', [

            'totalConsumers' => Consumer::count(),

            'newConsumers' => Consumer::whereDate(
                'created_at',
                today()
            )->count(),

            'activeConnections' => ServiceConnection::where(
                'status',
                'Active'
            )->count(),

            'pendingComplaints' => Complaint::where(
                'status',
                'Pending'
            )->count(),

            'recentConsumers' => Consumer::latest()
                ->take(5)
                ->get(),

            'recentComplaints' => Complaint::latest()
                ->take(5)
                ->get(),

        ]);
    }
}
