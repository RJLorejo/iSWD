<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MaintenanceHistoryController extends Controller
{
    /**
     * Display technician maintenance history.
     */
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        /*
        |--------------------------------------------------------------------------
        | Maintenance History Query
        |--------------------------------------------------------------------------
        */

        $query = Complaint::query()
            ->with([
                'consumer',
                'category',
                'maintenanceReport',
                'technicians',
            ])
            ->whereHas('technicians', function ($q) use ($technicianId) {

                $q->where('users.id', $technicianId);

            })
            ->where('complaints.status', 'Completed');

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('subject', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from')) {

            $query->whereDate(
                'completed_at',
                '>=',
                $request->from
            );
        }

        if ($request->filled('to')) {

            $query->whereDate(
                'completed_at',
                '<=',
                $request->to
            );
        }

        /*
        |--------------------------------------------------------------------------
        | History
        |--------------------------------------------------------------------------
        */

        $histories = $query
            ->latest('completed_at')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        | technicians is a relationship, not a complaints table column.
        | Therefore we must use whereHas('technicians').
        |
        */

        $baseQuery = Complaint::query()
            ->whereHas('technicians', function ($q) use ($technicianId) {

                $q->where('users.id', $technicianId);

            })
            ->where('complaints.status', 'Completed');

        /*
        |--------------------------------------------------------------------------
        | Total Completed
        |--------------------------------------------------------------------------
        */

        $totalCompleted = (clone $baseQuery)
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Completed With Maintenance Report
        |--------------------------------------------------------------------------
        */

        $withReports = (clone $baseQuery)
            ->whereHas('maintenanceReport')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Completed Without Maintenance Report
        |--------------------------------------------------------------------------
        */

        $withoutReports =
            $totalCompleted - $withReports;

        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        return view(
            'technician.maintenance-history.index',
            compact(
                'histories',
                'totalCompleted',
                'withReports',
                'withoutReports'
            )
        );
    }
}
