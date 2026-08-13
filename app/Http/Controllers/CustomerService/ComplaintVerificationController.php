<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintVerificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with([
            'consumer',
            'category',
            'customerService',
            'verifier',
            'technician',
        ]);

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
                    ->orWhere('address', 'like', "%{$search}%")

                    ->orWhereHas('consumer', function ($consumer) use ($search) {

                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('consumer_no', 'like', "%{$search}%");
                    })

                    ->orWhereHas('category', function ($category) use ($search) {

                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Verification Queue
        |--------------------------------------------------------------------------
        */

        $pendingComplaints = (clone $query)
            ->where('status', 'Pending')
            ->latest()
            ->paginate(10, ['*'], 'pending_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Already Verified
        |--------------------------------------------------------------------------
        */

        $verifiedComplaints = (clone $query)
            ->where('status', 'Verified')
            ->latest()
            ->paginate(10, ['*'], 'verified_page')
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Statistics
        |--------------------------------------------------------------------------
        */

        $pendingCount = Complaint::where(
            'status',
            'Pending'
        )->count();

        $verifiedCount = Complaint::where(
            'status',
            'Verified'
        )->count();

        $totalCount = Complaint::count();

        $rejectedCount = Complaint::where(
            'status',
            'Rejected'
        )->count();

        return view(
            'customer-service.complaint-verification.index',
            compact(
                'pendingComplaints',
                'verifiedComplaints',
                'pendingCount',
                'verifiedCount',
                'totalCount',
                'rejectedCount'
            )
        );
    }
}
