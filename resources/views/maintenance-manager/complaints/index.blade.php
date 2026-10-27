@extends('maintenance-manager.layouts.app')
@section('title', 'Complaint Management')
@section('content')
    @php
        $filterKeys = ['search', 'status', 'urgency', 'complaint_type_id', 'date_from', 'date_to'];
        $filters = request()->only($filterKeys);
        $hasFilters = collect($filters)->contains(fn($value) => filled($value));
        $activeStatus = request('status', '');
        $statusCards = [
            [
                'label' => 'Ready to Plan',
                'value' => $verifiedCount,
                'status' => 'For Assignment',
                'icon' => 'fa-calendar-plus',
                'tone' => 'sky',
                'hint' => 'Ready for scheduling',
            ],
            [
                'label' => 'Assigned',
                'value' => $assignedCount,
                'status' => 'Assigned',
                'icon' => 'fa-users-gear',
                'tone' => 'indigo',
                'hint' => 'Technicians assigned',
            ],
            [
                'label' => 'In Progress',
                'value' => $inProgressCount,
                'status' => 'In Progress',
                'icon' => 'fa-screwdriver-wrench',
                'tone' => 'amber',
                'hint' => 'Maintenance underway',
            ],
            [
                'label' => 'Accomplished',
                'value' => $completedCount,
                'status' => 'Completed',
                'icon' => 'fa-circle-check',
                'tone' => 'emerald',
                'hint' => 'Awaiting final closure',
            ],
            [
                'label' => 'Closed',
                'value' => $closedCount,
                'status' => 'Closed',
                'icon' => 'fa-box-archive',
                'tone' => 'slate',
                'hint' => 'Finalized complaints',
            ],
        ];
        $toneClasses = [
            'sky' => 'bg-sky-50 text-sky-700',
            'indigo' => 'bg-indigo-50 text-indigo-700',
            'amber' => 'bg-amber-50 text-amber-700',
            'emerald' => 'bg-emerald-50 text-emerald-700',
            'slate' => 'bg-slate-100 text-slate-700',
        ];
        $statusClasses = [
            'Verified' => 'bg-sky-50 text-sky-700 border-sky-200',
            'For Maintenance' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'In Progress' => 'bg-amber-50 text-amber-800 border-amber-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Closed' => 'bg-slate-100 text-slate-700 border-slate-200',
        ];
        $urgencyClasses = [
            'High' => 'bg-red-50 text-red-700 border-red-200',
            'Moderate' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Low' => 'bg-green-50 text-green-700 border-green-200',
        ];
    @endphp
    <div class="mx-auto max-w-7xl space-y-5 px-1 pb-8 sm:space-y-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">Maintenance Manager</p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Complaint Management</h1>
                <p class="mt-1 max-w-2xl text-sm text-slate-500">Monitor Engineering complaints and Commercial cases
                    officially forwarded for maintenance.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('maintenance-manager.schedules.index') }}#ready"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800"><i
                        class="fas fa-calendar-days"></i> Daily Planning</a>
                <a href="{{ route('maintenance-manager.complaints.index') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"><i
                        class="fas fa-rotate"></i> Refresh</a>
            </div>
        </div>
        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                {{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800"><strong>Check
                    your filters:</strong> {{ $errors->first() }}</div>
        @endif

        <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 xl:grid-cols-5">
            @foreach ($statusCards as $card)
                @php $selected = $activeStatus === $card['status']; @endphp
                <a href="{{ route('maintenance-manager.complaints.index', array_merge(collect($filters)->except('status')->all(), $selected ? [] : ['status' => $card['status']])) }}"
                    aria-label="Filter complaints by {{ $card['label'] }}"
                    class="group min-w-0 rounded-2xl border bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md sm:p-4 {{ $selected ? 'border-sky-500 ring-2 ring-sky-100' : 'border-slate-200' }}">
                    <div class="flex items-center justify-between gap-2">
                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl {{ $toneClasses[$card['tone']] }}"><i
                                class="fas {{ $card['icon'] }}"></i></span>
                        @if ($selected)
                            <span class="text-[10px] font-bold text-sky-700"><i class="fas fa-check-circle"></i>
                                Filtered</span>
                        @endif
                    </div>
                    <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">{{ number_format($card['value']) }}</p>
                    <p class="mt-1 text-xs font-bold text-slate-700 sm:text-sm">{{ $card['label'] }}</p>
                    <p class="mt-1 hidden text-xs text-slate-400 sm:block">{{ $card['hint'] }}</p>
                </a>
            @endforeach
        </div>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-labelledby="filter-heading">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-3 sm:px-5">
                <h2 id="filter-heading" class="font-bold text-slate-900"><i
                        class="fas fa-sliders mr-2 text-sky-600"></i>Filter Complaints</h2>
                @if ($hasFilters)
                    <span class="text-xs font-semibold text-sky-700">Filters applied</span>
                @endif
            </div>
            <form method="GET" action="{{ route('maintenance-manager.complaints.index') }}" class="p-4 sm:p-5">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
                    <div class="sm:col-span-2 xl:col-span-2">
                        <label for="search" class="mb-1 block text-xs font-semibold text-slate-600">Search</label>
                        <input id="search" name="search" maxlength="120" value="{{ request('search') }}"
                            placeholder="Complaint no., consumer, division, complaint type..."
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div><label for="status" class="mb-1 block text-xs font-semibold text-slate-600">Status</label>
                        <select id="status" name="status"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">All Statuses</option>
                            @foreach (['For Assignment' => 'For Assignment', 'Verified' => 'Verified', 'For Maintenance' => 'For Maintenance', 'Assigned' => 'Assigned', 'In Progress' => 'In Progress', 'Completed' => 'Accomplished', 'Closed' => 'Closed'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="urgency" class="mb-1 block text-xs font-semibold text-slate-600">Urgency</label>
                        <select id="urgency" name="urgency"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">All Urgency</option>
                            @foreach (['High' => 'High', 'Moderate' => 'Moderate', 'Low' => 'Low', 'not_assessed' => 'Not Assessed'] as $value => $label)
                                <option value="{{ $value }}" @selected(request('urgency') === $value)>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div><label for="date_from" class="mb-1 block text-xs font-semibold text-slate-600">Reported
                            From</label><input id="date_from" name="date_from" type="date"
                            value="{{ request('date_from') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div><label for="date_to" class="mb-1 block text-xs font-semibold text-slate-600">Reported
                            To</label><input id="date_to" name="date_to" type="date" min="{{ request('date_from') }}"
                            value="{{ request('date_to') }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                </div>
                <div class="mt-4 flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800"><i
                            class="fas fa-filter"></i> Apply Filters</button>
                    @if ($hasFilters)
                        <a href="{{ route('maintenance-manager.complaints.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i
                                class="fas fa-xmark"></i> Clear Filters</a>
                    @endif
                    <a href="{{ route('maintenance-manager.complaints.print-report', $filters) }}" target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:ml-auto"><i
                            class="fas fa-print text-sky-700"></i> Print Report</a>
                </div>
            </form>
        </section>
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-labelledby="results-heading">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-100 px-4 py-4 sm:px-5">
                <div>
                    <h2 id="results-heading" class="font-bold text-slate-900">Complaint Records</h2>
                    <p class="mt-1 text-xs text-slate-500">Engineering complaints and commercial cases forwarded to
                        Maintenance</p>
                </div>
                <span
                    class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">{{ number_format($complaints->total()) }}
                    matching</span>
            </div>
            @if ($complaints->count())
                <div class="hidden overflow-x-auto lg:block">
                    <table class="w-full min-w-[940px] text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-4 py-3 font-semibold">Complaint / Urgency</th>
                                <th class="px-4 py-3 font-semibold">Consumer</th>
                                <th class="px-4 py-3 font-semibold">Type / Division</th>
                                <th class="px-4 py-3 font-semibold">Status</th>
                                <th class="px-4 py-3 font-semibold">Assigned Technicians</th>
                                <th class="px-4 py-3 font-semibold">Updated</th>
                                <th class="px-4 py-3 text-right font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($complaints as $complaint)
                                @php
                                    $urgency =
                                        $complaint->aiAnalysis?->verified_urgency_level ?:
                                        $complaint->aiAnalysis?->urgency_level ?:
                                        null;
                                    $division = $complaint->division?->name ?? 'Unspecified division';
                                    $commercialOnly =
                                        str_contains(strtolower($division), 'commercial') &&
                                        !in_array(
                                            $complaint->status,
                                            ['For Maintenance', 'Assigned', 'In Progress', 'Completed'],
                                            true,
                                        ) &&
                                        $complaint->technicians->isEmpty();
                                    $detailUrl = route(
                                        'maintenance-manager.complaints.show',
                                        array_merge(
                                            ['complaint' => $complaint->id],
                                            request()->only(array_merge(['page'], $filterKeys)),
                                        ),
                                    );
                                @endphp
                                <tr class="transition hover:bg-sky-50/40">
                                    <td class="px-4 py-4 align-top"><a href="{{ $detailUrl }}"
                                            class="font-bold text-sky-700 hover:underline">{{ $complaint->complaint_no }}</a>
                                        <div class="mt-2"><span
                                                class="inline-flex rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $urgencyClasses[$urgency] ?? 'border-slate-200 bg-slate-50 text-slate-500' }}">{{ $urgency ?? 'Not Assessed' }}</span>
                                        </div>
                                        <p class="mt-2 text-xs text-slate-400">
                                            {{ $complaint->created_at?->format('M d, Y') }}</p>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-medium text-slate-800">
                                            {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                        </p>
                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $complaint->consumer?->account_number ? 'Account: ' . $complaint->consumer->account_number : 'Walk-in / Unlinked' }}
                                        </p>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        <p class="font-medium text-slate-800">
                                            {{ $complaint->category?->name ?? 'Uncategorized' }}</p>
                                        <p class="mt-1 text-xs text-slate-500">{{ $division }}</p>
                                    </td>
                                    <td class="px-4 py-4 align-top"><span
                                            class="inline-flex whitespace-nowrap rounded-full border px-2.5 py-1 text-xs font-semibold {{ $statusClasses[$complaint->status] ?? 'border-slate-200 bg-slate-50 text-slate-600' }}">{{ $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status }}</span>
                                    </td>
                                    <td class="px-4 py-4 align-top">
                                        @if ($commercialOnly)
                                            <span class="text-xs text-violet-700">Commercial Services</span>
                                        @elseif($complaint->technicians->isNotEmpty())
                                            <div class="space-y-1">
                                                @foreach ($complaint->technicians as $technician)
                                                    <p class="text-xs font-medium text-slate-700">
                                                        {{ $technician->full_name }}</p>
                                                @endforeach
                                            </div>
                                        @else<span class="text-xs text-slate-400">Not yet assigned</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-4 align-top text-xs text-slate-500">
                                        {{ $complaint->updated_at?->format('M d, Y') }}<p class="mt-1">
                                            {{ $complaint->updated_at?->format('h:i A') }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-right align-top"><a href="{{ $detailUrl }}"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-sky-200 bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100"><i
                                                class="fas fa-eye"></i> View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="grid gap-3 p-3 sm:grid-cols-2 lg:hidden sm:p-4">
                    @foreach ($complaints as $complaint)
                        @php
                            $urgency =
                                $complaint->aiAnalysis?->verified_urgency_level ?:
                                $complaint->aiAnalysis?->urgency_level ?:
                                null;
                            $division = $complaint->division?->name ?? 'Unspecified division';
                            $commercialOnly =
                                str_contains(strtolower($division), 'commercial') &&
                                !in_array(
                                    $complaint->status,
                                    ['For Maintenance', 'Assigned', 'In Progress', 'Completed'],
                                    true,
                                ) &&
                                $complaint->technicians->isEmpty();
                            $detailUrl = route(
                                'maintenance-manager.complaints.show',
                                array_merge(
                                    ['complaint' => $complaint->id],
                                    request()->only(array_merge(['page'], $filterKeys)),
                                ),
                            );
                        @endphp
                        <article class="min-w-0 rounded-xl border border-slate-200 p-4">
                            <div class="flex flex-wrap items-start justify-between gap-2"><a href="{{ $detailUrl }}"
                                    class="font-bold text-sky-700">{{ $complaint->complaint_no }}</a><span
                                    class="rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClasses[$complaint->status] ?? 'border-slate-200 bg-slate-50 text-slate-600' }}">{{ $complaint->status === 'Completed' ? 'Accomplished' : $complaint->status }}</span>
                            </div>
                            <div class="mt-2 flex flex-wrap gap-2"><span
                                    class="rounded-full border px-2 py-0.5 text-[10px] font-bold {{ $urgencyClasses[$urgency] ?? 'border-slate-200 bg-slate-50 text-slate-500' }}">Urgency:
                                    {{ $urgency ?? 'Not Assessed' }}</span><span
                                    class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] text-slate-600">{{ $division }}</span>
                            </div>
                            <dl class="mt-4 grid grid-cols-1 gap-3 text-sm">
                                <div>
                                    <dt class="text-xs font-semibold text-slate-400">Consumer</dt>
                                    <dd class="mt-0.5 font-medium text-slate-800">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown') }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-400">Complaint Type</dt>
                                    <dd class="mt-0.5 text-slate-700">{{ $complaint->category?->name ?? 'Uncategorized' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-xs font-semibold text-slate-400">Technicians</dt>
                                    <dd class="mt-0.5 text-slate-700">
                                        {{ $commercialOnly ? 'Commercial Services' : ($complaint->technicians->isEmpty() ? 'Not yet assigned' : $complaint->technicians->pluck('full_name')->implode(', ')) }}
                                    </dd>
                                </div>
                            </dl>
                            <div class="mt-4 flex items-center justify-between gap-2 border-t border-slate-100 pt-3"><span
                                    class="text-xs text-slate-400">Updated
                                    {{ $complaint->updated_at?->diffForHumans() }}</span><a href="{{ $detailUrl }}"
                                    class="inline-flex items-center gap-2 rounded-lg bg-sky-700 px-3 py-2 text-xs font-semibold text-white hover:bg-sky-800"><i
                                        class="fas fa-eye"></i> View Details</a></div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="px-5 py-14 text-center"><i class="fas fa-folder-open text-3xl text-slate-300"></i>
                    <h3 class="mt-3 font-semibold text-slate-800">No matching complaints</h3>
                    <p class="mt-1 text-sm text-slate-500">Try another complaint type, status, urgency, or date range.</p>
                    @if ($hasFilters)
                        <a href="{{ route('maintenance-manager.complaints.index') }}"
                            class="mt-4 inline-flex rounded-lg bg-sky-700 px-4 py-2 text-sm font-semibold text-white">Clear
                            Filters</a>
                    @endif
                </div>
            @endif
            @if ($complaints->hasPages())
                <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $complaints->links() }}</div>
            @endif
        </section>
    </div>
@endsection
