@extends('consumer.layouts.app')

@section('title', 'Submit Complaint')

@section('content')

    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Submit a Complaint
            </h1>

            <p class="mt-1 text-slate-500">
                Tell us about your water service concern.
            </p>
        </div>


        {{-- Validation Errors --}}
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
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>

            </div>
        @endif


        {{-- Complaint Form --}}
        <form method="POST" action="{{ route('consumer.complaints.store') }}" enctype="multipart/form-data"
            class="space-y-6">

            @csrf


            {{-- Classification --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="mb-6">
                    <h2 class="text-lg font-bold text-slate-900">
                        Complaint Classification
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Select the division and type that best describes your concern.
                    </p>
                </div>


                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Division --}}
                    <div>

                        <label for="division_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            Division
                            <span class="text-red-500">*</span>
                        </label>

                        <select id="division_id" name="division_id" required
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                Select division
                            </option>

                            @foreach ($divisions as $division)
                                <option value="{{ $division->id }}"
                                    {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('division_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Complaint Type --}}
                    <div>

                        <label for="complaint_category_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            Complaint Type
                            <span class="text-red-500">*</span>
                        </label>

                        <select id="complaint_category_id" name="complaint_category_id" required disabled
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500 disabled:bg-slate-100 disabled:text-slate-400">

                            <option value="">
                                Select a division first
                            </option>

                        </select>

                        @error('complaint_category_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Complaint Information --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-900">
                        Complaint Information
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Provide details about the problem.
                    </p>

                </div>


                <div class="space-y-6">


                    {{-- Description --}}
                    <div>

                        <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                            Description
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea id="description" name="description" rows="6" maxlength="5000" required
                            placeholder="Please describe what happened..."
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>

                        @error('description')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>


            {{-- Location --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-900">
                        Problem Location
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        Enter where the concern occurred.
                    </p>

                </div>


                <div class="space-y-6">

                    {{-- Address --}}
                    <div>

                        <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">
                            Address
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea id="address" name="address" rows="3" maxlength="500" required
                            placeholder="House number, street, barangay, municipality..."
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">{{ old('address') }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Landmark --}}
                    <div>

                        <label for="landmark" class="block text-sm font-semibold text-slate-700 mb-2">
                            Landmark
                            <span class="text-slate-400 font-normal">
                                (Optional)
                            </span>
                        </label>

                        <input type="text" id="landmark" name="landmark" value="{{ old('landmark') }}" maxlength="255"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500">

                        @error('landmark')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    {{-- Map Location --}}
                    <div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">

                            <div>

                                <label class="block text-sm font-semibold text-slate-700">
                                    Map Location
                                    <span class="text-slate-400 font-normal">
                                        (Optional)
                                    </span>
                                </label>

                                <p class="mt-1 text-xs text-slate-500">
                                    Click the map or drag the marker to the location of the concern.
                                </p>

                            </div>

                            <button type="button" id="locateMe"
                                class="inline-flex items-center justify-center gap-2 text-sm font-semibold text-sky-600 hover:text-sky-800 whitespace-nowrap">
                                <i class="fas fa-location-crosshairs"></i>
                                Use my location
                            </button>

                        </div>


                        <div id="complaintMap"
                            class="w-full h-80 rounded-xl border border-slate-300 overflow-hidden bg-slate-100"></div>


                        <p class="mt-2 text-xs text-slate-500">
                            Click the map or drag the marker to the location where the concern occurred.
                        </p>


                        {{-- Detected Location --}}
                        <div id="detectedLocationBox" class="hidden mt-4 rounded-2xl border border-sky-100 bg-sky-50 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl bg-white text-sky-600 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-map-pin"></i>
                                </div>

                                <div class="flex-1 min-w-0">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                                        Detected map location
                                    </p>

                                    <p id="detectedLocation" class="mt-1 text-sm font-medium text-slate-700 break-words">
                                        —
                                    </p>

                                    <p id="reverseGeocodeStatus" class="mt-1 text-xs text-slate-500">
                                        Location selected.
                                    </p>

                                </div>

                            </div>


                            <button type="button" id="useDetectedAddress"
                                class="hidden mt-3 text-sm font-semibold text-sky-700 hover:text-sky-900">

                                <i class="fas fa-arrow-down mr-1"></i>

                                Use detected location as address

                            </button>

                        </div>

                    </div>


                    {{-- Hidden Coordinates --}}
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">

                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">

                </div>

            </div>


            {{-- Supporting Photo --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-900">
                        Supporting Photo
                    </h2>

                    <p class="mt-1 text-sm text-slate-500">
                        You may upload a photo showing the concern.
                    </p>

                </div>

                <input type="file" name="photo" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                    class="block w-full text-sm text-slate-600">

                <p class="mt-2 text-xs text-slate-400">
                    JPG, JPEG, PNG, or WEBP. Maximum file size: 5 MB.
                </p>

                @error('photo')
                    <p class="mt-2 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row sm:justify-end gap-3">

                <a href="{{ route('consumer.complaints.index') }}"
                    class="inline-flex items-center justify-center px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50">
                    Cancel
                </a>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-700 text-white font-semibold hover:bg-sky-800">
                    <i class="fas fa-paper-plane"></i>
                    Submit Complaint
                </button>

            </div>

        </form>

    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const divisions = @json($divisions);

            const divisionSelect = document.getElementById('division_id');
            const complaintTypeSelect = document.getElementById('complaint_category_id');

            const oldComplaintType = @json(old('complaint_category_id'));


            function populateComplaintTypes(divisionId, selectedId = null) {

                complaintTypeSelect.innerHTML = '';

                /*
                |--------------------------------------------------------------------------
                | No Division Selected
                |--------------------------------------------------------------------------
                */

                if (!divisionId) {

                    complaintTypeSelect.disabled = true;

                    const option = document.createElement('option');

                    option.value = '';
                    option.textContent = 'Select a division first';

                    complaintTypeSelect.appendChild(option);

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Find Selected Division
                |--------------------------------------------------------------------------
                */

                const division = divisions.find(function(item) {
                    return String(item.id) === String(divisionId);
                });


                /*
                |--------------------------------------------------------------------------
                | Division Not Found
                |--------------------------------------------------------------------------
                */

                if (!division) {

                    complaintTypeSelect.disabled = true;

                    const option = document.createElement('option');

                    option.value = '';
                    option.textContent = 'No complaint types available';

                    complaintTypeSelect.appendChild(option);

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Enable Complaint Type
                |--------------------------------------------------------------------------
                */

                complaintTypeSelect.disabled = false;


                /*
                |--------------------------------------------------------------------------
                | Placeholder
                |--------------------------------------------------------------------------
                */

                const placeholder = document.createElement('option');

                placeholder.value = '';
                placeholder.textContent = 'Select complaint type';

                complaintTypeSelect.appendChild(placeholder);


                /*
                |--------------------------------------------------------------------------
                | Complaint Types
                |--------------------------------------------------------------------------
                */

                division.complaint_types.forEach(function(type) {

                    const option = document.createElement('option');

                    option.value = type.id;
                    option.textContent = type.name;

                    if (
                        selectedId !== null &&
                        String(selectedId) === String(type.id)
                    ) {
                        option.selected = true;
                    }

                    complaintTypeSelect.appendChild(option);
                });


                /*
                |--------------------------------------------------------------------------
                | No Complaint Types
                |--------------------------------------------------------------------------
                */

                if (division.complaint_types.length === 0) {

                    complaintTypeSelect.disabled = true;

                    complaintTypeSelect.innerHTML =
                        '<option value="">No complaint types available</option>';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Division Changed
            |--------------------------------------------------------------------------
            */

            divisionSelect.addEventListener('change', function() {

                populateComplaintTypes(this.value);

            });


            /*
            |--------------------------------------------------------------------------
            | Restore Old Input After Validation Error
            |--------------------------------------------------------------------------
            */

            if (divisionSelect.value) {

                populateComplaintTypes(
                    divisionSelect.value,
                    oldComplaintType
                );

            }

        });
    </script>
@endpush
@push('scripts')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

    {{-- Leaflet JS --}}
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const mapElement = document.getElementById('complaintMap');

            if (!mapElement) {
                console.error('Complaint map container was not found.');
                return;
            }

            if (typeof L === 'undefined') {
                console.error('Leaflet JS was not loaded.');
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | DEFAULT LOCATION — SAGAY
            |--------------------------------------------------------------------------
            */

            const defaultLat = 10.9447;
            const defaultLng = 123.4247;

            /*
            |--------------------------------------------------------------------------
            | FORM ELEMENTS
            |--------------------------------------------------------------------------
            */

            const latitudeInput =
                document.getElementById('latitude');

            const longitudeInput =
                document.getElementById('longitude');

            const addressInput =
                document.getElementById('address');

            const detectedLocationBox =
                document.getElementById('detectedLocationBox');

            const detectedLocation =
                document.getElementById('detectedLocation');

            const reverseGeocodeStatus =
                document.getElementById('reverseGeocodeStatus');

            const useDetectedAddress =
                document.getElementById('useDetectedAddress');

            const locateMe =
                document.getElementById('locateMe');

            /*
            |--------------------------------------------------------------------------
            | MAP
            |--------------------------------------------------------------------------
            */

            const map = L.map('complaintMap').setView(
                [defaultLat, defaultLng],
                14
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
            | MARKER
            |--------------------------------------------------------------------------
            */

            let marker = null;

            let lastDetectedAddress = '';

            /*
            |--------------------------------------------------------------------------
            | REVERSE GEOCODING
            |--------------------------------------------------------------------------
            */

            async function reverseGeocode(lat, lng) {

                if (!detectedLocationBox ||
                    !detectedLocation ||
                    !reverseGeocodeStatus ||
                    !useDetectedAddress) {
                    return;
                }

                detectedLocationBox.classList.remove('hidden');

                detectedLocation.textContent =
                    'Detecting location...';

                reverseGeocodeStatus.textContent =
                    'Please wait while the map location is being identified.';

                useDetectedAddress.classList.add('hidden');

                try {

                    const url =
                        'https://nominatim.openstreetmap.org/reverse' +
                        '?format=jsonv2' +
                        '&lat=' + encodeURIComponent(lat) +
                        '&lon=' + encodeURIComponent(lng) +
                        '&zoom=18' +
                        '&addressdetails=1' +
                        '&accept-language=en';

                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (!response.ok) {
                        throw new Error(
                            'Reverse geocoding request failed.'
                        );
                    }

                    const data = await response.json();

                    if (!data || !data.display_name) {
                        throw new Error(
                            'No readable address was found.'
                        );
                    }

                    lastDetectedAddress =
                        data.display_name;

                    detectedLocation.textContent =
                        lastDetectedAddress;

                    reverseGeocodeStatus.textContent =
                        'This location was detected from the map pin. Review the address before submitting.';

                    useDetectedAddress.classList.remove('hidden');

                } catch (error) {

                    console.error(
                        'Reverse geocoding error:',
                        error
                    );

                    lastDetectedAddress = '';

                    detectedLocation.textContent =
                        'Unable to detect a readable address.';

                    reverseGeocodeStatus.textContent =
                        'You can still enter the address manually.';

                    useDetectedAddress.classList.add('hidden');
                }
            }

            /*
            |--------------------------------------------------------------------------
            | SET LOCATION
            |--------------------------------------------------------------------------
            */

            function setLocation(lat, lng, shouldReverseGeocode = true) {

                latitudeInput.value =
                    Number(lat).toFixed(7);

                longitudeInput.value =
                    Number(lng).toFixed(7);

                if (marker) {

                    marker.setLatLng([
                        lat,
                        lng
                    ]);

                } else {

                    marker = L.marker(
                        [lat, lng], {
                            draggable: true
                        }
                    ).addTo(map);

                    marker.on(
                        'dragend',
                        function(event) {

                            const position =
                                event.target.getLatLng();

                            setLocation(
                                position.lat,
                                position.lng,
                                true
                            );
                        }
                    );
                }

                if (shouldReverseGeocode) {

                    reverseGeocode(
                        lat,
                        lng
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | CLICK MAP
            |--------------------------------------------------------------------------
            */

            map.on(
                'click',
                function(event) {

                    setLocation(
                        event.latlng.lat,
                        event.latlng.lng,
                        true
                    );

                }
            );

            /*
            |--------------------------------------------------------------------------
            | USE DETECTED ADDRESS
            |--------------------------------------------------------------------------
            */

            if (useDetectedAddress) {

                useDetectedAddress.addEventListener(
                    'click',
                    function() {

                        if (!lastDetectedAddress) {
                            return;
                        }

                        addressInput.value =
                            lastDetectedAddress;

                        addressInput.focus();

                        reverseGeocodeStatus.textContent =
                            'Detected location copied to the address field. You may edit it to include Purok/PRK or other local details.';
                    }
                );
            }

            /*
            |--------------------------------------------------------------------------
            | USE MY LOCATION
            |--------------------------------------------------------------------------
            */

            if (locateMe) {

                locateMe.addEventListener(
                    'click',
                    function() {

                        if (!navigator.geolocation) {

                            alert(
                                'Location services are not supported by your browser.'
                            );

                            return;
                        }

                        const button = this;

                        button.disabled = true;

                        button.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Locating...';

                        navigator.geolocation.getCurrentPosition(

                            function(position) {

                                const lat =
                                    position.coords.latitude;

                                const lng =
                                    position.coords.longitude;

                                map.setView(
                                    [lat, lng],
                                    17
                                );

                                setLocation(
                                    lat,
                                    lng,
                                    true
                                );

                                button.disabled = false;

                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use my location';

                            },

                            function(error) {

                                console.error(
                                    'Geolocation error:',
                                    error
                                );

                                alert(
                                    'Unable to get your location. Please allow location access or select the location manually on the map.'
                                );

                                button.disabled = false;

                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use my location';

                            },

                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    }
                );
            }

            /*
            |--------------------------------------------------------------------------
            | RESTORE OLD INPUT AFTER VALIDATION ERROR
            |--------------------------------------------------------------------------
            */

            const oldLat =
                parseFloat(latitudeInput.value);

            const oldLng =
                parseFloat(longitudeInput.value);

            if (!isNaN(oldLat) && !isNaN(oldLng)) {

                map.setView(
                    [oldLat, oldLng],
                    17
                );

                setLocation(
                    oldLat,
                    oldLng,
                    false
                );
            }

            /*
            |--------------------------------------------------------------------------
            | LEAFLET RENDERING FIX
            |--------------------------------------------------------------------------
            */

            setTimeout(function() {

                map.invalidateSize();

            }, 300);

        });
    </script>
@endpush
