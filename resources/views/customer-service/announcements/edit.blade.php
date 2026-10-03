@extends('customer-service.layouts.app')

@section('title', 'Edit Service Announcement')

@section('content')

<div class="space-y-5">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>

            <div class="flex items-center gap-2 text-xs font-medium text-sky-700">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Service Announcements</span>
            </div>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900">
                Edit Service Announcement
            </h1>

            <p class="mt-1 text-sm leading-6 text-slate-500">
                Update the announcement information, affected area, message, and service schedule.
            </p>

        </div>

        <a
            href="{{ route(
                'customer-service.announcements.show',
                $serviceAnnouncement
            ) }}"
            class="inline-flex w-fit items-center justify-center gap-2
                   rounded-xl border border-slate-200 bg-white
                   px-4 py-2.5 text-sm font-semibold text-slate-600
                   shadow-sm transition hover:border-slate-300
                   hover:bg-slate-50 hover:text-slate-900">

            <i class="fa-solid fa-arrow-left text-xs"></i>

            Back to Details

        </a>

    </div>

    @if ($errors->any())

        <div class="rounded-xl border border-red-200 bg-red-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0
                           items-center justify-center
                           rounded-lg bg-red-100 text-red-600">

                    <i class="fa-solid fa-circle-exclamation"></i>

                </div>

                <div>

                    <h2 class="text-sm font-semibold text-red-800">
                        Please correct the following errors
                    </h2>

                    <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">

                        @foreach ($errors->all() as $error)

                            <li>{{ $error }}</li>

                        @endforeach

                    </ul>

                </div>

            </div>

        </div>

    @endif

    @if ($serviceAnnouncement->status === 'Published')

        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0
                           items-center justify-center
                           rounded-lg bg-amber-100 text-amber-600">

                    <i class="fa-solid fa-triangle-exclamation"></i>

                </div>

                <div>

                    <p class="text-sm font-semibold text-amber-900">
                        Published Announcement
                    </p>

                    <p class="mt-1 text-sm leading-6 text-amber-700">
                        This announcement is currently visible in the consumer portal. Changes you save will update the published information.
                    </p>

                </div>

            </div>

        </div>

    @elseif ($serviceAnnouncement->status === 'Archived')

        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0
                           items-center justify-center
                           rounded-lg bg-slate-200 text-slate-600">

                    <i class="fa-solid fa-box-archive"></i>

                </div>

                <div>

                    <p class="text-sm font-semibold text-slate-900">
                        Archived Announcement
                    </p>

                    <p class="mt-1 text-sm leading-6 text-slate-600">
                        This announcement is archived and is not currently displayed to consumers.
                    </p>

                </div>

            </div>

        </div>

    @else

        <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0
                           items-center justify-center
                           rounded-lg bg-blue-100 text-blue-600">

                    <i class="fa-solid fa-file-pen"></i>

                </div>

                <div>

                    <p class="text-sm font-semibold text-blue-900">
                        Draft Announcement
                    </p>

                    <p class="mt-1 text-sm leading-6 text-blue-700">
                        This announcement is still a draft. Consumers will not see it until it is published.
                    </p>

                </div>

            </div>

        </div>

    @endif

    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="text-base font-semibold text-slate-900">
                        Announcement Details
                    </h2>

                    <p class="mt-0.5 text-sm text-slate-500">
                        Review and update the information below.
                    </p>

                </div>

                <div>

                    @if ($serviceAnnouncement->status === 'Published')

                        <span
                            class="inline-flex items-center gap-1.5
                                   rounded-full bg-emerald-50
                                   px-3 py-1.5 text-xs
                                   font-semibold text-emerald-700">

                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                            Published

                        </span>

                    @elseif ($serviceAnnouncement->status === 'Archived')

                        <span
                            class="inline-flex items-center gap-1.5
                                   rounded-full bg-slate-100
                                   px-3 py-1.5 text-xs
                                   font-semibold text-slate-600">

                            <i class="fa-solid fa-box-archive text-[10px]"></i>

                            Archived

                        </span>

                    @else

                        <span
                            class="inline-flex items-center gap-1.5
                                   rounded-full bg-amber-50
                                   px-3 py-1.5 text-xs
                                   font-semibold text-amber-700">

                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>

                            Draft

                        </span>

                    @endif

                </div>

            </div>

        </div>

        <form
            method="POST"
            action="{{ route(
                'customer-service.announcements.update',
                $serviceAnnouncement
            ) }}"
            class="py-5">

            @csrf
            @method('PUT')

            @include(
                'customer-service.announcements.partials.form',
                [
                    'serviceAnnouncement' => $serviceAnnouncement
                ]
            )

        </form>

    </div>

</div>

@endsection
