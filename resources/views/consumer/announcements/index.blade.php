@extends('consumer.layouts.app')

@section('content')

<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-slate-800">
            Service Announcements
        </h1>

        <p class="mt-1 text-sm text-slate-500">
            View water service interruptions, maintenance advisories,
            and other announcements from Sagay Water District.
        </p>
    </div>


    {{-- Announcements --}}
    <div class="space-y-4">

        @forelse ($announcements as $announcement)

            <a
                href="{{ route(
                    'consumer.announcements.show',
                    $announcement
                ) }}"
                class="block rounded-2xl border border-slate-200
                       bg-white p-5 shadow-sm
                       transition hover:-translate-y-0.5 hover:shadow-md"
            >

                <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">

                    <div class="min-w-0">

                        <div class="flex flex-wrap items-center gap-2">

                            <span class="rounded-full bg-sky-100 px-3 py-1
                                         text-xs font-semibold text-sky-700">
                                {{ $announcement->type }}
                            </span>

                            @if ($announcement->isActive())

                                <span class="rounded-full bg-emerald-100 px-3 py-1
                                             text-xs font-semibold text-emerald-700">
                                    Active
                                </span>

                            @endif

                        </div>


                        <h2 class="mt-3 text-lg font-bold text-slate-800">
                            {{ $announcement->title }}
                        </h2>


                        <p class="mt-2 line-clamp-2 text-sm text-slate-600">
                            {{ $announcement->content }}
                        </p>


                        <div class="mt-4 flex flex-wrap gap-4 text-xs text-slate-500">

                            @if ($announcement->affected_barangay)

                                <span>
                                    <i class="fa-solid fa-location-dot mr-1"></i>

                                    {{ $announcement->affected_barangay }}
                                </span>

                            @endif


                            @if ($announcement->published_at)

                                <span>
                                    <i class="fa-regular fa-clock mr-1"></i>

                                    {{ $announcement->published_at->format('M d, Y h:i A') }}
                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="shrink-0 text-sky-600">

                        <i class="fa-solid fa-chevron-right"></i>

                    </div>

                </div>

            </a>

        @empty

            <div class="rounded-2xl border border-dashed border-slate-300
                        bg-white p-10 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center
                            rounded-full bg-slate-100 text-slate-400">

                    <i class="fa-regular fa-bell text-xl"></i>

                </div>

                <h3 class="mt-4 font-semibold text-slate-700">
                    No announcements available
                </h3>

                <p class="mt-1 text-sm text-slate-500">
                    There are currently no published service announcements.
                </p>

            </div>

        @endforelse

    </div>


    {{-- Pagination --}}
    <div>
        {{ $announcements->links() }}
    </div>

</div>

@endsection