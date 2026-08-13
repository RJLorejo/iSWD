@extends('customer-service.layouts.app')

@section('title', 'Complaints')

@section('content')

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Complaints
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Manage consumer complaints, reported problems, and service issues.
            </p>
        </div>

        <a href="{{ route('customer-service.complaints.create') }}"
            class="inline-flex items-center justify-center gap-2 px-5 py-2.5
                   rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
                   text-white font-medium shadow-sm hover:shadow-md
                   hover:from-sky-800 hover:via-blue-800 hover:to-cyan-700
                   transition">

            <i class="fas fa-plus"></i>

            <span>New Complaint</span>

        </a>

    </div>


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-5 mb-6">

        <x-admin.stat-card title="Total Complaints" :value="$totalComplaints" icon="fas fa-file-alt" color="blue" />

        <x-admin.stat-card title="Pending" :value="$pendingComplaints" icon="fas fa-clock" color="yellow" />

        <x-admin.stat-card title="In Progress" :value="$inProgressComplaints" icon="fas fa-spinner" color="purple" />

        <x-admin.stat-card title="Completed" :value="$completedComplaints" icon="fas fa-check-circle" color="green" />

        <x-admin.stat-card title="Critical" :value="$criticalComplaints" icon="fas fa-exclamation-triangle" color="red" />

    </div>


    {{-- ========================================================= --}}
    {{-- SEARCH / FILTER --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 mb-6">

        <form method="GET" action="{{ route('customer-service.complaints.index') }}"
            class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

            {{-- Search --}}
            <div class="lg:col-span-2 relative">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Search
                </label>

                <div class="relative">

                    <i
                        class="fas fa-search absolute left-3 top-1/2
                        -translate-y-1/2 text-gray-400"></i>

                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Complaint no., consumer, subject..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl
                               border-gray-300 focus:border-blue-500
                               focus:ring-blue-500">

                </div>

            </div>


            {{-- Status --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Status
                </label>

                <select name="status"
                    class="w-full rounded-xl border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                    <option value="">All Status</option>

                    @foreach (['Pending', 'Verified', 'Assigned', 'In Progress', 'Completed', 'Closed', 'Rejected'] as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>
                            {{ $status }}
                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Priority --}}
            <div>

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Priority
                </label>

                <select name="priority"
                    class="w-full rounded-xl border-gray-300
                           focus:border-blue-500 focus:ring-blue-500">

                    <option value="">All Priority</option>

                    @foreach (['Low', 'Medium', 'High', 'Critical'] as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>
                            {{ $priority }}
                        </option>
                    @endforeach

                </select>

            </div>


            {{-- Buttons --}}
            <div class="flex items-end gap-2">

                <button type="submit"
                    class="flex-1 inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
                           text-white font-medium hover:opacity-95 transition">

                    <i class="fas fa-filter"></i>

                    Filter

                </button>


                <a href="{{ route('customer-service.complaints.index') }}"
                    class="inline-flex items-center justify-center
                           px-4 py-2.5 rounded-xl
                           border border-gray-300 text-gray-600
                           hover:bg-gray-50 transition"
                    title="Clear Filters">

                    <i class="fas fa-rotate-left"></i>

                </a>

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- COMPLAINT TABLE --}}
    {{-- ========================================================= --}}

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">

            <div class="flex items-center justify-between">

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Complaint Records
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        List of reported consumer service problems.
                    </p>

                </div>

                <div class="text-sm text-gray-500">

                    {{ $complaints->total() }}
                    {{ Str::plural('complaint', $complaints->total()) }}

                </div>

            </div>

        </div>


        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-100">

                <thead class="bg-gray-50">

                    <tr>

                        <th
                            class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Complaint
                        </th>

                        <th
                            class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Consumer
                        </th>

                        <th
                            class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Category
                        </th>

                        <th
                            class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Location
                        </th>

                        <th
                            class="px-6 py-4 text-center text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Priority
                        </th>

                        <th
                            class="px-6 py-4 text-center text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Status
                        </th>

                        <th
                            class="px-6 py-4 text-center text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse($complaints as $complaint)
                        <tr class="hover:bg-gray-50 transition">

                            {{-- Complaint --}}
                            <td class="px-6 py-4">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="w-10 h-10 rounded-xl bg-blue-50
                                                text-blue-600 flex items-center
                                                justify-center shrink-0">

                                        <i class="fas fa-file-circle-exclamation"></i>

                                    </div>

                                    <div class="min-w-0">

                                        <div class="font-semibold text-gray-900">
                                            {{ $complaint->complaint_no }}
                                        </div>

                                        <div class="text-sm text-gray-500 truncate max-w-xs">
                                            {{ $complaint->subject }}
                                        </div>

                                        <div class="text-xs text-gray-400 mt-1">

                                            <i class="far fa-clock mr-1"></i>

                                            {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                        </div>

                                    </div>

                                </div>

                            </td>


                            {{-- Consumer --}}
                            <td class="px-6 py-4">

                                @if ($complaint->consumer)
                                    <div class="font-medium text-gray-900">
                                        {{ $complaint->consumer->full_name }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $complaint->consumer->consumer_no }}
                                    </div>
                                @elseif ($complaint->complainant_name)
                                    <div class="font-medium text-gray-900">
                                        {{ $complaint->complainant_name }}
                                    </div>

                                    <div class="text-xs text-amber-600">
                                        <i class="fas fa-person-walking mr-1"></i>
                                        Walk-in / Unregistered
                                    </div>

                                    @if ($complaint->complainant_phone)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-phone mr-1"></i>
                                            {{ $complaint->complainant_phone }}
                                        </div>
                                    @endif
                                @else
                                    <span class="text-gray-400">
                                        No complainant information
                                    </span>
                                @endif

                            </td>


                            {{-- Category --}}
                            <td class="px-6 py-4">

                                @if ($complaint->category)
                                    <div class="font-medium text-gray-900">

                                        {{ $complaint->category->name }}

                                    </div>

                                    <div class="text-xs text-gray-500 mt-1">

                                        {{ $complaint->category->code }}

                                    </div>
                                @else
                                    <span class="text-gray-400">
                                        —
                                    </span>
                                @endif

                            </td>


                            {{-- Location --}}
                            <td class="px-6 py-4">

                                <div class="flex items-start gap-2 max-w-xs">

                                    <i class="fas fa-location-dot text-gray-400 mt-1"></i>

                                    <div>

                                        <div class="text-sm text-gray-700">
                                            {{ $complaint->address }}
                                        </div>

                                        @if ($complaint->landmark)
                                            <div class="text-xs text-gray-500 mt-1">

                                                <i class="fas fa-landmark mr-1"></i>

                                                {{ $complaint->landmark }}

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </td>


                            {{-- Priority --}}
                            <td class="px-6 py-4 text-center">

                                @switch($complaint->priority)
                                    @case('Low')
                                        <x-admin.badge color="green">
                                            <i class="fas fa-arrow-down mr-1"></i>
                                            Low
                                        </x-admin.badge>
                                    @break

                                    @case('Medium')
                                        <x-admin.badge color="yellow">
                                            <i class="fas fa-minus mr-1"></i>
                                            Medium
                                        </x-admin.badge>
                                    @break

                                    @case('High')
                                        <x-admin.badge color="orange">
                                            <i class="fas fa-arrow-up mr-1"></i>
                                            High
                                        </x-admin.badge>
                                    @break

                                    @case('Critical')
                                        <x-admin.badge color="red">
                                            <i class="fas fa-triangle-exclamation mr-1"></i>
                                            Critical
                                        </x-admin.badge>
                                    @break

                                    @default
                                        <x-admin.badge color="gray">
                                            {{ $complaint->priority }}
                                        </x-admin.badge>
                                @endswitch

                            </td>


                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">

                                @switch($complaint->status)
                                    @case('Pending')
                                        <x-admin.badge color="yellow">
                                            <i class="fas fa-clock mr-1"></i>
                                            Pending
                                        </x-admin.badge>
                                    @break

                                    @case('Verified')
                                        <x-admin.badge color="blue">
                                            <i class="fas fa-circle-check mr-1"></i>
                                            Verified
                                        </x-admin.badge>
                                    @break

                                    @case('Assigned')
                                        <x-admin.badge color="purple">
                                            <i class="fas fa-user-check mr-1"></i>
                                            Assigned
                                        </x-admin.badge>
                                    @break

                                    @case('In Progress')
                                        <x-admin.badge color="purple">
                                            <i class="fas fa-spinner mr-1"></i>
                                            In Progress
                                        </x-admin.badge>
                                    @break

                                    @case('Completed')
                                        <x-admin.badge color="green">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            Completed
                                        </x-admin.badge>
                                    @break

                                    @case('Closed')
                                        <x-admin.badge color="gray">
                                            <i class="fas fa-lock mr-1"></i>
                                            Closed
                                        </x-admin.badge>
                                    @break

                                    @case('Rejected')
                                        <x-admin.badge color="red">
                                            <i class="fas fa-xmark-circle mr-1"></i>
                                            Rejected
                                        </x-admin.badge>
                                    @break

                                    @default
                                        <x-admin.badge color="gray">
                                            {{ $complaint->status }}
                                        </x-admin.badge>
                                @endswitch

                            </td>


                            {{-- Actions --}}
                            <td class="px-6 py-4">

                                <div class="flex items-center justify-center gap-2">

                                    {{-- View --}}
                                    <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                                        class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600
                                               flex items-center justify-center
                                               hover:bg-blue-100 transition"
                                        title="View Complaint">

                                        <i class="fas fa-eye"></i>

                                    </a>


                                    {{-- Edit --}}
                                    <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                                        class="w-9 h-9 rounded-lg bg-yellow-50 text-yellow-600
                                               flex items-center justify-center
                                               hover:bg-yellow-100 transition"
                                        title="Edit Complaint">

                                        <i class="fas fa-pen-to-square"></i>

                                    </a>


                                    {{-- Delete --}}
                                    <form action="{{ route('customer-service.complaints.destroy', $complaint) }}"
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this complaint?');">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="w-9 h-9 rounded-lg bg-red-50 text-red-600
                                                   flex items-center justify-center
                                                   hover:bg-red-100 transition"
                                            title="Delete Complaint">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="px-6 py-16 text-center">

                                    <div class="flex flex-col items-center">

                                        <div
                                            class="w-16 h-16 rounded-2xl bg-gray-100
                                                text-gray-400 flex items-center
                                                justify-center mb-4">

                                            <i class="fas fa-file-circle-exclamation text-2xl"></i>

                                        </div>

                                        <h3 class="font-semibold text-gray-900">
                                            No complaints found
                                        </h3>

                                        <p class="text-sm text-gray-500 mt-1">
                                            No complaint records match your current filters.
                                        </p>

                                        @if (request()->hasAny(['search', 'status', 'priority']))
                                            <a href="{{ route('customer-service.complaints.index') }}"
                                                class="mt-4 text-sm text-blue-600 hover:text-blue-700 font-medium">

                                                Clear filters

                                            </a>
                                        @endif

                                    </div>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            @if ($complaints->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>
            @endif

        </div>

    @endsection
