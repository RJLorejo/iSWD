
@extends('admin.layouts.app')

@section('title', 'Consumer Details')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Header --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="min-w-0">
            <p class="text-xs font-medium text-slate-500">Administration / Consumer Management</p>

            <div class="mt-1 flex flex-wrap items-center gap-2">
                <h1 class="text-xl sm:text-2xl font-bold text-slate-900">
                    {{ $consumer->full_name }}
                </h1>

                @if ($consumer->is_active)
                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                        Active
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                        Inactive
                    </span>
                @endif
            </div>

            <p class="mt-1 text-sm text-slate-500">
                Account #{{ $consumer->account_number ?: '—' }}
            </p>
        </div>

        <div class="flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('admin.consumers.index', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <i class="fa-solid fa-arrow-left"></i>
                Back to Consumers
            </a>


        </div>
    </div>

    {{-- Consumer information --}}
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
            <h2 class="text-sm font-semibold text-slate-900">Consumer Information</h2>
            <p class="mt-0.5 text-xs text-slate-500">Personal and account information.</p>
        </div>

        <div class="p-4 sm:p-5">
            <div class="grid grid-cols-1 gap-x-6 gap-y-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Account Number</p>
                    <p class="mt-1 break-words text-sm font-semibold text-slate-900">{{ $consumer->account_number ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Full Name</p>
                    <p class="mt-1 break-words text-sm font-semibold text-slate-900">{{ $consumer->full_name }}</p>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Sex</p>
                    <p class="mt-1 text-sm text-slate-900">{{ $consumer->sex ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Contact Number</p>
                    <p class="mt-1 text-sm text-slate-900">{{ $consumer->phone ?: '—' }}</p>
                </div>

                <div class="min-w-0">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Email Address</p>
                    <p class="mt-1 break-all text-sm text-slate-900">{{ $consumer->email ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Registration Source</p>
                    <p class="mt-1 break-words text-sm text-slate-900">{{ $consumer->registration_source ?: '—' }}</p>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Verification Status</p>
                    <div class="mt-1">
                        @if ($consumer->verification_status === 'Verified')
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-green-600">
                                <i class="fa-solid fa-circle-check"></i> Verified
                            </span>
                        @elseif ($consumer->verification_status === 'Rejected')
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-red-600">
                                <i class="fa-solid fa-circle-xmark"></i> Rejected
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-sm font-medium text-amber-600">
                                <i class="fa-solid fa-clock"></i>
                                {{ $consumer->verification_status ?: 'Pending Verification' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div>
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Registration Date</p>
                    <p class="mt-1 text-sm text-slate-900">
                        {{ $consumer->created_at?->format('M d, Y h:i A') ?? '—' }}
                    </p>
                </div>

                @if ($consumer->verification_reason)
                    <div class="sm:col-span-2 lg:col-span-3 xl:col-span-4">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Verification Reason</p>
                        <p class="mt-1 whitespace-pre-line break-words text-sm text-slate-900">{{ $consumer->verification_reason }}</p>
                    </div>
                @endif

            </div>
        </div>
    </div>

    {{-- Address and portal --}}
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

        {{-- Service address --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Service Address &amp; Location</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Registered water service connection.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                @if ($consumer->address)
                    @php
                        $address = $consumer->address;
                        $addressText = collect([
                            $address->house_no,
                            $address->street,
                            $address->purok,
                            $address->barangay,
                            $address->municipality,
                            $address->province,
                        ])->filter(fn ($part) => filled($part))->implode(', ');

                        $latitude = $address->latitude;
                        $longitude = $address->longitude;

                        $hasCoordinates = is_numeric($latitude)
                            && is_numeric($longitude)
                            && (float) $latitude >= -90
                            && (float) $latitude <= 90
                            && (float) $longitude >= -180
                            && (float) $longitude <= 180;
                    @endphp

                    <div class="rounded-lg border border-blue-100 bg-blue-50 p-3">
                        <p class="text-sm font-medium leading-6 text-slate-900">
                            {{ $address->full_address ?: ($addressText ?: 'No complete address recorded.') }}
                        </p>
                    </div>

                    @if ($hasCoordinates)
                        <div class="mt-4 overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                            <div id="admin-consumer-map"
                                 class="h-[300px] w-full sm:h-[360px] lg:h-[390px]"></div>
                        </div>

                        <div class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                            <p class="text-xs text-slate-500">Registered service-location pin.</p>

                            <a href="https://www.google.com/maps?q={{ $latitude }},{{ $longitude }}"
                               target="_blank"
                               rel="noopener noreferrer"
                               class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 hover:bg-slate-50">
                                <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                Open Map
                            </a>
                        </div>
                    @else
                        <div class="mt-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3">
                            <p class="text-xs text-amber-800">No valid service-location coordinates have been saved.</p>
                        </div>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-x-4 gap-y-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs text-slate-500">House No.</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->house_no ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Street</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->street ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Purok</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->purok ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Barangay</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->barangay ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Municipality</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->municipality ?: '—' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500">Province</p>
                            <p class="mt-0.5 break-words text-sm text-slate-900">{{ $address->province ?: '—' }}</p>
                        </div>

                        @if ($hasCoordinates)
                            <div>
                                <p class="text-xs text-slate-500">Latitude</p>
                                <p class="mt-0.5 break-words text-sm text-slate-900">{{ $latitude }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500">Longitude</p>
                                <p class="mt-0.5 break-words text-sm text-slate-900">{{ $longitude }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="py-8 text-center">
                        <i class="fa-solid fa-location-dot text-2xl text-slate-300"></i>
                        <p class="mt-2 text-sm text-slate-500">No service address recorded.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Portal account --}}
        <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-100 px-4 py-4 sm:px-5">
                <div class="flex items-center gap-3">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-cyan-50 text-cyan-600">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Consumer Portal</h2>
                        <p class="mt-0.5 text-xs text-slate-500">Linked iSWD account information.</p>
                    </div>
                </div>
            </div>

            <div class="p-4 sm:p-5">
                @if ($consumer->user)
                    <div class="rounded-xl border p-4 {{ $consumer->user->is_active ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg {{ $consumer->user->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i class="fa-solid {{ $consumer->user->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>
                            </div>

                            <div class="min-w-0">
                                <p class="text-sm font-semibold {{ $consumer->user->is_active ? 'text-green-900' : 'text-red-900' }}">
                                    {{ $consumer->user->is_active ? 'Portal Account Active' : 'Portal Account Inactive' }}
                                </p>

                                <p class="mt-1 text-xs {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $consumer->user->is_active ? 'The linked account is marked active.' : 'The linked account is marked inactive.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Login Email</p>
                        <p class="mt-1 break-all text-sm font-medium text-slate-900">
                            {{ $consumer->user->email ?: '—' }}
                        </p>
                    </div>
                @else
                    <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600"></i>
                            <div>
                                <p class="text-sm font-semibold text-amber-900">Portal Account Missing</p>
                                <p class="mt-1 text-xs text-amber-700">No linked User account was found for this consumer.</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 border-t border-slate-100 pt-4">
                    <p class="text-[11px] font-medium uppercase tracking-wide text-slate-500">Consumer Account Status</p>
                    <p class="mt-1 text-sm font-semibold {{ $consumer->is_active ? 'text-green-700' : 'text-red-700' }}">
                        {{ $consumer->is_active ? 'Active' : 'Inactive' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
    @if (isset($consumer->address) && is_numeric($consumer->address->latitude) && is_numeric($consumer->address->longitude))
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endif
@endpush

@push('scripts')
    @if (isset($consumer->address) && is_numeric($consumer->address->latitude) && is_numeric($consumer->address->longitude))
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapElement = document.getElementById('admin-consumer-map');

                if (!mapElement || typeof L === 'undefined') {
                    return;
                }

                const latitude = @json((float) $consumer->address->latitude);
                const longitude = @json((float) $consumer->address->longitude);

                const map = L.map(mapElement).setView([latitude, longitude], 17);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                L.marker([latitude, longitude])
                    .addTo(map)
                    .bindPopup('Registered water service location');

                requestAnimationFrame(function () {
                    map.invalidateSize();
                });
            });
        </script>
    @endif
@endpush
