<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Display searchable, filterable activity logs.
     */
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $query = $this->filteredQuery($filters);

        $activities = (clone $query)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Summary cards intentionally describe the entire log,
        // rather than only the records matching the current filters.
        $todayCount = AuditLog::query()
            ->whereDate('created_at', today())
            ->count();

        $activeUsersToday = AuditLog::query()
            ->whereDate('created_at', today())
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');

        $totalCount = AuditLog::query()->count();

        $securityEventsToday = AuditLog::query()
            ->whereDate('created_at', today())
            ->where(function (Builder $query) {
                $query->where('module', 'like', '%auth%')
                    ->orWhere('module', 'like', '%security%')
                    ->orWhere('action', 'like', '%login%')
                    ->orWhere('action', 'like', '%logout%')
                    ->orWhere('action', 'like', '%password%')
                    ->orWhere('action', 'like', '%access%');
            })
            ->count();

        $modules = AuditLog::query()
            ->whereNotNull('module')
            ->where('module', '<>', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = AuditLog::query()
            ->whereNotNull('action')
            ->where('action', '<>', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $roles = AuditLog::query()
            ->whereNotNull('user_role')
            ->where('user_role', '<>', '')
            ->distinct()
            ->orderBy('user_role')
            ->pluck('user_role');

        return view('admin.audit-logs.index', compact(
            'activities',
            'filters',
            'todayCount',
            'activeUsersToday',
            'totalCount',
            'securityEventsToday',
            'modules',
            'actions',
            'roles'
        ));
    }

    /**
     * Display one audit record.
     */
    public function show(AuditLog $auditLog)
    {
        return view('admin.audit-logs.show', [
            'activity' => $auditLog,
        ]);
    }

    /**
     * Generate a printable report using the selected filters.
     */
    public function print(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $activities = $this->filteredQuery($filters)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('admin.audit-logs.print', [
            'activities' => $activities,
            'filters' => $filters,
            'reportGeneratedAt' => now(),
            'reportPreparedBy' => $request->user(),
        ]);
    }

    /**
     * Export matching records to CSV.
     *
     * Uses a cursor to avoid loading every matching record
     * into application memory at once.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $filename = 'iswd-activity-logs-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(
            function () use ($filters) {
                $output = fopen('php://output', 'w');

                // Helps spreadsheet applications display UTF-8 correctly.
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'Log ID',
                    'Date and Time',
                    'User ID',
                    'Employee Name',
                    'Role',
                    'Module',
                    'Action',
                    'Description',
                    'Subject Type',
                    'Subject ID',
                ]);

                $this->filteredQuery($filters)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->cursor()
                    ->each(function (AuditLog $log) use ($output) {
                        fputcsv($output, [
                            $log->id,
                            $log->created_at?->format('Y-m-d H:i:s'),
                            $log->user_id,
                            $log->user_name,
                            $log->user_role,
                            $log->module,
                            $log->action,
                            $log->description,
                            $log->subject_type,
                            $log->subject_id,
                        ]);
                    });

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store, private',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    /**
     * Validate and normalize supported filters.
     */
    private function validatedFilters(Request $request): array
    {
        $validator = Validator::make($request->query(), [
            'search' => ['nullable', 'string', 'max:150'],
            'module' => ['nullable', 'string', 'max:100'],
            'action' => ['nullable', 'string', 'max:100'],
            'role' => ['nullable', 'string', 'max:100'],
            'from' => ['nullable', 'date_format:Y-m-d'],
            'to' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:from',
            ],
        ]);

        $filters = $validator->validate();

        foreach (['search', 'module', 'action', 'role', 'from', 'to'] as $key) {
            $filters[$key] = isset($filters[$key])
                && trim((string) $filters[$key]) !== ''
                    ? trim((string) $filters[$key])
                    : null;
        }

        return $filters;
    }

    /**
     * Build the shared query used by the list, print report and CSV export.
     */
    private function filteredQuery(array $filters): Builder
    {
        return AuditLog::query()
            ->when($filters['search'] ?? null, function (
                Builder $query,
                string $search
            ) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('user_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('subject_type', 'like', "%{$search}%")
                        ->orWhere('subject_id', 'like', "%{$search}%");
                });
            })
            ->when($filters['module'] ?? null, function (
                Builder $query,
                string $module
            ) {
                $query->where('module', $module);
            })
            ->when($filters['action'] ?? null, function (
                Builder $query,
                string $action
            ) {
                $query->where('action', $action);
            })
            ->when($filters['role'] ?? null, function (
                Builder $query,
                string $role
            ) {
                $query->where('user_role', $role);
            })
            ->when($filters['from'] ?? null, function (
                Builder $query,
                string $from
            ) {
                $query->where('created_at', '>=', $from . ' 00:00:00');
            })
            ->when($filters['to'] ?? null, function (
                Builder $query,
                string $to
            ) {
                // Inclusive end date, including the entire day.
                $query->where('created_at', '<',
                    \Illuminate\Support\Carbon::parse($to)->addDay()->startOfDay()
                );
            });
    }
}
