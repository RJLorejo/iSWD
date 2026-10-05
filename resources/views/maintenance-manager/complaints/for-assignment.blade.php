@extends('maintenance-manager.layouts.app')

@section('title', 'For Assignment')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900 sm:text-3xl">
                    For Assignment
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Engineering complaints and service requests ready for maintenance assignment.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-gray-200
                    bg-white px-4 py-2.5 text-sm font-semibold text-gray-700
                    transition hover:bg-gray-50">

                    <i class="fas fa-list"></i>
                    All Complaints

                </a>

                <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                    class="inline-flex items-center gap-2 rounded-xl bg-blue-600
                    px-4 py-2.5 text-sm font-semibold text-white
                    transition hover:bg-blue-700">

                    <i class="fas fa-rotate"></i>
                    Refresh

                </a>

            </div>

        </div>


        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Waiting
                </p>

                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ $totalForAssignment ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Ready for assignment
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    High
                </p>

                <p class="mt-1 text-2xl font-bold text-red-600">
                    {{ $highUrgencyCount ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    High urgency
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Moderate
                </p>

                <p class="mt-1 text-2xl font-bold text-amber-600">
                    {{ $moderateUrgencyCount ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Moderate urgency
                </p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Low
                </p>

                <p class="mt-1 text-2xl font-bold text-green-600">
                    {{ $lowUrgencyCount ?? 0 }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Low urgency
                </p>
            </div>

        </div>


        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm">

            <form method="GET" action="{{ route('maintenance-manager.complaints.for-assignment') }}" class="p-4 sm:p-5">

                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 lg:grid-cols-5">

                    <div class="relative lg:col-span-2">

                        <i
                            class="fas fa-magnifying-glass absolute left-3 top-1/2
                            -translate-y-1/2 text-sm text-gray-400"></i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint, consumer, type..."
                            class="w-full rounded-xl border border-gray-200 py-2.5
                            pl-10 pr-4 text-sm focus:border-blue-500 focus:ring-blue-500">

                    </div>

                    <select name="urgency"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5
                        text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">

                        <option value="">All Urgency</option>

                        <option value="High" @selected(request('urgency') === 'High')>
                            High
                        </option>

                        <option value="Moderate" @selected(request('urgency') === 'Moderate')>
                            Moderate
                        </option>

                        <option value="Low" @selected(request('urgency') === 'Low')>
                            Low
                        </option>

                        <option value="not_assessed" @selected(request('urgency') === 'not_assessed')>
                            Not Assessed
                        </option>

                    </select>

                    <input type="date" name="date_from" value="{{ request('date_from') }}" title="From date"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5
                        text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">

                    <input type="date" name="date_to" value="{{ request('date_to') }}" title="To date"
                        class="w-full rounded-xl border border-gray-200 px-3 py-2.5
                        text-sm text-gray-700 focus:border-blue-500 focus:ring-blue-500">

                </div>

                <div class="mt-3 flex flex-wrap gap-2">

                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl
                        bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white
                        transition hover:bg-blue-700">

                        <i class="fas fa-filter"></i>
                        Apply Filters

                    </button>

                    @if (request()->hasAny(['search', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                            class="inline-flex items-center justify-center rounded-xl
                            bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700
                            transition hover:bg-gray-200">

                            Clear

                        </a>
                    @endif

                </div>

            </form>

        </div>


        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4
                sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="font-bold text-gray-900">
                        Complaints
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        Engineering complaints and service requests ready for maintenance assignment
                    </p>
                </div>

                <span class="text-sm text-gray-500">
                    {{ $complaints->total() }} total
                </span>

            </div>

            @if ($complaints->count())

                <div class="hidden overflow-x-auto lg:block">

                    <table class="min-w-full">

                        <thead class="border-b border-gray-100 bg-gray-50">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Complaint
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Consumer
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Complaint Type
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Plumber Assignment
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Updated
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-semibold uppercase
                                    tracking-wider text-gray-500">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-gray-100">

                            @foreach ($complaints as $complaint)
                                @php
                                    $urgency = $complaint->aiAnalysis?->urgency_level;

                                    $urgencyClasses = match ($urgency) {
                                        'High' => 'bg-red-50 text-red-700 border-red-200',
                                        'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'Low' => 'bg-green-50 text-green-700 border-green-200',
                                        default => 'bg-gray-50 text-gray-500 border-gray-200',
                                    };

                                    $divisionName = $complaint->division?->name ?? '—';
                                    $normalizedDivision = strtolower(trim($divisionName));
                                    $isCommercial = str_contains($normalizedDivision, 'commercial');
                                    $isForwarded = $complaint->status === 'For Maintenance';

                                    $statusClasses = match ($complaint->status) {
                                        'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',
                                        'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',
                                        default => 'bg-gray-50 text-gray-600 border-gray-100',
                                    };
                                @endphp

                                <tr class="transition hover:bg-gray-50/70">

                                    <td class="px-5 py-4">

                                        <div class="flex items-start gap-3">

                                            <div
                                                class="flex h-10 w-10 shrink-0 items-center
                                                justify-center rounded-xl bg-blue-50 text-blue-600">

                                                <i class="fas fa-file-lines"></i>

                                            </div>

                                            <div class="min-w-0">

                                                <div class="flex flex-wrap items-center gap-2">

                                                    <p class="text-sm font-bold text-blue-600">
                                                        {{ $complaint->complaint_no }}
                                                    </p>

                                                    @if ($urgency)
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-full border
                                                            px-2 py-0.5 text-[10px] font-bold {{ $urgencyClasses }}">

                                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                                            {{ strtoupper($urgency) }}

                                                        </span>
                                                    @else
                                                        <span
                                                            class="inline-flex items-center rounded-full border
                                                            border-gray-200 bg-gray-50 px-2 py-0.5
                                                            text-[10px] font-semibold text-gray-400">

                                                            NOT ASSESSED

                                                        </span>
                                                    @endif

                                                </div>

                                                <p class="mt-1 text-xs text-gray-400">
                                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-5 py-4">

                                        <p class="font-medium text-gray-900">
                                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}
                                        </p>

                                        @if ($complaint->consumer?->account_number)
                                            <p class="mt-0.5 text-xs text-gray-500">
                                                Account: {{ $complaint->consumer->account_number }}
                                            </p>
                                        @endif

                                        @if ($complaint->complainant_phone)
                                            <p class="mt-0.5 text-xs text-gray-400">
                                                {{ $complaint->complainant_phone }}
                                            </p>
                                        @endif

                                    </td>

                                    <td class="px-5 py-4">

                                        <div class="space-y-1.5">

                                            <span
                                                class="inline-flex items-center rounded-lg bg-gray-100
                                                px-2.5 py-1 text-xs font-medium text-gray-700">

                                                {{ $complaint->category?->name ?? 'Uncategorized' }}

                                            </span>

                                            <div>

                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-lg
                                                    px-2.5 py-1 text-xs font-medium
                                                    {{ $isCommercial ? 'bg-violet-50 text-violet-700' : 'bg-blue-50 text-blue-700' }}">

                                                    <i
                                                        class="fas {{ $isCommercial ? 'fa-headset' : 'fa-screwdriver-wrench' }} text-[10px]"></i>

                                                    {{ $divisionName }}

                                                </span>

                                            </div>

                                        </div>

                                    </td>

                                    <td class="px-5 py-4">

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full border
                                            px-2.5 py-1.5 text-xs font-semibold {{ $statusClasses }}">

                                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                            {{ $complaint->status }}

                                        </span>

                                    </td>

                                    <td class="px-5 py-4">

                                        <div class="flex items-center gap-2">

                                            <div
                                                class="flex h-8 w-8 shrink-0 items-center justify-center
                                                rounded-lg bg-gray-100 text-gray-400">

                                                <i class="fas fa-user-clock text-xs"></i>

                                            </div>

                                            <div>

                                                <p class="text-sm font-medium text-gray-500">
                                                    Not Assigned
                                                </p>

                                                <p class="text-[10px] text-gray-400">
                                                    Awaiting plumber assignment
                                                </p>

                                            </div>

                                        </div>

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        <p class="text-sm text-gray-700">
                                            {{ $complaint->updated_at?->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-400">
                                            {{ $complaint->updated_at?->format('h:i A') }}
                                        </p>

                                    </td>

                                    <td class="px-5 py-4 text-center">

                                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                            title="View Complaint" aria-label="View Complaint"
                                            class="inline-flex h-9 w-9 items-center justify-center
                                            rounded-lg border border-blue-100 bg-blue-50 text-blue-600
                                            transition hover:border-blue-600 hover:bg-blue-600 hover:text-white">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                    </td>

                                </tr>
                            @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="divide-y divide-gray-100 lg:hidden">

                    @foreach ($complaints as $complaint)
                        @php
                            $urgency = $complaint->aiAnalysis?->urgency_level;

                            $urgencyClasses = match ($urgency) {
                                'High' => 'bg-red-50 text-red-700 border-red-200',
                                'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',
                                'Low' => 'bg-green-50 text-green-700 border-green-200',
                                default => 'bg-gray-50 text-gray-500 border-gray-200',
                            };

                            $divisionName = $complaint->division?->name ?? '—';
                            $normalizedDivision = strtolower(trim($divisionName));
                            $isCommercial = str_contains($normalizedDivision, 'commercial');

                            $statusClasses = match ($complaint->status) {
                                'Verified' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-100',
                                default => 'bg-gray-50 text-gray-600 border-gray-100',
                            };
                        @endphp

                        <div class="p-5">

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <p class="text-sm font-bold text-blue-600">
                                            {{ $complaint->complaint_no }}
                                        </p>

                                        @if ($urgency)
                                            <span
                                                class="inline-flex items-center gap-1 rounded-full border
                                                px-2 py-0.5 text-[10px] font-bold {{ $urgencyClasses }}">

                                                <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                                {{ strtoupper($urgency) }}

                                            </span>
                                        @endif

                                    </div>

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $complaint->created_at?->format('M d, Y h:i A') }}
                                    </p>

                                </div>

                                <span
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-full border
                                    px-2.5 py-1 text-[11px] font-semibold {{ $statusClasses }}">

                                    <span class="h-1.5 w-1.5 rounded-full bg-current"></span>

                                    {{ $complaint->status }}

                                </span>

                            </div>


                            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                        Consumer
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-gray-800">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? '—') }}
                                    </p>

                                    @if ($complaint->consumer?->account_number)
                                        <p class="text-xs text-gray-500">
                                            {{ $complaint->consumer->account_number }}
                                        </p>
                                    @endif

                                </div>

                                <div>

                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                        Complaint Type
                                    </p>

                                    <p class="mt-1 text-sm font-medium text-gray-700">
                                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                                    </p>

                                    <p
                                        class="mt-1 text-xs font-semibold
                                        {{ $isCommercial ? 'text-violet-600' : 'text-blue-600' }}">

                                        {{ $divisionName }}

                                    </p>

                                </div>

                            </div>


                            <div class="mt-4 rounded-xl border border-gray-100 bg-gray-50 p-3">

                                <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-400">
                                    Plumber Assignment
                                </p>

                                <div class="mt-2 flex items-center gap-2">

                                    <div
                                        class="flex h-8 w-8 items-center justify-center
                                        rounded-lg bg-gray-100 text-gray-400">

                                        <i class="fas fa-user-clock text-xs"></i>

                                    </div>

                                    <div>

                                        <p class="text-sm font-medium text-gray-500">
                                            Not Assigned
                                        </p>

                                        <p class="text-[10px] text-gray-400">
                                            Awaiting plumber assignment
                                        </p>

                                    </div>

                                </div>

                            </div>


                            <div class="mt-4 flex items-center justify-between">

                                <span class="text-xs text-gray-400">
                                    Updated {{ $complaint->updated_at?->diffForHumans() }}
                                </span>

                                <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                    title="View Complaint" aria-label="View Complaint"
                                    class="inline-flex h-9 w-9 items-center justify-center
                                    rounded-lg border border-blue-100 bg-blue-50 text-blue-600
                                    transition hover:border-blue-600 hover:bg-blue-600 hover:text-white">

                                    <i class="fas fa-eye"></i>

                                </a>

                            </div>

                        </div>
                    @endforeach

                </div>
            @else
                <div class="px-6 py-16 text-center">

                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center
                        rounded-2xl bg-gray-100">

                        <i class="fas fa-file-circle-xmark text-2xl text-gray-400"></i>

                    </div>

                    <h3 class="mt-4 text-base font-semibold text-gray-900">
                        No complaints found
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Engineering complaints and service requests ready for assignment will appear here.
                    </p>

                    @if (request()->hasAny(['search', 'urgency', 'date_from', 'date_to']))
                        <a href="{{ route('maintenance-manager.complaints.for-assignment') }}"
                            class="mt-4 inline-flex items-center gap-2 rounded-xl
                            bg-gray-100 px-4 py-2.5 text-sm font-semibold text-gray-700
                            transition hover:bg-gray-200">

                            <i class="fas fa-rotate-left"></i>
                            Clear Filters

                        </a>
                    @endif

                </div>

            @endif

            @if ($complaints->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $complaints->withQueryString()->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection
