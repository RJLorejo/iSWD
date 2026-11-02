@extends('admin.layouts.app')

@section('title', 'Employee Activity Details')

@section('content')
    @php
        use App\Models\Complaint;
        use App\Models\MaintenanceWorkOrder;
        use App\Models\User;
        use Illuminate\Support\Str;

        $actionText = strtolower($activity->action ?? '');
        $actionStyle = 'bg-slate-100 text-slate-700';
        $actionIcon = 'fas fa-history';

        if (
            str_contains($actionText, 'delete') ||
            str_contains($actionText, 'reject') ||
            str_contains($actionText, 'deactivat')
        ) {
            $actionStyle = 'bg-red-50 text-red-700';
            $actionIcon = 'fas fa-exclamation-triangle';
        } elseif (
            str_contains($actionText, 'create') ||
            str_contains($actionText, 'assign') ||
            str_contains($actionText, 'complete') ||
            str_contains($actionText, 'approve')
        ) {
            $actionStyle = 'bg-emerald-50 text-emerald-700';
            $actionIcon = 'fas fa-check-circle';
        } elseif (
            str_contains($actionText, 'update') ||
            str_contains($actionText, 'edit') ||
            str_contains($actionText, 'reschedule')
        ) {
            $actionStyle = 'bg-amber-50 text-amber-700';
            $actionIcon = 'fas fa-edit';
        }

        /*
    |--------------------------------------------------------------------------
    | Historical foreign-key value resolver
    |--------------------------------------------------------------------------
    | This resolves known IDs to readable names.
    | Unknown ID fields remain labeled as references rather than being
    | incorrectly interpreted as employee IDs.
    */
        $resolveAuditValue = function ($key, $value) {
            if ($value === null || $value === '') {
                return 'Not specified';
            }

            if (!is_scalar($value)) {
                return json_encode($value, JSON_UNESCAPED_UNICODE);
            }

            $key = strtolower((string) $key);

            if (!is_numeric($value) || !str_ends_with($key, '_id')) {
                return (string) $value;
            }

            $id = (int) $value;

            $userFields = [
                'assigned_to',
                'customer_service_id',
                'verified_by',
                'technician_id',
                'scheduled_by',
                'pending_recorded_by',
                'approved_by',
                'missed_reported_by',
                'missed_confirmed_by',
                'user_id',
            ];

            if (in_array($key, $userFields, true)) {
                $user = User::find($id);

                return $user
                    ? ($user->full_name ?:
                        $user->employee_id ?:
                        'Employee #' . $id)
                    : 'Employee no longer available';
            }

            if ($key === 'complaint_id') {
                $complaint = Complaint::withTrashed()->find($id);

                return $complaint?->complaint_no ?: 'Complaint #' . $id;
            }

            if ($key === 'rescheduled_from_work_order_id') {
                $workOrder = MaintenanceWorkOrder::find($id);

                if (!$workOrder) {
                    return 'Work Order #' . $id;
                }

                $complaintNo = $workOrder->complaint?->complaint_no;

                return $complaintNo ? $complaintNo . ' — Visit ' . $workOrder->visit_number : 'Work Order #' . $id;
            }

            if ($key === 'maintenance_work_order_id') {
                $workOrder = MaintenanceWorkOrder::find($id);

                if (!$workOrder) {
                    return 'Work Order #' . $id;
                }

                $complaintNo = $workOrder->complaint?->complaint_no;

                return $complaintNo ? $complaintNo . ' — Visit ' . $workOrder->visit_number : 'Work Order #' . $id;
            }

            // Do not guess the meaning of other foreign keys.
            return 'Reference #' . $id;
        };

        $hiddenFields = [
            'id',
            'user_id',
            'password',
            'password_confirmation',
            'current_password',
            'remember_token',
            'token',
            'api_token',
            'secret',
            'otp',
            'code_hash',
            'verification_code',
        ];
    @endphp

    <div class="min-h-screen space-y-5 sm:space-y-6">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <div class="mb-2 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 sm:text-sm">
                    <a href="{{ route('admin.audit-logs.index') }}" class="hover:text-blue-600">
                        Employee Activity Logs
                    </a>
                    <i class="fas fa-chevron-right text-[10px]"></i>
                    <span class="text-slate-700">Activity Details</span>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Activity Details
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    Review the employee action and its recorded changes.
                </p>
            </div>

            <a href="{{ route('admin.audit-logs.index', request()->query()) }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:w-auto">
                <i class="fas fa-arrow-left"></i>
                Back to Activity Logs
            </a>
        </div>

        {{-- READ ONLY NOTICE --}}
        <div class="flex items-start gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800">
            <i class="fas fa-shield-alt mt-0.5 text-lg"></i>
            <div>
                <p class="font-semibold">Historical audit record</p>
                <p class="mt-1 leading-relaxed text-blue-700">
                    This activity record is read-only. It preserves the information captured when the employee performed the
                    action.
                    Changes to the underlying business record do not rewrite this historical entry.
                </p>
            </div>
        </div>

        {{-- ACTIVITY OVERVIEW --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-3 border-b border-slate-100 bg-slate-50/70 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <div>
                    <h2 class="font-semibold text-slate-900">Activity Overview</h2>
                    <p class="mt-1 text-xs text-slate-500">Employee action summary</p>
                </div>

                <span
                    class="inline-flex w-fit items-center gap-2 rounded-full px-3 py-1.5 text-xs font-semibold {{ $actionStyle }}">
                    <i class="{{ $actionIcon }}"></i>
                    {{ $activity->action ?: 'Activity recorded' }}
                </span>
            </div>

            <div class="grid grid-cols-1 gap-5 p-4 sm:grid-cols-2 sm:gap-6 sm:p-5">

                {{-- EMPLOYEE --}}
                <div class="flex min-w-0 items-center gap-4">
                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-xl font-bold text-blue-700">
                        {{ strtoupper(substr($activity->user_name ?: 'U', 0, 1)) }}
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Employee
                        </p>

                        <p class="mt-1 break-words text-base font-bold text-slate-900 sm:text-lg">
                            {{ $activity->user_name ?: 'Unknown employee' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $activity->user_role ?: 'Unknown role' }}
                        </p>
                    </div>
                </div>

                {{-- DATE --}}
                <div class="flex items-center gap-4">
                    <div
                        class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fas fa-calendar-alt text-xl"></i>
                    </div>

                    <div class="min-w-0">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            Date and Time
                        </p>

                        <p class="mt-1 break-words font-semibold text-slate-900">
                            {{ $activity->created_at?->format('F d, Y') ?? '—' }}
                        </p>

                        <p class="mt-1 text-sm text-slate-500">
                            {{ $activity->created_at?->format('h:i:s A') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- MODULE --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-layer-group mr-1.5"></i>
                        Module
                    </p>

                    <p class="break-words text-sm font-semibold text-slate-800">
                        {{ $activity->module ?: 'General' }}
                    </p>
                </div>

                {{-- ACTIVITY --}}
                <div class="rounded-xl border border-slate-100 bg-slate-50 p-4">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        <i class="fas fa-bolt mr-1.5"></i>
                        Activity
                    </p>

                    <p class="break-words text-sm font-semibold text-slate-800">
                        {{ $activity->action ?: 'Activity recorded' }}
                    </p>
                </div>

                {{-- AFFECTED RECORD --}}
                <div class="rounded-xl border border-blue-100 bg-blue-50/60 p-4 sm:col-span-2">
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-blue-700">
                        <i class="fas fa-link mr-1.5"></i>
                        Affected Record
                    </p>

                    @if ($activity->subject_type)
                        <p class="break-words text-sm font-bold text-slate-900">
                            {{ $subjectName ?: class_basename($activity->subject_type) . ' record unavailable' }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ class_basename($activity->subject_type) }}
                        </p>
                    @else
                        <p class="text-sm text-slate-500">
                            No related record was attached to this activity.
                        </p>
                    @endif
                </div>

                {{-- DESCRIPTION --}}
                <div class="sm:col-span-2">
                    <div class="rounded-xl border border-slate-100 bg-slate-50 p-4 sm:p-5">
                        <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-align-left mr-1.5"></i>
                            Activity Description
                        </p>

                        <p class="break-words text-sm leading-6 text-slate-700">
                            {{ $activity->description ?: 'No additional description was recorded.' }}
                        </p>
                    </div>
                </div>

            </div>
        </div>

        {{-- PREVIOUS AND NEW VALUES --}}
        @foreach ([
            'old_values' => [
                'title' => 'Previous Values',
                'description' => 'Information before the employee performed this action.',
                'icon' => 'fas fa-history',
                'style' => 'bg-amber-50 text-amber-700',
            ],
            'new_values' => [
                'title' => 'New Values',
                'description' => 'Information recorded after the employee performed this action.',
                'icon' => 'fas fa-check-circle',
                'style' => 'bg-emerald-50 text-emerald-700',
            ],
        ] as $field => $config)
            @php
                $values = $activity->{$field};

                $visibleValues = is_array($values)
                    ? collect($values)->reject(function ($value, $key) use ($hiddenFields) {
                        $key = strtolower((string) $key);

                        foreach ($hiddenFields as $hiddenField) {
                            if (
                                $key === $hiddenField ||
                                str_contains($key, 'password') ||
                                str_contains($key, 'token') ||
                                str_contains($key, 'secret') ||
                                str_contains($key, 'otp')
                            ) {
                                return true;
                            }
                        }

                        return false;
                    })
                    : collect();
            @endphp

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-4 sm:px-5">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $config['style'] }}">
                        <i class="{{ $config['icon'] }} text-lg"></i>
                    </div>

                    <div class="min-w-0">
                        <h2 class="font-semibold text-slate-900">
                            {{ $config['title'] }}
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $config['description'] }}
                        </p>
                    </div>
                </div>

                <div class="p-4 sm:p-5">
                    @if ($visibleValues->isNotEmpty())
                        <div class="space-y-3">

                            @foreach ($displayValues['new_values'] as $key => $value)
                                <div
                                    class="grid grid-cols-1 gap-2 rounded-xl border border-slate-100 p-3 sm:grid-cols-3 sm:gap-4 sm:p-4">

                                    <div class="text-xs font-semibold text-slate-600 sm:text-sm">
                                        {{ Str::headline((string) $key) }}
                                    </div>

                                    <div class="break-words text-sm leading-6 text-slate-700 sm:col-span-2">

                                        @if (is_array($value))
                                            <pre class="overflow-x-auto whitespace-pre-wrap break-words rounded-lg bg-slate-50 p-3 text-xs">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                        @elseif (is_bool($value))
                                            {{ $value ? 'Yes' : 'No' }}
                                        @elseif ($value === null || $value === '')
                                            <span class="text-slate-400">Not specified</span>
                                        @else
                                            {{ $resolveAuditValue($key, $value) }}
                                        @endif

                                    </div>
                                </div>
                            @endforeach

                        </div>
                    @else
                        <div class="py-8 text-center">
                            <i class="fas fa-info-circle text-2xl text-slate-300"></i>

                            <p class="mt-2 text-sm text-slate-500">
                                No additional values were recorded for this activity.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        {{-- TECHNICAL REQUEST INFORMATION --}}
        <details class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-4 sm:px-5">

                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">
                        <i class="fas fa-info-circle text-lg"></i>
                    </div>

                    <div class="min-w-0">
                        <h2 class="font-semibold text-slate-900">
                            Technical Request Information
                        </h2>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Additional context for authorized audit review.
                        </p>
                    </div>
                </div>

                <i class="fas fa-chevron-down shrink-0 text-xs text-slate-400 transition group-open:rotate-180"></i>
            </summary>

            <div class="grid grid-cols-1 gap-5 border-t border-slate-100 p-4 sm:grid-cols-2 sm:p-5">

                <div class="min-w-0">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        IP Address
                    </p>

                    <p class="break-all text-sm text-slate-700">
                        {{ $activity->ip_address ?: 'Not recorded' }}
                    </p>
                </div>

                <div class="min-w-0">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Request Method
                    </p>

                    <p class="text-sm text-slate-700">
                        {{ $activity->http_method ?: 'Not recorded' }}
                    </p>
                </div>

                <div class="min-w-0 sm:col-span-2">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Request Path
                    </p>

                    <p class="break-all text-sm text-slate-700">
                        {{ $activity->url ? parse_url($activity->url, PHP_URL_PATH) : 'Not recorded' }}
                    </p>
                </div>

                <div class="min-w-0 sm:col-span-2">
                    <p class="mb-1 text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Browser / Device Information
                    </p>

                    <p class="break-words text-sm leading-6 text-slate-700">
                        {{ $activity->user_agent ?: 'Not recorded' }}
                    </p>
                </div>

            </div>
        </details>

        {{-- BACK BUTTON --}}
        <div class="flex">
            <a href="{{ route('admin.audit-logs.index') }}"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 sm:ml-auto sm:w-auto">
                <i class="fas fa-arrow-left"></i>
                Return to Employee Activity Logs
            </a>
        </div>

    </div>
@endsection
