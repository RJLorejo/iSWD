<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\JsonResponse;

class SidebarCountController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'activeComplaints' => Complaint::query()
                ->whereHas('technicians', fn ($q) => $q->where('users.id', auth()->id()))
                ->whereIn('status', ['Assigned', 'In Progress'])
                ->count(),
        ])->header('Cache-Control', 'no-store');
    }
}