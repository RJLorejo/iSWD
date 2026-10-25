@extends('maintenance-manager.layouts.app')

@section('title', 'Maintenance Manager Dashboard')

@section('content')
    <div class="w-full min-w-0 max-w-full space-y-5 overflow-x-hidden pb-6 sm:space-y-6">

        {{-- PAGE HEADER --}}
        <x-form.page-header title="Maintenance Manager Dashboard"
            subtitle="Monitor maintenance operations, assign technicians, and manage verified complaints." />

        {{-- =========================================================
         SUMMARY CARDS
    ========================================================== --}}
        <div class="grid min-w-0 grid-cols-1 gap-3 min-[420px]:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-6 sm:gap-4">

            {{-- VERIFIED COMPLAINTS --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Verified Complaints</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $verifiedComplaints }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Ready for processing</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-indigo-50 text-indigo-600">
                        <i class="fas fa-shield-check"></i>
                    </div>
                </div>
            </x-form.card>

            {{-- UNASSIGNED CASES --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Unassigned Cases</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $unassignedCases }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Awaiting technician</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">
                        <i class="fas fa-user-clock"></i>
                    </div>
                </div>
            </x-form.card>

            {{-- ASSIGNED CASES --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Assigned Cases</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $assignedCases }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Assigned to technicians</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>
            </x-form.card>

            {{-- IN PROGRESS --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">In Progress</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $inProgressComplaints }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Active maintenance</p>
                    </div>
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-orange-50 text-orange-600">
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>
                </div>
            </x-form.card>

            {{-- REPORTS FOR REVIEW --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Reports for Review</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $reportsForReview }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Awaiting validation</p>
                    </div>
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-purple-50 text-purple-600">
                        <i class="fas fa-file-circle-check"></i>
                    </div>
                </div>
            </x-form.card>

            {{-- COMPLETED TODAY --}}
            <x-form.card>
                <div class="flex min-w-0 items-center justify-between gap-3 p-4">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-gray-500 sm:text-sm">Completed Today</p>
                        <p class="mt-1 text-2xl font-bold tracking-tight text-gray-900 tabular-nums">
                            {{ $completedToday }}
                        </p>
                        <p class="mt-1 text-xs text-gray-400">Successfully completed</p>
                    </div>
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-50 text-green-600">
                        <i class="fas fa-circle-check"></i>
                    </div>
                </div>
            </x-form.card>

        </div>

        {{-- =========================================================
         OPERATIONAL ANALYTICS
    ========================================================== --}}
        <div class="grid min-w-0 grid-cols-1 gap-4 xl:grid-cols-2 sm:gap-5">

            {{-- CASE STATUS --}}
            <x-form.card>
                <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                    <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
                        <i class="fas fa-chart-column mr-2 text-indigo-600"></i>
                        Maintenance Case Status
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">
                        Current distribution of maintenance cases.
                    </p>
                </div>

                <div class="min-w-0 p-3 sm:p-5">
                    <div id="maintenanceCaseStatusChart" class="min-h-[250px] w-full min-w-0"></div>
                </div>
            </x-form.card>

            {{-- WORKFLOW OVERVIEW --}}
            <x-form.card>
                <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                    <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
                        <i class="fas fa-chart-pie mr-2 text-blue-600"></i>
                        Workflow Overview
                    </h3>
                    <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">
                        Current maintenance workload distribution.
                    </p>
                </div>

                <div class="min-w-0 p-3 sm:p-5">
                    <div id="maintenanceWorkflowChart" class="min-h-[250px] w-full min-w-0"></div>
                </div>
            </x-form.card>

        </div>

        {{-- CHART DATA --}}
        <script>
            window.maintenanceDashboard = {
                verified: @json($verifiedComplaints),
                unassigned: @json($unassignedCases),
                assigned: @json($assignedCases),
                inProgress: @json($inProgressComplaints),
                review: @json($reportsForReview),
                completed: @json($completedToday),

                workflow: @json(
                    $maintenanceWorkflowChart ?? [
                        'labels' => [],
                        'data' => [],
                    ]
                ),
            };
        </script>

        {{-- =========================================================
         QUICK ACTIONS
    ========================================================== --}}
        <x-form.card>
            <div class="flex min-w-0 flex-col gap-4 p-4 sm:p-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="min-w-0">
                    <h3 class="font-semibold text-gray-900">Maintenance Operations</h3>
                    <p class="mt-1 text-sm leading-5 text-gray-500">
                        Manage verified complaints and technician assignments.
                    </p>
                </div>

                <div class="grid min-w-0 grid-cols-1 gap-2 min-[480px]:grid-cols-2 lg:flex lg:flex-wrap">
                    <a href="{{ route('maintenance-manager.schedules.index') }}"
                        class="inline-flex min-w-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-3 py-2.5 text-center text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-list-check shrink-0"></i>
                        <span>Verified Complaints</span>
                    </a>

                    <a href="{{ route('maintenance-manager.schedules.index') }}"
                        class="inline-flex min-w-0 items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        <i class="fas fa-clipboard-list shrink-0"></i>
                        <span>Work Orders</span>
                    </a>

                    <a href="{{ route('maintenance-manager.insights.index') }}"
                        class="inline-flex min-w-0 items-center justify-center gap-2 rounded-lg bg-sky-700 px-3 py-2.5 text-center text-sm font-medium text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 min-[480px]:col-span-2 lg:col-span-1">
                        <i class="fas fa-chart-line shrink-0"></i>
                        <span>Insights &amp; Reports</span>
                    </a>
                </div>
            </div>
        </x-form.card>

        {{-- =========================================================
         COMPLAINTS AND TECHNICIAN WORKLOAD
    ========================================================== --}}
        <div class="grid min-w-0 grid-cols-1 gap-4 xl:grid-cols-3 sm:gap-5">

            {{-- VERIFIED COMPLAINTS --}}
            <div class="min-w-0 xl:col-span-2">
                <x-form.card>
                    <div
                        class="flex min-w-0 flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                        <div class="min-w-0">
                            <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
                                <i class="fas fa-shield-check mr-2 text-indigo-600"></i>
                                New &amp; Verified Complaints
                            </h3>
                            <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">
                                Recently verified complaints waiting for technician assignment.
                            </p>
                        </div>

                        <span
                            class="inline-flex w-fit shrink-0 items-center rounded-full bg-indigo-50 px-3 py-1.5 text-xs font-semibold text-indigo-700">
                            {{ $unassignedCases }} Ready
                        </span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($recentVerifiedComplaints as $complaint)
                            <div class="min-w-0 p-4 transition hover:bg-gray-50 sm:p-5">
                                <div class="flex min-w-0 flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex min-w-0 flex-wrap items-center gap-2">
                                            <span class="break-words text-sm font-semibold text-gray-900">
                                                {{ $complaint->complaint_no }}
                                            </span>

                                            @php
                                                $analysis = $complaint->aiAnalysis;

                                                $urgency = strtoupper(
                                                    trim(
                                                        (string) ($analysis?->verified_urgency_level ??
                                                            ($analysis?->urgency_level ??
                                                                ($analysis?->predicted_urgency ??
                                                                    ($analysis?->urgency ?? '')))),
                                                    ),
                                                );

                                                // Normalize equivalent urgency labels without inventing a prediction.
                                                $urgency = match ($urgency) {
                                                    'CRITICAL', 'URGENT', 'VERY HIGH' => 'HIGH',
                                                    'MEDIUM', 'MODERATE' => 'MODERATE',
                                                    'NORMAL', 'MINOR' => 'LOW',
                                                    'HIGH', 'LOW' => $urgency,
                                                    default => '',
                                                };

                                                $urgencyClasses = match ($urgency) {
                                                    'HIGH' => 'bg-red-50 text-red-700',
                                                    'MODERATE' => 'bg-amber-50 text-amber-700',
                                                    'LOW' => 'bg-green-50 text-green-700',
                                                    default => 'bg-gray-100 text-gray-500',
                                                };

                                                $urgencyLabel = $urgency !== '' ? $urgency : 'NOT ASSESSED';
                                            @endphp

                                            <span
                                                class="max-w-full break-words rounded-full px-2 py-1 text-[11px] font-semibold {{ $urgencyClasses }}">
                                                {{ $urgencyLabel }} Urgency
                                            </span>
                                        </div>

                                        <p class="mt-2 break-words text-sm leading-6 text-gray-600">
                                            {{ Str::limit($complaint->description ?? 'No description provided', 100) }}
                                        </p>

                                        <div
                                            class="mt-3 flex min-w-0 flex-col gap-2 text-xs text-gray-500 sm:flex-row sm:flex-wrap sm:gap-x-4">
                                            <span class="flex min-w-0 items-start gap-1.5">
                                                <i class="fas fa-tag mt-0.5 shrink-0"></i>
                                                <span class="break-words">{{ $complaint->category?->name ?? '—' }}</span>
                                            </span>

                                            <span class="flex min-w-0 items-start gap-1.5">
                                                <i class="fas fa-location-dot mt-0.5 shrink-0"></i>
                                                <span
                                                    class="break-words">{{ Str::limit($complaint->address ?? 'Address unavailable', 70) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <a href="{{ route('maintenance-manager.schedules.index') }}"
                                        class="inline-flex w-full shrink-0 items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                                        <i class="fas fa-user-plus"></i>
                                        Assign
                                    </a>
                                </div>
                            </div>
                        @empty
                            <div class="px-4 py-10 text-center sm:px-6">
                                <div
                                    class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-green-50 text-green-600">
                                    <i class="fas fa-circle-check text-lg"></i>
                                </div>
                                <h4 class="mt-3 text-sm font-semibold text-gray-900">
                                    No Complaints Ready for Assignment
                                </h4>
                                <p class="mt-1 text-sm text-gray-500">
                                    Verified complaints will appear here.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </x-form.card>
            </div>

            {{-- TECHNICIAN WORKLOAD --}}
            <div class="min-w-0">
                <x-form.card>
                    <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                        <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
                            <i class="fas fa-users-gear mr-2 text-blue-600"></i>
                            Technician Workload
                        </h3>
                        <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">
                            Current active assignments.
                        </p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse ($technicians as $technician)
                            <div class="flex min-w-0 items-center gap-3 p-4">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-semibold text-blue-700">
                                    {{ strtoupper(substr($technician->first_name ?? ($technician->name ?? 'T'), 0, 1)) }}
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="break-words text-sm font-medium text-gray-900">
                                        {{ $technician->full_name ?? ($technician->name ?? trim(($technician->first_name ?? '') . ' ' . ($technician->last_name ?? ''))) }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-gray-500">
                                        Maintenance Technician
                                    </p>
                                </div>

                                <span
                                    class="inline-flex h-7 min-w-7 shrink-0 items-center justify-center rounded-full bg-gray-100 px-2 text-xs font-semibold text-gray-700">
                                    {{ $technician->active_complaints_count }}
                                </span>
                            </div>
                        @empty
                            <div class="p-8 text-center text-sm text-gray-500">
                                No technicians available.
                            </div>
                        @endforelse
                    </div>

                    <div class="border-t border-gray-100 p-4">
                        <a href="{{ route('maintenance-manager.technician-duties.index') }}"
                            class="flex items-center justify-center gap-2 rounded-lg px-3 py-2 text-center text-sm font-medium text-blue-700 transition hover:bg-blue-50">
                            <span>View Technician Workload</span>
                            <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </x-form.card>
            </div>

        </div>

        {{-- =========================================================
         ACTIVE MAINTENANCE
    ========================================================== --}}
        <x-form.card>
            <div class="border-b border-gray-100 px-4 py-4 sm:px-5">
                <h3 class="text-sm font-semibold text-gray-900 sm:text-base">
                    <i class="fas fa-screwdriver-wrench mr-2 text-orange-600"></i>
                    Active Maintenance
                </h3>
                <p class="mt-1 text-xs leading-5 text-gray-500 sm:text-sm">
                    Complaints currently assigned or being worked on.
                </p>
            </div>

            {{-- MOBILE / SMALL TABLET: CARD LAYOUT --}}
            <div class="divide-y divide-gray-100 lg:hidden">
                @forelse ($activeComplaints as $complaint)
                    <article class="min-w-0 p-4 sm:p-5">
                        <div class="flex min-w-0 items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <p class="break-words text-sm font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>
                                <p class="mt-1 break-words text-xs leading-5 text-gray-500">
                                    {{ Str::limit($complaint->category?->name ?? 'Maintenance Complaint', 70) }}
                                </p>
                            </div>

                            <span
                                class="inline-flex max-w-[45%] shrink-0 items-center rounded-full px-2.5 py-1 text-center text-[11px] font-semibold leading-4
                            {{ $complaint->status === 'In Progress' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700' }}">
                                {{ $complaint->status }}
                            </span>
                        </div>

                        <div class="mt-4 grid min-w-0 grid-cols-1 gap-3 min-[420px]:grid-cols-2">
                            <div class="min-w-0">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                    Consumer
                                </p>
                                <p class="mt-1 break-words text-sm text-gray-700">
                                    {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}
                                </p>
                            </div>

                            <div class="min-w-0">
                                <p class="text-[11px] font-medium uppercase tracking-wide text-gray-400">
                                    Technician
                                </p>
                                @if ($complaint->technicians->isNotEmpty())
                                    <p class="mt-1 break-words text-sm text-gray-700">
                                        {{ $complaint->technicians->map(fn($technician) => $technician->full_name ?? ($technician->name ?? trim(($technician->first_name ?? '') . ' ' . ($technician->last_name ?? ''))))->join(', ') }}
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-gray-400">Unassigned</p>
                                @endif
                            </div>
                        </div>

                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                            class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <i class="fas fa-eye"></i>
                            View Complaint
                        </a>
                    </article>
                @empty
                    <div class="px-4 py-10 text-center text-sm text-gray-500">
                        No active maintenance complaints.
                    </div>
                @endforelse
            </div>

            {{-- DESKTOP: TABLE LAYOUT --}}
            <div class="hidden min-w-0 lg:block">
                <div class="w-full min-w-0">
                    <table class="w-full table-fixed text-left text-sm">
                        <thead class="border-b border-gray-200 bg-gray-50">
                            <tr>
                                <th class="w-[21%] px-4 py-3 font-semibold text-gray-600 xl:px-5">
                                    Complaint
                                </th>
                                <th class="w-[19%] px-4 py-3 font-semibold text-gray-600 xl:px-5">
                                    Consumer
                                </th>
                                <th class="w-[27%] px-4 py-3 font-semibold text-gray-600 xl:px-5">
                                    Technician
                                </th>
                                <th class="w-[17%] px-4 py-3 font-semibold text-gray-600 xl:px-5">
                                    Status
                                </th>
                                <th class="w-[16%] px-4 py-3 text-right font-semibold text-gray-600 xl:px-5">
                                    Action
                                </th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($activeComplaints as $complaint)
                                <tr class="transition hover:bg-gray-50">
                                    <td class="break-words px-4 py-4 xl:px-5">
                                        <p class="break-words font-semibold text-gray-900">
                                            {{ $complaint->complaint_no }}
                                        </p>
                                        <p class="mt-1 break-words text-xs leading-5 text-gray-500">
                                            {{ Str::limit($complaint->category?->name ?? 'Maintenance Complaint', 55) }}
                                        </p>
                                    </td>

                                    <td class="break-words px-4 py-4 text-gray-700 xl:px-5">
                                        {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}
                                    </td>

                                    <td class="break-words px-4 py-4 xl:px-5">
                                        @if ($complaint->technicians->isNotEmpty())
                                            <span class="break-words font-medium text-gray-800">
                                                {{ $complaint->technicians->map(fn($technician) => $technician->full_name ?? ($technician->name ?? trim(($technician->first_name ?? '') . ' ' . ($technician->last_name ?? ''))))->join(', ') }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">Unassigned</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 xl:px-5">
                                        <span
                                            class="inline-flex max-w-full rounded-full px-2.5 py-1 text-xs font-medium
                                        {{ $complaint->status === 'In Progress' ? 'bg-orange-50 text-orange-700' : 'bg-blue-50 text-blue-700' }}">
                                            {{ $complaint->status }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-right xl:px-5">
                                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                            class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-200 px-3 py-2 text-xs font-medium text-gray-700 transition hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 xl:text-sm">
                                            <i class="fas fa-eye"></i>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-10 text-center text-sm text-gray-500">
                                        No active maintenance complaints.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </x-form.card>

    </div>
@endsection
