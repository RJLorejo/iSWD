@extends('customer-service.layouts.app')

@section('title', 'Edit Complaint')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')

    <div class="space-y-6">

        {{-- PAGE HEADER --}}
        <x-form.page-header title="Edit Complaint"
            subtitle="Update the complaint information and reported problem location." />


        <form action="{{ route('customer-service.complaints.update', $complaint) }}" method="POST"
            enctype="multipart/form-data" class="space-y-6">

            @csrf
            @method('PUT')


            {{-- ========================================================= --}}
            {{-- COMPLAINT NUMBER --}}
            {{-- ========================================================= --}}

            <x-form.card>

                <div class="p-6">

                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Complaint Number
                    </label>

                    <div class="relative">

                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-hashtag"></i>
                        </span>

                        <input type="text" value="{{ $complaint->complaint_no }}" disabled
                            class="w-full pl-10 rounded-xl border-gray-300
                               bg-gray-100 text-gray-600">

                    </div>

                    <p class="text-xs text-gray-500 mt-2">
                        Complaint number cannot be changed.
                    </p>

                </div>

            </x-form.card>
            {{-- ========================================================= --}}
            {{-- COMPLAINANT INFORMATION --}}
            {{-- ========================================================= --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl bg-blue-100
                    text-blue-600 flex items-center justify-center">

                            <i class="fas fa-user"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Complainant Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Update who reported this complaint.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">

                    {{-- COMPLAINANT TYPE --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">

                            Complainant Type

                            <span class="text-red-500">*</span>

                        </label>


                        @php
                            $complainantType = old(
                                'complainant_type',
                                $complaint->consumer_id ? 'registered' : 'walk_in',
                            );
                        @endphp


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            {{-- REGISTERED --}}

                            <label
                                class="complainant-type-option
                           flex items-start gap-3 p-4
                           border border-gray-200
                           rounded-xl cursor-pointer
                           hover:border-blue-400
                           hover:bg-blue-50
                           transition">

                                <input type="radio" name="complainant_type" value="registered" class="mt-1"
                                    {{ $complainantType === 'registered' ? 'checked' : '' }}>

                                <div>

                                    <div class="font-semibold text-gray-900">
                                        Registered Consumer
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Complaint is linked to an existing consumer.
                                    </p>

                                </div>

                            </label>


                            {{-- WALK-IN --}}

                            <label
                                class="complainant-type-option
                           flex items-start gap-3 p-4
                           border border-gray-200
                           rounded-xl cursor-pointer
                           hover:border-blue-400
                           hover:bg-blue-50
                           transition">

                                <input type="radio" name="complainant_type" value="walk_in" class="mt-1"
                                    {{ $complainantType === 'walk_in' ? 'checked' : '' }}>

                                <div>

                                    <div class="font-semibold text-gray-900">
                                        Walk-in / Unregistered
                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">
                                        Complaint is recorded without a consumer account.
                                    </p>

                                </div>

                            </label>

                        </div>


                        @error('complainant_type')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- REGISTERED CONSUMER --}}

                    <div id="registered-consumer-section" class="{{ $complainantType === 'registered' ? '' : 'hidden' }}">

                        <label class="block text-sm font-medium text-gray-700 mb-2">

                            Consumer

                            <span class="text-red-500">*</span>

                        </label>


                        <select name="consumer_id" id="consumer_id"
                            class="w-full rounded-xl border-gray-300
                       focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                Select Registered Consumer
                            </option>

                            @foreach ($consumers as $consumer)
                                <option value="{{ $consumer->id }}" @selected(old('consumer_id', $complaint->consumer_id) == $consumer->id)>

                                    {{ $consumer->consumer_no }}
                                    —
                                    {{ $consumer->full_name }}

                                    @if ($consumer->phone)
                                        — {{ $consumer->phone }}
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

                    <div id="walk-in-section" class="{{ $complainantType === 'walk_in' ? '' : 'hidden' }}">

                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 mb-5">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg bg-amber-100
                               text-amber-700 flex items-center justify-center">

                                    <i class="fas fa-person-walking"></i>

                                </div>

                                <div>

                                    <p class="font-semibold text-amber-900">
                                        Walk-in Complainant
                                    </p>

                                    <p class="text-xs text-amber-700 mt-1">
                                        Record the complainant directly without linking
                                        the complaint to a consumer account.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- NAME --}}

                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Complainant Full Name

                                    <span class="text-red-500">*</span>

                                </label>

                                <input type="text" name="complainant_name" id="complainant_name"
                                    value="{{ old('complainant_name', $complaint->complainant_name) }}"
                                    placeholder="Enter full name"
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

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Contact Number

                                    <span class="text-gray-400 text-xs">
                                        Optional
                                    </span>

                                </label>

                                <input type="text" name="complainant_phone" id="complainant_phone"
                                    value="{{ old('complainant_phone', $complaint->complainant_phone) }}"
                                    placeholder="09XXXXXXXXX"
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


                    <p class="text-xs text-gray-500">

                        <i class="fas fa-circle-info mr-1"></i>

                        Registered complaints are linked to a consumer record.
                        Walk-in complaints store the complainant's information directly.

                    </p>

                </div>

            </x-form.card>



            {{-- ========================================================= --}}
            {{-- COMPLAINT INFORMATION --}}
            {{-- ========================================================= --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl bg-orange-100
                                text-orange-600 flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Complaint Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Update the category, priority, status, and complaint details.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- CATEGORY / PRIORITY / STATUS --}}

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">


                        {{-- CATEGORY --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Complaint Category
                            </label>

                            <select name="complaint_category_id" required
                                class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('complaint_category_id', $complaint->complaint_category_id) == $category->id)>

                                        {{ $category->code }}
                                        —
                                        {{ $category->name }}

                                    </option>
                                @endforeach

                            </select>

                            @error('complaint_category_id')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        {{-- PRIORITY --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Priority
                            </label>

                            <select name="priority" required
                                class="w-full rounded-xl border-gray-300
                                   focus:border-blue-500 focus:ring-blue-500">

                                @foreach (['Low', 'Medium', 'High', 'Critical'] as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', $complaint->priority) === $priority)>

                                        {{ $priority }}

                                    </option>
                                @endforeach

                            </select>

                        </div>


                        {{-- STATUS --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>

                            <div
                                class="w-full rounded-xl border border-gray-200
                bg-gray-50 px-4 py-2.5
                text-gray-700">

                                {{ $complaint->status }}

                            </div>

                            <p class="text-xs text-gray-500 mt-2">
                                Complaint status is managed through the appropriate workflow actions.
                            </p>

                        </div>

                    </div>



                    {{-- SUBJECT --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Complaint Subject
                        </label>

                        <input type="text" name="subject" value="{{ old('subject', $complaint->subject) }}" required
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                        @error('subject')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- DESCRIPTION --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Problem Description
                        </label>

                        <textarea name="description" rows="5" required
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">{{ old('description', $complaint->description) }}</textarea>

                        @error('description')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

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
                            class="w-10 h-10 rounded-xl bg-green-100
                                text-green-600 flex items-center justify-center">

                            <i class="fas fa-location-dot"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Reported Problem Location
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Click on the map or drag the marker to update the exact location.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- ADDRESS --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Complete Problem Address
                        </label>

                        <input type="text" name="address" value="{{ old('address', $complaint->address) }}" required
                            placeholder="House No., Street, Barangay, Sagay City"
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                        @error('address')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- LANDMARK --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Landmark
                        </label>

                        <input type="text" name="landmark" value="{{ old('landmark', $complaint->landmark) }}"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                    </div>



                    {{-- MAP --}}

                    <div>

                        <div class="flex items-center justify-between mb-3">

                            <div>

                                <label class="block text-sm font-medium text-gray-700">
                                    Map Location
                                </label>

                                <p class="text-xs text-gray-500 mt-1">
                                    Click anywhere on the map to move the marker.
                                    You can also drag the marker.
                                </p>

                            </div>

                            <i class="fas fa-map-location-dot text-blue-600 text-lg"></i>

                        </div>


                        <div id="complaint-edit-map"
                            class="w-full h-[420px] rounded-2xl
                               border border-gray-300 overflow-hidden
                               relative z-0">
                        </div>

                    </div>



                    {{-- COORDINATES --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        {{-- LATITUDE --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Latitude
                            </label>

                            <div class="relative">

                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-location-arrow"></i>
                                </span>

                                <input type="text" name="latitude" id="latitude"
                                    value="{{ old('latitude', $complaint->latitude) }}" readonly
                                    class="w-full pl-10 rounded-xl border-gray-300
                                       bg-gray-50 text-gray-600">

                            </div>

                        </div>



                        {{-- LONGITUDE --}}

                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Longitude
                            </label>

                            <div class="relative">

                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-location-arrow"></i>
                                </span>

                                <input type="text" name="longitude" id="longitude"
                                    value="{{ old('longitude', $complaint->longitude) }}" readonly
                                    class="w-full pl-10 rounded-xl border-gray-300
                                       bg-gray-50 text-gray-600">

                            </div>

                        </div>

                    </div>


                    {{-- LOCATION SELECTED --}}

                    <div id="edit-selected-location" class="rounded-xl bg-blue-50 border border-blue-200 p-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-9 h-9 rounded-lg bg-blue-100
                                   text-blue-600 flex items-center justify-center">

                                <i class="fas fa-location-dot"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-blue-900">
                                    Reported Location
                                </p>

                                <p class="text-xs text-blue-700 mt-1">
                                    The marker represents the currently saved complaint location.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </x-form.card>



            {{-- ========================================================= --}}
            {{-- PHOTO --}}
            {{-- ========================================================= --}}

            <x-form.card>

                <div class="px-6 py-5 border-b border-gray-100">

                    <div class="flex items-center gap-3">

                        <div
                            class="w-10 h-10 rounded-xl bg-purple-100
                                text-purple-600 flex items-center justify-center">

                            <i class="fas fa-camera"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Photo Evidence
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Replace the existing evidence if necessary.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-4">

                    @if ($complaint->photo)
                        <div>

                            <p class="text-sm font-medium text-gray-700 mb-2">
                                Current Photo
                            </p>

                            <img src="{{ asset('storage/' . $complaint->photo) }}" alt="Complaint photo"
                                class="w-72 max-h-72 object-cover
                                   rounded-2xl border border-gray-200">

                        </div>

                        <p class="text-xs text-gray-500">
                            Upload a new image below to replace the current photo.
                        </p>
                    @endif


                    <input type="file" name="photo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-600
                           border border-gray-300 rounded-xl
                           file:mr-4 file:py-2.5 file:px-4
                           file:rounded-lg file:border-0
                           file:bg-blue-50 file:text-blue-700
                           hover:file:bg-blue-100">

                    @error('photo')
                        <p class="text-sm text-red-600 mt-1">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </x-form.card>



            {{-- ========================================================= --}}
            {{-- ACTIONS --}}
            {{-- ========================================================= --}}

            <div class="flex justify-end gap-3">

                <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                    class="px-5 py-2.5 rounded-xl
                       border border-gray-300
                       text-gray-700 hover:bg-gray-50 transition">

                    <i class="fas fa-xmark mr-2"></i>

                    Cancel

                </a>


                <button type="submit"
                    class="px-5 py-2.5 rounded-xl
                       bg-gradient-to-r from-sky-700
                       via-blue-700 to-cyan-600
                       text-white font-medium
                       hover:shadow-lg transition">

                    <i class="fas fa-floppy-disk mr-2"></i>

                    Save Changes

                </button>

            </div>

        </form>

    </div>


    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const mapElement =
                    document.getElementById('complaint-edit-map');

                if (!mapElement) {
                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | INPUTS
                |--------------------------------------------------------------------------
                */

                const latitudeInput =
                    document.getElementById('latitude');

                const longitudeInput =
                    document.getElementById('longitude');


                /*
                |--------------------------------------------------------------------------
                | DEFAULT LOCATION - SAGAY CITY
                |--------------------------------------------------------------------------
                */

                const defaultLatitude = 10.9447;
                const defaultLongitude = 123.4200;


                /*
                |--------------------------------------------------------------------------
                | EXISTING COMPLAINT LOCATION
                |--------------------------------------------------------------------------
                */

                const savedLatitude =
                    parseFloat(latitudeInput.value);

                const savedLongitude =
                    parseFloat(longitudeInput.value);


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
                | MAP
                |--------------------------------------------------------------------------
                */

                const map = L.map(
                    'complaint-edit-map', {
                        zoomControl: true,
                        attributionControl: true
                    }
                );


                /*
                |--------------------------------------------------------------------------
                | OPENSTREETMAP
                |--------------------------------------------------------------------------
                */

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | INITIAL VIEW
                |--------------------------------------------------------------------------
                */

                map.setView(
                    [
                        initialLatitude,
                        initialLongitude
                    ],
                    hasExistingLocation ? 17 : 14
                );


                /*
                |--------------------------------------------------------------------------
                | MARKER
                |--------------------------------------------------------------------------
                */

                let marker = L.marker(
                    [
                        initialLatitude,
                        initialLongitude
                    ], {
                        draggable: true
                    }
                ).addTo(map);


                /*
                |--------------------------------------------------------------------------
                | POPUP
                |--------------------------------------------------------------------------
                */

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
                | UPDATE COORDINATES
                |--------------------------------------------------------------------------
                */

                function updateCoordinates(lat, lng) {

                    const formattedLatitude =
                        Number(lat).toFixed(7);

                    const formattedLongitude =
                        Number(lng).toFixed(7);


                    latitudeInput.value =
                        formattedLatitude;

                    longitudeInput.value =
                        formattedLongitude;

                }


                /*
                |--------------------------------------------------------------------------
                | MAP CLICK
                |--------------------------------------------------------------------------
                */

                map.on('click', function(event) {

                    const lat =
                        event.latlng.lat;

                    const lng =
                        event.latlng.lng;


                    marker.setLatLng([
                        lat,
                        lng
                    ]);


                    updateCoordinates(
                        lat,
                        lng
                    );

                });


                /*
                |--------------------------------------------------------------------------
                | MARKER DRAG
                |--------------------------------------------------------------------------
                */

                marker.on('dragend', function(event) {

                    const position =
                        event.target.getLatLng();


                    updateCoordinates(
                        position.lat,
                        position.lng
                    );

                });


                /*
                |--------------------------------------------------------------------------
                | INITIAL COORDINATES
                |--------------------------------------------------------------------------
                */

                updateCoordinates(
                    initialLatitude,
                    initialLongitude
                );


                /*
                |--------------------------------------------------------------------------
                | MAP SIZE FIX
                |--------------------------------------------------------------------------
                */

                function refreshMap() {

                    map.invalidateSize({
                        animate: false,
                        pan: false
                    });

                }


                requestAnimationFrame(function() {

                    refreshMap();

                    setTimeout(refreshMap, 100);
                    setTimeout(refreshMap, 300);
                    setTimeout(refreshMap, 600);
                    setTimeout(refreshMap, 1000);

                });


                window.addEventListener(
                    'resize',
                    refreshMap
                );

            });
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const registeredSection =
                    document.getElementById('registered-consumer-section');

                const walkInSection =
                    document.getElementById('walk-in-section');

                const consumerSelect =
                    document.getElementById('consumer_id');

                const complainantName =
                    document.getElementById('complainant_name');


                function updateComplainantSections() {

                    const selected =
                        document.querySelector(
                            'input[name="complainant_type"]:checked'
                        );

                    if (!selected) {
                        return;
                    }


                    if (selected.value === 'registered') {

                        registeredSection.classList.remove('hidden');

                        walkInSection.classList.add('hidden');


                        if (consumerSelect) {
                            consumerSelect.disabled = false;
                        }

                        if (complainantName) {
                            complainantName.disabled = true;
                        }

                    }


                    if (selected.value === 'walk_in') {

                        registeredSection.classList.add('hidden');

                        walkInSection.classList.remove('hidden');


                        if (consumerSelect) {
                            consumerSelect.disabled = true;
                            consumerSelect.value = '';
                        }

                        if (complainantName) {
                            complainantName.disabled = false;
                        }

                    }

                }


                document
                    .querySelectorAll(
                        'input[name="complainant_type"]'
                    )
                    .forEach(function(radio) {

                        radio.addEventListener(
                            'change',
                            updateComplainantSections
                        );

                    });


                updateComplainantSections();

            });
        </script>
    @endpush

@endsection
