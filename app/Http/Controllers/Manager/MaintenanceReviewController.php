<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\MaintenanceReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MaintenanceReviewController extends Controller
{
    /**
     * Maintenance reports awaiting review.
     */
    public function index(Request $request)
    {
        $query = MaintenanceReport::with([
            'complaint.consumer',
            'complaint.category',
            'technician',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Review Status
        |--------------------------------------------------------------------------
        */

        $status = $request->get(
            'status',
            'Pending Review'
        );

        if ($status !== 'All') {
            $query->where(
                'review_status',
                $status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->whereHas(
                'complaint',
                function ($q) use ($search) {

                    $q->where(
                        'complaint_no',
                        'like',
                        "%{$search}%"
                    )
                        ->orWhere(
                            'subject',
                            'like',
                            "%{$search}%"
                        );
                }
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('from')) {

            $query->whereDate(
                'submitted_at',
                '>=',
                $request->from
            );
        }

        if ($request->filled('to')) {

            $query->whereDate(
                'submitted_at',
                '<=',
                $request->to
            );
        }

        $reports = $query
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $pendingCount = MaintenanceReport::where(
            'review_status',
            'Pending Review'
        )->count();

        $returnedCount = MaintenanceReport::where(
            'review_status',
            'Returned'
        )->count();

        $approvedCount = MaintenanceReport::where(
            'review_status',
            'Approved'
        )->count();

        return view(
            'maintenance-manager.maintenance-reviews.index',
            compact(
                'reports',
                'pendingCount',
                'returnedCount',
                'approvedCount'
            )
        );
    }

    /**
     * Show maintenance report for review.
     */
    public function show(MaintenanceReport $maintenanceReport)
    {
        $maintenanceReport->load([
            'complaint.consumer',
            'complaint.category',
            'complaint.technician',
            'technician',
            'reviewer',
        ]);

        return view(
            'maintenance-manager.maintenance-reviews.show',
            compact('maintenanceReport')
        );
    }

    /**
     * Approve report.
     */
    public function approve(
        Request $request,
        MaintenanceReport $maintenanceReport
    ) {
        $request->validate([
            'review_remarks' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);

        if ($maintenanceReport->review_status === 'Approved') {

            return back()->with(
                'error',
                'This maintenance report has already been approved.'
            );
        }

        DB::transaction(function () use (
            $maintenanceReport,
            $request
        ) {

            $maintenanceReport->update([

                'review_status' => 'Approved',

                'reviewed_by' => Auth::id(),

                'reviewed_at' => now(),

                'review_remarks' =>
                $request->review_remarks,
            ]);
        });

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.show',
                $maintenanceReport
            )
            ->with(
                'success',
                'Maintenance report approved successfully.'
            );
    }

    /**
     * Return report for correction.
     */
    public function returnForCorrection(
        Request $request,
        MaintenanceReport $maintenanceReport
    ) {
        $validated = $request->validate([
            'review_remarks' => [
                'required',
                'string',
                'max:10000',
            ],
        ], [
            'review_remarks.required' =>
            'Please provide the reason or corrections required.',
        ]);

        if ($maintenanceReport->review_status === 'Approved') {

            return back()->with(
                'error',
                'An approved maintenance report cannot be returned.'
            );
        }

        DB::transaction(function () use (
            $maintenanceReport,
            $validated
        ) {

            $maintenanceReport->update([

                'review_status' => 'Returned',

                'reviewed_by' => Auth::id(),

                'reviewed_at' => now(),

                'review_remarks' =>
                $validated['review_remarks'],
            ]);
        });

        return redirect()
            ->route(
                'maintenance-manager.maintenance-reviews.index'
            )
            ->with(
                'success',
                'Maintenance report returned to the technician for correction.'
            );
    }
}
