<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\MaintenanceReport;
use Illuminate\Http\JsonResponse;

class SidebarCountController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'pendingReviews' => MaintenanceReport::query()
                ->whereNotNull('submitted_at')
                ->where('review_status', 'Pending Review')
                ->count(),

            'readyForAssignment' => Complaint::query()
                ->where('complaints.status', 'Verified')
                ->whereDoesntHave('technicians')
                ->count(),
        ])->header('Cache-Control', 'no-store');
    }
}