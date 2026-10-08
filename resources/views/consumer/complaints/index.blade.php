@extends('consumer.layouts.app')

@section('title', 'My Complaints')

@section('content')

    @php

        /*
        |--------------------------------------------------------------------------
        | Consumer-Friendly Status Labels
        |--------------------------------------------------------------------------
        */

        $statusLabels = [

            'Pending' =>
                'Submitted',

            'Verified' =>
                'Verified',

            'CS Processing' =>
                'Under Initial Processing',

            'For Maintenance' =>
                'Forwarded to Maintenance',

            'Assigned' =>
                'Plumber Assigned',

            'In Progress' =>
                'Maintenance In Progress',

            'Completed' =>
                'Maintenance Completed',

            'Closed' =>
                'Request Closed',

            'Rejected' =>
                'Rejected',

        ];


        /*
        |--------------------------------------------------------------------------
        | Dashboard Group Labels
        |--------------------------------------------------------------------------
        */

        $groupLabels = [

            'pending' =>
                'Awaiting Review',

            'active' =>
                'Active Requests',

            'completed' =>
                'Completed Requests',

        ];


        /*
        |--------------------------------------------------------------------------
        | Status Styles
        |--------------------------------------------------------------------------
        */

        $statusClasses = [

            'Pending' =>
                'border-amber-200 bg-amber-50 text-amber-700',

            'Verified' =>
                'border-sky-200 bg-sky-50 text-sky-700',

            'CS Processing' =>
                'border-cyan-200 bg-cyan-50 text-cyan-700',

            'For Maintenance' =>
                'border-violet-200 bg-violet-50 text-violet-700',

            'Assigned' =>
                'border-indigo-200 bg-indigo-50 text-indigo-700',

            'In Progress' =>
                'border-blue-200 bg-blue-50 text-blue-700',

            'Completed' =>
                'border-emerald-200 bg-emerald-50 text-emerald-700',

            'Closed' =>
                'border-slate-200 bg-slate-100 text-slate-700',

            'Rejected' =>
                'border-red-200 bg-red-50 text-red-700',

        ];


        /*
        |--------------------------------------------------------------------------
        | Status Icons
        |--------------------------------------------------------------------------
        */

        $statusIcons = [

            'Pending' =>
                'fa-clock',

            'Verified' =>
                'fa-circle-check',

            'CS Processing' =>
                'fa-clipboard-list',

            'For Maintenance' =>
                'fa-share',

            'Assigned' =>
                'fa-user-group',

            'In Progress' =>
                'fa-screwdriver-wrench',

            'Completed' =>
                'fa-clipboard-check',

            'Closed' =>
                'fa-lock',

            'Rejected' =>
                'fa-circle-xmark',

        ];


        $hasFilters =
            $search !== ''
            ||
            $status !== ''
            ||
            $group !== '';

    @endphp


    <div class="mx-auto max-w-7xl space-y-6">


        {{-- ========================================================= --}}
        {{-- PAGE HEADER --}}
        {{-- ========================================================= --}}

        <div
            class="
                flex
                flex-col
                gap-4
                sm:flex-row
                sm:items-end
                sm:justify-between
            "
        >

            <div>

                <div
                    class="
                        flex
                        items-center
                        gap-2
                        text-xs
                        font-medium
                        text-sky-700
                    "
                >

                    <i class="fas fa-file-lines"></i>

                    <span>
                        Consumer Service Requests
                    </span>

                </div>


                <h1
                    class="
                        mt-2
                        text-2xl
                        font-semibold
                        tracking-tight
                        text-slate-900
                        sm:text-3xl
                    "
                >
                    My Complaints
                </h1>


                <p
                    class="
                        mt-1
                        text-sm
                        text-slate-500
                    "
                >
                    View and monitor the progress of your submitted
                    water service concerns.
                </p>

            </div>


            <a
                href="{{ route('consumer.complaints.create') }}"
                class="
                    inline-flex
                    items-center
                    justify-center
                    gap-2
                    rounded-xl
                    bg-sky-700
                    px-5
                    py-3
                    text-sm
                    font-semibold
                    text-white
                    shadow-sm
                    transition
                    hover:bg-sky-800
                "
            >

                <i class="fas fa-plus text-xs"></i>

                Submit Complaint

            </a>

        </div>



        {{-- ========================================================= --}}
        {{-- SEARCH / FILTER --}}
        {{-- ========================================================= --}}

        <section
            class="
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                shadow-sm
            "
        >

            <form
                method="GET"
                action="{{ route('consumer.complaints.index') }}"
                class="p-4 sm:p-5"
            >

                @if ($group !== '')

                    <input
                        type="hidden"
                        name="group"
                        value="{{ $group }}"
                    >

                @endif


                <div
                    class="
                        grid
                        grid-cols-1
                        gap-3
                        lg:grid-cols-[minmax(0,1fr)_230px_auto]
                    "
                >


                    {{-- Search --}}

                    <div>

                        <label
                            for="search"
                            class="sr-only"
                        >
                            Search complaints
                        </label>


                        <div class="relative">

                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    left-0
                                    flex
                                    items-center
                                    pl-4
                                    text-slate-400
                                "
                            >

                                <i
                                    class="
                                        fas
                                        fa-magnifying-glass
                                        text-sm
                                    "
                                ></i>

                            </div>


                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ $search }}"
                                placeholder="Search complaint number, type, division, or description..."
                                class="
                                    w-full
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    py-3
                                    pl-11
                                    pr-4
                                    text-sm
                                    text-slate-700
                                    outline-none
                                    transition
                                    placeholder:text-slate-400
                                    focus:border-sky-400
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-sky-100
                                "
                            >

                        </div>

                    </div>



                    {{-- Status --}}

                    <div>

                        <label
                            for="status"
                            class="sr-only"
                        >
                            Filter by status
                        </label>


                        <div class="relative">

                            <select
                                id="status"
                                name="status"
                                class="
                                    w-full
                                    appearance-none
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-slate-50
                                    px-4
                                    py-3
                                    pr-10
                                    text-sm
                                    font-medium
                                    text-slate-700
                                    outline-none
                                    transition
                                    focus:border-sky-400
                                    focus:bg-white
                                    focus:ring-4
                                    focus:ring-sky-100
                                "
                            >

                                <option value="">
                                    All Statuses
                                </option>


@foreach ($allowedStatuses as $filterStatus)

    <option
        value="{{ $filterStatus }}"
        @selected($status === $filterStatus)
    >
        {{ $statusLabels[$filterStatus] ?? $filterStatus }}
    </option>

@endforeach

                            </select>


                            <div
                                class="
                                    pointer-events-none
                                    absolute
                                    inset-y-0
                                    right-0
                                    flex
                                    items-center
                                    pr-4
                                    text-slate-400
                                "
                            >

                                <i
                                    class="
                                        fas
                                        fa-chevron-down
                                        text-[10px]
                                    "
                                ></i>

                            </div>

                        </div>

                    </div>



                    {{-- Actions --}}

                    <div class="flex gap-2">

                        <button
                            type="submit"
                            class="
                                inline-flex
                                flex-1
                                items-center
                                justify-center
                                gap-2
                                rounded-xl
                                bg-slate-900
                                px-5
                                py-3
                                text-sm
                                font-semibold
                                text-white
                                transition
                                hover:bg-slate-800
                                lg:flex-none
                            "
                        >

                            <i class="fas fa-filter text-xs"></i>

                            Apply

                        </button>


                        @if ($hasFilters)

                            <a
                                href="{{ route(
                                    'consumer.complaints.index'
                                ) }}"
                                title="Clear filters"
                                class="
                                    inline-flex
                                    h-[46px]
                                    w-[46px]
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    border
                                    border-slate-200
                                    bg-white
                                    text-slate-500
                                    transition
                                    hover:border-red-200
                                    hover:bg-red-50
                                    hover:text-red-600
                                "
                            >

                                <i class="fas fa-xmark"></i>

                            </a>

                        @endif

                    </div>

                </div>



                {{-- Active Filters --}}

                @if ($hasFilters)

                    <div
                        class="
                            mt-4
                            flex
                            flex-wrap
                            items-center
                            gap-2
                            border-t
                            border-slate-100
                            pt-4
                        "
                    >

                        <span
                            class="
                                text-xs
                                font-medium
                                text-slate-400
                            "
                        >
                            Showing:
                        </span>


                        @if (isset($groupLabels[$group]))

                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-sky-50
                                    px-3
                                    py-1.5
                                    text-xs
                                    font-semibold
                                    text-sky-700
                                "
                            >

                                <i
                                    class="
                                        fas
                                        fa-layer-group
                                        text-[9px]
                                    "
                                ></i>

                                {{ $groupLabels[$group] }}

                            </span>

                        @endif


                        @if ($search !== '')

                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-slate-100
                                    px-3
                                    py-1.5
                                    text-xs
                                    font-medium
                                    text-slate-600
                                "
                            >

                                <i
                                    class="
                                        fas
                                        fa-magnifying-glass
                                        text-[9px]
                                    "
                                ></i>

                                {{ $search }}

                            </span>

                        @endif


                        @if (
                            $status !== ''
                            &&
                            $group === ''
                        )

                            <span
                                class="
                                    inline-flex
                                    items-center
                                    gap-1.5
                                    rounded-full
                                    bg-slate-100
                                    px-3
                                    py-1.5
                                    text-xs
                                    font-medium
                                    text-slate-600
                                "
                            >

                                <i
                                    class="
                                        fas
                                        fa-filter
                                        text-[9px]
                                    "
                                ></i>

                                {{ $statusLabels[$status]
                                    ?? $status }}

                            </span>

                        @endif

                    </div>

                @endif

            </form>

        </section>



        {{-- ========================================================= --}}
        {{-- COMPLAINT LIST --}}
        {{-- ========================================================= --}}

        <section
            class="
                overflow-hidden
                rounded-2xl
                border
                border-slate-200
                bg-white
                shadow-sm
            "
        >


            {{-- Header --}}

            <div
                class="
                    flex
                    items-center
                    justify-between
                    gap-4
                    border-b
                    border-slate-100
                    bg-slate-50/70
                    px-5
                    py-4
                    sm:px-6
                "
            >

                <div>

                    <h2
                        class="
                            text-sm
                            font-semibold
                            text-slate-800
                        "
                    >

                        @if (isset($groupLabels[$group]))

                            {{ $groupLabels[$group] }}

                        @else

                            Complaint History

                        @endif

                    </h2>


                    <p
                        class="
                            mt-0.5
                            text-xs
                            text-slate-500
                        "
                    >

                        @if ($hasFilters)

                            Showing complaints matching your
                            selected filters.

                        @else

                            Your most recent complaints appear
                            first.

                        @endif

                    </p>

                </div>


                <span
                    class="
                        shrink-0
                        rounded-full
                        border
                        border-slate-200
                        bg-white
                        px-3
                        py-1.5
                        text-xs
                        font-semibold
                        text-slate-500
                    "
                >

                    {{ $complaints->total() }}

                    {{ Str::plural(
                        'record',
                        $complaints->total()
                    ) }}

                </span>

            </div>



            {{-- Complaints --}}

            @forelse ($complaints as $complaint)

                @php

                    $complaintStatusLabel =
                        $statusLabels[$complaint->status]
                        ?? $complaint->status;


                    $complaintStatusClass =
                        $statusClasses[$complaint->status]
                        ?? 'border-slate-200 bg-slate-100 text-slate-700';


                    $complaintStatusIcon =
                        $statusIcons[$complaint->status]
                        ?? 'fa-circle';


                    $divisionName =
                        optional(
                            $complaint->division
                        )->name;


                    $isCommercial =
                        $divisionName
                        &&
                        str_contains(
                            strtolower($divisionName),
                            'commercial'
                        );

                @endphp


                <a
                    href="{{ route(
                        'consumer.complaints.show',
                        $complaint
                    ) }}"
                    class="
                        group
                        block
                        border-b
                        border-slate-100
                        transition
                        last:border-b-0
                        hover:bg-sky-50/40
                    "
                >

                    <div
                        class="
                            px-5
                            py-5
                            sm:px-6
                        "
                    >

                        <div
                            class="
                                flex
                                items-start
                                gap-4
                            "
                        >


                            {{-- Icon --}}

                            <div
                                class="
                                    flex
                                    h-12
                                    w-12
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-2xl
                                    transition
                                    {{ $isCommercial
                                        ? 'bg-violet-50 text-violet-600 group-hover:bg-violet-100'
                                        : 'bg-sky-50 text-sky-700 group-hover:bg-sky-100'
                                    }}
                                "
                            >

                                <i
                                    class="
                                        fas
                                        {{ $isCommercial
                                            ? 'fa-file-invoice'
                                            : 'fa-droplet'
                                        }}
                                    "
                                ></i>

                            </div>



                            {{-- Content --}}

                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        flex-col
                                        gap-3
                                        sm:flex-row
                                        sm:items-start
                                        sm:justify-between
                                    "
                                >

                                    <div class="min-w-0">

                                        <div
                                            class="
                                                flex
                                                flex-wrap
                                                items-center
                                                gap-2
                                            "
                                        >

                                            <h3
                                                class="
                                                    text-sm
                                                    font-bold
                                                    text-slate-900
                                                    sm:text-base
                                                "
                                            >
                                                {{ $complaint->complaint_no }}
                                            </h3>


                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                    rounded-full
                                                    border
                                                    px-2.5
                                                    py-1
                                                    text-[11px]
                                                    font-semibold
                                                    {{ $complaintStatusClass }}
                                                "
                                            >

                                                <i
                                                    class="
                                                        fas
                                                        {{ $complaintStatusIcon }}
                                                        text-[9px]
                                                    "
                                                ></i>

                                                {{ $complaintStatusLabel }}

                                            </span>

                                        </div>


                                        <p
                                            class="
                                                mt-2
                                                text-sm
                                                font-semibold
                                                text-slate-700
                                                transition
                                                group-hover:text-sky-800
                                            "
                                        >

                                            {{ optional(
                                                $complaint->category
                                            )->name
                                                ?? 'Water Service Concern' }}

                                        </p>

                                    </div>


                                    <div
                                        class="
                                            hidden
                                            shrink-0
                                            items-center
                                            gap-2
                                            text-xs
                                            font-semibold
                                            text-slate-400
                                            transition
                                            group-hover:text-sky-700
                                            sm:flex
                                        "
                                    >

                                        View details

                                        <i
                                            class="
                                                fas
                                                fa-chevron-right
                                                text-[10px]
                                                transition
                                                group-hover:translate-x-0.5
                                            "
                                        ></i>

                                    </div>

                                </div>



                                {{-- Metadata --}}

                                <div
                                    class="
                                        mt-3
                                        flex
                                        flex-wrap
                                        items-center
                                        gap-x-5
                                        gap-y-2
                                        text-xs
                                        text-slate-500
                                    "
                                >

                                    @if ($divisionName)

                                        <span
                                            class="
                                                inline-flex
                                                items-center
                                                gap-1.5
                                            "
                                        >

                                            <i
                                                class="
                                                    fas
                                                    fa-building
                                                    text-[10px]
                                                    text-slate-400
                                                "
                                            ></i>

                                            {{ $divisionName }}

                                        </span>

                                    @endif


                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1.5
                                        "
                                    >

                                        <i
                                            class="
                                                far
                                                fa-calendar
                                                text-[10px]
                                                text-slate-400
                                            "
                                        ></i>

                                        {{ $complaint
                                            ->created_at
                                            ->timezone('Asia/Manila')
                                            ->format('M d, Y') }}

                                    </span>


                                    <span
                                        class="
                                            inline-flex
                                            items-center
                                            gap-1.5
                                        "
                                    >

                                        <i
                                            class="
                                                far
                                                fa-clock
                                                text-[10px]
                                                text-slate-400
                                            "
                                        ></i>

                                        {{ $complaint
                                            ->created_at
                                            ->timezone('Asia/Manila')
                                            ->format('g:i A') }}

                                    </span>

                                </div>



                                {{-- Description --}}

                                @if ($complaint->description)

                                    <p
                                        class="
                                            mt-3
                                            line-clamp-2
                                            max-w-4xl
                                            text-xs
                                            leading-5
                                            text-slate-500
                                        "
                                    >
                                        {{ $complaint->description }}
                                    </p>

                                @endif



                                {{-- Mobile Link --}}

                                <div
                                    class="
                                        mt-4
                                        flex
                                        items-center
                                        gap-1.5
                                        text-xs
                                        font-semibold
                                        text-sky-700
                                        sm:hidden
                                    "
                                >

                                    View complaint

                                    <i
                                        class="
                                            fas
                                            fa-arrow-right
                                            text-[10px]
                                        "
                                    ></i>

                                </div>

                            </div>

                        </div>

                    </div>

                </a>


            @empty


                {{-- ================================================= --}}
                {{-- EMPTY STATE --}}
                {{-- ================================================= --}}

                <div
                    class="
                        px-6
                        py-16
                        text-center
                    "
                >

                    <div
                        class="
                            mx-auto
                            flex
                            h-16
                            w-16
                            items-center
                            justify-center
                            rounded-2xl
                            bg-slate-100
                            text-slate-400
                        "
                    >

                        @if ($hasFilters)

                            <i
                                class="
                                    fas
                                    fa-magnifying-glass
                                    text-xl
                                "
                            ></i>

                        @else

                            <i
                                class="
                                    far
                                    fa-file-lines
                                    text-xl
                                "
                            ></i>

                        @endif

                    </div>


                    @if ($hasFilters)

                        <h2
                            class="
                                mt-4
                                text-base
                                font-semibold
                                text-slate-900
                            "
                        >
                            No matching complaints
                        </h2>


                        <p
                            class="
                                mx-auto
                                mt-1
                                max-w-md
                                text-sm
                                leading-6
                                text-slate-500
                            "
                        >
                            There are currently no complaints
                            matching this filter.
                        </p>


                        <a
                            href="{{ route(
                                'consumer.complaints.index'
                            ) }}"
                            class="
                                mt-5
                                inline-flex
                                items-center
                                gap-2
                                rounded-xl
                                border
                                border-slate-200
                                bg-white
                                px-5
                                py-3
                                text-sm
                                font-semibold
                                text-slate-700
                                transition
                                hover:bg-slate-50
                            "
                        >

                            <i
                                class="
                                    fas
                                    fa-rotate-left
                                    text-xs
                                "
                            ></i>

                            View All Complaints

                        </a>


                    @else


                        <h2
                            class="
                                mt-4
                                text-base
                                font-semibold
                                text-slate-900
                            "
                        >
                            No complaints submitted
                        </h2>


                        <p
                            class="
                                mx-auto
                                mt-1
                                max-w-md
                                text-sm
                                leading-6
                                text-slate-500
                            "
                        >
                            You have not submitted any water service
                            concerns yet.
                        </p>


                        <a
                            href="{{ route(
                                'consumer.complaints.create'
                            ) }}"
                            class="
                                mt-5
                                inline-flex
                                items-center
                                gap-2
                                rounded-xl
                                bg-sky-700
                                px-5
                                py-3
                                text-sm
                                font-semibold
                                text-white
                                transition
                                hover:bg-sky-800
                            "
                        >

                            <i class="fas fa-plus text-xs"></i>

                            Submit Your First Complaint

                        </a>

                    @endif

                </div>

            @endforelse

        </section>



        {{-- ========================================================= --}}
        {{-- PAGINATION --}}
        {{-- ========================================================= --}}

        @if ($complaints->hasPages())

            <div
                class="
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    px-4
                    py-3
                    shadow-sm
                "
            >

                {{ $complaints->links() }}

            </div>

        @endif


    </div>

@endsection
