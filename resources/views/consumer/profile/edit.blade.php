
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

<link rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
      integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
      crossorigin="">

<style>
    .profile-page {
        width: 100%;
        max-width: 1080px;
        margin: 0 auto;
        padding: 24px 18px 44px;
        color: #172033;
    }

    .profile-heading { margin-bottom: 25px; }

    .profile-heading h1 {
        margin: 0 0 6px;
        font-size: clamp(1.5rem, 3vw, 2rem);
        font-weight: 750;
        letter-spacing: -.04em;
    }

    .profile-heading p {
        margin: 0;
        color: #64748b;
        font-size: .94rem;
        line-height: 1.6;
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
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        color: #2563eb;
        background: #eff6ff;
        border-radius: 12px;
    }

    .section-icon svg {
        width: 21px;
        height: 21px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
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
        line-height: 1.5;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .profile-field { min-width: 0; }
    .profile-field.full { grid-column: 1 / -1; }

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
        min-width: 0;
        min-height: 46px;
        padding: 11px 13px;
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
    .profile-field select:focus,
    .map-search-row input:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgb(59 130 246 / 12%);
    }

    .field-hint {
        display: block;
        margin-top: 6px;
        color: #64748b;
        font-size: .78rem;
        line-height: 1.5;
    }

    .field-error {
        display: block;
        margin-top: 5px;
        color: #dc2626;
        font-size: .8rem;
    }

    .map-search-row {
        display: flex;
        gap: 9px;
        margin-bottom: 10px;
    }

    .map-search-row input {
        flex: 1;
        min-width: 0;
        min-height: 45px;
        padding: 10px 13px;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        outline: none;
        font: inherit;
        font-size: .9rem;
    }

    .map-toolbar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    .subtle-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 43px;
        padding: 9px 14px;
        color: #334155;
        background: #fff;
        border: 1px solid #dbe2ea;
        border-radius: 9px;
        cursor: pointer;
        font: inherit;
        font-size: .85rem;
        font-weight: 600;
        transition: background .15s, border-color .15s, color .15s;
    }

    .subtle-button:hover {
        color: #1d4ed8;
        background: #eff6ff;
        border-color: #bfdbfe;
    }

    .subtle-button:disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    .subtle-button svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .apply-location-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 100%;
        min-height: 46px;
        margin-top: 12px;
        padding: 11px 16px;
        color: #fff;
        background: #0891b2;
        border: 1px solid #0891b2;
        border-radius: 10px;
        cursor: pointer;
        font: inherit;
        font-size: .9rem;
        font-weight: 700;
        transition: background .15s;
    }

    .apply-location-button:hover {
        background: #0e7490;
    }

    .apply-location-button:disabled {
        cursor: not-allowed;
        opacity: .55;
    }

    .apply-location-button svg {
        width: 18px;
        height: 18px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    #profile-map {
        position: relative;
        z-index: 1;
        width: 100%;
        height: 350px;
        overflow: hidden;
        border: 1px solid #dbe2ea;
        border-radius: 12px;
    }

    #map-status {
        min-height: 20px;
        margin: 9px 0 0;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.5;
    }

    .map-note {
        margin: 8px 0 18px;
        color: #64748b;
        font-size: .82rem;
        line-height: 1.6;
    }

    .address-summary {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        margin: 14px 0 20px;
        padding: 13px 14px;
        color: #334155;
        background: #f8fafc;
        border: 1px solid #e8edf3;
        border-radius: 10px;
        font-size: .86rem;
        line-height: 1.5;
    }

    .address-summary-icon { color: #2563eb; }

    .address-summary-icon svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
    }

    .address-summary strong {
        display: block;
        margin-bottom: 3px;
        color: #172033;
    }

    .location-notice {
        margin-top: 10px;
        padding: 11px 13px;
        color: #854d0e;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 9px;
        font-size: .82rem;
        line-height: 1.5;
    }

    .password-wrap { position: relative; }

    .password-wrap input { padding-right: 48px; }

    .password-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        display: grid;
        place-items: center;
        width: 32px;
        height: 32px;
        padding: 0;
        color: #64748b;
        background: transparent;
        border: 0;
        border-radius: 7px;
        cursor: pointer;
        transform: translateY(-50%);
    }

    .password-toggle:hover {
        color: #2563eb;
        background: #eff6ff;
    }

    .password-toggle svg {
        width: 19px;
        height: 19px;
        fill: none;
        stroke: currentColor;
        stroke-width: 1.8;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .password-toggle .icon-eye-off { display: none; }
    .password-toggle.is-visible .icon-eye { display: none; }
    .password-toggle.is-visible .icon-eye-off { display: block; }

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
        line-height: 1.5;
    }

    .profile-actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
    }

    .cancel-button,
    .save-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        min-height: 46px;
        padding: 11px 22px;
        border-radius: 10px;
        cursor: pointer;
        font: inherit;
        font-size: .9rem;
        font-weight: 700;
        text-decoration: none;
        transition: background .15s, border-color .15s, opacity .15s;
    }

    .cancel-button {
        color: #334155;
        background: #fff;
        border: 1px solid #dbe2ea;
    }

    .cancel-button:hover { background: #f8fafc; }

    .save-button {
        color: #fff;
        background: #2563eb;
        border: 1px solid #2563eb;
        box-shadow: 0 4px 10px rgb(37 99 235 / 18%);
    }

    .save-button:hover {
        background: #1d4ed8;
        border-color: #1d4ed8;
    }

    .save-button:disabled {
        cursor: wait;
        opacity: .7;
    }

    .save-button svg {
        width: 17px;
        height: 17px;
        fill: none;
        stroke: currentColor;
        stroke-width: 2;
        stroke-linecap: round;
        stroke-linejoin: round;
    }

    .button-spinner {
        display: none;
        width: 17px;
        height: 17px;
        border: 2px solid rgb(255 255 255 / 40%);
        border-top-color: #fff;
        border-radius: 50%;
        animation: profile-spin .7s linear infinite;
    }

    .save-button.is-loading .button-spinner { display: inline-block; }
    .save-button.is-loading .save-icon { display: none; }

    @keyframes profile-spin {
        to { transform: rotate(360deg); }
    }

    .notice {
        margin-bottom: 18px;
        padding: 13px 15px;
        border-radius: 10px;
        font-size: .9rem;
        line-height: 1.55;
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

    .notice ul {
        margin: 8px 0 0;
        padding-left: 20px;
    }

    @media (max-width: 640px) {
        .profile-page { padding: 18px 12px 32px; }

        .profile-card {
            padding: 18px 15px;
            border-radius: 13px;
        }

        .profile-grid {
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .profile-field.full { grid-column: auto; }

        #profile-map { height: 290px; }

        .map-search-row { flex-wrap: wrap; }

        .map-search-row input { flex-basis: 100%; }

        .map-search-row .subtle-button { width: 100%; }

        .map-toolbar .subtle-button { flex: 1 1 auto; }

        .profile-footer {
            align-items: stretch;
            flex-direction: column;
        }

        .profile-actions {
            width: 100%;
            flex-direction: column-reverse;
        }

        .cancel-button,
        .save-button { width: 100%; }

        .apply-location-button { width: 100%; }
    }
</style>

<div class="profile-page">

    <header class="profile-heading">
        <h1>Edit your profile</h1>
        <p>Manage your personal details, service address, and account password.</p>
    </header>

    @if (session('success'))
        <div class="notice success" role="status">
            {{ session('success') }}
        </div>
    @endif

    @if (session('password_success'))
        <div class="notice success" role="status">
            {{ session('password_success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="notice error" role="alert">
            <strong>Please review the following:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="profile-form"
          method="POST"
          action="{{ route('consumer.profile.update') }}">

        @csrf
        @method('PUT')

        {{-- Only applied coordinates are submitted. --}}
        <input type="hidden"
               name="latitude"
               id="latitude"
               value="{{ $savedLatitude }}">

        <input type="hidden"
               name="longitude"
               id="longitude"
               value="{{ $savedLongitude }}">

        {{-- PERSONAL INFORMATION --}}
        <section class="profile-card">

            <div class="section-heading">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24">
                        <circle cx="12" cy="8" r="4"></circle>
                        <path d="M5 21v-2a7 7 0 0 1 14 0v2"></path>
                    </svg>
                </div>
                <div>
                    <h2>Personal information</h2>
                    <p>Keep your name and personal details up to date.</p>
                </div>
            </div>

            <div class="profile-grid">

                <div class="profile-field">
                    <label for="first_name">First name *</label>
                    <input type="text" id="first_name" name="first_name"
                           value="{{ old('first_name', $consumer->first_name) }}"
                           required autocomplete="given-name">
                    @error('first_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="middle_name">Middle name</label>
                    <input type="text" id="middle_name" name="middle_name"
                           value="{{ old('middle_name', $consumer->middle_name) }}"
                           autocomplete="additional-name">
                    @error('middle_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="last_name">Last name *</label>
                    <input type="text" id="last_name" name="last_name"
                           value="{{ old('last_name', $consumer->last_name) }}"
                           required autocomplete="family-name">
                    @error('last_name')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="suffix">Suffix</label>
                    <input type="text" id="suffix" name="suffix"
                           value="{{ old('suffix', $consumer->suffix) }}"
                           placeholder="e.g. Jr., III">
                    @error('suffix')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="sex">Sex *</label>
                    <select id="sex" name="sex" required>
                        <option value="">Select sex</option>
                        @foreach (['Male', 'Female', 'Other'] as $option)
                            <option value="{{ $option }}"
                                @selected(old('sex', $consumer->sex) === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                    @error('sex')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        {{-- CONTACT DETAILS --}}
        <section class="profile-card">

            <div class="section-heading">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="3" y="5" width="18" height="14" rx="2"></rect>
                        <path d="m3 7 9 6 9-6"></path>
                    </svg>
                </div>
                <div>
                    <h2>Contact details</h2>
                    <p>Use contact information where you can be reached.</p>
                </div>
            </div>

            <div class="profile-grid">

                <div class="profile-field">
                    <label for="email">Email address *</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $consumer->email) }}"
                           required autocomplete="email">
                    @error('email')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="phone">Phone number *</label>
                    <input type="tel" id="phone" name="phone"
                           value="{{ old('phone', $consumer->phone) }}"
                           inputmode="numeric"
                           pattern="09[0-9]{9}"
                           minlength="11"
                           maxlength="11"
                           autocomplete="tel"
                           placeholder="09XXXXXXXXX"
                           title="Enter an 11-digit Philippine mobile number starting with 09."
                           required>
                    <span class="field-hint">
                        Enter 11 digits only. Format: 09XXXXXXXXX
                    </span>
                    @error('phone')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        {{-- SERVICE ADDRESS --}}
        <section class="profile-card">

            <div class="section-heading">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24">
                        <path d="M20 10c0 5-8 12-8 12S4 15 4 10a8 8 0 1 1 16 0Z"></path>
                        <circle cx="12" cy="10" r="2.5"></circle>
                    </svg>
                </div>
                <div>
                    <h2>Service address</h2>
                    <p>Search for your location or move the map pin to your property.</p>
                </div>
            </div>

            <div class="map-search-row">
                <input id="map-search"
                       type="search"
                       placeholder="Search an address, street, or landmark"
                       aria-label="Search map location">

                <button type="button"
                        class="subtle-button"
                        id="search-location">
                    Search
                </button>
            </div>

            <div class="map-toolbar">

                <button type="button"
                        class="subtle-button"
                        id="use-my-location">
                    ◎ Use my location
                </button>

                <button type="button"
                        class="subtle-button"
                        id="map-street-view">
                    Street map
                </button>

                <button type="button"
                        class="subtle-button"
                        id="map-satellite-view">
                    Satellite
                </button>

            </div>

            <div id="profile-map"
                 aria-label="Interactive service address map"></div>

            <p id="map-status" role="status" aria-live="polite">
                Loading map…
            </p>

            <p class="map-note">
                You can search, use your current location, or move the pin.
                A newly detected location will not change your saved address
                fields unless you choose to apply it.
            </p>

            <div class="address-summary">
                <span class="address-summary-icon" aria-hidden="true">📍</span>
                <div>
                    <strong>Detected map location</strong>
                    <span id="selected-address">
                        Move the pin or search for your address.
                    </span>
                </div>
            </div>

            <button type="button"
                    id="apply-detected-address"
                    class="apply-location-button"
                    disabled>
                <svg viewBox="0 0 24 24">
                    <path d="m5 12 4 4L19 6"></path>
                </svg>
                Use Detected Address
            </button>

            <div id="location-notice"
                 class="location-notice"
                 role="status"
                 aria-live="polite">
                Your current saved address will remain unchanged unless
                you click Use Detected Address.
            </div>

            <div class="profile-grid" style="margin-top: 20px;">

                <div class="profile-field">
                    <label for="house_no">House / building number</label>
                    <input type="text" id="house_no" name="house_no"
                           value="{{ old('house_no', $address?->house_no) }}"
                           autocomplete="address-line1">
                    @error('house_no')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="street">Street</label>
                    <input type="text" id="street" name="street"
                           value="{{ old('street', $address?->street) }}">
                    @error('street')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="purok">Purok / subdivision</label>
                    <input type="text" id="purok" name="purok"
                           value="{{ old('purok', $address?->purok) }}">
                    @error('purok')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="barangay">Barangay *</label>

                    @if (count($barangays))
                        <select id="barangay" name="barangay" required>
                            <option value="">Select barangay</option>

                            @foreach ($barangays as $key => $item)
                                @php
                                    $barangayValue = is_array($item)
                                        ? ($item['name'] ?? $item['label'] ?? $key)
                                        : $item;
                                @endphp

                                <option value="{{ $barangayValue }}"
                                    @selected(strcasecmp($currentBarangay, $barangayValue) === 0)>
                                    {{ $barangayValue }}
                                </option>
                            @endforeach
                        </select>
                    @else
                        <input type="text" id="barangay" name="barangay"
                               value="{{ $currentBarangay }}" required>
                    @endif

                    @error('barangay')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="municipality">City / municipality</label>
                    <input type="text" id="municipality" name="municipality"
                           value="{{ $municipality }}">
                    @error('municipality')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="profile-field">
                    <label for="province">Province</label>
                    <input type="text" id="province" name="province"
                           value="{{ $province }}">
                    @error('province')
                        <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </section>

        {{-- CHANGE PASSWORD --}}
        <section class="profile-card">

            <div class="section-heading">
                <div class="section-icon">
                    <svg viewBox="0 0 24 24">
                        <rect x="4" y="10" width="16" height="11" rx="2"></rect>
                        <path d="M8 10V7a4 4 0 1 1 8 0v3"></path>
                        <path d="M12 14v3"></path>
                    </svg>
                </div>
                <div>
                    <h2>Change password</h2>
                    <p>Leave these fields empty if you do not want to change your password.</p>
                </div>
            </div>

            <div class="profile-grid">

                @foreach ([
                    'current_password' => 'Current password',
                    'password' => 'New password',
                    'password_confirmation' => 'Confirm new password'
                ] as $field => $label)

                    <div class="profile-field {{ $field === 'current_password' ? 'full' : '' }}">
                        <label for="{{ $field }}">{{ $label }}</label>

                        <div class="password-wrap">
                            <input
                                type="password"
                                id="{{ $field }}"
                                name="{{ $field }}"
                                autocomplete="{{ $field === 'current_password' ? 'current-password' : 'new-password' }}"
                                form="password-form">

                            <button type="button"
                                    class="password-toggle"
                                    data-target="{{ $field }}"
                                    aria-label="Show {{ strtolower($label) }}"
                                    aria-pressed="false">

                                <svg class="icon-eye" viewBox="0 0 24 24">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                    <circle cx="12" cy="12" r="3"></circle>
                                </svg>

                                <svg class="icon-eye-off" viewBox="0 0 24 24">
                                    <path d="m3 3 18 18"></path>
                                    <path d="M10.6 10.6a2 2 0 0 0 2.8 2.8"></path>
                                    <path d="M9.9 5.2A10.8 10.8 0 0 1 12 5c6.5 0 10 7 10 7a16 16 0 0 1-3.1 3.9"></path>
                                    <path d="M6.2 6.2C3.5 8 2 12 2 12s3.5 7 10 7a10 10 0 0 0 4-.8"></path>
                                </svg>
                            </button>
                        </div>

                        @error($field)
                            <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>

                @endforeach

            </div>
        </section>

        <div class="profile-footer">
            <p>* Required fields. Your changes are saved to your consumer account.</p>

            <div class="profile-actions">

                <a href="{{ route('consumer.profile.show') }}"
                   class="cancel-button">
                    Cancel
                </a>

                <button type="submit"
                        id="save-changes"
                        class="save-button">

                    <span class="button-spinner" aria-hidden="true"></span>

                    <svg class="save-icon" viewBox="0 0 24 24">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2Z"></path>
                        <path d="M17 21v-8H7v8"></path>
                        <path d="M7 3v5h8"></path>
                    </svg>

                    <span class="button-label">Save Changes</span>
                </button>

            </div>
        </div>

    </form>

    {{-- Existing separate password endpoint --}}
    <form id="password-form"
          method="POST"
          action="{{ route('consumer.profile.password') }}"
          hidden>
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

    const applyButton = document.getElementById('apply-detected-address');
    const locationNotice = document.getElementById('location-notice');

    const profileForm = document.getElementById('profile-form');
    const passwordForm = document.getElementById('password-form');
    const saveButton = document.getElementById('save-changes');
    const saveButtonLabel = saveButton.querySelector('.button-label');

    const addressFields = {
        house_no: document.getElementById('house_no'),
        street: document.getElementById('street'),
        purok: document.getElementById('purok'),
        barangay: document.getElementById('barangay'),
        municipality: document.getElementById('municipality'),
        province: document.getElementById('province')
    };

    let isSubmitting = false;

    /*
     * The detected location is temporary.
     * It is not submitted until the user clicks Use Detected Address.
     */
    let pendingLocation = null;
    let reverseTimer = null;
    let reverseRequestId = 0;
    let searchRequestId = 0;

    /* -------------------------------------------------------
       PHONE NUMBER
    ------------------------------------------------------- */

    const phoneInput = document.getElementById('phone');

    function cleanPhoneNumber(value) {
        return value.replace(/\D/g, '').slice(0, 11);
    }

    phoneInput.value = cleanPhoneNumber(phoneInput.value);

    phoneInput.addEventListener('input', function () {
        phoneInput.value = cleanPhoneNumber(phoneInput.value);
        phoneInput.setCustomValidity('');
    });

    phoneInput.addEventListener('blur', function () {
        const value = phoneInput.value.trim();

        phoneInput.setCustomValidity(
            value && !/^09\d{9}$/.test(value)
                ? 'Enter a valid 11-digit Philippine mobile number starting with 09.'
                : ''
        );
    });

    /* -------------------------------------------------------
       PASSWORD EYE ICONS
    ------------------------------------------------------- */

    document.querySelectorAll('.password-toggle').forEach(function (button) {

        button.addEventListener('click', function () {

            const input = document.getElementById(button.dataset.target);

            if (!input) return;

            const showing = input.type === 'text';

            input.type = showing ? 'password' : 'text';
            button.classList.toggle('is-visible', !showing);
            button.setAttribute('aria-pressed', String(!showing));

        });

    });

    /* -------------------------------------------------------
       MAP
    ------------------------------------------------------- */

    const defaultLat = 10.8990;
    const defaultLng = 123.4140;

    const hasSavedCoords =
        latitudeInput.value !== '' &&
        longitudeInput.value !== '' &&
        Number.isFinite(Number(latitudeInput.value)) &&
        Number.isFinite(Number(longitudeInput.value));

    const initialLat = hasSavedCoords
        ? Number(latitudeInput.value)
        : defaultLat;

    const initialLng = hasSavedCoords
        ? Number(longitudeInput.value)
        : defaultLng;

    if (typeof L === 'undefined') {
        status.textContent =
            'The map could not be loaded. You can still edit your address manually.';
        return;
    }

    const map = L.map('profile-map', {
        scrollWheelZoom: true
    }).setView(
        [initialLat, initialLng],
        hasSavedCoords ? 17 : 13
    );

    const streetLayer = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        {
            maxZoom: 20,
            attribution: '© OpenStreetMap contributors'
        }
    );

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            maxZoom: 19,
            attribution: 'Tiles © Esri'
        }
    );

    streetLayer.addTo(map);

    const marker = L.marker(
        [initialLat, initialLng],
        { draggable: true }
    ).addTo(map);

    const manualFields = new Set();

    Object.entries(addressFields).forEach(function ([name, field]) {

        if (!field) return;

        field.addEventListener('input', function () {
            manualFields.add(name);
        });

        field.addEventListener('change', function () {
            manualFields.add(name);
        });

    });

    function setStatus(message) {
        status.textContent = message;
    }

    function setCoordinates(latlng) {
        latitudeInput.value = Number(latlng.lat).toFixed(7);
        longitudeInput.value = Number(latlng.lng).toFixed(7);
    }

    function setField(name, value) {

        const field = addressFields[name];

        if (
            !field ||
            value === undefined ||
            value === null ||
            value === ''
        ) {
            return;
        }

        if (field.tagName === 'SELECT') {

            const match = Array.from(field.options).find(option =>
                option.value.toLowerCase() === String(value).toLowerCase()
            );

            if (match) {
                field.value = match.value;
            }

        } else {

            field.value = value;

        }

        manualFields.add(name);
    }

    /*
     * Store detected data temporarily.
     * Do not change the form fields or hidden coordinates here.
     */
    function stageDetectedLocation(result, latlng) {

        const a = result.address || {};

        pendingLocation = {
            latitude: Number(latlng.lat),
            longitude: Number(latlng.lng),

            house_no: a.house_number || a.house_name || '',

            street:
                a.road ||
                a.residential ||
                a.pedestrian ||
                a.footway ||
                a.path ||
                a.street ||
                '',

            barangay:
                a.suburb ||
                a.neighbourhood ||
                a.quarter ||
                a.village ||
                a.hamlet ||
                a.city_district ||
                '',

            municipality:
                a.city ||
                a.town ||
                a.municipality ||
                '',

            province:
                a.state ||
                a.province ||
                '',

            display_name:
                result.display_name || 'Selected map location'
        };

        selectedAddress.textContent = pendingLocation.display_name;

        applyButton.disabled = false;

        locationNotice.textContent =
            'New location detected. Your saved address has not changed. Click Use Detected Address only if you want to apply this location.';

    }

    /*
     * Apply only when the user explicitly clicks the button.
     */
    function applyDetectedLocation() {

        if (!pendingLocation) {
            return;
        }

        setCoordinates({
            lat: pendingLocation.latitude,
            lng: pendingLocation.longitude
        });

        setField('house_no', pendingLocation.house_no);
        setField('street', pendingLocation.street);
        setField('barangay', pendingLocation.barangay);
        setField('municipality', pendingLocation.municipality);
        setField('province', pendingLocation.province);

        applyButton.disabled = true;

        locationNotice.textContent =
            'The detected location has been applied to the form. Review the address fields before saving.';

        setStatus('Detected location applied. Review your address before saving.');

        pendingLocation = null;
    }

    applyButton.addEventListener('click', function () {

        if (!pendingLocation) return;

        const confirmed = window.confirm(
            'Apply this newly detected location to your service address? Your current address fields and map coordinates will be replaced where a new value is available.'
        );

        if (!confirmed) return;

        applyDetectedLocation();

    });

    function movePin(latlng, zoom = 17) {

        marker.setLatLng(latlng);
        map.setView(latlng, zoom);

        pendingLocation = null;
        applyButton.disabled = true;

        selectedAddress.textContent = 'Checking the selected location…';

        locationNotice.textContent =
            'Checking the new location. Your saved address remains unchanged.';

        clearTimeout(reverseTimer);

        reverseTimer = setTimeout(function () {
            reverseGeocode(latlng);
        }, 650);

    }

    marker.on('dragend', function () {
        movePin(marker.getLatLng());
    });

    map.on('click', function (event) {
        movePin(event.latlng);
    });

    /* -------------------------------------------------------
       REVERSE GEOCODING
    ------------------------------------------------------- */

    async function reverseGeocode(latlng) {

        const requestId = ++reverseRequestId;

        setStatus('Finding the nearest address…');

        try {

            const url = new URL(
                'https://nominatim.openstreetmap.org/reverse'
            );

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

            if (!response.ok) {
                throw new Error('Address lookup failed.');
            }

            const result = await response.json();

            if (requestId !== reverseRequestId) return;

            if (result && result.address) {

                stageDetectedLocation(result, latlng);

                setStatus(
                    'Location detected. Click Use Detected Address if you want to apply it.'
                );

            } else {

                selectedAddress.textContent =
                    'No matching address found for this pin.';

                setStatus(
                    'No address details found. Your saved address remains unchanged.'
                );

            }

        } catch (error) {

            if (requestId !== reverseRequestId) return;

            selectedAddress.textContent =
                'Address lookup unavailable.';

            setStatus(
                'Could not retrieve the address. Your saved address remains unchanged.'
            );

        }
    }

    /* -------------------------------------------------------
       SEARCH LOCATION
    ------------------------------------------------------- */

    async function searchAddress() {

        const query = searchInput.value.trim();

        if (!query) {
            setStatus('Enter an address or landmark to search.');
            searchInput.focus();
            return;
        }

        setStatus('Searching for the best address match…');

        const requestId = ++searchRequestId;

        try {

            const url = new URL(
                'https://nominatim.openstreetmap.org/search'
            );

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

            if (!response.ok) {
                throw new Error('Search failed.');
            }

            const results = await response.json();

            if (requestId !== searchRequestId) return;

            if (!results.length) {
                setStatus(
                    'No results found. Try adding the barangay, city, or province.'
                );
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

            stageDetectedLocation(preferred, latlng);

            setStatus(
                'Search result selected. Review it, then click Use Detected Address if correct.'
            );

        } catch (error) {

            if (requestId !== searchRequestId) return;

            setStatus(
                'Search is temporarily unavailable. You can move the pin manually.'
            );

        }
    }

    document
        .getElementById('search-location')
        .addEventListener('click', searchAddress);

    searchInput.addEventListener('keydown', function (event) {

        if (event.key === 'Enter') {
            event.preventDefault();
            searchAddress();
        }

    });

    /* -------------------------------------------------------
       USE MY LOCATION
    ------------------------------------------------------- */

    document
        .getElementById('use-my-location')
        .addEventListener('click', function () {

            if (!navigator.geolocation) {
                setStatus(
                    'Your browser does not support location detection.'
                );
                return;
            }

            setStatus('Requesting your location…');

            navigator.geolocation.getCurrentPosition(

                function (position) {

                    const latlng = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    marker.setLatLng(latlng);
                    map.setView(latlng, 18);

                    pendingLocation = null;
                    applyButton.disabled = true;

                    reverseGeocode(latlng);

                },

                function () {

                    setStatus(
                        'Location access was unavailable. Allow location permission or search for your address.'
                    );

                },

                {
                    enableHighAccuracy: true,
                    timeout: 15000,
                    maximumAge: 0
                }

            );

        });

    /* -------------------------------------------------------
       MAP LAYERS
    ------------------------------------------------------- */

    document
        .getElementById('map-street-view')
        .addEventListener('click', function () {

            if (map.hasLayer(satelliteLayer)) {
                map.removeLayer(satelliteLayer);
            }

            if (!map.hasLayer(streetLayer)) {
                streetLayer.addTo(map);
            }

        });

    document
        .getElementById('map-satellite-view')
        .addEventListener('click', function () {

            if (map.hasLayer(streetLayer)) {
                map.removeLayer(streetLayer);
            }

            if (!map.hasLayer(satelliteLayer)) {
                satelliteLayer.addTo(map);
            }

        });

    /* -------------------------------------------------------
       SAVE CONFIRMATION + PASSWORD UPDATE
    ------------------------------------------------------- */

    profileForm.addEventListener('submit', async function (event) {

        event.preventDefault();

        if (isSubmitting) return;

        const phone = phoneInput.value.trim();

        if (!/^09\d{9}$/.test(phone)) {

            phoneInput.setCustomValidity(
                'Enter a valid 11-digit Philippine mobile number starting with 09.'
            );

            phoneInput.reportValidity();
            phoneInput.focus();

            return;
        }

        phoneInput.setCustomValidity('');

        if (!profileForm.reportValidity()) {
            return;
        }

        const confirmed = window.confirm(
            'Are you sure you want to save your profile changes? Please make sure your personal details and service address are correct.'
        );

        if (!confirmed) {
            return;
        }

        const currentPassword =
            document.getElementById('current_password').value;

        const newPassword =
            document.getElementById('password').value;

        const confirmation =
            document.getElementById('password_confirmation').value;

        const passwordRequested =
            currentPassword !== '' ||
            newPassword !== '' ||
            confirmation !== '';

        if (passwordRequested) {

            if (!currentPassword || !newPassword || !confirmation) {
                alert(
                    'To change your password, fill in the current password, new password, and confirmation.'
                );
                return;
            }

            if (newPassword !== confirmation) {
                alert(
                    'The new password and confirmation do not match.'
                );
                document.getElementById('password_confirmation').focus();
                return;
            }

        }

        isSubmitting = true;

        saveButton.disabled = true;
        saveButton.classList.add('is-loading');
        saveButtonLabel.textContent = 'Saving changes…';

        try {

            if (passwordRequested) {

                const passwordData = new FormData();

                passwordData.append(
                    '_token',
                    passwordForm.querySelector('[name="_token"]').value
                );

                passwordData.append('_method', 'PUT');
                passwordData.append('current_password', currentPassword);
                passwordData.append('password', newPassword);
                passwordData.append(
                    'password_confirmation',
                    confirmation
                );

                const passwordResponse = await fetch(
                    passwordForm.action,
                    {
                        method: 'POST',
                        body: passwordData,
                        headers: {
                            'Accept': 'application/json'
                        },
                        credentials: 'same-origin'
                    }
                );

                if (passwordResponse.status === 422) {

                    const errors = await passwordResponse.json();

                    const messages = Object.values(
                        errors.errors || {}
                    ).flat();

                    throw new Error(
                        messages.join('\n') ||
                        'Please check your password fields.'
                    );
                }

                if (!passwordResponse.ok) {
                    throw new Error(
                        'Your password could not be updated. Please try again.'
                    );
                }

                if (
                    passwordResponse.redirected &&
                    passwordResponse.url.includes('/login')
                ) {
                    throw new Error(
                        'Your session may have expired. Please sign in again.'
                    );
                }
            }

            /*
             * Native form submission preserves Laravel's normal
             * redirect and validation behavior.
             */
            profileForm.submit();

        } catch (error) {

            alert(
                error.message ||
                'Something went wrong. Please try again.'
            );

            isSubmitting = false;

            saveButton.disabled = false;
            saveButton.classList.remove('is-loading');
            saveButtonLabel.textContent = 'Save Changes';

        }

    });

    /* -------------------------------------------------------
       INITIAL MAP STATE
    ------------------------------------------------------- */

    setTimeout(function () {
        map.invalidateSize();
    }, 150);

    if (hasSavedCoords) {

        selectedAddress.textContent =
            'Your currently saved map location.';

        setStatus(
            'Saved location loaded. Move the pin only if you want to detect another location.'
        );

    } else {

        selectedAddress.textContent =
            'No saved map coordinates are available.';

        setStatus(
            'Choose a location by searching, using your location, or moving the pin.'
        );

    }

});
</script>
@endsection
