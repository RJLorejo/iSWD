@extends('consumer.auth.layout')

@section('title', 'Correct Registration')

@section('content')

    <div class="min-h-screen bg-slate-50" x-data="{ loading: false }">

        {{-- ========================================================= --}}
        {{-- TOP NAVIGATION --}}
        {{-- ========================================================= --}}

        <div class="border-b border-slate-200 bg-white">

            <div
                class="
                    mx-auto flex max-w-7xl
                    items-center justify-between
                    px-4 py-4
                    sm:px-6
                    lg:px-8
                ">

                <a href="{{ route('landing') }}" class="flex items-center gap-3">

                    <div
                        class="
                            flex h-11 w-11
                            items-center justify-center
                            rounded-xl
                            border border-sky-100
                            bg-white
                        ">
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

                <a href="{{ route('consumer.registration.status') }}"
                    class="
                        inline-flex items-center gap-2
                        rounded-xl
                        border border-slate-200
                        px-3.5 py-2
                        text-xs font-semibold
                        text-slate-600
                        transition
                        hover:border-sky-200
                        hover:bg-sky-50
                        hover:text-sky-700
                        sm:px-4
                        sm:text-sm
                    ">

                    <i class="fa-solid fa-arrow-left"></i>

                    <span class="hidden sm:inline">
                        Registration Status
                    </span>

                    <span class="sm:hidden">
                        Status
                    </span>

                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- PAGE --}}
        {{-- ========================================================= --}}

        <div
            class="
                mx-auto max-w-7xl
                px-4 py-8
                sm:px-6
                lg:px-8
                lg:py-10
            ">

            <div
                class="
                    grid items-start gap-8
                    lg:grid-cols-[280px_1fr]
                    xl:grid-cols-[320px_1fr]
                ">

                {{-- ================================================= --}}
                {{-- LEFT SIDEBAR --}}
                {{-- ================================================= --}}

                <aside class="lg:sticky lg:top-6">

                    <div
                        class="
                            overflow-hidden
                            rounded-2xl
                            bg-gradient-to-b
                            from-sky-700
                            via-blue-700
                            to-cyan-700
                            text-white
                            shadow-lg
                        ">

                        <div class="p-6">

                            <div
                                class="
                                    flex h-11 w-11
                                    items-center justify-center
                                    rounded-xl
                                    bg-white/15
                                ">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </div>

                            <h1 class="mt-5 text-2xl font-semibold tracking-tight">
                                Correct Registration
                            </h1>

                            <p class="mt-3 text-sm leading-6 text-sky-50/90">
                                Review the verification feedback,
                                correct your consumer information,
                                and submit it again for Sagay Water
                                District review.
                            </p>

                        </div>


                        {{-- Process --}}
                        <div class="border-t border-white/15 px-6 py-5">

                            <p class="text-xs font-semibold uppercase tracking-[0.14em] text-sky-100">
                                Resubmission Process
                            </p>

                            <div class="mt-4 space-y-4">

                                {{-- Step 1 --}}
                                <div class="flex gap-3">

                                    <div
                                        class="
                                            flex h-7 w-7 shrink-0
                                            items-center justify-center
                                            rounded-full
                                            bg-white
                                            text-xs font-semibold
                                            text-sky-700
                                        ">
                                        1
                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">
                                            Review Feedback
                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">
                                            Check the reason provided
                                            during verification.
                                        </p>

                                    </div>

                                </div>


                                {{-- Step 2 --}}
                                <div class="flex gap-3">

                                    <div
                                        class="
                                            flex h-7 w-7 shrink-0
                                            items-center justify-center
                                            rounded-full
                                            bg-white/15
                                            text-xs font-semibold
                                            text-white
                                        ">
                                        2
                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">
                                            Correct Information
                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">
                                            Update information that does
                                            not match SWD records.
                                        </p>

                                    </div>

                                </div>


                                {{-- Step 3 --}}
                                <div class="flex gap-3">

                                    <div
                                        class="
                                            flex h-7 w-7 shrink-0
                                            items-center justify-center
                                            rounded-full
                                            bg-white/15
                                            text-xs font-semibold
                                            text-white
                                        ">
                                        3
                                    </div>

                                    <div>

                                        <p class="text-sm font-medium">
                                            Resubmit for Review
                                        </p>

                                        <p class="mt-0.5 text-xs leading-5 text-sky-100/80">
                                            SWD will review the corrected
                                            registration again.
                                        </p>

                                    </div>

                                </div>

                            </div>

                        </div>


                        <div class="px-6 pb-6">

                            <div class="rounded-xl border border-white/15 bg-white/10 p-4">

                                <div class="flex items-start gap-3">

                                    <i class="fa-solid fa-shield-halved mt-0.5 text-sky-100"></i>

                                    <p class="text-xs leading-5 text-sky-50/90">
                                        Make sure your account number
                                        and registered consumer information
                                        match your Sagay Water District
                                        records.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>


                    <a href="{{ route('consumer.registration.status') }}"
                        class="
                            mt-4
                            hidden items-center gap-3
                            rounded-xl
                            border border-slate-200
                            bg-white
                            p-4
                            transition
                            hover:border-sky-200
                            hover:bg-sky-50
                            lg:flex
                        ">

                        <div
                            class="
                                flex h-9 w-9 shrink-0
                                items-center justify-center
                                rounded-lg
                                bg-slate-100
                                text-slate-500
                            ">
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                        </div>

                        <div>

                            <p class="text-xs font-semibold text-slate-700">
                                Return to Status
                            </p>

                            <p class="mt-0.5 text-[11px] text-slate-500">
                                View your verification result
                            </p>

                        </div>

                    </a>

                </aside>


                {{-- ================================================= --}}
                {{-- MAIN CONTENT --}}
                {{-- ================================================= --}}

                <div class="min-w-0">

                    {{-- Verification Feedback --}}
                    <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4 sm:p-5">

                        <div class="flex items-start gap-3">

                            <div
                                class="
                                    flex h-10 w-10 shrink-0
                                    items-center justify-center
                                    rounded-xl
                                    bg-red-100
                                    text-red-600
                                ">
                                <i class="fa-solid fa-circle-exclamation"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="text-sm font-semibold text-red-900">
                                    Verification Feedback
                                </p>

                                <p class="mt-1 text-sm leading-6 text-red-800">
                                    {{ $consumer->verification_reason ?: 'The submitted registration information could not be verified.' }}
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Validation Errors --}}
                    @if ($errors->any())

                        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 p-4">

                            <div class="flex items-start gap-3">

                                <div
                                    class="
                                        flex h-9 w-9 shrink-0
                                        items-center justify-center
                                        rounded-lg
                                        bg-red-100
                                        text-red-600
                                    ">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-red-800">
                                        Please review the following information
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


                    {{-- ================================================= --}}
                    {{-- FORM --}}
                    {{-- ================================================= --}}

                    <form
                        id="registration-correction-form"
                        method="POST"
                        action="{{ route('consumer.registration.update') }}"
                        @submit.prevent="if (loading) return; if (window.confirm('Are you sure you want to resubmit your corrected registration information to Sagay Water District for verification? Please make sure that all information is accurate.')) { loading = true; $el.submit(); }"
                        class="
                            overflow-hidden
                            rounded-2xl
                            border border-slate-200
                            bg-white
                            shadow-lg
                            shadow-slate-200/40
                        ">

                        @csrf
                        @method('PUT')


                        {{-- ============================================= --}}
                        {{-- WATER SERVICE ACCOUNT --}}
                        {{-- ============================================= --}}

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div
                                    class="
                                        flex h-10 w-10 shrink-0
                                        items-center justify-center
                                        rounded-xl
                                        bg-sky-50
                                        text-sky-700
                                    ">
                                    <i class="fa-solid fa-droplet"></i>
                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">
                                        Water Service Account
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Enter the account number exactly as shown
                                        on your Sagay Water District bill.
                                    </p>

                                </div>

                            </div>


                            <div class="max-w-lg">

                                <label for="account_number" class="mb-1.5 block text-sm font-medium text-slate-700">
                                    Account Number
                                    <span class="text-red-500">*</span>
                                </label>

                                <input
                                    id="account_number"
                                    type="text"
                                    name="account_number"
                                    required
                                    autocomplete="off"
                                    value="{{ old('account_number', $consumer->account_number) }}"
                                    placeholder="Example: 14D-122-120"
                                    class="
                                        w-full rounded-xl border border-slate-300
                                        px-3.5 py-2.5 text-sm outline-none transition
                                        placeholder:text-slate-400
                                        focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                        @error('account_number') border-red-300 @enderror
                                    ">

                                @error('account_number')
                                    <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>


                        {{-- ============================================= --}}
                        {{-- PERSONAL INFORMATION --}}
                        {{-- ============================================= --}}

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div
                                    class="
                                        flex h-10 w-10 shrink-0
                                        items-center justify-center
                                        rounded-xl
                                        bg-sky-50
                                        text-sky-700
                                    ">
                                    <i class="fa-solid fa-user"></i>
                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">
                                        Personal Information
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Make sure the name matches the registered
                                        SWD account holder.
                                    </p>

                                </div>

                            </div>


                            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                {{-- First Name --}}
                                <div>

                                    <label for="first_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        First Name
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        id="first_name"
                                        type="text"
                                        name="first_name"
                                        required
                                        autocomplete="given-name"
                                        value="{{ old('first_name', $consumer->first_name) }}"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                            @error('first_name') border-red-300 @enderror
                                        ">

                                    @error('first_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>


                                {{-- Middle Name --}}
                                <div>

                                    <label for="middle_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Middle Name
                                        <span class="ml-1 text-xs font-normal text-slate-400">Optional</span>
                                    </label>

                                    <input
                                        id="middle_name"
                                        type="text"
                                        name="middle_name"
                                        autocomplete="additional-name"
                                        value="{{ old('middle_name', $consumer->middle_name) }}"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                        ">

                                </div>


                                {{-- Last Name --}}
                                <div>

                                    <label for="last_name" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Last Name
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        id="last_name"
                                        type="text"
                                        name="last_name"
                                        required
                                        autocomplete="family-name"
                                        value="{{ old('last_name', $consumer->last_name) }}"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                            @error('last_name') border-red-300 @enderror
                                        ">

                                    @error('last_name')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>


                                {{-- Suffix --}}
                                <div>

                                    <label for="suffix" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Suffix
                                        <span class="ml-1 text-xs font-normal text-slate-400">Optional</span>
                                    </label>

                                    <input
                                        id="suffix"
                                        type="text"
                                        name="suffix"
                                        value="{{ old('suffix', $consumer->suffix) }}"
                                        placeholder="Jr., Sr., III"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            placeholder:text-slate-400
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                        ">

                                </div>


                                {{-- Sex --}}
                                <div>

                                    <label for="sex" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Sex
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <select
                                        id="sex"
                                        name="sex"
                                        required
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                            @error('sex') border-red-300 @enderror
                                        ">

                                        <option value="">Select sex</option>

                                        <option value="Male" @selected(old('sex', $consumer->sex) === 'Male')>
                                            Male
                                        </option>

                                        <option value="Female" @selected(old('sex', $consumer->sex) === 'Female')>
                                            Female
                                        </option>

                                    </select>

                                    @error('sex')
                                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                                    @enderror

                                </div>

                            </div>

                        </div>


                        {{-- ============================================= --}}
                        {{-- CONTACT INFORMATION --}}
                        {{-- ============================================= --}}

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div
                                    class="
                                        flex h-10 w-10 shrink-0
                                        items-center justify-center
                                        rounded-xl
                                        bg-sky-50
                                        text-sky-700
                                    ">
                                    <i class="fa-solid fa-address-book"></i>
                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">
                                        Contact Information
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Review your contact information before
                                        resubmitting.
                                    </p>

                                </div>

                            </div>


                            <div class="grid gap-4 sm:grid-cols-2">

                                {{-- Phone --}}
                                <div>

                                    <label for="phone" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Mobile Number
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <input
                                        id="phone"
                                        type="tel"
                                        name="phone"
                                        required
                                        inputmode="numeric"
                                        maxlength="11"
                                        pattern="09[0-9]{9}"
                                        autocomplete="tel"
                                        value="{{ old('phone', $consumer->phone) }}"
                                        placeholder="09XXXXXXXXX"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            placeholder:text-slate-400
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                            @error('phone') border-red-300 @enderror
                                        ">

                                    <p class="mt-1.5 text-xs text-slate-400">
                                        Enter an 11-digit mobile number starting with 09.
                                    </p>

                                    @error('phone')
                                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                {{-- Login Email --}}
                                <div>

                                    <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                        Login Email
                                    </label>

                                    <div
                                        class="
                                            flex min-h-[42px] items-center gap-2
                                            rounded-xl border border-slate-200
                                            bg-slate-50 px-3.5 py-2.5
                                            text-sm text-slate-500
                                        ">

                                        <i class="fa-solid fa-envelope text-xs text-slate-400"></i>

                                        <span class="min-w-0 truncate">
                                            {{ $consumer->email }}
                                        </span>

                                        <i class="fa-solid fa-lock ml-auto text-[10px] text-slate-400"></i>

                                    </div>

                                    <p class="mt-1.5 text-xs text-slate-400">
                                        Your verified login email remains unchanged.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- ============================================= --}}
                        {{-- SERVICE ADDRESS --}}
                        {{-- ============================================= --}}

                        <div class="border-b border-slate-200 p-5 sm:p-6">

                            <div class="mb-5 flex items-start gap-3">

                                <div
                                    class="
                                        flex h-10 w-10 shrink-0
                                        items-center justify-center
                                        rounded-xl
                                        bg-sky-50
                                        text-sky-700
                                    ">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>

                                <div>

                                    <h2 class="text-base font-semibold text-slate-900">
                                        Service Address
                                    </h2>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Review the registered service address and
                                        exact water service location before
                                        resubmitting.
                                    </p>

                                </div>

                            </div>


                            {{-- ========================================= --}}
                            {{-- MAP / REGISTERED WATER SERVICE LOCATION --}}
                            {{-- ========================================= --}}

                            <div class="mb-7">

                                <div
                                    class="
                                        mb-4 flex flex-col gap-2
                                        lg:flex-row lg:items-end lg:justify-between
                                    ">

                                    <div>

                                        <h3 class="text-sm font-semibold text-slate-900">
                                            Registered Water Service Location
                                            <span class="text-red-500">*</span>
                                        </h3>

                                        <p class="mt-1 max-w-2xl text-xs leading-5 text-slate-500">
                                            Place the pin first. You can click the map,
                                            drag the marker, or use your device location.
                                            The selected coordinates will only replace
                                            your registered location after you confirm
                                            <strong>Use Detected Address</strong>.
                                        </p>

                                    </div>


                                    <div
                                        class="
                                            inline-flex w-fit items-center gap-2
                                            rounded-lg bg-slate-100 px-3 py-2
                                            text-[11px] font-medium text-slate-600
                                        ">
                                        <i class="fa-solid fa-circle-info text-sky-600"></i>
                                        Existing address is protected until applied
                                    </div>

                                </div>


                                {{-- Map --}}
                                <div
                                    class="
                                        overflow-hidden rounded-2xl
                                        border border-slate-200
                                        bg-slate-100 shadow-sm
                                    ">
                                    <div
                                        id="registration-map"
                                        class="h-[380px] w-full sm:h-[430px] lg:h-[500px]">
                                    </div>
                                </div>


                                {{-- Map Controls --}}
                                <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">

                                    <button
                                        type="button"
                                        id="use-registration-location"
                                        class="
                                            inline-flex shrink-0 items-center justify-center gap-2
                                            rounded-xl border border-sky-200 bg-sky-50
                                            px-4 py-2.5 text-sm font-semibold text-sky-700
                                            transition hover:bg-sky-100
                                            disabled:cursor-not-allowed disabled:opacity-60
                                        ">
                                        <i class="fa-solid fa-location-crosshairs"></i>
                                        Use My Location
                                    </button>

                                    <p id="registration-location-status" class="text-xs leading-5 text-slate-500">
                                        Click the map or use your current location
                                        to preview a service-location pin. Your
                                        registered address will not change yet.
                                    </p>

                                </div>


                                {{-- Detected Address --}}
                                <div
                                    id="detected-address-panel"
                                    class="
                                        mt-4 hidden rounded-xl
                                        border border-sky-200 bg-sky-50 p-4
                                    ">

                                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                                        <div class="min-w-0">

                                            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-sky-700">
                                                Preview: Detected Address
                                            </p>

                                            <p
                                                id="detected-address-text"
                                                class="mt-1 text-sm font-medium leading-6 text-slate-800">
                                            </p>

                                            <p
                                                id="detected-coordinates-text"
                                                class="mt-1 text-[11px] text-slate-500">
                                            </p>

                                            <p class="mt-2 text-xs leading-5 text-slate-600">
                                                This is only a preview. Your saved
                                                address and coordinates will remain
                                                unchanged unless you click
                                                <strong>Use Detected Address</strong>.
                                            </p>

                                        </div>


                                        <button
                                            type="button"
                                            id="apply-detected-address"
                                            disabled
                                            class="
                                                inline-flex shrink-0 items-center justify-center gap-2
                                                rounded-xl bg-sky-700 px-4 py-2.5
                                                text-sm font-semibold text-white
                                                transition hover:bg-sky-800
                                                disabled:cursor-not-allowed disabled:opacity-50
                                            ">
                                            <i class="fa-solid fa-check"></i>
                                            Use Detected Address
                                        </button>

                                    </div>

                                </div>


                                {{-- Detection Error --}}
                                <div
                                    id="address-detection-error"
                                    class="
                                        mt-4 hidden rounded-xl
                                        border border-amber-200 bg-amber-50
                                        p-3 text-xs leading-5 text-amber-800
                                    ">

                                    <i class="fa-solid fa-triangle-exclamation mr-1"></i>

                                    The readable address could not be detected
                                    for this pin. You can still enter the service
                                    address manually. Your existing address and
                                    coordinates have not been changed.

                                </div>


                                {{-- Coordinates submitted to backend --}}
                                <input
                                    type="hidden"
                                    name="latitude"
                                    id="registration_latitude"
                                    value="{{ old('latitude', $consumer->address?->latitude) }}">

                                <input
                                    type="hidden"
                                    name="longitude"
                                    id="registration_longitude"
                                    value="{{ old('longitude', $consumer->address?->longitude) }}">


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


                            {{-- ========================================= --}}
                            {{-- ADDRESS DETAILS --}}
                            {{-- ========================================= --}}

                            <div>

                                <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-4">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="
                                                flex h-9 w-9 shrink-0 items-center justify-center
                                                rounded-lg bg-white text-sky-700
                                                shadow-sm ring-1 ring-slate-200
                                            ">
                                            <i class="fa-solid fa-house"></i>
                                        </div>

                                        <div>

                                            <p class="text-sm font-semibold text-slate-800">
                                                Registered Service Address
                                            </p>

                                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                                Enter the address where the water
                                                service is registered. Provide at least
                                                <span class="font-semibold text-slate-700">Street or Purok</span>
                                                based on your service location.
                                            </p>

                                        </div>

                                    </div>

                                </div>


                                {{-- House / Building --}}
                                <div class="mb-5">

                                    <label for="house_no" class="mb-1.5 block text-sm font-medium text-slate-700">
                                        House / Building No.
                                        <span class="ml-1 text-xs font-normal text-slate-400">Optional</span>
                                    </label>

                                    <input
                                        id="house_no"
                                        type="text"
                                        name="house_no"
                                        value="{{ old('house_no', $consumer->address?->house_no) }}"
                                        autocomplete="address-line1"
                                        placeholder="e.g. House 24, Blk 3 Lot 5"
                                        class="
                                            w-full rounded-xl border border-slate-300
                                            px-3.5 py-2.5 text-sm outline-none transition
                                            placeholder:text-slate-400
                                            focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                            @error('house_no') border-red-300 @enderror
                                        ">

                                    @error('house_no')
                                        <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                            <i class="fa-solid fa-circle-exclamation"></i>
                                            {{ $message }}
                                        </p>
                                    @enderror

                                </div>


                                {{-- Street / Purok --}}
                                <div class="mb-5">

                                    <div class="mb-3">

                                        <p class="text-sm font-medium text-slate-700">
                                            Street / Purok
                                            <span class="text-red-500">*</span>
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-500">
                                            Enter at least one — Street or Purok —
                                            based on your service address.
                                        </p>

                                    </div>


                                    <div class="grid gap-4 sm:grid-cols-2">

                                        {{-- Street --}}
                                        <div>

                                            <label for="street" class="mb-1.5 block text-sm font-medium text-slate-700">
                                                Street
                                            </label>

                                            <input
                                                id="street"
                                                type="text"
                                                name="street"
                                                value="{{ old('street', $consumer->address?->street) }}"
                                                autocomplete="address-line2"
                                                placeholder="e.g. Rizal Street"
                                                class="
                                                    w-full rounded-xl border border-slate-300
                                                    px-3.5 py-2.5 text-sm outline-none transition
                                                    placeholder:text-slate-400
                                                    focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                                    @error('street') border-red-300 @enderror
                                                ">

                                            @error('street')
                                                <p class="mt-1.5 flex items-start gap-1.5 text-xs leading-5 text-red-600">
                                                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                                                    <span>{{ $message }}</span>
                                                </p>
                                            @enderror

                                        </div>


                                        {{-- Purok --}}
                                        <div>

                                            <label for="purok" class="mb-1.5 block text-sm font-medium text-slate-700">
                                                Purok
                                            </label>

                                            <input
                                                id="purok"
                                                type="text"
                                                name="purok"
                                                value="{{ old('purok', $consumer->address?->purok) }}"
                                                placeholder="e.g. Purok 3"
                                                class="
                                                    w-full rounded-xl border border-slate-300
                                                    px-3.5 py-2.5 text-sm outline-none transition
                                                    placeholder:text-slate-400
                                                    focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                                    @error('purok') border-red-300 @enderror
                                                ">

                                            @error('purok')
                                                <p class="mt-1.5 flex items-start gap-1.5 text-xs leading-5 text-red-600">
                                                    <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                                                    <span>{{ $message }}</span>
                                                </p>
                                            @enderror

                                        </div>

                                    </div>

                                </div>


                                {{-- Barangay / City / Province --}}
                                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">

                                    {{-- Barangay --}}
                                    <div>

                                        <label for="barangay" class="mb-1.5 block text-sm font-medium text-slate-700">
                                            Barangay
                                            <span class="text-red-500">*</span>
                                        </label>

                                        <select
                                            id="barangay"
                                            name="barangay"
                                            required
                                            class="
                                                w-full rounded-xl border border-slate-300
                                                px-3.5 py-2.5 text-sm outline-none transition
                                                focus:border-sky-500 focus:ring-2 focus:ring-sky-100
                                                @error('barangay') border-red-300 @enderror
                                            ">

                                            <option value="">Select barangay</option>

                                            @foreach (config('sagay.barangays', []) as $barangay)
                                                <option
                                                    value="{{ $barangay }}"
                                                    @selected(old('barangay', $consumer->address?->barangay) === $barangay)>
                                                    {{ $barangay }}
                                                </option>
                                            @endforeach

                                        </select>

                                        @error('barangay')
                                            <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                                <i class="fa-solid fa-circle-exclamation"></i>
                                                {{ $message }}
                                            </p>
                                        @enderror

                                    </div>


                                    {{-- Municipality --}}
                                    <div>

                                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                            Municipality / City
                                        </label>

                                        <div
                                            class="
                                                flex min-h-[42px] w-full items-center gap-2
                                                rounded-xl border border-slate-200 bg-slate-50
                                                px-3.5 py-2.5 text-sm text-slate-500
                                            ">

                                            <i class="fa-solid fa-location-dot text-xs text-slate-400"></i>

                                            Sagay City

                                            <i class="fa-solid fa-lock ml-auto text-[9px] text-slate-300"></i>

                                        </div>

                                    </div>


                                    {{-- Province --}}
                                    <div>

                                        <label class="mb-1.5 block text-sm font-medium text-slate-700">
                                            Province
                                        </label>

                                        <div
                                            class="
                                                flex min-h-[42px] w-full items-center gap-2
                                                rounded-xl border border-slate-200 bg-slate-50
                                                px-3.5 py-2.5 text-sm text-slate-500
                                            ">

                                            <i class="fa-solid fa-map text-xs text-slate-400"></i>

                                            Negros Occidental

                                            <i class="fa-solid fa-lock ml-auto text-[9px] text-slate-300"></i>

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>


                        {{-- ============================================= --}}
                        {{-- RESUBMISSION --}}
                        {{-- ============================================= --}}

                        <div class="p-5 sm:p-6">

                            <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="
                                            flex h-9 w-9 shrink-0 items-center justify-center
                                            rounded-lg bg-sky-100 text-sky-700
                                        ">
                                        <i class="fa-solid fa-rotate"></i>
                                    </div>

                                    <div>

                                        <p class="text-sm font-semibold text-sky-900">
                                            Resubmission
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-sky-800">
                                            After resubmission, your corrected
                                            registration will return to Sagay Water
                                            District for another verification review.
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
                                    class="
                                        mt-1 rounded border-slate-300
                                        text-sky-600 focus:ring-sky-500
                                    ">

                                <span class="text-xs leading-5 text-slate-600 sm:text-sm sm:leading-6">
                                    I certify that the corrected information
                                    provided is accurate and belongs to my
                                    registered Sagay Water District service account.
                                </span>

                            </label>


                            @error('terms')
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror


                            <div
                                class="
                                    mt-6 flex flex-col-reverse gap-3
                                    sm:flex-row sm:items-center sm:justify-between
                                ">

                                <a
                                    href="{{ route('consumer.registration.status') }}"
                                    class="
                                        inline-flex items-center justify-center gap-2
                                        rounded-xl border border-slate-300
                                        px-5 py-3 text-sm font-semibold
                                        text-slate-600 transition
                                        hover:bg-slate-50 hover:text-slate-800
                                    ">
                                    <i class="fa-solid fa-arrow-left text-xs"></i>
                                    Cancel
                                </a>


                                <button
                                    type="submit"
                                    :disabled="loading"
                                    class="
                                        inline-flex items-center justify-center
                                        rounded-xl bg-gradient-to-r
                                        from-sky-700 via-blue-700 to-cyan-600
                                        px-6 py-3 text-sm font-semibold text-white
                                        shadow-md transition hover:shadow-lg
                                        disabled:cursor-not-allowed disabled:opacity-70
                                    ">

                                    <span x-show="!loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-paper-plane"></i>
                                        Resubmit Registration
                                    </span>

                                    <span x-cloak x-show="loading" class="flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-spinner animate-spin"></i>
                                        Resubmitting...
                                    </span>

                                </button>

                            </div>

                        </div>

                    </form>


                    <p class="mt-5 text-center text-[11px] text-slate-400">
                        &copy; {{ date('Y') }}
                        Sagay Water District &middot;
                        iSWD Consumer Portal
                    </p>

                </div>

            </div>

        </div>

    </div>

@endsection


{{-- ============================================================= --}}
{{-- LEAFLET --}}
{{-- ============================================================= --}}

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


            /*
            |--------------------------------------------------------------------------
            | Address Fields
            |--------------------------------------------------------------------------
            */

            const houseInput = document.getElementById('house_no');

            const streetInput = document.getElementById('street');

            const purokInput = document.getElementById('purok');

            const barangayInput = document.getElementById('barangay');


            /*
            |--------------------------------------------------------------------------
            | Detection UI
            |--------------------------------------------------------------------------
            */

            const detectedPanel = document.getElementById('detected-address-panel');

            const detectedText = document.getElementById('detected-address-text');

            const detectedCoordinatesText = document.getElementById('detected-coordinates-text');

            const applyDetectedButton = document.getElementById('apply-detected-address');

            const detectionError = document.getElementById('address-detection-error');


            if (!mapElement || !latitudeInput || !longitudeInput || typeof L === 'undefined') {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Default Sagay Location
            |--------------------------------------------------------------------------
            */

            const defaultLat = 10.8990;

            const defaultLng = 123.4140;


            const savedLat = parseFloat(latitudeInput.value);

            const savedLng = parseFloat(longitudeInput.value);

            const hasSavedLocation =
                Number.isFinite(savedLat) &&
                Number.isFinite(savedLng) &&
                savedLat >= -90 &&
                savedLat <= 90 &&
                savedLng >= -180 &&
                savedLng <= 180;


            /*
            |--------------------------------------------------------------------------
            | Initialize Map
            |--------------------------------------------------------------------------
            */

            const map = L.map('registration-map', {
                zoomControl: true
            }).setView(
                hasSavedLocation ?
                [savedLat, savedLng] :
                [defaultLat, defaultLng],
                hasSavedLocation ? 17 : 14
            );


            const streetLayer = L.tileLayer(
                'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }
            );


            const satelliteLayer = L.tileLayer(
                'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
                {
                    maxZoom: 19,
                    attribution: 'Tiles &copy; Esri'
                }
            );


            streetLayer.addTo(map);


            L.control.layers(
                {
                    'Street Map': streetLayer,
                    'Satellite': satelliteLayer
                },
                null,
                {
                    position: 'topright'
                }
            ).addTo(map);


            let marker = null;

            /*
             * This stores the temporary detected location.
             * It does NOT update the hidden form coordinates.
             */
            let stagedLocation = null;

            let reverseGeocodeRequest = 0;


            /*
            |--------------------------------------------------------------------------
            | Helpers
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Match Detected Barangay With Dropdown
            |--------------------------------------------------------------------------
            */

            function findBarangayOption(value) {

                if (!barangayInput || !value) {
                    return null;
                }

                const normalizedValue = normalizeBarangay(value).toLowerCase();

                const options = Array.from(barangayInput.options || []);

                return options.find(option => {

                    return normalizeBarangay(option.value).toLowerCase() === normalizedValue;

                }) || null;

            }


            /*
            |--------------------------------------------------------------------------
            | Build Detected Address
            |--------------------------------------------------------------------------
            */

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
                    address.footway,
                    address.path
                );

                /*
                 * Only use a value as Purok when Nominatim explicitly
                 * identifies it as a Purok, Sitio, or Zone.
                 *
                 * Do not automatically treat every neighbourhood as Purok.
                 */
                const purok = firstValue(
                    address.purok,
                    address.sitio,
                    address.zone
                );

                const barangay = normalizeBarangay(
                    firstValue(
                        address.suburb,
                        address.village,
                        address.hamlet,
                        address.city_district
                    )
                );

                return {

                    houseNo: houseNo,

                    street: street,

                    purok: purok,

                    barangay: barangay,

                    displayName: data?.display_name || ''

                };

            }


            /*
            |--------------------------------------------------------------------------
            | Enable / Disable Apply Button
            |--------------------------------------------------------------------------
            */

            function setApplyButtonEnabled(enabled) {

                if (!applyDetectedButton) {
                    return;
                }

                applyDetectedButton.disabled = !enabled;

                if (enabled) {

                    applyDetectedButton.classList.remove(
                        'opacity-50',
                        'cursor-not-allowed'
                    );

                } else {

                    applyDetectedButton.classList.add(
                        'opacity-50',
                        'cursor-not-allowed'
                    );

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Detection UI
            |--------------------------------------------------------------------------
            */

            function showDetectionLoading(latitude, longitude) {

                stagedLocation = null;

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

                if (detectedCoordinatesText) {

                    detectedCoordinatesText.textContent =
                        'Selected coordinates: ' +
                        latitude.toFixed(7) + ', ' +
                        longitude.toFixed(7);

                }

                setApplyButtonEnabled(false);

                if (detectionError) {
                    detectionError.classList.add('hidden');
                }

            }


            function showDetectedAddress(result) {

                stagedLocation = result;

                if (detectedPanel) {
                    detectedPanel.classList.remove('hidden');
                }

                if (detectedText) {

                    detectedText.textContent =
                        result.displayName ||
                        'A nearby address was detected from the selected map location.';

                }

                if (detectedCoordinatesText) {

                    detectedCoordinatesText.textContent =
                        'Selected coordinates: ' +
                        result.latitude.toFixed(7) + ', ' +
                        result.longitude.toFixed(7);

                }

                setApplyButtonEnabled(true);

                if (detectionError) {
                    detectionError.classList.add('hidden');
                }

                if (statusText) {

                    statusText.textContent =
                        'Address detected. Review the preview and select Use Detected Address if you want to apply this location.';

                }

            }


            function showDetectionFailure(latitude, longitude) {

                stagedLocation = null;

                if (detectedPanel) {
                    detectedPanel.classList.add('hidden');
                }

                if (detectionError) {
                    detectionError.classList.remove('hidden');
                }

                setApplyButtonEnabled(false);

                if (statusText) {

                    statusText.textContent =
                        'Address detection failed. Your existing address and coordinates remain unchanged. You can still enter the address manually.';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Reverse Geocoding
            |--------------------------------------------------------------------------
            */

            async function reverseGeocode(latitude, longitude) {

                const requestId = ++reverseGeocodeRequest;

                showDetectionLoading(latitude, longitude);

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

                    /*
                     * Ignore old responses when the user has already
                     * selected another map location.
                     */
                    if (requestId !== reverseGeocodeRequest) {
                        return;
                    }

                    const detected = buildDetectedAddress(data);

                    showDetectedAddress({

                        latitude: latitude,

                        longitude: longitude,

                        houseNo: detected.houseNo,

                        street: detected.street,

                        purok: detected.purok,

                        barangay: detected.barangay,

                        displayName: detected.displayName

                    });

                } catch (error) {

                    console.error('Reverse geocoding error:', error);

                    if (requestId === reverseGeocodeRequest) {

                        showDetectionFailure(latitude, longitude);

                    }

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Set Temporary Map Location
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            | This function updates only the map marker and preview.
            | It does NOT modify latitudeInput or longitudeInput.
            |
            */

            function setLocation(latitude, longitude, options = {}) {

                const lat = Number(latitude);

                const lng = Number(longitude);

                if (
                    !Number.isFinite(lat) ||
                    !Number.isFinite(lng) ||
                    lat < -90 ||
                    lat > 90 ||
                    lng < -180 ||
                    lng > 180
                ) {
                    return;
                }


                if (marker) {

                    marker.setLatLng([lat, lng]);

                } else {

                    marker = L.marker(
                        [lat, lng],
                        {
                            draggable: true
                        }
                    ).addTo(map);


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
                        'New location selected for preview. Your registered address has not changed.';

                }


                if (options.detectAddress !== false) {

                    reverseGeocode(lat, lng);

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Restore Saved Location
            |--------------------------------------------------------------------------
            |
            | The saved marker is displayed without replacing form values.
            |
            */

            if (hasSavedLocation) {

                marker = L.marker(
                    [savedLat, savedLng],
                    {
                        draggable: true
                    }
                ).addTo(map);


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


                if (statusText) {

                    statusText.textContent =
                        'Your currently registered location is shown. Move the pin only if you want to preview a different location.';

                }

            }


            /*
            |--------------------------------------------------------------------------
            | Map Click
            |--------------------------------------------------------------------------
            */

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


            /*
            |--------------------------------------------------------------------------
            | Apply Detected Address
            |--------------------------------------------------------------------------
            |
            | This is the ONLY place where detected coordinates and
            | detected address values are copied into the form.
            |
            */

            if (applyDetectedButton) {

                applyDetectedButton.addEventListener('click', function() {

                    if (!stagedLocation) {
                        return;
                    }


                    const confirmed = window.confirm(
                        'Apply this detected location to your registration form? ' +
                        'Your current address fields and coordinates will be replaced ' +
                        'only where a detected value is available.'
                    );


                    if (!confirmed) {
                        return;
                    }


                    /*
                     * Apply coordinates only after explicit confirmation.
                     */
                    latitudeInput.value = stagedLocation.latitude.toFixed(7);

                    longitudeInput.value = stagedLocation.longitude.toFixed(7);


                    /*
                     * Apply detected address details only when available.
                     * Existing values are preserved when no value was detected.
                     */
                    if (houseInput && stagedLocation.houseNo) {

                        houseInput.value = stagedLocation.houseNo;

                    }


                    if (streetInput && stagedLocation.street) {

                        streetInput.value = stagedLocation.street;

                    }


                    if (purokInput && stagedLocation.purok) {

                        purokInput.value = stagedLocation.purok;

                    }


                    /*
                     * Only apply barangay when it exactly matches
                     * an available Sagay barangay option.
                     */
                    if (barangayInput && stagedLocation.barangay) {

                        const matchedOption = findBarangayOption(
                            stagedLocation.barangay
                        );

                        if (matchedOption) {

                            barangayInput.value = matchedOption.value;

                        }

                    }


                    if (statusText) {

                        statusText.textContent =
                            'Detected location applied to the form. Review all address fields before resubmitting.';

                    }


                    applyDetectedButton.innerHTML =
                        '<i class="fa-solid fa-check"></i> Address Applied';

                    applyDetectedButton.disabled = true;

                    if (detectedText) {

                        detectedText.textContent =
                            'This detected address has been applied to your registration form. You may still edit the address fields manually.';

                    }

                });

            }


            /*
            |--------------------------------------------------------------------------
            | Use My Location
            |--------------------------------------------------------------------------
            */

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


                            /*
                             * Only preview the device location.
                             * Do not update the hidden form coordinates.
                             */
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
                                    'Device location found. Review the detected address, then choose Use Detected Address if you want to apply it.';

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
                                    'Location permission is blocked. Allow location access for this site in your browser, then try again. You can also click the map to place the service-location pin manually.';

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
                            timeout: 15000,
                            maximumAge: 0
                        }

                    );

                });

            }


            /*
            |--------------------------------------------------------------------------
            | Leaflet Resize Fix
            |--------------------------------------------------------------------------
            */

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
