<?php

namespace App\Http\Controllers\Technician;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $technicianId = Auth::id();

        $scope = $request->get('scope', 'active');

        if (!in_array($scope, ['active', 'all'], true)) {
            $scope = 'active';
        }

        $query = $this->complaintsQuery($technicianId);

        if ($scope === 'active') {
            $query->whereIn('status', [
                'Assigned',
                'In Progress',
            ]);
        } else {
            $query->whereIn('status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function (Builder $query) use ($search) {
                $query
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('complainant_phone', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function (Builder $category) use ($search) {
                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ];

            if (in_array($request->status, $allowedStatuses, true)) {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('urgency')) {
            $urgency = strtoupper(trim($request->urgency));

            if (in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)) {
                $query->whereHas('aiAnalysis', function (Builder $aiQuery) use ($urgency) {
                    $aiQuery->whereRaw(
                        'UPPER(urgency_level) = ?',
                        [$urgency]
                    );
                });
            } elseif ($request->urgency === 'not_assessed') {
                $query->where(function (Builder $urgencyQuery) {
                    $urgencyQuery
                        ->whereDoesntHave('aiAnalysis')
                        ->orWhereHas('aiAnalysis', function (Builder $aiQuery) {
                            $aiQuery
                                ->whereNull('urgency_level')
                                ->orWhere('urgency_level', '');
                        });
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $complaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $assignedCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'Assigned')
            ->count();

        $inProgressCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->where('status', 'In Progress')
            ->count();

        $urgentCount = $this
            ->assignedComplaintsQuery($technicianId)
            ->whereIn('status', [
                'Assigned',
                'In Progress',
            ])
            ->whereHas('aiAnalysis', function (Builder $query) {
                $query->whereRaw(
                    'UPPER(urgency_level) = ?',
                    ['HIGH']
                );
            })
            ->count();

        $activeCount = $assignedCount + $inProgressCount;

        return view(
            'technician.complaints.index',
            compact(
                'complaints',
                'assignedCount',
                'inProgressCount',
                'urgentCount',
                'activeCount',
                'scope'
            )
        );
    }

    public function printReport(Request $request)
    {
        $technicianId = Auth::id();

        $scope = $request->get('scope', 'active');

        if (!in_array($scope, ['active', 'all'], true)) {
            $scope = 'active';
        }

        $query = $this->complaintsQuery($technicianId);

        if ($scope === 'active') {
            $query->whereIn('status', [
                'Assigned',
                'In Progress',
            ]);
        } else {
            $query->whereIn('status', [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ]);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function (Builder $query) use ($search) {
                $query
                    ->where('complaint_no', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%")
                    ->orWhere('complainant_name', 'like', "%{$search}%")
                    ->orWhere('complainant_phone', 'like', "%{$search}%")
                    ->orWhereHas('consumer', function (Builder $consumer) use ($search) {
                        $consumer
                            ->where('first_name', 'like', "%{$search}%")
                            ->orWhere('middle_name', 'like', "%{$search}%")
                            ->orWhere('last_name', 'like', "%{$search}%")
                            ->orWhere('account_number', 'like', "%{$search}%")
                            ->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('category', function (Builder $category) use ($search) {
                        $category
                            ->where('name', 'like', "%{$search}%")
                            ->orWhere('code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $allowedStatuses = [
                'Assigned',
                'In Progress',
                'Completed',
                'Closed',
            ];

            if (in_array($request->status, $allowedStatuses, true)) {
                $query->where('status', $request->status);
            }
        }

        if ($request->filled('urgency')) {
            $urgency = strtoupper(trim($request->urgency));

            if (in_array($urgency, ['HIGH', 'MODERATE', 'LOW'], true)) {
                $query->whereHas('aiAnalysis', function (Builder $aiQuery) use ($urgency) {
                    $aiQuery->whereRaw(
                        'UPPER(urgency_level) = ?',
                        [$urgency]
                    );
                });
            } elseif ($request->urgency === 'not_assessed') {
                $query->where(function (Builder $urgencyQuery) {
                    $urgencyQuery
                        ->whereDoesntHave('aiAnalysis')
                        ->orWhereHas('aiAnalysis', function (Builder $aiQuery) {
                            $aiQuery
                                ->whereNull('urgency_level')
                                ->orWhere('urgency_level', '');
                        });
                });
            }
        }

        if ($request->filled('date_from')) {
            $query->whereDate(
                'created_at',
                '>=',
                $request->date_from
            );
        }

        if ($request->filled('date_to')) {
            $query->whereDate(
                'created_at',
                '<=',
                $request->date_to
            );
        }

        $complaints = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view(
            'technician.complaints.print-report',
            compact(
                'complaints',
                'scope'
            )
        );
    }

    public function show(Complaint $complaint)
    {
        $isAssignedToMe = $complaint
            ->technicians()
            ->where('users.id', Auth::id())
            ->exists();

        abort_unless($isAssignedToMe, 403);

        $complaint->load([
            'consumer.address',
            'division',
            'category',
            'photos',
            'customerService',
            'verifier',
            'technicians',
            'maintenanceReport',
            'aiAnalysis',
            'commercialResolution.processor',
            'commercialResolution.forwarder',
        ]);

        return view(
            'technician.complaints.show',
            compact('complaint')
        );
    }

    private function complaintsQuery(int $technicianId): Builder
    {
        return Complaint::query()
            ->with([
                'consumer.address',
                'division',
                'category',
                'customerService',
                'verifier',
                'technicians',
                'maintenanceReport',
                'aiAnalysis',
                'commercialResolution.processor',
                'commercialResolution.forwarder',
            ])
            ->withCount('technicians')
            ->whereHas('technicians', function (Builder $query) use ($technicianId) {
                $query->where(
                    'users.id',
                    $technicianId
                );
            });
    }

    private function assignedComplaintsQuery(
        int $technicianId
    ): Builder {
        return Complaint::query()
            ->whereHas('technicians', function (Builder $query) use ($technicianId) {
                $query->where(
                    'users.id',
                    $technicianId
                );
            });
    }
}
