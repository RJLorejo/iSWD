<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ComplaintFeedback;
use App\Models\Consumer;

class DashboardController extends Controller
{
    public function index()
    {
        $totalConsumers = Consumer::count();

        $newConsumers = Consumer::whereDate(
            'created_at',
            today()
        )->count();

        $pendingComplaints = Complaint::where(
            'status',
            'Pending'
        )->count();

        $newFeedback = ComplaintFeedback::where(
            'handling_status',
            'New'
        )->count();

        $complaintStats = [
            'Pending' => Complaint::where('status', 'Pending')->count(),
            'Verified' => Complaint::where('status', 'Verified')->count(),
            'Assigned' => Complaint::where('status', 'Assigned')->count(),
            'In Progress' => Complaint::where('status', 'In Progress')->count(),
            'Accomplished' => Complaint::where('status', 'Accomplished')->count(),
            'Completed' => Complaint::where('status', 'Completed')->count(),
            'Closed' => Complaint::where('status', 'Closed')->count(),
        ];

        $recentConsumers = Consumer::query()
            ->with('address')
            ->latest()
            ->limit(5)
            ->get();

        $recentComplaints = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
            ])
            ->latest()
            ->limit(6)
            ->get();

        $attentionFeedback = ComplaintFeedback::query()
            ->with([
                'consumer',
                'complaint',
            ])
            ->whereIn('handling_status', [
                'New',
                'Follow-up Required',
                'Follow-up In Progress',
            ])
            ->latest()
            ->limit(5)
            ->get();

        return view(
            'customer-service.dashboard',
            compact(
                'totalConsumers',
                'newConsumers',
                'pendingComplaints',
                'newFeedback',
                'complaintStats',
                'recentConsumers',
                'recentComplaints',
                'attentionFeedback'
            )
        );
    }
}
