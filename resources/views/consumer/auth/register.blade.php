@extends('consumer.auth.layout')

@section('title', 'Create Consumer Account')

@section('content')

    <div class="min-h-screen bg-slate-50" x-data="{

        loading: false,

        showPassword: false,

        showConfirmation: false

    }">

        <div class="border-b border-slate-200 bg-white">

            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">

                <a href="{{ route('landing') }}" class="flex items-center gap-3">

                    <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white">

                        <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo" class="h-9 w-9 object-contain">

                    </div>

                    <div>

                        <p class="text-xl font-semibold leading-none text-sky-700">

                            iSWD

                        </p>

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

                <aside class="lg:sticky lg:top-6">

                    <div

                        class="rounded-2xl bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 p-6 text-white shadow-lg">

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

                                <div class="flex gap-3">

                                    <div

                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white text-xs font-semibold text-sky-700">

                                        1

                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">

                                            Submit Information

                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">

                                            Provide your service account and contact details.

                                        </p>

                                    </div>

                                </div>

                                <div class="flex gap-3">

                                    <div

                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold text-white">

                                        2

                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">

                                            SWD Verification

                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">

                                            Your registration is reviewed against SWD records.

                                        </p>

                                    </div>

                                </div>

                                <div class="flex gap-3">

                                    <div

                                        class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-white/15 text-xs font-semibold text-white">

                                        3

                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">

                                            Portal Activation

                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">

                                            Once approved, your Consumer Portal access becomes active.

                                        </p>

                                    </div>

                                </div>

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

                                <p class="text-xs font-semibold text-slate-700">

                                    Already registered?

                                </p>

                                <a href="{{ route('consumer.login') }}"

                                    class="mt-1 inline-flex items-center gap-1.5 text-xs font-semibold text-sky-700 hover:text-sky-800">

                                    Sign in to Consumer Portal

                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>

                                </a>

                            </div>

                        </div>

                    </div>

                </aside>

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

                    <form method="POST" action="{{ route('consumer.register.store') }}" @submit="loading = true"

                        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg shadow-slate-200/40">

                        @csrf

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div

                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                    <i class="fa-solid fa-droplet"></i>

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">

                                        Water Service Account

                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">

                                        Enter the account number shown on your Sagay Water District bill.

                                    </p>

                                </div>

                            </div>

                            <div class="max-w-lg">

                                <label for="account_number" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Account Number

                                    <span class="text-red-500">*</span>

                                </label>

                                <input id="account_number" type="text" name="account_number"

                                    value="{{ old('account_number') }}" required autocomplete="off"

                                    placeholder="Example: 14D-122-120"

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

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div

                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                    <i class="fa-solid fa-user"></i>

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">

                                        Personal Information

                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">

                                        Enter the registered account holder's information.

                                    </p>

                                </div>

                            </div>

                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                <div>

                                    <label for="first_name" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        First Name <span class="text-red-500">*</span>

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

                                    </label>

                                    <input id="middle_name" type="text" name="middle_name"

                                        value="{{ old('middle_name') }}" autocomplete="additional-name"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                                </div>

                                <div>

                                    <label for="last_name" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Last Name <span class="text-red-500">*</span>

                                    </label>

                                    <input id="last_name" type="text" name="last_name"

                                        value="{{ old('last_name') }}" required autocomplete="family-name"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('last_name') border-red-300 @enderror">

                                    @error('last_name')

                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>

                                    @enderror

                                </div>

                                <div>

                                    <label for="suffix" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Suffix

                                    </label>

                                    <input id="suffix" type="text" name="suffix" value="{{ old('suffix') }}"

                                        placeholder="Jr., Sr., III"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                                </div>

                                <div>

                                    <label for="sex" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Sex <span class="text-red-500">*</span>

                                    </label>

                                    <select id="sex" name="sex" required

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('sex') border-red-300 @enderror">

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

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div

                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                    <i class="fa-solid fa-address-book"></i>

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">

                                        Contact Information

                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">

                                        Used for portal access and account-related communication.

                                    </p>

                                </div>

                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>

                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Mobile Number <span class="text-red-500">*</span>

                                    </label>

                                    <input id="phone" type="text" name="phone" value="{{ old('phone') }}"

                                        required autocomplete="tel" placeholder="09XXXXXXXXX"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('phone') border-red-300 @enderror">

                                    @error('phone')

                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>

                                    @enderror

                                </div>

                                <div>

                                    <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Email Address <span class="text-red-500">*</span>

                                    </label>

                                    <input id="email" type="email" name="email" value="{{ old('email') }}"

                                        required autocomplete="email" placeholder="you@example.com"

                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('email') border-red-300 @enderror">

                                    @error('email')

                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>

                                    @enderror

                                </div>

                            </div>

                        </div>

                                                <div class="border-b border-slate-200 p-5 sm:p-6">
                            <div class="mb-5 flex items-start gap-3">
                                <div
                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>
                                    <h2 class="text-base font-semibold text-slate-900">
                                        Service Address
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Enter the registered service address, then use the map to identify the exact water
                                        service connection.
                                    </p>
                                </div>
                            </div>

                            {{-- Registered Water Service Location --}}
                            <div class="mb-7">
                                <div class="mb-4 flex flex-col gap-2 lg:flex-row lg:items-end lg:justify-between">
                                    <div>
                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Registered Water Service Location
                                            <span class="text-red-500">*</span>
                                        </h3>

                                        <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">
                                            Place the pin first. You can click the map, search by pin, or use your device location. The pin stores the exact coordinates
                                            used for location-based complaint matching.
                                        </p>
                                    </div>

                                    <div
                                        class="inline-flex w-fit items-center gap-2 rounded-lg bg-slate-100 px-3 py-2 text-[11px] font-medium text-slate-600">
                                        <i class="fa-solid fa-circle-info text-sky-600"></i>
                                        Address fields remain editable
                                    </div>
                                </div>

                                <div
                                    class="overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 shadow-sm">
                                    <div id="registration-map" class="h-[380px] w-full sm:h-[430px] lg:h-[500px]"></div>
                                </div>

                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                                    <button type="button" id="use-registration-location"
                                        class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100 disabled:cursor-not-allowed disabled:opacity-60">
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                        Use My Location
                                    </button>

                                    <p id="registration-location-status" class="text-xs leading-5 text-slate-500">
                                        Click the map or use your current location to place the service-location pin.
                                    </p>
                                </div>

                                <div id="detected-address-panel"
                                    class="mt-4 hidden rounded-xl border border-sky-200 bg-sky-50 p-4">
                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                        <div class="min-w-0">
                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">
                                                Address detected from map
                                            </p>

                                            <p id="detected-address-text"
                                                class="mt-1 text-sm font-medium leading-6 text-slate-800">
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Review this result before copying it to the registered service address.
                                            </p>
                                        </div>

                                        <button type="button" id="apply-detected-address"
                                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800">
                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                                            Use Detected Address
                                        </button>
                                    </div>
                                </div>

                                <div id="address-detection-error"
                                    class="mt-4 hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                                    The pin was saved, but the readable address could not be detected. You can still enter the
                                    service address manually.
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
                                    </label>

                                    <input id="house_no" type="text" name="house_no" value="{{ old('house_no') }}"
                                        autocomplete="address-line1"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

                                <div>
                                    <label for="street" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Street
                                    </label>

                                    <input id="street" type="text" name="street" value="{{ old('street') }}"
                                        autocomplete="address-line2"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

                                <div>
                                    <label for="purok" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Purok
                                    </label>

                                    <input id="purok" type="text" name="purok" value="{{ old('purok') }}"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>

                                <div>
                                    <label for="barangay" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Barangay <span class="text-red-500">*</span>
                                    </label>

                                    <input id="barangay" type="text" name="barangay" value="{{ old('barangay') }}"
                                        required placeholder="Barangay"
                                        class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('barangay') border-red-300 @enderror">

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


                        </div>


<div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div

                                    class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                    <i class="fa-solid fa-lock"></i>

                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">

                                        Account Security

                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">

                                        Create the password you will use after account approval.

                                    </p>

                                </div>

                            </div>

                            <div class="grid gap-4 sm:grid-cols-2">

                                <div>

                                    <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">

                                        Password <span class="text-red-500">*</span>

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

                                        Confirm Password <span class="text-red-500">*</span>

                                    </label>

                                    <div class="relative">

                                        <input id="password_confirmation" :type="showConfirmation ? 'text' : 'password'"

                                            name="password_confirmation" required autocomplete="new-password"

                                            placeholder="Repeat your password"

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

                                            Your registration will be reviewed by Sagay Water District. Portal access

                                            remains inactive until your consumer information has been verified and approved.

                                        </p>

                                    </div>

                                </div>

                            </div>

                            <label class="mt-5 flex cursor-pointer items-start gap-3">

                                <input type="checkbox" name="terms" value="1" required

                                    @checked(old('terms'))

                                    class="mt-1 rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                                <span class="text-xs leading-5 text-slate-600 sm:text-sm sm:leading-6">

                                    I certify that the information provided is accurate and belongs to the registered Sagay

                                    Water District service account. I understand that my registration must be verified

                                    before my online account can be activated.

                                </span>

                            </label>

                            @error('terms')

                                <p class="mt-2 text-xs text-red-600">

                                    {{ $message }}

                                </p>

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
                const mapElement = document.getElementById('registration-map');
                const latitudeInput = document.getElementById('registration_latitude');
                const longitudeInput = document.getElementById('registration_longitude');
                const locateButton = document.getElementById('use-registration-location');
                const statusText = document.getElementById('registration-location-status');

                const houseInput = document.getElementById('house_no');
                const streetInput = document.getElementById('street');
                const purokInput = document.getElementById('purok');
                const barangayInput = document.getElementById('barangay');

                const detectedPanel = document.getElementById('detected-address-panel');
                const detectedText = document.getElementById('detected-address-text');
                const applyDetectedButton = document.getElementById('apply-detected-address');
                const detectionError = document.getElementById('address-detection-error');

                if (!mapElement || !latitudeInput || !longitudeInput) {
                    return;
                }

                const defaultLat = 10.9447;
                const defaultLng = 123.4247;

                const savedLat = parseFloat(latitudeInput.value);
                const savedLng = parseFloat(longitudeInput.value);

                const hasSavedLocation =
                    !Number.isNaN(savedLat) &&
                    !Number.isNaN(savedLng);

                const map = L.map('registration-map', {
                    zoomControl: true
                }).setView(
                    hasSavedLocation
                        ? [savedLat, savedLng]
                        : [defaultLat, defaultLng],
                    hasSavedLocation ? 17 : 14
                );

                L.tileLayer(
                    'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    {
                        maxZoom: 19,
                        attribution: '&copy; OpenStreetMap contributors'
                    }
                ).addTo(map);

                let marker = null;
                let detectedAddress = null;
                let reverseGeocodeRequest = 0;

                function firstValue(...values) {
                    return values.find(value =>
                        typeof value === 'string' &&
                        value.trim() !== ''
                    ) || '';
                }

                function normalizeBarangay(value) {
                    if (!value) {
                        return '';
                    }

                    return value
                        .replace(/^Barangay\s+/i, '')
                        .replace(/^Brgy\.?\s+/i, '')
                        .trim();
                }

                function buildDetectedAddress(data) {
                    const address = data?.address || {};

                    const houseNo = firstValue(
                        address.house_number,
                        address.building
                    );

                    const street = firstValue(
                        address.road,
                        address.pedestrian,
                        address.residential,
                        address.path
                    );

                    const purok = firstValue(
                        address.neighbourhood,
                        address.quarter
                    );

                    const barangay = normalizeBarangay(
                        firstValue(
                            address.suburb,
                            address.village,
                            address.hamlet,
                            address.neighbourhood
                        )
                    );

                    return {
                        houseNo,
                        street,
                        purok,
                        barangay,
                        displayName: data?.display_name || ''
                    };
                }

                function showDetectionLoading() {
                    if (detectedPanel) {
                        detectedPanel.classList.remove('hidden');
                    }

                    if (detectedText) {
                        detectedText.innerHTML =
                            '<span class="inline-flex items-center gap-2 text-slate-500">' +
                            '<i class="fa-solid fa-spinner fa-spin text-sky-600"></i>' +
                            'Detecting the nearest readable address...' +
                            '</span>';
                    }

                    if (applyDetectedButton) {
                        applyDetectedButton.disabled = true;
                        applyDetectedButton.classList.add('opacity-60', 'cursor-not-allowed');
                    }

                    if (detectionError) {
                        detectionError.classList.add('hidden');
                    }
                }

                function showDetectedAddress(result) {
                    detectedAddress = result;

                    if (detectedPanel) {
                        detectedPanel.classList.remove('hidden');
                    }

                    if (detectedText) {
                        detectedText.textContent =
                            result.displayName ||
                            'A nearby address was detected from the selected map location.';
                    }

                    if (applyDetectedButton) {
                        applyDetectedButton.disabled = false;
                        applyDetectedButton.classList.remove('opacity-60', 'cursor-not-allowed');
                    }

                    if (detectionError) {
                        detectionError.classList.add('hidden');
                    }
                }

                function showDetectionFailure() {
                    detectedAddress = null;

                    if (detectedPanel) {
                        detectedPanel.classList.add('hidden');
                    }

                    if (detectionError) {
                        detectionError.classList.remove('hidden');
                    }
                }

                async function reverseGeocode(latitude, longitude) {
                    const requestId = ++reverseGeocodeRequest;

                    showDetectionLoading();

                    try {
                        const url =
                            'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2' +
                            '&lat=' + encodeURIComponent(latitude) +
                            '&lon=' + encodeURIComponent(longitude) +
                            '&zoom=18' +
                            '&addressdetails=1';

                        const response = await fetch(url, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });

                        if (!response.ok) {
                            throw new Error('Reverse geocoding request failed.');
                        }

                        const data = await response.json();

                        if (requestId !== reverseGeocodeRequest) {
                            return;
                        }

                        showDetectedAddress(
                            buildDetectedAddress(data)
                        );
                    } catch (error) {
                        console.error('Reverse geocoding error:', error);

                        if (requestId === reverseGeocodeRequest) {
                            showDetectionFailure();
                        }
                    }
                }

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

                        marker.on('dragend', function(event) {
                            const position = event.target.getLatLng();

                            setLocation(
                                position.lat,
                                position.lng,
                                {
                                    center: false,
                                    detectAddress: true
                                }
                            );
                        });
                    }

                    if (options.center !== false) {
                        map.setView(
                            [lat, lng],
                            options.zoom || Math.max(map.getZoom(), 17)
                        );
                    }

                    if (statusText) {
                        statusText.textContent =
                            'Service location selected. Drag the pin anytime to fine-tune the exact location.';
                    }

                    if (options.detectAddress !== false) {
                        reverseGeocode(lat, lng);
                    }
                }

                if (hasSavedLocation) {
                    setLocation(
                        savedLat,
                        savedLng,
                        {
                            center: false,
                            detectAddress: true
                        }
                    );
                }

                map.on('click', function(event) {
                    setLocation(
                        event.latlng.lat,
                        event.latlng.lng,
                        {
                            center: false,
                            detectAddress: true
                        }
                    );
                });

                if (applyDetectedButton) {
                    applyDetectedButton.addEventListener('click', function() {
                        if (!detectedAddress) {
                            return;
                        }

                        if (houseInput && detectedAddress.houseNo) {
                            houseInput.value = detectedAddress.houseNo;
                        }

                        if (streetInput && detectedAddress.street) {
                            streetInput.value = detectedAddress.street;
                        }

                        /*
                         * Nominatim does not consistently know local purok names.
                         * Only copy a detected purok when one is actually returned.
                         */
                        if (purokInput && detectedAddress.purok) {
                            purokInput.value = detectedAddress.purok;
                        }

                        if (barangayInput && detectedAddress.barangay) {
                            barangayInput.value = detectedAddress.barangay;
                        }

                        if (statusText) {
                            statusText.textContent =
                                'Detected address copied. Please review the address fields before submitting.';
                        }

                        this.innerHTML =
                            '<i class="fa-solid fa-check"></i> Address Applied';

                        const button = this;

                        setTimeout(function() {
                            button.innerHTML =
                                '<i class="fa-solid fa-arrow-up-right-from-square"></i> Use Detected Address';
                        }, 1800);
                    });
                }

                if (locateButton) {
                    locateButton.addEventListener('click', function() {
                        if (!navigator.geolocation) {
                            alert(
                                'Location services are not supported by your browser. Please place the pin manually on the map.'
                            );
                            return;
                        }

                        const button = this;
                        const originalHtml = button.innerHTML;

                        button.disabled = true;
                        button.innerHTML =
                            '<i class="fa-solid fa-spinner fa-spin"></i> Locating...';

                        navigator.geolocation.getCurrentPosition(
                            function(position) {
                                const latitude = position.coords.latitude;
                                const longitude = position.coords.longitude;

                                setLocation(
                                    latitude,
                                    longitude,
                                    {
                                        center: true,
                                        zoom: 17,
                                        detectAddress: true
                                    }
                                );

                                button.disabled = false;
                                button.innerHTML = originalHtml;

                                if (statusText) {
                                    statusText.textContent =
                                        'Device location found. Drag the pin if the registered water service is at a different spot.';
                                }
                            },
                            function(error) {
                                console.error('Geolocation error:', error);

                                button.disabled = false;
                                button.innerHTML = originalHtml;

                                let message =
                                    'Unable to get your device location. You can still click the map to place the service-location pin manually.';

                                if (error && error.code === 1) {
                                    message =
                                        'Location permission is blocked. Allow location access for this site in your browser, then try again. You can also click the map to place the pin manually.';
                                } else if (error && error.code === 2) {
                                    message =
                                        'Your device could not determine its current location. Please click the map to place the service-location pin manually.';
                                } else if (error && error.code === 3) {
                                    message =
                                        'Location detection timed out. Try again or click the map to place the service-location pin manually.';
                                }

                                if (statusText) {
                                    statusText.textContent = message;
                                }

                                alert(message);
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    });
                }

                requestAnimationFrame(function() {
                    requestAnimationFrame(function() {
                        map.invalidateSize();
                    });
                });

                window.addEventListener('resize', function() {
                    map.invalidateSize();
                });
            });
        </script>
    @endpush

@endsection

