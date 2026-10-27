@extends('customer-service.layouts.app')

@section('title', 'Pending Final Closure')

@section('content')
@php
    $activeReviewStatus = request('review_status', 'all');
    $reviewFilters = [
        'all' => 'All Pending',
        'ready' => 'Ready to Close',
        'pending' => 'Awaiting Review',
        'returned' => 'Returned',
    ];
    $hasFilters = request()->filled('search')
        || ($activeReviewStatus !== 'all' && $activeReviewStatus !== '')
        || request()->filled('date_from')
        || request()->filled('date_to');
    $totalMatching = $complaints->total();
@endphp

<div class="mx-auto w-full max-w-7xl space-y-6">
    @if (session('success'))
        <div role="alert" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-4 text-emerald-900 shadow-sm sm:px-5">
            <i class="fas fa-circle-check mt-0.5 text-lg text-emerald-600" aria-hidden="true"></i>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold">Final Closure Successful</p>
                <p class="mt-1 break-words text-sm">{{ session('success') }}</p>
            </div>
            <button type="button" onclick="this.closest('[role=alert]').remove()" class="rounded-lg p-1 text-emerald-700 transition hover:bg-emerald-100" aria-label="Dismiss notification">
                <i class="fas fa-xmark" aria-hidden="true"></i>
            </button>
        </div>
    @endif

    {{-- Page header --}}
    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex min-w-0 items-start gap-3">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-sky-100 text-sky-700">
                <i class="fas fa-clipboard-check text-xl" aria-hidden="true"></i>
            </div>
            <div class="min-w-0">
                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">Pending Final Closure</h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">Track accomplished maintenance complaints, review approved reports, and finalize official closure.</p>
            </div>
        </div>
        <a href="{{ route('customer-service.complaints.index') }}" class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
            <i class="fas fa-arrow-left" aria-hidden="true"></i> All Complaints
        </a>
    </section>

    {{-- Summary cards --}}
    <section aria-label="Final closure summary" class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-slate-500">Pending Closure</p><p class="mt-2 text-3xl font-bold tabular-nums text-slate-900">{{ number_format($pendingClosureCount) }}</p></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700"><i class="fas fa-folder-open text-lg" aria-hidden="true"></i></span>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Accomplished but not officially closed</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-slate-500">Ready to Close</p><p class="mt-2 text-3xl font-bold tabular-nums text-emerald-700">{{ number_format($readyToCloseCount) }}</p></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-700"><i class="fas fa-circle-check text-lg" aria-hidden="true"></i></span>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Reports approved by the Manager</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-slate-500">Awaiting Review</p><p class="mt-2 text-3xl font-bold tabular-nums text-amber-700">{{ number_format($awaitingReviewCount) }}</p></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-100 text-amber-700"><i class="fas fa-clock text-lg" aria-hidden="true"></i></span>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Reports awaiting Manager approval</p>
        </article>
        <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-start justify-between gap-3">
                <div><p class="text-sm font-medium text-slate-500">Returned</p><p class="mt-2 text-3xl font-bold tabular-nums text-rose-700">{{ number_format($returnedCount) }}</p></div>
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-rose-100 text-rose-700"><i class="fas fa-rotate-left text-lg" aria-hidden="true"></i></span>
            </div>
            <p class="mt-3 text-xs leading-5 text-slate-500">Reports returned for correction</p>
        </article>
    </section>

    {{-- Search and filters --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
            <div class="flex items-start gap-3">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600"><i class="fas fa-magnifying-glass" aria-hidden="true"></i></span>
                <div>
                    <h2 class="text-base font-bold text-slate-900">Find Complaints</h2>
                    <p class="mt-1 text-xs leading-5 text-slate-500">Search by complaint number, complainant, description, or consumer account, then narrow the results.</p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('customer-service.final-closures.index') }}" class="p-4 sm:p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-[2fr_1.2fr_1fr_1fr] xl:items-end">
                <div class="min-w-0">
                    <label for="search" class="mb-1.5 block text-xs font-semibold text-slate-600">Search</label>
                    <div class="relative">
                        <i class="fas fa-magnifying-glass pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400" aria-hidden="true"></i>
                        <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Complaint no., name, account..." class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-3 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                    </div>
                </div>
                <div class="min-w-0">
                    <label for="review_status" class="mb-1.5 block text-xs font-semibold text-slate-600">Review Status</label>
                    <select id="review_status" name="review_status" class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        @foreach ($reviewFilters as $value => $label)
                            <option value="{{ $value }}" @selected($activeReviewStatus === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="min-w-0">
                    <label for="date_from" class="mb-1.5 block text-xs font-semibold text-slate-600">Accomplished From</label>
                    <input type="date" id="date_from" name="date_from" value="{{ request('date_from') }}" class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>
                <div class="min-w-0">
                    <label for="date_to" class="mb-1.5 block text-xs font-semibold text-slate-600">Accomplished To</label>
                    <input type="date" id="date_to" name="date_to" value="{{ request('date_to') }}" class="block w-full min-w-0 rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-800 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                </div>
            </div>

            @error('date_to')
                <p class="mt-2 text-xs font-medium text-rose-600">{{ $message }}</p>
            @enderror

            {{-- Filter actions: Reset appears only when filters are active --}}
            <div class="mt-5 flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:flex-wrap sm:items-center">
                <div class="flex flex-wrap items-center gap-2">
                    <button type="submit" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i class="fas fa-filter" aria-hidden="true"></i> Apply Filters
                    </button>
                    @if ($hasFilters)
                        <a href="{{ route('customer-service.final-closures.index') }}" aria-label="Reset all filters" title="Reset all filters" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm font-semibold text-slate-600 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600 focus:outline-none focus:ring-2 focus:ring-slate-300">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i><span class="sr-only">Reset filters</span>
                        </a>
                    @endif
                    <a href="{{ route('customer-service.final-closures.print', request()->query()) }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-sky-200 hover:bg-sky-50 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i class="fas fa-print text-sky-600" aria-hidden="true"></i> Print Report
                    </a>
                </div>
                <p class="text-xs text-slate-500 sm:ml-auto"><span class="font-semibold text-slate-700">{{ number_format($totalMatching) }}</span> matching complaint(s)</p>
            </div>
        </form>
    </section>

    {{-- Complaint list --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h2 class="text-base font-bold text-slate-900">Accomplished Maintenance Complaints</h2>
                <p class="mt-1 text-xs leading-5 text-slate-500">Manager-approved reports are ready for official Customer Service closure.</p>
            </div>
            <span class="inline-flex w-fit shrink-0 items-center rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">{{ number_format($totalMatching) }} Records</span>
        </div>

        {{-- Mobile cards --}}
        <div class="grid grid-cols-1 gap-3 p-4 md:hidden">
            @forelse ($complaints as $complaint)
                @php
                    $report = $complaint->maintenanceReport;
                    $reportStatus = $report?->review_status ?? 'Draft';
                    $isReady = $report?->submitted_at !== null && $reportStatus === 'Approved';
                    $statusLabel = match (true) {
                        $isReady => 'Ready to Close',
                        $reportStatus === 'Returned' => 'Returned',
                        default => 'Awaiting Review',
                    };
                    $statusClasses = match (true) {
                        $isReady => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                        $reportStatus === 'Returned' => 'bg-rose-50 text-rose-700 ring-rose-200',
                        default => 'bg-amber-50 text-amber-700 ring-amber-200',
                    };
                @endphp
                <article class="min-w-0 rounded-xl border border-slate-200 p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="break-words text-sm font-bold text-slate-900">{{ $complaint->complaint_no }}</p>
                            <p class="mt-1 break-words text-xs leading-5 text-slate-500">{{ $complaint->description ?: 'No description' }}</p>
                        </div>
                        <span class="inline-flex shrink-0 items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $statusClasses }}">{{ $statusLabel }}</span>
                    </div>
                    <dl class="mt-4 grid grid-cols-1 gap-3 border-t border-slate-100 pt-3 sm:grid-cols-2">
                        <div class="min-w-0">
                            <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Complainant</dt>
                            <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $complaint->complainant_name ?: ($complaint->consumer?->full_name ?? 'N/A') }}</dd>
                            <dd class="mt-1 break-words text-xs text-slate-500">{{ $complaint->consumer?->account_number ?? 'Walk-in / No linked account' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Division / Type</dt>
                            <dd class="mt-1 break-words text-sm text-slate-800">{{ $complaint->division?->name ?? 'N/A' }}</dd>
                            <dd class="mt-1 break-words text-xs text-slate-500">{{ $complaint->category?->name ?? 'N/A' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Accomplished</dt>
                            <dd class="mt-1 text-sm text-slate-800">{{ $complaint->completed_at ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('M d, Y') : 'Not recorded' }}</dd>
                            <dd class="mt-1 text-xs text-slate-500">{{ $complaint->completed_at ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('h:i A') : '—' }}</dd>
                        </div>
                        <div class="min-w-0">
                            <dt class="text-[11px] font-semibold uppercase tracking-wide text-slate-500">Report Status</dt>
                            <dd class="mt-1 break-words text-sm text-slate-800">{{ $reportStatus }}</dd>
                        </div>
                    </dl>
                    <a href="{{ route('customer-service.complaints.show', ['complaint' => $complaint, 'return_to' => 'final-closures']) }}" class="mt-4 inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-xl px-4 py-2.5 text-sm font-semibold transition focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 {{ $isReady ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                        <i class="fas fa-{{ $isReady ? 'clipboard-check' : 'eye' }}" aria-hidden="true"></i> {{ $isReady ? 'Review & Close' : 'View Details' }}
                    </a>
                </article>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 px-5 py-12 text-center">
                    <i class="fas fa-clipboard-check text-3xl text-slate-300" aria-hidden="true"></i>
                    <h3 class="mt-3 text-sm font-bold text-slate-800">No complaints found</h3>
                    <p class="mt-1 text-sm leading-6 text-slate-500">No accomplished maintenance complaints match the selected filters.</p>
                    @if ($hasFilters)
                        <a href="{{ route('customer-service.final-closures.index') }}" class="mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800"><i class="fas fa-rotate-left" aria-hidden="true"></i> Clear Filters</a>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Tablet and desktop table --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-[900px] w-full divide-y divide-slate-100">
                <thead class="bg-slate-50">
                    <tr class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                        <th scope="col" class="px-5 py-3.5">Complaint</th>
                        <th scope="col" class="px-5 py-3.5">Complainant</th>
                        <th scope="col" class="px-5 py-3.5">Division / Type</th>
                        <th scope="col" class="px-5 py-3.5">Accomplished</th>
                        <th scope="col" class="px-5 py-3.5">Manager Review</th>
                        <th scope="col" class="px-5 py-3.5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($complaints as $complaint)
                        @php
                            $report = $complaint->maintenanceReport;
                            $reportStatus = $report?->review_status ?? 'Draft';
                            $isReady = $report?->submitted_at !== null && $reportStatus === 'Approved';
                            $statusLabel = match (true) {
                                $isReady => 'Ready to Close',
                                $reportStatus === 'Returned' => 'Returned',
                                default => 'Awaiting Review',
                            };
                            $statusClasses = match (true) {
                                $isReady => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
                                $reportStatus === 'Returned' => 'bg-rose-50 text-rose-700 ring-rose-200',
                                default => 'bg-amber-50 text-amber-700 ring-amber-200',
                            };
                        @endphp
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="max-w-xs px-5 py-4 align-top">
                                <p class="break-words text-sm font-bold text-slate-900">{{ $complaint->complaint_no }}</p>
                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-slate-500" title="{{ $complaint->description }}">{{ $complaint->description ?: 'No description' }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm font-medium text-slate-800">{{ $complaint->complainant_name ?: ($complaint->consumer?->full_name ?? 'N/A') }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $complaint->consumer?->account_number ?? 'Walk-in / No linked account' }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <p class="text-sm font-medium text-slate-800">{{ $complaint->division?->name ?? 'N/A' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $complaint->category?->name ?? 'N/A' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 align-top">
                                <p class="text-sm text-slate-700">{{ $complaint->completed_at ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('M d, Y') : 'Not recorded' }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ $complaint->completed_at ? \Illuminate\Support\Carbon::parse($complaint->completed_at)->format('h:i A') : '—' }}</p>
                            </td>
                            <td class="px-5 py-4 align-top">
                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold ring-1 ring-inset {{ $statusClasses }}"><span class="h-1.5 w-1.5 rounded-full bg-current" aria-hidden="true"></span>{{ $statusLabel }}</span>
                                <p class="mt-1.5 text-xs text-slate-500">{{ $reportStatus }}</p>
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-right align-top">
                                <a href="{{ route('customer-service.complaints.show', ['complaint' => $complaint, 'return_to' => 'final-closures']) }}" class="inline-flex items-center justify-center gap-2 rounded-xl px-3.5 py-2 text-xs font-semibold transition focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 {{ $isReady ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'border border-slate-200 bg-white text-slate-700 hover:bg-slate-50' }}">
                                    <i class="fas fa-{{ $isReady ? 'clipboard-check' : 'eye' }}" aria-hidden="true"></i> {{ $isReady ? 'Review & Close' : 'View Details' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <i class="fas fa-clipboard-check text-3xl text-slate-300" aria-hidden="true"></i>
                                <h3 class="mt-3 text-sm font-bold text-slate-800">No complaints found</h3>
                                <p class="mt-1 text-sm text-slate-500">No records match the selected filters.</p>
                                @if ($hasFilters)
                                    <a href="{{ route('customer-service.final-closures.index') }}" class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800"><i class="fas fa-rotate-left" aria-hidden="true"></i> Clear Filters</a>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($complaints->hasPages())
            <div class="border-t border-slate-100 px-4 py-4 sm:px-5">{{ $complaints->links() }}</div>
        @endif
    </section>

    {{-- Workflow information --}}
    <aside class="rounded-2xl border border-sky-100 bg-sky-50 p-4 sm:p-5">
        <div class="flex items-start gap-3">
            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700"><i class="fas fa-circle-info" aria-hidden="true"></i></span>
            <div class="min-w-0">
                <h2 class="text-sm font-bold text-sky-900">Final Closure Workflow</h2>
                <p class="mt-1 text-sm leading-6 text-sky-800">Once maintenance work is accomplished, the Maintenance Manager reviews the technician's report. After approval, Customer Service can verify the resolution and officially close the complaint. Registered consumers with a linked email account may then receive the final closure notification.</p>
            </div>
        </div>
    </aside>
</div>
@endsection
