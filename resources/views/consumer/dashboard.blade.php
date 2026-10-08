@extends('consumer.layouts.app')

@section('title', 'Dashboard')

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

    @endphp


    <div class="mx-auto max-w-7xl space-y-6">


        {{-- ========================================================= --}}
        {{-- WELCOME --}}
        {{-- ========================================================= --}}

        <section
            class="
                overflow-hidden
                rounded-2xl
                border
                border-sky-100
                bg-gradient-to-br
                from-sky-50
                via-white
                to-cyan-50
                shadow-sm
            "
        >

            <div
                class="
                    flex
                    flex-col
                    gap-6
                    px-6
                    py-7
                    sm:px-7
                    lg:flex-row
                    lg:items-center
                    lg:justify-between
                "
            >

                <div class="max-w-2xl">

                    <div
                        class="
                            inline-flex
                            items-center
                            gap-2
                            rounded-full
                            border
                            border-sky-100
                            bg-white/80
                            px-3
                            py-1.5
                            text-xs
                            font-semibold
                            text-sky-700
                        "
                    >

                        <i class="fas fa-house"></i>

                        Consumer Dashboard

                    </div>


                    <h1
                        class="
                            mt-4
                            text-2xl
                            font-bold
                            tracking-tight
                            text-slate-900
                            sm:text-3xl
                        "
                    >

                        Welcome,
                        {{ $consumer->first_name }}

                    </h1>


                    <p
                        class="
                            mt-2
                            max-w-xl
                            text-sm
                            leading-6
                            text-slate-500
                        "
                    >

                        Track your water service concerns and stay
                        informed about important Sagay Water District
                        service updates.

                    </p>

                </div>


                <a
                    href="{{ route('consumer.complaints.create') }}"
                    class="
                        inline-flex
                        shrink-0
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
                        focus:outline-none
                        focus:ring-4
                        focus:ring-sky-100
                    "
                >

                    <i class="fas fa-plus text-xs"></i>

                    Submit Complaint

                </a>

            </div>

        </section>



        {{-- ========================================================= --}}
        {{-- CLICKABLE STATISTICS --}}
        {{-- ========================================================= --}}

        <section
            class="
                grid
                grid-cols-1
                gap-4
                sm:grid-cols-2
                xl:grid-cols-4
            "
        >


            {{-- Total Requests --}}

            <a
                href="{{ route('consumer.complaints.index') }}"
                class="
                    group
                    block
                    rounded-2xl
                    border
                    border-slate-200
                    bg-white
                    p-5
                    shadow-sm
                    transition
                    duration-200
                    hover:-translate-y-0.5
                    hover:border-slate-300
                    hover:shadow-md
                    focus:outline-none
                    focus:ring-4
                    focus:ring-slate-100
                "
            >

                <div
                    class="flex items-start justify-between gap-4"
                >

                    <div>

                        <p
                            class="
                                text-xs
                                font-semibold
                                uppercase
                                tracking-wide
                                text-slate-400
                            "
                        >
                            Total Requests
                        </p>


                        <p
                            class="
                                mt-2
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-900
                            "
                        >
                            {{ $totalComplaints }}
                        </p>

                    </div>


                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-slate-100
                            text-slate-600
                            transition
                            group-hover:bg-sky-50
                            group-hover:text-sky-700
                        "
                    >

                        <i class="fas fa-file-lines"></i>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        flex
                        items-center
                        justify-between
                        gap-3
                    "
                >

                    <span
                        class="text-xs text-slate-500"
                    >
                        All submitted requests
                    </span>


                    <span
                        class="
                            inline-flex
                            shrink-0
                            items-center
                            gap-1
                            text-xs
                            font-semibold
                            text-slate-500
                            transition
                            group-hover:text-sky-700
                        "
                    >

                        View

                        <i
                            class="
                                fas
                                fa-arrow-right
                                text-[9px]
                                transition
                                group-hover:translate-x-0.5
                            "
                        ></i>

                    </span>

                </div>

            </a>



            {{-- Awaiting Review --}}

            <a
                href="{{ route(
                    'consumer.complaints.index',
                    ['group' => 'pending']
                ) }}"
                class="
                    group
                    block
                    rounded-2xl
                    border
                    border-amber-100
                    bg-white
                    p-5
                    shadow-sm
                    transition
                    duration-200
                    hover:-translate-y-0.5
                    hover:border-amber-200
                    hover:shadow-md
                    focus:outline-none
                    focus:ring-4
                    focus:ring-amber-50
                "
            >

                <div
                    class="flex items-start justify-between gap-4"
                >

                    <div>

                        <p
                            class="
                                text-xs
                                font-semibold
                                uppercase
                                tracking-wide
                                text-slate-400
                            "
                        >
                            Awaiting Review
                        </p>


                        <p
                            class="
                                mt-2
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-900
                            "
                        >
                            {{ $pendingComplaints }}
                        </p>

                    </div>


                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-amber-50
                            text-amber-600
                        "
                    >

                        <i class="fas fa-clock"></i>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        flex
                        items-center
                        justify-between
                        gap-3
                    "
                >

                    <span
                        class="text-xs text-slate-500"
                    >
                        Submitted or verified
                    </span>


                    <span
                        class="
                            inline-flex
                            shrink-0
                            items-center
                            gap-1
                            text-xs
                            font-semibold
                            text-amber-600
                        "
                    >

                        View

                        <i
                            class="
                                fas
                                fa-arrow-right
                                text-[9px]
                                transition
                                group-hover:translate-x-0.5
                            "
                        ></i>

                    </span>

                </div>

            </a>



            {{-- Active Requests --}}

            <a
                href="{{ route(
                    'consumer.complaints.index',
                    ['group' => 'active']
                ) }}"
                class="
                    group
                    block
                    rounded-2xl
                    border
                    border-sky-100
                    bg-white
                    p-5
                    shadow-sm
                    transition
                    duration-200
                    hover:-translate-y-0.5
                    hover:border-sky-200
                    hover:shadow-md
                    focus:outline-none
                    focus:ring-4
                    focus:ring-sky-50
                "
            >

                <div
                    class="flex items-start justify-between gap-4"
                >

                    <div>

                        <p
                            class="
                                text-xs
                                font-semibold
                                uppercase
                                tracking-wide
                                text-slate-400
                            "
                        >
                            Active Requests
                        </p>


                        <p
                            class="
                                mt-2
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-900
                            "
                        >
                            {{ $activeComplaints }}
                        </p>

                    </div>


                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-sky-50
                            text-sky-700
                        "
                    >

                        <i class="fas fa-screwdriver-wrench"></i>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        flex
                        items-center
                        justify-between
                        gap-3
                    "
                >

                    <span
                        class="text-xs text-slate-500"
                    >
                        Currently being handled
                    </span>


                    <span
                        class="
                            inline-flex
                            shrink-0
                            items-center
                            gap-1
                            text-xs
                            font-semibold
                            text-sky-700
                        "
                    >

                        View

                        <i
                            class="
                                fas
                                fa-arrow-right
                                text-[9px]
                                transition
                                group-hover:translate-x-0.5
                            "
                        ></i>

                    </span>

                </div>

            </a>



            {{-- Completed Requests --}}

            <a
                href="{{ route(
                    'consumer.complaints.index',
                    ['group' => 'completed']
                ) }}"
                class="
                    group
                    block
                    rounded-2xl
                    border
                    border-emerald-100
                    bg-white
                    p-5
                    shadow-sm
                    transition
                    duration-200
                    hover:-translate-y-0.5
                    hover:border-emerald-200
                    hover:shadow-md
                    focus:outline-none
                    focus:ring-4
                    focus:ring-emerald-50
                "
            >

                <div
                    class="flex items-start justify-between gap-4"
                >

                    <div>

                        <p
                            class="
                                text-xs
                                font-semibold
                                uppercase
                                tracking-wide
                                text-slate-400
                            "
                        >
                            Completed
                        </p>


                        <p
                            class="
                                mt-2
                                text-3xl
                                font-bold
                                tracking-tight
                                text-slate-900
                            "
                        >
                            {{ $completedComplaints }}
                        </p>

                    </div>


                    <div
                        class="
                            flex
                            h-11
                            w-11
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-emerald-50
                            text-emerald-600
                        "
                    >

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>


                <div
                    class="
                        mt-4
                        flex
                        items-center
                        justify-between
                        gap-3
                    "
                >

                    <span
                        class="text-xs text-slate-500"
                    >
                        Completed or closed
                    </span>


                    <span
                        class="
                            inline-flex
                            shrink-0
                            items-center
                            gap-1
                            text-xs
                            font-semibold
                            text-emerald-600
                        "
                    >

                        View

                        <i
                            class="
                                fas
                                fa-arrow-right
                                text-[9px]
                                transition
                                group-hover:translate-x-0.5
                            "
                        ></i>

                    </span>

                </div>

            </a>

        </section>



        {{-- ========================================================= --}}
        {{-- RECENT COMPLAINTS + SERVICE UPDATES --}}
        {{-- ========================================================= --}}

        <div
            class="
                grid
                grid-cols-1
                gap-6
                xl:grid-cols-[minmax(0,1.4fr)_minmax(340px,0.8fr)]
            "
        >


            {{-- ===================================================== --}}
            {{-- RECENT COMPLAINTS --}}
            {{-- ===================================================== --}}

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

                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-4
                        border-b
                        border-slate-100
                        bg-slate-50/60
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
                                text-slate-900
                            "
                        >
                            Recent Complaints
                        </h2>


                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-slate-500
                            "
                        >
                            Your latest submitted service concerns.
                        </p>

                    </div>


                    <a
                        href="{{ route('consumer.complaints.index') }}"
                        class="
                            inline-flex
                            shrink-0
                            items-center
                            gap-1.5
                            text-xs
                            font-semibold
                            text-sky-700
                            transition
                            hover:text-sky-900
                        "
                    >

                        View all

                        <i
                            class="
                                fas
                                fa-arrow-right
                                text-[9px]
                            "
                        ></i>

                    </a>

                </div>



                @forelse ($recentComplaints as $complaint)

                    @php

                        $complaintStatus =
                            $statusLabels[$complaint->status]
                            ?? $complaint->status;


                        $complaintStatusClass =
                            $statusClasses[$complaint->status]
                            ?? 'border-slate-200 bg-slate-100 text-slate-700';


                        $complaintStatusIcon =
                            $statusIcons[$complaint->status]
                            ?? 'fa-circle';

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
                            px-5
                            py-4
                            transition
                            last:border-b-0
                            hover:bg-sky-50/40
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

                            <div
                                class="
                                    flex
                                    h-10
                                    w-10
                                    shrink-0
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-sky-50
                                    text-sky-700
                                "
                            >

                                <i class="fas fa-droplet text-sm"></i>

                            </div>


                            <div class="min-w-0 flex-1">

                                <div
                                    class="
                                        flex
                                        flex-col
                                        gap-2
                                        sm:flex-row
                                        sm:items-center
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

                                            <p
                                                class="
                                                    text-sm
                                                    font-semibold
                                                    text-slate-900
                                                "
                                            >
                                                {{ $complaint->complaint_no }}
                                            </p>


                                            <span
                                                class="
                                                    inline-flex
                                                    items-center
                                                    gap-1.5
                                                    rounded-full
                                                    border
                                                    px-2.5
                                                    py-1
                                                    text-[10px]
                                                    font-semibold
                                                    {{ $complaintStatusClass }}
                                                "
                                            >

                                                <i
                                                    class="
                                                        fas
                                                        {{ $complaintStatusIcon }}
                                                        text-[8px]
                                                    "
                                                ></i>

                                                {{ $complaintStatus }}

                                            </span>

                                        </div>


                                        <p
                                            class="
                                                mt-1
                                                truncate
                                                text-xs
                                                font-medium
                                                text-slate-600
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
                                            flex
                                            shrink-0
                                            items-center
                                            gap-3
                                        "
                                    >

                                        <span
                                            class="
                                                text-[11px]
                                                text-slate-400
                                            "
                                        >

                                            {{ $complaint
                                                ->created_at
                                                ->timezone('Asia/Manila')
                                                ->format('M d, Y') }}

                                        </span>


                                        <i
                                            class="
                                                fas
                                                fa-chevron-right
                                                text-[9px]
                                                text-slate-300
                                                transition
                                                group-hover:text-sky-600
                                            "
                                        ></i>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </a>


                @empty


                    <div
                        class="
                            px-6
                            py-14
                            text-center
                        "
                    >

                        <div
                            class="
                                mx-auto
                                flex
                                h-14
                                w-14
                                items-center
                                justify-center
                                rounded-2xl
                                bg-slate-100
                                text-slate-400
                            "
                        >

                            <i class="far fa-file-lines text-lg"></i>

                        </div>


                        <h3
                            class="
                                mt-4
                                text-sm
                                font-semibold
                                text-slate-800
                            "
                        >
                            No complaints yet
                        </h3>


                        <p
                            class="
                                mx-auto
                                mt-1
                                max-w-sm
                                text-xs
                                leading-5
                                text-slate-500
                            "
                        >
                            Your recently submitted water service
                            concerns will appear here.
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
                                px-4
                                py-2.5
                                text-xs
                                font-semibold
                                text-white
                                transition
                                hover:bg-sky-800
                            "
                        >

                            <i class="fas fa-plus text-[10px]"></i>

                            Submit Complaint

                        </a>

                    </div>

                @endforelse

            </section>



            {{-- ===================================================== --}}
            {{-- SERVICE UPDATES --}}
            {{-- ===================================================== --}}

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

                <div
                    class="
                        flex
                        items-center
                        justify-between
                        gap-4
                        border-b
                        border-slate-100
                        bg-slate-50/60
                        px-5
                        py-4
                    "
                >

                    <div>

                        <div
                            class="
                                flex
                                items-center
                                gap-2
                            "
                        >

                            <h2
                                class="
                                    text-sm
                                    font-semibold
                                    text-slate-900
                                "
                            >
                                Service Updates
                            </h2>


                            @if ($unreadAnnouncementCount > 0)

                                <span
                                    class="
                                        inline-flex
                                        min-w-5
                                        items-center
                                        justify-center
                                        rounded-full
                                        bg-red-500
                                        px-1.5
                                        py-0.5
                                        text-[10px]
                                        font-bold
                                        text-white
                                    "
                                >

                                    {{ $unreadAnnouncementCount }}

                                </span>

                            @endif

                        </div>


                        <p
                            class="
                                mt-0.5
                                text-xs
                                text-slate-500
                            "
                        >
                            Latest SWD announcements.
                        </p>

                    </div>


                    <div
                        class="
                            flex
                            h-9
                            w-9
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-amber-50
                            text-amber-600
                        "
                    >

                        <i class="fas fa-bullhorn text-sm"></i>

                    </div>

                </div>


                <div>

                    @forelse ($announcements as $announcement)

                        @php

                            $isRead =
                                $announcement
                                    ->reads
                                    ->isNotEmpty();

                        @endphp


                        <div
                            class="
                                border-b
                                border-slate-100
                                px-5
                                py-4
                                last:border-b-0
                                {{ !$isRead
                                    ? 'bg-sky-50/40'
                                    : ''
                                }}
                            "
                        >

                            <div
                                class="
                                    flex
                                    items-start
                                    gap-3
                                "
                            >

                                <span
                                    class="
                                        mt-1.5
                                        h-2
                                        w-2
                                        shrink-0
                                        rounded-full
                                        {{ !$isRead
                                            ? 'bg-sky-600'
                                            : 'bg-slate-200'
                                        }}
                                    "
                                ></span>


                                <div class="min-w-0 flex-1">

                                    <div
                                        class="
                                            flex
                                            items-start
                                            justify-between
                                            gap-2
                                        "
                                    >

                                        <p
                                            class="
                                                text-sm
                                                font-semibold
                                                text-slate-800
                                            "
                                        >
                                            {{ $announcement->title }}
                                        </p>


                                        @if (!$isRead)

                                            <span
                                                class="
                                                    shrink-0
                                                    rounded-full
                                                    bg-sky-100
                                                    px-2
                                                    py-0.5
                                                    text-[9px]
                                                    font-bold
                                                    uppercase
                                                    tracking-wide
                                                    text-sky-700
                                                "
                                            >
                                                New
                                            </span>

                                        @endif

                                    </div>


                                    @if ($announcement->message)

                                        <p
                                            class="
                                                mt-1
                                                line-clamp-3
                                                text-xs
                                                leading-5
                                                text-slate-500
                                            "
                                        >
                                            {{ $announcement->message }}
                                        </p>

                                    @endif


                                    @if ($announcement->published_at)

                                        <p
                                            class="
                                                mt-2
                                                text-[11px]
                                                text-slate-400
                                            "
                                        >

                                            <i
                                                class="
                                                    far
                                                    fa-clock
                                                    mr-1
                                                "
                                            ></i>

                                            {{ $announcement
                                                ->published_at
                                                ->timezone('Asia/Manila')
                                                ->diffForHumans() }}

                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>


                    @empty


                        <div
                            class="
                                flex
                                min-h-[260px]
                                flex-col
                                items-center
                                justify-center
                                px-6
                                text-center
                            "
                        >

                            <div
                                class="
                                    flex
                                    h-12
                                    w-12
                                    items-center
                                    justify-center
                                    rounded-xl
                                    bg-slate-100
                                    text-slate-400
                                "
                            >

                                <i class="fas fa-bullhorn"></i>

                            </div>


                            <p
                                class="
                                    mt-3
                                    text-sm
                                    font-semibold
                                    text-slate-700
                                "
                            >
                                No service updates
                            </p>


                            <p
                                class="
                                    mt-1
                                    max-w-xs
                                    text-xs
                                    leading-5
                                    text-slate-500
                                "
                            >
                                Important Sagay Water District
                                announcements will appear here.
                            </p>

                        </div>

                    @endforelse

                </div>

            </section>

        </div>


    </div>

@endsection
