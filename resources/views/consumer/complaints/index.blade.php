@extends('consumer.layouts.app')

@section('title', 'My Complaints')

@section('content')

@php
    $statusClasses = [
        'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
        'Verified' => 'bg-blue-50 text-blue-700 border-blue-200',
        'Assigned' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'In Progress' => 'bg-sky-50 text-sky-700 border-sky-200',
        'Accomplished' => 'bg-violet-50 text-violet-700 border-violet-200',
        'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'Closed' => 'bg-slate-100 text-slate-700 border-slate-200',
        'Rejected' => 'bg-red-50 text-red-700 border-red-200',
    ];

    $statusIcons = [
        'Pending' => 'fa-clock',
        'Verified' => 'fa-circle-check',
        'Assigned' => 'fa-user-check',
        'In Progress' => 'fa-screwdriver-wrench',
        'Accomplished' => 'fa-clipboard-check',
        'Completed' => 'fa-circle-check',
        'Closed' => 'fa-lock',
        'Rejected' => 'fa-circle-xmark',
    ];
@endphp

<div class="space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">

        <div>
            <div class="flex items-center gap-2 text-xs font-medium text-sky-700">
                <i class="fa-solid fa-file-lines"></i>
                <span>Consumer Service Requests</span>
            </div>

            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">
                My Complaints
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                View and monitor the progress of your submitted water service concerns.
            </p>
        </div>

        <a
            href="{{ route('consumer.complaints.create') }}"
            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800"
        >
            <i class="fa-solid fa-plus text-xs"></i>
            Submit Complaint
        </a>

    </div>


    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-100 bg-slate-50/70 px-5 py-3 sm:px-6">

            <div class="flex items-center justify-between gap-4">

                <p class="text-xs font-medium text-slate-500">
                    Complaint History
                </p>

                <span class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-medium text-slate-500">
                    {{ $complaints->total() }}
                    {{ Str::plural('record', $complaints->total()) }}
                </span>

            </div>

        </div>


        @forelse($complaints as $complaint)

            <a
                href="{{ route('consumer.complaints.show', $complaint) }}"
                class="group block border-b border-slate-100 px-5 py-5 transition last:border-b-0 hover:bg-slate-50 sm:px-6"
            >

                <div class="flex items-start gap-4">

                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                        <i class="fa-solid fa-droplet"></i>
                    </div>


                    <div class="min-w-0 flex-1">

                        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">

                            <div class="min-w-0">

                                <div class="flex flex-wrap items-center gap-2">

                                    <h2 class="text-sm font-semibold text-slate-900 sm:text-base">
                                        {{ $complaint->complaint_no }}
                                    </h2>

                                    <span
                                        class="inline-flex items-center gap-1.5 rounded-full border px-2.5 py-1 text-[11px] font-semibold {{ $statusClasses[$complaint->status] ?? 'border-slate-200 bg-slate-100 text-slate-700' }}"
                                    >
                                        <i class="fa-solid {{ $statusIcons[$complaint->status] ?? 'fa-circle' }} text-[9px]"></i>
                                        {{ $complaint->status }}
                                    </span>

                                </div>

                                <p class="mt-1.5 truncate text-sm font-medium text-slate-700">
                                    {{ optional($complaint->category)->name ?? 'Water Service Concern' }}
                                </p>

                            </div>

                            <i class="fa-solid fa-chevron-right hidden text-xs text-slate-300 transition group-hover:text-sky-600 sm:block"></i>

                        </div>


                        <div class="mt-3 flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-slate-500">

                            @if ($complaint->division)

                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-building text-[10px] text-slate-400"></i>
                                    {{ $complaint->division->name }}
                                </span>

                            @endif

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-[10px] text-slate-400"></i>
                                {{ $complaint->created_at->timezone('Asia/Manila')->format('M d, Y') }}
                            </span>

                            <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-[10px] text-slate-400"></i>
                                {{ $complaint->created_at->timezone('Asia/Manila')->format('g:i A') }}
                            </span>

                        </div>


                        @if ($complaint->description)

                            <p class="mt-3 line-clamp-2 max-w-4xl text-xs leading-5 text-slate-500">
                                {{ $complaint->description }}
                            </p>

                        @endif

                    </div>

                </div>

            </a>

        @empty

            <div class="px-6 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fa-regular fa-file-lines text-xl"></i>
                </div>

                <h2 class="mt-4 text-base font-semibold text-slate-900">
                    No complaints submitted
                </h2>

                <p class="mx-auto mt-1 max-w-md text-sm leading-6 text-slate-500">
                    You have not submitted any water service complaints. When you need assistance, you can create a new service request here.
                </p>

                <a
                    href="{{ route('consumer.complaints.create') }}"
                    class="mt-5 inline-flex items-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-sky-800"
                >
                    <i class="fa-solid fa-plus text-xs"></i>
                    Submit Your First Complaint
                </a>

            </div>

        @endforelse

    </div>


    @if ($complaints->hasPages())

        <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm">
            {{ $complaints->links() }}
        </div>

    @endif

</div>

@endsection
