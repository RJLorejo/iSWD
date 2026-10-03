@extends('consumer.layouts.app')

@section('title', 'Service Announcements')

@section('content')

<div class="space-y-6">

    <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">

        <div>

            <div class="flex items-center gap-2 text-xs font-medium text-sky-700">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Service Information</span>
            </div>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                Service Announcements
            </h1>

            <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                Stay informed about water service interruptions, maintenance schedules, advisories, and other updates from Sagay Water District.
            </p>

        </div>

        <div class="flex flex-wrap items-center gap-2">

            @if ($unreadCount > 0)

                <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-3.5 py-2 text-xs font-semibold text-sky-700 shadow-sm">

                    <span class="h-2 w-2 rounded-full bg-sky-500"></span>

                    <span>
                        {{ $unreadCount }}
                        {{ Str::plural('unread', $unreadCount) }}
                    </span>

                </div>

            @endif

            <div class="inline-flex w-fit items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs text-slate-500 shadow-sm">

                <i class="fa-regular fa-bell text-sky-600"></i>

                <span>
                    {{ $announcements->total() }}
                    {{ Str::plural('announcement', $announcements->total()) }}
                </span>

            </div>

        </div>

    </div>

    <div class="space-y-3">

        @forelse ($announcements as $announcement)

            @php
                $isUnread = $announcement->reads->isEmpty();
            @endphp

            <a
                href="{{ route('consumer.announcements.show', $announcement) }}"
                class="
                    group relative block overflow-hidden rounded-2xl
                    bg-white p-5 shadow-sm transition hover:shadow-md sm:p-6
                    {{ $isUnread
                        ? 'border border-sky-200 hover:border-sky-300'
                        : 'border border-slate-200 hover:border-sky-200' }}
                "
            >

                @if ($isUnread)

                    <div class="absolute bottom-0 left-0 top-0 w-1 bg-sky-500"></div>

                @endif

                <div class="flex items-start gap-4">

                    <div
                        class="
                            flex h-11 w-11 shrink-0 items-center justify-center rounded-xl
                            {{ $isUnread
                                ? 'bg-sky-100 text-sky-700'
                                : 'bg-sky-50 text-sky-700' }}
                        "
                    >

                        <i class="fa-solid fa-bullhorn"></i>

                    </div>

                    <div class="min-w-0 flex-1">

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <span class="inline-flex rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                                        {{ $announcement->type }}
                                    </span>

                                    @if ($isUnread)

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-600 px-2.5 py-1 text-[11px] font-semibold text-white">

                                            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>

                                            Unread

                                        </span>

                                    @endif

                                    @if ($announcement->isActive())

                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Active

                                        </span>

                                    @endif

                                </div>

                                <h2
                                    class="
                                        mt-3 text-base text-slate-900 sm:text-lg
                                        {{ $isUnread
                                            ? 'font-bold'
                                            : 'font-semibold' }}
                                    "
                                >
                                    {{ $announcement->title }}
                                </h2>

                            </div>

                            <div class="flex items-center gap-3">

                                @if ($isUnread)

                                    <span
                                        class="h-2.5 w-2.5 shrink-0 rounded-full bg-sky-500"
                                        title="Unread announcement">
                                    </span>

                                @endif

                                <i class="fa-solid fa-chevron-right mt-1 hidden text-xs text-slate-300 transition group-hover:text-sky-600 sm:block"></i>

                            </div>

                        </div>

                        <p class="mt-2 line-clamp-2 max-w-4xl text-sm leading-6 text-slate-500">
                            {{ $announcement->content }}
                        </p>

                        <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 border-t border-slate-100 pt-3 text-xs text-slate-500">

                            @if ($announcement->affected_barangay)

                                <span class="inline-flex items-center gap-1.5">

                                    <i class="fa-solid fa-location-dot text-[10px] text-sky-600"></i>

                                    {{ $announcement->affected_barangay }}

                                </span>

                            @else

                                <span class="inline-flex items-center gap-1.5">

                                    <i class="fa-solid fa-location-dot text-[10px] text-slate-400"></i>

                                    General Service Area

                                </span>

                            @endif

                            @if ($announcement->published_at)

                                <span class="inline-flex items-center gap-1.5">

                                    <i class="fa-regular fa-calendar text-[10px] text-slate-400"></i>

                                    {{ $announcement->published_at->format('M d, Y') }}

                                </span>

                                <span class="inline-flex items-center gap-1.5">

                                    <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>

                                    {{ $announcement->published_at->format('g:i A') }}

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </a>

        @empty

            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">

                    <i class="fa-regular fa-bell text-xl"></i>

                </div>

                <h2 class="mt-4 text-base font-semibold text-slate-800">
                    No service announcements
                </h2>

                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                    There are currently no published service advisories. New Sagay Water District updates will appear here when available.
                </p>

            </div>

        @endforelse

    </div>

    @if ($announcements->hasPages())

        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">

            {{ $announcements->links() }}

        </div>

    @endif

</div>

@endsection
