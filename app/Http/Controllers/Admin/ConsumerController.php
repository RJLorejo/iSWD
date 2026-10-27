<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Consumer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

class ConsumerController extends Controller
{
    /**
     * Build the shared consumer query.
     * Used by the index and print report so their filters stay consistent.
     */
    private function consumerQuery(Request $request): Builder
    {
        $query = Consumer::query()
            ->with(['address', 'user']);

        $search = trim((string) $request->input('search', ''));

        if ($search !== '') {
            $query->where(function (Builder $q) use ($search) {
                $q->where('account_number', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('middle_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('user', function (Builder $userQuery) use ($search) {
                        $userQuery->where('email', 'like', "%{$search}%");
                    })
                    ->orWhereHas('address', function (Builder $addressQuery) use ($search) {
                        $addressQuery->where(function (Builder $addressFields) use ($search) {
                            $addressFields
                                ->where('house_no', 'like', "%{$search}%")
                                ->orWhere('street', 'like', "%{$search}%")
                                ->orWhere('purok', 'like', "%{$search}%")
                                ->orWhere('barangay', 'like', "%{$search}%")
                                ->orWhere('municipality', 'like', "%{$search}%")
                                ->orWhere('province', 'like', "%{$search}%");
                        });
                    });
            });
        }

        $status = (string) $request->input('status', '');

        if ($status === '1' || $status === '0') {
            $query->where('is_active', (int) $status);
        }

        return $query->orderByDesc('created_at');
    }

    /**
     * Display the read-only consumer directory.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:,all,1,0'],
        ]);

        $consumers = $this->consumerQuery($request)
            ->paginate(10)
            ->withQueryString();

        $totalConsumers = Consumer::count();

        $activeConsumers = Consumer::where('is_active', true)->count();

        $inactiveConsumers = Consumer::where('is_active', false)->count();

        $registeredToday = Consumer::whereDate(
            'created_at',
            now()->toDateString()
        )->count();

        return view('admin.consumers.index', compact(
            'consumers',
            'totalConsumers',
            'activeConsumers',
            'inactiveConsumers',
            'registeredToday'
        ));
    }

    /**
     * Display consumer details without changing the consumer record.
     */
    public function show(Consumer $consumer)
    {
        $consumer->load(['address', 'user']);

        return view('admin.consumers.show', compact('consumer'));
    }

    /**
     * Print all matching consumers using the current filters.
     */
    public function printReport(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:,all,1,0'],
        ]);

        $consumers = $this->consumerQuery($request)->get();

        return view('admin.consumers.print-report', compact('consumers'));
    }
}
