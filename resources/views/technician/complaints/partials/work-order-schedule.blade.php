@php
    $state = $scheduleState ?? [
        'state' => 'unscheduled',
        'label' => 'Not Scheduled',
        'can_start' => false,
        'message' => 'No scheduled work order.',
        'schedule_date' => null,
    ];
    $currentState = $state['state'] ?? 'unscheduled';
    $scheduleColors = match ($currentState) {
        'ready', 'completed' => 'bg-green-50 border-green-200 text-green-800',
        'in_progress', 'unfinished_previous_day' => 'bg-amber-50 border-amber-200 text-amber-800',
        'upcoming' => 'bg-blue-50 border-blue-200 text-blue-800',
        'pending' => 'bg-violet-50 border-violet-200 text-violet-800',
        'overdue', 'schedule_missing', 'legacy_review', 'approval_review' => 'bg-red-50 border-red-200 text-red-800',
        'pending_workload' => 'bg-orange-50 border-orange-200 text-orange-800',
        default => 'bg-gray-50 border-gray-200 text-gray-700',
    };
    $scheduleDate = $state['schedule_date'] ?? null;
    $dateLabel = $scheduleDate
        ? \Carbon\CarbonImmutable::parse($scheduleDate, 'Asia/Manila')->format('M d, Y (l)')
        : 'Not yet scheduled';
    $visitNumber = $workOrder?->visit_number;
    $isFollowUp = $visitNumber !== null && (int) $visitNumber > 1;
    $team = $workOrderTeam ?? collect();
    $isApproved = $workOrder?->approval_status === 'Approved';

    $latestReschedule = $workOrder
        ? \App\Models\MaintenanceScheduleHistory::query()
            ->where('maintenance_work_order_id', $workOrder->id)
            ->where('action', 'Rescheduled')
            ->latest('id')
            ->first()
        : null;

    $previousScheduleDate = $latestReschedule ? data_get($latestReschedule->old_values, 'schedule_date') : null;

    $newScheduleDate = $latestReschedule ? data_get($latestReschedule->new_values, 'schedule_date') : null;
@endphp

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-sky-50 text-sky-700 flex items-center justify-center">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">Maintenance Work Schedule</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Manager-planned work order and execution authorization</p>
                </div>
            </div>
            <span
                class="inline-flex items-center px-3 py-1.5 rounded-full border text-xs font-semibold {{ $scheduleColors }}">
                {{ $state['label'] ?? 'Not Scheduled' }}
            </span>
        </div>
    </div>

    <div class="p-5 sm:p-6 space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Work Date</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">{{ $dateLabel }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">Work Order</p>
                <p class="mt-1 text-sm font-semibold text-gray-900">
                    {{ $workOrder ? '#' . $workOrder->id : 'Not created' }}
                </p>
                @if ($visitNumber !== null)
                    <p class="mt-1 text-xs text-gray-500">
                        Visit {{ $visitNumber }} · {{ $isFollowUp ? 'Follow-up Maintenance' : 'Initial Visit' }}
                    </p>
                @endif
            </div>
        </div>

        @if ($workOrder)
            <div class="border-t border-gray-100 pt-4 space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Work Order Approval</span>
                    @if ($isApproved)
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                            <i class="fas fa-circle-check"></i> Approved
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5 rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                            <i class="fas fa-clock"></i> Draft — Awaiting Approval
                        </span>
                    @endif
                </div>
                @if ($workOrder->schedule)
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-wide text-gray-400">Daily Schedule</span>
                        @if ($workOrder->schedule->status === 'Finalized')
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-green-200 bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                <i class="fas fa-circle-check"></i> Finalized
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full border border-violet-200 bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">
                                <i class="fas fa-clock"></i> {{ $workOrder->schedule->status }}
                            </span>
                        @endif
                    </div>
                @endif
                @if (!$isApproved)
                    <p class="rounded-xl border border-violet-200 bg-violet-50 px-4 py-3 text-sm text-violet-800">
                        <i class="fas fa-lock mr-1"></i>
                        This assignment is visible for reference only. Start Maintenance is unavailable until the
                        Maintenance Manager approves this work order, even if the daily schedule is already Finalized.
                    </p>
                @endif
            </div>
        @endif{{-- Rescheduling Information --}}
        @if ($latestReschedule)
            <div class="border-t border-gray-100 pt-4 space-y-3">

                <div class="flex items-center gap-2">

                    <i class="fas fa-calendar-days text-amber-600"></i>

                    <h3 class="text-sm font-semibold text-gray-900">
                        Rescheduling Information
                    </h3>

                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    <div class="rounded-xl bg-gray-50 border border-gray-200 p-3">

                        <p class="text-xs text-gray-500">
                            Previous Schedule
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $previousScheduleDate ? \Carbon\Carbon::parse($previousScheduleDate)->format('M d, Y') : 'Not recorded' }}
                        </p>

                    </div>

                    <div class="rounded-xl bg-sky-50 border border-sky-200 p-3">

                        <p class="text-xs text-sky-600">
                            Rescheduled To
                        </p>

                        <p class="mt-1 text-sm font-semibold text-sky-900">
                            {{ $newScheduleDate ? \Carbon\Carbon::parse($newScheduleDate)->format('M d, Y') : 'Not recorded' }}
                        </p>

                    </div>

                </div>

                <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">
                        <i class="fas fa-clipboard-list mr-1"></i>
                        Manager's Rescheduling Reason
                    </p>

                    <p class="mt-2 text-sm text-amber-950 whitespace-pre-line">
                        {{ $latestReschedule->reason ?: 'No reason recorded.' }}
                    </p>

                    <p class="mt-3 text-xs text-amber-700">
                        Rescheduled on
                        {{ $latestReschedule->created_at
                            ? $latestReschedule->created_at->timezone('Asia/Manila')->format('M d, Y h:i A')
                            : 'Unknown date' }}
                    </p>

                </div>

            </div>
        @endif

        @if ($team->isNotEmpty())
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Assigned Maintenance Team
                </p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($team as $member)
                        <span
                            class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-xs font-medium text-slate-700">
                            <i class="fas fa-user-gear text-slate-400"></i> {{ $member->full_name }}
                        </span>
                    @endforeach
                </div>
            </div>
        @elseif ($workOrder)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-sm text-amber-700">
                    <i class="fas fa-triangle-exclamation mr-1"></i>
                    No dedicated maintenance team is recorded for this work order. Please contact the Maintenance
                    Manager.
                </p>
            </div>
        @endif

        @if ($workOrder?->status === 'Pending Workload' && $workOrder->pending_reason)
            <div class="border-t border-gray-100 pt-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-400 mb-2">Reason for Unfinished
                    Maintenance</p>
                <div class="rounded-xl border border-orange-200 bg-orange-50 p-4">
                    <p class="text-sm text-orange-900 whitespace-pre-line">{{ $workOrder->pending_reason }}</p>
                </div>
            </div>
        @endif

        <div class="rounded-xl border px-4 py-3 text-sm {{ $scheduleColors }}">
            <div class="flex items-start gap-2">
                <i class="fas fa-circle-info mt-0.5"></i>
                <p>{{ $state['message'] ?? 'Schedule information unavailable.' }}</p>
            </div>
        </div>
    </div>
</div>
