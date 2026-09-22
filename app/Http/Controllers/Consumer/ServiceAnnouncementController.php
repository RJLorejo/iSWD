<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\ServiceAnnouncement;

class ServiceAnnouncementController extends Controller
{
    /**
     * Display published service announcements.
     */
    public function index()
    {
        $announcements = ServiceAnnouncement::published()
            ->latest('published_at')
            ->paginate(10);

        return view(
            'consumer.announcements.index',
            compact('announcements')
        );
    }

    /**
     * Display one announcement.
     */
    public function show(ServiceAnnouncement $serviceAnnouncement)
    {
        abort_unless(
            $serviceAnnouncement->status === 'Published',
            404
        );

        return view(
            'consumer.announcements.show',
            compact('serviceAnnouncement')
        );
    }
}
