@extends('customer-service.layouts.app')

@section('title', 'Announcement Details')

@section('content')

    <div class="mx-auto max-w-6xl space-y-5">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-sm font-medium text-gray-500">
                    Service Announcement
                </p>

                <h1 class="mt-0.5 text-2xl font-bold text-gray-900">
                    {{ $serviceAnnouncement->title }}
                </h1>

            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('customer-service.announcements.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-gray-300
                           px-4 py-2 text-sm font-medium
                           text-gray-700 hover:bg-gray-50">

                    <i class="fa-solid fa-arrow-left"></i>
                    Back

                </a>

                <a
                    href="{{ route(
                        'customer-service.announcements.edit',
                        $serviceAnnouncement
                    ) }}"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-amber-200
                           bg-amber-50 px-4 py-2 text-sm
                           font-medium text-amber-700
                           hover:bg-amber-100">

                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit

                </a>

            </div>

        </div>

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 p-5">

                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">

                    <div>

                        <div class="flex flex-wrap items-center gap-2">

                            <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                {{ $serviceAnnouncement->type }}
                            </span>

                            @if ($serviceAnnouncement->status === 'Published')

                                <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    Published
                                </span>

                            @elseif ($serviceAnnouncement->status === 'Draft')

                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                    Draft
                                </span>

                            @else

                                <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                    Archived
                                </span>

                            @endif

                            @if ($serviceAnnouncement->isActive())

                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                    Active Advisory
                                </span>

                            @endif

                        </div>

                        <p class="mt-3 text-sm text-gray-500">
                            {{ $serviceAnnouncement->affected_barangay ?: 'General Service Area' }}
                        </p>

                    </div>

                    <div class="flex flex-wrap gap-2">

                        @if ($serviceAnnouncement->status !== 'Published')

                            <form
                                method="POST"
                                action="{{ route(
                                    'customer-service.announcements.publish',
                                    $serviceAnnouncement
                                ) }}"
                                onsubmit="return confirm('Publish this announcement to the consumer portal?');">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           rounded-lg bg-green-600 px-4 py-2
                                           text-sm font-medium text-white
                                           hover:bg-green-700">

                                    <i class="fa-solid fa-paper-plane"></i>
                                    Publish

                                </button>

                            </form>

                        @endif

                        @if ($serviceAnnouncement->status === 'Published')

                            <form
                                method="POST"
                                action="{{ route(
                                    'customer-service.announcements.archive',
                                    $serviceAnnouncement
                                ) }}">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           rounded-lg border border-gray-300
                                           px-4 py-2 text-sm font-medium
                                           text-gray-700 hover:bg-gray-50">

                                    <i class="fa-solid fa-box-archive"></i>
                                    Archive

                                </button>

                            </form>

                        @endif

                        @if ($serviceAnnouncement->status === 'Archived')

                            <form
                                method="POST"
                                action="{{ route(
                                    'customer-service.announcements.draft',
                                    $serviceAnnouncement
                                ) }}">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center gap-2
                                           rounded-lg border border-blue-200
                                           bg-blue-50 px-4 py-2
                                           text-sm font-medium text-blue-700">

                                    <i class="fa-solid fa-file-pen"></i>
                                    Move to Draft

                                </button>

                            </form>

                        @endif

                    </div>

                </div>

            </div>

            <div class="p-5">

                <div class="whitespace-pre-line text-sm leading-7 text-gray-700">
                    {{ $serviceAnnouncement->content }}
                </div>

            </div>

        </div>

        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <h2 class="font-semibold text-gray-900">
                    Service Schedule
                </h2>

                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Starts
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $serviceAnnouncement->start_at
                                ? $serviceAnnouncement->start_at->format('M d, Y g:i A')
                                : 'Not specified' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Expected End
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $serviceAnnouncement->end_at
                                ? $serviceAnnouncement->end_at->format('M d, Y g:i A')
                                : 'Not specified' }}
                        </p>

                    </div>

                </div>

            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">

                <h2 class="font-semibold text-gray-900">
                    Publication Information
                </h2>

                <div class="mt-4 space-y-4">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Published By
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $serviceAnnouncement->publisher?->full_name ?? 'Not yet published' }}
                        </p>

                    </div>

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Published At
                        </p>

                        <p class="mt-1 text-sm font-medium text-gray-900">
                            {{ $serviceAnnouncement->published_at
                                ? $serviceAnnouncement->published_at->format('M d, Y g:i A')
                                : '—' }}
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="flex justify-end border-t border-gray-200 pt-5">

            <form
                method="POST"
                action="{{ route(
                    'customer-service.announcements.destroy',
                    $serviceAnnouncement
                ) }}"
                onsubmit="return confirm('Delete this announcement? It will be removed from normal records.');">

                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-red-200
                           bg-red-50 px-4 py-2
                           text-sm font-medium text-red-700
                           hover:bg-red-100">

                    <i class="fa-solid fa-trash"></i>
                    Delete Announcement

                </button>

            </form>

        </div>

    </div>

@endsection
