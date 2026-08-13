@extends('customer-service.layouts.app')

@section('title', 'Complaint Details')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <div class="flex items-center gap-3">

                    <div
                        class="w-12 h-12 rounded-2xl
                        bg-blue-100 text-blue-600
                        flex items-center justify-center">

                        <i class="fas fa-file-circle-exclamation text-xl"></i>

                    </div>

                    <div>

                        <p class="text-sm text-gray-500">
                            Customer Service
                        </p>

                        <h1 class="text-2xl font-bold text-gray-900">
                            Complaint Details
                        </h1>

                        <p class="text-sm text-gray-500 mt-1">
                            Review complaint information, verification status,
                            location, and maintenance progress.
                        </p>

                    </div>

                </div>

            </div>


            {{-- HEADER ACTIONS --}}

            <div class="flex flex-wrap gap-3">

                <a href="{{ route('customer-service.complaints.index') }}"
                    class="inline-flex items-center gap-2
                    px-4 py-2.5 rounded-xl
                    border border-gray-300
                    text-gray-700
                    hover:bg-gray-50
                    transition">

                    <i class="fas fa-arrow-left"></i>

                    Back to Complaints

                </a>


                @if (in_array($complaint->status, ['Pending', 'Verified']))
                    <a href="{{ route('customer-service.complaints.edit', $complaint) }}"
                        class="inline-flex items-center gap-2
                        px-4 py-2.5 rounded-xl
                        bg-blue-600 text-white
                        hover:bg-blue-700
                        transition">

                        <i class="fas fa-pen-to-square"></i>

                        Edit

                    </a>
                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- STATUS BANNER --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="p-5">

                <div
                    class="flex flex-col lg:flex-row
                    lg:items-center
                    lg:justify-between
                    gap-5">

                    {{-- STATUS --}}

                    <div class="flex items-center gap-4">

                        <div
                            class="w-12 h-12 rounded-xl
                            flex items-center justify-center

                            @if ($complaint->status === 'Pending') bg-yellow-100 text-yellow-600
                            @elseif ($complaint->status === 'Verified')
                                bg-green-100 text-green-600
                            @elseif ($complaint->status === 'Rejected')
                                bg-red-100 text-red-600
                            @elseif ($complaint->status === 'Assigned')
                                bg-indigo-100 text-indigo-600
                            @elseif ($complaint->status === 'Completed')
                                bg-emerald-100 text-emerald-600
                            @else
                                bg-gray-100 text-gray-600 @endif">

                            @if ($complaint->status === 'Pending')
                                <i class="fas fa-clock"></i>
                            @elseif ($complaint->status === 'Verified')
                                <i class="fas fa-circle-check"></i>
                            @elseif ($complaint->status === 'Rejected')
                                <i class="fas fa-circle-xmark"></i>
                            @elseif ($complaint->status === 'Assigned')
                                <i class="fas fa-user-check"></i>
                            @elseif ($complaint->status === 'Completed')
                                <i class="fas fa-check-double"></i>
                            @else
                                <i class="fas fa-circle-info"></i>
                            @endif

                        </div>


                        <div>

                            <p class="text-sm text-gray-500">
                                Current Complaint Status
                            </p>

                            <h2 class="text-lg font-bold text-gray-900 mt-0.5">
                                {{ $complaint->status }}
                            </h2>

                        </div>

                    </div>


                    {{-- STATUS MESSAGE --}}

                    @if ($complaint->status === 'Pending')
                        <div
                            class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-yellow-50
                            border border-yellow-100
                            text-yellow-700">

                            <i class="fas fa-hourglass-half"></i>

                            <span class="text-sm font-medium">
                                Waiting for Customer Service Verification
                            </span>

                        </div>
                    @elseif ($complaint->status === 'Verified')
                        <div
                            class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-green-50
                            border border-green-100
                            text-green-700">

                            <i class="fas fa-circle-check"></i>

                            <span class="text-sm font-medium">
                                Verified — Ready for Maintenance Management
                            </span>

                        </div>
                    @elseif ($complaint->status === 'Rejected')
                        <div
                            class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-red-50
                            border border-red-100
                            text-red-700">

                            <i class="fas fa-circle-xmark"></i>

                            <span class="text-sm font-medium">
                                Complaint Rejected
                            </span>

                        </div>
                    @elseif ($complaint->status === 'Assigned')
                        <div
                            class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-indigo-50
                            border border-indigo-100
                            text-indigo-700">

                            <i class="fas fa-user-check"></i>

                            <span class="text-sm font-medium">
                                Forwarded to Maintenance
                            </span>

                        </div>
                    @elseif ($complaint->status === 'Completed')
                        <div
                            class="inline-flex items-center gap-2
                            px-4 py-2.5 rounded-xl
                            bg-emerald-50
                            border border-emerald-100
                            text-emerald-700">

                            <i class="fas fa-check-double"></i>

                            <span class="text-sm font-medium">
                                Maintenance Completed
                            </span>

                        </div>
                    @endif

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- VERIFICATION ACTION --}}
        {{-- ========================================================= --}}

        @if ($complaint->status === 'Pending')
            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-11 h-11 rounded-xl
                            bg-blue-100 text-blue-600
                            flex items-center justify-center">

                            <i class="fas fa-clipboard-check"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Complaint Verification
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Review the complaint before forwarding it
                                to Maintenance Management.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <div
                        class="rounded-xl
                        bg-blue-50
                        border border-blue-100
                        p-4 mb-6">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-circle-info text-blue-600 mt-0.5"></i>

                            <div>

                                <p class="font-medium text-blue-900">
                                    What does verification mean?
                                </p>

                                <p class="text-sm text-blue-700 mt-1 leading-relaxed">

                                    Verification confirms that the complaint
                                    contains sufficient and valid information
                                    for maintenance action. Once verified,
                                    the complaint becomes available to the
                                    Maintenance Manager for technician assignment.

                                </p>

                            </div>

                        </div>

                    </div>


                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        {{-- VERIFY --}}

                        <div
                            class="border border-green-200
                            rounded-2xl
                            bg-green-50/50
                            p-5">

                            <div class="flex items-center gap-3 mb-4">

                                <div
                                    class="w-10 h-10 rounded-xl
                                    bg-green-100
                                    text-green-600
                                    flex items-center justify-center">

                                    <i class="fas fa-circle-check"></i>

                                </div>

                                <div>

                                    <h4 class="font-semibold text-gray-900">
                                        Verify Complaint
                                    </h4>

                                    <p class="text-xs text-gray-500">
                                        Approve for maintenance review
                                    </p>

                                </div>

                            </div>


                            <form method="POST" action="{{ route('customer-service.complaints.verify', $complaint) }}"
                                class="space-y-4">

                                @csrf

                                <div>

                                    <label for="verification_reason" class="block text-sm font-medium text-gray-700 mb-2">

                                        Verification Notes

                                        <span class="text-gray-400 font-normal">
                                            (Optional)
                                        </span>

                                    </label>

                                    <textarea name="verification_reason" id="verification_reason" rows="4"
                                        class="w-full rounded-xl border-gray-300
                                        focus:border-green-500
                                        focus:ring-green-500"
                                        placeholder="Add verification notes if necessary..."></textarea>

                                    @error('verification_reason')
                                        <p class="text-sm text-red-600 mt-1">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <button type="submit"
                                    onclick="return confirm(
                                        'Verify this complaint and forward it to Maintenance Management?'
                                    )"
                                    class="w-full inline-flex
                                    items-center justify-center
                                    gap-2 px-4 py-3 rounded-xl
                                    bg-green-600 text-white
                                    font-medium hover:bg-green-700
                                    transition">

                                    <i class="fas fa-circle-check"></i>

                                    Verify & Forward to Maintenance

                                </button>

                            </form>

                        </div>


                        {{-- REJECT --}}

                        <div
                            class="border border-red-200
                            rounded-2xl
                            bg-red-50/50
                            p-5">

                            <div class="flex items-center gap-3 mb-4">

                                <div
                                    class="w-10 h-10 rounded-xl
                                    bg-red-100
                                    text-red-600
                                    flex items-center justify-center">

                                    <i class="fas fa-circle-xmark"></i>

                                </div>

                                <div>

                                    <h4 class="font-semibold text-gray-900">
                                        Reject Complaint
                                    </h4>

                                    <p class="text-xs text-gray-500">
                                        Return invalid or insufficient complaints
                                    </p>

                                </div>

                            </div>


                            <form method="POST" action="{{ route('customer-service.complaints.reject', $complaint) }}"
                                id="rejectComplaintForm">

                                @csrf

                                <div>

                                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">

                                        Rejection Reason

                                    </label>

                                    <textarea name="verification_reason" id="rejection_reason" rows="4" required
                                        class="w-full rounded-xl
                                        border-gray-300
                                        focus:border-red-500
                                        focus:ring-red-500"
                                        placeholder="Explain why this complaint is being rejected..."></textarea>

                                    @error('verification_reason')
                                        <p class="text-sm text-red-600 mt-1">
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                <button type="button" onclick="rejectComplaint()"
                                    class="w-full mt-4
                                    inline-flex items-center
                                    justify-center gap-2
                                    px-4 py-3 rounded-xl
                                    border border-red-300
                                    text-red-700 font-medium
                                    hover:bg-red-100
                                    transition">

                                    <i class="fas fa-circle-xmark"></i>

                                    Reject Complaint

                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            </x-form.card>
        @endif


        {{-- ========================================================= --}}
        {{-- VERIFICATION INFORMATION --}}
        {{-- ========================================================= --}}

        @if (in_array($complaint->status, ['Verified', 'Rejected', 'Assigned', 'Completed']))

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-gray-100 text-gray-600
                            flex items-center justify-center">

                            <i class="fas fa-user-check"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Verification Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Record of the Customer Service verification action.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Verified / Reviewed By

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->verifier?->full_name ?? '—' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Verification Date

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->verified_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>


                        <div>

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Result

                            </p>

                            <div class="mt-1">

                                @if ($complaint->status === 'Rejected')
                                    <span
                                        class="inline-flex items-center gap-2
                                        px-3 py-1.5 rounded-full
                                        bg-red-100 text-red-700
                                        text-sm font-medium">

                                        <i class="fas fa-circle-xmark"></i>

                                        Rejected

                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-2
                                        px-3 py-1.5 rounded-full
                                        bg-green-100 text-green-700
                                        text-sm font-medium">

                                        <i class="fas fa-circle-check"></i>

                                        Verified

                                    </span>
                                @endif

                            </div>

                        </div>

                    </div>


                    @if ($complaint->verification_reason)
                        <div class="mt-6">

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Verification / Rejection Notes

                            </p>

                            <div
                                class="mt-2 p-4 rounded-xl
                                bg-gray-50 border border-gray-200
                                text-gray-700 whitespace-pre-line">

                                {{ $complaint->verification_reason }}

                            </div>

                        </div>
                    @endif

                </div>

            </x-form.card>

        @endif


        {{-- ========================================================= --}}
        {{-- COMPLAINT SUMMARY --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex flex-col md:flex-row
                    md:items-center md:justify-between gap-4">

                    <div>

                        <p class="text-xs uppercase tracking-wide
                        text-gray-500 font-medium">

                            Complaint Number

                        </p>

                        <h2 class="text-xl font-bold text-gray-900 mt-1">

                            {{ $complaint->complaint_no }}

                        </h2>

                    </div>


                    <div class="flex flex-wrap gap-2">

                        {{-- PRIORITY --}}

                        <span
                            class="px-3 py-1.5 rounded-full
                            text-sm font-medium

                            @if ($complaint->priority === 'Critical') bg-red-100 text-red-700
                            @elseif ($complaint->priority === 'High')
                                bg-orange-100 text-orange-700
                            @elseif ($complaint->priority === 'Medium')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-green-100 text-green-700 @endif">

                            <i class="fas fa-flag mr-1"></i>

                            {{ $complaint->priority }}

                        </span>


                        {{-- STATUS --}}

                        <span
                            class="px-3 py-1.5 rounded-full
                            text-sm font-medium

                            @if ($complaint->status === 'Verified') bg-green-100 text-green-700
                            @elseif ($complaint->status === 'Rejected')
                                bg-red-100 text-red-700
                            @elseif ($complaint->status === 'Pending')
                                bg-yellow-100 text-yellow-700
                            @elseif ($complaint->status === 'Assigned')
                                bg-indigo-100 text-indigo-700
                            @elseif ($complaint->status === 'Completed')
                                bg-emerald-100 text-emerald-700
                            @else
                                bg-blue-100 text-blue-700 @endif">

                            <i class="fas fa-circle-info mr-1"></i>

                            {{ $complaint->status }}

                        </span>

                    </div>

                </div>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                    {{-- COMPLAINANT --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Complainant
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            @if ($complaint->complainant_name)
                                {{ $complaint->complainant_name }}
                            @elseif ($complaint->consumer)
                                {{ $complaint->consumer->full_name }}
                            @else
                                Walk-in / Unregistered Complainant
                            @endif

                        </p>

                    </div>


                    {{-- PHONE --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Contact Number
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->complainant_phone ?? ($complaint->consumer?->phone ?? '—') }}

                        </p>

                    </div>


                    {{-- CONSUMER NUMBER --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Consumer Number
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->consumer?->consumer_no ?? 'Walk-in' }}

                        </p>

                    </div>


                    {{-- CATEGORY --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Complaint Category
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            @if ($complaint->category)
                                {{ $complaint->category->code }}
                                —
                                {{ $complaint->category->name }}
                            @else
                                —
                            @endif

                        </p>

                    </div>

                </div>


                {{-- REGISTERED CONSUMER INFO --}}

                @if ($complaint->consumer)
                    <div class="mt-6 p-4 rounded-xl
                        bg-blue-50 border border-blue-100">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-9 h-9 rounded-lg
                                bg-blue-100 text-blue-600
                                flex items-center justify-center">

                                <i class="fas fa-user"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-blue-900">
                                    Registered Consumer
                                </p>

                                <p class="text-sm text-blue-700 mt-1">

                                    This complaint is associated with
                                    registered consumer
                                    <strong>
                                        {{ $complaint->consumer->consumer_no }}
                                    </strong>.

                                </p>

                            </div>

                        </div>

                    </div>
                @else
                    <div class="mt-6 p-4 rounded-xl
                        bg-gray-50 border border-gray-200">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-9 h-9 rounded-lg
                                bg-gray-100 text-gray-600
                                flex items-center justify-center">

                                <i class="fas fa-person-walking"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-gray-800">
                                    Walk-in / Unregistered Complaint
                                </p>

                                <p class="text-sm text-gray-600 mt-1">

                                    This complaint was submitted without
                                    an associated registered consumer account.

                                </p>

                            </div>

                        </div>

                    </div>
                @endif


                {{-- CREATED DATE --}}

                <div class="mt-6">

                    <p class="text-xs text-gray-500">
                        Reported Date
                    </p>

                    <p class="font-medium text-gray-900 mt-1">

                        {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                    </p>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- COMPLAINT INFORMATION --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-orange-100 text-orange-600
                        flex items-center justify-center">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Complaint Information
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Reported water service problem.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6 space-y-6">

                {{-- SUBJECT --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Complaint Subject
                    </p>

                    <p class="text-lg font-semibold text-gray-900 mt-1">

                        {{ $complaint->subject }}

                    </p>

                </div>


                {{-- DESCRIPTION --}}

                <div>

                    <p class="text-sm text-gray-500">
                        Problem Description
                    </p>

                    <div
                        class="mt-2 p-4
                        bg-gray-50 rounded-xl
                        text-gray-700
                        whitespace-pre-line">

                        {{ $complaint->description }}

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- LOCATION --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-green-100 text-green-600
                        flex items-center justify-center">

                        <i class="fas fa-location-dot"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Problem Location
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Location reported by the complainant.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6 space-y-6">

                {{-- ADDRESS --}}

                <div>

                    <p class="text-xs uppercase tracking-wide
                    text-gray-500 font-medium">

                        Complete Address

                    </p>

                    <div
                        class="mt-2 p-4 rounded-xl
                        bg-gray-50
                        border border-gray-200">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-location-dot
                            text-green-600 mt-1"></i>

                            <p class="font-medium text-gray-900">

                                {{ $complaint->address ?: 'No address recorded.' }}

                            </p>

                        </div>

                    </div>

                </div>


                {{-- LANDMARK --}}

                <div>

                    <p class="text-xs uppercase tracking-wide
                    text-gray-500 font-medium">

                        Landmark

                    </p>

                    <p class="font-medium text-gray-900 mt-1">

                        {{ $complaint->landmark ?: 'No landmark provided.' }}

                    </p>

                </div>


                {{-- COORDINATES --}}

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div class="p-4 rounded-xl
                        bg-gray-50 border border-gray-200">

                        <p class="text-xs text-gray-500">
                            Latitude
                        </p>

                        <p class="font-mono font-medium text-gray-900 mt-1">

                            {{ $complaint->latitude ?? 'Not recorded' }}

                        </p>

                    </div>


                    <div class="p-4 rounded-xl
                        bg-gray-50 border border-gray-200">

                        <p class="text-xs text-gray-500">
                            Longitude
                        </p>

                        <p class="font-mono font-medium text-gray-900 mt-1">

                            {{ $complaint->longitude ?? 'Not recorded' }}

                        </p>

                    </div>

                </div>


                {{-- MAP --}}

                @if ($complaint->latitude && $complaint->longitude)
                    <div id="complaint-map"
                        class="w-full h-[420px]
                        rounded-2xl
                        border border-gray-300
                        overflow-hidden
                        relative z-0">
                    </div>
                @else
                    <div
                        class="p-5 rounded-xl
                        bg-gray-50
                        border border-gray-200
                        text-gray-500">

                        <i class="fas fa-map-location-dot mr-2"></i>

                        No map location was recorded for this complaint.

                    </div>
                @endif

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- PHOTO EVIDENCE --}}
        {{-- ========================================================= --}}

        @if ($complaint->photo)
            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                            bg-purple-100 text-purple-600
                            flex items-center justify-center">

                            <i class="fas fa-camera"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Photo Evidence
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Photo submitted with the complaint.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                        class="max-w-3xl w-full rounded-2xl
                        border border-gray-200">

                    <p class="text-xs text-gray-500 mt-3">
                        Original complaint photo evidence.
                    </p>

                </div>

            </x-form.card>
        @endif


        {{-- ========================================================= --}}
        {{-- MAINTENANCE REPORT --}}
        {{-- ========================================================= --}}

        @if ($complaint->maintenanceReport)

            <x-form.card>

                {{-- HEADER --}}

                <div class="px-6 py-5 border-b border-gray-100">

                    <div
                        class="flex flex-col md:flex-row
                        md:items-center
                        md:justify-between gap-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                                <i class="fas fa-screwdriver-wrench"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Maintenance Report
                                </h3>

                                <p class="text-sm text-gray-500 mt-1">
                                    Technical repair information recorded by Maintenance.
                                </p>

                            </div>

                        </div>


                        <span
                            class="inline-flex items-center gap-2
                            px-3 py-1.5 rounded-full
                            bg-emerald-100 text-emerald-700
                            text-sm font-medium">

                            <i class="fas fa-circle-check"></i>

                            Report Available

                        </span>

                    </div>

                </div>


                <div class="p-6 space-y-8">

                    {{-- ================================================= --}}
                    {{-- TECHNICIAN / DATES --}}
                    {{-- ================================================= --}}

                    <div
                        class="grid grid-cols-1
                        md:grid-cols-2
                        lg:grid-cols-4 gap-5">

                        {{-- TECHNICIAN --}}

                        <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Technician

                            </p>

                            <p class="font-semibold text-gray-900 mt-1">

                                {{ $complaint->maintenanceReport->technician?->name ?? 'Not assigned' }}

                            </p>

                        </div>


                        {{-- STARTED --}}

                        <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Maintenance Started

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->maintenanceReport->started_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>


                        {{-- SUBMITTED --}}

                        <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Report Submitted

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>


                        {{-- CREATED --}}

                        <div class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200">

                            <p
                                class="text-xs uppercase tracking-wide
                            text-gray-500 font-medium">

                                Report Created

                            </p>

                            <p class="font-medium text-gray-900 mt-1">

                                {{ $complaint->maintenanceReport->created_at?->format('M d, Y h:i A') ?? '—' }}

                            </p>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- DIAGNOSIS --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-stethoscope text-indigo-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Diagnosis
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->diagnosis ?: 'No diagnosis recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- ROOT CAUSE --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-magnifying-glass text-orange-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Root Cause
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->root_cause ?: 'No root cause recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- WORK PERFORMED --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-tools text-blue-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Work Performed
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-blue-50 border border-blue-100
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->work_performed ?: 'No work performed details recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- REPAIR PROCEDURE --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-list-check text-green-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Repair Procedure
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-gray-50 border border-gray-200
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->repair_procedure ?: 'No repair procedure recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- MATERIALS / PARTS / TOOLS --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-4">

                            <i class="fas fa-boxes-stacked text-purple-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Maintenance Resources
                            </h4>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                            {{-- MATERIALS --}}

                            <div
                                class="p-4 rounded-xl
                                bg-gray-50
                                border border-gray-200">

                                <p
                                    class="text-xs uppercase tracking-wide
                                text-gray-500 font-medium">

                                    Materials Used

                                </p>

                                <div
                                    class="mt-2 text-sm text-gray-700
                                    whitespace-pre-line">

                                    {{ $complaint->maintenanceReport->materials_used ?: 'None recorded.' }}

                                </div>

                            </div>


                            {{-- PARTS --}}

                            <div
                                class="p-4 rounded-xl
                                bg-gray-50
                                border border-gray-200">

                                <p
                                    class="text-xs uppercase tracking-wide
                                text-gray-500 font-medium">

                                    Parts Replaced

                                </p>

                                <div
                                    class="mt-2 text-sm text-gray-700
                                    whitespace-pre-line">

                                    {{ $complaint->maintenanceReport->parts_replaced ?: 'None recorded.' }}

                                </div>

                            </div>


                            {{-- TOOLS --}}

                            <div
                                class="p-4 rounded-xl
                                bg-gray-50
                                border border-gray-200">

                                <p
                                    class="text-xs uppercase tracking-wide
                                text-gray-500 font-medium">

                                    Tools Used

                                </p>

                                <div
                                    class="mt-2 text-sm text-gray-700
                                    whitespace-pre-line">

                                    {{ $complaint->maintenanceReport->tools_used ?: 'None recorded.' }}

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- TECHNICIAN NOTES --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-note-sticky text-yellow-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Technician Notes
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-yellow-50 border border-yellow-100
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->technician_notes ?: 'No technician notes recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- COMPLETION REMARKS --}}
                    {{-- ================================================= --}}

                    <div>

                        <div class="flex items-center gap-2 mb-3">

                            <i class="fas fa-comment-check text-emerald-600"></i>

                            <h4 class="font-semibold text-gray-900">
                                Completion Remarks
                            </h4>

                        </div>


                        <div
                            class="p-4 rounded-xl
                            bg-emerald-50 border border-emerald-100
                            text-gray-700 whitespace-pre-line">

                            {{ $complaint->maintenanceReport->completion_remarks ?: 'No completion remarks recorded.' }}

                        </div>

                    </div>


                    {{-- ================================================= --}}
                    {{-- BEFORE / AFTER PHOTOS --}}
                    {{-- ================================================= --}}

                    @if ($complaint->maintenanceReport->before_photo || $complaint->maintenanceReport->after_photo)

                        <div>

                            <div class="flex items-center gap-2 mb-4">

                                <i class="fas fa-images text-purple-600"></i>

                                <h4 class="font-semibold text-gray-900">
                                    Maintenance Photo Evidence
                                </h4>

                            </div>


                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                {{-- BEFORE PHOTO --}}

                                @if ($complaint->maintenanceReport->before_photo)
                                    <div>

                                        <p
                                            class="text-sm font-medium
                                        text-gray-700 mb-2">

                                            Before Maintenance

                                        </p>

                                        <div
                                            class="rounded-2xl overflow-hidden
                                            border border-gray-200
                                            bg-gray-50">

                                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->before_photo) }}"
                                                alt="Before maintenance" class="w-full h-72 object-cover">

                                        </div>

                                    </div>
                                @endif


                                {{-- AFTER PHOTO --}}

                                @if ($complaint->maintenanceReport->after_photo)
                                    <div>

                                        <p
                                            class="text-sm font-medium
                                        text-gray-700 mb-2">

                                            After Maintenance

                                        </p>

                                        <div
                                            class="rounded-2xl overflow-hidden
                                            border border-gray-200
                                            bg-gray-50">

                                            <img src="{{ asset('storage/' . $complaint->maintenanceReport->after_photo) }}"
                                                alt="After maintenance" class="w-full h-72 object-cover">

                                        </div>

                                    </div>
                                @endif

                            </div>

                        </div>

                    @endif


                    {{-- INFORMATION NOTICE --}}

                    <div
                        class="p-4 rounded-xl
                        bg-indigo-50
                        border border-indigo-100">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-circle-info
                            text-indigo-600 mt-0.5"></i>

                            <div>

                                <p class="text-sm font-semibold text-indigo-900">
                                    Maintenance Information
                                </p>

                                <p class="text-sm text-indigo-700 mt-1">

                                    This technical report was recorded by the
                                    Maintenance team. Customer Service can
                                    review the repair details and completion
                                    status but should not modify the technical
                                    maintenance record from this page.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </x-form.card>
        @else
            {{-- NO MAINTENANCE REPORT --}}

            @if (in_array($complaint->status, ['Assigned', 'Completed', 'Verified']))
                <x-form.card>

                    <div class="p-6">

                        <div
                            class="flex items-start gap-4
                            p-5 rounded-2xl
                            bg-gray-50
                            border border-gray-200">

                            <div
                                class="w-11 h-11 rounded-xl
                                bg-gray-200 text-gray-600
                                flex items-center justify-center
                                flex-shrink-0">

                                <i class="fas fa-screwdriver-wrench"></i>

                            </div>

                            <div>

                                <h3 class="font-semibold text-gray-900">
                                    Maintenance Report Not Available
                                </h3>

                                <p class="text-sm text-gray-600 mt-1 leading-relaxed">

                                    The complaint has been forwarded to Maintenance,
                                    but no maintenance report has been submitted yet.

                                </p>

                            </div>

                        </div>

                    </div>

                </x-form.card>
            @endif

        @endif


        {{-- ========================================================= --}}
        {{-- TIMELINE --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-6 py-5 border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                        bg-gray-100 text-gray-600
                        flex items-center justify-center">

                        <i class="fas fa-clock-rotate-left"></i>

                    </div>

                    <div>

                        <h3 class="font-semibold text-gray-900">
                            Complaint Timeline
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Important dates recorded by the system.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-6">

                <div class="grid grid-cols-1
                    md:grid-cols-2
                    lg:grid-cols-4 gap-6">

                    {{-- CREATED --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Created
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->created_at?->format('M d, Y h:i A') ?? '—' }}

                        </p>

                    </div>


                    {{-- VERIFIED --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Verified / Reviewed
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->verified_at?->format('M d, Y h:i A') ?? 'Not yet reviewed' }}

                        </p>

                    </div>


                    {{-- COMPLETED --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Completed
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->completed_at?->format('M d, Y h:i A') ?? 'Not yet completed' }}

                        </p>

                    </div>


                    {{-- UPDATED --}}

                    <div>

                        <p class="text-xs text-gray-500">
                            Last Updated
                        </p>

                        <p class="font-medium text-gray-900 mt-1">

                            {{ $complaint->updated_at?->format('M d, Y h:i A') ?? '—' }}

                        </p>

                    </div>

                </div>


                {{-- MAINTENANCE TIMELINE --}}

                @if ($complaint->maintenanceReport)
                    <div class="mt-6 pt-6
                        border-t border-gray-100">

                        <p class="text-xs uppercase tracking-wide
                        text-gray-500 font-medium mb-4">

                            Maintenance Timeline

                        </p>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>

                                <p class="text-xs text-gray-500">
                                    Maintenance Started
                                </p>

                                <p class="font-medium text-gray-900 mt-1">

                                    {{ $complaint->maintenanceReport->started_at?->format('M d, Y h:i A') ?? '—' }}

                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Maintenance Report Submitted
                                </p>

                                <p class="font-medium text-gray-900 mt-1">

                                    {{ $complaint->maintenanceReport->submitted_at?->format('M d, Y h:i A') ?? '—' }}

                                </p>

                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </x-form.card>


        {{-- ========================================================= --}}
        {{-- DELETE --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div
                class="p-6 flex flex-col md:flex-row
                md:items-center
                md:justify-between gap-4">

                <div>

                    <div class="flex items-center gap-2">

                        <i class="fas fa-trash-can text-red-600"></i>

                        <h3 class="font-semibold text-gray-900">
                            Delete Complaint
                        </h3>

                    </div>

                    <p class="text-sm text-gray-500 mt-1">
                        The complaint will be moved to deleted records.
                    </p>

                </div>


                <form action="{{ route('customer-service.complaints.destroy', $complaint) }}" method="POST"
                    onsubmit="return confirm(
                        'Are you sure you want to delete this complaint?'
                    );">

                    @csrf
                    @method('DELETE')

                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl
                        bg-red-600 text-white
                        hover:bg-red-700
                        transition">

                        <i class="fas fa-trash mr-2"></i>

                        Delete Complaint

                    </button>

                </form>

            </div>

        </x-form.card>

    </div>


    {{-- ============================================================= --}}
    {{-- REJECT CONFIRMATION --}}
    {{-- ============================================================= --}}

    <script>
        function rejectComplaint() {

            const textarea =
                document.getElementById('rejection_reason');

            const reason =
                textarea.value.trim();

            if (!reason) {

                alert('Please provide a rejection reason.');

                textarea.focus();

                return;
            }


            const confirmed = confirm(
                'Are you sure you want to reject this complaint?\n\n' +
                'This complaint will not be forwarded to Maintenance Management.'
            );


            if (confirmed) {

                document
                    .getElementById('rejectComplaintForm')
                    .submit();

            }

        }
    </script>


    {{-- ============================================================= --}}
    {{-- MAP --}}
    {{-- ============================================================= --}}

    @if ($complaint->latitude && $complaint->longitude)
        @push('styles')
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        @endpush


        @push('scripts')
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function() {

                        const mapElement =
                            document.getElementById(
                                'complaint-map'
                            );


                        if (!mapElement) {
                            return;
                        }


                        const latitude =
                            {{ (float) $complaint->latitude }};


                        const longitude =
                            {{ (float) $complaint->longitude }};


                        const map =
                            L.map(
                                'complaint-map', {
                                    zoomControl: true,
                                    attributionControl: true
                                }
                            );


                        L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors'
                            }
                        ).addTo(map);


                        map.setView(
                            [
                                latitude,
                                longitude
                            ],
                            17
                        );


                        const marker =
                            L.marker(
                                [
                                    latitude,
                                    longitude
                                ]
                            ).addTo(map);


                        marker.bindPopup(`

                            <div class="text-sm">

                                <div
                                    class="font-semibold text-gray-900">

                                    <i
                                        class="fas fa-file-circle-exclamation
                                        text-blue-600 mr-1">
                                    </i>

                                    {{ $complaint->complaint_no }}

                                </div>


                                <div class="text-gray-700 mt-1">

                                    {{ addslashes($complaint->subject) }}

                                </div>


                                <div class="text-gray-500 mt-1">

                                    {{ addslashes($complaint->address ?? '') }}

                                </div>

                            </div>

                        `).openPopup();


                        function refreshMap() {

                            map.invalidateSize({
                                animate: false,
                                pan: false
                            });

                        }


                        requestAnimationFrame(
                            function() {

                                refreshMap();


                                setTimeout(
                                    refreshMap,
                                    100
                                );


                                setTimeout(
                                    refreshMap,
                                    300
                                );


                                setTimeout(
                                    refreshMap,
                                    600
                                );

                            }
                        );


                        window.addEventListener(
                            'resize',
                            refreshMap
                        );

                    }
                );
            </script>
        @endpush
    @endif

@endsection
