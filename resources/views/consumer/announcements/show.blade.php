@extends('consumer.layouts.app')

@section('content')

<div class="mx-auto max-w-4xl space-y-6">

    {{-- Back --}}
    <div>
        <a
            href="{{ route('consumer.announcements.index') }}"
            class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800"
        >
            <i class="fa-solid fa-arrow-left"></i>
            Back to Announcements
        </a>
    </div>


    {{-- Announcement --}}
    <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 p-6">

            <div class="flex flex-wrap items-center gap-2">

                <span class="rounded-full bg-sky-100 px-3 py-1
                             text-xs font-semibold text-sky-700">
                    {{ $serviceAnnouncement->type }}
                </span>

                @if ($serviceAnnouncement->isActive())

                    <span class="rounded-full bg-emerald-100 px-3 py-1
                                 text-xs font-semibold text-emerald-700">
                        Active
                    </span>

                @endif

            </div>


            <h1 class="mt-4 text-2xl font-bold text-slate-800">
                {{ $serviceAnnouncement->title }}
            </h1>


            <div class="mt-3 flex flex-wrap gap-4 text-sm text-slate-500">

                @if ($serviceAnnouncement->affected_barangay)

                    <span>
                        <i class="fa-solid fa-location-dot mr-1"></i>
                        {{ $serviceAnnouncement->affected_barangay }}
                    </span>

                @endif


                @if ($serviceAnnouncement->published_at)

                    <span>
                        <i class="fa-regular fa-calendar mr-1"></i>
                        Published
                        {{ $serviceAnnouncement->published_at->format('M d, Y h:i A') }}
                    </span>

                @endif

            </div>

        </div>


        <div class="p-6">

            <div class="whitespace-pre-line text-sm leading-7 text-slate-700">
                {{ $serviceAnnouncement->content }}
            </div>


            @if ($serviceAnnouncement->start_at || $serviceAnnouncement->end_at)

                <div class="mt-6 rounded-xl bg-slate-50 p-4">

                    <h2 class="font-semibold text-slate-800">
                        Service Schedule
                    </h2>

                    <div class="mt-3 space-y-2 text-sm text-slate-600">

                        @if ($serviceAnnouncement->start_at)

                            <p>
                                <strong>Starts:</strong>
                                {{ $serviceAnnouncement->start_at->format('M d, Y h:i A') }}
                            </p>

                        @endif


                        @if ($serviceAnnouncement->end_at)

                            <p>
                                <strong>Expected End:</strong>
                                {{ $serviceAnnouncement->end_at->format('M d, Y h:i A') }}
                            </p>

                        @endif

                    </div>

                </div>

            @endif

        </div>

    </article>

</div>

@endsection
