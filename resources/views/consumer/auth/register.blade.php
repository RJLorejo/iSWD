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

    <div class="min-h-screen bg-slate-50" x-data="{
        loading: false,
        showPassword: false,
        showConfirmation: false,
        submit(e) {
            if (!document.getElementById('registration_latitude').value) {
                e.preventDefault();
                window.dispatchEvent(new CustomEvent('pin-required'));
                return;
            }
            this.loading = true;
        }
    }">

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

                    <form method="POST" action="{{ route('consumer.register.store') }}" @submit="submit($event)"
                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/40">

                        @csrf

                        {{-- Water Service Account --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
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

                                <input id="account_number" type="text" name="account_number"
                                    value="{{ old('account_number') }}" required autocomplete="off"
                                    placeholder="Example: 14D-122-120 that appears on your water bill"
                                    class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('account_number') border-red-300 @enderror">

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
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
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
                                    <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                        required autocomplete="given-name"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('first_name') border-red-300 @enderror">
                                    @error('first_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="middle_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Middle Name
                                        <span class="font-normal text-slate-400">(optional)</span>
                                    </label>
                                    <input id="middle_name" type="text" name="middle_name"
                                        value="{{ old('middle_name') }}" autocomplete="additional-name"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

                                <div>
                                    <label for="last_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Last Name <span class="text-red-500">* required</span>
                                    </label>
                                    <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                        required autocomplete="family-name"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('last_name') border-red-300 @enderror">
                                    @error('last_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="suffix" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Suffix
                                        <span class="font-normal text-slate-400">(optional)</span>
                                    </label>
                                    <input id="suffix" type="text" name="suffix" value="{{ old('suffix') }}"
                                        placeholder="Jr., Sr., III"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

                                <div>
                                    <label for="sex" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Sex <span class="text-red-500">* required</span>
                                    </label>
                                    <select id="sex" name="sex" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('sex') border-red-300 @enderror">
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
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
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
                                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                                        autocomplete="tel" placeholder="09XXXXXXXXX"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('phone') border-red-300 @enderror">
                                    @error('phone')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Email Address <span class="text-red-500">* required</span>
                                    </label>
                                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                        autocomplete="email" placeholder="you@example.com"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('email') border-red-300 @enderror">
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
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">Service Address</h2>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Pin the exact water service connection on the map. The address fields below fill in
                                        automatically and stay editable.
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
                                        Search your street, purok, or landmark, then click the map or drag the pin onto your
                                        house or water meter. Switch to <strong>Satellite</strong> (top-right of the map) to
                                        see rooftops. The pin's coordinates are used for location-based complaint matching.
                                    </p>
                                </div>

                                {{-- Address search --}}
                                <div class="relative mb-3">
                                    <div class="flex gap-2">
                                        <div class="relative min-w-0 flex-1">
                                            <i
                                                class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                                            <input type="text" id="map-search" autocomplete="off"
                                                placeholder="Search street, purok, or landmark in Sagay City"
                                                class="w-full rounded-xl border border-slate-300 py-2.5 pl-9 pr-3.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                        </div>

                                        <button type="button" id="map-search-btn"
                                            class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:opacity-60">
                                            <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                            <span class="hidden sm:inline">Search</span>
                                        </button>
                                    </div>

                                    <div id="map-search-results"
                                        class="absolute left-0 right-0 top-full z-[1000] mt-1 hidden max-h-64 overflow-y-auto rounded-xl border border-slate-200 bg-white shadow-lg">
                                    </div>
                                </div>

                                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
                                    <div id="registration-map" class="h-[380px] w-full sm:h-[430px] lg:h-[500px]"></div>
                                </div>

                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <button type="button" id="use-registration-location"
                                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-60">
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                        Use My Location
                                    </button>

                                    <p id="registration-location-status" class="text-xs leading-5 text-slate-500">
                                        Search above, click the map, or use your current location to place the pin.
                                    </p>
                                </div>

                                <p class="mt-2 text-[11px] leading-5 text-slate-400">
                                    Tip: "Use My Location" is most accurate on a phone with GPS. Laptops and desktops
                                    estimate location from Wi-Fi or internet and can be off by a kilometer or more — use
                                    search or drag the pin instead.
                                </p>

                                <div id="detected-address-panel"
                                    class="mt-4 hidden rounded-xl border border-sky-200 bg-sky-50 p-4">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">
                                                Address detected from map
                                            </p>

                                            <p id="detected-address-text"
                                                class="mt-1 text-sm font-medium leading-6 text-slate-800"></p>

                                            <p id="detected-barangay-text"
                                                class="mt-1 text-xs font-semibold text-sky-800"></p>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Empty address fields were filled in for you. Anything you type yourself is
                                                never overwritten.
                                            </p>
                                        </div>

                                        <button type="button" id="apply-detected-address"
                                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800">
                                            <i class="fa-solid fa-rotate"></i>
                                            Use Detected Address
                                        </button>
                                    </div>
                                </div>

                                <div id="address-detection-error"
                                    class="mt-4 hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    The pin was saved, but the readable address could not be detected. You can still enter
                                    the address manually.
                                </div>

                                <input type="hidden" name="latitude" id="registration_latitude"
                                    value="{{ old('latitude') }}">

                                <input type="hidden" name="longitude" id="registration_longitude"
                                    value="{{ old('longitude') }}">

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

                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                <div>
                                    <label for="house_no" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        House / Building No.
                                        <span class="font-normal text-slate-400">(optional)</span>
                                    </label>
                                    <input id="house_no" type="text" name="house_no" value="{{ old('house_no') }}"
                                        autocomplete="address-line1"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

<div class="mb-3">
    <p class="text-sm font-semibold text-slate-700">
        Street / Purok
        <span class="text-red-500">*</span>
    </p>

    <p class="mt-1 text-xs text-slate-500">
        Enter at least one — Street or Purok — based on your service address.
    </p>
</div>

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2">

    {{-- Street --}}
    <div>
        <label
            for="street"
            class="mb-1.5 block text-sm font-medium text-slate-700"
        >
            Street
        </label>

        <input
            type="text"
            id="street"
            name="street"
            value="{{ old('street') }}"
            placeholder="e.g. Rizal Street"
            class="w-full rounded-xl border border-slate-300 px-4 py-3
                   text-sm text-slate-900 outline-none transition
                   focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
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
        <label
            for="purok"
            class="mb-1.5 block text-sm font-medium text-slate-700"
        >
            Purok
        </label>

        <input
            type="text"
            id="purok"
            name="purok"
            value="{{ old('purok') }}"
            placeholder="e.g. Purok 3"
            class="w-full rounded-xl border border-slate-300 px-4 py-3
                   text-sm text-slate-900 outline-none transition
                   focus:border-sky-500 focus:ring-2 focus:ring-sky-100"
        >

        @error('purok')
            <p class="mt-1.5 text-xs text-red-600">
                <i class="fa-solid fa-circle-exclamation mr-1"></i>
                {{ $message }}
            </p>
        @enderror
    </div>

</div>

                                {{-- Barangay dropdown --}}
                                <div>
                                    <label for="barangay" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Barangay <span class="text-red-500">* required</span>
                                    </label>

                                    <select id="barangay" name="barangay" required
                                        class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('barangay') border-red-300 @enderror">
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

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Municipality / City
                                    </label>
                                    <div
                                        class="flex min-h-[42px] w-full items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500">
                                        <i class="fa-solid fa-location-dot text-xs text-slate-400"></i>
                                        Sagay City
                                    </div>
                                </div>

                                <div>
                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Province
                                    </label>
                                    <div
                                        class="flex min-h-[42px] w-full items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-500">
                                        <i class="fa-solid fa-map text-xs text-slate-400"></i>
                                        Negros Occidental
                                    </div>
                                </div>

                            </div>

                            <p class="mt-4 text-xs leading-5 text-slate-400">
                                House No., Street, and Purok are optional — fill in whichever applies to your address. SWD
                                locates your connection using the map pin and barangay.
                            </p>

                        </div>

                        {{-- Account Security --}}
                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
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
                                        <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                                            required autocomplete="new-password" placeholder="Minimum 8 characters"
                                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('password') border-red-300 @enderror">

                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                            :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                            <i class="fa-solid text-sm"
                                                :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
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
                                    <label for="password_confirmation"
                                        class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Confirm Password <span class="text-red-500">* required</span>
                                    </label>

                                    <div class="relative">
                                        <input id="password_confirmation"
                                            :type="showConfirmation ? 'text' : 'password'" name="password_confirmation"
                                            required autocomplete="new-password" placeholder="Repeat your password"
                                            class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 pr-11 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                                        <button type="button" @click="showConfirmation = !showConfirmation"
                                            class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                            :aria-label="showConfirmation ? 'Hide password' : 'Show password'">
                                            <i class="fa-solid text-sm"
                                                :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>

                        {{-- Verification notice + submit --}}
                        <div class="p-5 sm:p-6">

                            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700">
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
                                <input type="checkbox" name="terms" value="1" required @checked(old('terms'))
                                    class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500">

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

                                <a href="{{ route('consumer.login') }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-800">
                                    <i class="fa-solid fa-arrow-left text-xs"></i>
                                    Back to Login
                                </a>

                                <button type="submit" :disabled="loading"
                                    class="inline-flex items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70">

                                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Submit Registration
                                    </span>

                                    <span x-cloak x-show="loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-spinner animate-spin"></i>
                                        Submitting...
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
            document.addEventListener('DOMContentLoaded', function() {
                const $ = id => document.getElementById(id);

                const mapElement = $('registration-map');
                const latitudeInput = $('registration_latitude');
                const longitudeInput = $('registration_longitude');
                const locateButton = $('use-registration-location');
                const statusText = $('registration-location-status');

                const houseInput = $('house_no');
                const streetInput = $('street');
                const purokInput = $('purok');
                const barangayInput = $('barangay'); // <select>

                const detectedPanel = $('detected-address-panel');
                const detectedText = $('detected-address-text');
                const detectedBarangayText = $('detected-barangay-text');
                const applyDetectedButton = $('apply-detected-address');
                const detectionError = $('address-detection-error');
                const mismatchHint = $('barangay-mismatch');

                const searchInput = $('map-search');
                const searchButton = $('map-search-btn');
                const searchResults = $('map-search-results');

                if (!mapElement || !latitudeInput || !longitudeInput) return;

                const BARANGAYS = @json(config('sagay.barangays'));
                const RAW_ALIASES = @json(config('sagay.barangay_aliases', []));
                const BOUNDARY_URL = @json(asset('geo/sagay-barangays.geojson'));

                // Rough bounding box of Sagay City, used only for warnings and search limits.
                const SAGAY_BOUNDS = L.latLngBounds([10.78, 123.28], [11.12, 123.62]);
                const DEFAULT_CENTER = [10.9447, 123.4247];

                /* ---------------- Map + layers ---------------- */
                const savedLat = parseFloat(latitudeInput.value);
                const savedLng = parseFloat(longitudeInput.value);
                const hasSavedLocation = !Number.isNaN(savedLat) && !Number.isNaN(savedLng);

                const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                });

                const satelliteLayer = L.layerGroup([
                    L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19,
                            attribution: 'Imagery &copy; Esri'
                        }),
                    L.tileLayer(
                        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
                            maxZoom: 19
                        })
                ]);

                const map = L.map('registration-map', {
                        zoomControl: true,
                        layers: [streetLayer],
                        minZoom: 10,
                        maxBounds: SAGAY_BOUNDS.pad(0.6),
                        maxBoundsViscosity: 0.7
                    })
                    .setView(hasSavedLocation ? [savedLat, savedLng] : DEFAULT_CENTER, hasSavedLocation ? 17 : 14);

                L.control.layers({
                    'Street': streetLayer,
                    'Satellite': satelliteLayer
                }, null, {
                    position: 'topright',
                    collapsed: false
                }).addTo(map);

                let marker = null;
                let accuracyCircle = null;
                let detectedAddress = null;

                /* ---------------- Helpers ---------------- */
                function setStatus(message, tone) {
                    if (!statusText) return;
                    statusText.textContent = message;
                    statusText.className = 'text-xs leading-5 ' + ({
                        warn: 'text-amber-600',
                        error: 'text-red-600',
                        ok: 'text-emerald-600'
                    } [tone] || 'text-slate-500');
                }

                const norm = s => (s || '').toLowerCase()
                    .replace(/\b(barangay|brgy\.?|bgy\.?)\b/g, ' ')
                    .replace(/[^a-z0-9]+/g, ' ')
                    .trim();

                const BARANGAY_INDEX = BARANGAYS
                    .map(name => ({
                        name,
                        key: norm(name)
                    }))
                    .sort((a, b) => b.key.length - a.key.length); // longest first

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

                // Sitio / area names from config/sagay.php => barangay
                Object.keys(RAW_ALIASES || {}).forEach(k => {
                    if (BARANGAYS.includes(RAW_ALIASES[k])) ALIASES[norm(k)] = RAW_ALIASES[k];
                });

                // Whole-phrase check so "Poblacion I" never matches inside "Poblacion II".
                function phraseMatch(text) {
                    const t = ' ' + norm(text) + ' ';
                    if (t.trim() === '') return '';

                    for (const b of BARANGAY_INDEX) {
                        if (b.key.length >= 4 && t.includes(' ' + b.key + ' ')) return b.name;
                    }
                    for (const key in ALIASES) {
                        if (key.length >= 4 && t.includes(' ' + key + ' ')) return ALIASES[key];
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
                    return values.find(v => typeof v === 'string' && v.trim() !== '') || '';
                }

                /* ---------------- Optional barangay boundaries (most accurate) ---------------- */
                let boundaryFeatures = null;

                fetch(BOUNDARY_URL)
                    .then(r => (r.ok ? r.json() : null))
                    .then(json => {
                        if (json && Array.isArray(json.features) && json.features.length) {
                            boundaryFeatures = json.features;
                            if (marker) {
                                const p = marker.getLatLng();
                                reverseGeocode(p.lat, p.lng);
                            }
                        }
                    })
                    .catch(() => {});

                function pointInRing(lng, lat, ring) {
                    let inside = false;
                    for (let i = 0, j = ring.length - 1; i < ring.length; j = i++) {
                        const xi = ring[i][0],
                            yi = ring[i][1],
                            xj = ring[j][0],
                            yj = ring[j][1];
                        if ((yi > lat) !== (yj > lat) && lng < (xj - xi) * (lat - yi) / (yj - yi) + xi) {
                            inside = !inside;
                        }
                    }
                    return inside;
                }

                function pointInPolygon(lng, lat, rings) {
                    if (!rings.length || !pointInRing(lng, lat, rings[0])) return false;
                    for (let i = 1; i < rings.length; i++) {
                        if (pointInRing(lng, lat, rings[i])) return false; // inside a hole
                    }
                    return true;
                }

                function boundaryLookup(lat, lng) {
                    if (!boundaryFeatures) return null; // no boundary file available

                    for (const f of boundaryFeatures) {
                        const g = f.geometry;
                        if (!g) continue;

                        const polys = g.type === 'Polygon' ? [g.coordinates] :
                            g.type === 'MultiPolygon' ? g.coordinates : [];

                        if (polys.some(p => pointInPolygon(lng, lat, p))) {
                            const values = Object.values(f.properties || {}).filter(v => typeof v === 'string');
                            for (const v of values) {
                                const m = exactMatch(v) || phraseMatch(v);
                                if (m) return {
                                    inside: true,
                                    barangay: m
                                };
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
                    const a = data?.address || {};
                    const displayName = data?.display_name || '';

                    let barangay = '';
                    let source = '';

                    // 1. Real boundary polygons (if public/geo/sagay-barangays.geojson exists)
                    const boundary = boundaryLookup(lat, lng);
                    if (boundary && boundary.barangay) {
                        barangay = boundary.barangay;
                        source = 'boundary';
                    }

                    // 2. OSM address fields
                    if (!barangay) {
                        const fields = [a.suburb, a.village, a.hamlet, a.neighbourhood, a.quarter, a.city_district,
                            a.residential
                        ];
                        for (const f of fields) {
                            const m = exactMatch(f) || phraseMatch(f);
                            if (m) {
                                barangay = m;
                                source = 'osm';
                                break;
                            }
                        }
                    }

                    // 3. Any part of the full display name (best guess)
                    if (!barangay && displayName) {
                        for (const token of displayName.split(',')) {
                            const m = exactMatch(token) || phraseMatch(token);
                            if (m) {
                                barangay = m;
                                source = 'guess';
                                break;
                            }
                        }
                    }

                    const purokRaw = firstValue(a.neighbourhood, a.quarter, a.hamlet);

                    return {
                        houseNo: firstValue(a.house_number, a.building),
                        street: firstValue(a.road, a.pedestrian, a.path, a.footway),
                        // Only copy a purok when OSM actually labels it as one.
                        purok: /purok|sitio|zone/i.test(purokRaw) ? purokRaw : '',
                        barangay,
                        source,
                        outsideSagay: boundary ? boundary.inside === false : false,
                        displayName
                    };
                }

                /* ---------------- Auto-fill (never overwrites what the user typed) ---------------- */
                [houseInput, streetInput, purokInput].forEach(el => {
                    el?.addEventListener('input', () => {
                        el.dataset.auto = '0';
                    });
                });

                barangayInput?.addEventListener('change', () => {
                    barangayInput.dataset.auto = '0';
                    checkMismatch();
                });

                function fillField(el, value, force) {
                    if (!el) return;

                    const isEmpty = el.value.trim() === '';
                    const wasAuto = el.dataset.auto === '1';

                    if (force || isEmpty || wasAuto) {
                        el.value = value || '';
                        el.dataset.auto = value ? '1' : '0';
                    }
                }

                function applyDetected(result, force) {
                    fillField(houseInput, result.houseNo, force);
                    fillField(streetInput, result.street, force);
                    fillField(purokInput, result.purok, force);

                    if (barangayInput) {
                        if (result.barangay) {
                            fillField(barangayInput, result.barangay, force);
                        } else if (force) {
                            // keep the user's selection when we could not detect one
                        }
                    }

                    checkMismatch();
                }

                function checkMismatch() {
                    if (!mismatchHint) return;

                    const show = detectedAddress?.barangay &&
                        detectedAddress.source !== 'guess' &&
                        barangayInput?.value &&
                        detectedAddress.barangay !== barangayInput.value;

                    mismatchHint.classList.toggle('hidden', !show);
                }

                /* ---------------- Detection panel ---------------- */
                function showDetectionLoading() {
                    detectedPanel?.classList.remove('hidden');
                    detectionError?.classList.add('hidden');

                    if (detectedText) {
                        detectedText.innerHTML =
                            '<span class="inline-flex items-center gap-2 text-slate-500">' +
                            '<i class="fa-solid fa-spinner fa-spin text-sky-600"></i>' +
                            'Detecting the nearest readable address...</span>';
                    }
                    if (detectedBarangayText) detectedBarangayText.textContent = '';

                    if (applyDetectedButton) {
                        applyDetectedButton.disabled = true;
                        applyDetectedButton.classList.add('opacity-60', 'cursor-not-allowed');
                    }
                }

                function showDetectedAddress(result) {
                    detectedAddress = result;

                    detectedPanel?.classList.remove('hidden');
                    detectionError?.classList.add('hidden');

                    if (detectedText) {
                        detectedText.textContent = result.displayName ||
                            'A nearby address was detected from the selected map location.';
                    }

                    if (detectedBarangayText) {
                        if (!result.barangay) {
                            detectedBarangayText.textContent =
                                'Barangay could not be matched automatically. Please choose it from the dropdown.';
                        } else if (result.source === 'guess') {
                            detectedBarangayText.textContent =
                                'Possible barangay: ' + result.barangay + ' (best guess — please confirm).';
                        } else {
                            detectedBarangayText.textContent = 'Matched barangay: ' + result.barangay;
                        }
                    }

                    applyDetected(result, false);

                    if (result.outsideSagay) {
                        setStatus('This pin is outside Sagay City boundaries. Please place it on your water service connection.', 'warn');
                    } else if (result.barangay) {
                        setStatus('Address filled in from the pin. Review the fields and drag the pin to fine-tune.', 'ok');
                    }

                    if (applyDetectedButton) {
                        applyDetectedButton.disabled = false;
                        applyDetectedButton.classList.remove('opacity-60', 'cursor-not-allowed');
                    }
                }

                function showDetectionFailure() {
                    detectedAddress = null;
                    detectedPanel?.classList.add('hidden');
                    detectionError?.classList.remove('hidden');
                    mismatchHint?.classList.add('hidden');
                }

                /* ---------------- Reverse geocoding (debounced, cancellable) ---------------- */
                let geocodeTimer = null;
                let geocodeController = null;

                function reverseGeocode(lat, lng) {
                    clearTimeout(geocodeTimer);
                    showDetectionLoading();
                    geocodeTimer = setTimeout(() => runReverseGeocode(lat, lng), 450);
                }

                async function runReverseGeocode(lat, lng) {
                    geocodeController?.abort();
                    geocodeController = new AbortController();
                    const controller = geocodeController;

                    try {
                        const url = 'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2&zoom=18&addressdetails=1&accept-language=en' +
                            '&lat=' + encodeURIComponent(lat) +
                            '&lon=' + encodeURIComponent(lng);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            },
                            signal: controller.signal
                        });

                        if (!response.ok) throw new Error('Reverse geocoding request failed.');

                        const data = await response.json();
                        if (data.error) throw new Error(data.error);

                        showDetectedAddress(buildDetectedAddress(data, lat, lng));
                    } catch (error) {
                        if (error.name === 'AbortError') return;
                        console.error('Reverse geocoding error:', error);

                        // Boundary data alone can still fill the barangay.
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
                    if (Number.isNaN(lat) || Number.isNaN(lng)) return;

                    latitudeInput.value = lat.toFixed(7);
                    longitudeInput.value = lng.toFixed(7);

                    if (marker) {
                        marker.setLatLng([lat, lng]);
                    } else {
                        marker = L.marker([lat, lng], {
                            draggable: true
                        }).addTo(map);

                        marker.on('dragend', function(event) {
                            const p = event.target.getLatLng();
                            setLocation(p.lat, p.lng, {
                                center: false,
                                detectAddress: true
                            });
                        });
                    }

                    // Accuracy circle only for GPS fixes; manual placement is exact.
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
                        map.setView([lat, lng], options.zoom || Math.max(map.getZoom(), 18));
                    }

                    if (!SAGAY_BOUNDS.contains([lat, lng])) {
                        setStatus('This pin looks to be outside Sagay City. Please make sure it is on your water service connection.', 'warn');
                    } else if (!options.keepStatus) {
                        setStatus('Service location selected. Drag the pin anytime to fine-tune it.', 'ok');
                    }

                    if (options.detectAddress !== false) reverseGeocode(lat, lng);
                }

                if (hasSavedLocation) {
                    setLocation(savedLat, savedLng, {
                        center: false,
                        detectAddress: true
                    });
                }

                map.on('click', e => setLocation(e.latlng.lat, e.latlng.lng, {
                    center: false,
                    detectAddress: true
                }));

                /* ---------------- Address / landmark search ---------------- */
                function hideResults() {
                    searchResults?.classList.add('hidden');
                    if (searchResults) searchResults.innerHTML = '';
                }

                function renderResults(items) {
                    searchResults.innerHTML = '';

                    if (!items.length) {
                        const empty = document.createElement('div');
                        empty.className = 'px-4 py-3 text-xs text-slate-500';
                        empty.textContent = 'No matches in Sagay City. Try another spelling, or click the map to place the pin.';
                        searchResults.appendChild(empty);
                        searchResults.classList.remove('hidden');
                        return;
                    }

                    items.forEach(item => {
                        const btn = document.createElement('button');
                        btn.type = 'button';
                        btn.className =
                            'block w-full border-b border-slate-100 px-4 py-2.5 text-left text-xs leading-5 text-slate-700 transition last:border-0 hover:bg-sky-50';
                        btn.textContent = item.display_name;

                        btn.addEventListener('click', () => {
                            hideResults();
                            searchInput.value = item.display_name.split(',').slice(0, 2).join(',').trim();
                            setLocation(item.lat, item.lon, {
                                center: true,
                                zoom: 18,
                                detectAddress: true
                            });
                        });

                        searchResults.appendChild(btn);
                    });

                    searchResults.classList.remove('hidden');
                }

                async function runSearch() {
                    const q = (searchInput?.value || '').trim();
                    if (q.length < 3) {
                        setStatus('Type at least 3 characters to search.', 'warn');
                        return;
                    }

                    searchButton.disabled = true;

                    try {
                        // Bounded to Sagay City so results are never from another town.
                        const url = 'https://nominatim.openstreetmap.org/search' +
                            '?format=jsonv2&limit=6&countrycodes=ph&accept-language=en&bounded=1' +
                            '&viewbox=123.28,11.12,123.62,10.78' +
                            '&q=' + encodeURIComponent(q);

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                        if (!response.ok) throw new Error('Search failed');

                        renderResults(await response.json());
                    } catch (error) {
                        console.error('Search error:', error);
                        setStatus('Search is unavailable right now. Please click the map to place the pin.', 'error');
                    } finally {
                        searchButton.disabled = false;
                    }
                }

                searchButton?.addEventListener('click', runSearch);

                searchInput?.addEventListener('keydown', e => {
                    if (e.key === 'Enter') {
                        e.preventDefault(); // do not submit the registration form
                        runSearch();
                    }
                    if (e.key === 'Escape') hideResults();
                });

                document.addEventListener('click', e => {
                    if (!searchResults?.contains(e.target) && e.target !== searchInput) hideResults();
                });

                /* ---------------- Overwrite with detected ---------------- */
                applyDetectedButton?.addEventListener('click', function() {
                    if (!detectedAddress) return;

                    applyDetected(detectedAddress, true);

                    setStatus(detectedAddress.barangay ?
                        'Detected address applied. Please review the fields before submitting.' :
                        'Address applied, but the barangay could not be matched. Please select it from the dropdown.',
                        detectedAddress.barangay ? 'ok' : 'warn');

                    this.innerHTML = '<i class="fa-solid fa-check"></i> Applied';
                    const button = this;
                    setTimeout(() => {
                        button.innerHTML = '<i class="fa-solid fa-rotate"></i> Overwrite with Detected';
                    }, 1800);
                });

                /* ---------------- High-accuracy device location ---------------- */
                locateButton?.addEventListener('click', function() {
                    if (!navigator.geolocation) {
                        setStatus('Location services are not supported by your browser. Please search or place the pin manually.', 'error');
                        return;
                    }

                    const button = this;
                    const originalHtml = button.innerHTML;
                    let best = null;
                    let watchId = null;
                    let timer = null;

                    button.disabled = true;
                    button.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';
                    setStatus('Getting a precise GPS fix. This can take a few seconds...', 'info');

                    function finish() {
                        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                        clearTimeout(timer);
                        watchId = null;

                        button.disabled = false;
                        button.innerHTML = originalHtml;

                        if (!best) {
                            setStatus('Could not get your location. Please search or click the map to place the pin.', 'error');
                            return;
                        }

                        const acc = Math.round(best.coords.accuracy);

                        setLocation(best.coords.latitude, best.coords.longitude, {
                            center: true,
                            zoom: acc > 500 ? 16 : 18,
                            accuracy: best.coords.accuracy,
                            detectAddress: true,
                            keepStatus: true
                        });

                        if (acc > 100) {
                            setStatus('Location is only accurate to about ' + acc +
                                ' m (typical for laptops). Use search, or switch to Satellite and drag the pin onto your property.',
                                'warn');
                        } else {
                            setStatus('Location found (accurate to about ' + acc +
                                ' m). Drag the pin if the water service is at a different spot.', 'ok');
                        }
                    }

                    watchId = navigator.geolocation.watchPosition(
                        function(position) {
                            if (!best || position.coords.accuracy < best.coords.accuracy) {
                                best = position;
                                // Live preview while the fix improves.
                                setLocation(position.coords.latitude, position.coords.longitude, {
                                    center: true,
                                    zoom: position.coords.accuracy > 500 ? 16 : 18,
                                    accuracy: position.coords.accuracy,
                                    detectAddress: false,
                                    keepStatus: true
                                });
                            }
                            if (position.coords.accuracy <= 25) finish();
                        },
                        function(error) {
                            console.error('Geolocation error:', error);

                            if (best) return finish();

                            if (watchId !== null) navigator.geolocation.clearWatch(watchId);
                            clearTimeout(timer);
                            button.disabled = false;
                            button.innerHTML = originalHtml;

                            const messages = {
                                1: 'Location permission is blocked. Allow location access for this site, then try again, or search / click the map to place the pin.',
                                2: 'Your device could not determine its location. Please search or click the map to place the pin.',
                                3: 'Location detection timed out. Try again, or search / click the map to place the pin.'
                            };
                            setStatus(messages[error.code] ||
                                'Unable to get your location. Please search or click the map to place the pin.',
                                'error');
                        }, {
                            enableHighAccuracy: true,
                            timeout: 20000,
                            maximumAge: 0
                        }
                    );

                    // Stop waiting for a better fix after 12 seconds and use the best so far.
                    timer = setTimeout(finish, 12000);
                });

                /* ---------------- Pin required on submit ---------------- */
                window.addEventListener('pin-required', function() {
                    setStatus('Please place the service-location pin on the map before submitting.', 'error');
                    mapElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                });

                requestAnimationFrame(() => requestAnimationFrame(() => map.invalidateSize()));
                window.addEventListener('resize', () => map.invalidateSize());
            });
        </script>
    @endpush

@endsection
