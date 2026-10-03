@extends('admin.layouts.app')

@section('title', 'Review Consumer Registration')

@section('content')

    <div class="max-w-7xl mx-auto space-y-4" x-data="{

        rejectModal: {{ $errors->has('verification_reason') ? 'true' : 'false' }},

        approveModal: false,

        approving: false,

        rejecting: false

    }">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <a href="{{ route('admin.consumer-verifications.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-sky-700 hover:text-sky-800">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Consumer Verifications

                </a>

                <h1 class="text-xl sm:text-2xl font-bold text-slate-900 mt-4">

                    Review Consumer Registration

                </h1>

                <p class="text-sm text-slate-500 mt-2">

                    Verify the submitted information against official

                    Sagay Water District records before approving access.

                </p>

            </div>

            <div>

                @if ($consumer->verification_status === 'Verified')
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-green-100 text-green-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-circle-check"></i>

                        Verified

                    </span>
                @elseif ($consumer->verification_status === 'Rejected')
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-red-100 text-red-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-circle-xmark"></i>

                        Rejected

                    </span>
                @else
                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-amber-100 text-amber-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-clock"></i>

                        Pending Verification

                    </span>
                @endif

            </div>

        </div>

        @if ($consumer->verification_status === 'Pending Verification')
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-shield-halved"></i>

                    </div>

                    <div>

                        <h2 class="font-semibold text-amber-900">

                            Verification Required

                        </h2>

                        <p class="text-sm text-amber-800 mt-1 leading-6">

                            Confirm that the account number and consumer

                            name match official Sagay Water District records

                            before approving this registration.

                        </p>

                    </div>

                </div>

            </div>
        @endif

        <div class="grid xl:grid-cols-3 gap-4">

            <div class="xl:col-span-2 space-y-4">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-5 py-4 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">

                                <i class="fa-solid fa-droplet"></i>

                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">

                                    Water Service Account

                                </h2>

                                <p class="text-xs text-slate-500 mt-1">

                                    Verify against the official SWD account record.

                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">

                            Account Number

                        </p>

                        <p class="text-lg font-bold text-sky-700 mt-1">

                            {{ $consumer->account_number }}

                        </p>

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-4 sm:px-5 py-4 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <h2 class="font-bold text-slate-900">

                                Consumer Information

                            </h2>

                        </div>

                    </div>

                    <div class="p-4 sm:p-5 grid sm:grid-cols-2 gap-4">

                        <div class="sm:col-span-2">

                            <p class="text-xs text-slate-500">

                                Registered Consumer Name

                            </p>

                            <p class="font-semibold text-slate-900 mt-1">

                                {{ $consumer->full_name }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-500">

                                Sex

                            </p>

                            <p class="font-medium text-slate-800 mt-1">

                                {{ $consumer->sex ?? '—' }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-500">

                                Registration Source

                            </p>

                            <p class="font-medium text-slate-800 mt-1">

                                {{ $consumer->registration_source }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-500">

                                Email Address

                            </p>

                            <p class="font-medium text-slate-800 mt-1 break-all">

                                {{ $consumer->email }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-500">

                                Mobile Number

                            </p>

                            <p class="font-medium text-slate-800 mt-1">

                                {{ $consumer->phone }}

                            </p>

                        </div>

                    </div>

                </div>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-4 sm:px-5 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-9 h-9 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>

                            <div>
                                <h2 class="text-sm font-bold text-slate-900">
                                    Service Address & Location
                                </h2>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    Registered SWD service location.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 sm:p-5">
                        @if ($consumer->address)
                            <div class="grid gap-4 sm:grid-cols-2">
                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Registered Address
                                    </p>
                                    <p class="mt-1 text-sm font-medium leading-6 text-slate-800">
                                        {{ $consumer->address->full_address ?: 'No complete address provided.' }}
                                    </p>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] text-slate-500">Municipality</p>
                                        <p class="mt-1 text-sm font-medium text-slate-800">
                                            {{ $consumer->address->municipality ?: 'Sagay' }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-slate-50 p-3">
                                        <p class="text-[11px] text-slate-500">Province</p>
                                        <p class="mt-1 text-sm font-medium text-slate-800">
                                            {{ $consumer->address->province ?: 'Negros Occidental' }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if ($consumer->address->latitude !== null && $consumer->address->longitude !== null)
                                <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                                    <div id="admin-consumer-map" class="h-[280px] w-full sm:h-[340px] lg:h-[380px]"></div>
                                </div>

                                <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                                    <p class="text-xs text-slate-500">
                                        Pin submitted for the registered water service connection.
                                    </p>

                                    <a href="https://www.google.com/maps?q={{ $consumer->address->latitude }},{{ $consumer->address->longitude }}"
                                        target="_blank" rel="noopener noreferrer"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                        Open Map
                                    </a>
                                </div>
                            @else
                                <div
                                    class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                                    No map location has been saved for this consumer yet.
                                </div>
                            @endif
                        @else
                            <p class="text-sm text-slate-500">
                                No service address record is available.
                            </p>
                        @endif
                    </div>
                </div>

            </div>

            <div class="space-y-4">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">

                    <h2 class="font-bold text-slate-900">

                        Registration Details

                    </h2>

                    <div class="mt-5 space-y-5">

                        <div>

                            <p class="text-xs text-slate-500">

                                Submitted

                            </p>

                            <p class="text-sm font-medium text-slate-800 mt-1">

                                {{ $consumer->created_at?->format('F d, Y') }}

                            </p>

                            <p class="text-xs text-slate-400 mt-1">

                                {{ $consumer->created_at?->format('h:i A') }}

                            </p>

                        </div>

                        <div>

                            <p class="text-xs text-slate-500">

                                Portal Account

                            </p>

                            @if ($consumer->user?->is_active)
                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-green-600">

                                    <i class="fa-solid fa-circle-check"></i>

                                    Active

                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-slate-500">

                                    <i class="fa-solid fa-circle-pause"></i>

                                    Inactive

                                </span>
                            @endif

                        </div>

                        @if ($consumer->verified_at)
                            <div>

                                <p class="text-xs text-slate-500">

                                    Reviewed

                                </p>

                                <p class="text-sm font-medium text-slate-800 mt-1">

                                    {{ $consumer->verified_at->format('F d, Y h:i A') }}

                                </p>

                            </div>
                        @endif

                        @if ($consumer->verifier)
                            <div>

                                <p class="text-xs text-slate-500">

                                    Reviewed By

                                </p>

                                <p class="text-sm font-medium text-slate-800 mt-1">

                                    {{ $consumer->verifier->full_name }}

                                </p>

                            </div>
                        @endif

                    </div>

                </div>

                @if ($consumer->verification_status === 'Pending Verification')
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5">

                        <h2 class="font-bold text-slate-900">

                            Verification Decision

                        </h2>

                        <p class="text-sm text-slate-500 mt-2 leading-6">

                            Approve the registration if the submitted information

                            matches official SWD records.

                        </p>

                        <div class="mt-5 space-y-3">

                            <button type="button" @click="approveModal = true"
                                class="w-full inline-flex items-center justify-center gap-2

                                       rounded-xl bg-green-600 text-white

                                       px-5 py-3 font-semibold

                                       hover:bg-green-700

                                       focus:outline-none

                                       focus:ring-2

                                       focus:ring-green-500

                                       focus:ring-offset-2

                                       transition">

                                <i class="fa-solid fa-check"></i>

                                Approve Registration

                            </button>

                            <button type="button" @click="rejectModal = true"
                                class="w-full inline-flex items-center justify-center gap-2

                                       rounded-xl border border-red-300

                                       bg-white text-red-700

                                       px-5 py-3 font-semibold

                                       hover:bg-red-50

                                       focus:outline-none

                                       focus:ring-2

                                       focus:ring-red-500

                                       focus:ring-offset-2

                                       transition">

                                <i class="fa-solid fa-xmark"></i>

                                Reject Registration

                            </button>

                        </div>

                    </div>
                @endif

                @if ($consumer->verification_status === 'Rejected')
                    <div class="rounded-2xl border border-red-200 bg-red-50 p-6">

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-xmark text-red-600"></i>

                            <h2 class="font-bold text-red-900">

                                Verification Reason

                            </h2>

                        </div>

                        <p class="text-sm text-red-800 mt-3 leading-6">

                            {{ $consumer->verification_reason ?: 'No reason was provided.' }}

                        </p>

                    </div>
                @endif

            </div>

        </div>

        <div x-show="approveModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4">

            <div @click.outside="approveModal = false"
                class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">

                <div class="p-6">

                    <div
                        class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto">

                        <i class="fa-solid fa-user-check text-2xl"></i>

                    </div>

                    <div class="text-center mt-4">

                        <h2 class="text-xl font-bold text-slate-900">

                            Approve Registration?

                        </h2>

                        <p class="text-sm text-slate-500 mt-2 leading-6">

                            You are approving the registration for

                            <strong class="text-slate-700">

                                {{ $consumer->full_name }}

                            </strong>.

                            Their iSWD Consumer Portal account will become active.

                        </p>

                    </div>

                    <form method="POST" action="{{ route('admin.consumer-verifications.approve', $consumer) }}"
                        @submit="approving = true" class="mt-6">

                        @csrf

                        @method('PATCH')

                        <div class="grid grid-cols-2 gap-3">

                            <button type="button" @click="approveModal = false" :disabled="approving"
                                class="inline-flex items-center justify-center

                                       rounded-xl border border-slate-300

                                       px-4 py-3 font-semibold text-slate-700

                                       hover:bg-slate-50

                                       disabled:opacity-60

                                       disabled:cursor-not-allowed

                                       transition">

                                Cancel

                            </button>

                            <button type="submit" :disabled="approving"
                                class="inline-flex items-center justify-center

                                       rounded-xl bg-green-600

                                       px-4 py-3 font-semibold text-white

                                       hover:bg-green-700

                                       disabled:opacity-60

                                       disabled:cursor-not-allowed

                                       transition">

                                <span x-show="!approving" class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-check"></i>

                                    Approve

                                </span>

                                <span x-show="approving" x-cloak class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-spinner animate-spin"></i>

                                    Approving...

                                </span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

        <div x-show="rejectModal" x-cloak x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4">

            <div @click.outside="rejectModal = false"
                class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">

                <div class="p-6">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-user-xmark text-xl"></i>

                        </div>

                        <div>

                            <h2 class="text-xl font-bold text-slate-900">

                                Reject Registration

                            </h2>

                            <p class="text-sm text-slate-500 mt-1 leading-6">

                                Provide a clear reason for rejecting this

                                consumer registration.

                            </p>

                        </div>

                    </div>

                    <form method="POST" action="{{ route('admin.consumer-verifications.reject', $consumer) }}"
                        @submit="rejecting = true" class="mt-6">

                        @csrf

                        @method('PATCH')

                        <label for="verification_reason" class="block text-sm font-semibold text-slate-700 mb-2">

                            Verification Reason

                            <span class="text-red-500">*</span>

                        </label>

                        <textarea id="verification_reason" name="verification_reason" rows="5" required maxlength="1000"
                            placeholder="Example: The submitted account number does not match the registered consumer name in Sagay Water District records."
                            class="w-full rounded-xl

                                   border-slate-300

                                   focus:border-red-500

                                   focus:ring-red-500

                                   px-4 py-3">{{ old('verification_reason') }}</textarea>

                        @error('verification_reason')
                            <p class="mt-2 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror

                        <div class="mt-6 grid grid-cols-2 gap-3">

                            <button type="button" @click="rejectModal = false" :disabled="rejecting"
                                class="inline-flex items-center justify-center

                                       rounded-xl border border-slate-300

                                       px-4 py-3 font-semibold text-slate-700

                                       hover:bg-slate-50

                                       disabled:opacity-60

                                       disabled:cursor-not-allowed

                                       transition">

                                Cancel

                            </button>

                            <button type="submit" :disabled="rejecting"
                                class="inline-flex items-center justify-center

                                       rounded-xl bg-red-600

                                       px-4 py-3 font-semibold text-white

                                       hover:bg-red-700

                                       disabled:opacity-60

                                       disabled:cursor-not-allowed

                                       transition">

                                <span x-show="!rejecting" class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-xmark"></i>

                                    Confirm Rejection

                                </span>

                                <span x-show="rejecting" x-cloak class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-spinner animate-spin"></i>

                                    Rejecting...

                                </span>

                            </button>

                        </div>

                    </form>

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
                document.addEventListener('DOMContentLoaded', function() {
                    const mapElement = document.getElementById('admin-consumer-map');

                    if (!mapElement || typeof L === 'undefined') {
                        return;
                    }

                    const latitude = @json((float) $consumer->address->latitude);
                    const longitude = @json((float) $consumer->address->longitude);

                    const map = L.map('admin-consumer-map').setView(
                        [latitude, longitude],
                        17
                    );

                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);

                    L.marker([latitude, longitude])
                        .addTo(map)
                        .bindPopup('Registered water service location');

                    requestAnimationFrame(function() {
                        map.invalidateSize();
                    });
                });
            </script>
        @endif
    @endpush

@endsection
