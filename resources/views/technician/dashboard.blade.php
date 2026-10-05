@extends('technician.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-2 text-sm font-medium text-sky-600">

                    <i class="fas fa-screwdriver-wrench"></i>

                    Maintenance Operations

                </div>

                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                    Dashboard
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Welcome back, {{ auth()->user()->first_name ?? 'Technician' }}.
                    Here is your current maintenance workload.
                </p>

            </div>


            @if ($activeCount > 0)

                <a
                    href="{{ route('technician.complaints.index', ['scope' => 'active']) }}"
                    class="inline-flex items-center gap-2
                           px-3.5 py-2 rounded-xl
                           bg-sky-50 border border-sky-100
                           text-sky-700 text-sm font-medium
                           hover:bg-sky-100 transition"
                >

                    <span class="relative flex h-2.5 w-2.5">

                        <span
                            class="animate-ping absolute inline-flex
                                   h-full w-full rounded-full
                                   bg-sky-400 opacity-75"
                        ></span>

                        <span
                            class="relative inline-flex rounded-full
                                   h-2.5 w-2.5 bg-sky-600"
                        ></span>

                    </span>

                    {{ $activeCount }}

                    active
                    {{ $activeCount === 1 ? 'complaint' : 'complaints' }}

                </a>

            @else

                <div
                    class="inline-flex items-center gap-2
                           px-3.5 py-2 rounded-xl
                           bg-green-50 border border-green-100
                           text-green-700 text-sm font-medium"
                >

                    <i class="fas fa-circle-check"></i>

                    No active work

                </div>

            @endif

        </div>


        @if (session('success'))

            <div
                class="rounded-xl border border-green-200
                       bg-green-50 px-4 py-3
                       text-sm text-green-800
                       flex items-start gap-3"
            >

                <i class="fas fa-circle-check mt-0.5"></i>

                <span>
                    {{ session('success') }}
                </span>

            </div>

        @endif


        @if (session('error'))

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 px-4 py-3
                       text-sm text-red-800
                       flex items-start gap-3"
            >

                <i class="fas fa-circle-exclamation mt-0.5"></i>

                <span>
                    {{ session('error') }}
                </span>

            </div>

        @endif


        <div class="grid grid-cols-3 gap-2 sm:gap-4">

            <a
                href="{{ route('technician.complaints.index', [
                    'scope' => 'active',
                    'status' => 'Assigned',
                ]) }}"
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5
                       hover:border-blue-200
                       hover:shadow-md transition"
            >

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            Assigned
                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                   font-bold text-gray-900 mt-1"
                        >
                            {{ $assignedCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                               w-10 h-10 rounded-xl
                               bg-blue-50 text-blue-600
                               items-center justify-center"
                    >
                        <i class="fas fa-clipboard-list"></i>
                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Waiting to start
                </p>

            </a>


            <a
                href="{{ route('technician.complaints.index', [
                    'scope' => 'active',
                    'status' => 'In Progress',
                ]) }}"
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5
                       hover:border-amber-200
                       hover:shadow-md transition"
            >

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            In Progress
                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                   font-bold text-amber-600 mt-1"
                        >
                            {{ $inProgressCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                               w-10 h-10 rounded-xl
                               bg-amber-50 text-amber-600
                               items-center justify-center"
                    >
                        <i class="fas fa-screwdriver-wrench"></i>
                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Active field work
                </p>

            </a>


            <a
                href="{{ route('technician.complaints.index', [
                    'scope' => 'active',
                    'urgency' => 'High',
                ]) }}"
                class="bg-white rounded-xl sm:rounded-2xl
                       border border-gray-200 shadow-sm
                       p-3 sm:p-5
                       hover:border-red-200
                       hover:shadow-md transition"
            >

                <div class="flex items-center justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-sm text-gray-500">
                            High Urgency
                        </p>

                        <p
                            class="text-xl sm:text-3xl
                                   font-bold text-red-600 mt-1"
                        >
                            {{ $urgentCount }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                               w-10 h-10 rounded-xl
                               bg-red-50 text-red-600
                               items-center justify-center"
                    >
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                </div>

                <p class="hidden sm:block text-xs text-gray-400 mt-2">
                    Needs attention
                </p>

            </a>

        </div>


        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            <div class="xl:col-span-2">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200
                           shadow-sm overflow-hidden"
                >

                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                        <div
                            class="flex flex-col sm:flex-row
                                   sm:items-center
                                   sm:justify-between gap-3"
                        >

                            <div>

                                <h2 class="font-semibold text-gray-900">

                                    <i class="fas fa-person-digging text-sky-600 mr-2"></i>

                                    Active Work

                                </h2>

                                <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                    Complaints currently assigned to you.
                                </p>

                            </div>


                            <a
                                href="{{ route('technician.complaints.index', [
                                    'scope' => 'active',
                                ]) }}"
                                class="inline-flex items-center gap-2
                                       text-sm font-semibold text-sky-600
                                       hover:text-sky-700"
                            >

                                View All

                                <i class="fas fa-arrow-right text-xs"></i>

                            </a>

                        </div>

                    </div>


                    <div class="divide-y divide-gray-100">

                        @forelse ($currentComplaints as $complaint)

                            @php
                                $urgency = strtoupper(
                                    trim($complaint->aiAnalysis?->urgency_level ?? '')
                                );

                                $urgencyClasses = match ($urgency) {
                                    'HIGH' => 'bg-red-50 text-red-700 border-red-200',
                                    'MODERATE' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'LOW' => 'bg-green-50 text-green-700 border-green-200',
                                    default => 'bg-gray-50 text-gray-500 border-gray-200',
                                };

                                $statusClasses = match ($complaint->status) {
                                    'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                                    'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                    default => 'bg-gray-50 text-gray-600 border-gray-200',
                                };

                                $isEngineering = str_contains(
                                    strtolower($complaint->division?->name ?? ''),
                                    'engineering'
                                );

                                $location = $complaint->address;

                                if (!$isEngineering && $complaint->consumer?->address) {
                                    $location =
                                        $complaint->consumer->address->full_address
                                        ?? $complaint->consumer->address->address
                                        ?? $complaint->address;
                                }
                            @endphp


                            <div class="p-4 sm:p-5 hover:bg-gray-50/70 transition">

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex items-start gap-3 min-w-0">

                                        <div
                                            class="hidden sm:flex
                                                   w-10 h-10 rounded-xl
                                                   bg-sky-50 text-sky-600
                                                   items-center justify-center
                                                   shrink-0"
                                        >
                                            <i class="fas fa-file-lines"></i>
                                        </div>


                                        <div class="min-w-0">

                                            <div class="flex flex-wrap items-center gap-2">

                                                <a
                                                    href="{{ route('technician.complaints.show', $complaint) }}"
                                                    class="font-bold text-sky-700
                                                           hover:text-sky-800"
                                                >
                                                    {{ $complaint->complaint_no }}
                                                </a>


                                                @if ($urgency)

                                                    <span
                                                        class="inline-flex items-center gap-1
                                                               px-2 py-0.5 rounded-full border
                                                               text-[10px] font-bold
                                                               {{ $urgencyClasses }}"
                                                    >

                                                        <span
                                                            class="w-1.5 h-1.5
                                                                   rounded-full bg-current"
                                                        ></span>

                                                        {{ $urgency }}

                                                    </span>

                                                @else

                                                    <span
                                                        class="inline-flex items-center
                                                               px-2 py-0.5 rounded-full border
                                                               bg-gray-50 border-gray-200
                                                               text-[10px] font-semibold
                                                               text-gray-400"
                                                    >
                                                        NOT ASSESSED
                                                    </span>

                                                @endif

                                            </div>


                                            <p
                                                class="text-sm font-semibold
                                                       text-gray-800 mt-1"
                                            >
                                                {{ $complaint->category?->name ?? 'Uncategorized' }}
                                            </p>


                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown Consumer') }}
                                            </p>


                                            @if ($location)

                                                <p
                                                    class="text-xs text-gray-400
                                                           mt-2 flex items-start gap-1.5"
                                                >

                                                    <i class="fas fa-location-dot mt-0.5"></i>

                                                    <span>
                                                        {{ \Illuminate\Support\Str::limit($location, 75) }}
                                                    </span>

                                                </p>

                                            @endif

                                        </div>

                                    </div>


                                    <div class="flex flex-col items-end gap-3 shrink-0">

                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   px-2.5 py-1 rounded-full border
                                                   text-[10px] sm:text-xs
                                                   font-semibold
                                                   {{ $statusClasses }}"
                                        >

                                            <span
                                                class="w-1.5 h-1.5
                                                       rounded-full bg-current"
                                            ></span>

                                            {{ $complaint->status }}

                                        </span>


                                        <a
                                            href="{{ route('technician.complaints.show', $complaint) }}"
                                            title="View Complaint"
                                            class="inline-flex w-9 h-9
                                                   items-center justify-center
                                                   rounded-lg
                                                   bg-sky-50 text-sky-600
                                                   border border-sky-100
                                                   hover:bg-sky-600
                                                   hover:text-white
                                                   hover:border-sky-600
                                                   transition"
                                        >
                                            <i class="fas fa-eye"></i>
                                        </a>

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="px-6 py-12 text-center">

                                <div
                                    class="w-14 h-14 mx-auto rounded-2xl
                                           bg-green-50 text-green-600
                                           flex items-center justify-center"
                                >
                                    <i class="fas fa-circle-check text-xl"></i>
                                </div>

                                <h3 class="font-semibold text-gray-900 mt-4">
                                    No active complaints
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    You currently have no assigned or
                                    in-progress maintenance work.
                                </p>

                            </div>

                        @endforelse

                    </div>

                </div>

            </div>


            <div class="space-y-5">

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200
                           shadow-sm overflow-hidden"
                >

                    <div class="px-5 py-4 border-b border-gray-100">

                        <h2 class="font-semibold text-gray-900">

                            <i class="fas fa-chart-simple text-sky-600 mr-2"></i>

                            Work Summary

                        </h2>

                    </div>


                    <div class="p-5 space-y-4">

                        <div
                            class="flex items-center justify-between
                                   gap-4 pb-4 border-b border-gray-100"
                        >

                            <div>

                                <p class="text-sm text-gray-500">
                                    Active Work
                                </p>

                                <p class="text-xs text-gray-400 mt-0.5">
                                    Assigned + In Progress
                                </p>

                            </div>

                            <span class="text-xl font-bold text-sky-600">
                                {{ $activeCount }}
                            </span>

                        </div>


                        <div
                            class="flex items-center justify-between
                                   gap-4 pb-4 border-b border-gray-100"
                        >

                            <div>

                                <p class="text-sm text-gray-500">
                                    Accomplished
                                </p>

                                <p class="text-xs text-gray-400 mt-0.5">
                                    Submitted maintenance work
                                </p>

                            </div>

                            <span class="text-xl font-bold text-green-600">
                                {{ $completedCount }}
                            </span>

                        </div>


                        <div class="flex items-center justify-between gap-4">

                            <div>

                                <p class="text-sm text-gray-500">
                                    Total Assigned
                                </p>

                                <p class="text-xs text-gray-400 mt-0.5">
                                    Your complaint history
                                </p>

                            </div>

                            <span class="text-xl font-bold text-gray-900">
                                {{ $totalCount }}
                            </span>

                        </div>

                    </div>

                </div>


                <a
                    href="{{ route('technician.complaints.index', [
                        'scope' => 'active',
                    ]) }}"
                    class="block bg-sky-600 rounded-2xl
                           shadow-sm p-5 text-white
                           hover:bg-sky-700 transition"
                >

                    <div class="flex items-center justify-between gap-4">

                        <div>

                            <p class="font-semibold">
                                My Active Work
                            </p>

                            <p class="text-sm text-sky-100 mt-1">
                                View assigned maintenance complaints.
                            </p>

                        </div>

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-white/15
                                   flex items-center justify-center
                                   shrink-0"
                        >
                            <i class="fas fa-arrow-right"></i>
                        </div>

                    </div>

                </a>

            </div>

        </div>


        <div class="grid grid-cols-1 xl:grid-cols-2 gap-5">

            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm overflow-hidden"
            >

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <h2 class="font-semibold text-gray-900">

                                <i class="fas fa-clipboard-check text-sky-600 mr-2"></i>

                                Recently Accomplished

                            </h2>

                            <p class="text-xs text-gray-500 mt-1">
                                Your latest submitted maintenance work.
                            </p>

                        </div>


                        <a
                            href="{{ route('technician.complaints.index', [
                                'scope' => 'all',
                                'status' => 'Completed',
                            ]) }}"
                            class="text-xs sm:text-sm
                                   font-semibold text-sky-600
                                   hover:text-sky-700"
                        >
                            View All
                        </a>

                    </div>

                </div>


                <div class="divide-y divide-gray-100">

                    @forelse ($recentCompleted as $complaint)

                        @php
                            $reviewStatus =
                                $complaint->maintenanceReport?->review_status;

                            $reviewClasses = match ($reviewStatus) {
                                'Approved' => 'bg-green-50 text-green-700 border-green-200',
                                'Returned' => 'bg-red-50 text-red-700 border-red-200',
                                'Pending Review' => 'bg-violet-50 text-violet-700 border-violet-200',
                                default => 'bg-gray-50 text-gray-500 border-gray-200',
                            };
                        @endphp


                        <a
                            href="{{ route('technician.complaints.show', $complaint) }}"
                            class="block p-4 sm:p-5
                                   hover:bg-gray-50 transition"
                        >

                            <div class="flex items-start justify-between gap-4">

                                <div class="min-w-0">

                                    <div class="flex flex-wrap items-center gap-2">

                                        <p class="font-bold text-sky-700">
                                            {{ $complaint->complaint_no }}
                                        </p>

                                        <span
                                            class="inline-flex items-center
                                                   px-2 py-0.5 rounded-full border
                                                   bg-green-50 text-green-700
                                                   border-green-200
                                                   text-[10px] font-bold"
                                        >
                                            ACCOMPLISHED
                                        </span>

                                    </div>


                                    <p
                                        class="text-sm font-semibold
                                               text-gray-800 mt-1"
                                    >
                                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                                    </p>


                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $complaint->consumer?->full_name ?? ($complaint->complainant_name ?? 'Unknown Consumer') }}
                                    </p>

                                </div>


                                @if ($reviewStatus)

                                    <span
                                        class="inline-flex items-center
                                               px-2 py-1 rounded-full border
                                               text-[10px] font-semibold
                                               shrink-0
                                               {{ $reviewClasses }}"
                                    >
                                        {{ strtoupper($reviewStatus) }}
                                    </span>

                                @endif

                            </div>


                            <div
                                class="flex items-center justify-between
                                       gap-3 mt-3"
                            >

                                <p class="text-xs text-gray-400">

                                    @if ($complaint->completed_at)

                                        {{ $complaint->completed_at->format('M d, Y h:i A') }}

                                    @else

                                        {{ $complaint->updated_at?->diffForHumans() }}

                                    @endif

                                </p>

                                <i class="fas fa-chevron-right text-xs text-gray-300"></i>

                            </div>

                        </a>

                    @empty

                        <div class="px-6 py-10 text-center">

                            <div
                                class="w-12 h-12 mx-auto rounded-xl
                                       bg-gray-100 text-gray-400
                                       flex items-center justify-center"
                            >
                                <i class="fas fa-clipboard-check"></i>
                            </div>

                            <p class="font-semibold text-gray-800 mt-3">
                                No accomplished work yet
                            </p>

                            <p class="text-sm text-gray-500 mt-1">
                                Submitted accomplishment reports will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>


            <div
                class="bg-white rounded-2xl
                       border border-gray-200
                       shadow-sm overflow-hidden"
            >

                <div class="px-5 sm:px-6 py-4 border-b border-gray-100">

                    <div class="flex items-center justify-between gap-3">

                        <div>

                            <h2 class="font-semibold text-gray-900">

                                <i class="fas fa-clock-rotate-left text-sky-600 mr-2"></i>

                                Recent Activity

                            </h2>

                            <p class="text-xs text-gray-500 mt-1">
                                Latest changes to your assigned complaints.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="divide-y divide-gray-100">

                    @forelse ($recentActivity as $complaint)

                        @php
                            $activityStatus = match ($complaint->status) {
                                'Completed' => 'Accomplished',
                                default => $complaint->status,
                            };

                            $activityClasses = match ($complaint->status) {
                                'Assigned' => 'bg-blue-50 text-blue-700 border-blue-100',
                                'In Progress' => 'bg-amber-50 text-amber-700 border-amber-100',
                                'Completed' => 'bg-green-50 text-green-700 border-green-100',
                                'Closed' => 'bg-gray-100 text-gray-700 border-gray-200',
                                default => 'bg-gray-50 text-gray-600 border-gray-200',
                            };
                        @endphp


                        <a
                            href="{{ route('technician.complaints.show', $complaint) }}"
                            class="block p-4 sm:p-5
                                   hover:bg-gray-50 transition"
                        >

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9 rounded-xl
                                           bg-sky-50 text-sky-600
                                           flex items-center justify-center
                                           shrink-0"
                                >
                                    @if ($complaint->status === 'Assigned')

                                        <i class="fas fa-user-check text-sm"></i>

                                    @elseif ($complaint->status === 'In Progress')

                                        <i class="fas fa-screwdriver-wrench text-sm"></i>

                                    @elseif ($complaint->status === 'Completed')

                                        <i class="fas fa-clipboard-check text-sm"></i>

                                    @else

                                        <i class="fas fa-circle-check text-sm"></i>

                                    @endif
                                </div>


                                <div class="min-w-0 flex-1">

                                    <div
                                        class="flex flex-wrap items-center
                                               justify-between gap-2"
                                    >

                                        <p class="font-semibold text-gray-900">
                                            {{ $complaint->complaint_no }}
                                        </p>

                                        <span
                                            class="inline-flex items-center
                                                   px-2 py-0.5 rounded-full border
                                                   text-[10px] font-semibold
                                                   {{ $activityClasses }}"
                                        >
                                            {{ $activityStatus }}
                                        </span>

                                    </div>


                                    <p class="text-sm text-gray-700 mt-1">
                                        {{ $complaint->category?->name ?? 'Uncategorized' }}
                                    </p>


                                    <p class="text-xs text-gray-400 mt-1">
                                        Updated
                                        {{ $complaint->updated_at?->diffForHumans() }}
                                    </p>

                                </div>

                            </div>

                        </a>

                    @empty

                        <div class="px-6 py-10 text-center">

                            <p class="text-sm text-gray-500">
                                No recent activity.
                            </p>

                        </div>

                    @endforelse

                </div>

            </div>

        </div>

    </div>

@endsection
