@extends('consumer.layouts.app')

@section('title', 'Service Announcement')

@section('content')

<div class="mx-auto max-w-5xl space-y-5">

    <div class="flex items-center justify-between gap-4">

        <a
            href="{{ route('consumer.announcements.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-slate-600 transition hover:text-sky-700"
        >
            <i class="fa-solid fa-arrow-left text-xs"></i>
            Back to Announcements
        </a>

        @if ($serviceAnnouncement->isActive())

            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                Active Advisory
            </span>

        @endif

    </div>


    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <header class="border-b border-slate-100 px-5 py-6 sm:px-7 sm:py-7">

            <div class="flex items-start gap-4">

                <div class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700 sm:flex">
                    <i class="fa-solid fa-bullhorn"></i>
                </div>

                <div class="min-w-0 flex-1">

                    <div class="flex flex-wrap items-center gap-2">

                        <span class="rounded-full bg-sky-50 px-2.5 py-1 text-[11px] font-semibold text-sky-700">
                            {{ $serviceAnnouncement->type }}
                        </span>

                        @if ($serviceAnnouncement->isActive())

                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700 sm:hidden">
                                Active
                            </span>

                        @endif

                    </div>

                    <h1 class="mt-3 text-xl font-semibold leading-tight tracking-tight text-slate-900 sm:text-2xl">
                        {{ $serviceAnnouncement->title }}
                    </h1>


                    <div class="mt-4 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">

                        @if ($serviceAnnouncement->affected_barangay)

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-sky-600"></i>
                                {{ $serviceAnnouncement->affected_barangay }}
                            </span>

                        @else

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-solid fa-location-dot text-slate-400"></i>
                                General Service Area
                            </span>

                        @endif


                        @if ($serviceAnnouncement->published_at)

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-slate-400"></i>
                                Published {{ $serviceAnnouncement->published_at->format('M d, Y') }}
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400"></i>
                                {{ $serviceAnnouncement->published_at->format('g:i A') }}
                            </span>

                        @endif

                    </div>

                </div>

            </div>

        </header>


        <div class="px-5 py-6 sm:px-7 sm:py-7">

            <div class="max-w-4xl whitespace-pre-line text-sm leading-7 text-slate-700 sm:text-[15px]">
                {{ $serviceAnnouncement->content }}
            </div>


            @if ($serviceAnnouncement->start_at || $serviceAnnouncement->end_at)

                <div class="mt-7 overflow-hidden rounded-xl border border-slate-200">

                    <div class="flex items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">

                        <i class="fa-regular fa-calendar text-sky-700"></i>

                        <h2 class="text-sm font-semibold text-slate-800">
                            Service Schedule
                        </h2>

                    </div>


                    <div class="grid sm:grid-cols-2">

                        @if ($serviceAnnouncement->start_at)

                            <div class="p-4 sm:border-r sm:border-slate-200">

                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                    Starts
                                </p>

                                <div class="mt-2 flex items-start gap-2.5">

                                    <i class="fa-solid fa-play mt-0.5 text-xs text-sky-600"></i>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $serviceAnnouncement->start_at->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ $serviceAnnouncement->start_at->format('g:i A') }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                        @endif


                        @if ($serviceAnnouncement->end_at)

                            <div class="border-t border-slate-200 p-4 sm:border-t-0">

                                <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                                    Expected End
                                </p>

                                <div class="mt-2 flex items-start gap-2.5">

                                    <i class="fa-solid fa-flag-checkered mt-0.5 text-xs text-emerald-600"></i>

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $serviceAnnouncement->end_at->format('M d, Y') }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            {{ $serviceAnnouncement->end_at->format('g:i A') }}
                                        </p>
                                    </div>

                                </div>

                            </div>

                        @endif

                    </div>

                </div>

            @endif


            <div class="mt-7 rounded-xl border border-sky-100 bg-sky-50 p-4">

                <div class="flex items-start gap-3">

                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                        <i class="fa-solid fa-circle-info"></i>
                    </div>

                    <div>

                        <p class="text-sm font-semibold text-sky-900">
                            Consumer Advisory
                        </p>

                        <p class="mt-1 text-xs leading-5 text-sky-800 sm:text-sm sm:leading-6">
                            Please monitor iSWD for updated service information. Schedule details may change depending on field conditions and service restoration progress.
                        </p>

                    </div>

                </div>

            </div>

        </div>

    </article>


    <div class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:flex-row sm:items-center sm:justify-between">

        <div>

            <p class="text-sm font-semibold text-slate-800">
                Experiencing a water service concern?
            </p>

            <p class="mt-1 text-xs text-slate-500">
                Submit a complaint if you need assistance with your water service.
            </p>

        </div>

        <a
            href="{{ route('consumer.complaints.create') }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            Submit Complaint
        </a>

    </div>

</div>

@endsection
