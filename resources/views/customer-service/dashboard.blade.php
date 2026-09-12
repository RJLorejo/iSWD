@extends('customer-service.layouts.app')

@section('title', 'Customer Service Dashboard')

@php
    use Illuminate\Support\Str;
@endphp

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <x-form.page-header title=""
            subtitle="Manage consumers and customer complaints." />

        {{-- STATISTICS --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            {{-- TOTAL CONSUMERS --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>
                        <p class="text-sm font-medium text-slate-500">
                            Total Consumers
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $totalConsumers ?? 0 }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-500">
                            Registered consumers
                        </p>
                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-blue-50
                            flex items-center justify-center">

                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110 8 4 4 0 010-8zm6 4a3 3 0 100-6 3 3 0 000 6z" />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- NEW CONSUMERS --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            New Today
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $newConsumers ?? 0 }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-500">
                            Registered today
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-emerald-50
                            flex items-center justify-center">

                        <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- ACTIVE CONNECTIONS --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Active Connections
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $activeConnections ?? 0 }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-500">
                            Active water connections
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-cyan-50
                            flex items-center justify-center">

                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3.5C12 3.5 6 10 6 14a6 6 0 0012 0c0-4-6-10.5-6-10.5z" />

                        </svg>

                    </div>

                </div>

            </div>


            {{-- PENDING COMPLAINTS --}}
            <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Pending Complaints
                        </p>

                        <h2 class="mt-2 text-3xl font-bold text-slate-900">
                            {{ $pendingComplaints ?? 0 }}
                        </h2>

                        <p class="mt-2 text-xs text-slate-500">
                            Require verification
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-xl bg-amber-50
                            flex items-center justify-center">

                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.72 3h17.62a2 2 0 001.72-3l-8.82-15a2 2 0 00-3.42 0z" />

                        </svg>

                    </div>

                </div>

            </div>

        </div>


        {{-- QUICK ACTIONS --}}
        <div class="bg-white rounded-2xl border border-slate-200
                shadow-sm p-6">

            <div class="flex items-center justify-between mb-5">

                <div>

                    <h3 class="text-lg font-semibold text-slate-900">
                        Quick Actions
                    </h3>

                    <p class="text-sm text-slate-500 mt-1">
                        Frequently used customer service functions.
                    </p>

                </div>

            </div>


            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- REGISTER CONSUMER --}}
                <a href="{{ route('customer-service.consumers.create') }}"
                    class="group flex items-center gap-4 p-4 rounded-xl
                      border border-slate-200
                      hover:border-blue-300 hover:bg-blue-50
                      transition">

                    <div
                        class="w-11 h-11 rounded-lg bg-blue-100
                            flex items-center justify-center
                            group-hover:bg-blue-200 transition">

                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />

                        </svg>

                    </div>

                    <div>

                        <p class="font-semibold text-slate-900">
                            Register Consumer
                        </p>

                        <p class="text-xs text-slate-500">
                            Add new consumer
                        </p>

                    </div>

                </a>


                {{-- VIEW CONSUMERS --}}
                <a href="{{ route('customer-service.consumers.index') }}"
                    class="group flex items-center gap-4 p-4 rounded-xl
                      border border-slate-200
                      hover:border-cyan-300 hover:bg-cyan-50
                      transition">

                    <div
                        class="w-11 h-11 rounded-lg bg-cyan-100
                            flex items-center justify-center">

                        <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H4v-2a4 4 0 014-4h1m4-8a4 4 0 110 8 4 4 0 010-8z" />

                        </svg>

                    </div>

                    <div>

                        <p class="font-semibold text-slate-900">
                            Consumers
                        </p>

                        <p class="text-xs text-slate-500">
                            Manage consumer records
                        </p>

                    </div>

                </a>


                {{-- COMPLAINTS --}}
                <a href="{{ route('customer-service.complaints.index') }}"
                    class="group flex items-center gap-4 p-4 rounded-xl
                      border border-slate-200
                      hover:border-amber-300 hover:bg-amber-50
                      transition">

                    <div
                        class="w-11 h-11 rounded-lg bg-amber-100
                            flex items-center justify-center">

                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 10h8M8 14h5m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />

                        </svg>

                    </div>

                    <div>

                        <p class="font-semibold text-slate-900">
                            Complaints
                        </p>

                        <p class="text-xs text-slate-500">
                            View complaint queue
                        </p>

                    </div>

                </a>


                {{-- PENDING --}}
                <a href="{{ route('customer-service.complaints.index', ['status' => 'Pending']) }}"
                    class="group flex items-center gap-4 p-4 rounded-xl
                      border border-slate-200
                      hover:border-red-300 hover:bg-red-50
                      transition">

                    <div
                        class="w-11 h-11 rounded-lg bg-red-100
                            flex items-center justify-center">

                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v4m0 4h.01M10.29 3.86l-8.82 15a2 2 0 001.72 3h17.62a2 2 0 001.72-3l-8.82-15a2 2 0 00-3.42 0z" />

                        </svg>

                    </div>

                    <div>

                        <p class="font-semibold text-slate-900">
                            Pending Verification
                        </p>

                        <p class="text-xs text-slate-500">
                            Review complaints
                        </p>

                    </div>

                </a>

            </div>

        </div>


        {{-- MAIN CONTENT --}}
        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">

            {{-- RECENT CONSUMERS --}}
            <div class="bg-white rounded-2xl border border-slate-200
                    shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-200
                        flex items-center justify-between">

                    <div>

                        <h3 class="font-semibold text-slate-900">
                            Recent Consumers
                        </h3>

                        <p class="text-xs text-slate-500 mt-1">
                            Recently registered consumers
                        </p>

                    </div>

                    <a href="{{ route('customer-service.consumers.index') }}"
                        class="text-sm font-medium text-blue-600
                          hover:text-blue-700">

                        View All

                    </a>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($recentConsumers ?? [] as $consumer)
                        <div class="px-6 py-4 flex items-center
                                justify-between">

                            <div>

                                <p class="font-medium text-slate-900">

                                    {{ $consumer->first_name }}
                                    {{ $consumer->last_name }}

                                </p>

                                <p class="text-xs text-slate-500 mt-1">

                                    {{ $consumer->consumer_no }}

                                </p>

                            </div>

                            <div class="text-right">

                                <p class="text-xs text-slate-500">

                                    {{ $consumer->created_at?->format('M d, Y') }}

                                </p>

                            </div>

                        </div>

                    @empty

                        <div class="px-6 py-10 text-center">

                            <p class="text-sm text-slate-500">
                                No consumers registered yet.
                            </p>

                        </div>
                    @endforelse

                </div>

            </div>


            {{-- RECENT COMPLAINTS --}}
            <div class="bg-white rounded-2xl border border-slate-200
                    shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-200
                        flex items-center justify-between">

                    <div>

                        <h3 class="font-semibold text-slate-900">
                            Recent Complaints
                        </h3>

                        <p class="text-xs text-slate-500 mt-1">
                            Latest customer complaints
                        </p>

                    </div>

                    <a href="{{ route('customer-service.complaints.index') }}"
                        class="text-sm font-medium text-blue-600
                          hover:text-blue-700">

                        View All

                    </a>

                </div>


                <div class="divide-y divide-slate-100">

                    @forelse($recentComplaints ?? [] as $complaint)
                        <div class="px-6 py-4 flex items-center
                                justify-between">

                            <div>

                                <p class="font-medium text-slate-900">

                                    {{ $complaint->complaint_number ?? 'Complaint' }}

                                </p>

                                <p class="text-xs text-slate-500 mt-1">

                                    {{ $complaint->description ? Str::limit($complaint->description, 45) : 'No description' }}

                                </p>

                            </div>

                            <span
                                class="px-2.5 py-1 rounded-full
                                     text-xs font-medium
                                     bg-amber-50 text-amber-700">

                                {{ $complaint->status ?? 'Pending' }}

                            </span>

                        </div>

                    @empty

                        <div class="px-6 py-10 text-center">

                            <p class="text-sm text-slate-500">
                                No complaints found.
                            </p>

                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection
