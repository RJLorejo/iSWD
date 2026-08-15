@extends($layout)

@section('title', 'Global Search')

@section('content')

    <div class="space-y-8">

        {{-- ================================================================
        HEADER
    ================================================================= --}}

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8">

            <div class="flex items-center justify-between gap-6">

                <div>

                    <h1 class="text-3xl font-bold text-slate-800">
                        Search Results
                    </h1>

                    @if ($keyword !== '')
                        <p class="mt-2 text-slate-500">
                            Showing results for
                            <strong class="text-slate-800">
                                "{{ $keyword }}"
                            </strong>
                        </p>
                    @else
                        <p class="mt-2 text-slate-500">
                            Search employees, consumers, complaints,
                            maintenance reports, and other system records.
                        </p>
                    @endif

                </div>

                <div
                    class="hidden sm:flex w-14 h-14 rounded-2xl
                       bg-sky-50 text-sky-600
                       items-center justify-center">

                    <i class="fas fa-search text-xl"></i>

                </div>

            </div>

        </div>


        @if ($keyword === '')

            {{-- Empty Search --}}

            <div class="bg-white rounded-2xl shadow-sm border p-12 text-center">

                <div
                    class="mx-auto w-16 h-16 rounded-2xl
                       bg-slate-100 text-slate-500
                       flex items-center justify-center">

                    <i class="fas fa-magnifying-glass text-2xl"></i>

                </div>

                <h2 class="mt-5 text-xl font-bold text-slate-800">
                    Search the system
                </h2>

                <p class="mt-2 text-slate-500">
                    Enter a keyword in the navigation search bar.
                </p>

            </div>
        @else
            {{-- ============================================================
            EMPLOYEES
        ============================================================= --}}

            @if ($users->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-users text-sky-600 mr-2"></i>

                            Employees

                            <span class="text-sm text-slate-400">
                                ({{ $users->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($users as $user)
                        <a href="{{ route('admin.users.show', $user) }}"
                            class="block p-5 border-b last:border-0
                               hover:bg-slate-50 transition">

                            <div class="flex items-center justify-between gap-5">

                                <div class="flex items-center gap-4">

                                    <div
                                        class="w-11 h-11 rounded-full
                                           bg-sky-100 text-sky-700
                                           flex items-center justify-center
                                           font-bold">

                                        {{ strtoupper(substr($user->first_name ?? 'U', 0, 1) . substr($user->last_name ?? '', 0, 1)) }}

                                    </div>

                                    <div>

                                        <div class="font-semibold text-slate-800">

                                            {{ $user->first_name }}
                                            {{ $user->last_name }}

                                        </div>

                                        <div class="text-sm text-slate-500">

                                            {{ $user->employee_id }}
                                            ·
                                            {{ $user->email }}

                                        </div>

                                    </div>

                                </div>

                                <div class="text-right text-sm">

                                    <div class="text-slate-600">

                                        {{ optional($user->department)->department_name ?? 'No Department' }}

                                    </div>

                                    <div class="text-slate-400">

                                        {{ optional($user->position)->position_name ?? 'No Position' }}

                                    </div>

                                </div>

                            </div>

                        </a>
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            CONSUMERS
        ============================================================= --}}

            @if ($consumers->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-user text-emerald-600 mr-2"></i>

                            Consumers

                            <span class="text-sm text-slate-400">
                                ({{ $consumers->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($consumers as $consumer)
                        <a href="{{ route('admin.consumers.show', $consumer) }}"
                            class="block p-5 border-b last:border-0
                               hover:bg-slate-50 transition">

                            <div class="flex items-center justify-between">

                                <div>

                                    <div class="font-semibold text-slate-800">

                                        {{ $consumer->first_name }}
                                        {{ $consumer->middle_name }}
                                        {{ $consumer->last_name }}

                                    </div>

                                    <div class="text-sm text-slate-500 mt-1">

                                        {{ $consumer->email ?? 'No email' }}

                                    </div>

                                </div>

                                <div class="text-sm text-slate-500">

                                    {{ $consumer->phone ?? 'No phone' }}

                                </div>

                            </div>

                        </a>
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            COMPLAINTS
        ============================================================= --}}

            @if ($complaints->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-triangle-exclamation text-amber-600 mr-2"></i>

                            Complaints

                            <span class="text-sm text-slate-400">
                                ({{ $complaints->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($complaints as $complaint)
                        <a href="{{ route('technician.complaints.show', $complaint) }}"
                            class="block p-5 border-b last:border-0
                               hover:bg-slate-50 transition">

                            <div class="flex items-center justify-between gap-6">

                                <div>

                                    <div class="flex items-center gap-3">

                                        <span class="font-bold text-slate-800">

                                            {{ $complaint->complaint_no }}

                                        </span>

                                        <span
                                            class="px-2.5 py-1 rounded-full
                                               text-xs font-semibold
                                               bg-slate-100 text-slate-600">

                                            {{ $complaint->status }}

                                        </span>

                                    </div>

                                    <div class="font-semibold mt-2 text-slate-700">

                                        {{ $complaint->subject }}

                                    </div>

                                    <div class="text-sm text-slate-500 mt-1">

                                        {{ \Illuminate\Support\Str::limit($complaint->description, 120) }}

                                    </div>

                                </div>

                                <div class="text-right">

                                    <div class="text-xs text-slate-400">
                                        Priority
                                    </div>

                                    <div class="font-semibold text-slate-700">

                                        {{ $complaint->priority }}

                                    </div>

                                    <div class="text-xs text-slate-400 mt-2">

                                        {{ optional($complaint->category)->category_name ?? 'Uncategorized' }}

                                    </div>

                                </div>

                            </div>

                        </a>
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            MAINTENANCE REPORTS
        ============================================================= --}}

            @if ($maintenanceReports->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-screwdriver-wrench text-indigo-600 mr-2"></i>

                            Maintenance Reports

                            <span class="text-sm text-slate-400">
                                ({{ $maintenanceReports->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($maintenanceReports as $report)
                        @if ($report->complaint)
                            <a href="{{ route('technician.maintenance-reports.show', $report->complaint) }}"
                                class="block p-5 border-b last:border-0
                                   hover:bg-slate-50 transition">

                                <div class="flex items-center justify-between gap-6">

                                    <div>

                                        <div class="flex items-center gap-3">

                                            <span class="font-bold text-slate-800">

                                                {{ $report->complaint->complaint_no }}

                                            </span>

                                            @if ($report->submitted_at)
                                                <span
                                                    class="px-2.5 py-1 rounded-full
                                                       text-xs font-semibold
                                                       bg-green-100 text-green-700">

                                                    Submitted

                                                </span>
                                            @else
                                                <span
                                                    class="px-2.5 py-1 rounded-full
                                                       text-xs font-semibold
                                                       bg-amber-100 text-amber-700">

                                                    Draft

                                                </span>
                                            @endif

                                        </div>

                                        <div class="font-semibold mt-2 text-slate-700">

                                            {{ $report->complaint->subject }}

                                        </div>

                                        <div class="text-sm text-slate-500 mt-1">

                                            Diagnosis:

                                            {{ \Illuminate\Support\Str::limit($report->diagnosis, 120) }}

                                        </div>

                                    </div>

                                    <div class="text-right text-sm">

                                        <div class="text-slate-400">
                                            Technician
                                        </div>

                                        <div class="font-medium text-slate-700">

                                            {{ optional($report->technician)->first_name }}
                                            {{ optional($report->technician)->last_name }}

                                        </div>

                                        @if ($report->submitted_at)
                                            <div class="text-xs text-slate-400 mt-2">

                                                {{ $report->submitted_at->format('M d, Y h:i A') }}

                                            </div>
                                        @endif

                                    </div>

                                </div>

                            </a>
                        @endif
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            DEPARTMENTS
        ============================================================= --}}

            @if ($departments->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-building text-purple-600 mr-2"></i>

                            Departments

                            <span class="text-sm text-slate-400">
                                ({{ $departments->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($departments as $department)
                        <div class="p-5 border-b last:border-0">

                            {{ $department->department_name }}

                        </div>
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            POSITIONS
        ============================================================= --}}

            @if ($positions->isNotEmpty())

                <div class="bg-white rounded-2xl shadow-sm border">

                    <div class="p-6 border-b">

                        <h2 class="text-xl font-bold text-slate-800">

                            <i class="fas fa-id-badge text-cyan-600 mr-2"></i>

                            Positions

                            <span class="text-sm text-slate-400">
                                ({{ $positions->count() }})
                            </span>

                        </h2>

                    </div>

                    @foreach ($positions as $position)
                        <div class="p-5 border-b last:border-0">

                            {{ $position->position_name }}

                        </div>
                    @endforeach

                </div>

            @endif


            {{-- ============================================================
            NO RESULTS
        ============================================================= --}}

            @if (
                $users->isEmpty() &&
                    $departments->isEmpty() &&
                    $positions->isEmpty() &&
                    $consumers->isEmpty() &&
                    $complaints->isEmpty() &&
                    $maintenanceReports->isEmpty())
                <div class="bg-white rounded-2xl shadow-sm border p-12 text-center">

                    <div
                        class="mx-auto w-16 h-16 rounded-2xl
                           bg-slate-100 text-slate-500
                           flex items-center justify-center">

                        <i class="fas fa-search-minus text-2xl"></i>

                    </div>

                    <h2 class="mt-5 text-xl font-bold text-slate-800">

                        No results found

                    </h2>

                    <p class="mt-2 text-slate-500">

                        We couldn't find anything matching

                        <strong>
                            "{{ $keyword }}"
                        </strong>

                    </p>

                </div>
            @endif

        @endif

    </div>

@endsection
