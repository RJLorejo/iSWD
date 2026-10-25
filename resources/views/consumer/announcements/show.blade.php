@extends('consumer.layouts.app')

@section('title', 'Service Announcement')

@section('content')
<div class="mx-auto w-full max-w-5xl space-y-5">

    {{-- Navigation and status --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a
            href="{{ route('consumer.announcements.index') }}"
            class="inline-flex items-center gap-2 rounded-lg px-2 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-100 hover:text-sky-700 focus:outline-none focus:ring-4 focus:ring-sky-100"
        >
            <i class="fa-solid fa-arrow-left text-xs" aria-hidden="true"></i>
            Back to Announcements
        </a>

        @if ($serviceAnnouncement->isActive())
            <span class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                Active Advisory
            </span>
        @endif
    </div>

    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        {{-- Announcement heading --}}
        <header class="border-b border-slate-100 px-5 py-5 sm:px-6 sm:py-6">
            <div class="flex items-start gap-3 sm:gap-4">
                <div class="hidden h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700 sm:flex">
                    <i class="fa-solid fa-bullhorn" aria-hidden="true"></i>
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

                    <h1 class="mt-2.5 break-words text-xl font-bold leading-tight tracking-tight text-slate-900 sm:text-2xl">
                        {{ $serviceAnnouncement->title }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fa-solid fa-location-dot {{ $serviceAnnouncement->affected_barangay ? 'text-sky-600' : 'text-slate-400' }}" aria-hidden="true"></i>
                            {{ $serviceAnnouncement->affected_barangay ?: 'General Service Area' }}
                        </span>

                        @if ($serviceAnnouncement->published_at)
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-slate-400" aria-hidden="true"></i>
                                Published {{ $serviceAnnouncement->published_at->format('M d, Y') }}
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-slate-400" aria-hidden="true"></i>
                                {{ $serviceAnnouncement->published_at->format('g:i A') }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </header>

        {{-- Announcement content --}}
        <div class="px-5 py-5 sm:px-6 sm:py-6">
            <div class="max-w-4xl whitespace-pre-line break-words text-sm leading-7 text-slate-700 sm:text-[15px]">
                {{ $serviceAnnouncement->content }}
            </div>

            {{-- Service schedule --}}
            @if ($serviceAnnouncement->start_at || $serviceAnnouncement->end_at)
                <section class="mt-6 overflow-hidden rounded-xl border border-slate-200" aria-labelledby="service-schedule-heading">
                    <div class="flex items-center gap-2 border-b border-slate-200 bg-slate-50 px-4 py-3">
                        <i class="fa-regular fa-calendar text-sky-700" aria-hidden="true"></i>
                        <h2 id="service-schedule-heading" class="text-sm font-semibold text-slate-800">Service Schedule</h2>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2">
                        @if ($serviceAnnouncement->start_at)
                            <div class="p-4 {{ $serviceAnnouncement->end_at ? 'sm:border-r sm:border-slate-200' : '' }}">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Starts</p>
                                <div class="mt-2 flex items-start gap-2.5">
                                    <i class="fa-solid fa-play mt-0.5 text-xs text-sky-600" aria-hidden="true"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $serviceAnnouncement->start_at->format('M d, Y') }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $serviceAnnouncement->start_at->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if ($serviceAnnouncement->end_at)
                            <div class="border-t border-slate-200 p-4 sm:border-t-0">
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">Expected End</p>
                                <div class="mt-2 flex items-start gap-2.5">
                                    <i class="fa-solid fa-flag-checkered mt-0.5 text-xs text-emerald-600" aria-hidden="true"></i>
                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">{{ $serviceAnnouncement->end_at->format('M d, Y') }}</p>
                                        <p class="mt-0.5 text-xs text-slate-500">{{ $serviceAnnouncement->end_at->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </section>
            @endif

            {{-- Consumer advisory --}}
            <aside class="mt-6 rounded-xl border border-sky-100 bg-sky-50 p-4" aria-label="Consumer advisory note">
                <div class="flex items-start gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                        <i class="fa-solid fa-circle-info" aria-hidden="true"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-sky-900">Consumer Advisory</p>
                        <p class="mt-1 text-xs leading-6 text-sky-800 sm:text-sm">
                            Please monitor iSWD for updated service information. Schedule details may change depending on field conditions and service restoration progress.
                        </p>
                    </div>
                </div>
            </aside>
        </div>
    </article>

    {{-- Complaint call to action --}}
    <section class="flex flex-col gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:gap-5 sm:p-5">
        <div class="min-w-0">
            <p class="text-sm font-semibold text-slate-800">Experiencing a water service concern?</p>
            <p class="mt-1 text-xs leading-5 text-slate-500">
                Submit a complaint if you need assistance with your water service.
            </p>
        </div>

        <a
            href="{{ route('consumer.complaints.create') }}"
            class="inline-flex min-h-10 shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100"
        >
            <i class="fa-solid fa-plus text-xs" aria-hidden="true"></i>
            Submit Complaint
        </a>
    </section>
</div>
@endsection
