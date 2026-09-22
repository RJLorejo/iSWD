@extends('customer-service.layouts.app')

@section('title', 'Complaints')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm text-gray-500">
                    Complaint Management
                </p>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Complaints
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage consumer complaints, reported problems, and service concerns.
                </p>

            </div>


            <a
                href="{{ route('customer-service.complaints.create') }}"
                class="inline-flex items-center justify-center gap-2
                       px-5 py-3 rounded-xl
                       bg-blue-600 text-white font-semibold
                       hover:bg-blue-700 transition shadow-sm
                       w-full sm:w-auto"
            >

                <i class="fas fa-plus"></i>

                New Complaint

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- FLASH MESSAGES --}}
        {{-- ========================================================= --}}

        @if (session('success'))

            <div class="rounded-2xl border border-green-200 bg-green-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-check text-green-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif


        @if (session('error'))

            <div class="rounded-2xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>

                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">

            {{-- TOTAL --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500">
                            Total
                        </p>

                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                            {{ $totalComplaints }}
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center shrink-0"
                    >

                        <i class="fas fa-file-lines"></i>

                    </div>

                </div>

            </div>


            {{-- PENDING --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500">
                            Pending
                        </p>

                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                            {{ $pendingComplaints }}
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                               bg-yellow-100 text-yellow-600
                               flex items-center justify-center shrink-0"
                    >

                        <i class="fas fa-clock"></i>

                    </div>

                </div>

            </div>


            {{-- IN PROGRESS --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500">
                            In Progress
                        </p>

                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                            {{ $inProgressComplaints }}
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                               bg-purple-100 text-purple-600
                               flex items-center justify-center shrink-0"
                    >

                        <i class="fas fa-spinner"></i>

                    </div>

                </div>

            </div>


            {{-- COMPLETED --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500">
                            Completed
                        </p>

                        <p class="text-xl sm:text-2xl font-bold text-gray-900 mt-1">
                            {{ $completedComplaints }}
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                               bg-green-100 text-green-600
                               flex items-center justify-center shrink-0"
                    >

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            <div class="px-4 sm:px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center"
                    >

                        <i class="fas fa-filter"></i>

                    </div>

                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Search & Filter
                        </h2>

                        <p class="text-sm text-gray-500">
                            Find complaints by number, complainant, description, or status.
                        </p>

                    </div>

                </div>

            </div>


            <form
                method="GET"
                action="{{ route('customer-service.complaints.index') }}"
                class="p-4 sm:p-6"
            >

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

                    {{-- SEARCH --}}

                    <div class="lg:col-span-7">

                        <label
                            for="search"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Search
                        </label>

                        <div class="relative">

                            <i
                                class="fas fa-magnifying-glass
                                       absolute left-4 top-1/2
                                       -translate-y-1/2 text-gray-400"
                            ></i>

                            <input
                                type="text"
                                name="search"
                                id="search"
                                value="{{ request('search') }}"
                                placeholder="Complaint no., complainant, complaint type, address..."
                                class="w-full pl-11 pr-4 py-3 rounded-xl
                                       border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500"
                            >

                        </div>

                    </div>


                    {{-- STATUS --}}

                    <div class="lg:col-span-3">

                        <label
                            for="status"
                            class="block text-sm font-medium text-gray-700 mb-2"
                        >
                            Status
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500"
                        >

                            <option value="">
                                All Statuses
                            </option>

                            @foreach (
                                [
                                    'Pending',
                                    'Verified',
                                    'Assigned',
                                    'In Progress',
                                    'Completed',
                                    'Closed',
                                    'Rejected'
                                ] as $status
                            )

                                <option
                                    value="{{ $status }}"
                                    @selected(request('status') === $status)
                                >
                                    {{ $status }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- BUTTONS --}}

                    <div class="lg:col-span-2 flex items-end gap-2">

                        <button
                            type="submit"
                            class="flex-1 inline-flex items-center justify-center gap-2
                                   px-4 py-3 rounded-xl
                                   bg-blue-600 text-white font-semibold
                                   hover:bg-blue-700 transition"
                        >

                            <i class="fas fa-filter"></i>

                            <span class="lg:hidden xl:inline">
                                Filter
                            </span>

                        </button>


                        <a
                            href="{{ route('customer-service.complaints.index') }}"
                            title="Clear Filters"
                            class="inline-flex items-center justify-center
                                   w-12 h-12 rounded-xl
                                   border border-gray-300
                                   text-gray-600
                                   hover:bg-gray-50 transition shrink-0"
                        >

                            <i class="fas fa-rotate-left"></i>

                        </a>

                    </div>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- COMPLAINT RECORDS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            {{-- HEADER --}}

            <div
                class="px-4 sm:px-6 py-5 border-b border-gray-100
                       flex flex-col sm:flex-row
                       sm:items-center sm:justify-between gap-2"
            >

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Complaint Records
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Consumer complaints currently recorded in the system.
                    </p>

                </div>

                <span
                    class="inline-flex items-center self-start sm:self-auto
                           px-3 py-1.5 rounded-full
                           bg-gray-100 text-gray-600
                           text-sm font-medium"
                >
                    {{ $complaints->total() }}
                    {{ Str::plural('complaint', $complaints->total()) }}
                </span>

            </div>


            {{-- ===================================================== --}}
            {{-- MOBILE / TABLET CARDS --}}
            {{-- ===================================================== --}}

            <div class="lg:hidden divide-y divide-gray-100">

                @forelse ($complaints as $complaint)

                    <div class="p-4 sm:p-5">

                        {{-- TOP --}}

                        <div class="flex items-start justify-between gap-3">

                            <div class="flex items-start gap-3 min-w-0">

                                <div
                                    class="w-11 h-11 rounded-xl
                                           bg-blue-50 text-blue-600
                                           flex items-center justify-center shrink-0"
                                >

                                    <i class="fas fa-file-circle-exclamation"></i>

                                </div>


                                <div class="min-w-0">

                                    <a
                                        href="{{ route('customer-service.complaints.show', $complaint) }}"
                                        class="font-bold text-gray-900 hover:text-blue-600"
                                    >
                                        {{ $complaint->complaint_no }}
                                    </a>

                                    <p class="text-xs text-gray-400 mt-1">

                                        <i class="far fa-clock mr-1"></i>

                                        {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                    </p>

                                </div>

                            </div>


                            {{-- STATUS --}}

                            @php
                                $statusClasses = match ($complaint->status) {
                                    'Pending' => 'bg-yellow-100 text-yellow-700',
                                    'Verified' => 'bg-blue-100 text-blue-700',
                                    'Assigned' => 'bg-purple-100 text-purple-700',
                                    'In Progress' => 'bg-indigo-100 text-indigo-700',
                                    'Completed' => 'bg-green-100 text-green-700',
                                    'Closed' => 'bg-gray-100 text-gray-700',
                                    'Rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700',
                                };
                            @endphp

                            <span
                                class="inline-flex items-center
                                       px-2.5 py-1 rounded-full
                                       text-xs font-semibold shrink-0
                                       {{ $statusClasses }}"
                            >
                                {{ $complaint->status }}
                            </span>

                        </div>


                        {{-- DETAILS --}}

                        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- COMPLAINANT --}}

                            <div>

                                <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                                    Complainant
                                </p>

                                <p class="text-sm font-medium text-gray-900 mt-1">

                                    @if ($complaint->consumer)

                                        {{ $complaint->consumer->full_name }}

                                    @elseif ($complaint->complainant_name)

                                        {{ $complaint->complainant_name }}

                                    @else

                                        Walk-in / Unregistered

                                    @endif

                                </p>

                            </div>


                            {{-- DIVISION --}}

                            <div>

                                <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                                    Division
                                </p>

                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $complaint->division?->name ?? '—' }}
                                </p>

                            </div>


                            {{-- TYPE --}}

                            <div>

                                <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                                    Complaint Type
                                </p>

                                <p class="text-sm font-medium text-gray-900 mt-1">
                                    {{ $complaint->category?->name ?? '—' }}
                                </p>

                                @if ($complaint->category?->code)

                                    <p class="text-xs text-gray-500 mt-0.5">
                                        {{ $complaint->category->code }}
                                    </p>

                                @endif

                            </div>


                            {{-- ADDRESS --}}

                            <div>

                                <p class="text-xs uppercase tracking-wide text-gray-400 font-medium">
                                    Problem Address
                                </p>

                                <p class="text-sm text-gray-700 mt-1 break-words">
                                    {{ $complaint->address ?: 'No address recorded.' }}
                                </p>

                            </div>

                        </div>


                        {{-- DESCRIPTION --}}

                        <div class="mt-4 p-3 rounded-xl bg-gray-50">

                            <p class="text-xs text-gray-500 mb-1">
                                Description
                            </p>

                            <p class="text-sm text-gray-700 line-clamp-2">
                                {{ $complaint->description }}
                            </p>

                        </div>


                        {{-- ACTIONS --}}

                        <div class="mt-4 flex items-center gap-2">

                            <a
                                href="{{ route('customer-service.complaints.show', $complaint) }}"
                                class="flex-1 inline-flex items-center justify-center gap-2
                                       px-4 py-2.5 rounded-xl
                                       bg-blue-50 text-blue-700
                                       font-medium hover:bg-blue-100 transition"
                            >

                                <i class="fas fa-eye"></i>

                                View

                            </a>


                            @if (in_array($complaint->status, ['Pending', 'Verified']))

                                <a
                                    href="{{ route('customer-service.complaints.edit', $complaint) }}"
                                    class="inline-flex items-center justify-center
                                           w-11 h-11 rounded-xl
                                           bg-yellow-50 text-yellow-600
                                           hover:bg-yellow-100 transition"
                                    title="Edit Complaint"
                                >

                                    <i class="fas fa-pen-to-square"></i>

                                </a>

                            @endif

                        </div>

                    </div>

                @empty

                    <div class="px-5 py-16 text-center">

                        <div
                            class="w-16 h-16 mx-auto rounded-2xl
                                   bg-gray-100 text-gray-400
                                   flex items-center justify-center"
                        >

                            <i class="fas fa-file-circle-exclamation text-2xl"></i>

                        </div>

                        <h3 class="font-semibold text-gray-900 mt-4">
                            No complaints found
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            No complaint records match your current filters.
                        </p>

                        @if (request()->hasAny(['search', 'status']))

                            <a
                                href="{{ route('customer-service.complaints.index') }}"
                                class="inline-flex mt-4 text-sm text-blue-600 font-medium"
                            >
                                Clear filters
                            </a>

                        @endif

                    </div>

                @endforelse

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP TABLE --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-100">

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-5 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Complaint
                            </th>

                            <th
                                class="px-5 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Complainant
                            </th>

                            <th
                                class="px-5 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Classification
                            </th>

                            <th
                                class="px-5 py-4 text-left
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Problem Address
                            </th>

                            <th
                                class="px-5 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Status
                            </th>

                            <th
                                class="px-5 py-4 text-center
                                       text-xs font-semibold text-gray-500
                                       uppercase tracking-wider"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($complaints as $complaint)

                            <tr class="hover:bg-gray-50/70 transition">

                                {{-- COMPLAINT --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                   bg-blue-50 text-blue-600
                                                   flex items-center justify-center shrink-0"
                                        >

                                            <i class="fas fa-file-circle-exclamation"></i>

                                        </div>

                                        <div class="min-w-0">

                                            <a
                                                href="{{ route('customer-service.complaints.show', $complaint) }}"
                                                class="font-semibold text-gray-900 hover:text-blue-600"
                                            >
                                                {{ $complaint->complaint_no }}
                                            </a>

                                            <p class="text-sm text-gray-500 mt-1 max-w-[240px] truncate">
                                                {{ Str::limit($complaint->description, 65) }}
                                            </p>

                                            <p class="text-xs text-gray-400 mt-1">

                                                <i class="far fa-clock mr-1"></i>

                                                {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- COMPLAINANT --}}

                                <td class="px-5 py-4">

                                    @if ($complaint->consumer)

                                        <p class="font-medium text-gray-900">
                                            {{ $complaint->consumer->full_name }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            <i class="fas fa-id-card mr-1"></i>

                                            {{ $complaint->consumer->consumer_no ?? 'Registered' }}

                                        </p>

                                    @else

                                        <p class="font-medium text-gray-900">
                                            {{ $complaint->complainant_name ?: 'Walk-in Complainant' }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">
                                            Walk-in / Unregistered
                                        </p>

                                    @endif

                                </td>


                                {{-- CLASSIFICATION --}}

                                <td class="px-5 py-4">

                                    <p class="text-xs text-gray-500">
                                        {{ $complaint->division?->name ?? 'No division' }}
                                    </p>

                                    <p class="font-medium text-gray-900 mt-1">
                                        {{ $complaint->category?->name ?? '—' }}
                                    </p>

                                    @if ($complaint->category?->code)

                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $complaint->category->code }}
                                        </p>

                                    @endif

                                </td>


                                {{-- ADDRESS --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-start gap-2 max-w-xs">

                                        <i class="fas fa-location-dot text-green-600 mt-1 shrink-0"></i>

                                        <div class="min-w-0">

                                            <p class="text-sm text-gray-700 line-clamp-2">
                                                {{ $complaint->address ?: 'No address recorded.' }}
                                            </p>

                                            @if ($complaint->landmark)

                                                <p class="text-xs text-gray-500 mt-1 truncate">
                                                    Landmark: {{ $complaint->landmark }}
                                                </p>

                                            @endif

                                        </div>

                                    </div>

                                </td>


                                {{-- STATUS --}}

                                <td class="px-5 py-4 text-center">

                                    @php
                                        $statusClasses = match ($complaint->status) {
                                            'Pending' => 'bg-yellow-100 text-yellow-700',
                                            'Verified' => 'bg-blue-100 text-blue-700',
                                            'Assigned' => 'bg-purple-100 text-purple-700',
                                            'In Progress' => 'bg-indigo-100 text-indigo-700',
                                            'Completed' => 'bg-green-100 text-green-700',
                                            'Closed' => 'bg-gray-100 text-gray-700',
                                            'Rejected' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp

                                    <span
                                        class="inline-flex items-center justify-center
                                               px-3 py-1.5 rounded-full
                                               text-xs font-semibold
                                               whitespace-nowrap
                                               {{ $statusClasses }}"
                                    >
                                        {{ $complaint->status }}
                                    </span>

                                </td>


                                {{-- ACTIONS --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-center gap-2">

                                        <a
                                            href="{{ route('customer-service.complaints.show', $complaint) }}"
                                            class="w-9 h-9 rounded-lg
                                                   bg-blue-50 text-blue-600
                                                   flex items-center justify-center
                                                   hover:bg-blue-100 transition"
                                            title="View Complaint"
                                        >

                                            <i class="fas fa-eye"></i>

                                        </a>


                                        @if (in_array($complaint->status, ['Pending', 'Verified']))

                                            <a
                                                href="{{ route('customer-service.complaints.edit', $complaint) }}"
                                                class="w-9 h-9 rounded-lg
                                                       bg-yellow-50 text-yellow-600
                                                       flex items-center justify-center
                                                       hover:bg-yellow-100 transition"
                                                title="Edit Complaint"
                                            >

                                                <i class="fas fa-pen-to-square"></i>

                                            </a>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-16 text-center">

                                    <div
                                        class="w-16 h-16 mx-auto rounded-2xl
                                               bg-gray-100 text-gray-400
                                               flex items-center justify-center"
                                    >

                                        <i class="fas fa-file-circle-exclamation text-2xl"></i>

                                    </div>

                                    <h3 class="font-semibold text-gray-900 mt-4">
                                        No complaints found
                                    </h3>

                                    <p class="text-sm text-gray-500 mt-1">
                                        No complaint records match your current filters.
                                    </p>

                                    @if (request()->hasAny(['search', 'status']))

                                        <a
                                            href="{{ route('customer-service.complaints.index') }}"
                                            class="inline-flex mt-4 text-sm text-blue-600 font-medium"
                                        >
                                            Clear filters
                                        </a>

                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- PAGINATION --}}

            @if ($complaints->hasPages())

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>

            @endif

        </div>

    </div>

@endsection
