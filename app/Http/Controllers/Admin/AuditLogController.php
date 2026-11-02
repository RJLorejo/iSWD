<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Complaint;
use App\Models\MaintenanceWorkOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditLogController extends Controller
{
    /**
     * Only internal office employees are included.
     */
    private const EMPLOYEE_ROLES = [
        'Administrator',
        'Maintenance Manager',
        'Customer Service',
        'Maintenance Technician',
    ];

    /**
     * Fields whose values refer to employee/user records.
     */
    private const USER_ID_FIELDS = [
        'assigned_to',
        'customer_service_id',
        'verified_by',
        'technician_id',
        'technician_ids',
        'replaced_technician_id',
        'replacement_technician_id',
        'replacement_technician_ids',
        'scheduled_by',
        'pending_recorded_by',
        'approved_by',
        'missed_reported_by',
        'missed_confirmed_by',
        'user_id',
    ];

    /**
     * Display searchable, filterable employee activity logs.
     */
    public function index(Request $request)
    {
        $filters = $this->validatedFilters($request);

        $employeeLogs = $this->employeeQuery();

        $activities = $this->filteredQuery($filters)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        // Summary cards always describe all employee logs.
        $todayCount = (clone $employeeLogs)
            ->whereDate('created_at', today())
            ->count();

        $activeUsersToday = (clone $employeeLogs)
            ->whereDate('created_at', today())
            ->whereNotNull('user_id')
            ->distinct()
            ->count('user_id');

        $totalCount = (clone $employeeLogs)->count();

        $securityEventsToday = (clone $employeeLogs)
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

        $modules = $this->employeeQuery()
            ->whereNotNull('module')
            ->where('module', '<>', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        $actions = $this->employeeQuery()
            ->whereNotNull('action')
            ->where('action', '<>', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $roles = collect(self::EMPLOYEE_ROLES);

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
     * Display an individual employee activity record.
     */
    public function show(AuditLog $auditLog)
    {
        $activity = $this->employeeQuery()
            ->whereKey($auditLog->getKey())
            ->firstOrFail();

        /*
         * Resolve the affected record.
         */
        $subject = null;
        $subjectName = null;

        $subjectModels = [
            'Complaint' => Complaint::class,
            'MaintenanceWorkOrder' => MaintenanceWorkOrder::class,
            'User' => User::class,
        ];

        $modelClass = $subjectModels[$activity->subject_type] ?? null;

        if ($modelClass && $activity->subject_id) {
            $query = $modelClass::query();

            if (in_array(
                SoftDeletes::class,
                class_uses_recursive($modelClass),
                true
            )) {
                $query->withTrashed();
            }

            $subject = $query->find($activity->subject_id);

            if ($subject) {
                $subjectName = $this->resolveSubjectName($subject);
            }
        }

        /*
         * Convert old and new values into readable display values.
         * This prevents raw employee IDs and ID arrays from appearing
         * in the Blade view.
         */
        $displayValues = [
            'old_values' => $this->prepareDisplayValues(
                $activity->old_values
            ),
            'new_values' => $this->prepareDisplayValues(
                $activity->new_values
            ),
        ];

        return view('admin.audit-logs.show', compact(
            'activity',
            'subject',
            'subjectName',
            'displayValues'
        ));
    }

    /**
     * Resolve a readable reference for the audited record.
     */
    private function resolveSubjectName(Model $subject): string
    {
        if ($subject instanceof Complaint) {
            return $subject->complaint_no
                ?: 'Complaint #' . $subject->getKey();
        }

        if ($subject instanceof MaintenanceWorkOrder) {
            $complaint = $subject->complaint()
                ->withTrashed()
                ->first();

            $complaintNo = $complaint?->complaint_no;

            return $complaintNo
                ? $complaintNo . ' — Visit ' . $subject->visit_number
                : 'Work Order #' . $subject->getKey();
        }

        if ($subject instanceof User) {
            return $subject->full_name
                ?: $subject->employee_id
                ?: 'Employee #' . $subject->getKey();
        }

        return class_basename($subject) . ' #' . $subject->getKey();
    }

    /**
     * Prepare an entire old/new values array for display.
     */
    private function prepareDisplayValues(?array $values): array
    {
        if (!$values) {
            return [];
        }

        $hiddenFields = [
            'id',
            'user_id',
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'api_token',
            'token',
            'secret',
            'otp',
            'verification_code',
            'code_hash',
        ];

        $displayValues = [];

        foreach ($values as $key => $value) {
            $field = strtolower((string) $key);

            // Never expose passwords, authentication tokens or secrets.
            if (in_array($field, $hiddenFields, true)) {
                continue;
            }

            $displayValues[$key] = $this->resolveAuditValue(
                $field,
                $value
            );
        }

        return $displayValues;
    }

    /**
     * Convert stored values into readable employee-facing values.
     */
    private function resolveAuditValue(string $key, $value): string
    {
        if ($value === null || $value === '') {
            return 'Not specified';
        }

        /*
         * Employee ID fields may contain either:
         *
         * 21
         *
         * or:
         *
         * [21, 22, 23]
         *
         * Handle arrays first so technician_ids is converted correctly.
         */
        if (in_array($key, self::USER_ID_FIELDS, true)) {
            $ids = is_array($value) ? $value : [$value];

            $employees = collect($ids)
                ->filter(fn ($id) => is_numeric($id))
                ->map(function ($id) {
                    return $this->resolveEmployeeName((int) $id);
                })
                ->filter()
                ->values();

            return $employees->isNotEmpty()
                ? $employees->implode(', ')
                : 'No employee recorded';
        }

        /*
         * Complaint references.
         */
        if ($key === 'complaint_id' && is_numeric($value)) {
            $id = (int) $value;

            $complaint = Complaint::withTrashed()->find($id);

            return $complaint?->complaint_no
                ?: 'Complaint #' . $id;
        }

        /*
         * Maintenance work order references.
         */
        if (in_array($key, [
            'rescheduled_from_work_order_id',
            'maintenance_work_order_id',
        ], true) && is_numeric($value)) {
            $id = (int) $value;

            $workOrder = MaintenanceWorkOrder::find($id);

            return $workOrder
                ? $this->resolveSubjectName($workOrder)
                : 'Work Order #' . $id;
        }

        /*
         * Other arrays are displayed as readable comma-separated values.
         */
        if (is_array($value)) {
            return collect($value)
                ->map(fn ($item) => is_scalar($item)
                    ? (string) $item
                    : 'Recorded value')
                ->implode(', ');
        }

        /*
         * Boolean values.
         */
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        /*
         * Unknown foreign-key fields.
         * Keep the ID visible, but label it as a reference.
         */
        if (str_ends_with($key, '_id') && is_numeric($value)) {
            return 'Reference #' . $value;
        }

        return is_scalar($value)
            ? (string) $value
            : 'Recorded value';
    }

    /**
     * Resolve an employee ID to a readable employee name.
     */
    private function resolveEmployeeName(int $id): string
    {
        $employee = User::find($id);

        if (!$employee) {
            return 'Employee #' . $id;
        }

        return $employee->full_name
            ?: $employee->employee_id
            ?: 'Employee #' . $id;
    }

    /**
     * Generate a printable employee activity report.
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
     * Export matching employee activity records to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $filters = $this->validatedFilters($request);

        $filename = 'iswd-employee-activity-' . now()->format('Y-m-d-His') . '.csv';

        return response()->streamDownload(
            function () use ($filters) {
                $output = fopen('php://output', 'w');

                // UTF-8 BOM for spreadsheet compatibility.
                fwrite($output, "\xEF\xBB\xBF");

                fputcsv($output, [
                    'Date and Time',
                    'Employee',
                    'Role',
                    'Module',
                    'Activity',
                    'Description',
                    'IP Address',
                ]);

                $this->filteredQuery($filters)
                    ->orderByDesc('created_at')
                    ->orderByDesc('id')
                    ->cursor()
                    ->each(function (AuditLog $log) use ($output) {
                        fputcsv($output, [
                            $log->created_at?->format('Y-m-d H:i:s'),
                            $log->user_name ?: 'Unknown employee',
                            $log->user_role ?: 'Unknown role',
                            $log->module ?: 'General',
                            $log->action ?: 'Activity recorded',
                            $log->description,
                            $log->ip_address,
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
     * Base query for internal employee activity only.
     */
    private function employeeQuery(): Builder
    {
        return AuditLog::query()
            ->whereNotNull('user_id')
            ->whereIn('user_role', self::EMPLOYEE_ROLES);
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
            'role' => ['nullable', 'in:' . implode(',', self::EMPLOYEE_ROLES)],
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
                ? trim($filters[$key])
                : null;
        }

        return $filters;
    }

    /**
     * Shared employee-only query for listing, print and CSV export.
     */
    private function filteredQuery(array $filters): Builder
    {
        return $this->employeeQuery()
            ->when($filters['search'] ?? null, function (
                Builder $query,
                string $search
            ) {
                $query->where(function (Builder $nested) use ($search) {
                    $nested->where('user_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%");
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
                // Include the entire selected end date.
                $query->where(
                    'created_at',
                    '<',
                    Carbon::parse($to)->addDay()->startOfDay()
                );
            });
    }
}
