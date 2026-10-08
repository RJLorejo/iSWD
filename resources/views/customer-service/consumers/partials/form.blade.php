@php
    $consumer = $consumer ?? null;
    $isEdit = $consumer !== null;

    $savedLatitude = old('latitude', $consumer?->address?->latitude);

    $savedLongitude = old('longitude', $consumer?->address?->longitude);
@endphp


<div class="space-y-7">

    {{-- ========================================================= --}}
    {{-- CONSUMER INFORMATION --}}
    {{-- ========================================================= --}}

    <section class="px-3 sm:px-4">

        <div class="mb-4">

            <h3 class="text-base font-semibold text-gray-900">
                Consumer Information
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Account, identity and contact information.
            </p>

        </div>


        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">

            {{-- Account Number --}}
            <div>

                <label for="account_number" class="mb-1 block text-sm font-medium text-gray-700">

                    Account Number
                    <span class="text-red-500">*</span>

                </label>

                <input type="text" name="account_number" id="account_number"
                    value="{{ old('account_number', $consumer?->account_number ?? '') }}"
                    placeholder="Enter SWD account number"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('account_number')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- First Name --}}
            <div>

                <label for="first_name" class="mb-1 block text-sm font-medium text-gray-700">

                    First Name
                    <span class="text-red-500">*</span>

                </label>

                <input type="text" name="first_name" id="first_name"
                    value="{{ old('first_name', $consumer?->first_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('first_name')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Middle Name --}}
            <div>

                <label for="middle_name" class="mb-1 block text-sm font-medium text-gray-700">

                    Middle Name

                </label>

                <input type="text" name="middle_name" id="middle_name"
                    value="{{ old('middle_name', $consumer?->middle_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500">

                @error('middle_name')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Last Name --}}
            <div>

                <label for="last_name" class="mb-1 block text-sm font-medium text-gray-700">

                    Last Name
                    <span class="text-red-500">*</span>

                </label>

                <input type="text" name="last_name" id="last_name"
                    value="{{ old('last_name', $consumer?->last_name ?? '') }}"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('last_name')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Suffix --}}
            <div>

                <label for="suffix" class="mb-1 block text-sm font-medium text-gray-700">

                    Suffix

                </label>

                <input type="text" name="suffix" id="suffix"
                    value="{{ old('suffix', $consumer?->suffix ?? '') }}" placeholder="Jr., Sr., III"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500">

                @error('suffix')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Sex --}}
            <div>

                <label for="sex" class="mb-1 block text-sm font-medium text-gray-700">

                    Sex
                    <span class="text-red-500">*</span>

                </label>

                <select name="sex" id="sex"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                    <option value="">
                        Select Sex
                    </option>

                    <option value="Male" @selected(old('sex', $consumer?->sex ?? '') === 'Male')>

                        Male

                    </option>

                    <option value="Female" @selected(old('sex', $consumer?->sex ?? '') === 'Female')>

                        Female

                    </option>

                </select>

                @error('sex')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Phone --}}
            <div>

                <label for="phone" class="mb-1 block text-sm font-medium text-gray-700">

                    Contact Number
                    <span class="text-red-500">*</span>

                </label>

                <input type="text" name="phone" id="phone" value="{{ old('phone', $consumer?->phone ?? '') }}"
                    placeholder="09XXXXXXXXX"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                @error('phone')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>


            {{-- Email --}}
            <div>

                <label for="email" class="mb-1 block text-sm font-medium text-gray-700">

                    Email Address
                    <span class="text-red-500">*</span>

                </label>

                <input type="email" name="email" id="email" value="{{ old('email', $consumer?->email ?? '') }}"
                    placeholder="consumer@email.com"
                    class="w-full rounded-lg border-gray-300 text-sm
                           focus:border-blue-500 focus:ring-blue-500"
                    required>

                <p class="mt-1 text-[11px] text-gray-500">
                    Used to sign in to the consumer portal.
                </p>

                @error('email')
                    <p class="mt-1 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- ACCOUNT STATUS --}}
    {{-- ========================================================= --}}

    @if ($isEdit)
        <section class="border-t border-gray-200 px-3 pt-6 sm:px-4">

            <div class="mb-4">

                <h3 class="text-base font-semibold text-gray-900">
                    Online Account Status
                </h3>

                <p class="mt-1 text-xs text-gray-500">
                    Control access to the consumer portal.
                </p>

            </div>


            @php
                $currentStatus = (string) old('is_active', $consumer->is_active ? '1' : '0');
            @endphp


            <div class="grid max-w-2xl grid-cols-1 gap-3 sm:grid-cols-2">

                <label
                    class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition
                    {{ $currentStatus === '1' ? 'border-green-400 bg-green-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}">

                    <input type="radio" name="is_active" value="1" @checked($currentStatus === '1')
                        class="text-green-600 focus:ring-green-500" required>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                               bg-green-100 text-green-600">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-900">
                            Active
                        </p>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Portal access enabled.
                        </p>

                    </div>

                </label>


                <label
                    class="flex cursor-pointer items-center gap-3 rounded-xl border p-4 transition
                    {{ $currentStatus === '0' ? 'border-red-400 bg-red-50' : 'border-gray-200 bg-white hover:bg-gray-50' }}">

                    <input type="radio" name="is_active" value="0" @checked($currentStatus === '0')
                        class="text-red-600 focus:ring-red-500" required>

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
                               bg-red-100 text-red-600">

                        <i class="fa-solid fa-circle-xmark"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-gray-900">
                            Inactive
                        </p>

                        <p class="mt-0.5 text-xs text-gray-500">
                            Portal access disabled.
                        </p>

                    </div>

                </label>

            </div>


            @error('is_active')
                <p class="mt-2 text-xs text-red-600">
                    {{ $message }}
                </p>
            @enderror

        </section>
    @endif


    {{-- ========================================================= --}}
    {{-- SERVICE ADDRESS --}}
    {{-- ========================================================= --}}

    <section class="border-t border-gray-200 px-3 pt-6 sm:px-4">

        <div class="mb-4">

            <h3 class="text-base font-semibold text-gray-900">
                Registered Service Location
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Place the pin at the consumer's registered SWD water service connection.
            </p>

        </div>


        {{-- Map FIRST --}}
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-gray-100">

            <div id="consumer-service-map" class="h-[330px] w-full sm:h-[390px] lg:h-[430px]">
            </div>

        </div>


        {{-- Map Controls --}}
        <div class="mt-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">


            <p id="consumer-location-status" class="text-xs text-gray-500">

                @if ($savedLatitude !== null && $savedLongitude !== null)
                    Saved service location loaded. Drag the pin to adjust it.
                @else
                    Click the map or use your current location to place the pin.
                @endif

            </p>

        </div>


        <input type="hidden" name="latitude" id="consumer_latitude" value="{{ $savedLatitude }}">

        <input type="hidden" name="longitude" id="consumer_longitude" value="{{ $savedLongitude }}">


        @error('latitude')
            <p class="mt-2 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror

        @error('longitude')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
        @enderror


        {{-- Detected Address --}}
        <div id="detected-address-panel" class="mt-4 hidden rounded-xl border border-cyan-200 bg-cyan-50 p-4">

            <div class="flex items-start gap-3">

                <div
                    class="flex h-9 w-9 shrink-0 items-center justify-center
                           rounded-lg bg-cyan-100 text-cyan-700">

                    <i class="fa-solid fa-map-location-dot"></i>

                </div>

                <div class="min-w-0 flex-1">

                    <p class="text-xs font-semibold text-cyan-900">
                        Detected Address
                    </p>

                    <p id="detected-address-text" class="mt-1 text-sm leading-5 text-cyan-800">
                    </p>

                </div>

            </div>

        </div>


        {{-- Address fields --}}
        <div class="mt-5">

            <div class="mb-3">

                <h4 class="text-sm font-semibold text-gray-900">
                    Service Address
                </h4>

                <p class="mt-0.5 text-xs text-gray-500">
                    The map fills available address details automatically. Review them before saving.
                </p>

            </div>


            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">

                {{-- House Number --}}
                <div>

                    <label for="house_no" class="mb-1 block text-sm font-medium text-gray-700">

                        House No.

                    </label>

                    <input type="text" name="house_no" id="house_no"
                        value="{{ old('house_no', $consumer?->address?->house_no ?? '') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500">

                    @error('house_no')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Street --}}
                <div>

                    <label for="street" class="mb-1 block text-sm font-medium text-gray-700">

                        Street

                    </label>

                    <input type="text" name="street" id="street"
                        value="{{ old('street', $consumer?->address?->street ?? '') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500">

                    @error('street')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Purok --}}
                <div>

                    <label for="purok" class="mb-1 block text-sm font-medium text-gray-700">

                        Purok

                    </label>

                    <input type="text" name="purok" id="purok"
                        value="{{ old('purok', $consumer?->address?->purok ?? '') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500">

                    @error('purok')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Barangay --}}
                <div>

                    <label for="barangay" class="mb-1 block text-sm font-medium text-gray-700">

                        Barangay
                        <span class="text-red-500">*</span>

                    </label>

                    <input type="text" name="barangay" id="barangay"
                        value="{{ old('barangay', $consumer?->address?->barangay ?? '') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500"
                        required>

                    @error('barangay')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Municipality --}}
                <div>

                    <label for="municipality" class="mb-1 block text-sm font-medium text-gray-700">

                        Municipality
                        <span class="text-red-500">*</span>

                    </label>

                    <input type="text" name="municipality" id="municipality"
                        value="{{ old('municipality', $consumer?->address?->municipality ?? 'Sagay') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500"
                        required>

                    @error('municipality')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                {{-- Province --}}
                <div>

                    <label for="province" class="mb-1 block text-sm font-medium text-gray-700">

                        Province
                        <span class="text-red-500">*</span>

                    </label>

                    <input type="text" name="province" id="province"
                        value="{{ old('province', $consumer?->address?->province ?? 'Negros Occidental') }}"
                        class="w-full rounded-lg border-gray-300 text-sm
                               focus:border-blue-500 focus:ring-blue-500"
                        required>

                    @error('province')
                        <p class="mt-1 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================= --}}
    {{-- PORTAL ACCOUNT --}}
    {{-- ========================================================= --}}

    <section class="border-t border-gray-200 px-3 pt-6 sm:px-4">

        <div class="mb-4">

            <h3 class="text-base font-semibold text-gray-900">
                Consumer Portal Account
            </h3>

            <p class="mt-1 text-xs text-gray-500">
                Manage iSWD online portal access.
            </p>

        </div>


        @if (!$isEdit)
            <div class="rounded-xl border border-blue-200 bg-blue-50 p-4">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center
                               rounded-lg bg-blue-100 text-blue-600">

                        <i class="fa-solid fa-user-lock"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-blue-900">
                            Online Account Created Automatically
                        </p>

                        <p class="mt-1 text-xs leading-5 text-blue-700">
                            An active portal account and one-time temporary password
                            will be created after registration.
                        </p>

                    </div>

                </div>

            </div>
        @elseif ($consumer->user)
            <div class="space-y-4">

                <div
                    class="rounded-xl border p-4
                    {{ $consumer->user->is_active ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50' }}">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg
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
                                class="mt-1 break-all text-xs
                                {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">

                                {{ $consumer->user->email }}

                            </p>

                        </div>

                    </div>

                </div>


                {{-- Password Reset --}}
                <div class="rounded-xl border border-gray-200 bg-white p-4">

                    <div class="mb-4">

                        <h4 class="text-sm font-semibold text-gray-900">
                            Reset Portal Password
                        </h4>

                        <p class="mt-1 text-xs text-gray-500">
                            Leave blank to keep the existing password.
                        </p>

                    </div>


                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">

                        <div>

                            <label for="new_password" class="mb-1 block text-sm font-medium text-gray-700">

                                New Password

                            </label>

                            <div class="relative">

                                <input type="password" name="new_password" id="new_password"
                                    autocomplete="new-password" placeholder="Enter new password"
                                    class="w-full rounded-lg border-gray-300 pr-11 text-sm
                                           focus:border-blue-500 focus:ring-blue-500">

                                <button type="button" onclick="toggleConsumerPassword('new_password', this)"
                                    class="absolute right-3 top-1/2 -translate-y-1/2
                                           text-gray-400 hover:text-gray-600"
                                    tabindex="-1">

                                    <i class="fa-solid fa-eye"></i>

                                </button>

                            </div>

                            @error('new_password')
                                <p class="mt-1 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                        </div>


                        <div>

                            <label for="new_password_confirmation"
                                class="mb-1 block text-sm font-medium text-gray-700">

                                Confirm New Password

                            </label>

                            <div class="relative">

                                <input type="password" name="new_password_confirmation"
                                    id="new_password_confirmation" autocomplete="new-password"
                                    placeholder="Confirm new password"
                                    class="w-full rounded-lg border-gray-300 pr-11 text-sm
                                           focus:border-blue-500 focus:ring-blue-500">

                                <button type="button"
                                    onclick="toggleConsumerPassword(
                                        'new_password_confirmation',
                                        this
                                    )"
                                    class="absolute right-3 top-1/2 -translate-y-1/2
                                           text-gray-400 hover:text-gray-600"
                                    tabindex="-1">

                                    <i class="fa-solid fa-eye"></i>

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        @else
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fa-solid fa-triangle-exclamation mt-0.5 text-amber-600">
                    </i>

                    <div>

                        <p class="text-sm font-semibold text-amber-900">
                            Portal Account Missing
                        </p>

                        <p class="mt-1 text-xs text-amber-700">
                            This consumer does not have a linked User account.
                        </p>

                    </div>

                </div>

            </div>
        @endif

    </section>


    {{-- ========================================================= --}}
    {{-- ACTIONS --}}
    {{-- ========================================================= --}}

    <div
        class="flex flex-col-reverse gap-3 border-t border-gray-200
               px-3 pt-5 sm:flex-row sm:items-center sm:justify-end sm:px-4">

        <a href="{{ route('customer-service.consumers.index') }}"
            class="inline-flex items-center justify-center rounded-lg
                   border border-gray-300 px-5 py-2.5
                   text-sm font-medium text-gray-700
                   hover:bg-gray-50">

            Cancel

        </a>


        <button type="submit"
            class="inline-flex items-center justify-center gap-2
                   rounded-lg bg-blue-600 px-5 py-2.5
                   text-sm font-medium text-white
                   transition hover:bg-blue-700">

            <i class="fa-solid fa-floppy-disk"></i>

            {{ $isEdit ? 'Update Consumer' : 'Register Consumer' }}

        </button>

    </div>

</div>


@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush


@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const mapElement =
                document.getElementById('consumer-service-map');

            const latitudeInput =
                document.getElementById('consumer_latitude');

            const longitudeInput =
                document.getElementById('consumer_longitude');

            const locateButton =
                document.getElementById('use-consumer-location');

            const statusText =
                document.getElementById('consumer-location-status');

            const detectedPanel =
                document.getElementById('detected-address-panel');

            const detectedText =
                document.getElementById('detected-address-text');

            if (
                !mapElement ||
                !latitudeInput ||
                !longitudeInput ||
                typeof L === 'undefined'
            ) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Address Fields
            |--------------------------------------------------------------------------
            */

            const houseNoInput =
                document.getElementById('house_no');

            const streetInput =
                document.getElementById('street');

            const purokInput =
                document.getElementById('purok');

            const barangayInput =
                document.getElementById('barangay');

            const municipalityInput =
                document.getElementById('municipality');

            const provinceInput =
                document.getElementById('province');


            /*
            |--------------------------------------------------------------------------
            | Default Map
            |--------------------------------------------------------------------------
            */

            const defaultLat = 10.9447;
            const defaultLng = 123.4247;

            const savedLat =
                parseFloat(latitudeInput.value);

            const savedLng =
                parseFloat(longitudeInput.value);

            const hasSavedLocation = !Number.isNaN(savedLat) &&
                !Number.isNaN(savedLng);


            const map = L.map(
                'consumer-service-map'
            ).setView(
                hasSavedLocation ?
                [savedLat, savedLng] :
                [defaultLat, defaultLng],

                hasSavedLocation ?
                17 :
                14
            );


            L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }
            ).addTo(map);


            let marker = null;


            /*
            |--------------------------------------------------------------------------
            | Reverse Geocoding
            |--------------------------------------------------------------------------
            */

            async function reverseGeocode(
                latitude,
                longitude
            ) {

                if (statusText) {
                    statusText.textContent =
                        'Finding the address for this location...';
                }

                try {

                    const url =
                        'https://nominatim.openstreetmap.org/reverse' +
                        '?format=jsonv2' +
                        '&lat=' + encodeURIComponent(latitude) +
                        '&lon=' + encodeURIComponent(longitude) +
                        '&zoom=18' +
                        '&addressdetails=1';

                    const response =
                        await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });


                    if (!response.ok) {
                        throw new Error(
                            'Reverse geocoding request failed.'
                        );
                    }


                    const data =
                        await response.json();

                    const address =
                        data.address || {};


                    /*
                    |--------------------------------------------------------------------------
                    | Display Detected Address
                    |--------------------------------------------------------------------------
                    */

                    if (
                        detectedPanel &&
                        detectedText &&
                        data.display_name
                    ) {

                        detectedText.textContent =
                            data.display_name;

                        detectedPanel.classList.remove(
                            'hidden'
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Fill Available Address Fields
                    |--------------------------------------------------------------------------
                    |
                    | Nominatim names vary depending on the mapped area.
                    | We use fallbacks rather than depending on one field only.
                    |
                    */

                    const detectedHouseNo =
                        address.house_number || '';

                    const detectedStreet =
                        address.road ||
                        address.street ||
                        address.residential ||
                        '';

                    const detectedPurok =
                        address.neighbourhood ||
                        address.quarter ||
                        address.suburb ||
                        '';

                    const detectedBarangay =
                        address.village ||
                        address.hamlet ||
                        address.suburb ||
                        address.neighbourhood ||
                        '';

                    const detectedMunicipality =
                        address.city ||
                        address.town ||
                        address.municipality ||
                        address.city_district ||
                        '';

                    const detectedProvince =
                        address.state ||
                        address.region ||
                        '';


                    if (
                        houseNoInput &&
                        detectedHouseNo
                    ) {
                        houseNoInput.value =
                            detectedHouseNo;
                    }


                    if (
                        streetInput &&
                        detectedStreet
                    ) {
                        streetInput.value =
                            detectedStreet;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Purok
                    |--------------------------------------------------------------------------
                    |
                    | OSM does not consistently contain Philippine purok names.
                    | Only fill it when a useful neighbourhood-level value exists.
                    |
                    */

                    if (
                        purokInput &&
                        detectedPurok
                    ) {
                        purokInput.value =
                            detectedPurok;
                    }


                    if (
                        barangayInput &&
                        detectedBarangay
                    ) {
                        barangayInput.value =
                            detectedBarangay;
                    }


                    if (
                        municipalityInput &&
                        detectedMunicipality
                    ) {
                        municipalityInput.value =
                            detectedMunicipality;
                    }


                    if (
                        provinceInput &&
                        detectedProvince
                    ) {
                        provinceInput.value =
                            detectedProvince;
                    }


                    if (statusText) {
                        statusText.textContent =
                            'Service location selected. Review the detected address below.';
                    }

                } catch (error) {

                    console.error(
                        'Reverse geocoding error:',
                        error
                    );


                    if (statusText) {
                        statusText.textContent =
                            'Location selected. Address lookup was unavailable, so enter the address manually.';
                    }
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Set Map Location
            |--------------------------------------------------------------------------
            */

            function setLocation(
                latitude,
                longitude,
                shouldReverseGeocode = true
            ) {

                latitudeInput.value =
                    Number(latitude).toFixed(7);

                longitudeInput.value =
                    Number(longitude).toFixed(7);


                if (marker) {

                    marker.setLatLng([
                        latitude,
                        longitude
                    ]);

                } else {

                    marker = L.marker(
                        [
                            latitude,
                            longitude
                        ], {
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
                        latitude,
                        longitude
                    );

                } else if (statusText) {

                    statusText.textContent =
                        'Saved service location loaded. Drag the pin to adjust it.';
                }
            }


            /*
            |--------------------------------------------------------------------------
            | Restore Existing Location
            |--------------------------------------------------------------------------
            */

            if (hasSavedLocation) {

                setLocation(
                    savedLat,
                    savedLng,
                    false
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Manual Map Click
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
            | Use My Location
            |--------------------------------------------------------------------------
            */

            if (locateButton) {

                locateButton.addEventListener(
                    'click',
                    function() {

                        if (!navigator.geolocation) {

                            alert(
                                'Location services are not supported by this browser. Please place the pin manually.'
                            );

                            return;
                        }


                        const button = this;

                        const originalHtml =
                            button.innerHTML;


                        button.disabled = true;

                        button.innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin"></i>' +
                            '<span>Locating...</span>';


                        if (statusText) {
                            statusText.textContent =
                                'Getting your current location...';
                        }


                        navigator.geolocation.getCurrentPosition(

                            function(position) {

                                const latitude =
                                    position.coords.latitude;

                                const longitude =
                                    position.coords.longitude;


                                map.setView(
                                    [
                                        latitude,
                                        longitude
                                    ],
                                    17
                                );


                                setLocation(
                                    latitude,
                                    longitude,
                                    true
                                );


                                button.disabled = false;
                                button.innerHTML =
                                    originalHtml;
                            },


                            function(error) {

                                console.error(
                                    'Geolocation error:',
                                    error
                                );


                                let message =
                                    'Unable to get your current location. Please place the pin manually.';


                                if (error.code === 1) {

                                    message =
                                        'Location permission was denied. Allow location access in the browser or place the pin manually.';

                                } else if (error.code === 2) {

                                    message =
                                        'Your current location is unavailable. Please place the pin manually.';

                                } else if (error.code === 3) {

                                    message =
                                        'Getting your location took too long. Please try again or place the pin manually.';
                                }


                                if (statusText) {
                                    statusText.textContent =
                                        message;
                                }


                                alert(message);


                                button.disabled = false;
                                button.innerHTML =
                                    originalHtml;
                            },

                            {
                                enableHighAccuracy: true,
                                timeout: 15000,
                                maximumAge: 0
                            }
                        );
                    }
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Fix Leaflet Size
            |--------------------------------------------------------------------------
            */

            requestAnimationFrame(
                function() {

                    requestAnimationFrame(
                        function() {

                            map.invalidateSize();
                        }
                    );
                }
            );


            window.addEventListener(
                'resize',
                function() {

                    map.invalidateSize();
                }
            );

        });


        /*
        |--------------------------------------------------------------------------
        | Password Toggle
        |--------------------------------------------------------------------------
        */

        function toggleConsumerPassword(
            inputId,
            button
        ) {

            const input =
                document.getElementById(inputId);

            const icon =
                button.querySelector('i');


            if (!input) {
                return;
            }


            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove(
                    'fa-eye'
                );

                icon.classList.add(
                    'fa-eye-slash'
                );

            } else {

                input.type = 'password';

                icon.classList.remove(
                    'fa-eye-slash'
                );

                icon.classList.add(
                    'fa-eye'
                );
            }
        }
    </script>
@endpush
