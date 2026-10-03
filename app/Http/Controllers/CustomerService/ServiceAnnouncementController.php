<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\ServiceAnnouncement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ServiceAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $query = ServiceAnnouncement::query()
            ->with('publisher');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('affected_barangay', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $announcements = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $types = ServiceAnnouncement::query()
            ->select('type')
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->distinct()
            ->orderBy('type')
            ->pluck('type');

        $totalAnnouncements = ServiceAnnouncement::count();

        $publishedAnnouncements = ServiceAnnouncement::where(
            'status',
            'Published'
        )->count();

        $draftAnnouncements = ServiceAnnouncement::where(
            'status',
            'Draft'
        )->count();

        $archivedAnnouncements = ServiceAnnouncement::where(
            'status',
            'Archived'
        )->count();

        $activeAnnouncements = ServiceAnnouncement::active()->count();

        return view(
            'customer-service.announcements.index',
            compact(
                'announcements',
                'types',
                'totalAnnouncements',
                'publishedAnnouncements',
                'draftAnnouncements',
                'archivedAnnouncements',
                'activeAnnouncements'
            )
        );
    }

    public function create()
    {
        return view(
            'customer-service.announcements.create'
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateAnnouncement($request);

        $validated['status'] = 'Draft';
        $validated['published_by'] = null;
        $validated['published_at'] = null;

        $announcement = ServiceAnnouncement::create(
            $validated
        );

        return redirect()
            ->route(
                'customer-service.announcements.show',
                $announcement
            )
            ->with(
                'success',
                'Service announcement created successfully.'
            );
    }

    public function show(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        $serviceAnnouncement->load('publisher');

        return view(
            'customer-service.announcements.show',
            compact('serviceAnnouncement')
        );
    }

    public function edit(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        return view(
            'customer-service.announcements.edit',
            compact('serviceAnnouncement')
        );
    }

    public function update(
        Request $request,
        ServiceAnnouncement $serviceAnnouncement
    ) {
        $validated = $this->validateAnnouncement(
            $request
        );

        $serviceAnnouncement->update($validated);

        return redirect()
            ->route(
                'customer-service.announcements.show',
                $serviceAnnouncement
            )
            ->with(
                'success',
                'Service announcement updated successfully.'
            );
    }

    public function publish(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        DB::transaction(
            function () use ($serviceAnnouncement) {
                $serviceAnnouncement->update([
                    'status' => 'Published',
                    'published_by' => auth()->id(),
                    'published_at' => now(),
                ]);
            }
        );

        return back()->with(
            'success',
            'Service announcement published successfully.'
        );
    }

    public function archive(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        $serviceAnnouncement->update([
            'status' => 'Archived',
        ]);

        return back()->with(
            'success',
            'Service announcement archived successfully.'
        );
    }

    public function moveToDraft(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        $serviceAnnouncement->update([
            'status' => 'Draft',
        ]);

        return back()->with(
            'success',
            'Service announcement moved back to draft.'
        );
    }

    public function destroy(
        ServiceAnnouncement $serviceAnnouncement
    ) {
        $serviceAnnouncement->delete();

        return redirect()
            ->route(
                'customer-service.announcements.index'
            )
            ->with(
                'success',
                'Service announcement deleted successfully.'
            );
    }

    private function validateAnnouncement(
        Request $request
    ): array {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'type' => [
                'required',
                'string',
                'max:100',
            ],

            'content' => [
                'required',
                'string',
            ],

            'affected_barangay' => [
                'nullable',
                'string',
                'max:255',
            ],

            'start_at' => [
                'nullable',
                'date',
            ],

            'end_at' => [
                'nullable',
                'date',
                'after_or_equal:start_at',
            ],
        ]);
    }
}
