@extends('customer-service.layouts.app')

@section('title', 'Edit Complaint')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    <style>
        #complaint-edit-map {
            min-height: 420px;
            width: 100%;
            z-index: 0;
        }

        .leaflet-container {
            font-family: inherit;
        }
    </style>
@endpush


@section('content')

    <div class="max-w-6xl mx-auto space-y-6">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm text-gray-500">
                    Complaint Management
                </p>

                <h1 class="text-2xl font-bold text-gray-900">
                    Edit Complaint
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Update complaint {{ $complaint->complaint_no }}.
                </p>

            </div>


            <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                class="inline-flex items-center justify-center gap-2
                       px-4 py-2.5 rounded-xl
                       border border-gray-300
                       text-gray-700
                       hover:bg-gray-50 transition">

                <i class="fas fa-arrow-left"></i>

                Back to Complaint

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- VALIDATION ERRORS --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

                <div class="flex gap-3">

                    <div class="text-red-600 mt-0.5">

                        <i class="fas fa-circle-exclamation"></i>

                    </div>

                    <div>

                        <h2 class="font-semibold text-red-800">
                            Please correct the following:
                        </h2>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">

                            @foreach ($errors->all() as $error)
                                <li>
                                    {{ $error }}
                                </li>
                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif


        {{-- ========================================================= --}}
        {{-- FORM --}}
        {{-- ========================================================= --}}

        <form action="{{ route('customer-service.complaints.update', $complaint) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">

            @csrf
            @method('PUT')


            {{-- ========================================================= --}}
            {{-- COMPLAINANT INFORMATION --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-blue-100 text-blue-600
                                   flex items-center justify-center">

                            <i class="fas fa-user"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Complainant Information
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Update the registered consumer or walk-in complainant information.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">

                    @php

                        $complainantType = old('complainant_type', $complaint->consumer_id ? 'registered' : 'walk_in');

                    @endphp


                    {{-- COMPLAINANT TYPE --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Complainant Type
                        </label>


                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                            {{-- REGISTERED --}}

                            <label
                                class="flex items-start gap-3 p-4
                                       rounded-xl border border-gray-200
                                       cursor-pointer hover:bg-gray-50">

                                <input type="radio" name="complainant_type" value="registered"
                                    class="mt-1 text-blue-600 focus:ring-blue-500" @checked($complainantType === 'registered')>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        Registered Consumer
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Complaint belongs to an existing consumer account.
                                    </p>

                                </div>

                            </label>


                            {{-- WALK-IN --}}

                            <label
                                class="flex items-start gap-3 p-4
                                       rounded-xl border border-gray-200
                                       cursor-pointer hover:bg-gray-50">

                                <input type="radio" name="complainant_type" value="walk_in"
                                    class="mt-1 text-blue-600 focus:ring-blue-500" @checked($complainantType === 'walk_in')>

                                <div>

                                    <p class="font-semibold text-gray-900">
                                        Walk-in Complainant
                                    </p>

                                    <p class="text-sm text-gray-500 mt-1">
                                        Complaint was submitted without a registered consumer account.
                                    </p>

                                </div>

                            </label>

                        </div>


                        @error('complainant_type')
                            <p class="text-sm text-red-600 mt-2">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- REGISTERED CONSUMER --}}

                    <div id="registered-consumer-section">

                        <label for="consumer_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Consumer
                        </label>

                        <select name="consumer_id" id="consumer_id"
                            class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                Select Consumer
                            </option>

                            @foreach ($consumers as $consumer)
                                <option value="{{ $consumer->id }}" @selected(old('consumer_id', $complaint->consumer_id) == $consumer->id)>

                                    {{ $consumer->last_name }},
                                    {{ $consumer->first_name }}

                                    @if ($consumer->consumer_no)
                                        — {{ $consumer->consumer_no }}
                                    @endif

                                </option>
                            @endforeach

                        </select>


                        @error('consumer_id')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- WALK-IN --}}

                    <div id="walk-in-section" class="hidden">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- NAME --}}

                            <div>

                                <label for="complainant_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Complainant Name
                                </label>

                                <input type="text" name="complainant_name" id="complainant_name"
                                    value="{{ old('complainant_name', $complaint->complainant_name) }}"
                                    placeholder="Enter complainant name"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500">

                                @error('complainant_name')
                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- PHONE --}}

                            <div>

                                <label for="complainant_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Contact Number
                                </label>

                                <input type="text" name="complainant_phone" id="complainant_phone"
                                    value="{{ old('complainant_phone', $complaint->complainant_phone) }}"
                                    placeholder="Enter contact number"
                                    class="w-full rounded-xl border-gray-300
                                           focus:border-blue-500 focus:ring-blue-500">

                                @error('complainant_phone')
                                    <p class="text-sm text-red-600 mt-1">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- COMPLAINT INFORMATION --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-orange-100 text-orange-600
                                   flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Complaint Information
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Update the classification and complaint details.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">

                    {{-- DIVISION / COMPLAINT TYPE / STATUS --}}

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                        {{-- DIVISION --}}

                        <div>

                            <label for="division_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Division

                                <span class="text-red-500">*</span>
                            </label>

                            <select name="division_id" id="division_id" required
                                class="w-full rounded-xl border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500">

                                <option value="">
                                    Select Division
                                </option>

                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" @selected(old('division_id', $complaint->division_id) == $division->id)>

                                        {{ $division->name }}

                                    </option>
                                @endforeach

                            </select>


                            @error('division_id')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- COMPLAINT TYPE --}}

                        <div>

                            <label for="complaint_category_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Complaint Type

                                <span class="text-red-500">*</span>
                            </label>

                            <select name="complaint_category_id" id="complaint_category_id" required
                                class="w-full rounded-xl border-gray-300
                                       focus:border-blue-500 focus:ring-blue-500">

                                <option value="">
                                    Select Complaint Type
                                </option>

                            </select>


                            @error('complaint_category_id')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- STATUS --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>

                            <div
                                class="w-full rounded-xl
                                       border border-gray-200
                                       bg-gray-50 px-4 py-2.5
                                       text-gray-700">

                                {{ $complaint->status }}

                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                Status is managed through complaint workflow actions.
                            </p>

                        </div>

                    </div>


                    {{-- DESCRIPTION --}}

                    <div>

                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Problem Description

                            <span class="text-red-500">*</span>
                        </label>

                        <textarea name="description" id="description" rows="5" required
                            class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">{{ old('description', $complaint->description) }}</textarea>


                        @error('description')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- LOCATION --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-emerald-100 text-emerald-600
                                   flex items-center justify-center">

                            <i class="fas fa-location-dot"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Problem Location
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Update the reported address or exact map location.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- MAP --}}

                    <div>

                        <div class="flex items-center justify-between gap-4 mb-3">

                            <div>

                                <label class="block text-sm font-medium text-gray-700">
                                    Reported Problem Location
                                </label>

                                <p class="text-xs text-gray-500 mt-1">
                                    Click the map or drag the marker to change the exact location.
                                </p>

                            </div>

                            <i class="fas fa-map-location-dot text-blue-600 text-lg"></i>

                        </div>


                        <div id="complaint-edit-map"
                            class="w-full h-[420px]
                                   rounded-2xl border border-gray-300
                                   overflow-hidden relative z-0">
                        </div>


                        {{-- HIDDEN COORDINATES --}}

                        <input type="hidden" name="latitude" id="latitude"
                            value="{{ old('latitude', $complaint->latitude) }}">

                        <input type="hidden" name="longitude" id="longitude"
                            value="{{ old('longitude', $complaint->longitude) }}">


                        {{-- HUMAN-READABLE LOCATION --}}

                        <div id="selected-location"
                            class="mt-4 rounded-xl
                                   bg-blue-50 border border-blue-200 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg
                                           bg-blue-100 text-blue-600
                                           flex items-center justify-center shrink-0">

                                    <i class="fas fa-location-dot"></i>

                                </div>

                                <div class="min-w-0">

                                    <p class="text-sm font-semibold text-blue-900">
                                        Reported Location
                                    </p>

                                    <p id="detected-address" class="text-xs text-blue-700 mt-1 break-words">

                                        {{ old('address', $complaint->address) }}

                                    </p>

                                </div>

                            </div>

                        </div>
                        {{-- ADDRESS --}}

                        <div>

                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Problem Address

                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" name="address" id="address"
                                value="{{ old('address', $complaint->address) }}" required
                                placeholder="House No., Street, Barangay, Sagay City"
                                class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                            <p class="text-xs text-gray-500 mt-2">
                                Move the map marker only when you need to change the complaint location.
                            </p>


                            @error('address')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- LANDMARK --}}

                        <div>

                            <label for="landmark" class="block text-sm font-medium text-gray-700 mb-2">
                                Landmark
                            </label>

                            <input type="text" name="landmark" id="landmark"
                                value="{{ old('landmark', $complaint->landmark) }}"
                                placeholder="Example: Near barangay hall, school, store, etc."
                                class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">


                            @error('landmark')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- PHOTO --}}
            {{-- ========================================================= --}}

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-purple-100 text-purple-600
                                   flex items-center justify-center">

                            <i class="fas fa-camera"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-gray-900">
                                Photo Evidence
                            </h2>

                            <p class="text-sm text-gray-500 mt-1">
                                Keep the current photo or upload a replacement.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-5">

                    {{-- CURRENT PHOTO --}}

                    @if ($complaint->photo)
                        <div>

                            <p class="text-sm font-medium text-gray-700 mb-2">
                                Current Photo
                            </p>

                            <div
                                class="max-w-md rounded-2xl
                                       border border-gray-200
                                       overflow-hidden bg-gray-50">

                                <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                    class="w-full max-h-72 object-cover">

                            </div>

                        </div>
                    @endif


                    {{-- NEW PHOTO --}}

                    <div>

                        <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">
                            {{ $complaint->photo ? 'Replace Photo' : 'Upload Photo' }}
                        </label>

                        <input type="file" name="photo" id="photo" accept="image/*"
                            class="block w-full text-sm text-gray-600
                                   file:mr-4 file:py-2.5 file:px-4
                                   file:rounded-xl file:border-0
                                   file:text-sm file:font-semibold
                                   file:bg-blue-50 file:text-blue-700
                                   hover:file:bg-blue-100">

                        <p class="text-xs text-gray-500 mt-2">
                            Leave this empty to keep the current photo.
                        </p>


                        @error('photo')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- ========================================================= --}}
            {{-- ACTIONS --}}
            {{-- ========================================================= --}}

            <div class="flex flex-col-reverse sm:flex-row
                       sm:items-center sm:justify-end gap-3">

                <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center gap-2
                           px-5 py-3 rounded-xl
                           border border-gray-300
                           text-gray-700
                           hover:bg-gray-50 transition">

                    <i class="fas fa-xmark"></i>

                    Cancel

                </a>


                <button type="submit"
                    class="inline-flex items-center justify-center gap-2
                           px-6 py-3 rounded-xl
                           bg-blue-600 text-white font-semibold
                           hover:bg-blue-700 transition">

                    <i class="fas fa-floppy-disk"></i>

                    Save Changes

                </button>

            </div>

        </form>

    </div>

@endsection


@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


    {{-- ========================================================= --}}
    {{-- DIVISION → COMPLAINT TYPE --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const divisions =
                @json($divisions);

            const divisionSelect =
                document.getElementById(
                    'division_id'
                );

            const complaintTypeSelect =
                document.getElementById(
                    'complaint_category_id'
                );

            const selectedComplaintType =
                @json(old('complaint_category_id', $complaint->complaint_category_id));


            function populateComplaintTypes(
                divisionId,
                selectedId = null
            ) {

                complaintTypeSelect.innerHTML =
                    '';


                if (!divisionId) {

                    complaintTypeSelect.disabled =
                        true;

                    complaintTypeSelect.innerHTML =
                        '<option value="">Select a division first</option>';

                    return;

                }


                const division =
                    divisions.find(
                        function(item) {

                            return String(item.id) ===
                                String(divisionId);

                        }
                    );


                if (
                    !division ||
                    !division.complaint_types ||
                    division.complaint_types.length === 0
                ) {

                    complaintTypeSelect.disabled =
                        true;

                    complaintTypeSelect.innerHTML =
                        '<option value="">No complaint types available</option>';

                    return;

                }


                complaintTypeSelect.disabled =
                    false;


                const placeholder =
                    document.createElement(
                        'option'
                    );

                placeholder.value =
                    '';

                placeholder.textContent =
                    'Select Complaint Type';

                complaintTypeSelect
                    .appendChild(
                        placeholder
                    );


                division.complaint_types.forEach(
                    function(type) {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value =
                            type.id;


                        option.textContent =
                            type.code ?
                            `${type.code} — ${type.name}` :
                            type.name;


                        if (
                            selectedId !== null &&
                            String(selectedId) ===
                            String(type.id)
                        ) {

                            option.selected =
                                true;

                        }


                        complaintTypeSelect
                            .appendChild(
                                option
                            );

                    }
                );

            }


            divisionSelect.addEventListener(
                'change',
                function() {

                    populateComplaintTypes(
                        this.value
                    );

                }
            );


            if (divisionSelect.value) {

                populateComplaintTypes(
                    divisionSelect.value,
                    selectedComplaintType
                );

            }

        });
    </script>


    {{-- ========================================================= --}}
    {{-- COMPLAINANT TYPE --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const registeredSection =
                document.getElementById(
                    'registered-consumer-section'
                );

            const walkInSection =
                document.getElementById(
                    'walk-in-section'
                );

            const consumerSelect =
                document.getElementById(
                    'consumer_id'
                );

            const complainantName =
                document.getElementById(
                    'complainant_name'
                );

            const complainantPhone =
                document.getElementById(
                    'complainant_phone'
                );


            function updateComplainantSections() {

                const selected =
                    document.querySelector(
                        'input[name="complainant_type"]:checked'
                    );


                if (!selected) {
                    return;
                }


                if (
                    selected.value ===
                    'registered'
                ) {

                    registeredSection
                        .classList
                        .remove('hidden');

                    walkInSection
                        .classList
                        .add('hidden');


                    consumerSelect.disabled =
                        false;

                    complainantName.disabled =
                        true;

                    complainantPhone.disabled =
                        true;

                }


                if (
                    selected.value ===
                    'walk_in'
                ) {

                    registeredSection
                        .classList
                        .add('hidden');

                    walkInSection
                        .classList
                        .remove('hidden');


                    consumerSelect.disabled =
                        true;

                    consumerSelect.value =
                        '';

                    complainantName.disabled =
                        false;

                    complainantPhone.disabled =
                        false;

                }

            }


            document
                .querySelectorAll(
                    'input[name="complainant_type"]'
                )
                .forEach(
                    function(radio) {

                        radio.addEventListener(
                            'change',
                            updateComplainantSections
                        );

                    }
                );


            updateComplainantSections();

        });
    </script>


    {{-- ========================================================= --}}
    {{-- MAP + REVERSE GEOCODING --}}
    {{-- ========================================================= --}}

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const mapElement =
                document.getElementById(
                    'complaint-edit-map'
                );


            if (!mapElement) {
                return;
            }


            const latitudeInput =
                document.getElementById(
                    'latitude'
                );

            const longitudeInput =
                document.getElementById(
                    'longitude'
                );

            const addressInput =
                document.getElementById(
                    'address'
                );

            const detectedAddress =
                document.getElementById(
                    'detected-address'
                );


            /*
            |--------------------------------------------------------------------------
            | Default Sagay City
            |--------------------------------------------------------------------------
            */

            const defaultLatitude =
                10.9447;

            const defaultLongitude =
                123.4200;


            /*
            |--------------------------------------------------------------------------
            | Existing Location
            |--------------------------------------------------------------------------
            */

            const savedLatitude =
                parseFloat(
                    latitudeInput.value
                );

            const savedLongitude =
                parseFloat(
                    longitudeInput.value
                );


            const hasExistingLocation = !isNaN(savedLatitude) &&
                !isNaN(savedLongitude);


            const initialLatitude =
                hasExistingLocation ?
                savedLatitude :
                defaultLatitude;

            const initialLongitude =
                hasExistingLocation ?
                savedLongitude :
                defaultLongitude;


            /*
            |--------------------------------------------------------------------------
            | Create Map
            |--------------------------------------------------------------------------
            */

            const map =
                L.map(
                    'complaint-edit-map', {
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
                    initialLatitude,
                    initialLongitude
                ],
                hasExistingLocation ? 17 : 14
            );


            /*
            |--------------------------------------------------------------------------
            | Marker
            |--------------------------------------------------------------------------
            */

            const marker =
                L.marker(
                    [
                        initialLatitude,
                        initialLongitude
                    ], {
                        draggable: true
                    }
                )
                .addTo(map);


            marker.bindPopup(`
                <div class="text-sm">

                    <div class="font-semibold text-gray-900">

                        <i class="fas fa-location-dot text-blue-600 mr-1"></i>

                        Reported Problem Location

                    </div>

                    <div class="text-gray-500 mt-1">

                        Drag the marker or click the map to change the location.

                    </div>

                </div>
            `);


            /*
            |--------------------------------------------------------------------------
            | Reverse Geocoding
            |--------------------------------------------------------------------------
            */

            async function reverseGeocode(
                lat,
                lng
            ) {

                detectedAddress.textContent =
                    'Detecting address...';


                try {

                    const response =
                        await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Reverse geocoding failed.'
                        );

                    }


                    const data =
                        await response.json();


                    if (data.display_name) {

                        addressInput.value =
                            data.display_name;

                        detectedAddress.textContent =
                            data.display_name;

                    } else {

                        detectedAddress.textContent =
                            'Address could not be detected. Please enter it manually.';

                    }

                } catch (error) {

                    console.error(
                        'Reverse geocoding error:',
                        error
                    );


                    detectedAddress.textContent =
                        'Unable to detect the address. Please enter it manually.';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Update Location
            |--------------------------------------------------------------------------
            */

            function updateLocation(
                lat,
                lng,
                detectAddress = true
            ) {

                const formattedLatitude =
                    Number(lat).toFixed(7);

                const formattedLongitude =
                    Number(lng).toFixed(7);


                latitudeInput.value =
                    formattedLatitude;

                longitudeInput.value =
                    formattedLongitude;


                marker.setLatLng([
                    lat,
                    lng
                ]);


                map.panTo([
                    lat,
                    lng
                ]);


                if (detectAddress) {

                    reverseGeocode(
                        formattedLatitude,
                        formattedLongitude
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Map Click
            |--------------------------------------------------------------------------
            */

            map.on(
                'click',
                function(event) {

                    updateLocation(
                        event.latlng.lat,
                        event.latlng.lng,
                        true
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Marker Drag
            |--------------------------------------------------------------------------
            */

            marker.on(
                'dragend',
                function(event) {

                    const position =
                        event
                        .target
                        .getLatLng();


                    updateLocation(
                        position.lat,
                        position.lng,
                        true
                    );

                }
            );


            /*
            |--------------------------------------------------------------------------
            | Preserve Existing Location
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | Do not reverse geocode automatically when opening Edit.
            | This prevents the saved address from being replaced just
            | because the user opened this page.
            |
            */

            if (hasExistingLocation) {

                latitudeInput.value =
                    Number(
                        savedLatitude
                    ).toFixed(7);

                longitudeInput.value =
                    Number(
                        savedLongitude
                    ).toFixed(7);

            }


            /*
            |--------------------------------------------------------------------------
            | Map Size Fix
            |--------------------------------------------------------------------------
            */

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

                    setTimeout(
                        refreshMap,
                        1000
                    );

                }
            );


            window.addEventListener(
                'resize',
                refreshMap
            );

        });
    </script>
@endpush
