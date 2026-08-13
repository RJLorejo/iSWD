@extends('customer-service.layouts.app')

@section('title', 'Create Complaint')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIINfQ3Jk4e6l6vJ7M8R3Q7V8Y6N5K5D3M=" crossorigin="" />
@endpush

@section('content')

    <div class="space-y-6">

        <x-form.page-header title="Create Complaint"
            subtitle="Register a new consumer complaint or reported water service problem." />

        <form action="{{ route('customer-service.complaints.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6">

            @csrf


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
                                Identify whether the complaint is from a registered consumer
                                or a walk-in complainant.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- ===================================================== --}}
                    {{-- COMPLAINANT TYPE --}}
                    {{-- ===================================================== --}}

                    <div>

                        <label class="block text-sm font-medium text-gray-700 mb-2">

                            Complainant Type

                            <span class="text-red-500">*</span>

                        </label>


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
                                    {{ old('complainant_type', 'registered') === 'registered' ? 'checked' : '' }}>

                                <div>

                                    <div class="font-semibold text-gray-900">

                                        <i class="fas fa-user-check text-blue-600 mr-1"></i>

                                        Registered Consumer

                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">

                                        Select an existing consumer record.

                                    </p>

                                </div>

                            </label>


                            {{-- WALK-IN --}}
                            <label
                                class="complainant-type-option
                                flex items-start gap-3 p-4
                                border border-gray-200
                                rounded-xl cursor-pointer
                                hover:border-amber-400
                                hover:bg-amber-50
                                transition">

                                <input type="radio" name="complainant_type" value="walk_in" class="mt-1"
                                    {{ old('complainant_type') === 'walk_in' ? 'checked' : '' }}>

                                <div>

                                    <div class="font-semibold text-gray-900">

                                        <i class="fas fa-person-walking text-amber-600 mr-1"></i>

                                        Walk-in / Unregistered

                                    </div>

                                    <p class="text-xs text-gray-500 mt-1">

                                        Record a person without requiring a consumer account.

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



                    {{-- ===================================================== --}}
                    {{-- REGISTERED CONSUMER --}}
                    {{-- ===================================================== --}}

                    <div id="registered-consumer-section">

                        <label for="consumer_id" class="block text-sm font-medium text-gray-700 mb-2">

                            Registered Consumer

                            <span class="text-red-500">*</span>

                        </label>


                        <select name="consumer_id" id="consumer_id"
                            class="w-full rounded-xl border-gray-300
                            focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                Select Registered Consumer
                            </option>

                            @foreach ($consumers as $consumer)
                                <option value="{{ $consumer->id }}" @selected(old('consumer_id') == $consumer->id)>

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


                        <p class="text-xs text-gray-500 mt-2">

                            Select the registered consumer who is reporting the problem.

                        </p>

                    </div>



                    {{-- ===================================================== --}}
                    {{-- WALK-IN COMPLAINANT --}}
                    {{-- ===================================================== --}}

                    <div id="walk-in-section" class="{{ old('complainant_type') === 'walk_in' ? '' : 'hidden' }}">


                        <div class="rounded-xl bg-amber-50
                            border border-amber-200 p-4 mb-5">

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

                                        Record the person's identity for complaint
                                        tracking without creating a consumer account.

                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            {{-- NAME --}}
                            <div>

                                <label for="complainant_name" class="block text-sm font-medium text-gray-700 mb-2">

                                    Complainant Full Name

                                    <span class="text-red-500">*</span>

                                </label>


                                <input type="text" name="complainant_name" id="complainant_name"
                                    value="{{ old('complainant_name') }}" placeholder="Enter full name"
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

                                    <span class="text-gray-400 text-xs">
                                        Optional
                                    </span>

                                </label>


                                <input type="text" name="complainant_phone" id="complainant_phone"
                                    value="{{ old('complainant_phone') }}" placeholder="09XXXXXXXXX"
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


                    {{-- INFORMATION --}}
                    <div class="rounded-xl bg-gray-50
                        border border-gray-200 p-4">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-circle-info
                                text-blue-600 mt-0.5"></i>

                            <p class="text-xs text-gray-600">

                                <strong>Registered consumers</strong> are linked to
                                their existing consumer record.

                                <strong>Walk-in complainants</strong> are recorded
                                directly on this complaint and do not need an account.

                            </p>

                        </div>

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
                            class="w-10 h-10 rounded-xl bg-orange-100
                            text-orange-600 flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                        <div>

                            <h3 class="font-semibold text-gray-900">
                                Complaint Information
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Provide details about the reported problem.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- CATEGORY / PRIORITY --}}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                        {{-- CATEGORY --}}
                        <div>

                            <label class="block text-sm font-medium text-gray-700 mb-2">

                                Complaint Category

                                <span class="text-red-500">*</span>

                            </label>


                            <select name="complaint_category_id"
                                class="w-full rounded-xl border-gray-300
                                focus:border-blue-500 focus:ring-blue-500"
                                required>

                                <option value="">
                                    Select Complaint Category
                                </option>

                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('complaint_category_id') == $category->id)>

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

                                <span class="text-red-500">*</span>

                            </label>


                            <select name="priority"
                                class="w-full rounded-xl border-gray-300
                                focus:border-blue-500 focus:ring-blue-500"
                                required>

                                @foreach (['Low', 'Medium', 'High', 'Critical'] as $priority)
                                    <option value="{{ $priority }}" @selected(old('priority', 'Medium') === $priority)>

                                        {{ $priority }}

                                    </option>
                                @endforeach

                            </select>


                            @error('priority')
                                <p class="text-sm text-red-600 mt-1">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>

                    </div>



                    {{-- SUBJECT --}}

                    <div>

                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">

                            Complaint Subject

                            <span class="text-red-500">*</span>

                        </label>


                        <input type="text" name="subject" id="subject" value="{{ old('subject') }}"
                            placeholder="Example: No water supply in our area"
                            class="w-full rounded-xl border-gray-300
                            focus:border-blue-500 focus:ring-blue-500"
                            required>


                        @error('subject')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- DESCRIPTION --}}

                    <div>

                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">

                            Problem Description

                            <span class="text-red-500">*</span>

                        </label>


                        <textarea name="description" id="description" rows="5" placeholder="Describe the problem in detail..."
                            class="w-full rounded-xl border-gray-300
                            focus:border-blue-500 focus:ring-blue-500"
                            required>{{ old('description') }}</textarea>


                        @error('description')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </x-form.card>



            {{-- ========================================================= --}}
            {{-- PROBLEM LOCATION --}}
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
                                Problem Location
                            </h3>

                            <p class="text-sm text-gray-500 mt-1">
                                Enter where the water service problem occurred.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 space-y-6">


                    {{-- ADDRESS --}}
                    <div>

                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">

                            Problem Address

                            <span class="text-red-500">*</span>

                        </label>


                        <input type="text" name="address" id="address" value="{{ old('address') }}"
                            placeholder="House No., Street, Barangay, Sagay City"
                            class="w-full rounded-xl border-gray-300
                            focus:border-blue-500 focus:ring-blue-500"
                            required>


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


                        <input type="text" name="landmark" id="landmark" value="{{ old('landmark') }}"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-gray-300
                            focus:border-blue-500 focus:ring-blue-500">


                        @error('landmark')
                            <p class="text-sm text-red-600 mt-1">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>



                    {{-- MAP --}}
                    <div>

                        <div class="flex items-center justify-between mb-3">

                            <div>

                                <label class="block text-sm font-medium text-gray-700">

                                    Reported Problem Location

                                </label>

                                <p class="text-xs text-gray-500 mt-1">

                                    Click on the map to pin the exact location
                                    of the reported problem.

                                </p>

                            </div>

                            <i class="fas fa-map-location-dot
                                text-blue-600 text-lg"></i>

                        </div>


                        <div id="complaint-map"
                            class="w-full h-[420px] rounded-2xl
                            border border-gray-300
                            overflow-hidden relative z-0">
                        </div>


                        {{-- HIDDEN COORDINATES --}}

                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">


                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">



                        {{-- COORDINATE DISPLAY --}}

                        <div class="grid grid-cols-1 md:grid-cols-2
                            gap-4 mt-4">


                            {{-- LATITUDE --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Latitude

                                </label>


                                <div class="relative">

                                    <span
                                        class="absolute inset-y-0 left-0
                                        flex items-center pl-3 text-gray-400">

                                        <i class="fas fa-location-arrow"></i>

                                    </span>


                                    <input type="text" id="latitude-display" readonly
                                        placeholder="Select a location on the map"
                                        class="w-full pl-10 rounded-xl
                                        border-gray-300 bg-gray-50
                                        text-gray-600
                                        focus:border-blue-500
                                        focus:ring-blue-500">

                                </div>

                            </div>



                            {{-- LONGITUDE --}}
                            <div>

                                <label class="block text-sm font-medium text-gray-700 mb-2">

                                    Longitude

                                </label>


                                <div class="relative">

                                    <span
                                        class="absolute inset-y-0 left-0
                                        flex items-center pl-3 text-gray-400">

                                        <i class="fas fa-location-arrow"></i>

                                    </span>


                                    <input type="text" id="longitude-display" readonly
                                        placeholder="Select a location on the map"
                                        class="w-full pl-10 rounded-xl
                                        border-gray-300 bg-gray-50
                                        text-gray-600
                                        focus:border-blue-500
                                        focus:ring-blue-500">

                                </div>

                            </div>

                        </div>



                        {{-- LOCATION STATUS --}}
                        <div id="selected-location"
                            class="hidden mt-4 rounded-xl
                            bg-blue-50 border border-blue-200 p-4">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-9 h-9 rounded-lg bg-blue-100
                                    text-blue-600 flex items-center justify-center">

                                    <i class="fas fa-location-dot"></i>

                                </div>


                                <div>

                                    <p class="text-sm font-semibold text-blue-900">

                                        Location Selected

                                    </p>

                                    <p class="text-xs text-blue-700 mt-1">

                                        The exact reported problem location
                                        has been pinned on the map.

                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </x-form.card>



            {{-- ========================================================= --}}
            {{-- PHOTO EVIDENCE --}}
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
                                Attach a photo showing the reported problem.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6">

                    <label for="photo" class="block text-sm font-medium text-gray-700 mb-2">

                        Complaint Photo

                    </label>


                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-gray-600
                        border border-gray-300 rounded-xl
                        file:mr-4 file:py-2.5 file:px-4
                        file:rounded-lg file:border-0
                        file:bg-blue-50 file:text-blue-700
                        hover:file:bg-blue-100">


                    <p class="text-xs text-gray-500 mt-2">

                        JPG, JPEG, PNG or WEBP.
                        Maximum size: 5 MB.

                    </p>


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

            <div class="flex items-center justify-end gap-3">

                <a href="{{ route('customer-service.complaints.index') }}"
                    class="px-5 py-2.5 rounded-xl
                    border border-gray-300
                    text-gray-700
                    hover:bg-gray-50
                    transition">

                    <i class="fas fa-arrow-left mr-2"></i>

                    Cancel

                </a>


                <button type="submit"
                    class="px-5 py-2.5 rounded-xl
                    bg-gradient-to-r
                    from-sky-700
                    via-blue-700
                    to-cyan-600
                    text-white
                    font-medium
                    hover:shadow-lg
                    transition">

                    <i class="fas fa-paper-plane mr-2"></i>

                    Submit Complaint

                </button>

            </div>

        </form>

    </div>



    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {


                /*
                |--------------------------------------------------------------------------
                | COMPLAINANT TYPE SWITCHING
                |--------------------------------------------------------------------------
                */

                const complainantTypeInputs =
                    document.querySelectorAll(
                        'input[name="complainant_type"]'
                    );

                const registeredSection =
                    document.getElementById(
                        'registered-consumer-section'
                    );

                const walkInSection =
                    document.getElementById(
                        'walk-in-section'
                    );

                const consumerInput =
                    document.getElementById(
                        'consumer_id'
                    );

                const complainantNameInput =
                    document.getElementById(
                        'complainant_name'
                    );


                function updateComplainantType() {

                    const selected =
                        document.querySelector(
                            'input[name="complainant_type"]:checked'
                        );

                    if (!selected) {
                        return;
                    }


                    if (selected.value === 'walk_in') {

                        registeredSection.classList.add('hidden');

                        walkInSection.classList.remove('hidden');

                        consumerInput.value = '';

                        consumerInput.removeAttribute('required');

                        complainantNameInput.setAttribute(
                            'required',
                            'required'
                        );

                    } else {

                        registeredSection.classList.remove('hidden');

                        walkInSection.classList.add('hidden');

                        consumerInput.setAttribute(
                            'required',
                            'required'
                        );

                        complainantNameInput.removeAttribute(
                            'required'
                        );

                    }

                }


                complainantTypeInputs.forEach(function(input) {

                    input.addEventListener(
                        'change',
                        updateComplainantType
                    );

                });


                updateComplainantType();



                /*
                |--------------------------------------------------------------------------
                | MAP
                |--------------------------------------------------------------------------
                */

                const mapElement =
                    document.getElementById(
                        'complaint-map'
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

                const latitudeDisplay =
                    document.getElementById(
                        'latitude-display'
                    );

                const longitudeDisplay =
                    document.getElementById(
                        'longitude-display'
                    );

                const selectedLocation =
                    document.getElementById(
                        'selected-location'
                    );


                /*
                |--------------------------------------------------------------------------
                | DEFAULT SAGAY CITY LOCATION
                |--------------------------------------------------------------------------
                */

                const defaultLatitude = 10.9447;

                const defaultLongitude = 123.4200;


                /*
                |--------------------------------------------------------------------------
                | OLD LOCATION
                |--------------------------------------------------------------------------
                */

                const oldLatitude =
                    parseFloat(
                        latitudeInput.value
                    );

                const oldLongitude =
                    parseFloat(
                        longitudeInput.value
                    );


                const hasExistingLocation = !isNaN(oldLatitude) &&
                    !isNaN(oldLongitude);


                const initialLatitude =
                    hasExistingLocation ?
                    oldLatitude :
                    defaultLatitude;


                const initialLongitude =
                    hasExistingLocation ?
                    oldLongitude :
                    defaultLongitude;



                /*
                |--------------------------------------------------------------------------
                | CREATE MAP
                |--------------------------------------------------------------------------
                */

                const map =
                    L.map(
                        'complaint-map', {
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

                let marker = null;



                /*
                |--------------------------------------------------------------------------
                | UPDATE COORDINATES
                |--------------------------------------------------------------------------
                */

                function updateCoordinates(lat, lng) {

                    const formattedLat =
                        Number(lat).toFixed(7);

                    const formattedLng =
                        Number(lng).toFixed(7);


                    latitudeInput.value =
                        formattedLat;

                    longitudeInput.value =
                        formattedLng;


                    latitudeDisplay.value =
                        formattedLat;

                    longitudeDisplay.value =
                        formattedLng;


                    if (selectedLocation) {

                        selectedLocation.classList.remove(
                            'hidden'
                        );

                    }

                }



                /*
                |--------------------------------------------------------------------------
                | UPDATE LOCATION
                |--------------------------------------------------------------------------
                */

                function updateLocation(lat, lng) {

                    updateCoordinates(
                        lat,
                        lng
                    );


                    if (!marker) {

                        marker =
                            L.marker(
                                [
                                    lat,
                                    lng
                                ], {
                                    draggable: true
                                }
                            ).addTo(map);


                        marker.bindPopup(`

                            <div class="text-sm">

                                <div class="font-semibold text-gray-900">

                                    <i class="fas fa-location-dot
                                    text-blue-600 mr-1"></i>

                                    Reported Problem Location

                                </div>

                                <div class="text-gray-500 mt-1">

                                    Drag the marker to adjust
                                    the exact location.

                                </div>

                            </div>

                        `);


                        marker.on(
                            'dragend',
                            function(event) {

                                const position =
                                    event.target.getLatLng();

                                updateLocation(
                                    position.lat,
                                    position.lng
                                );

                            }
                        );

                    } else {

                        marker.setLatLng(
                            [
                                lat,
                                lng
                            ]
                        );

                    }


                    map.panTo(
                        [
                            lat,
                            lng
                        ]
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | MAP CLICK
                |--------------------------------------------------------------------------
                */

                map.on(
                    'click',
                    function(event) {

                        updateLocation(
                            event.latlng.lat,
                            event.latlng.lng
                        );

                    }
                );



                /*
                |--------------------------------------------------------------------------
                | RESTORE OLD LOCATION
                |--------------------------------------------------------------------------
                */

                if (hasExistingLocation) {

                    updateLocation(
                        oldLatitude,
                        oldLongitude
                    );

                }



                /*
                |--------------------------------------------------------------------------
                | MAP SIZE
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

@endsection
