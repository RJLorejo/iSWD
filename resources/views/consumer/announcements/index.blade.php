@extends('consumer.layouts.app')

@section('title', 'Service Announcements')

@section('content')
<div class="mx-auto w-full max-w-7xl space-y-5 sm:space-y-6">

    {{-- Page heading and summary --}}
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="min-w-0">
            <p class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-sky-700">
                <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                <span>Service Information</span>
            </p>

            <h1 class="mt-1.5 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Service Announcements
            </h1>

            <p class="mt-1.5 max-w-2xl text-sm leading-6 text-slate-500">
                Stay informed about water service interruptions, maintenance schedules, advisories, and other updates from Sagay Water District.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if ($unreadCount > 0)
                <div class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-700">
                    <span class="h-2 w-2 rounded-full bg-sky-500" aria-hidden="true"></span>
                    <span>{{ $unreadCount }} {{ Str::plural('unread', $unreadCount) }}</span>
                </div>
            @endif

            <div class="inline-flex min-h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-medium text-slate-600">
                <i class="fa-regular fa-bell text-sky-600" aria-hidden="true"></i>
                <span>{{ $announcements->total() }} {{ Str::plural('announcement', $announcements->total()) }}</span>
            </div>
        </div>
    </header>

    {{-- Search current page's announcements without changing stored data --}}
    <section class="rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:p-4" aria-label="Search announcements">
        <form id="announcement-search-form" class="flex flex-col gap-2 sm:flex-row" role="search">
            <label for="announcement-search" class="sr-only">Search announcements</label>
            <div class="relative min-w-0 flex-1">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400" aria-hidden="true"></i>
                <input
                    id="announcement-search"
                    type="search"
                    autocomplete="off"
                    placeholder="Search title, details, type, or barangay..."
                    class="min-h-11 w-full rounded-xl border border-slate-200 bg-slate-50 py-2.5 pl-10 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100"
                >
            </div>

            <div class="flex gap-2">
                <button
                    type="submit"
                    class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100 sm:flex-none"
                >
                    <i class="fa-solid fa-magnifying-glass text-xs" aria-hidden="true"></i>
                    Search
                </button>
                <button
                    id="announcement-search-clear"
                    type="button"
                    class="inline-flex min-h-11 items-center justify-center rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-100"
                >
                    Clear
                </button>
            </div>
        </form>
        <p id="announcement-search-feedback" class="mt-2 hidden text-xs text-slate-500" aria-live="polite"></p>
    </section>

    {{-- Announcement list --}}
    <div id="announcement-list" class="space-y-3">
        @forelse ($announcements as $announcement)
            @php
                $isUnread = $announcement->reads->isEmpty();
            @endphp

            <a
                href="{{ route('consumer.announcements.show', $announcement) }}"
                data-announcement-card
                data-search="{{ \Illuminate\Support\Str::lower(implode(' ', array_filter([
                    $announcement->title,
                    $announcement->content,
                    $announcement->type,
                    $announcement->affected_barangay,
                    $announcement->published_at ? $announcement->published_at->format('M d, Y g:i A') : null,
                ]))) }}"
                class="group relative block overflow-hidden rounded-2xl border bg-white p-4 transition duration-200 hover:-translate-y-0.5 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100 sm:p-5 lg:p-6 {{ $isUnread ? 'border-sky-200 hover:border-sky-300' : 'border-slate-200 hover:border-sky-200' }}"
            >
                @if ($isUnread)
                    <span class="absolute bottom-0 left-0 top-0 w-1 bg-sky-500" aria-hidden="true"></span>
                @endif

                <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isUnread ? 'bg-sky-100 text-sky-700' : 'bg-slate-100 text-slate-600' }} sm:h-11 sm:w-11">
                        <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-1.5 sm:gap-2">
                                    <span class="inline-flex max-w-full items-center rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                                        {{ $announcement->type }}
                                    </span>

                                    @if ($isUnread)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-2.5 py-1 text-[11px] font-semibold text-white">
                                            <span class="h-1.5 w-1.5 rounded-full bg-white" aria-hidden="true"></span>
                                            Unread
                                        </span>
                                    @endif

                                    @if ($announcement->isActive())
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <h2 class="mt-3 break-words text-base leading-6 text-slate-900 sm:text-lg sm:leading-7 {{ $isUnread ? 'font-bold' : 'font-semibold' }}">
                                    {{ $announcement->title }}
                                </h2>
                            </div>

                            <div class="flex shrink-0 items-center justify-between gap-3 sm:justify-end">
                                @if ($isUnread)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-sky-700 sm:hidden">
                                        <span class="h-2 w-2 rounded-full bg-sky-500" aria-hidden="true"></span>
                                        Unread
                                    </span>
                                @endif
                                <i class="fa-solid fa-chevron-right hidden pt-1 text-xs text-slate-300 transition group-hover:translate-x-0.5 group-hover:text-sky-600 sm:block" aria-hidden="true"></i>
                            </div>
                        </div>

                        <p class="mt-2 line-clamp-2 max-w-4xl break-words text-sm leading-6 text-slate-500">
                            {{ $announcement->content }}
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-100 pt-3 text-xs text-slate-500 sm:gap-x-5">
                            @if ($announcement->affected_barangay)
                                <span class="inline-flex min-w-0 items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot shrink-0 text-[10px] text-sky-600" aria-hidden="true"></i>
                                    <span class="break-words">{{ $announcement->affected_barangay }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-location-dot text-[10px] text-slate-400" aria-hidden="true"></i>
                                    General Service Area
                                </span>
                            @endif

                            @if ($announcement->published_at)
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-calendar text-[10px] text-slate-400" aria-hidden="true"></i>
                                    {{ $announcement->published_at->format('M d, Y') }}
                                </span>
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-[10px] text-slate-400" aria-hidden="true"></i>
                                    {{ $announcement->published_at->format('g:i A') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-14 text-center sm:px-6 sm:py-16">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-bell text-xl" aria-hidden="true"></i>
                </div>
                <h2 class="mt-4 text-base font-semibold text-slate-800">No service announcements</h2>
                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                    There are currently no published service advisories. New Sagay Water District updates will appear here when available.
                </p>
            </div>
        @endforelse
    </div>

    <div id="announcement-no-results" class="hidden rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-12 text-center sm:px-6">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-100 text-slate-400">
            <i class="fa-solid fa-magnifying-glass text-lg" aria-hidden="true"></i>
        </div>
        <h2 class="mt-3 text-base font-semibold text-slate-800">No matching announcements</h2>
        <p class="mt-1 text-sm leading-6 text-slate-500">Try another keyword or clear your search to see all announcements on this page.</p>
    </div>

    @if ($announcements->hasPages())
        <div id="announcement-pagination" class="rounded-2xl border border-slate-200 bg-white px-3 py-3 sm:px-4">
            {{ $announcements->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('announcement-search-form');
        const input = document.getElementById('announcement-search');
        const clearButton = document.getElementById('announcement-search-clear');
        const feedback = document.getElementById('announcement-search-feedback');
        const noResults = document.getElementById('announcement-no-results');
        const pagination = document.getElementById('announcement-pagination');
        const cards = Array.from(document.querySelectorAll('[data-announcement-card]'));

        if (!form || !input || !clearButton) return;

        function runSearch() {
            const query = input.value.trim().toLocaleLowerCase();
            let visibleCount = 0;

            cards.forEach(function (card) {
                const searchableText = (card.dataset.search || '').toLocaleLowerCase();
                const matches = query === '' || searchableText.includes(query);

                card.classList.toggle('hidden', !matches);
                if (matches) visibleCount++;
            });

            const hasQuery = query !== '';
            feedback.classList.toggle('hidden', !hasQuery);
            feedback.textContent = hasQuery
                ? visibleCount + ' matching announcement' + (visibleCount === 1 ? '' : 's') + ' on this page.'
                : '';

            noResults.classList.toggle('hidden', !hasQuery || visibleCount > 0);
            if (pagination) pagination.classList.toggle('hidden', hasQuery);
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            runSearch();
        });

        input.addEventListener('input', runSearch);

        clearButton.addEventListener('click', function () {
            input.value = '';
            runSearch();
            input.focus();
        });
    });
</script>
@endsection
