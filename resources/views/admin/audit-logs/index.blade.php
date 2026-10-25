@extends('admin.layouts.app')

@section('title', 'System Activity Log')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">System Activity Log</h1>
            <p class="text-sm text-slate-500">
                Monitor recorded activities and changes across the iSWD system.
            </p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.audit-logs.export', request()->query()) }}"
               class="inline-flex items-center gap-2 rounded-lg border border-green-600 px-4 py-2 text-sm font-medium text-green-700 hover:bg-green-50">
                <i class="fa-solid fa-file-csv"></i> Export CSV
            </a>

            <a href="{{ route('admin.audit-logs.print', request()->query()) }}"
               target="_blank" rel="noopener"
               class="inline-flex items-center gap-2 rounded-lg border border-blue-600 px-4 py-2 text-sm font-medium text-blue-700 hover:bg-blue-50">
                <i class="fa-solid fa-print"></i> Print Report
            </a>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
            <div class="flex items-start gap-3">
                <i class="fa-solid fa-triangle-exclamation mt-0.5 text-red-600"></i>
                <div>
                    <strong>Unable to apply filters.</strong>
                    Please check your search criteria and date range.
                </div>
            </div>
        </div>
    @endif

    {{-- Summary Cards --}}
    @php
        $cards = [
            ['label' => 'Total Recorded Activities', 'value' => $totalCount, 'note' => 'All recorded events', 'icon' => 'fa-book', 'color' => 'bg-blue-100 text-blue-600'],
            ['label' => 'Activities Today', 'value' => $todayCount, 'note' => now()->format('F d, Y'), 'icon' => 'fa-chart-line', 'color' => 'bg-green-100 text-green-600'],
            ['label' => 'Users Recorded Today', 'value' => $activeUsersToday, 'note' => 'Distinct authenticated users', 'icon' => 'fa-users', 'color' => 'bg-sky-100 text-sky-600'],
            ['label' => 'Security-Related Events Today', 'value' => $securityEventsToday, 'note' => 'Based on recorded event labels', 'icon' => 'fa-shield-halved', 'color' => 'bg-amber-100 text-amber-600'],
        ];
    @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($cards as $card)
            <div class="rounded-xl bg-white p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <div class="mb-1 text-sm text-slate-500">{{ $card['label'] }}</div>
                        <div class="text-3xl font-bold text-slate-800">{{ number_format($card['value']) }}</div>
                        <div class="text-xs text-slate-500">{{ $card['note'] }}</div>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl {{ $card['color'] }}">
                        <i class="fa-solid {{ $card['icon'] }} text-lg"></i>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Search and Filters --}}
    <div class="rounded-xl bg-white shadow-sm">
        <div class="flex items-center gap-2 border-b border-slate-100 px-5 py-4">
            <i class="fa-solid fa-filter text-blue-600"></i>
            <h2 class="text-sm font-semibold text-slate-800">Search and Filters</h2>
        </div>

        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="p-5">
            @php
                $input = 'w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500';
                $label = 'mb-1 block text-sm font-medium text-slate-700';
            @endphp

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-12">

                <div class="xl:col-span-4">
                    <label for="search" class="{{ $label }}">Search Activities</label>
                    <input id="search" type="search" name="search" class="{{ $input }}"
                           value="{{ $filters['search'] ?? '' }}" maxlength="150"
                           placeholder="Search user, description, or record ID...">
                </div>

                <div class="xl:col-span-2">
                    <label for="module" class="{{ $label }}">Module</label>
                    <select id="module" name="module" class="{{ $input }}">
                        <option value="">All modules</option>
                        @foreach ($modules as $module)
                            <option value="{{ $module }}" @selected(($filters['module'] ?? '') === $module)>
                                {{ $module }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="xl:col-span-2">
                    <label for="action" class="{{ $label }}">Action</label>
                    <select id="action" name="action" class="{{ $input }}">
                        <option value="">All actions</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(($filters['action'] ?? '') === $action)>
                                {{ \Illuminate\Support\Str::headline($action) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="xl:col-span-2">
                    <label for="role" class="{{ $label }}">User Role</label>
                    <select id="role" name="role" class="{{ $input }}">
                        <option value="">All roles</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(($filters['role'] ?? '') === $role)>
                                {{ $role }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="xl:col-span-1">
                    <label for="from" class="{{ $label }}">From</label>
                    <input id="from" type="date" name="from" class="{{ $input }}"
                           value="{{ $filters['from'] ?? '' }}" max="{{ $filters['to'] ?? '' }}">
                </div>

                <div class="xl:col-span-1">
                    <label for="to" class="{{ $label }}">To</label>
                    <input id="to" type="date" name="to" class="{{ $input }}"
                           value="{{ $filters['to'] ?? '' }}" min="{{ $filters['from'] ?? '' }}">
                </div>

                <div class="flex flex-wrap gap-2 pt-1 md:col-span-2 xl:col-span-12">
                    <button type="submit"
                            class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700">
                        <i class="fa-solid fa-magnifying-glass"></i> Apply Filters
                    </button>

                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-50">
                        <i class="fa-solid fa-rotate-left"></i> Clear Filters
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- Activity Table --}}
    <div class="rounded-xl bg-white shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-5 py-4">
            <div>
                <h2 class="text-sm font-semibold text-slate-800">Recorded Activities</h2>
                <p class="text-xs text-slate-500">
                    Review who performed an action and which record was affected.
                </p>
            </div>

            <span class="rounded-full border border-slate-200 bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-700">
                {{ number_format($activities->total()) }} matching records
            </span>
        </div>

        @if ($activities->isEmpty())

            <div class="px-4 py-12 text-center">
                <div class="mx-auto mb-3 flex h-16 w-16 items-center justify-center rounded-full bg-slate-100">
                    <i class="fa-solid fa-clock-rotate-left text-2xl text-slate-400"></i>
                </div>

                <h3 class="text-lg font-semibold text-slate-800">No activity records found</h3>

                <p class="mb-3 text-sm text-slate-500">
                    No records match the selected filters, or no activities have been recorded yet.
                </p>

                @if (collect($filters)->filter()->isNotEmpty())
                    <a href="{{ route('admin.audit-logs.index') }}"
                       class="inline-flex rounded-lg border border-blue-600 px-3 py-1.5 text-sm font-medium text-blue-700 hover:bg-blue-50">
                        Clear Filters
                    </a>
                @endif
            </div>

        @else

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                        <tr>
                            <th class="whitespace-nowrap px-4 py-3">Date and Time</th>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Module</th>
                            <th class="px-4 py-3">Action</th>
                            <th class="px-4 py-3">Description / Affected Record</th>
                            <th class="px-4 py-3 text-right">Details</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach ($activities as $activity)
                            <tr class="hover:bg-slate-50">
                                <td class="whitespace-nowrap px-4 py-3">
                                    <div class="font-semibold text-slate-800">
                                        {{ $activity->created_at?->format('M d, Y') ?? '—' }}
                                    </div>
                                    <div class="text-xs text-slate-500">
                                        {{ $activity->created_at?->format('h:i:s A') ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-800">
                                        {{ $activity->user_name ?: 'System / Guest' }}
                                    </div>
                                    @if ($activity->user_id)
                                        <div class="text-xs text-slate-500">User ID: {{ $activity->user_id }}</div>
                                    @endif
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-md border border-slate-200 bg-slate-50 px-2 py-1 text-xs text-slate-700">
                                        {{ $activity->user_role ?: 'Unknown' }}
                                    </span>
                                </td>

                                <td class="px-4 py-3 text-slate-700">
                                    {{ $activity->module ?: 'Unspecified' }}
                                </td>

                                <td class="px-4 py-3">
                                    <span class="rounded-md border border-blue-200 bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700">
                                        {{ \Illuminate\Support\Str::headline($activity->action ?: 'Unknown') }}
                                    </span>
                                </td>

                                <td class="min-w-[240px] max-w-[420px] px-4 py-3">
                                    <div class="break-words text-slate-700">
                                        {{ $activity->description ?: 'No description recorded.' }}
                                    </div>

                                    @if ($activity->subject_type || $activity->subject_id)
                                        <div class="mt-1 text-xs text-slate-500">
                                            <i class="fa-solid fa-link mr-1"></i>
                                            {{ $activity->subject_type ?: 'Affected record' }}
                                            @if ($activity->subject_id)
                                                #{{ $activity->subject_id }}
                                            @endif
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-3 text-right">
                                    <a href="{{ route('admin.audit-logs.show', $activity) }}"
                                       aria-label="View activity {{ $activity->id }}"
                                       class="inline-flex items-center gap-1 whitespace-nowrap rounded-lg border border-blue-600 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-50">
                                        <i class="fa-solid fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 p-4">
                <div class="text-xs text-slate-500">
                    Showing
                    <strong>{{ number_format($activities->firstItem() ?? 0) }}</strong>
                    to
                    <strong>{{ number_format($activities->lastItem() ?? 0) }}</strong>
                    of
                    <strong>{{ number_format($activities->total()) }}</strong>
                    records
                </div>

                <div>{{ $activities->links() }}</div>
            </div>

        @endif
    </div>

    <p class="text-xs text-slate-500">
        <i class="fa-solid fa-shield-halved mr-1"></i>
        Activity records are intended for accountability and review.
        Historical records should not be edited through this interface.
    </p>

</div>
@endsection