@extends('customer-service.layouts.app')

@section('title', 'Service Announcements')

@section('content')

    <div class="space-y-5">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Service Announcements
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Create and manage service information displayed in the consumer portal.
                </p>

            </div>

            <a
                href="{{ route('customer-service.announcements.create') }}"
                class="inline-flex items-center justify-center gap-2
                       rounded-lg bg-blue-600 px-4 py-2.5
                       text-sm font-medium text-white
                       transition hover:bg-blue-700">

                <i class="fa-solid fa-plus"></i>
                Create Announcement

            </a>

        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-5">

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Total
                </p>
                <p class="mt-1 text-2xl font-bold text-gray-900">
                    {{ number_format($totalAnnouncements) }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Published
                </p>
                <p class="mt-1 text-2xl font-bold text-blue-600">
                    {{ number_format($publishedAnnouncements) }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Active
                </p>
                <p class="mt-1 text-2xl font-bold text-green-600">
                    {{ number_format($activeAnnouncements) }}
                </p>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <p class="text-xs font-medium text-gray-500">
                    Draft
                </p>
                <p class="mt-1 text-2xl font-bold text-amber-600">
                    {{ number_format($draftAnnouncements) }}
                </p>
            </div>

            <div class="col-span-2 rounded-xl border border-gray-200 bg-white p-4 shadow-sm lg:col-span-1">
                <p class="text-xs font-medium text-gray-500">
                    Archived
                </p>
                <p class="mt-1 text-2xl font-bold text-gray-600">
                    {{ number_format($archivedAnnouncements) }}
                </p>
            </div>

        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

            <form method="GET">

                <div class="grid grid-cols-1 gap-3 lg:grid-cols-12">

                    <div class="lg:col-span-5">

                        <label
                            for="search"
                            class="mb-1 block text-sm font-medium text-gray-700">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            id="search"
                            value="{{ request('search') }}"
                            placeholder="Title, content or affected area"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                    </div>

                    <div class="lg:col-span-3">

                        <label
                            for="type"
                            class="mb-1 block text-sm font-medium text-gray-700">
                            Type
                        </label>


                        <select
                            name="type"
                            id="type"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                            <option value="">All Types</option>

                            @foreach ($types as $type)

                                <option
                                    value="{{ $type }}"
                                    @selected(request('type') === $type)>

                                    {{ $type }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="lg:col-span-2">

                        <label
                            for="status"
                            class="mb-1 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="w-full rounded-lg border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                            <option value="">All Status</option>

                            @foreach (['Draft', 'Published', 'Archived'] as $status)

                                <option
                                    value="{{ $status }}"
                                    @selected(request('status') === $status)>

                                    {{ $status }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="flex items-end gap-2 lg:col-span-2">

                        <button
                            type="submit"
                            class="inline-flex flex-1 items-center
                                   justify-center gap-2 rounded-lg
                                   bg-blue-600 px-4 py-2.5
                                   text-sm font-medium text-white
                                   hover:bg-blue-700">

                            <i class="fa-solid fa-filter"></i>
                            Filter

                        </button>

                        @if (
                            request()->filled('search') ||
                            request()->filled('type') ||
                            request()->filled('status')
                        )

                            <a
                                href="{{ route('customer-service.announcements.index') }}"
                                class="inline-flex h-[42px] w-[42px]
                                       shrink-0 items-center justify-center
                                       rounded-lg border border-gray-300
                                       text-gray-500 hover:bg-gray-50">

                                <i class="fa-solid fa-xmark"></i>

                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

        <div class="space-y-3 lg:hidden">

            @forelse ($announcements as $announcement)

                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <span class="text-xs font-semibold text-blue-600">
                                {{ $announcement->type }}
                            </span>

                            <h2 class="mt-1 font-semibold text-gray-900">
                                {{ $announcement->title }}
                            </h2>

                        </div>

                        @if ($announcement->status === 'Published')

                            <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                Published
                            </span>

                        @elseif ($announcement->status === 'Draft')

                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                Draft
                            </span>

                        @else

                            <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                Archived
                            </span>

                        @endif

                    </div>

                    <p class="mt-2 line-clamp-2 text-sm leading-6 text-gray-500">
                        {{ $announcement->content }}
                    </p>

                    <div class="mt-3 space-y-1.5 text-xs text-gray-500">

                        <p>
                            <i class="fa-solid fa-location-dot mr-1.5 text-blue-500"></i>
                            {{ $announcement->affected_barangay ?: 'General Service Area' }}
                        </p>

                        @if ($announcement->published_at)

                            <p>
                                <i class="fa-regular fa-calendar mr-1.5"></i>
                                {{ $announcement->published_at->format('M d, Y g:i A') }}
                            </p>

                        @endif

                    </div>

                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3">

                        <a
                            href="{{ route(
                                'customer-service.announcements.show',
                                $announcement
                            ) }}"
                            class="inline-flex flex-1 items-center
                                   justify-center gap-2 rounded-lg
                                   bg-blue-50 py-2 text-sm
                                   font-medium text-blue-700">

                            <i class="fa-solid fa-eye"></i>
                            View

                        </a>

                        <a
                            href="{{ route(
                                'customer-service.announcements.edit',
                                $announcement
                            ) }}"
                            class="inline-flex flex-1 items-center
                                   justify-center gap-2 rounded-lg
                                   bg-amber-50 py-2 text-sm
                                   font-medium text-amber-700">

                            <i class="fa-solid fa-pen-to-square"></i>
                            Edit

                        </a>

                    </div>

                </div>

            @empty

                <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center">

                    <i class="fa-regular fa-bell text-2xl text-gray-300"></i>

                    <h3 class="mt-3 font-semibold text-gray-900">
                        No announcements found
                    </h3>

                </div>

            @endforelse

        </div>

        <div class="hidden overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm lg:block">

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Announcement
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Area
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Status
                            </th>

                            <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Published
                            </th>

                            <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($announcements as $announcement)

                            <tr class="hover:bg-gray-50">

                                <td class="px-5 py-4">

                                    <p class="font-medium text-gray-900">
                                        {{ $announcement->title }}
                                    </p>

                                    <p class="mt-0.5 text-xs text-blue-600">
                                        {{ $announcement->type }}
                                    </p>

                                </td>

                                <td class="px-5 py-4 text-sm text-gray-700">
                                    {{ $announcement->affected_barangay ?: 'General Service Area' }}
                                </td>

                                <td class="px-5 py-4">

                                    @if ($announcement->status === 'Published')

                                        <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                            Published
                                        </span>

                                    @elseif ($announcement->status === 'Draft')

                                        <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            Draft
                                        </span>

                                    @else

                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">
                                            Archived
                                        </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4 text-sm text-gray-600">

                                    {{ $announcement->published_at
                                        ? $announcement->published_at->format('M d, Y')
                                        : '—' }}

                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-1.5">

                                        <a
                                            href="{{ route(
                                                'customer-service.announcements.show',
                                                $announcement
                                            ) }}"
                                            class="inline-flex h-8 w-8 items-center
                                                   justify-center rounded-lg
                                                   bg-blue-50 text-blue-600
                                                   hover:bg-blue-100"
                                            title="View">

                                            <i class="fa-solid fa-eye text-xs"></i>

                                        </a>

                                        <a
                                            href="{{ route(
                                                'customer-service.announcements.edit',
                                                $announcement
                                            ) }}"
                                            class="inline-flex h-8 w-8 items-center
                                                   justify-center rounded-lg
                                                   bg-amber-50 text-amber-600
                                                   hover:bg-amber-100"
                                            title="Edit">

                                            <i class="fa-solid fa-pen-to-square text-xs"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-500">
                                    No announcements found.
                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($announcements->hasPages())

            <div>
                {{ $announcements->links() }}
            </div>

        @endif

    </div>

@endsection
