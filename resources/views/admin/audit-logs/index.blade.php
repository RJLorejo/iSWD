
@extends('admin.layouts.app')

@section('title', 'Employee Activity Logs')

@section('content')
<div class="min-h-screen space-y-5 sm:space-y-6">

    {{-- PAGE HEADER --}}
    <div class="flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
        <div class="min-w-0">
            <div class="mb-2 flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 sm:text-sm">
                <i class="fas fa-shield-alt text-blue-600"></i>
                <span>Administration</span>
                <i class="fas fa-chevron-right text-[10px]"></i>
                <span class="text-slate-700">Employee Activity</span>
            </div>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Employee Activity Logs
            </h1>

            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                Monitor and review activities performed by authorized iSWD office employees.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-2 sm:flex">
            <a href="{{ route('admin.audit-logs.print', request()->query()) }}"
               target="_blank"
               class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 sm:px-4 sm:text-sm">
                <i class="fas fa-print"></i>
                Print Report
            </a>

            <a href="{{ route('admin.audit-logs.export', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-3 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-blue-700 sm:px-4 sm:text-sm">
                <i class="fas fa-file-export"></i>
                Export CSV
            </a>
        </div>
    </div>

    {{-- VALIDATION ERRORS --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
            <div class="flex items-start gap-3">
                <i class="fas fa-exclamation-circle mt-0.5"></i>
                <div>
                    <p class="font-semibold">Please check the selected filters.</p>
                    <ul class="mt-1 list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    {{-- SUMMARY CARDS --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

        @foreach ([
            [
                'label' => 'Total Activities',
                'value' => $totalCount,
                'description' => 'Recorded employee activities',
                'icon' => 'fas fa-history',
                'color' => 'blue',
            ],
            [
                'label' => 'Activities Today',
                'value' => $todayCount,
                'description' => now()->format('F d, Y'),
                'icon' => 'fas fa-calendar-check',
                'color' => 'emerald',
            ],
            [
                'label' => 'Employees Active Today',
                'value' => $activeUsersToday,
                'description' => 'Employees with recorded activity',
                'icon' => 'fas fa-users',
                'color' => 'violet',
            ],
            [
                'label' => 'Security Events Today',
                'value' => $securityEventsToday,
                'description' => 'Recorded access and security events',
                'icon' => 'fas fa-shield-alt',
                'color' => 'amber',
            ],
        ] as $card)
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm transition hover:shadow-md sm:p-5">
                <div class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="text-xs font-medium text-slate-500 sm:text-sm">
                            {{ $card['label'] }}
                        </p>

                        <p class="mt-2 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                            {{ number_format($card['value']) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $card['description'] }}
                        </p>
                    </div>

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-{{ $card['color'] }}-50 text-{{ $card['color'] }}-600 sm:h-12 sm:w-12">
                        <i class="{{ $card['icon'] }} text-lg sm:text-xl"></i>
                    </div>
                </div>
            </div>
        @endforeach

    </div>

    {{-- EMPLOYEE MONITORING COVERAGE --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="flex items-start gap-3">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">
                <i class="fas fa-user-shield"></i>
            </div>

            <div class="min-w-0">
                <h2 class="font-semibold text-slate-900">
                    Employee Monitoring Coverage
                </h2>
                <p class="mt-1 text-xs leading-5 text-slate-500 sm:text-sm">
                    Activity records are limited to authorized internal office roles.
                </p>
            </div>
        </div>

        <div class="mt-4 flex flex-wrap gap-2">
            @foreach (['Administrator', 'Maintenance Manager', 'Customer Service', 'Maintenance Technician'] as $employeeRole)
                <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    {{ $employeeRole }}
                </span>
            @endforeach
        </div>
    </div>

    {{-- SEARCH AND FILTERS --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
        <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-slate-900">Search and Filters</h2>
                <p class="mt-1 text-xs text-slate-500 sm:text-sm">
                    Find employee activities using the available filters.
                </p>
            </div>

            <a href="{{ route('admin.audit-logs.index') }}"
               class="inline-flex items-center gap-2 text-xs font-semibold text-blue-600 hover:text-blue-700 sm:text-sm">
                <i class="fas fa-undo"></i>
                Reset filters
            </a>
        </div>

        <form method="GET"
              action="{{ route('admin.audit-logs.index') }}"
              class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-6">

            <div class="sm:col-span-2 xl:col-span-2">
                <label for="search" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Search activities
                </label>

                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>

                    <input id="search"
                           type="text"
                           name="search"
                           value="{{ $filters['search'] ?? '' }}"
                           placeholder="Employee, activity, description..."
                           class="w-full rounded-xl border border-slate-200 py-2.5 pl-9 pr-3 text-sm outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                </div>
            </div>

            <div>
                <label for="role" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Employee Role
                </label>

                <select id="role" name="role"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All employee roles</option>

                    @foreach ($roles as $role)
                        <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="module" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Module
                </label>

                <select id="module" name="module"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All modules</option>

                    @foreach ($modules as $module)
                        <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>
                            {{ $module }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="action" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    Activity
                </label>

                <select id="action" name="action"
                        class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                    <option value="">All activities</option>

                    @foreach ($actions as $action)
                        <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>
                            {{ $action }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="from" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    From date
                </label>

                <input id="from" type="date" name="from"
                       value="{{ $filters['from'] ?? '' }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div>
                <label for="to" class="mb-1.5 block text-xs font-semibold text-slate-600">
                    To date
                </label>

                <input id="to" type="date" name="to"
                       value="{{ $filters['to'] ?? '' }}"
                       class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
            </div>

            <div class="flex items-end sm:col-span-2 xl:col-span-6">
                <button type="submit"
                        class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 sm:w-auto">
                    <i class="fas fa-filter"></i>
                    Apply Filters
                </button>
            </div>
        </form>
    </div>

    {{-- ACTIVITY HISTORY --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h2 class="font-semibold text-slate-900">Employee Activity History</h2>
                <p class="mt-1 text-xs text-slate-500">
                    Chronological record of actions performed by internal employees.
                </p>
            </div>

            <span class="inline-flex w-fit items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700">
                <i class="fas fa-list"></i>
                {{ number_format($activities->total()) }} matching records
            </span>
        </div>

        @if ($activities->count())

            {{-- DESKTOP AND TABLET TABLE --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="min-w-full divide-y divide-slate-100">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Date & Time
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Employee
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Module
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Activity
                            </th>
                            <th class="min-w-[250px] px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Description
                            </th>
                            <th class="whitespace-nowrap px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Details
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($activities as $activity)
                            @php
                                $actionText = strtolower($activity->action ?? '');
                                $actionStyle = 'bg-slate-100 text-slate-700';

                                if (
                                    str_contains($actionText, 'delete') ||
                                    str_contains($actionText, 'reject') ||
                                    str_contains($actionText, 'deactivat')
                                ) {
                                    $actionStyle = 'bg-red-50 text-red-700';
                                } elseif (
                                    str_contains($actionText, 'create') ||
                                    str_contains($actionText, 'assign') ||
                                    str_contains($actionText, 'complete') ||
                                    str_contains($actionText, 'approve')
                                ) {
                                    $actionStyle = 'bg-emerald-50 text-emerald-700';
                                } elseif (
                                    str_contains($actionText, 'update') ||
                                    str_contains($actionText, 'edit') ||
                                    str_contains($actionText, 'reschedule')
                                ) {
                                    $actionStyle = 'bg-amber-50 text-amber-700';
                                }
                            @endphp

                            <tr class="transition hover:bg-slate-50/80">
                                <td class="whitespace-nowrap px-5 py-4 align-top">
                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $activity->created_at?->format('M d, Y') ?? '—' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $activity->created_at?->format('h:i A') ?? '—' }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <div class="flex min-w-[180px] items-center gap-3">
                                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-blue-50 text-sm font-bold text-blue-700">
                                            {{ strtoupper(substr($activity->user_name ?: 'U', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900">
                                                {{ $activity->user_name ?: 'Unknown employee' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-500">
                                                {{ $activity->user_role ?: 'Unknown role' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 align-top">
                                    <span class="inline-flex rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-700">
                                        {{ $activity->module ?: 'General' }}
                                    </span>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 align-top">
                                    <span class="inline-flex max-w-[190px] items-center rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $actionStyle }}">
                                        {{ $activity->action ?: 'Activity recorded' }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 align-top">
                                    <p class="max-w-md text-sm leading-5 text-slate-700">
                                        {{ $activity->description ?: 'No additional description was provided.' }}
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-5 py-4 text-right align-top">
                                    <a href="{{ route('admin.audit-logs.show', $activity) }}"
                                       class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-700">
                                        <i class="fas fa-eye"></i>
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- MOBILE ACTIVITY CARDS --}}
            <div class="space-y-3 p-3 md:hidden">
                @foreach ($activities as $activity)
                    @php
                        $actionText = strtolower($activity->action ?? '');
                        $actionStyle = 'bg-slate-100 text-slate-700';
                        $actionIcon = 'fas fa-history';

                        if (
                            str_contains($actionText, 'delete') ||
                            str_contains($actionText, 'reject') ||
                            str_contains($actionText, 'deactivat')
                        ) {
                            $actionStyle = 'bg-red-50 text-red-700';
                            $actionIcon = 'fas fa-exclamation-circle';
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
                    @endphp

                    <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 font-bold text-blue-700">
                                    {{ strtoupper(substr($activity->user_name ?: 'U', 0, 1)) }}
                                </div>

                                <div class="min-w-0">
                                    <p class="break-words text-sm font-bold text-slate-900">
                                        {{ $activity->user_name ?: 'Unknown employee' }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $activity->user_role ?: 'Unknown role' }}
                                    </p>
                                </div>
                            </div>

                            <span class="shrink-0 text-[10px] font-medium text-slate-400">
                                #{{ $activity->created_at?->format('His') ?? '—' }}
                            </span>
                        </div>

                        <div class="mt-4 flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $actionStyle }}">
                                <i class="{{ $actionIcon }}"></i>
                                {{ $activity->action ?: 'Activity recorded' }}
                            </span>

                            <span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1.5 text-xs font-medium text-slate-600">
                                <i class="fas fa-layer-group text-slate-400"></i>
                                {{ $activity->module ?: 'General' }}
                            </span>
                        </div>

                        <div class="mt-3 rounded-lg bg-slate-50 p-3">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                Description
                            </p>
                            <p class="mt-1 break-words text-sm leading-5 text-slate-700">
                                {{ $activity->description ?: 'No additional description was provided.' }}
                            </p>
                        </div>

                        <div class="mt-3 flex flex-col gap-3 border-t border-slate-100 pt-3 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2 text-xs text-slate-500">
                                <i class="fas fa-clock text-slate-400"></i>
                                <span>
                                    {{ $activity->created_at?->format('M d, Y') ?? '—' }}
                                    ·
                                    {{ $activity->created_at?->format('h:i A') ?? '—' }}
                                </span>
                            </div>

                            <a href="{{ route('admin.audit-logs.show', $activity) }}"
                               class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-blue-600 px-3 py-2.5 text-xs font-semibold text-white transition hover:bg-blue-700 sm:w-auto">
                                <i class="fas fa-eye"></i>
                                View Activity Details
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- PAGINATION --}}
            <div class="border-t border-slate-100 px-3 py-4 sm:px-5">
                {{ $activities->links() }}
            </div>

        @else

            {{-- EMPTY STATE --}}
            <div class="px-5 py-14 text-center sm:py-16">
                <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-clipboard-list text-2xl"></i>
                </div>

                <h3 class="mt-4 text-base font-semibold text-slate-900">
                    No employee activities found
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    No records match your current search or filters. Try adjusting your filters or check again later.
                </p>

                <a href="{{ route('admin.audit-logs.index') }}"
                   class="mt-5 inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700">
                    <i class="fas fa-undo"></i>
                    Clear filters
                </a>
            </div>

        @endif

        {{-- FOOTER --}}
        <div class="border-t border-slate-100 bg-slate-50 px-4 py-3 sm:px-5">
            <div class="flex flex-col gap-2 text-xs text-slate-500 sm:flex-row sm:items-center sm:justify-between">
                <span class="flex items-start gap-2">
                    <i class="fas fa-lock mt-0.5"></i>
                    Employee activity records are for internal administrative monitoring.
                </span>

                <span class="flex items-center gap-2">
                    <i class="fas fa-sort-amount-down"></i>
                    Newest records first
                </span>
            </div>
        </div>
    </div>
</div>
@endsection
