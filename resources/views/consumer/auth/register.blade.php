
@extends('consumer.auth.layout')

@section('title', 'Create Consumer Account')

@section('content')

    @php
        $steps = [
            ['Submit Information', 'Provide your service account and contact details.', true],
            ['Email OTP Verification', 'Enter the 6-digit code sent to your email to confirm ownership.', false],
            ['SWD Verification', 'Your registration is reviewed against SWD records.', false],
            ['Portal Activation', 'Once approved, your Consumer Portal access becomes active.', false],
        ];
    @endphp

    <div
        class="min-h-screen bg-slate-50"
        x-data="{
            loading: false,
            showPassword: false,
            showConfirmation: false,

            submit(e) {
                if (this.loading) {
                    e.preventDefault();
                    return;
                }

                const latitude = document.getElementById('registration_latitude')?.value;
                const longitude = document.getElementById('registration_longitude')?.value;

                if (!latitude || !longitude) {
                    e.preventDefault();
                    window.dispatchEvent(new CustomEvent('pin-required'));
                    return;
                }

                this.loading = true;
            }
        }"
        @pageshow.window="loading = false"
    >

        {{-- Top bar --}}
        <div class="border-b border-slate-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white">
                        <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo" class="h-9 w-9 object-contain">
                    </div>

                    <div>
                        <p class="text-xl font-semibold leading-none text-sky-700">iSWD</p>
                        <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.12em] text-slate-500">
                            Sagay Water District
                        </p>
                    </div>
                </a>

                <a href="{{ route('consumer.login') }}"
                    class="inline-flex items-center gap-2 rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 sm:px-4 sm:text-sm">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    <span class="hidden sm:inline">Consumer Login</span>
                    <span class="sm:hidden">Login</span>
                </a>

            </div>
        </div>

        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 lg:py-10">
            <div class="grid items-start gap-8 lg:grid-cols-[280px_1fr] xl:grid-cols-[320px_1fr]">

                {{-- Sidebar --}}
                <aside class="lg:sticky lg:top-6">

                    <div class="rounded-2xl bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 p-6 text-white shadow-lg">

                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-white/15">
                            <i class="fa-solid fa-user-plus"></i>
                        </div>

                        <h1 class="mt-5 text-2xl font-semibold tracking-tight">
                            Create Consumer Account
                        </h1>

                        <p class="mt-3 text-sm leading-6 text-sky-50/90">
                            Register your Sagay Water District service account to access online complaint submission and
                            tracking.
                        </p>

                        <div class="mt-6 border-t border-white/15 pt-5">

                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-sky-100">
                                Registration Process
                            </p>

                            <div class="mt-4 space-y-4">
                                @foreach ($steps as $i => $step)
                                    <div class="flex gap-3">
                                        <div
                                            class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full text-xs font-semibold
                                            {{ $step[2] ? 'bg-white text-sky-700' : 'bg-white/15 text-white' }}">
                                            {{ $i + 1 }}
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium">{{ $step[0] }}</p>
                                            <p class="mt-0.5 text-xs leading-5 text-sky-100/80">{{ $step[1] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>

                        <div class="mt-6 rounded-xl border border-white/15 bg-white/10 p-4">
                            <div class="flex items-start gap-3">
                                <i class="fa-solid fa-shield-halved mt-0.5 text-sky-100"></i>
                                <p class="text-xs leading-5 text-sky-50/90">
                                    Use information that matches the registered Sagay Water District service account.
                                </p>
                            </div>
                        </div>

                    </div>

                    <div class="mt-4 hidden rounded-xl border border-slate-200 bg-white p-4 lg:block">
                        <div class="flex items-start gap-3">
                            <i class="fa-solid fa-circle-info mt-0.5 text-sky-600"></i>

                            <div>
                                <p class="text-xs font-semibold text-slate-700">Already registered?</p>

                                <a href="{{ route('consumer.login') }}"
                                    class="mt-1 inline-flex items-center gap-1.5 text-xs font-semibold text-sky-700 hover:text-sky-800">
                                    Sign in to Consumer Portal
                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                </aside>

                {{-- Main --}}
                <div class="min-w-0">

                    {{-- Validation errors --}}
                    @if ($errors->any())
                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-red-800">
                                        Please review your registration information
                                    </p>

                                    <ul class="mt-2 space-y-1 text-xs leading-5 text-red-700">
                                        @foreach ($errors->all() as $error)
                                            <li class="flex items-start gap-2">
                                                <i class="fa-solid fa-angle-right mt-1 text-[9px]"></i>
                                                <span>{{ $error }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form
                        method="POST"
                        action="{{ route('consumer.register.store') }}"
                        @submit="submit($event)"
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/40"
                    >

                        @csrf

                        {{-- Water Service Account --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-droplet"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Water Service Account</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Enter the account number shown on your Sagay Water District bill.
                                    </p>
                                </div>
                            </div>

                            <div class="max-w-lg">
                                <label for="account_number" class="mb-1.5 block text-sm font-medium text-slate-700">
                                    Account Number <span class="text-red-500">* required</span>
                                </label>

                                <input
                                    id="account_number"
                                    type="text"
                                    name="account_number"
                                    value="{{ old('account_number') }}"
                                    required
                                    autocomplete="off"
                                    placeholder="Example: 14D-122-120"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('account_number') border-red-300 @enderror"
                                >

                                @error('account_number')
                                    <p class="mt-1.5 text-xs text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                <p class="mt-1.5 text-xs leading-5 text-slate-400">
                                    This account number will be checked during verification.
                                </p>
                            </div>

                        </div>

                        {{-- Personal Information --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Personal Information</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Enter the registered account holder's information.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                <div>
                                    <label for="first_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        First Name <span class="text-red-500">* required</span>
                                    </label>

                                    <input
                                        id="first_name"
                                        type="text"
                                        name="first_name"
                                        value="{{ old('first_name') }}"
                                        required
                                        autocomplete="given-name"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('first_name') border-red-300 @enderror"
                                    >

                                    @error('first_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Middle Name <span class="font-normal text-slate-400">(optional)</span>
                                    </label>

                                    <input
                                        id="middle_name"
                                        type="text"
                                        name="middle_name"
                                        value="{{ old('middle_name') }}"
                                        autocomplete="additional-name"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                    >
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Last Name <span class="text-red-500">* required</span>
                                    </label>

                                    <input
                                        id="last_name"
                                        type="text"
                                        name="last_name"
                                        value="{{ old('last_name') }}"
                                        required
                                        autocomplete="family-name"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('last_name') border-red-300 @enderror"
                                    >

                                    @error('last_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="suffix" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Suffix <span class="font-normal text-slate-400">(optional)</span>
                                    </label>

                                    <input
                                        id="suffix"
                                        type="text"
                                        name="suffix"
                                        value="{{ old('suffix') }}"
                                        placeholder="Jr., Sr., III"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                    >
                                </div>

                                <div>
                                    <label for="sex" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Sex <span class="text-red-500">* required</span>
                                    </label>

                                    <select
                                        id="sex"
                                        name="sex"
                                        required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('sex') border-red-300 @enderror"
                                    >
                                        <option value="">Select sex</option>
                                        <option value="Male" @selected(old('sex') === 'Male')>Male</option>
                                        <option value="Female" @selected(old('sex') === 'Female')>Female</option>
                                    </select>

                                    @error('sex')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                            </div>

                        </div>

                        {{-- Contact Information --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-address-book"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Contact Information</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Used for portal access and account-related communication.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>
                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Mobile Number <span class="text-red-500">* required</span>
                                    </label>

                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        value="{{ old('phone') }}"
                                        required
                                        inputmode="numeric"
                                        autocomplete="tel-national"
                                        maxlength="11"
                                        minlength="11"
                                        pattern="09[0-9]{9}"
                                        placeholder="09XXXXXXXXX"
                                        class="block w-full min-w-0 rounded-xl border border-slate-300 px-4 py-3 text-base font-medium tracking-wide tabular-nums text-slate-800 outline-none transition placeholder:font-normal placeholder:tracking-normal placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('phone') border-red-300 @enderror"
                                        aria-describedby="phone-help"
                                    >

                                    <p id="phone-help" class="mt-1.5 text-xs leading-5 text-slate-500">
                                        Enter exactly 11 digits starting with 09. Example: 09123456789.
                                    </p>

                                    @error('phone')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Email Address <span class="text-red-500">* required</span>
                                    </label>

                                    <input
                                        id="email"
                                        type="email"
                                        name="email"
                                        value="{{ old('email') }}"
                                        required
                                        autocomplete="email"
                                        placeholder="you@example.com"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('email') border-red-300 @enderror"
                                    >

                                    @error('email')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                    <p class="mt-1.5 text-xs leading-5 text-slate-400">
                                        A 6-digit verification code will be sent to this email.
                                    </p>
                                </div>

                            </div>

                        </div>

                        {{-- Service Address --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Service Address</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Pin the exact water service connection on the map. Address fields are filled
                                        automatically when information is available and remain editable.
                                    </p>
                                </div>
                            </div>

                            {{-- Registered Water Service Location --}}
                            <div class="mb-7">

                                <div class="mb-4">
                                    <h3 class="text-sm font-semibold text-slate-900">
                                        Registered Water Service Location
                                        <span class="text-red-500">* required</span>
                                    </h3>

                                    <p class="mt-1 max-w-3xl text-xs leading-5 text-slate-500">
                                        Search your street, purok, or landmark, then click the map or drag the pin onto
                                        your house or water meter. Switch to <strong>Satellite</strong> to see rooftops.
                                        The pin coordinates are used for location-based complaint matching.
                                    </p>
                                </div>

                                {{-- Address search --}}
                                <div class="relative mb-3">
                                    <div class="flex gap-2">
                                        <div class="relative min-w-0 flex-1">
                                            <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>

                                            <input
                                                type="text"
                                                id="map-search"
                                                autocomplete="off"
                                                placeholder="Search street, purok, or landmark in Sagay City"
                                                class="w-full rounded-xl border border-slate-300 py-2.5 pl-9 pr-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                            >
                                        </div>

                                        <button
                                            type="button"
                                            id="map-search-btn"
                                            class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:opacity-60"
                                        >
                                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                            <span class="hidden sm:inline">Search</span>
                                        </button>
                                    </div>

                                    <div
                                        id="map-search-results"
                                        class="absolute left-0 right-0 top-full z-[1000] mt-1 hidden max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg"
                                    ></div>
                                </div>

                                {{-- Map --}}
                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
                                    <div id="registration-map" class="h-[380px] w-full sm:h-[430px] lg:h-[500px]"></div>
                                </div>

                                {{-- Use My Location --}}
                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <button
                                        type="button"
                                        id="use-registration-location"
                                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-60"
                                    >
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                        Use My Location
                                    </button>

                                    <p id="registration-location-status" class="text-xs leading-5 text-slate-500">
                                        Search above, click the map, or use your current location to place the pin.
                                    </p>
                                </div>
                                {{-- Messenger Browser Guidance --}}
                                <div class="mt-3 rounded-xl border border-sky-200 bg-sky-50 p-3.5">
                                    <div class="flex items-start gap-3">
                                        <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                                            <i class="fa-solid fa-mobile-screen-button"></i>
                                        </div>

                                        <div>
                                            <p class="text-xs font-semibold text-sky-900">
                                                Having trouble detecting your location?
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-sky-800">
                                                If you opened this page through Messenger, tap the three-dot menu
                                                and select <strong>Open in Chrome</strong> or <strong>Open in Browser</strong>.
                                                This helps ensure your browser can access your device's location.
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <p class="mt-2 text-[11px] leading-5 text-slate-400">
                                    Tip: "Use My Location" is most accurate on a phone with GPS. Laptops and desktops
                                    estimate location from Wi-Fi or internet and can be off by a kilometer or more.
                                    You can also search or drag the pin manually.
                                </p>

                                {{-- Address detection failure --}}
                                <div
                                    id="address-detection-error"
                                    class="mt-4 hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800"
                                >
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    The pin was saved, but the readable address could not be detected. Please enter
                                    the address manually.
                                </div>

                                <input
                                    type="hidden"
                                    name="latitude"
                                    id="registration_latitude"
                                    value="{{ old('latitude') }}"
                                >

                                <input
                                    type="hidden"
                                    name="longitude"
                                    id="registration_longitude"
                                    value="{{ old('longitude') }}"
                                >

                                @error('latitude')
                                    <p class="mt-2 text-xs text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                                @error('longitude')
                                    <p class="mt-2 text-xs text-red-600">
                                        <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                            {{-- Address fields --}}
                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                <div>
                                    <label for="house_no" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        House / Building No.
                                        <span class="font-normal text-slate-400">(optional)</span>
                                    </label>

                                    <input
                                        id="house_no"
                                        type="text"
                                        name="house_no"
                                        value="{{ old('house_no') }}"
                                        autocomplete="address-line1"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                    >
                                </div>

                                <div class="sm:col-span-2 xl:col-span-3">
                                    <div class="mb-3">
                                        <p class="text-sm font-semibold text-slate-700">
                                            Street / Purok <span class="text-red-500">*</span>
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            Enter at least one — Street or Purok — based on your service address.
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

                                        {{-- Street --}}
                                        <div>
                                            <label for="street" class="mb-1.5 block text-sm font-medium text-slate-700">
                                                Street
                                            </label>

                                            <input
                                                type="text"
                                                id="street"
                                                name="street"
                                                value="{{ old('street') }}"
                                                placeholder="e.g. Rizal Street"
                                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                            >

                                            @error('street')
                                                <p class="mt-1.5 text-xs text-red-600">
                                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        {{-- Purok --}}
                                        <div>
                                            <label for="purok" class="mb-1.5 block text-sm font-medium text-slate-700">
                                                Purok
                                            </label>

                                            <input
                                                type="text"
                                                id="purok"
                                                name="purok"
                                                value="{{ old('purok') }}"
                                                placeholder="e.g. Purok 3"
                                                class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                            >

                                            @error('purok')
                                                <p class="mt-1.5 text-xs text-red-600">
                                                    <i class="fa-solid fa-circle-exclamation mr-1"></i>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                    </div>
                                </div>

                                {{-- Barangay --}}
                                <div>
                                    <label for="barangay" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Barangay <span class="text-red-500">* required</span>
                                    </label>

                                    <select
                                        id="barangay"
                                        name="barangay"
                                        required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('barangay') border-red-300 @enderror"
                                    >
                                        <option value="">Select barangay</option>

                                        @foreach (config('sagay.barangays') as $barangay)
                                            <option value="{{ $barangay }}" @selected(old('barangay') === $barangay)>
                                                {{ $barangay }}
                                            </option>
                                        @endforeach
                                    </select>

                                    <p id="barangay-mismatch" class="mt-1.5 hidden text-xs leading-5 text-amber-600">
                                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                        The pin appears to be in a different barangay. Please double-check.
                                    </p>

                                    @error('barangay')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Municipality --}}
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Municipality / City
                                    </label>

                                    <div class="flex min-h-[42px] w-full items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500">
                                        <i class="fa-solid fa-location-dot text-xs text-slate-400"></i>
                                        Sagay City
                                    </div>
                                </div>

                                {{-- Province --}}
                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Province
                                    </label>

                                    <div class="flex min-h-[42px] w-full items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500">
                                        <i class="fa-solid fa-map text-xs text-slate-400"></i>
                                        Negros Occidental
                                    </div>
                                </div>

                            </div>

                            <p class="mt-4 text-xs leading-5 text-slate-400">
                                House No. is optional. Enter either a Street or Purok. SWD locates your connection
                                using the map pin and barangay.
                            </p>

                        </div>

                        {{-- Account Security --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-lock"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Account Security</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Create the password you will use after account approval.
                                    </p>
                                </div>
                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>
                                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Password <span class="text-red-500">* required</span>
                                    </label>

                                    <div class="relative">
                                        <input
                                            id="password"
                                            :type="showPassword ? 'text' : 'password'"
                                            name="password"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Minimum 8 characters"
                                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('password') border-red-300 @enderror"
                                        >

                                        <button
                                            type="button"
                                            @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                            :aria-label="showPassword ? 'Hide password' : 'Show password'"
                                        >
                                            <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>

                                    <p class="mt-1.5 text-xs leading-5 text-slate-400">
                                        At least 8 characters with uppercase, lowercase, and a number.
                                    </p>

                                    @error('password')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Confirm Password <span class="text-red-500">* required</span>
                                    </label>

                                    <div class="relative">
                                        <input
                                            id="password_confirmation"
                                            :type="showConfirmation ? 'text' : 'password'"
                                            name="password_confirmation"
                                            required
                                            autocomplete="new-password"
                                            placeholder="Repeat your password"
                                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
                                        >

                                        <button
                                            type="button"
                                            @click="showConfirmation = !showConfirmation"
                                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                            :aria-label="showConfirmation ? 'Hide password' : 'Show password'"
                                        >
                                            <i class="fa-solid text-sm" :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- Verification notice + submit --}}
                        <div class="p-5 sm:p-6">

                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
                                        <i class="fa-solid fa-shield-halved"></i>
                                    </div>

                                    <div>
                                        <p class="text-sm font-semibold text-amber-900">
                                            Account Verification Required
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-amber-800">
                                            A verification code will be sent to your email after you submit. Once your
                                            email is confirmed, your registration will be reviewed by Sagay Water District.
                                            Portal access remains inactive until your consumer information has been
                                            verified and approved.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <label class="mt-5 flex cursor-pointer items-start gap-3">
                                <input
                                    type="checkbox"
                                    name="terms"
                                    value="1"
                                    required
                                    @checked(old('terms'))
                                    class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500"
                                >

                                <span class="text-xs leading-5 text-slate-600 sm:text-sm sm:leading-6">
                                    I certify that the information provided is accurate and belongs to the registered
                                    Sagay Water District service account. I understand that my registration must be
                                    verified before my online account can be activated.
                                </span>
                            </label>

                            @error('terms')
                                <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                            @enderror

                            <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-between">

                                <a
                                    href="{{ route('consumer.login') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800"
                                >
                                    <i class="fa-solid fa-arrow-left text-xs"></i>
                                    Back to Login
                                </a>

                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="inline-flex min-h-[46px] items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70"
                                >

                                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Submit Registration
                                    </span>

                                    <span x-cloak x-show="loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-spinner animate-spin"></i>
                                        Submitting registration...
                                    </span>

                                </button>

                            </div>

                        </div>

                    </form>

                    <p class="mt-5 text-center text-[11px] text-slate-400">
                        &copy; {{ date('Y') }} Sagay Water District &middot; iSWD Consumer Portal
                    </p>

                </div>

            </div>
        </div>

    </div>

    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const $ = id => document.getElementById(id);

                const mapElement = $('registration-map');
                const latitudeInput = $('registration_latitude');
                const longitudeInput = $('registration_longitude');

                const locateButton = $('use-registration-location');
                const statusText = $('registration-location-status');

                const houseInput = $('house_no');
                const streetInput = $('street');
                const purokInput = $('purok');
                const barangayInput = $('barangay');

                const detectionError = $('address-detection-error');
                const mismatchHint = $('barangay-mismatch');

                const searchInput = $('map-search');
                const searchButton = $('map-search-btn');
                const searchResults = $('map-search-results');

                const phoneInput = $('phone');

                if (!mapElement || !latitudeInput || !longitudeInput || typeof L === 'undefined') {
                    return;
                }

                /* ---------------- Phone number input ---------------- */

                phoneInput?.addEventListener('input', function () {
                    this.value = this.value.replace(/\D/g, '').slice(0, 11);
                    this.setCustomValidity('');
                });

                phoneInput?.addEventListener('blur', function () {
                    if (this.value && !/^09\d{9}$/.test(this.value)) {
                        this.setCustomValidity(
                            'Enter exactly 11 digits starting with 09.'
                        );
                    } else {
                        this.setCustomValidity('');
                    }
                });

                /* ---------------- Configuration ---------------- */

                const BARANGAYS = @json(config('sagay.barangays'));
                const RAW_ALIASES = @json(config('sagay.barangay_aliases', []));
                const BOUNDARY_URL = @json(asset('geo/sagay-barangays.geojson'));

                const SAGAY_BOUNDS = L.latLngBounds(
                    [10.78, 123.28],
                    [11.12, 123.62]
                );

                // Approximate center of Sagay City proper.
                const DEFAULT_CENTER = [10.8990, 123.4140];

                /* ---------------- Map + layers ---------------- */

                const savedLat = parseFloat(latitudeInput.value);
                const savedLng = parseFloat(longitudeInput.value);

                const hasSavedLocation =
                    !Number.isNaN(savedLat) &&
                    !Number.isNaN(savedLng);

                const streetLayer = L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                );

                const satelliteLayer = L.layerGroup([
                    L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                        {
                            maxZoom: 19,
                            attribution: 'Imagery &copy; Esri'
                        }
                    ),

                    L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
                        {
                            maxZoom: 19
                        }
                    )
                ]);

                const map = L.map('registration-map', {
                    zoomControl: true,
                    layers: [streetLayer],
                    minZoom: 10,
                    maxBounds: SAGAY_BOUNDS.pad(0.6),
                    maxBoundsViscosity: 0.7
                }).setView(
                    hasSavedLocation ? [savedLat, savedLng] : DEFAULT_CENTER,
                    hasSavedLocation ? 17 : 14
                );

                L.control.layers(
                    {
                        'Street': streetLayer,
                        'Satellite': satelliteLayer
                    },
                    null,
                    {
                        position: 'topright',
                        collapsed: false
                    }
                ).addTo(map);

                let marker = null;
                let accuracyCircle = null;
                let detectedAddress = null;

                /* ---------------- Status helper ---------------- */

                function setStatus(message, tone) {
                    if (!statusText) return;

                    statusText.textContent = message;

                    statusText.className =
                        'text-xs leading-5 ' + ({
                            warn: 'text-amber-600',
                            error: 'text-red-600',
                            ok: 'text-emerald-600',
                            info: 'text-sky-600'
                        }[tone] || 'text-slate-500');
                }

                /* ---------------- Barangay matching ---------------- */

                const norm = s => (s || '')
                    .toLowerCase()
                    .replace(/\b(barangay|brgy\.?|bgy\.?)\b/g, ' ')
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim();

                const BARANGAY_INDEX = BARANGAYS
                    .map(name => ({
                        name,
                        key: norm(name)
                    }))
                    .sort((a, b) => b.key.length - a.key.length);

                const ALIASES = {
                    'poblacion 1': 'Poblacion I',
                    'poblacion i': 'Poblacion I',
                    'i pob': 'Poblacion I',
                    '1 pob': 'Poblacion I',

                    'poblacion 2': 'Poblacion II',
                    'poblacion ii': 'Poblacion II',
                    'ii pob': 'Poblacion II',
                    '2 pob': 'Poblacion II',

                    'bonifacio': 'Andres Bonifacio'
                };

                Object.keys(RAW_ALIASES || {}).forEach(k => {
                    if (BARANGAYS.includes(RAW_ALIASES[k])) {
                        ALIASES[norm(k)] = RAW_ALIASES[k];
                    }
                });

                function phraseMatch(text) {
                    const t = ' ' + norm(text) + ' ';

                    if (t.trim() === '') return '';

                    for (const b of BARANGAY_INDEX) {
                        if (b.key.length >= 4 && t.includes(' ' + b.key + ' ')) {
                            return b.name;
                        }
                    }

                    for (const key in ALIASES) {
                        if (key.length >= 4 && t.includes(' ' + key + ' ')) {
                            return ALIASES[key];
                        }
                    }

                    return '';
                }

                function exactMatch(raw) {
                    const key = norm(raw);

                    if (!key) return '';

                    const exact = BARANGAY_INDEX.find(b => b.key === key);

                    if (exact) return exact.name;

                    return ALIASES[key] || '';
                }

                function firstValue(...values) {
                    return values.find(v =>
                        typeof v === 'string' && v.trim() !== ''
                    ) || '';
                }

                /* ---------------- Barangay boundaries ---------------- */

                let boundaryFeatures = null;

                fetch(BOUNDARY_URL)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Barangay boundary file unavailable.');
                        }

                        return response.json();
                    })
                    .then(json => {
                        if (
                            json &&
                            Array.isArray(json.features) &&
                            json.features.length
                        ) {
                            boundaryFeatures = json.features;

                            if (marker) {
                                const point = marker.getLatLng();
                                reverseGeocode(point.lat, point.lng);
                            }
                        }
                    })
                    .catch(error => {
                        console.warn('Barangay boundary unavailable:', error);
                    });

                function pointInRing(lng, lat, ring) {
                    let inside = false;

                    for (
                        let i = 0, j = ring.length - 1;
                        i < ring.length;
                        j = i++
                    ) {
                        const xi = ring[i][0];
                        const yi = ring[i][1];
                        const xj = ring[j][0];
                        const yj = ring[j][1];

                        if (
                            (yi > lat) !== (yj > lat) &&
                            lng < (xj - xi) * (lat - yi) / (yj - yi) + xi
                        ) {
                            inside = !inside;
                        }
                    }

                    return inside;
                }

                function pointInPolygon(lng, lat, rings) {
                    if (!rings.length || !pointInRing(lng, lat, rings[0])) {
                        return false;
                    }

                    for (let i = 1; i < rings.length; i++) {
                        if (pointInRing(lng, lat, rings[i])) {
                            return false;
                        }
                    }

                    return true;
                }

                function boundaryLookup(lat, lng) {
                    if (!boundaryFeatures) return null;

                    for (const feature of boundaryFeatures) {
                        const geometry = feature.geometry;

                        if (!geometry) continue;

                        const polygons =
                            geometry.type === 'Polygon'
                                ? [geometry.coordinates]
                                : geometry.type === 'MultiPolygon'
                                    ? geometry.coordinates
                                    : [];

                        if (polygons.some(polygon =>
                            pointInPolygon(lng, lat, polygon)
                        )) {
                            const values = Object.values(
                                feature.properties || {}
                            ).filter(value => typeof value === 'string');

                            for (const value of values) {
                                const match =
                                    exactMatch(value) ||
                                    phraseMatch(value);

                                if (match) {
                                    return {
                                        inside: true,
                                        barangay: match
                                    };
                                }
                            }

                            return {
                                inside: true,
                                barangay: ''
                            };
                        }
                    }

                    return {
                        inside: false,
                        barangay: ''
                    };
                }

                /* ---------------- Address detection ---------------- */

                function buildDetectedAddress(data, lat, lng) {
                    const address = data?.address || {};
                    const displayName = data?.display_name || '';

                    let barangay = '';
                    let source = '';

                    // 1. Barangay polygon boundary lookup.
                    const boundary = boundaryLookup(lat, lng);

                    if (boundary && boundary.barangay) {
                        barangay = boundary.barangay;
                        source = 'boundary';
                    }

                    // 2. OpenStreetMap address fields.
                    if (!barangay) {
                        const fields = [
                            address.suburb,
                            address.village,
                            address.hamlet,
                            address.neighbourhood,
                            address.quarter,
                            address.city_district,
                            address.residential
                        ];

                        for (const field of fields) {
                            const match =
                                exactMatch(field) ||
                                phraseMatch(field);

                            if (match) {
                                barangay = match;
                                source = 'osm';
                                break;
                            }
                        }
                    }

                    // 3. Fallback guess from the full display name.
                    if (!barangay && displayName) {
                        for (const token of displayName.split(',')) {
                            const match =
                                exactMatch(token) ||
                                phraseMatch(token);

                            if (match) {
                                barangay = match;
                                source = 'guess';
                                break;
                            }
                        }
                    }

                    const purokRaw = firstValue(
                        address.neighbourhood,
                        address.quarter,
                        address.hamlet
                    );

                    return {
                        houseNo: firstValue(
                            address.house_number,
                            address.building
                        ),

                        street: firstValue(
                            address.road,
                            address.pedestrian,
                            address.path,
                            address.footway
                        ),

                        purok: /purok|sitio|zone/i.test(purokRaw)
                            ? purokRaw
                            : '',

                        barangay,
                        source,

                        outsideSagay: boundary
                            ? boundary.inside === false
                            : false,

                        displayName
                    };
                }

                /* ---------------- Auto-fill handling ---------------- */

                [houseInput, streetInput, purokInput].forEach(element => {
                    element?.addEventListener('input', () => {
                        element.dataset.auto = '0';
                    });
                });

                barangayInput?.addEventListener('change', () => {
                    barangayInput.dataset.auto = '0';
                    checkMismatch();
                });

                function fillField(element, value) {
                    if (!element) return;

                    const isEmpty = element.value.trim() === '';
                    const wasAuto = element.dataset.auto === '1';

                    if (isEmpty || wasAuto) {
                        element.value = value || '';
                        element.dataset.auto = value ? '1' : '0';
                    }
                }

                function applyDetected(result) {
                    fillField(houseInput, result.houseNo);
                    fillField(streetInput, result.street);
                    fillField(purokInput, result.purok);

                    if (barangayInput && result.barangay) {
                        fillField(barangayInput, result.barangay);
                    }

                    checkMismatch();
                }

                function checkMismatch() {
                    if (!mismatchHint) return;

                    const show =
                        detectedAddress?.barangay &&
                        detectedAddress.source !== 'guess' &&
                        barangayInput?.value &&
                        detectedAddress.barangay !== barangayInput.value;

                    mismatchHint.classList.toggle('hidden', !show);
                }

                /* ---------------- Automatic detection status ---------------- */

                function showDetectionLoading() {
                    detectionError?.classList.add('hidden');

                    setStatus(
                        'Detecting the address from your selected map location...',
                        'info'
                    );
                }

                function showDetectedAddress(result) {
                    detectedAddress = result;

                    // Automatically fill fields without overwriting manual entries.
                    applyDetected(result);

                    if (result.outsideSagay) {
                        setStatus(
                            'This pin is outside Sagay City boundaries. Please place it on your water service connection.',
                            'warn'
                        );

                        return;
                    }

                    if (!result.barangay) {
                        setStatus(
                            'Address detected where available. Please select your barangay from the dropdown.',
                            'warn'
                        );

                        return;
                    }

                    if (result.source === 'guess') {
                        setStatus(
                            'Possible barangay detected: ' + result.barangay +
                            '. Please confirm the barangay selection.',
                            'warn'
                        );

                        return;
                    }

                    setStatus(
                        'Address automatically detected. Please review the address fields and barangay.',
                        'ok'
                    );
                }

                function showDetectionFailure() {
                    detectedAddress = null;

                    detectionError?.classList.remove('hidden');
                    mismatchHint?.classList.add('hidden');

                    setStatus(
                        'The pin was saved, but the readable address could not be detected. Please enter the address manually.',
                        'warn'
                    );
                }

                /* ---------------- Reverse geocoding ---------------- */

                let geocodeTimer = null;
                let geocodeController = null;

                function reverseGeocode(lat, lng) {
                    clearTimeout(geocodeTimer);

                    showDetectionLoading();

                    geocodeTimer = setTimeout(() => {
                        runReverseGeocode(lat, lng);
                    }, 450);
                }

                async function runReverseGeocode(lat, lng) {
                    geocodeController?.abort();

                    geocodeController = new AbortController();

                    const controller = geocodeController;

                    try {
                        const url =
                            'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2&zoom=18&addressdetails=1&accept-language=en' +
                            '&lat=' + encodeURIComponent(lat) +
                            '&lon=' + encodeURIComponent(lng);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: controller.signal
                        });

                        if (!response.ok) {
                            throw new Error('Reverse geocoding request failed.');
                        }

                        const data = await response.json();

                        if (data.error) {
                            throw new Error(data.error);
                        }

                        showDetectedAddress(
                            buildDetectedAddress(data, lat, lng)
                        );

                    } catch (error) {
                        if (error.name === 'AbortError') return;

                        console.error('Reverse geocoding error:', error);

                        // Boundary data can still provide the barangay.
                        const boundary = boundaryLookup(lat, lng);

                        if (boundary && boundary.barangay) {
                            showDetectedAddress({
                                houseNo: '',
                                street: '',
                                purok: '',
                                barangay: boundary.barangay,
                                source: 'boundary',
                                outsideSagay: false,
                                displayName: ''
                            });
                        } else {
                            showDetectionFailure();
                        }
                    }
                }

                /* ---------------- Pin placement ---------------- */

                function setLocation(latitude, longitude, options = {}) {
                    const lat = Number(latitude);
                    const lng = Number(longitude);

                    if (Number.isNaN(lat) || Number.isNaN(lng)) {
                        return;
                    }

                    latitudeInput.value = lat.toFixed(7);
                    longitudeInput.value = lng.toFixed(7);

                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng], {
                            draggable: true
                        }).addTo(map);

                        marker.on('dragend', function (event) {
                            const point = event.target.getLatLng();

                            setLocation(
                                point.lat,
                                point.lng,
                                {
                                    center: false,
                                    detectAddress: true
                                }
                            );
                        });
                    }

                    if (accuracyCircle) {
                        map.removeLayer(accuracyCircle);
                        accuracyCircle = null;
                    }

                    if (options.accuracy) {
                        accuracyCircle = L.circle([lat, lng], {
                            radius: options.accuracy,
                            color: '#0369a1',
                            weight: 1,
                            fillOpacity: 0.12
                        }).addTo(map);
                    }

                    if (options.center !== false) {
                        map.setView(
                            [lat, lng],
                            options.zoom || Math.max(map.getZoom(), 18)
                        );
                    }

                    if (!SAGAY_BOUNDS.contains([lat, lng])) {
                        setStatus(
                            'This pin looks to be outside Sagay City. Please make sure it is on your water service connection.',
                            'warn'
                        );
                    } else if (!options.keepStatus) {
                        setStatus(
                            'Service location selected. Drag the pin anytime to fine-tune it.',
                            'ok'
                        );
                    }

                    if (options.detectAddress !== false) {
                        reverseGeocode(lat, lng);
                    }
                }

                // Restore previously submitted pin after validation errors.
                if (hasSavedLocation) {
                    setLocation(savedLat, savedLng, {
                        center: false,
                        detectAddress: true
                    });
                }

                // Manual map click.
                map.on('click', event => {
                    setLocation(
                        event.latlng.lat,
                        event.latlng.lng,
                        {
                            center: false,
                            detectAddress: true
                        }
                    );
                });

                /* ---------------- Address / landmark search ---------------- */

                function hideResults() {
                    searchResults?.classList.add('hidden');

                    if (searchResults) {
                        searchResults.innerHTML = '';
                    }
                }

                function renderResults(items) {
                    searchResults.innerHTML = '';

                    if (!items.length) {
                        const empty = document.createElement('div');

                        empty.className = 'px-4 py-3 text-xs text-slate-500';

                        empty.textContent =
                            'No matches in Sagay City. Try another spelling, or click the map to place the pin.';

                        searchResults.appendChild(empty);
                        searchResults.classList.remove('hidden');

                        return;
                    }

                    items.forEach(item => {
                        const button = document.createElement('button');

                        button.type = 'button';

                        button.className =
                            'block w-full border-b border-slate-100 px-4 py-2.5 text-left text-xs leading-5 text-slate-700 transition last:border-0 hover:bg-sky-50';

                        button.textContent = item.display_name;

                        button.addEventListener('click', () => {
                            hideResults();

                            searchInput.value = item.display_name
                                .split(',')
                                .slice(0, 2)
                                .join(',')
                                .trim();

                            setLocation(item.lat, item.lon, {
                                center: true,
                                zoom: 18,
                                detectAddress: true
                            });
                        });

                        searchResults.appendChild(button);
                    });

                    searchResults.classList.remove('hidden');
                }

                async function runSearch() {
                    const query = (searchInput?.value || '').trim();

                    if (query.length < 3) {
                        setStatus(
                            'Type at least 3 characters to search.',
                            'warn'
                        );

                        return;
                    }

                    searchButton.disabled = true;

                    const originalSearchHtml = searchButton.innerHTML;

                    searchButton.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i>' +
                        '<span class="hidden sm:inline">Searching...</span>';

                    try {
                        const url =
                            'https://nominatim.openstreetmap.org/search' +
                            '?format=jsonv2&limit=6&countrycodes=ph&accept-language=en&bounded=1' +
                            '&viewbox=123.28,11.12,123.62,10.78' +
                            '&q=' + encodeURIComponent(query);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Search failed.');
                        }

                        renderResults(await response.json());

                    } catch (error) {
                        console.error('Search error:', error);

                        setStatus(
                            'Search is unavailable right now. Please click the map to place the pin.',
                            'error'
                        );

                    } finally {
                        searchButton.disabled = false;
                        searchButton.innerHTML = originalSearchHtml;
                    }
                }

                searchButton?.addEventListener('click', runSearch);

                searchInput?.addEventListener('keydown', event => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        runSearch();
                    }

                    if (event.key === 'Escape') {
                        hideResults();
                    }
                });

                document.addEventListener('click', event => {
                    if (
                        !searchResults?.contains(event.target) &&
                        event.target !== searchInput
                    ) {
                        hideResults();
                    }
                });

                /* ---------------- Use My Location ---------------- */

                locateButton?.addEventListener('click', function () {
                    if (!navigator.geolocation) {
                        setStatus(
                            'Location services are not supported by your browser. Please search or place the pin manually.',
                            'error'
                        );

                        return;
                    }

                    const button = this;
                    const originalHtml = button.innerHTML;

                    let best = null;
                    let watchId = null;
                    let timer = null;
                    let finished = false;

                    button.disabled = true;

                    button.innerHTML =
                        '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';

                    setStatus(
                        'Getting a precise GPS fix. This can take a few seconds...',
                        'info'
                    );

                    function finish() {
                        if (finished) return;

                        finished = true;

                        if (watchId !== null) {
                            navigator.geolocation.clearWatch(watchId);
                        }

                        clearTimeout(timer);

                        watchId = null;

                        button.disabled = false;
                        button.innerHTML = originalHtml;

                        if (!best) {
                            setStatus(
                                'Could not get your location. Please search or click the map to place the pin.',
                                'error'
                            );

                            return;
                        }

                        const accuracy = Math.round(best.coords.accuracy);

                        setLocation(
                            best.coords.latitude,
                            best.coords.longitude,
                            {
                                center: true,
                                zoom: accuracy > 500 ? 16 : 18,
                                accuracy: best.coords.accuracy,
                                detectAddress: true,
                                keepStatus: true
                            }
                        );

                        if (accuracy > 100) {
                            setStatus(
                                'Location is only accurate to about ' + accuracy +
                                ' m. Use search, or switch to Satellite and drag the pin onto your property.',
                                'warn'
                            );
                        } else {
                            setStatus(
                                'Location found (accurate to about ' + accuracy +
                                ' m). Drag the pin if the water service is at a different spot.',
                                'ok'
                            );
                        }
                    }

                    watchId = navigator.geolocation.watchPosition(
                        function (position) {
                            if (
                                !best ||
                                position.coords.accuracy < best.coords.accuracy
                            ) {
                                best = position;

                                // Live preview while GPS accuracy improves.
                                setLocation(
                                    position.coords.latitude,
                                    position.coords.longitude,
                                    {
                                        center: true,
                                        zoom: position.coords.accuracy > 500 ? 16 : 18,
                                        accuracy: position.coords.accuracy,
                                        detectAddress: false,
                                        keepStatus: true
                                    }
                                );
                            }

                            if (position.coords.accuracy <= 25) {
                                finish();
                            }
                        },

                        function (error) {
                            console.error('Geolocation error:', error);

                            if (best) {
                                finish();
                                return;
                            }

                            if (finished) return;

                            finished = true;

                            if (watchId !== null) {
                                navigator.geolocation.clearWatch(watchId);
                            }

                            clearTimeout(timer);

                            button.disabled = false;
                            button.innerHTML = originalHtml;

                            const messages = {
                                1: 'Location permission is blocked. Allow location access for this site, then try again, or search / click the map to place the pin.',
                                2: 'Your device could not determine its location. Please search or click the map to place the pin.',
                                3: 'Location detection timed out. Try again, or search / click the map to place the pin.'
                            };

                            setStatus(
                                messages[error.code] ||
                                'Unable to get your location. Please search or click the map to place the pin.',
                                'error'
                            );
                        },

                        {
                            enableHighAccuracy: true,
                            timeout: 20000,
                            maximumAge: 0
                        }
                    );

                    // Use the best available fix after 12 seconds.
                    timer = setTimeout(finish, 12000);
                });

                /* ---------------- Pin required on submit ---------------- */

                window.addEventListener('pin-required', function () {
                    setStatus(
                        'Please place the service-location pin on the map before submitting.',
                        'error'
                    );

                    mapElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });

                /* ---------------- Map display correction ---------------- */

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        map.invalidateSize();
                    });
                });

                window.addEventListener('resize', () => {
                    map.invalidateSize();
                });

            });
        </script>
    @endpush

@endsection
