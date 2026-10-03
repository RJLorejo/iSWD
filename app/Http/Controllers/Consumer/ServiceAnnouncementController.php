<?php

namespace App\Http\Controllers\Consumer;

use App\Http\Controllers\Controller;
use App\Models\AnnouncementRead;
use App\Models\ServiceAnnouncement;
use Illuminate\Http\Request;

class ServiceAnnouncementController extends Controller
{
    public function index()
    {
        $consumer = auth()->user()->consumer;

        abort_unless($consumer, 403);
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
            ->paginate(10);

        $unreadCount = ServiceAnnouncement::published()
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
            'consumer.announcements.index',
            compact(
                'announcements',
                'unreadCount'
            )
        );
    }

    public function show(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        abort_unless(
            $serviceAnnouncement->status === 'Published' &&
                $serviceAnnouncement->published_at &&
                $serviceAnnouncement->published_at->lte(now()),
            404
        );

        $consumer = auth()->user()->consumer;

        abort_unless($consumer, 403);

        AnnouncementRead::firstOrCreate(
            [
                'consumer_id' => $consumer->id,
                'service_announcement_id' => $serviceAnnouncement->id,
            ],
            [
                'read_at' => now(),
            ]
        );

        return view(
            'consumer.announcements.show',
            compact('serviceAnnouncement')
        );
    }

    public function markAllAsRead(Request $request)
    {
        $consumer = auth()->user()->consumer;

        abort_unless($consumer, 403);

        ServiceAnnouncement::published()
            ->select('id')
            ->chunkById(
                100,
                function ($announcements) use ($consumer) {
                    $now = now();

                    $rows = $announcements
                        ->map(function ($announcement) use (
                            $consumer,
                            $now
                        ) {
                            return [
                                'consumer_id' => $consumer->id,
                                'service_announcement_id' => $announcement->id,
                                'read_at' => $now,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                        })
                        ->all();

                    AnnouncementRead::upsert(
                        $rows,
                        [
                            'consumer_id',
                            'service_announcement_id',
                        ],
                        []
                    );
                }
            );

        return back()->with(
            'success',
            'All announcements have been marked as read.'
        );
    }
}
