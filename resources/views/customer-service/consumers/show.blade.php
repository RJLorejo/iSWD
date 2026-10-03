@extends('customer-service.layouts.app')

@section('title', 'Consumer Details')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- SUCCESS --}}
        {{-- ========================================================= --}}

        @if (session('success') && !session('temporary_password'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4">

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-check text-green-600"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- TEMPORARY PASSWORD --}}
        {{-- ========================================================= --}}

        @if (session('temporary_password'))
            <div class="overflow-hidden rounded-xl border border-green-300
                       bg-green-50 shadow-sm">

                <div class="p-4 sm:p-5">

                    <div
                        class="flex flex-col gap-4
                               lg:flex-row lg:items-start lg:justify-between">

                        <div class="flex items-start gap-3">

                            <div
                                class="flex h-9 w-9 shrink-0
                                       items-center justify-center rounded-lg
                                       bg-green-100 text-green-600">

                                <i class="fa-solid fa-key"></i>

                            </div>

                            <div>

                                <h2 class="text-sm font-bold text-green-900">
                                    Consumer Online Account Created
                                </h2>

                                <p class="mt-1 text-xs text-green-700">
                                    Give these login credentials to the consumer.
                                </p>

                            </div>

                        </div>


                        <div
                            class="w-full rounded-xl border border-green-200
                                   bg-white p-4 lg:max-w-md">

                            <div>

                                <p
                                    class="text-[11px] font-semibold uppercase
                                           tracking-wide text-gray-500">

                                    Login Email

                                </p>

                                <div class="mt-1.5 flex items-center gap-2">

                                    <div id="loginEmail"
                                        class="min-w-0 flex-1 break-all rounded-lg
                                               border border-gray-200 bg-gray-50
                                               px-3 py-2 text-sm font-medium text-gray-900">

                                        {{ $consumer->user?->email ?? $consumer->email }}

                                    </div>

                                    <button type="button" onclick="copyCredential('loginEmail', this)"
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg border
                                               border-gray-300 text-gray-600
                                               hover:bg-gray-50"
                                        title="Copy email">

                                        <i class="fa-solid fa-copy"></i>

                                    </button>

                                </div>

                            </div>


                            <div class="mt-3">

                                <p
                                    class="text-[11px] font-semibold uppercase
                                           tracking-wide text-gray-500">

                                    Temporary Password

                                </p>

                                <div class="mt-1.5 flex items-center gap-2">

                                    <div id="temporaryPassword"
                                        class="min-w-0 flex-1 rounded-lg border
                                               border-gray-200 bg-gray-50
                                               px-3 py-2 font-mono
                                               text-sm font-bold text-gray-900">

                                        {{ session('temporary_password') }}

                                    </div>

                                    <button type="button" onclick="copyCredential('temporaryPassword', this)"
                                        class="flex h-9 w-9 shrink-0 items-center
                                               justify-center rounded-lg border
                                               border-gray-300 text-gray-600
                                               hover:bg-gray-50"
                                        title="Copy password">

                                        <i class="fa-solid fa-copy"></i>

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    <p class="mt-3 text-xs text-green-800">
                        The temporary password is displayed only once.
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4
                   md:flex-row md:items-center md:justify-between">

            <div class="min-w-0">

                <p class="text-xs font-medium text-gray-500">
                    Consumer Management
                </p>

                <div class="mt-1 flex flex-wrap items-center gap-2">

                    <h1 class="break-words text-xl font-bold text-gray-900
                               sm:text-2xl">

                        {{ $consumer->full_name }}

                    </h1>


                    @if ($consumer->is_active)
                        <span
                            class="inline-flex items-center gap-1
                                   rounded-full bg-green-50 px-2.5 py-1
                                   text-xs font-semibold text-green-700">

                            <span class="h-1.5 w-1.5 rounded-full bg-green-500">
                            </span>

                            Active

                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1
                                   rounded-full bg-red-50 px-2.5 py-1
                                   text-xs font-semibold text-red-700">

                            <span class="h-1.5 w-1.5 rounded-full bg-red-500">
                            </span>

                            Inactive

                        </span>
                    @endif

                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Account #{{ $consumer->account_number }}
                </p>

            </div>


            <div class="flex flex-col gap-2 sm:flex-row">

                <a href="{{ route('customer-service.consumers.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           rounded-lg border border-gray-300 px-4 py-2
                           text-sm font-medium text-gray-700
                           transition hover:bg-gray-50">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back

                </a>

                @if ($consumer->registration_source !== 'Self Registration' || $consumer->verification_status === 'Verified')
                    <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                        class="inline-flex items-center justify-center gap-2
               rounded-lg bg-blue-600 px-4 py-2
               text-sm font-medium text-white
               transition hover:bg-blue-700">

                        <i class="fa-solid fa-pen-to-square"></i>

                        Edit Consumer

                    </a>
                @endif

            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- SELF-REGISTRATION REVIEW NOTICE --}}
        {{-- ========================================================= --}}

        @if ($consumer->registration_source === 'Self Registration' && $consumer->verification_status === 'Pending Verification')

            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center
                       justify-center rounded-lg
                       bg-amber-100 text-amber-600">

                        <i class="fa-solid fa-clock"></i>

                    </div>

                    <div>

                        <h2 class="text-sm font-semibold text-amber-900">
                            Awaiting Administrator Verification
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-amber-800">
                            This consumer registered through the Consumer Portal.
                            Customer Service can view the submitted information,
                            but editing is unavailable until an administrator
                            verifies the registration.
                        </p>

                    </div>

                </div>

            </div>
        @elseif ($consumer->registration_source === 'Self Registration' && $consumer->verification_status === 'Rejected')
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center
                       justify-center rounded-lg
                       bg-red-100 text-red-600">

                        <i class="fa-solid fa-circle-xmark"></i>

                    </div>

                    <div>

                        <h2 class="text-sm font-semibold text-red-900">
                            Registration Rejected
                        </h2>

                        <p class="mt-1 text-xs leading-5 text-red-800">
                            This self-registration was rejected during verification.
                            Customer Service can view the record, but cannot edit it.
                            The consumer must correct and resubmit the registration
                            for administrator review.
                        </p>

                        @if ($consumer->verification_reason)
                            <div
                                class="mt-3 rounded-lg border border-red-200
                               bg-white/60 px-3 py-2">

                                <p
                                    class="text-[11px] font-semibold uppercase
                                   tracking-wide text-red-500">

                                    Verification Reason

                                </p>

                                <p class="mt-1 text-xs leading-5 text-red-800">
                                    {{ $consumer->verification_reason }}
                                </p>

                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- CONSUMER INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

            <div class="border-b border-gray-100 px-4 py-4 sm:px-5">

                <h2 class="text-sm font-semibold text-gray-900">
                    Consumer Information
                </h2>

                <p class="mt-0.5 text-xs text-gray-500">
                    Personal and account information.
                </p>

            </div>


            <div class="p-4 sm:p-5">

                <div
                    class="grid grid-cols-1 gap-x-6 gap-y-5
                           sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Account Number

                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $consumer->account_number }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Full Name

                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900">
                            {{ $consumer->full_name }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Sex

                        </p>

                        <p class="mt-1 text-sm text-gray-900">
                            {{ $consumer->sex ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Contact Number

                        </p>

                        <p class="mt-1 text-sm text-gray-900">
                            {{ $consumer->phone ?: '—' }}
                        </p>

                    </div>


                    <div class="min-w-0">

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Email Address

                        </p>

                        <p class="mt-1 break-all text-sm text-gray-900">
                            {{ $consumer->email ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Registration Source

                        </p>

                        <p class="mt-1 text-sm text-gray-900">
                            {{ $consumer->registration_source ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Verification

                        </p>

                        <div class="mt-1">

                            @if ($consumer->verification_status === 'Verified')
                                <span
                                    class="inline-flex items-center gap-1.5
                                           text-sm font-medium text-green-600">

                                    <i class="fa-solid fa-circle-check"></i>

                                    Verified

                                </span>
                            @elseif ($consumer->verification_status === 'Rejected')
                                <span
                                    class="inline-flex items-center gap-1.5
                                           text-sm font-medium text-red-600">

                                    <i class="fa-solid fa-circle-xmark"></i>

                                    Rejected

                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5
                                           text-sm font-medium text-amber-600">

                                    <i class="fa-solid fa-clock"></i>

                                    {{ $consumer->verification_status ?: 'Pending Verification' }}

                                </span>
                            @endif

                        </div>

                    </div>


                    <div>

                        <p
                            class="text-[11px] font-medium uppercase
                                   tracking-wide text-gray-500">

                            Registered

                        </p>

                        <p class="mt-1 text-sm text-gray-900">
                            {{ $consumer->created_at?->format('M d, Y h:i A') ?? '—' }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SERVICE ADDRESS + PORTAL --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            {{-- Service Address --}}
            <div
                class="overflow-hidden rounded-xl border border-gray-200
                       bg-white shadow-sm xl:col-span-2">

                <div class="border-b border-gray-100 px-4 py-4 sm:px-5">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center
                                   justify-center rounded-lg
                                   bg-blue-50 text-blue-600">

                            <i class="fa-solid fa-location-dot"></i>

                        </div>

                        <div>

                            <h2 class="text-sm font-semibold text-gray-900">
                                Service Address & Location
                            </h2>

                            <p class="mt-0.5 text-xs text-gray-500">
                                Registered SWD water service connection.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-4 sm:p-5">

                    @if ($consumer->address)

                        <div class="rounded-lg border border-blue-100
                                   bg-blue-50 p-3">

                            <p class="text-sm font-medium leading-6
                                       text-gray-900">

                                {{ $consumer->address->full_address ?: 'No complete address recorded.' }}

                            </p>

                        </div>


                        @if ($consumer->address->latitude !== null && $consumer->address->longitude !== null)
                            <div
                                class="mt-4 overflow-hidden rounded-xl
                                       border border-gray-200 bg-gray-100">

                                <div id="cs-consumer-map"
                                    class="h-[300px] w-full
                                           sm:h-[360px] lg:h-[390px]">
                                </div>

                            </div>


                            <div
                                class="mt-3 flex flex-col gap-2
                                       sm:flex-row sm:items-center
                                       sm:justify-between">

                                <p class="text-xs text-gray-500">
                                    Registered service-location pin.
                                </p>

                                <a href="https://www.google.com/maps?q={{ $consumer->address->latitude }},{{ $consumer->address->longitude }}"
                                    target="_blank" rel="noopener noreferrer"
                                    class="inline-flex items-center justify-center
                                           gap-2 rounded-lg border
                                           border-gray-300 px-3 py-2
                                           text-xs font-semibold text-gray-700
                                           hover:bg-gray-50">

                                    <i class="fa-solid fa-arrow-up-right-from-square"></i>

                                    Open Map

                                </a>

                            </div>
                        @else
                            <div
                                class="mt-4 rounded-lg border border-amber-200
                                       bg-amber-50 px-4 py-3">

                                <p class="text-xs text-amber-800">
                                    No registered service-location pin has been saved.
                                </p>

                            </div>
                        @endif


                        <div
                            class="mt-4 grid grid-cols-2 gap-x-4 gap-y-4
                                   sm:grid-cols-3">

                            <div>

                                <p class="text-xs text-gray-500">
                                    House No.
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->house_no ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Street
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->street ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Purok
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->purok ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Barangay
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->barangay ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Municipality
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->municipality ?: '—' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-500">
                                    Province
                                </p>

                                <p class="mt-0.5 text-sm text-gray-900">
                                    {{ $consumer->address->province ?: '—' }}
                                </p>

                            </div>

                        </div>
                    @else
                        <div class="py-6 text-center">

                            <i
                                class="fa-solid fa-location-dot
                                       text-2xl text-gray-300">
                            </i>

                            <p class="mt-2 text-sm text-gray-500">
                                No service address recorded.
                            </p>

                        </div>

                    @endif

                </div>

            </div>


            {{-- Portal --}}
            <div class="rounded-xl border border-gray-200
                       bg-white shadow-sm">

                <div class="border-b border-gray-100 px-4 py-4 sm:px-5">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center
                                   justify-center rounded-lg
                                   bg-cyan-50 text-cyan-600">

                            <i class="fa-solid fa-globe"></i>

                        </div>

                        <div>

                            <h2 class="text-sm font-semibold text-gray-900">
                                Consumer Portal
                            </h2>

                            <p class="mt-0.5 text-xs text-gray-500">
                                iSWD account information.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-4 sm:p-5">

                    @if ($consumer->user)
                        <div
                            class="rounded-xl border p-4
                            {{ $consumer->user->is_active ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0
                                           items-center justify-center
                                           rounded-lg
                                    {{ $consumer->user->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">

                                    <i
                                        class="fa-solid
                                        {{ $consumer->user->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}">
                                    </i>

                                </div>

                                <div class="min-w-0">

                                    <p
                                        class="text-sm font-semibold
                                        {{ $consumer->user->is_active ? 'text-green-900' : 'text-red-900' }}">

                                        {{ $consumer->user->is_active ? 'Portal Account Active' : 'Portal Account Inactive' }}

                                    </p>

                                    <p
                                        class="mt-1 text-xs
                                        {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">

                                        {{ $consumer->user->is_active ? 'Consumer can access the iSWD portal.' : 'Portal access is disabled.' }}

                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="mt-4">

                            <p
                                class="text-[11px] font-medium uppercase
                                       tracking-wide text-gray-500">

                                Login Email

                            </p>

                            <p
                                class="mt-1 break-all text-sm
                                       font-medium text-gray-900">

                                {{ $consumer->user->email }}

                            </p>

                        </div>
                    @else
                        <div class="rounded-xl border border-amber-200
                                   bg-amber-50 p-4">

                            <div class="flex items-start gap-3">

                                <i
                                    class="fa-solid fa-triangle-exclamation
                                           mt-0.5 text-amber-600">
                                </i>

                                <div>

                                    <p class="text-sm font-semibold text-amber-900">
                                        Portal Account Missing
                                    </p>

                                    <p class="mt-1 text-xs text-amber-700">
                                        No linked User account.
                                    </p>

                                </div>

                            </div>

                        </div>
                    @endif

                </div>

            </div>

        </div>

    </div>


    @push('styles')

        @if ($consumer->address?->latitude !== null && $consumer->address?->longitude !== null)
            <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
        @endif

    @endpush


    @push('scripts')

        @if ($consumer->address?->latitude !== null && $consumer->address?->longitude !== null)
            <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

            <script>
                document.addEventListener(
                    'DOMContentLoaded',
                    function() {

                        const mapElement =
                            document.getElementById(
                                'cs-consumer-map'
                            );


                        if (
                            !mapElement ||
                            typeof L === 'undefined'
                        ) {
                            return;
                        }


                        const latitude =
                            @json((float) $consumer->address->latitude);

                        const longitude =
                            @json((float) $consumer->address->longitude);


                        const map =
                            L.map(
                                'cs-consumer-map'
                            ).setView(
                                [
                                    latitude,
                                    longitude
                                ],
                                17
                            );


                        L.tileLayer(
                            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                maxZoom: 19,
                                attribution: '&copy; OpenStreetMap contributors'
                            }
                        ).addTo(map);


                        L.marker(
                                [
                                    latitude,
                                    longitude
                                ]
                            )
                            .addTo(map)
                            .bindPopup(
                                'Registered water service location'
                            );


                        requestAnimationFrame(
                            function() {

                                map.invalidateSize();
                            }
                        );
                    }
                );
            </script>
        @endif


        @if (session('temporary_password'))
            <script>
                function copyCredential(
                    elementId,
                    button
                ) {

                    const element =
                        document.getElementById(
                            elementId
                        );


                    if (!element) {
                        return;
                    }


                    const value =
                        element.innerText.trim();


                    navigator.clipboard
                        .writeText(value)
                        .then(function() {

                            const icon =
                                button.querySelector('i');


                            icon.classList.remove(
                                'fa-copy'
                            );

                            icon.classList.add(
                                'fa-check'
                            );


                            setTimeout(
                                function() {

                                    icon.classList.remove(
                                        'fa-check'
                                    );

                                    icon.classList.add(
                                        'fa-copy'
                                    );
                                },
                                1500
                            );
                        });
                }
            </script>
        @endif

    @endpush

@endsection
