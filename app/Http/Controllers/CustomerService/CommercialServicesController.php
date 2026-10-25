<?php

namespace App\Http\Controllers\CustomerService;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CommercialServicesController extends Controller
{
    private function filteredQuery(Request $request): Builder
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:150'],
            'status' => ['nullable', 'in:all,Verified,CS Processing,For Maintenance,Assigned,In Progress,Completed,Closed'],
            'date_from' => ['nullable', 'date_format:Y-m-d'],
            'date_to' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:date_from'],
        ]);

        $query = Complaint::query()
            ->with([
                'consumer',
                'division',
                'category',
                'verifier',
                'commercialResolution',
            ])
            ->whereNotNull('verified_at')
            ->whereNotIn('status', ['Pending', 'Rejected'])
            ->whereHas('division', function (Builder $division) {
                $division->whereRaw('LOWER(name) LIKE ?', ['%commercial%']);
            });

        if (!empty($validated['search'])) {
            $search = trim($validated['search']);
            $keyword = "%{$search}%";

            $query->where(function (Builder $q) use ($keyword) {
                // Complaint number and complaint details
                $q->where('complaint_no', 'like', $keyword)
                    ->orWhere('complainant_name', 'like', $keyword)
                    ->orWhere('description', 'like', $keyword)

                    // Consumer account, name, and address
                    ->orWhereHas('consumer', function (Builder $consumer) use ($keyword) {
                        $consumer->where('account_number', 'like', $keyword)
                            ->orWhere('first_name', 'like', $keyword)
                            ->orWhere('last_name', 'like', $keyword)
                            ->orWhere('address', 'like', $keyword);
                    })

                    // Complaint category
                    ->orWhereHas('category', function (Builder $category) use ($keyword) {
                        $category->where('name', 'like', $keyword);
                    });
            });
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        // Dates are based on the Customer Service verification decision.
        if (!empty($validated['date_from'])) {
            $query->whereDate('verified_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('verified_at', '<=', $validated['date_to']);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $query = $this->filteredQuery($request);

        $counts = (clone $query)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $complaints = $query
            ->orderByDesc('verified_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view(
            'customer-service.commercial-services.index',
            compact('complaints', 'counts')
        );
    }

    public function print(Request $request)
    {
        $complaints = $this->filteredQuery($request)
            ->orderByDesc('verified_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'customer-service.commercial-services.print',
            compact('complaints')
        );
    }
}
