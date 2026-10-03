@extends('customer-service.layouts.app')

@section('title', 'Dashboard')

@section('content')

    @php
        $statusStyles = [
            'Pending' => 'bg-amber-50 text-amber-700 border-amber-200',
            'Verified' => 'bg-sky-50 text-sky-700 border-sky-200',
            'Assigned' => 'bg-blue-50 text-blue-700 border-blue-200',
            'In Progress' => 'bg-violet-50 text-violet-700 border-violet-200',
            'Accomplished' => 'bg-cyan-50 text-cyan-700 border-cyan-200',
            'Completed' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Closed' => 'bg-slate-100 text-slate-600 border-slate-200',
            'Rejected' => 'bg-red-50 text-red-700 border-red-200',
        ];
    @endphp

    <div class="space-y-6">

        <section
            class="overflow-hidden rounded-3xl bg-gradient-to-br from-sky-700 via-blue-700 to-cyan-600 shadow-sm">

            <div class="relative px-5 py-6 sm:px-7 sm:py-7 lg:px-8">

                <div
                    class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10">
                </div>

                <div
                    class="absolute -bottom-24 right-24 h-48 w-48 rounded-full bg-white/5">
                </div>

                <div class="relative flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">

                    <div class="max-w-2xl">

                        <p class="text-xs font-bold uppercase tracking-[0.18em] text-sky-100">
                            Customer Service Workspace
                        </p>

                        <h2 class="mt-2 text-2xl font-bold tracking-tight text-white sm:text-3xl">
                            Welcome back, {{ auth()->user()->first_name ?? auth()->user()->name }}
                        </h2>

                        <p class="mt-2 max-w-xl text-sm leading-6 text-sky-100 sm:text-base">
                            Monitor consumer concerns, review incoming complaints,
                            and keep service requests moving through the proper workflow.
                        </p>

                    </div>

                    <div class="flex flex-wrap gap-2">

                        <a
                            href="{{ route('customer-service.complaints.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-sky-700 shadow-sm transition hover:bg-sky-50">

                            <i class="fas fa-plus"></i>

                            New Complaint

                        </a>

                        <a
                            href="{{ route('customer-service.consumers.create') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-white/30 bg-white/10 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-white/20">

                            <i class="fas fa-user-plus"></i>

                            Register Consumer

                        </a>

                    </div>

                </div>

            </div>

        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <a
                href="{{ route('customer-service.consumers.index') }}"
                class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-md">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Total Consumers
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                            {{ number_format($totalConsumers) }}
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Registered consumer accounts
                        </p>

                    </div>

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 transition group-hover:bg-sky-100">

                        <i class="fas fa-users"></i>

                    </span>

                </div>

            </a>

            <a
                href="{{ route('customer-service.consumers.index') }}"
                class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            New Today
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                            {{ number_format($newConsumers) }}
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Consumers registered today
                        </p>

                    </div>

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                        <i class="fas fa-user-plus"></i>

                    </span>

                </div>

            </a>

            <a
                href="{{ route('customer-service.complaint-verification.index') }}"
                class="group rounded-2xl border {{ $pendingComplaints > 0 ? 'border-amber-200' : 'border-slate-200' }} bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            Pending Verification
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                            {{ number_format($pendingComplaints) }}
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Complaints awaiting review
                        </p>

                    </div>

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                        <i class="fas fa-shield-halved"></i>

                    </span>

                </div>

            </a>

            <a
                href="{{ route('customer-service.feedback.index', ['handling_status' => 'New']) }}"
                class="group rounded-2xl border {{ $newFeedback > 0 ? 'border-red-200' : 'border-slate-200' }} bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-bold uppercase tracking-wide text-slate-400">
                            New Feedback
                        </p>

                        <p class="mt-3 text-3xl font-bold tracking-tight text-slate-900">
                            {{ number_format($newFeedback) }}
                        </p>

                        <p class="mt-1 text-xs leading-5 text-slate-500">
                            Waiting for CS review
                        </p>

                    </div>

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-50 text-red-500">

                        <i class="fas fa-star"></i>

                    </span>

                </div>

            </a>

        </section>

        <section class="grid gap-6 xl:grid-cols-[minmax(0,1.5fr)_minmax(320px,0.5fr)]">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div class="flex items-start gap-3">

                        <span
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">

                            <i class="fas fa-chart-column"></i>

                        </span>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Complaint Status Overview
                            </h2>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Current distribution of complaints across the service workflow.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-4 sm:p-6">

                    <div class="h-[280px] sm:h-[320px]">
                        <canvas id="complaintStatusChart"></canvas>
                    </div>

                </div>

            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-100 px-5 py-5">

                    <div class="flex items-start justify-between gap-3">

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Requires Attention
                            </h2>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Feedback needing Customer Service action.
                            </p>

                        </div>

                        <span
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                            <i class="fas fa-triangle-exclamation"></i>

                        </span>

                    </div>

                </div>

                <div class="divide-y divide-slate-100">

                    @forelse ($attentionFeedback as $item)

                        <a
                            href="{{ route('customer-service.feedback.show', $item) }}"
                            class="block p-4 transition hover:bg-slate-50">

                            <div class="flex items-start gap-3">

                                <span
                                    class="mt-0.5 flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                    {{ $item->handling_status === 'New'
                                        ? 'bg-blue-50 text-blue-600'
                                        : 'bg-amber-50 text-amber-600' }}">

                                    <i class="fas {{ $item->handling_status === 'New' ? 'fa-envelope' : 'fa-arrow-rotate-right' }}"></i>

                                </span>

                                <div class="min-w-0 flex-1">

                                    <div class="flex items-start justify-between gap-2">

                                        <p class="truncate text-sm font-bold text-slate-800">
                                            {{ $item->complaint?->complaint_no ?? 'Feedback' }}
                                        </p>

                                        <span class="shrink-0 text-[10px] text-slate-400">
                                            {{ $item->created_at->diffForHumans() }}
                                        </span>

                                    </div>

                                    <p class="mt-1 truncate text-xs text-slate-500">
                                        {{ $item->consumer?->full_name ?? 'Consumer' }}
                                    </p>

                                    <span
                                        class="mt-2 inline-flex rounded-full px-2 py-1 text-[10px] font-bold
                                        {{ $item->handling_status === 'New'
                                            ? 'bg-blue-50 text-blue-700'
                                            : 'bg-amber-50 text-amber-700' }}">

                                        {{ $item->handling_status }}

                                    </span>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="px-5 py-12 text-center">

                            <span
                                class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">

                                <i class="fas fa-check"></i>

                            </span>

                            <p class="mt-3 text-sm font-bold text-slate-700">
                                No pending feedback actions
                            </p>

                            <p class="mt-1 text-xs leading-5 text-slate-400">
                                New or follow-up feedback will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

                @if ($attentionFeedback->count())

                    <div class="border-t border-slate-100 p-4">

                        <a
                            href="{{ route('customer-service.feedback.index', ['needs_attention' => 1]) }}"
                            class="flex items-center justify-center gap-2 text-xs font-bold text-sky-700 hover:text-sky-800">

                            View Feedback Queue

                            <i class="fas fa-arrow-right text-[10px]"></i>

                        </a>

                    </div>

                @endif

            </div>

        </section>

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="border-b border-slate-100 px-5 py-5 sm:px-6">

                <h2 class="font-bold text-slate-900">
                    Quick Actions
                </h2>

                <p class="mt-1 text-xs leading-5 text-slate-500">
                    Access frequently used Customer Service tasks.
                </p>

            </div>

            <div class="grid gap-3 p-4 sm:grid-cols-2 sm:p-6 xl:grid-cols-4">

                <a
                    href="{{ route('customer-service.consumers.create') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-sky-200 hover:bg-sky-50/50">

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600 transition group-hover:bg-sky-100">

                        <i class="fas fa-user-plus"></i>

                    </span>

                    <div class="min-w-0">

                        <p class="text-sm font-bold text-slate-800">
                            Register Consumer
                        </p>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Create a consumer account
                        </p>

                    </div>

                </a>

                <a
                    href="{{ route('customer-service.complaints.create') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-blue-200 hover:bg-blue-50/50">

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600">

                        <i class="fas fa-file-circle-plus"></i>

                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Record Complaint
                        </p>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Log a consumer concern
                        </p>

                    </div>

                </a>

                <a
                    href="{{ route('customer-service.complaint-verification.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-amber-200 hover:bg-amber-50/50">

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                        <i class="fas fa-shield-halved"></i>

                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Verify Complaints
                        </p>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Review pending submissions
                        </p>

                    </div>

                </a>

                <a
                    href="{{ route('customer-service.feedback.index') }}"
                    class="group flex items-center gap-4 rounded-2xl border border-slate-200 p-4 transition hover:border-cyan-200 hover:bg-cyan-50/50">

                    <span
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-cyan-50 text-cyan-600">

                        <i class="fas fa-star"></i>

                    </span>

                    <div>

                        <p class="text-sm font-bold text-slate-800">
                            Consumer Feedback
                        </p>

                        <p class="mt-0.5 text-xs text-slate-500">
                            Review service experience
                        </p>

                    </div>

                </a>

            </div>

        </section>

        <section class="grid gap-6 xl:grid-cols-2">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div>

                        <h2 class="font-bold text-slate-900">
                            Recent Consumers
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Latest consumer registrations
                        </p>

                    </div>

                    <a
                        href="{{ route('customer-service.consumers.index') }}"
                        class="shrink-0 text-xs font-bold text-sky-700 hover:text-sky-800">
                        View All
                    </a>

                </div>

                <div class="hidden md:block">

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-slate-100">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Consumer
                                    </th>

                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Account
                                    </th>

                                    <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Registered
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @forelse ($recentConsumers as $consumer)

                                    <tr class="transition hover:bg-slate-50">

                                        <td class="px-5 py-4">

                                            <p class="text-sm font-bold text-slate-800">
                                                {{ $consumer->full_name }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-400">
                                                {{ $consumer->phone ?: 'No phone number' }}
                                            </p>

                                        </td>

                                        <td class="px-5 py-4 text-sm font-medium text-slate-600">
                                            {{ $consumer->account_number ?: '—' }}
                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right">

                                            <p class="text-xs font-medium text-slate-600">
                                                {{ $consumer->created_at?->format('M d, Y') }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-slate-400">
                                                {{ $consumer->created_at?->format('h:i A') }}
                                            </p>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3" class="px-5 py-12 text-center text-sm text-slate-400">
                                            No consumer records found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="divide-y divide-slate-100 md:hidden">

                    @forelse ($recentConsumers as $consumer)

                        <div class="p-4">

                            <div class="flex items-start gap-3">

                                <span
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">

                                    <i class="fas fa-user"></i>

                                </span>

                                <div class="min-w-0 flex-1">

                                    <p class="truncate text-sm font-bold text-slate-800">
                                        {{ $consumer->full_name }}
                                    </p>

                                    <p class="mt-1 text-xs font-medium text-slate-500">
                                        {{ $consumer->account_number ?: 'No account number' }}
                                    </p>

                                    <div class="mt-3 flex flex-wrap items-center gap-x-4 gap-y-1 text-[11px] text-slate-400">

                                        <span>
                                            <i class="far fa-calendar mr-1"></i>
                                            {{ $consumer->created_at?->format('M d, Y') }}
                                        </span>

                                        @if ($consumer->phone)

                                            <span>
                                                <i class="fas fa-phone mr-1"></i>
                                                {{ $consumer->phone }}
                                            </span>

                                        @endif

                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="p-10 text-center text-sm text-slate-400">
                            No consumer records found.
                        </div>

                    @endforelse

                </div>

            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-5 sm:px-6">

                    <div>

                        <h2 class="font-bold text-slate-900">
                            Recent Complaints
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Latest concerns recorded in the system
                        </p>

                    </div>

                    <a
                        href="{{ route('customer-service.complaints.index') }}"
                        class="shrink-0 text-xs font-bold text-sky-700 hover:text-sky-800">
                        View All
                    </a>

                </div>

                <div class="hidden md:block">

                    <div class="overflow-x-auto">

                        <table class="min-w-full divide-y divide-slate-100">

                            <thead class="bg-slate-50">

                                <tr>

                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Complaint
                                    </th>

                                    <th class="px-5 py-3 text-left text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Type
                                    </th>

                                    <th class="px-5 py-3 text-right text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                        Status
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-100">

                                @forelse ($recentComplaints as $complaint)

                                    <tr class="transition hover:bg-slate-50">

                                        <td class="px-5 py-4">

                                            <a
                                                href="{{ route('customer-service.complaints.show', $complaint) }}"
                                                class="text-sm font-bold text-slate-800 transition hover:text-sky-700">

                                                {{ $complaint->complaint_no }}

                                            </a>

                                            <p class="mt-1 max-w-[250px] truncate text-xs text-slate-400">
                                                {{ $complaint->consumer?->full_name ?? $complaint->complainant_name ?? 'Walk-in Consumer' }}
                                            </p>

                                        </td>

                                        <td class="px-5 py-4">

                                            <p class="text-xs font-semibold text-slate-600">
                                                {{ $complaint->category?->name ?? '—' }}
                                            </p>

                                            <p class="mt-1 text-[10px] text-slate-400">
                                                {{ $complaint->division?->name ?? '—' }}
                                            </p>

                                        </td>

                                        <td class="whitespace-nowrap px-5 py-4 text-right">

                                            <span
                                                class="inline-flex rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $statusStyles[$complaint->status] ?? 'border-slate-200 bg-slate-100 text-slate-600' }}">

                                                {{ $complaint->status }}

                                            </span>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td colspan="3" class="px-5 py-12 text-center text-sm text-slate-400">
                                            No complaints found.
                                        </td>

                                    </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                <div class="divide-y divide-slate-100 md:hidden">

                    @forelse ($recentComplaints as $complaint)

                        <a
                            href="{{ route('customer-service.complaints.show', $complaint) }}"
                            class="block p-4 transition active:bg-slate-50">

                            <div class="flex items-start justify-between gap-3">

                                <div class="min-w-0">

                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    <p class="mt-1 truncate text-xs text-slate-500">
                                        {{ $complaint->category?->name ?? 'Complaint' }}
                                    </p>

                                </div>

                                <span
                                    class="shrink-0 rounded-full border px-2.5 py-1 text-[10px] font-bold {{ $statusStyles[$complaint->status] ?? 'border-slate-200 bg-slate-100 text-slate-600' }}">

                                    {{ $complaint->status }}

                                </span>

                            </div>

                            <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500">
                                {{ $complaint->description ?: 'No description provided.' }}
                            </p>

                            <div class="mt-3 flex items-center justify-between gap-3 text-[10px] text-slate-400">

                                <span class="truncate">
                                    <i class="fas fa-user mr-1"></i>
                                    {{ $complaint->consumer?->full_name ?? $complaint->complainant_name ?? 'Walk-in' }}
                                </span>

                                <span class="shrink-0">
                                    {{ $complaint->created_at?->format('M d') }}
                                </span>

                            </div>

                        </a>

                    @empty

                        <div class="p-10 text-center text-sm text-slate-400">
                            No complaints found.
                        </div>

                    @endforelse

                </div>

            </div>

        </section>

    </div>

@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const canvas = document.getElementById('complaintStatusChart');

            if (!canvas) {
                return;
            }

            new Chart(canvas, {
                type: 'bar',

                data: {
                    labels: @json(array_keys($complaintStats)),
                    datasets: [{
                        label: 'Complaints',
                        data: @json(array_values($complaintStats)),
                        backgroundColor: 'rgba(14, 165, 233, 0.72)',
                        borderColor: 'rgb(2, 132, 199)',
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                        maxBarThickness: 46
                    }]
                },

                options: {
                    responsive: true,
                    maintainAspectRatio: false,

                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            displayColors: false
                        }
                    },

                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#64748b',
                                font: {
                                    size: 11
                                }
                            }
                        },

                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0,
                                color: '#94a3b8'
                            },
                            grid: {
                                color: 'rgba(226, 232, 240, 0.7)'
                            },
                            border: {
                                display: false
                            }
                        }
                    }
                }
            });

        });
    </script>

@endpush
