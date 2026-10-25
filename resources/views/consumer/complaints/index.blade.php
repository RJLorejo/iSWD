@extends('consumer.layouts.app')

@section('title', 'My Complaints')

@section('content')
    @php
        $statusLabels = [
            'Pending' => 'Submitted',
            'Verified' => 'Verified',
            'CS Processing' => 'Under Initial Processing',
            'For Maintenance' => 'Forwarded to Maintenance',
            'Assigned' => 'Plumber Assigned',
            'In Progress' => 'Maintenance In Progress',
            'Completed' => 'Maintenance Completed',
            'Closed' => 'Request Closed',
            'Rejected' => 'Rejected',
        ];

        $groupLabels = [
            'pending' => 'Awaiting Review',
            'active' => 'Active Requests',
            'completed' => 'Completed Requests',
        ];

        $statusClasses = [
            'Pending' => 'border-amber-200 bg-amber-50 text-amber-700',
            'Verified' => 'border-sky-200 bg-sky-50 text-sky-700',
            'CS Processing' => 'border-cyan-200 bg-cyan-50 text-cyan-700',
            'For Maintenance' => 'border-violet-200 bg-violet-50 text-violet-700',
            'Assigned' => 'border-indigo-200 bg-indigo-50 text-indigo-700',
            'In Progress' => 'border-blue-200 bg-blue-50 text-blue-700',
            'Completed' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
            'Closed' => 'border-slate-200 bg-slate-100 text-slate-700',
            'Rejected' => 'border-red-200 bg-red-50 text-red-700',
        ];

        $statusIcons = [
            'Pending' => 'fa-clock',
            'Verified' => 'fa-circle-check',
            'CS Processing' => 'fa-clipboard-list',
            'For Maintenance' => 'fa-share',
            'Assigned' => 'fa-user-group',
            'In Progress' => 'fa-screwdriver-wrench',
            'Completed' => 'fa-clipboard-check',
            'Closed' => 'fa-lock',
            'Rejected' => 'fa-circle-xmark',
        ];

        $hasFilters = ($search ?? '') !== '' || ($status ?? '') !== '' || ($group ?? '') !== '';
    @endphp

    <main class="mx-auto w-full max-w-7xl space-y-5 px-0 sm:space-y-6">
        <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wide text-sky-700">
                    <i class="fas fa-file-lines" aria-hidden="true"></i>
                    Consumer Service Requests
                </p>
                <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">My Complaints</h1>
                <p class="mt-1.5 max-w-2xl text-sm leading-6 text-slate-500">
                    Track the status and progress of your water service concerns.
                </p>
            </div>

            <a href="{{ route('consumer.complaints.create') }}"
                class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100 sm:w-auto sm:px-5">
                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                Submit Complaint
            </a>
        </header>

        {{-- Search and filters --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-label="Search and filter complaints">
            <form id="complaint-filter-form" method="GET" action="{{ route('consumer.complaints.index') }}"
                class="space-y-4 p-4 sm:p-5">
                @if (($group ?? '') !== '')
                    <input type="hidden" name="group" value="{{ $group }}">
                @endif

                <div class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_220px_auto]">
                    <div class="relative min-w-0">
                        <label for="search" class="sr-only">Search complaints</label>
                        <i class="fas fa-magnifying-glass pointer-events-none absolute inset-y-0 left-0 flex items-center pt-4 pl-4 text-sm text-slate-400"
                            aria-hidden="true"></i>
                        <input type="search" id="search" name="search" value="{{ $search ?? '' }}"
                            placeholder="Search complaint number, type, division, or description" autocomplete="off"
                            aria-controls="complaint-list"
                            class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                    </div>

                    <div class="relative min-w-0">
                        <label for="status" class="sr-only">Filter by status</label>
                        <select id="status" name="status"
                            class="min-h-11 w-full appearance-none rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 pr-10 text-sm text-slate-700 outline-none transition focus:border-sky-400 focus:bg-white focus:ring-4 focus:ring-sky-100">
                            <option value="">All Statuses</option>
                            @foreach ($allowedStatuses as $filterStatus)
                                <option value="{{ $filterStatus }}" @selected(($status ?? '') === $filterStatus)>
                                    {{ $statusLabels[$filterStatus] ?? $filterStatus }}
                                </option>
                            @endforeach

                            <option value="Initial Processing Completed" @selected(($status ?? '') === 'Initial Processing Completed')>
                                Initial Processing Completed
                            </option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit"
                            class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 focus:outline-none focus:ring-4 focus:ring-slate-200 md:flex-none">
                            <i class="fas fa-filter text-xs" aria-hidden="true"></i>
                            Apply filters
                        </button>

                        @if ($hasFilters)
                            <a href="{{ route('consumer.complaints.index') }}" aria-label="Clear all filters"
                                title="Clear filters"
                                class="inline-flex min-h-11 w-11 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600 focus:outline-none focus:ring-4 focus:ring-slate-100">
                                <i class="fas fa-xmark" aria-hidden="true"></i>
                            </a>
                        @endif
                    </div>
                </div>

                <div id="live-search-status" class="hidden text-xs text-slate-500" aria-live="polite"></div>

                @if ($hasFilters)
                    <div class="flex flex-wrap items-center gap-2 border-t border-slate-100 pt-3">
                        <span class="text-xs font-medium text-slate-400">Filters:</span>

                        @if (isset($groupLabels[$group ?? '']))
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-sky-50 px-3 py-1.5 text-xs font-semibold text-sky-700">
                                <i class="fas fa-layer-group text-[9px]" aria-hidden="true"></i>
                                {{ $groupLabels[$group] }}
                            </span>
                        @endif

                        @if (($search ?? '') !== '')
                            <span
                                class="inline-flex max-w-full items-center gap-1.5 break-all rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                                <i class="fas fa-magnifying-glass text-[9px]" aria-hidden="true"></i>
                                {{ $search }}
                            </span>
                        @endif

                        @if (($status ?? '') !== '')
                            <span
                                class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-medium text-slate-600">
                                <i class="fas fa-filter text-[9px]" aria-hidden="true"></i>
                                {{ $statusLabels[$status] ?? $status }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </section>

        {{-- Complaint list --}}
        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
            aria-label="Complaint history">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 bg-slate-50/60 px-4 py-4 sm:px-6">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-900">
                        {{ $groupLabels[$group ?? ''] ?? 'Complaint History' }}
                    </h2>
                    <p id="complaint-list-caption" class="mt-1 text-xs leading-5 text-slate-500">
                        @if ($hasFilters)
                            Results matching your selected filters.
                        @else
                            Your most recent complaints appear first.
                        @endif
                    </p>
                </div>
                <span id="complaint-record-count"
                    class="shrink-0 rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-600">
                    {{ $complaints->total() }} {{ \Illuminate\Support\Str::plural('record', $complaints->total()) }}
                </span>
            </div>

            <div id="complaint-list">
                @forelse ($complaints as $complaint)
                    @php
                        $divisionName = optional($complaint->division)->name;
                        $categoryName = optional($complaint->category)->name ?? 'Water Service Concern';
                        $isCommercial = $divisionName && str_contains(strtolower($divisionName), 'commercial');

                        // Keep the saved complaint status unchanged.
                        // Only adjust the displayed label when initial processing is completed.
                        $complaint->loadMissing('commercialResolution');

                        $initialProcessingCompleted =
                            $isCommercial &&
                            $complaint->status === 'CS Processing' &&
                            $complaint->commercialResolution?->initial_processing_completed_at;

                        if ($initialProcessingCompleted) {
                            $complaintStatusLabel = 'Initial Processing Completed';
                            $complaintStatusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700';
                            $complaintStatusIcon = 'fa-circle-check';
                        } else {
                            $complaintStatusLabel = $statusLabels[$complaint->status] ?? $complaint->status;
                            $complaintStatusClass =
                                $statusClasses[$complaint->status] ?? 'border-slate-200 bg-slate-100 text-slate-700';
                            $complaintStatusIcon = $statusIcons[$complaint->status] ?? 'fa-circle';
                        }

                        $liveSearchText = implode(
                            ' ',
                            array_filter([
                                $complaint->complaint_no,
                                $complaintStatusLabel,
                                $complaint->status,
                                $categoryName,
                                $divisionName,
                                $complaint->description,
                                $complaint->created_at->timezone('Asia/Manila')->format('M d, Y'),
                                $complaint->created_at->timezone('Asia/Manila')->format('g:i A'),
                            ]),
                        );
                    @endphp


                    <a href="{{ route('consumer.complaints.show', $complaint) }}" data-complaint-card
                        data-search="{{ \Illuminate\Support\Str::lower($liveSearchText) }}"
                        class="group block border-b border-slate-100 transition last:border-b-0 hover:bg-sky-50/40 focus:bg-sky-50/40 focus:outline-none focus:ring-inset focus:ring-2 focus:ring-sky-500">
                        <div class="px-4 py-4 sm:px-6 sm:py-5">
                            <div class="flex items-start gap-3 sm:gap-4">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition sm:h-12 sm:w-12 sm:rounded-2xl {{ $isCommercial ? 'bg-violet-50 text-violet-600 group-hover:bg-violet-100' : 'bg-sky-50 text-sky-700 group-hover:bg-sky-100' }}">
                                    <i class="fas {{ $isCommercial ? 'fa-file-invoice' : 'fa-droplet' }}"
                                        aria-hidden="true"></i>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between sm:gap-4">
                                        <div class="min-w-0">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="break-words text-sm font-bold text-slate-900 sm:text-base">
                                                    {{ $complaint->complaint_no }}</h3>
                                                <span
                                                    class="inline-flex max-w-full items-center gap-1.5 rounded-full border px-2.5 py-1 text-[10px] font-semibold leading-4 sm:text-[11px] {{ $complaintStatusClass }}">
                                                    <i class="fas {{ $complaintStatusIcon }} text-[9px]"
                                                        aria-hidden="true"></i>
                                                    {{ $complaintStatusLabel }}
                                                </span>
                                            </div>
                                            <p
                                                class="mt-2 break-words text-sm font-semibold text-slate-700 transition group-hover:text-sky-800">
                                                {{ $categoryName }}</p>
                                        </div>

                                        <span
                                            class="hidden shrink-0 items-center gap-2 pt-1 text-xs font-semibold text-slate-400 transition group-hover:text-sky-700 sm:inline-flex">
                                            View details
                                            <i class="fas fa-chevron-right text-[10px] transition group-hover:translate-x-0.5"
                                                aria-hidden="true"></i>
                                        </span>
                                    </div>

                                    <div
                                        class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-2 text-xs leading-5 text-slate-500 sm:gap-x-5">
                                        @if ($divisionName)
                                            <span class="inline-flex items-center gap-1.5">
                                                <i class="fas fa-building text-[10px] text-slate-400"
                                                    aria-hidden="true"></i>
                                                {{ $divisionName }}
                                            </span>
                                        @endif
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="far fa-calendar text-[10px] text-slate-400" aria-hidden="true"></i>
                                            {{ $complaint->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                                        </span>
                                        <span class="inline-flex items-center gap-1.5">
                                            <i class="far fa-clock text-[10px] text-slate-400" aria-hidden="true"></i>
                                            {{ $complaint->created_at->timezone('Asia/Manila')->format('g:i A') }}
                                        </span>
                                    </div>

                                    @if ($complaint->description)
                                        <p
                                            class="mt-3 line-clamp-2 max-w-4xl break-words text-xs leading-5 text-slate-500">
                                            {{ $complaint->description }}</p>
                                    @endif

                                    <span
                                        class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-sky-700 sm:hidden">
                                        View complaint
                                        <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                @empty
                    <div id="server-empty-state" class="px-5 py-12 text-center sm:px-6 sm:py-16">
                        <div
                            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <i class="fas {{ $hasFilters ? 'fa-magnifying-glass' : 'fa-file-lines' }} text-xl"
                                aria-hidden="true"></i>
                        </div>

                        <h2 class="mt-4 text-base font-semibold text-slate-900">
                            {{ $hasFilters ? 'No matching complaints' : 'No complaints submitted' }}
                        </h2>
                        <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                            @if ($hasFilters)
                                No complaints match your current search or filters. Try changing your search or clearing the
                                filters.
                            @else
                                You have not submitted any water service concerns yet. Your complaints will appear here
                                after submission.
                            @endif
                        </p>

                        @if ($hasFilters)
                            <a href="{{ route('consumer.complaints.index') }}"
                                class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100">
                                <i class="fas fa-rotate-left text-xs" aria-hidden="true"></i>
                                Clear filters
                            </a>
                        @else
                            <a href="{{ route('consumer.complaints.create') }}"
                                class="mt-5 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                                <i class="fas fa-plus text-xs" aria-hidden="true"></i>
                                Submit your first complaint
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>

            <div id="live-search-empty-state" class="hidden px-5 py-12 text-center sm:px-6 sm:py-16" role="status"
                aria-live="polite">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-magnifying-glass text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="mt-4 text-base font-semibold text-slate-900">No matching complaints</h2>
                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">Try another keyword to find a complaint
                    in the records currently displayed.</p>
            </div>
        </section>

        {{-- Pagination --}}
        @if ($complaints->hasPages())
            <nav id="complaint-pagination" aria-label="Complaint history pagination"
                class="overflow-x-auto rounded-2xl border border-slate-200 bg-white px-3 py-3 shadow-sm sm:px-4">
                <div class="min-w-max">
                    {{ $complaints->appends(request()->query())->links() }}
                </div>
            </nav>
        @endif
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('search');
            const complaintList = document.getElementById('complaint-list');
            const complaintCards = Array.from(document.querySelectorAll('[data-complaint-card]'));
            const liveEmptyState = document.getElementById('live-search-empty-state');
            const liveSearchStatus = document.getElementById('live-search-status');
            const recordCount = document.getElementById('complaint-record-count');
            const listCaption = document.getElementById('complaint-list-caption');
            const pagination = document.getElementById('complaint-pagination');
            const serverEmptyState = document.getElementById('server-empty-state');
            const filterForm = document.getElementById('complaint-filter-form');

            if (!searchInput || !complaintList) return;

            const normalize = value => (value || '')
                .toLocaleLowerCase()
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .trim();

            function filterComplaints() {
                const query = normalize(searchInput.value);
                let visibleCount = 0;

                complaintCards.forEach(card => {
                    const searchableText = normalize(card.dataset.search || card.textContent);
                    const matches = !query || searchableText.includes(query);

                    card.classList.toggle('hidden', !matches);
                    if (matches) visibleCount++;
                });

                const hasQuery = query.length > 0;
                const hasCards = complaintCards.length > 0;

                if (liveEmptyState) {
                    liveEmptyState.classList.toggle('hidden', !(hasQuery && hasCards && visibleCount === 0));
                }

                if (serverEmptyState && hasQuery) {
                    serverEmptyState.classList.add('hidden');
                } else if (serverEmptyState && !hasQuery) {
                    serverEmptyState.classList.remove('hidden');
                }

                if (pagination) {
                    pagination.classList.toggle('hidden', hasQuery);
                }

                if (recordCount) {
                    if (hasQuery) {
                        recordCount.textContent = visibleCount + (visibleCount === 1 ? ' matching record' :
                            ' matching records');
                    } else {
                        recordCount.textContent = recordCount.dataset.original || recordCount.textContent;
                    }
                }

                if (listCaption) {
                    listCaption.textContent = hasQuery ?
                        'Showing matches from the records on this page. Press Enter or Apply filters to search all matching records on the server.' :
                        {{ Illuminate\Support\Js::from($hasFilters ? 'Results matching your selected filters.' : 'Your most recent complaints appear first.') }};
                }

                if (liveSearchStatus) {
                    liveSearchStatus.classList.toggle('hidden', !hasQuery);
                    liveSearchStatus.textContent = hasQuery ?
                        visibleCount + (visibleCount === 1 ? ' matching complaint found on this page.' :
                            ' matching complaints found on this page.') :
                        '';
                }
            }

            if (recordCount) {
                recordCount.dataset.original = recordCount.textContent.trim();
            }

            searchInput.addEventListener('input', filterComplaints);

            // Submitting the form keeps server-side search and status filters available.
            if (filterForm) {
                filterForm.addEventListener('submit', function() {
                    // Allow the normal GET request to proceed.
                });
            }

            filterComplaints();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const stateUrl = @json(route('consumer.complaints.refresh-state'));

            let previousSignature = null;
            let checking = false;
            let noticeShown = false;

            function showUpdateNotice() {
                if (noticeShown) return;

                noticeShown = true;

                const notice = document.createElement('div');
                notice.setAttribute('role', 'status');
                notice.setAttribute('aria-live', 'polite');

                notice.style.cssText = [
                    'display:flex',
                    'align-items:center',
                    'justify-content:space-between',
                    'flex-wrap:wrap',
                    'gap:12px',
                    'padding:14px 16px',
                    'margin:0 0 16px',
                    'border:1px solid #93c5fd',
                    'border-radius:8px',
                    'background:#eff6ff',
                    'color:#1e3a8a'
                ].join(';');

                const message = document.createElement('span');
                message.textContent =
                    'Complaint updates are available. Refresh when you are ready.';

                const button = document.createElement('button');
                button.type = 'button';
                button.textContent = 'Refresh now';
                button.style.cssText = [
                    'padding:8px 14px',
                    'border:0',
                    'border-radius:6px',
                    'background:#1d4ed8',
                    'color:white',
                    'font-weight:600',
                    'cursor:pointer'
                ].join(';');

                button.addEventListener('click', function() {
                    window.location.reload();
                });

                notice.append(message, button);

                const complaintList = document.getElementById('complaint-list');

                if (complaintList) {
                    complaintList.insertAdjacentElement('beforebegin', notice);
                } else {
                    document.body.prepend(notice);
                }
            }

            async function checkForUpdates() {
                if (checking || document.visibilityState !== 'visible') {
                    return;
                }

                checking = true;

                try {
                    const response = await fetch(stateUrl, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-store',
                        credentials: 'same-origin'
                    });

                    if (!response.ok) return;

                    const data = await response.json();

                    if (!data.signature) return;

                    if (previousSignature === null) {
                        previousSignature = data.signature;
                        return;
                    }

                    if (data.signature !== previousSignature) {
                        showUpdateNotice();
                    }
                } catch (error) {
                    // A temporary network error must not interrupt the consumer.
                    console.warn('Complaint update check failed.');
                } finally {
                    checking = false;
                }
            }

            checkForUpdates();
            window.setInterval(checkForUpdates, 10000);
        });
    </script>

@endsection
