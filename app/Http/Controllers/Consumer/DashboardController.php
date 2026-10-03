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

        /*
        |--------------------------------------------------------------------------
        | Base Complaint Query
        |--------------------------------------------------------------------------
        */

        $baseQuery = Complaint::query()
            ->where('consumer_id', $consumer->id);


        /*
        |--------------------------------------------------------------------------
        | Dashboard Statistics
        |--------------------------------------------------------------------------
        |
        | Pending:
        | Complaint is still in the verification / initial review stage.
        |
        | Active:
        | Complaint/request has moved beyond initial review and is currently
        | being processed or handled by maintenance.
        |
        | Completed:
        | Work/request has already been completed or closed.
        |
        */

        $pendingComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'Pending',
                'Verified',
            ])
            ->count();


        $activeComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'CS Processing',
                'For Maintenance',
                'Assigned',
                'In Progress',
                'Accomplished',
            ])
            ->count();


        $completedComplaints = (clone $baseQuery)
            ->whereIn('status', [
                'Completed',
                'Closed',
            ])
            ->count();


        /*
        |--------------------------------------------------------------------------
        | Recent Complaints
        |--------------------------------------------------------------------------
        */

        $recentComplaints = (clone $baseQuery)
            ->with([
                'division',
                'category',
            ])
            ->latest('created_at')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Service Announcements
        |--------------------------------------------------------------------------
        */

        $announcements = ServiceAnnouncement::published()
            ->with([
                'reads' => function ($query) use ($consumer) {

                    $query->where(
                        'consumer_id',
                        $consumer->id
                    );

                },
            ])
            ->latest('published_at')
            ->limit(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Unread Announcement Count
        |--------------------------------------------------------------------------
        */

        $unreadAnnouncementCount = ServiceAnnouncement::published()
            ->whereDoesntHave(
                'reads',
                function ($query) use ($consumer) {

                    $query->where(
                        'consumer_id',
                        $consumer->id
                    );

                }
            )
            ->count();


        return view(
            'consumer.dashboard',
            compact(
                'consumer',
                'activeComplaints',
                'pendingComplaints',
                'completedComplaints',
                'recentComplaints',
                'announcements',
                'unreadAnnouncementCount'
            )
        );
    }
}
