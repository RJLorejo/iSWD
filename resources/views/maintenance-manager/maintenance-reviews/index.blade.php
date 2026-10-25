@extends('maintenance-manager.layouts.app')

@section('title', 'Maintenance Reviews')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- Header --}}
        <div>

            <div class="flex items-center gap-2 text-sm font-medium text-sky-600">
                <i class="fas fa-clipboard-check"></i>
                Maintenance Management
            </div>

            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                Accomplishment Reviews
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Review plumber accomplishment reports before finalizing maintenance work.
            </p>

        </div>


        {{-- Success --}}
        @if (session('success'))
            <div
                class="rounded-xl border border-green-200
                       bg-green-50 px-4 py-3
                       text-sm text-green-800">
                <i class="fas fa-circle-check mr-2"></i>
                {{ session('success') }}
            </div>
        @endif


        {{-- Error --}}
        @if (session('error'))
            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800">
                <i class="fas fa-circle-exclamation mr-2"></i>
                {{ session('error') }}
            </div>
        @endif


        {{-- Clickable review status filter cards; preserve search and date range. --}}
        @php
            $selectedReviewStatus = request('status', 'Pending Review');
            $reviewCards = [
                [
                    'label' => 'All Reports',
                    'status' => 'All',
                    'count' => $pendingCount + $returnedCount + $approvedCount,
                    'icon' => 'fa-layer-group',
                    'color' => 'sky',
                    'description' => 'Every submitted report',
                ],
                [
                    'label' => 'Pending Review',
                    'status' => 'Pending Review',
                    'count' => $pendingCount,
                    'icon' => 'fa-clock',
                    'color' => 'amber',
                    'description' => 'Awaiting decision',
                ],
                [
                    'label' => 'Returned',
                    'status' => 'Returned',
                    'count' => $returnedCount,
                    'icon' => 'fa-rotate-left',
                    'color' => 'rose',
                    'description' => 'For correction',
                ],
                [
                    'label' => 'Approved',
                    'status' => 'Approved',
                    'count' => $approvedCount,
                    'icon' => 'fa-circle-check',
                    'color' => 'emerald',
                    'description' => 'Reviewed and approved',
                ],
            ];
        @endphp

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            @foreach ($reviewCards as $card)
                @php
                    $selected = $selectedReviewStatus === $card['status'];
                    $cardUrl = route(
                        'maintenance-manager.maintenance-reviews.index',
                        array_merge(request()->except(['status', 'page']), ['status' => $card['status']]),
                    );
                    $activeClass = match ($card['color']) {
                        'amber' => 'border-amber-400 bg-amber-50 ring-2 ring-amber-100',
                        'rose' => 'border-rose-400 bg-rose-50 ring-2 ring-rose-100',
                        'emerald' => 'border-emerald-400 bg-emerald-50 ring-2 ring-emerald-100',
                        default => 'border-sky-400 bg-sky-50 ring-2 ring-sky-100',
                    };
                    $numberClass = match ($card['color']) {
                        'amber' => 'text-amber-700',
                        'rose' => 'text-rose-700',
                        'emerald' => 'text-emerald-700',
                        default => 'text-sky-700',
                    };
                @endphp
                <a href="{{ $cardUrl }}" aria-current="{{ $selected ? 'page' : 'false' }}"
                    class="group flex min-w-0 flex-col justify-between gap-3 rounded-2xl border p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-sky-500 sm:p-5 {{ $selected ? $activeClass : 'border-slate-200 bg-white hover:border-sky-200' }}">
                    <div class="flex items-start justify-between gap-2">
                        <span class="text-xs font-semibold leading-5 text-slate-600 sm:text-sm">{{ $card['label'] }}</span>
                        <i class="fas {{ $card['icon'] }} text-slate-400" aria-hidden="true"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-extrabold tabular-nums sm:text-3xl {{ $numberClass }}">
                            {{ number_format($card['count']) }}</p>
                        <p class="mt-1 hidden text-xs text-slate-500 sm:block">{{ $card['description'] }}</p>
                    </div>
                    @if ($selected)
                        <span class="text-[11px] font-semibold text-slate-600"><i class="fas fa-check mr-1"></i>Current
                            filter</span>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Filters --}}
        <div class="bg-white rounded-2xl
                   border border-slate-200 shadow-sm">

            <form method="GET" action="{{ route('maintenance-manager.maintenance-reviews.index') }}" class="p-4 sm:p-5">

                <div
                    class="grid grid-cols-1
                           sm:grid-cols-2
                           lg:grid-cols-[minmax(260px,1.7fr)_minmax(150px,0.8fr)_minmax(150px,0.8fr)_auto]
                           gap-3">

                    <div>

                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            Search
                        </label>

                        <div class="relative">

                            <i
                                class="fas fa-search
                                       absolute left-3.5 top-1/2
                                       -translate-y-1/2
                                       text-slate-400 text-sm"></i>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Complaint no., type, consumer..."
                                class="w-full rounded-xl
                                       border-slate-300
                                       pl-10 text-sm
                                       focus:border-sky-500
                                       focus:ring-sky-500">

                        </div>

                    </div>


                    <input type="hidden" name="status" value="{{ $selectedReviewStatus }}">

                    <div>

                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            From
                        </label>

                        <input type="date" name="from" value="{{ request('from') }}"
                            class="w-full rounded-xl
                                   border-slate-300 text-sm
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>


                    <div>

                        <label class="block text-xs font-semibold text-slate-500 mb-1.5">
                            To
                        </label>

                        <input type="date" name="to" value="{{ request('to') }}"
                            class="w-full rounded-xl
                                   border-slate-300 text-sm
                                   focus:border-sky-500
                                   focus:ring-sky-500">

                    </div>


                    <div class="flex items-end gap-2">

                        <button type="submit"
                            class="inline-flex items-center justify-center
                                   gap-2 px-4 py-2.5 rounded-xl
                                   bg-sky-700 text-white
                                   text-sm font-semibold
                                   hover:bg-sky-800 transition">
                            <i class="fas fa-filter"></i>
                            Apply
                        </button>

                        <a href="{{ route('maintenance-manager.maintenance-reviews.index') }}" title="Reset Filters"
                            class="inline-flex items-center justify-center
                                   w-10 h-10 rounded-xl
                                   bg-slate-100 text-slate-600
                                   hover:bg-slate-200 transition">
                            <i class="fas fa-rotate-left"></i>
                        </a>

                    </div>

                </div>

            </form>

        </div>



        {{-- Print every matching report, not only the current page --}}
        <div
            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
            <div>
                <p class="text-sm font-bold text-slate-900">Maintenance Accomplishment Register</p>
                <p class="mt-1 text-xs text-slate-500">Print all submitted reports matching the current search, status, and
                    date filters.</p>
            </div>
            <a href="{{ route('maintenance-manager.maintenance-reviews.print', request()->only(['search', 'status', 'from', 'to'])) }}"
                target="_blank" rel="noopener noreferrer"
                class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 sm:w-auto">
                <i class="fas fa-print"></i> Print Report
            </a>
        </div>

        {{-- Reports --}}
        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm overflow-hidden">

            <div
                class="px-5 sm:px-6 py-5
                       border-b border-indigo-100
                       bg-indigo-50/40">

                <div class="flex items-start gap-3">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-indigo-100 text-indigo-700
                               flex items-center justify-center shrink-0">
                        <i class="fas fa-file-circle-check"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Accomplishment Reports
                        </h2>

                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Submitted maintenance reports requiring review or already reviewed.
                        </p>

                    </div>

                </div>

            </div>


            {{-- Desktop --}}
            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full text-sm">

                    <thead class="bg-slate-50 border-b border-slate-200">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Consumer
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Complaint Type
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Report Submitted By
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Submitted
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Review Status
                            </th>

                            <th class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($reports as $report)
                            @php
                                $complaint = $report->complaint;

                                $urgency = strtoupper(
                                    trim(
                                        $complaint?->aiAnalysis?->verified_urgency_level ?:
                                        $complaint?->aiAnalysis?->urgency_level ?:
                                        '',
                                    ),
                                );

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',

                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',

                                    default => 'bg-slate-50 text-slate-500 border-slate-200',
                                };

                                $reviewClasses = match ($report->review_status) {
                                    'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',

                                    'Returned' => 'bg-red-50 text-red-700 border-red-200',

                                    'Approved' => 'bg-green-50 text-green-700 border-green-200',

                                    default => 'bg-slate-50 text-slate-600 border-slate-200',
                                };
                            @endphp


                            <tr class="hover:bg-slate-50/70 transition">

                                <td class="px-5 py-4">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <span class="font-bold text-sky-700">
                                            {{ $complaint?->complaint_no ?? '—' }}
                                        </span>

                                        @if ($urgency)
                                            <span
                                                class="inline-flex items-center gap-1
                                                       px-2 py-0.5 rounded-full
                                                       border text-[10px] font-bold
                                                       {{ $urgencyClasses }}">
                                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                                {{ $urgency }}
                                            </span>
                                        @endif

                                    </div>

                                </td>


                                <td class="px-5 py-4">

                                    <p class="font-medium text-slate-900">

                                        {{ $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown') }}

                                    </p>

                                    @if ($complaint?->consumer?->account_number)
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $complaint->consumer->account_number }}
                                        </p>
                                    @endif

                                </td>


                                <td class="px-5 py-4">

                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}

                                </td>


                                <td class="px-5 py-4">

                                    <p class="font-medium text-slate-800">
                                        {{ $report->technician?->full_name ?? '—' }}
                                    </p>

                                    @if ($report->technician?->employee_id)
                                        <p class="text-xs text-slate-500 mt-1">
                                            {{ $report->technician->employee_id }}
                                        </p>
                                    @endif

                                </td>


                                <td class="px-5 py-4 text-slate-600">

                                    {{ $report->submitted_at?->format('M d, Y h:i A') ?? '—' }}

                                </td>


                                <td class="px-5 py-4">

                                    <span
                                        class="inline-flex items-center gap-1.5
                                               px-2.5 py-1.5 rounded-full
                                               border text-xs font-semibold
                                               {{ $reviewClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full bg-current"></span>

                                        {{ $report->review_status }}
                                    </span>

                                </td>


                                <td class="px-5 py-4 text-center">

                                    <a href="{{ route('maintenance-manager.maintenance-reviews.show', $report) }}"
                                        title="View Accomplishment Report"
                                        class="inline-flex items-center justify-center
                                               w-9 h-9 rounded-lg
                                               bg-sky-50 text-sky-600
                                               border border-sky-100
                                               hover:bg-sky-700
                                               hover:text-white
                                               hover:border-sky-700
                                               transition">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-14 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto rounded-2xl
                                               bg-slate-100 text-slate-400
                                               flex items-center justify-center">
                                        <i class="fas fa-clipboard-check text-xl"></i>
                                    </div>

                                    <p class="font-semibold text-slate-900 mt-3">
                                        No accomplishment reports found
                                    </p>

                                    <p class="text-sm text-slate-500 mt-1">
                                        No reports match the selected filters.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile --}}
            <div class="lg:hidden divide-y divide-slate-100">

                @forelse ($reports as $report)
                    @php
                        $complaint = $report->complaint;

                        $urgency = strtoupper(
                            trim(
                                $complaint?->aiAnalysis?->verified_urgency_level ?:
                                $complaint?->aiAnalysis?->urgency_level ?:
                                '',
                            ),
                        );

                        $urgencyClasses = match ($urgency) {
                            'HIGH' => 'bg-red-50 text-red-700 border-red-200',

                            'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'LOW' => 'bg-green-50 text-green-700 border-green-200',

                            default => 'bg-slate-50 text-slate-500 border-slate-200',
                        };

                        $reviewClasses = match ($report->review_status) {
                            'Pending Review' => 'bg-amber-50 text-amber-700 border-amber-200',

                            'Returned' => 'bg-red-50 text-red-700 border-red-200',

                            'Approved' => 'bg-green-50 text-green-700 border-green-200',

                            default => 'bg-slate-50 text-slate-600 border-slate-200',
                        };
                    @endphp


                    <div class="p-4">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <p class="font-bold text-sky-700">
                                        {{ $complaint?->complaint_no ?? '—' }}
                                    </p>

                                    @if ($urgency)
                                        <span
                                            class="inline-flex items-center gap-1
                                                   px-2 py-0.5 rounded-full
                                                   border text-[10px] font-bold
                                                   {{ $urgencyClasses }}">
                                            <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                            {{ $urgency }}
                                        </span>
                                    @endif

                                </div>

                                <p class="text-sm font-medium text-slate-800 mt-2">
                                    {{ $complaint?->category?->name ?? 'Uncategorized' }}
                                </p>

                                <p class="text-xs text-slate-500 mt-1">
                                    {{ $complaint?->consumer?->full_name ?? ($complaint?->complainant_name ?? 'Unknown') }}
                                </p>

                            </div>


                            <span
                                class="inline-flex px-2.5 py-1
                                       rounded-full border
                                       text-[10px] font-semibold
                                       shrink-0
                                       {{ $reviewClasses }}">
                                {{ $report->review_status }}
                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4 mt-4">

                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400">
                                    Report Submitted By
                                </p>

                                <p class="text-sm font-medium text-slate-700 mt-1">
                                    {{ $report->technician?->full_name ?? '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase tracking-wide font-semibold text-slate-400">
                                    Submitted
                                </p>

                                <p class="text-sm text-slate-700 mt-1">
                                    {{ $report->submitted_at?->format('M d, Y') ?? '—' }}
                                </p>

                            </div>

                        </div>


                        <div class="flex justify-end mt-4">

                            <a href="{{ route('maintenance-manager.maintenance-reviews.show', $report) }}"
                                title="View Accomplishment Report"
                                class="inline-flex items-center justify-center
                                       w-9 h-9 rounded-lg
                                       bg-sky-50 text-sky-600
                                       border border-sky-100
                                       hover:bg-sky-700
                                       hover:text-white transition">
                                <i class="fas fa-eye"></i>
                            </a>

                        </div>

                    </div>

                @empty

                    <div class="p-10 text-center text-slate-500">
                        No accomplishment reports found.
                    </div>
                @endforelse

            </div>


            @if ($reports->hasPages())
                <div class="p-5 border-t border-slate-100">
                    {{ $reports->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection
