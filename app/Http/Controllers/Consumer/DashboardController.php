<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\ServiceAnnouncement;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    public function index()
    {

        $consumer = Auth::user()->consumer;

        if (!$consumer) {
            abort(403, 'Consumer profile not found.');
        }

        $baseQuery = Complaint::where(
            'consumer_id',
            $consumer->id
        );

        $activeComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'Pending',
                'Verified',
                'Assigned',
                'In Progress',
            ])
            ->count();

        $completedComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'Completed',
                'Closed',
            ])
            ->count();

        $pendingComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'Pending',
                'Verified',
            ])
            ->count();

        $recentComplaints = (clone $baseQuery)
            ->with([
                'category',
                'technicians',
            ])
            ->latest()
            ->limit(5)
            ->get();

        $announcements = ServiceAnnouncement::active()
            ->latest('published_at')
            ->take(5)
            ->get();

        return view(
            'consumer.dashboard',

            compact(
                'consumer',
                'activeComplaints',
                'completedComplaints',
                'pendingComplaints',
                'recentComplaints',
                'announcements'
            )
        );
    }
}
