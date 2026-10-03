@extends('consumer.layouts.app')

@section('title', 'Consumer Dashboard')

@section('content')

    @php

        /*
        |--------------------------------------------------------------------------
        | Status Display
        |--------------------------------------------------------------------------
        */

        $statusClasses = [

            'Pending' =>
                'bg-amber-50 text-amber-700 border-amber-100',

            'Verified' =>
                'bg-blue-50 text-blue-700 border-blue-100',

            'CS Processing' =>
                'bg-violet-50 text-violet-700 border-violet-100',

            'For Maintenance' =>
                'bg-cyan-50 text-cyan-700 border-cyan-100',

            'Assigned' =>
                'bg-indigo-50 text-indigo-700 border-indigo-100',

            'In Progress' =>
                'bg-sky-50 text-sky-700 border-sky-100',

            'Accomplished' =>
                'bg-violet-50 text-violet-700 border-violet-100',

            'Completed' =>
                'bg-emerald-50 text-emerald-700 border-emerald-100',

            'Closed' =>
                'bg-slate-100 text-slate-700 border-slate-200',

            'Rejected' =>
                'bg-red-50 text-red-700 border-red-100',

        ];


        $statusIcons = [

            'Pending' =>
                'fa-clock',

            'Verified' =>
                'fa-circle-check',

            'CS Processing' =>
                'fa-clipboard-list',

            'For Maintenance' =>
                'fa-screwdriver-wrench',

            'Assigned' =>
                'fa-user-check',

            'In Progress' =>
                'fa-screwdriver-wrench',

            'Accomplished' =>
                'fa-clipboard-check',

            'Completed' =>
                'fa-circle-check',

            'Closed' =>
                'fa-lock',

            'Rejected' =>
                'fa-circle-xmark',

        ];


        /*
        |--------------------------------------------------------------------------
        | Consumer-Friendly Status Labels
        |--------------------------------------------------------------------------
        */

        $statusLabels = [

            'Pending' =>
                'Submitted',

            'Verified' =>
                'Verified',

            'CS Processing' =>
                'Under Initial Processing',

            'For Maintenance' =>
                'Forwarded to Maintenance',

            'Assigned' =>
                'Assigned for Maintenance',

            'In Progress' =>
                'Maintenance In Progress',

            'Accomplished' =>
                'Work Accomplished',

            'Completed' =>
                'Maintenance Completed',

            'Closed' =>
                'Closed',

            'Rejected' =>
                'Rejected',

        ];

    @endphp


    <div class="max-w-7xl mx-auto space-y-5">


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-sky-600">
                    Consumer Portal
                </p>

                <p class="mt-1 text-sm text-slate-500">
                    Track your water service concerns and view SWD announcements.
                </p>

            </div>


            <a href="{{ route('consumer.complaints.create') }}"
                class="inline-flex self-start sm:self-auto
                       items-center justify-center gap-2
                       rounded-xl bg-sky-700
                       px-4 py-2.5
                       text-sm font-semibold text-white
                       transition hover:bg-sky-800">

                <i class="fa-solid fa-plus text-xs"></i>

                Submit Complaint

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- MINIMAL STATISTICS --}}
        {{-- ========================================================= --}}

        <section class="grid grid-cols-3 gap-2 sm:gap-4">


            {{-- Pending --}}

            <a href="{{ route('consumer.complaints.index', [
                    'status' => 'Pending'
                ]) }}"
                class="rounded-xl sm:rounded-2xl
                       border border-slate-200
                       bg-white
                       p-3 sm:p-5
                       transition
                       hover:border-amber-200
                       hover:shadow-sm">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  font-medium text-slate-500">
                            Pending
                        </p>

                        <p class="mt-1 text-xl sm:text-3xl
                                  font-bold text-amber-600">

                            {{ number_format($pendingComplaints) }}

                        </p>

                    </div>


                    <div class="hidden sm:flex
                                h-10 w-10
                                items-center justify-center
                                rounded-xl
                                bg-amber-50 text-amber-600">

                        <i class="fa-solid fa-clock"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          mt-2 text-xs text-slate-400">

                    Awaiting or under initial review

                </p>

            </a>


            {{-- Active --}}

            <a href="{{ route('consumer.complaints.index') }}"
                class="rounded-xl sm:rounded-2xl
                       border border-slate-200
                       bg-white
                       p-3 sm:p-5
                       transition
                       hover:border-sky-200
                       hover:shadow-sm">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  font-medium text-slate-500">
                            Active
                        </p>

                        <p class="mt-1 text-xl sm:text-3xl
                                  font-bold text-sky-600">

                            {{ number_format($activeComplaints) }}

                        </p>

                    </div>


                    <div class="hidden sm:flex
                                h-10 w-10
                                items-center justify-center
                                rounded-xl
                                bg-sky-50 text-sky-600">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          mt-2 text-xs text-slate-400">

                    Currently being processed

                </p>

            </a>


            {{-- Completed --}}

            <a href="{{ route('consumer.complaints.index') }}"
                class="rounded-xl sm:rounded-2xl
                       border border-slate-200
                       bg-white
                       p-3 sm:p-5
                       transition
                       hover:border-emerald-200
                       hover:shadow-sm">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm
                                  font-medium text-slate-500">
                            Completed
                        </p>

                        <p class="mt-1 text-xl sm:text-3xl
                                  font-bold text-emerald-600">

                            {{ number_format($completedComplaints) }}

                        </p>

                    </div>


                    <div class="hidden sm:flex
                                h-10 w-10
                                items-center justify-center
                                rounded-xl
                                bg-emerald-50 text-emerald-600">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                </div>


                <p class="hidden sm:block
                          mt-2 text-xs text-slate-400">

                    Completed or closed requests

                </p>

            </a>

        </section>


        {{-- ========================================================= --}}
        {{-- MAIN CONTENT --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1
                    xl:grid-cols-5 gap-5
                    items-start">


            {{-- ===================================================== --}}
            {{-- RECENT COMPLAINTS --}}
            {{-- ===================================================== --}}

            <section class="xl:col-span-3
                            overflow-hidden
                            rounded-2xl
                            border border-slate-200
                            bg-white">


                {{-- Header --}}

                <div class="flex items-center
                            justify-between gap-4
                            border-b border-slate-100
                            px-4 sm:px-5 py-4">

                    <div>

                        <h2 class="font-semibold text-slate-900">
                            Recent Complaints
                        </h2>

                        <p class="mt-1 text-xs sm:text-sm
                                  text-slate-500">

                            Latest updates from your submitted concerns.

                        </p>

                    </div>


                    <a href="{{ route('consumer.complaints.index') }}"
                        class="inline-flex shrink-0
                               items-center gap-1.5
                               text-xs font-semibold
                               text-sky-700
                               hover:text-sky-800">

                        View All

                        <i class="fa-solid fa-arrow-right
                                  text-[9px]">
                        </i>

                    </a>

                </div>


                {{-- Complaint Records --}}

                <div class="divide-y divide-slate-100">

                    @forelse ($recentComplaints as $complaint)

                        <a href="{{ route(
                                'consumer.complaints.show',
                                $complaint
                            ) }}"
                            class="group block
                                   px-4 sm:px-5 py-4
                                   transition
                                   hover:bg-slate-50/70">


                            <div class="flex items-start gap-3">


                                {{-- Icon --}}

                                <div class="flex h-9 w-9
                                            shrink-0
                                            items-center justify-center
                                            rounded-xl
                                            bg-sky-50
                                            text-sky-600">

                                    <i class="fa-solid fa-droplet
                                              text-xs">
                                    </i>

                                </div>


                                {{-- Complaint --}}

                                <div class="min-w-0 flex-1">


                                    {{-- Number + Status --}}

                                    <div class="flex items-start
                                                justify-between gap-3">

                                        <div class="min-w-0">

                                            <p class="truncate
                                                      text-sm font-semibold
                                                      text-slate-900">

                                                {{ $complaint->complaint_no }}

                                            </p>


                                            <p class="mt-0.5 truncate
                                                      text-xs sm:text-sm
                                                      text-slate-600">

                                                {{ $complaint->category?->name
                                                    ?? 'Water Service Concern' }}

                                            </p>

                                        </div>


                                        <span class="inline-flex
                                                     shrink-0
                                                     items-center gap-1.5
                                                     rounded-full border
                                                     px-2 py-1
                                                     text-[9px] sm:text-[10px]
                                                     font-semibold
                                                     {{ $statusClasses[$complaint->status]
                                                        ?? 'border-slate-200 bg-slate-50 text-slate-600' }}">

                                            <i class="fa-solid
                                                      {{ $statusIcons[$complaint->status]
                                                        ?? 'fa-circle' }}
                                                      text-[7px]">
                                            </i>

                                            <span class="hidden sm:inline">

                                                {{ $statusLabels[$complaint->status]
                                                    ?? $complaint->status }}

                                            </span>

                                            <span class="sm:hidden">

                                                {{ $complaint->status }}

                                            </span>

                                        </span>

                                    </div>


                                    {{-- Metadata --}}

                                    <div class="mt-2
                                                flex flex-wrap
                                                items-center
                                                gap-x-3 gap-y-1
                                                text-[10px] sm:text-[11px]
                                                text-slate-400">

                                        @if ($complaint->division)

                                            <span class="inline-flex
                                                         items-center gap-1">

                                                <i class="fa-solid
                                                          fa-building
                                                          text-[8px]">
                                                </i>

                                                {{ $complaint->division->name }}

                                            </span>

                                        @endif


                                        <span class="inline-flex
                                                     items-center gap-1">

                                            <i class="fa-regular
                                                      fa-calendar
                                                      text-[8px]">
                                            </i>

                                            {{ $complaint->created_at
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y') }}

                                        </span>

                                    </div>

                                </div>


                                <i class="fa-solid fa-chevron-right
                                          hidden sm:block
                                          mt-3 text-[9px]
                                          text-slate-300
                                          group-hover:text-sky-600">
                                </i>

                            </div>

                        </a>


                    @empty

                        {{-- Empty State --}}

                        <div class="px-5 py-12 text-center">

                            <div class="mx-auto
                                        flex h-11 w-11
                                        items-center justify-center
                                        rounded-xl
                                        bg-slate-100
                                        text-slate-400">

                                <i class="fa-regular fa-file-lines"></i>

                            </div>


                            <p class="mt-3
                                      text-sm font-semibold
                                      text-slate-700">

                                No complaints submitted

                            </p>


                            <p class="mx-auto mt-1
                                      max-w-sm
                                      text-xs leading-5
                                      text-slate-400">

                                Your submitted water service concerns
                                will appear here.

                            </p>


                            <a href="{{ route('consumer.complaints.create') }}"
                                class="mt-4 inline-flex
                                       items-center gap-2
                                       rounded-lg
                                       bg-sky-700
                                       px-3.5 py-2
                                       text-xs font-semibold
                                       text-white
                                       hover:bg-sky-800">

                                <i class="fa-solid fa-plus"></i>

                                Submit Complaint

                            </a>

                        </div>

                    @endforelse

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- ANNOUNCEMENTS --}}
            {{-- ===================================================== --}}

            <section class="xl:col-span-2
                            overflow-hidden
                            rounded-2xl
                            border border-slate-200
                            bg-white">


                {{-- Header --}}

                <div class="flex items-center
                            justify-between gap-3
                            border-b border-slate-100
                            px-4 sm:px-5 py-4">

                    <div class="min-w-0">

                        <div class="flex flex-wrap
                                    items-center gap-2">

                            <h2 class="font-semibold text-slate-900">
                                Service Announcements
                            </h2>


                            @if (($unreadAnnouncementCount ?? 0) > 0)

                                <span class="inline-flex
                                             items-center gap-1
                                             rounded-full
                                             bg-sky-50
                                             px-2 py-0.5
                                             text-[9px]
                                             font-bold
                                             text-sky-700">

                                    <span class="h-1.5 w-1.5
                                                 rounded-full
                                                 bg-sky-500">
                                    </span>

                                    {{ $unreadAnnouncementCount }}

                                </span>

                            @endif

                        </div>


                        <p class="mt-1
                                  text-xs sm:text-sm
                                  text-slate-500">

                            Latest SWD service advisories.

                        </p>

                    </div>


                    <a href="{{ route(
                            'consumer.announcements.index'
                        ) }}"
                        class="shrink-0
                               text-xs font-semibold
                               text-sky-700
                               hover:text-sky-800">

                        View All

                    </a>

                </div>


                {{-- Announcement List --}}

                <div class="divide-y divide-slate-100">

                    @forelse ($announcements as $announcement)

                        @php

                            $isUnread =
                                $announcement->reads->isEmpty();

                        @endphp


                        <a href="{{ route(
                                'consumer.announcements.show',
                                $announcement
                            ) }}"
                            class="group relative block
                                   px-4 sm:px-5 py-4
                                   transition
                                   hover:bg-slate-50/70
                                   {{ $isUnread
                                        ? 'bg-sky-50/30'
                                        : '' }}">


                            {{-- Unread Indicator --}}

                            @if ($isUnread)

                                <span class="absolute
                                             bottom-0 left-0 top-0
                                             w-0.5
                                             bg-sky-500">
                                </span>

                            @endif


                            <div class="flex items-start gap-3">


                                {{-- Icon --}}

                                <div class="flex h-9 w-9
                                            shrink-0
                                            items-center justify-center
                                            rounded-xl
                                            {{ $isUnread
                                                ? 'bg-sky-100 text-sky-700'
                                                : 'bg-slate-50 text-slate-500' }}">

                                    <i class="fa-solid
                                              fa-bullhorn
                                              text-xs">
                                    </i>

                                </div>


                                {{-- Announcement --}}

                                <div class="min-w-0 flex-1">

                                    <div class="flex
                                                items-start
                                                justify-between
                                                gap-3">

                                        <div class="min-w-0">

                                            {{-- Badges --}}

                                            <div class="flex flex-wrap
                                                        items-center gap-1.5">

                                                <span class="rounded-full
                                                             bg-slate-100
                                                             px-2 py-0.5
                                                             text-[9px]
                                                             font-semibold
                                                             text-slate-600">

                                                    {{ $announcement->type }}

                                                </span>


                                                @if ($isUnread)

                                                    <span class="rounded-full
                                                                 bg-sky-600
                                                                 px-2 py-0.5
                                                                 text-[9px]
                                                                 font-semibold
                                                                 text-white">

                                                        New

                                                    </span>

                                                @endif

                                            </div>


                                            {{-- Title --}}

                                            <p class="mt-1.5
                                                      line-clamp-1
                                                      text-sm
                                                      {{ $isUnread
                                                        ? 'font-bold'
                                                        : 'font-semibold' }}
                                                      text-slate-800">

                                                {{ $announcement->title }}

                                            </p>

                                        </div>


                                        <i class="fa-solid
                                                  fa-chevron-right
                                                  mt-2
                                                  text-[9px]
                                                  text-slate-300
                                                  group-hover:text-sky-600">
                                        </i>

                                    </div>


                                    {{-- Content --}}

                                    <p class="mt-1
                                              line-clamp-2
                                              text-xs leading-5
                                              text-slate-500">

                                        {{ $announcement->content }}

                                    </p>


                                    {{-- Metadata --}}

                                    <div class="mt-2
                                                flex flex-wrap
                                                items-center
                                                gap-x-3 gap-y-1
                                                text-[10px]
                                                text-slate-400">


                                        <span class="inline-flex
                                                     items-center gap-1">

                                            <i class="fa-solid
                                                      fa-location-dot
                                                      text-[8px]">
                                            </i>

                                            {{ $announcement->affected_barangay
                                                ?: 'General Service Area' }}

                                        </span>


                                        @if ($announcement->published_at)

                                            <span class="inline-flex
                                                         items-center gap-1">

                                                <i class="fa-regular
                                                          fa-clock
                                                          text-[8px]">
                                                </i>

                                                {{ $announcement
                                                    ->published_at
                                                    ->diffForHumans() }}

                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </a>


                    @empty

                        <div class="px-5 py-10 text-center">

                            <div class="mx-auto
                                        flex h-10 w-10
                                        items-center justify-center
                                        rounded-xl
                                        bg-slate-100
                                        text-slate-400">

                                <i class="fa-regular fa-bell"></i>

                            </div>

                            <p class="mt-3
                                      text-sm font-medium
                                      text-slate-700">

                                No active announcements

                            </p>

                            <p class="mt-1
                                      text-xs text-slate-400">

                                New SWD advisories will appear here.

                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>

    </div>

@endsection
