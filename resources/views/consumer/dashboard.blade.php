@extends('consumer.layouts.app')

@section('title', 'Consumer Dashboard')

@section('content')

    <div class="space-y-8">

        {{-- HERO --}}
        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 p-8 lg:p-10 text-white shadow-lg">

            <div class="relative z-10 max-w-3xl">

                <p class="text-sm font-medium text-sky-100">
                    Sagay Water District
                </p>

                <h1 class="mt-2 text-3xl lg:text-4xl font-bold tracking-tight">
                    Welcome, {{ $consumer->first_name ?? $consumer->full_name }}
                </h1>

                <p class="mt-3 text-sky-100 text-base lg:text-lg">
                    How can we help with your water service today?
                </p>

                <div class="mt-6 flex flex-wrap gap-3">

                    <a href="{{ route('consumer.complaints.create') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-3 font-semibold text-sky-700 shadow hover:bg-sky-50 transition">
                        <i class="fas fa-plus-circle"></i>
                        Submit a Complaint
                    </a>

                    <a href="#"
                        class="inline-flex items-center gap-2 rounded-xl border border-white/30 bg-white/10 px-5 py-3 font-semibold text-white backdrop-blur hover:bg-white/20 transition">
                        <i class="fas fa-robot"></i>
                        Ask AI Assistant
                    </a>

                </div>

            </div>

            <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-white/10"></div>
            <div class="absolute -bottom-24 right-24 h-72 w-72 rounded-full bg-cyan-300/10"></div>

        </div>


        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">

            {{-- Active --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Active Complaints
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $activeComplaints }}
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                        <i class="fas fa-file-circle-exclamation text-lg"></i>
                    </div>

                </div>

            </div>


            {{-- Pending --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Pending Review
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $pendingComplaints }}
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                        <i class="fas fa-clock text-lg"></i>
                    </div>

                </div>

            </div>


            {{-- Completed --}}
            <div class="bg-white rounded-2xl border shadow-sm p-6">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Completed
                        </p>

                        <p class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $completedComplaints }}
                        </p>

                    </div>

                    <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                        <i class="fas fa-circle-check text-lg"></i>
                    </div>

                </div>

            </div>

        </div>


        {{-- RECENT COMPLAINTS --}}
        <div class="bg-white rounded-2xl border shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">

                <div>

                    <h2 class="text-xl font-bold text-slate-900">
                        My Recent Complaints
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Track the status of your submitted service requests.
                    </p>

                </div>

                <a href="{{ route('consumer.complaints.index') }}"
                    class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                    View All
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>

            </div>


            @forelse($recentComplaints as $complaint)
                <a href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="block px-6 py-5 border-b last:border-b-0 hover:bg-slate-50 transition">

                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

                        <div class="flex items-start gap-4">

                            <div
                                class="w-11 h-11 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">

                                <i class="fas fa-droplet"></i>

                            </div>

                            <div>

                                <p class="font-bold text-slate-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $complaint->subject }}
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    {{ optional($complaint->category)->category_name ?? 'Water Service Concern' }}
                                    •
                                    {{ $complaint->created_at->format('M d, Y') }}
                                </p>

                            </div>

                        </div>


                        <div class="flex items-center gap-4">

                            @php
                                $statusClasses = [
                                    'Pending' => 'bg-amber-100 text-amber-700',
                                    'Verified' => 'bg-blue-100 text-blue-700',
                                    'Assigned' => 'bg-indigo-100 text-indigo-700',
                                    'In Progress' => 'bg-sky-100 text-sky-700',
                                    'Completed' => 'bg-emerald-100 text-emerald-700',
                                    'Closed' => 'bg-slate-100 text-slate-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp

                            <span
                                class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold {{ $statusClasses[$complaint->status] ?? 'bg-slate-100 text-slate-700' }}">

                                {{ $complaint->status }}

                            </span>

                            <i class="fas fa-chevron-right text-slate-400"></i>

                        </div>

                    </div>

                </a>

            @empty

                <div class="px-6 py-14 text-center">

                    <div class="mx-auto w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center">

                        <i class="fas fa-file-circle-question text-xl"></i>

                    </div>

                    <h3 class="mt-4 font-bold text-slate-900">
                        No complaints yet
                    </h3>

                    <p class="mt-1 text-sm text-slate-500">
                        Submit a water service complaint when you need assistance.
                    </p>

                    <a href="{{ route('consumer.complaints.create') }}"
                        class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                        <i class="fas fa-plus"></i>
                        Submit Complaint
                    </a>

                </div>
            @endforelse

        </div>


        {{-- AI ASSISTANT --}}
        <div class="bg-white rounded-2xl border shadow-sm p-6 lg:p-8">

            <div class="flex flex-col md:flex-row md:items-center gap-6">

                <div
                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-sky-100 to-cyan-100 text-sky-700 flex items-center justify-center shrink-0">

                    <i class="fas fa-robot text-xl"></i>

                </div>

                <div class="flex-1">

                    <h2 class="text-xl font-bold text-slate-900">
                        AI Water Service Assistant
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Get initial answers and helpful suggestions about common
                        water service concerns.
                    </p>

                </div>

                <a href="#"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800 transition">
                    <i class="fas fa-comments"></i>
                    Ask Assistant
                </a>

            </div>

        </div>

    </div>

@endsection
