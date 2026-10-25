
@extends('consumer.layouts.app')

@section('content')
@php
    $address = $consumer->address;
    $barangays = config('sagay.barangays', []);
    $savedLatitude = old('latitude', $address?->latitude);
    $savedLongitude = old('longitude', $address?->longitude);
    $currentBarangay = old('barangay', $address?->barangay ?? '');
    $municipality = old('municipality', $address?->municipality ?? 'Sagay City');
    $province = old('province', $address?->province ?? 'Negros Occidental');
@endphp

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">

<style>
    .profile-page {
        max-width: 1080px;
        margin: 0 auto;
        padding: 24px 18px 44px;
        color: #172033;
    }

    .profile-heading {
        margin-bottom: 26px;
    }

    .profile-heading h1 {
        margin: 0 0 6px;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 750;
        letter-spacing: -0.04em;
    }

    .profile-heading p {
        margin: 0;
        color: #64748b;
        font-size: .94rem;
    }

    .profile-card {
        margin-bottom: 20px;
        padding: 25px;
        background: #fff;
        border: 1px solid #e5eaf1;
        border-radius: 16px;
        box-shadow: 0 4px 18px rgb(15 23 42 / 3%);
    }

    .section-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 22px;
    }

    .section-icon {
        display: grid;
        place-items: center;
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        color: #2563eb;
        background: #eff6ff;
        border-radius: 12px;
        font-size: 1.15rem;
    }

    .section-heading h2 {
        margin: 1px 0 4px;
        font-size: 1.08rem;
        font-weight: 700;
    }

    .section-heading p {
        margin: 0;
        color: #64748b;
        font-size: .86rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .profile-field {
        min-width: 0;
    }

    .profile-field.full {
        grid-column: 1 / -1;
    }

    .profile-field label {
        display: block;
        margin-bottom: 7px;
        color: #334155;
        font-size: .85rem;
        font-weight: 650;
    }

    .profile-field input,
    .profile-field select {
        display: block;
        width: 100%;
        min-height: 45px;
        padding: 10px 12px;
        color: #172033;
        background: #fff;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        outline: none;
        font: inherit;
        font-size: .92rem;
        transition: border-color .15s, box-shadow .15s;
    }

    .profile-field input:focus,
    .profile-field select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgb(59 130 246 / 12%);
    }

    .profile-field input[readonly] {
        background: #f8fafc;
        color: #64748b;
    }

    .field-hint {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: .78rem;
    }

    .field-error {
        display: block;
        margin-top: 5px;
        color: #dc2626;
        font-size: .8rem;
    }

    .map-search-row {
        display: flex;
        gap: 8px;
        margin-bottom: 10px;
    }

    .map-search-row input {
        flex: 1;
        min-width: 0;
    }

    .subtle-button {
        min-height: 43px;
        padding: 9px 13px;
        color: #334155;
        background: #fff;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        cursor: pointer;
        font: inherit;
        font-size: .85rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .subtle-button:hover {
        background: #f8fafc;
    }

    .map-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    #profile-map {
        width: 100%;
        height: 350px;
        overflow: hidden;
        border: 1px solid #dbe2ea;
        border-radius: 12px;
        z-index: 1;
    }

    #map-status {
        min-height: 20px;
        margin: 9px 0 0;
        color: #64748b;
        font-size: .82rem;
    }

    .map-note {
        margin: 8px 0 20px;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.5;
    }

    .address-summary {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 14px 0 20px;
        padding: 12px 14px;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e8edf3;
        border-radius: 10px;
        font-size: .86rem;
        line-height: 1.5;
    }

    .address-summary strong {
        display: block;
        margin-bottom: 2px;
        color: #172033;
    }

    .password-wrap {
        position: relative;
    }

    .password-wrap input {
        padding-right: 70px;
    }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        padding: 5px;
        color: #2563eb;
        background: transparent;
        border: 0;
        cursor: pointer;
        font-size: .8rem;
        font-weight: 650;
    }

    .profile-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-top: 24px;
    }

    .profile-footer p {
        margin: 0;
        color: #64748b;
        font-size: .82rem;
    }

    .save-button {
        min-height: 46px;
        padding: 12px 23px;
        color: #fff;
        background: #2563eb;
        border: 0;
        border-radius: 10px;
        cursor: pointer;
        font: inherit;
        font-size: .92rem;
        font-weight: 700;
        box-shadow: 0 4px 10px rgb(37 99 235 / 18%);
    }

    .save-button:hover {
        background: #1d4ed8;
    }

    .save-button:disabled {
        cursor: wait;
        opacity: .65;
    }

    .notice {
        margin-bottom: 18px;
        padding: 12px 14px;
        border-radius: 10px;
        font-size: .9rem;
    }

    .notice.success {
        color: #166534;
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .notice.error {
        color: #991b1b;
        background: #fef2f2;
        border: 1px solid #fecaca;
    }

    @media (max-width: 640px) {
        .profile-page {
            padding: 18px 12px 32px;
        }

        .profile-card {
            padding: 18px 15px;
            border-radius: 13px;
        }

        .profile-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .profile-field.full {
            grid-column: auto;
        }

        #profile-map {
            height: 290px;
        }

        .map-search-row {
            flex-wrap: wrap;
        }

        .map-search-row input {
            flex-basis: 100%;
        }

        .profile-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .save-button {
            width: 100%;
        }
    }
</style>

<div class="profile-page">
    <header class="profile-heading">
        <h1>Edit your profile</h1>
        <p>Manage your personal details, service address, and account password.</p>
    </header>

    @if (session('success'))
        <div class="notice success">{{ session('success') }}</div>
    @endif

    @if (session('password_success'))
        <div class="notice success">{{ session('password_success') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice error">
            <strong>Please review the following:</strong>
            <ul style="margin:8px 0 0;padding-left:20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main profile form --}}
    <form id="profile-form" method="POST" action="{{ route('consumer.profile.update') }}">
        @csrf
        @method('PUT')

        {{-- Coordinates are intentionally hidden, not displayed as editable fields. --}}
        <input type="hidden" name="latitude" id="latitude" value="{{ $savedLatitude }}">
        <input type="hidden" name="longitude" id="longitude" value="{{ $savedLongitude }}">

        <section class="profile-card">
            <div class="section-heading">
                <div class="section-icon">♙</div>
                <div>
                    <h2>Personal information</h2>
                    <p>Keep your name and personal details up to date.</p>
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-field">
                    <label for="first_name">First name *</label>
                    <input id="first_name" name="first_name" value="{{ old('first_name', $consumer->first_name) }}" required autocomplete="given-name">
                    @error('first_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="middle_name">Middle name</label>
                    <input id="middle_name" name="middle_name" value="{{ old('middle_name', $consumer->middle_name) }}" autocomplete="additional-name">
                    @error('middle_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="last_name">Last name *</label>
                    <input id="last_name" name="last_name" value="{{ old('last_name', $consumer->last_name) }}" required autocomplete="family-name">
                    @error('last_name') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="suffix">Suffix</label>
                    <input id="suffix" name="suffix" value="{{ old('suffix', $consumer->suffix) }}" placeholder="e.g. Jr., III">
                    @error('suffix') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="sex">Sex *</label>
                    <select id="sex" name="sex" required>
                        <option value="">Select sex</option>
                        @foreach (['Male', 'Female', 'Other'] as $option)
                            <option value="{{ $option }}" @selected(old('sex', $consumer->sex) === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                    @error('sex') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="profile-card">
            <div class="section-heading">
                <div class="section-icon">✉</div>
                <div>
                    <h2>Contact details</h2>
                    <p>Use contact information where you can be reached.</p>
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-field">
                    <label for="email">Email address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email', $consumer->email) }}" required autocomplete="email">
                    @error('email') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="phone">Phone number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $consumer->phone) }}" required autocomplete="tel">
                    @error('phone') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        <section class="profile-card">
            <div class="section-heading">
                <div class="section-icon">⌖</div>
                <div>
                    <h2>Service address</h2>
                    <p>Search for your location or move the map pin to your exact property.</p>
                </div>
            </div>

            <div class="map-search-row">
                <input id="map-search" type="search" placeholder="Search an address, street, or landmark" aria-label="Search map location">
                <button type="button" class="subtle-button" id="search-location">Search</button>
            </div>

            <div class="map-toolbar">
                <button type="button" class="subtle-button" id="use-my-location">◎ Use my location</button>
                <button type="button" class="subtle-button" id="map-street-view">Street map</button>
                <button type="button" class="subtle-button" id="map-satellite-view">Satellite</button>
            </div>

            <div id="profile-map" aria-label="Interactive service address map"></div>
            <p id="map-status" role="status" aria-live="polite">Loading map…</p>
            <p class="map-note">For the best result, zoom in to your property and place the pin on the building or entrance. Map suggestions may not always identify the exact house or unit, so check the address fields below.</p>

            <div class="address-summary">
                <span aria-hidden="true">📍</span>
                <div>
                    <strong>Selected map location</strong>
                    <span id="selected-address">Move the pin or search for your address.</span>
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-field">
                    <label for="house_no">House / building number</label>
                    <input id="house_no" name="house_no" value="{{ old('house_no', $address?->house_no) }}" autocomplete="address-line1">
                    @error('house_no') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="street">Street</label>
                    <input id="street" name="street" value="{{ old('street', $address?->street) }}">
                    @error('street') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="purok">Purok / subdivision</label>
                    <input id="purok" name="purok" value="{{ old('purok', $address?->purok) }}">
                    @error('purok') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="barangay">Barangay *</label>
                    @if (count($barangays))
                        <select id="barangay" name="barangay" required>
                            <option value="">Select barangay</option>
                            @foreach ($barangays as $key => $item)
                                @php
                                    $barangayValue = is_array($item) ? ($item['name'] ?? $item['label'] ?? $key) : $item;
                                @endphp
                                <option value="{{ $barangayValue }}" @selected($currentBarangay === $barangayValue)>
                                    {{ $barangayValue }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input id="barangay" name="barangay" value="{{ $currentBarangay }}" required>
                    @endif
                    @error('barangay') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="municipality">City / municipality</label>
                    <input id="municipality" name="municipality" value="{{ $municipality }}">
                    @error('municipality') <span class="field-error">{{ $message }}</span> @enderror
                </div>

                <div class="profile-field">
                    <label for="province">Province</label>
                    <input id="province" name="province" value="{{ $province }}">
                    @error('province') <span class="field-error">{{ $message }}</span> @enderror
                </div>
            </div>
        </section>

        {{-- Password fields use the existing password endpoint through the same visible Save button. --}}
        <section class="profile-card">
            <div class="section-heading">
                <div class="section-icon">⌑</div>
                <div>
                    <h2>Change password</h2>
                    <p>Leave these fields empty if you do not want to change your password.</p>
                </div>
            </div>

            <div class="profile-grid">
                <div class="profile-field full">
                    <label for="current_password">Current password</label>
                    <div class="password-wrap">
                        <input type="password" id="current_password" name="current_password" autocomplete="current-password" form="password-form">
                        <button type="button" class="password-toggle" data-target="current_password">Show</button>
                    </div>
                </div>

                <div class="profile-field">
                    <label for="password">New password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password" autocomplete="new-password" form="password-form">
                        <button type="button" class="password-toggle" data-target="password">Show</button>
                    </div>
                </div>

                <div class="profile-field">
                    <label for="password_confirmation">Confirm new password</label>
                    <div class="password-wrap">
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" form="password-form">
                        <button type="button" class="password-toggle" data-target="password_confirmation">Show</button>
                    </div>
                </div>
            </div>
        </section>

        <div class="profile-footer">
            <p>* Required fields. Your changes are saved to your consumer account.</p>
            <button type="submit" id="save-changes" class="save-button">Save Changes</button>
        </div>
    </form>

    {{-- Separate request target is required because the existing controller has a separate password route. --}}
    <form id="password-form" method="POST" action="{{ route('consumer.profile.password') }}" hidden>
        @csrf
        @method('PUT')
    </form>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const status = document.getElementById('map-status');
    const latitudeInput = document.getElementById('latitude');
    const longitudeInput = document.getElementById('longitude');
    const selectedAddress = document.getElementById('selected-address');
    const searchInput = document.getElementById('map-search');

    const addressFields = {
        house_no: document.getElementById('house_no'),
        street: document.getElementById('street'),
        purok: document.getElementById('purok'),
        barangay: document.getElementById('barangay'),
        municipality: document.getElementById('municipality'),
        province: document.getElementById('province')
    };

    const defaultLat = 10.9447;
    const defaultLng = 123.4247;
    const hasSavedCoords = latitudeInput.value !== '' && longitudeInput.value !== '';
    const initialLat = hasSavedCoords ? Number(latitudeInput.value) : defaultLat;
    const initialLng = hasSavedCoords ? Number(longitudeInput.value) : defaultLng;

    const map = L.map('profile-map', {
        scrollWheelZoom: true
    }).setView([initialLat, initialLng], hasSavedCoords ? 17 : 13);

    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 20,
        attribution: '&copy; OpenStreetMap contributors'
    });

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri'
        }
    );

    streetLayer.addTo(map);

    let marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(map);
    let reverseTimer = null;
    let lastReverseRequest = 0;
    let manualFields = new Set();
    let latestRequest = 0;

    Object.values(addressFields).forEach(function (field) {
        if (!field) return;

        field.addEventListener('input', function () {
            manualFields.add(field.id);
        });

        field.addEventListener('change', function () {
            manualFields.add(field.id);
        });
    });

    function setStatus(message) {
        status.textContent = message;
    }

    function setCoordinates(latlng) {
        latitudeInput.value = Number(latlng.lat).toFixed(7);
        longitudeInput.value = Number(latlng.lng).toFixed(7);
    }

    function setField(name, value, force = false) {
        const field = addressFields[name];
        if (!field || value === undefined || value === null || value === '') return;
        if (!force && manualFields.has(name)) return;

        if (field.tagName === 'SELECT') {
            const match = Array.from(field.options).find(option =>
                option.value.toLowerCase() === String(value).toLowerCase()
            );

            if (match) {
                field.value = match.value;
            } else if (force) {
                field.value = '';
            }
        } else {
            field.value = value;
        }
    }

    function updateAddressFromResult(result, force = false) {
        const a = result.address || {};
        const display = result.display_name || 'Selected map location';

        selectedAddress.textContent = display;

        const house = a.house_number || a.house_name || '';
        const road = a.road || a.residential || a.pedestrian ||
                     a.footway || a.path || a.street || '';
        const barangay = a.suburb || a.neighbourhood || a.quarter ||
                         a.village || a.hamlet || a.city_district || '';
        const city = a.city || a.town || a.municipality || a.city_district || '';
        const province = a.state || a.province || '';

        setField('house_no', house, force);
        setField('street', road, force);
        setField('barangay', barangay, force);
        setField('municipality', city, force);
        setField('province', province, force);

        if (result.lat && result.lon) {
            setCoordinates({
                lat: Number(result.lat),
                lng: Number(result.lon)
            });
        }
    }

    async function reverseGeocode(latlng) {
        const requestId = ++lastReverseRequest;
        setCoordinates(latlng);
        setStatus('Finding the nearest address…');

        try {
            const url = new URL('https://nominatim.openstreetmap.org/reverse');
            url.search = new URLSearchParams({
                format: 'jsonv2',
                lat: latlng.lat,
                lon: latlng.lng,
                zoom: '18',
                addressdetails: '1'
            });

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Address lookup failed.');

            const result = await response.json();
            if (requestId !== lastReverseRequest) return;

            if (result && result.address) {
                updateAddressFromResult(result);
                setStatus('Location updated. Check the address fields for accuracy.');
            } else {
                selectedAddress.textContent = 'No matching street address found.';
                setStatus('Pin saved. Please enter the address details manually.');
            }
        } catch (error) {
            if (requestId !== lastReverseRequest) return;
            setStatus('Could not retrieve the address. You can still enter it manually.');
        }
    }

    function movePin(latlng, zoom = 17) {
        marker.setLatLng(latlng);
        map.setView(latlng, zoom);
        setCoordinates(latlng);
        clearTimeout(reverseTimer);
        reverseTimer = setTimeout(() => reverseGeocode(latlng), 650);
    }

    marker.on('dragend', function () {
        movePin(marker.getLatLng());
    });

    map.on('click', function (event) {
        movePin(event.latlng);
    });

    async function searchAddress() {
        const query = searchInput.value.trim();
        if (!query) {
            setStatus('Enter an address or landmark to search.');
            return;
        }

        setStatus('Searching for the best address match…');
        const requestId = ++latestRequest;

        try {
            const url = new URL('https://nominatim.openstreetmap.org/search');
            url.search = new URLSearchParams({
                format: 'jsonv2',
                q: query,
                addressdetails: '1',
                limit: '5',
                countrycodes: 'ph'
            });

            const response = await fetch(url, {
                headers: { 'Accept': 'application/json' }
            });

            if (!response.ok) throw new Error('Search failed.');

            const results = await response.json();
            if (requestId !== latestRequest) return;

            if (!results.length) {
                setStatus('No results found. Try adding the barangay, city, or province.');
                return;
            }

            const preferred = results.find(result =>
                /Sagay|Negros Occidental/i.test(result.display_name || '')
            ) || results[0];

            const latlng = {
                lat: Number(preferred.lat),
                lng: Number(preferred.lon)
            };

            marker.setLatLng(latlng);
            map.setView(latlng, 18);
            setCoordinates(latlng);
            updateAddressFromResult(preferred);
            setStatus('Search result selected. Zoom in and adjust the pin to your property.');

            // Do not immediately reverse-geocode the same search result;
            // it can be less detailed than the result returned by search.
        } catch (error) {
            if (requestId !== latestRequest) return;
            setStatus('Search is temporarily unavailable. You can move the pin manually.');
        }
    }

    document.getElementById('search-location').addEventListener('click', searchAddress);

    searchInput.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            event.preventDefault();
            searchAddress();
        }
    });

    document.getElementById('use-my-location').addEventListener('click', function () {
        if (!navigator.geolocation) {
            setStatus('Your browser does not support location detection. Search for your address instead.');
            return;
        }

        setStatus('Requesting your location…');

        navigator.geolocation.getCurrentPosition(
            function (position) {
                movePin({
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                }, 18);
                setStatus('Your location was found. Checking its address…');
            },
            function () {
                setStatus('Location access was unavailable. Allow location permission or search for your address.');
            },
            {
                enableHighAccuracy: true,
                timeout: 15000,
                maximumAge: 0
            }
        );
    });

    document.getElementById('map-street-view').addEventListener('click', function () {
        if (map.hasLayer(satelliteLayer)) map.removeLayer(satelliteLayer);
        if (!map.hasLayer(streetLayer)) streetLayer.addTo(map);
    });

    document.getElementById('map-satellite-view').addEventListener('click', function () {
        if (map.hasLayer(streetLayer)) map.removeLayer(streetLayer);
        if (!map.hasLayer(satelliteLayer)) satelliteLayer.addTo(map);
    });

    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);
            const showing = input.type === 'text';
            input.type = showing ? 'password' : 'text';
            button.textContent = showing ? 'Show' : 'Hide';
        });
    });

    // One visible button: save the password first (only when requested),
    // then submit the profile form to its existing endpoint.
    const profileForm = document.getElementById('profile-form');
    const passwordForm = document.getElementById('password-form');
    const saveButton = document.getElementById('save-changes');

    profileForm.addEventListener('submit', async function (event) {
        event.preventDefault();

        const currentPassword = document.getElementById('current_password').value;
        const newPassword = document.getElementById('password').value;
        const confirmation = document.getElementById('password_confirmation').value;
        const passwordRequested = currentPassword || newPassword || confirmation;

        if (passwordRequested) {
            if (!currentPassword || !newPassword || !confirmation) {
                alert('To change your password, fill in the current password, new password, and confirmation.');
                return;
            }

            if (newPassword !== confirmation) {
                alert('The new password and confirmation do not match.');
                return;
            }

            const passwordData = new FormData();
            passwordData.append('_token', passwordForm.querySelector('[name="_token"]').value);
            passwordData.append('_method', 'PUT');
            passwordData.append('current_password', currentPassword);
            passwordData.append('password', newPassword);
            passwordData.append('password_confirmation', confirmation);

            saveButton.disabled = true;
            saveButton.textContent = 'Saving…';

            try {
                const response = await fetch(passwordForm.action, {
                    method: 'POST',
                    body: passwordData,
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });

                if (response.status === 422) {
                    const errors = await response.json();
                    const messages = Object.values(errors.errors || {}).flat();
                    alert(messages.join('\n') || 'Please check your password fields.');
                    return;
                }

                if (!response.ok) {
                    throw new Error('Password update failed.');
                }
            } catch (error) {
                alert('Your password could not be updated. Please check the current password and try again.');
                return;
            }
        }

        saveButton.disabled = true;
        saveButton.textContent = 'Saving…';
        profileForm.submit();
    });

    // Leaflet may need a size refresh after the layout has settled.
    setTimeout(() => map.invalidateSize(), 150);

    if (hasSavedCoords) {
        selectedAddress.textContent = 'Saved map location. Drag the pin to adjust it if needed.';
        setStatus('Saved location loaded.');
    } else {
        setStatus('Choose your location by searching, using your location, or moving the pin.');
    }
});
</script>
@endsection
