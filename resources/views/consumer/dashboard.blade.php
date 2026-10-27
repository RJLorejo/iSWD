@extends('consumer.layouts.app')

@section('title', 'Dashboard')

@section('content')
@php
    $statusLabels = [
        'Pending' => 'Submitted',
        'Verified' => 'Verified',
        'CS Processing' => 'Under initial processing',
        'For Maintenance' => 'Forwarded to maintenance',
        'Assigned' => 'Plumber assigned',
        'In Progress' => 'Maintenance in progress',
        'Completed' => 'Completed',
        'Closed' => 'Closed',
        'Rejected' => 'Rejected',
    ];

    $statusClasses = [
        'Pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'Verified' => 'bg-sky-50 text-sky-700 ring-sky-200',
        'CS Processing' => 'bg-cyan-50 text-cyan-700 ring-cyan-200',
        'For Maintenance' => 'bg-violet-50 text-violet-700 ring-violet-200',
        'Assigned' => 'bg-indigo-50 text-indigo-700 ring-indigo-200',
        'In Progress' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'Completed' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'Closed' => 'bg-slate-100 text-slate-600 ring-slate-200',
        'Rejected' => 'bg-rose-50 text-rose-700 ring-rose-200',
    ];

    $statusIcons = [
        'Pending' => 'fa-clock',
        'Verified' => 'fa-circle-check',
        'CS Processing' => 'fa-clipboard-list',
        'For Maintenance' => 'fa-share',
        'Assigned' => 'fa-user-group',
        'In Progress' => 'fa-screwdriver-wrench',
        'Completed' => 'fa-check',
        'Closed' => 'fa-lock',
        'Rejected' => 'fa-circle-xmark',
    ];
@endphp

<div class="mx-auto w-full max-w-7xl space-y-5 sm:space-y-6">

    {{-- Compact page heading --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-semibold uppercase tracking-wider text-sky-700">Sagay Water District</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Dashboard</h1>
            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                Hello, {{ $consumer->first_name }}. View your complaint progress and the latest service announcements.
            </p>
        </div>

        <a href="{{ route('consumer.complaints.create') }}"
           class="inline-flex min-h-11 w-full shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100 sm:w-auto">
            <i class="fas fa-plus text-xs" aria-hidden="true"></i>
            Submit a complaint
        </a>
    </header>

    {{-- At-a-glance metrics --}}
    <section aria-label="Complaint summary" class="grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-4">
        <a href="{{ route('consumer.complaints.index') }}"
           class="group min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium text-slate-500 sm:text-sm">All complaints</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-600">
                    <i class="fas fa-file-lines" aria-hidden="true"></i>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">{{ $totalComplaints }}</p>
            <p class="mt-1 text-xs text-slate-500">Total submitted</p>
        </a>

        <a href="{{ route('consumer.complaints.index', ['group' => 'pending']) }}"
           class="group min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-amber-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-amber-100 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium text-slate-500 sm:text-sm">Awaiting review</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <i class="fas fa-clock" aria-hidden="true"></i>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">{{ $pendingComplaints }}</p>
            <p class="mt-1 text-xs text-slate-500">Submitted or verified</p>
        </a>

        <a href="{{ route('consumer.complaints.index', ['group' => 'active']) }}"
           class="group min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-sky-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium text-slate-500 sm:text-sm">In progress</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                    <i class="fas fa-screwdriver-wrench" aria-hidden="true"></i>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">{{ $activeComplaints }}</p>
            <p class="mt-1 text-xs text-slate-500">Being handled</p>
        </a>

        <a href="{{ route('consumer.complaints.index', ['group' => 'completed']) }}"
           class="group min-w-0 rounded-xl border border-slate-200 bg-white p-4 shadow-sm transition hover:border-emerald-200 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-emerald-100 sm:p-5">
            <div class="flex items-center justify-between gap-2">
                <p class="text-xs font-medium text-slate-500 sm:text-sm">Completed</p>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-700">
                    <i class="fas fa-circle-check" aria-hidden="true"></i>
                </span>
            </div>
            <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900 sm:text-3xl">{{ $completedComplaints }}</p>
            <p class="mt-1 text-xs text-slate-500">Completed or closed</p>
        </a>
    </section>

    {{-- Recent complaints and service updates --}}
    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-5 lg:gap-6">

        <section class="min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-3">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-5">
                <div class="min-w-0">
                    <h2 class="text-sm font-semibold text-slate-900 sm:text-base">Recent complaints</h2>
                    <p class="mt-1 text-xs text-slate-500">Check the latest status of your requests.</p>
                </div>
                <a href="{{ route('consumer.complaints.index') }}"
                   class="inline-flex shrink-0 items-center gap-1 text-xs font-semibold text-sky-700 hover:text-sky-900 sm:text-sm">
                    View all <i class="fas fa-arrow-right text-[10px]" aria-hidden="true"></i>
                </a>
            </div>

            @forelse ($recentComplaints as $complaint)
                @php
                    $rawStatus = $complaint->status;
                    $displayStatus = $statusLabels[$rawStatus] ?? $rawStatus;
                    $displayClass = $statusClasses[$rawStatus] ?? 'bg-slate-100 text-slate-600 ring-slate-200';
                    $displayIcon = $statusIcons[$rawStatus] ?? 'fa-circle';
                @endphp

                <a href="{{ route('consumer.complaints.show', $complaint) }}"
                   class="flex min-w-0 items-start gap-3 border-b border-slate-100 px-4 py-4 transition last:border-b-0 hover:bg-slate-50 focus:bg-slate-50 focus:outline-none sm:gap-4 sm:px-5">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                        <i class="fas fa-droplet" aria-hidden="true"></i>
                    </span>

                    <span class="min-w-0 flex-1">
                        <span class="flex flex-col gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-between">
                            <span class="break-words text-sm font-semibold text-slate-900">
                                {{ $complaint->complaint_no }}
                            </span>
                            <span class="inline-flex w-fit max-w-full items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $displayClass }}">
                                <i class="fas {{ $displayIcon }} text-[9px]" aria-hidden="true"></i>
                                {{ $displayStatus }}
                            </span>
                        </span>
                        <span class="mt-1 block truncate text-xs text-slate-600">
                            {{ optional($complaint->category)->name ?? 'Water service concern' }}
                        </span>
                        <span class="mt-1.5 block text-[11px] text-slate-400">
                            Submitted {{ $complaint->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                        </span>
                    </span>
                    <i class="fas fa-chevron-right mt-1 shrink-0 text-[10px] text-slate-300" aria-hidden="true"></i>
                </a>
            @empty
                <div class="px-5 py-10 text-center sm:py-12">
                    <span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <i class="far fa-file-lines text-lg" aria-hidden="true"></i>
                    </span>
                    <h3 class="mt-3 text-sm font-semibold text-slate-800">No complaints yet</h3>
                    <p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-slate-500">
                        When you submit a water service concern, its status will appear here.
                    </p>
                    <a href="{{ route('consumer.complaints.create') }}"
                       class="mt-4 inline-flex min-h-10 items-center justify-center gap-2 rounded-lg bg-sky-700 px-4 py-2 text-xs font-semibold text-white hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <i class="fas fa-plus text-[10px]" aria-hidden="true"></i>
                        Submit a complaint
                    </a>
                </div>
            @endforelse
        </section>

        <section class="min-w-0 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
            <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-4 py-4 sm:px-5">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h2 class="text-sm font-semibold text-slate-900 sm:text-base">Service updates</h2>
                        @if ($unreadAnnouncementCount > 0)
                            <span class="inline-flex min-w-5 items-center justify-center rounded-full bg-sky-100 px-1.5 py-0.5 text-[10px] font-bold text-sky-800">
                                {{ $unreadAnnouncementCount }} unread
                            </span>
                        @endif
                    </div>
                    <p class="mt-1 text-xs text-slate-500">Announcements from Sagay Water District.</p>
                </div>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-700">
                    <i class="fas fa-bullhorn" aria-hidden="true"></i>
                </span>
            </div>

            @forelse ($announcements as $announcement)
                @php
                    $isRead = $announcement->reads->isNotEmpty();
                @endphp

                <a href="{{ route('consumer.announcements.show', $announcement) }}"
                   class="group relative block min-w-0 border-b border-slate-100 px-4 py-4 transition last:border-b-0 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-600 sm:px-5 {{ !$isRead ? 'bg-sky-50/40' : '' }}"
                   aria-label="Read announcement: {{ $announcement->title }}">
                    <span class="flex items-start gap-3">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ !$isRead ? 'bg-sky-600' : 'bg-slate-300' }}" aria-hidden="true"></span>

                        <span class="min-w-0 flex-1">
                            <span class="flex items-start justify-between gap-2">
                                <span class="break-words text-sm font-semibold leading-5 text-slate-800 group-hover:text-sky-800">
                                    {{ $announcement->title }}
                                </span>
                                <span class="flex shrink-0 items-center gap-2">
                                    @if (!$isRead)
                                        <span class="rounded-full bg-sky-100 px-2 py-0.5 text-[9px] font-bold uppercase tracking-wide text-sky-700">New</span>
                                    @endif
                                    <i class="fas fa-chevron-right mt-0.5 text-[10px] text-slate-400 transition-transform group-hover:translate-x-0.5" aria-hidden="true"></i>
                                </span>
                            </span>

                            @if ($announcement->content)
                                <span class="mt-1.5 block break-words text-xs leading-5 text-slate-600">
                                    {{ \Illuminate\Support\Str::limit($announcement->content, 140) }}
                                </span>
                            @endif

                            <span class="mt-2 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">
                                @if ($announcement->type)
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-tag text-[9px]" aria-hidden="true"></i>
                                        {{ $announcement->type }}
                                    </span>
                                @endif
                                @if ($announcement->published_at)
                                    <span class="inline-flex items-center gap-1">
                                        <i class="far fa-clock" aria-hidden="true"></i>
                                        {{ $announcement->published_at->timezone('Asia/Manila')->diffForHumans() }}
                                    </span>
                                @endif
                            </span>
                            <span class="mt-2 block text-[11px] font-medium text-sky-700">Read full announcement</span>
                        </span>
                    </span>
                </a>
            @empty
                <div class="px-5 py-10 text-center">
                    <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
                        <i class="fas fa-bullhorn" aria-hidden="true"></i>
                    </span>
                    <h3 class="mt-3 text-sm font-semibold text-slate-800">No service updates</h3>
                    <p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-slate-500">
                        Important service announcements will appear here when available.
                    </p>
                </div>
            @endforelse
        </section>
    </div>

    <p class="px-1 text-center text-[11px] leading-5 text-slate-400">
        Need help? Submit a complaint to let Sagay Water District know about your water service concern.
    </p>
</div>
@endsection
