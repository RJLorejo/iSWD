@extends('consumer.auth.layout')

@section('title', 'Create New Password')

@section('content')

    <div
        class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
        x-data="{
            showPassword: false,
            showConfirmation: false,
            loading: false
        }"
    >

        {{-- LEFT --}}
        <div
            class="
                flex min-h-screen
                items-center justify-center
                px-4 py-5
                sm:px-6
                lg:px-10
                xl:px-16
            "
        >

            <div class="w-full max-w-md">

                {{-- Brand --}}
                <div class="mb-5 flex items-center justify-between">

                    <a
                        href="{{ route('landing') }}"
                        class="flex items-center gap-3"
                    >

                        <div
                            class="
                                flex h-11 w-11
                                items-center justify-center
                                rounded-xl
                                border border-sky-100
                                bg-white
                                shadow-sm
                            "
                        >

                            <img
                                src="{{ asset('images/logo/logo.png') }}"
                                alt="iSWD Logo"
                                class="h-9 w-9 object-contain"
                            >

                        </div>


                        <div>

                            <p class="text-xl font-semibold leading-none text-sky-700">
                                iSWD
                            </p>

                            <p
                                class="
                                    mt-1
                                    text-[10px]
                                    font-medium
                                    uppercase
                                    tracking-[0.12em]
                                    text-slate-500
                                "
                            >
                                Sagay Water District
                            </p>

                        </div>

                    </a>

                </div>


                {{-- Card --}}
                <div
                    class="
                        rounded-2xl
                        border border-slate-200
                        bg-white
                        p-6
                        shadow-xl
                        shadow-slate-200/60
                        sm:p-7
                    "
                >

                    <div
                        class="
                            flex h-11 w-11
                            items-center justify-center
                            rounded-xl
                            bg-emerald-50
                            text-emerald-700
                        "
                    >
                        <i class="fa-solid fa-lock-open"></i>
                    </div>


                    <h1
                        class="
                            mt-4
                            text-2xl
                            font-semibold
                            tracking-tight
                            text-slate-900
                        "
                    >
                        Create New Password
                    </h1>


                    <p class="mt-1.5 text-sm leading-6 text-slate-500">
                        Your email has been verified. Create a new
                        password for your Consumer Portal account.
                    </p>


                    @if (session('status'))

                        <div
                            class="
                                mt-5
                                flex items-start gap-3
                                rounded-xl
                                border border-emerald-200
                                bg-emerald-50
                                p-3.5
                            "
                        >

                            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>

                            <p class="text-sm leading-5 text-emerald-700">
                                {{ session('status') }}
                            </p>

                        </div>

                    @endif


                    @if ($errors->any())

                        <div
                            class="
                                mt-5
                                flex items-start gap-3
                                rounded-xl
                                border border-red-200
                                bg-red-50
                                p-3.5
                            "
                        >

                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-600"></i>

                            <p class="text-sm leading-5 text-red-700">
                                {{ $errors->first() }}
                            </p>

                        </div>

                    @endif


                    <div
                        class="
                            mt-5
                            rounded-xl
                            border border-slate-200
                            bg-slate-50
                            px-4 py-3
                        "
                    >

                        <p class="text-[11px] font-medium uppercase tracking-wide text-slate-400">
                            Resetting password for
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $email }}
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('consumer.password.update') }}"
                        class="mt-5 space-y-4"
                        @submit="loading = true"
                    >

                        @csrf


                        {{-- Password --}}
                        <div>

                            <label
                                for="password"
                                class="
                                    mb-1.5
                                    block
                                    text-sm font-medium
                                    text-slate-700
                                "
                            >
                                New Password
                            </label>


                            <div class="relative">

                                <div
                                    class="
                                        pointer-events-none
                                        absolute inset-y-0 left-0
                                        flex items-center
                                        pl-3.5
                                        text-slate-400
                                    "
                                >
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>


                                <input
                                    id="password"
                                    :type="showPassword ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autofocus
                                    autocomplete="new-password"
                                    placeholder="Enter new password"
                                    class="
                                        w-full
                                        rounded-xl
                                        border border-slate-300
                                        bg-white
                                        py-2.5
                                        pl-10
                                        pr-11
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        focus:border-sky-500
                                        focus:ring-2
                                        focus:ring-sky-100
                                    "
                                >


                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="
                                        absolute inset-y-0 right-0
                                        flex w-11
                                        items-center justify-center
                                        text-slate-400
                                        transition
                                        hover:text-sky-700
                                    "
                                >

                                    <i
                                        class="fa-solid text-sm"
                                        :class="
                                            showPassword
                                                ? 'fa-eye-slash'
                                                : 'fa-eye'
                                        "
                                    ></i>

                                </button>

                            </div>

                        </div>


                        {{-- Confirm --}}
                        <div>

                            <label
                                for="password_confirmation"
                                class="
                                    mb-1.5
                                    block
                                    text-sm font-medium
                                    text-slate-700
                                "
                            >
                                Confirm New Password
                            </label>


                            <div class="relative">

                                <div
                                    class="
                                        pointer-events-none
                                        absolute inset-y-0 left-0
                                        flex items-center
                                        pl-3.5
                                        text-slate-400
                                    "
                                >
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>


                                <input
                                    id="password_confirmation"
                                    :type="
                                        showConfirmation
                                            ? 'text'
                                            : 'password'
                                    "
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Confirm new password"
                                    class="
                                        w-full
                                        rounded-xl
                                        border border-slate-300
                                        bg-white
                                        py-2.5
                                        pl-10
                                        pr-11
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        focus:border-sky-500
                                        focus:ring-2
                                        focus:ring-sky-100
                                    "
                                >


                                <button
                                    type="button"
                                    @click="
                                        showConfirmation =
                                            !showConfirmation
                                    "
                                    class="
                                        absolute inset-y-0 right-0
                                        flex w-11
                                        items-center justify-center
                                        text-slate-400
                                        transition
                                        hover:text-sky-700
                                    "
                                >

                                    <i
                                        class="fa-solid text-sm"
                                        :class="
                                            showConfirmation
                                                ? 'fa-eye-slash'
                                                : 'fa-eye'
                                        "
                                    ></i>

                                </button>

                            </div>

                        </div>


                        {{-- Requirements --}}
                        <div
                            class="
                                rounded-xl
                                border border-slate-200
                                bg-slate-50
                                p-3.5
                            "
                        >

                            <p class="text-xs font-semibold text-slate-600">
                                Password requirements
                            </p>


                            <div class="mt-2 space-y-1.5 text-xs text-slate-500">

                                <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                    At least 8 characters
                                </p>

                                <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                    Uppercase and lowercase letters
                                </p>

                                <p class="flex items-center gap-2">
                                    <i class="fa-solid fa-check text-emerald-500"></i>
                                    At least one number
                                </p>

                            </div>

                        </div>


                        <button
                            type="submit"
                            :disabled="loading"
                            class="
                                inline-flex w-full
                                items-center justify-center
                                rounded-xl
                                bg-gradient-to-r
                                from-sky-700
                                via-blue-700
                                to-cyan-600
                                px-5 py-3
                                text-sm font-semibold
                                text-white
                                shadow-md
                                transition
                                hover:shadow-lg
                                disabled:cursor-not-allowed
                                disabled:opacity-70
                            "
                        >

                            <span
                                x-show="!loading"
                                class="flex items-center gap-2"
                            >
                                <i class="fa-solid fa-key"></i>
                                Update Password
                            </span>


                            <span
                                x-cloak
                                x-show="loading"
                                class="flex items-center gap-2"
                            >
                                <i class="fa-solid fa-spinner animate-spin"></i>
                                Updating Password...
                            </span>

                        </button>

                    </form>

                </div>


                <p class="mt-4 text-center text-[11px] text-slate-400">
                    &copy; {{ date('Y') }}
                    Sagay Water District &middot;
                    iSWD Consumer Portal
                </p>

            </div>

        </div>


        {{-- RIGHT --}}
        <div
            class="
                relative hidden
                overflow-hidden
                bg-gradient-to-b
                from-sky-700
                via-blue-700
                to-cyan-700
                text-white
                lg:flex
                lg:items-center
            "
        >

            <div class="relative mx-auto w-full max-w-lg px-10 xl:px-14">

                <span
                    class="
                        inline-flex
                        items-center gap-2
                        rounded-full
                        border border-white/20
                        bg-white/10
                        px-3.5 py-1.5
                        text-xs font-medium
                        text-sky-50
                    "
                >
                    <i class="fa-solid fa-key"></i>
                    Final Step
                </span>


                <h2
                    class="
                        mt-5
                        text-3xl
                        font-semibold
                        leading-tight
                        tracking-tight
                        xl:text-4xl
                    "
                >
                    Secure your Consumer Portal account
                </h2>


                <p class="mt-4 text-sm leading-7 text-sky-50/90 xl:text-base">
                    Your identity has been verified. Choose a
                    strong new password to complete account recovery.
                </p>


                <div
                    class="
                        mt-8
                        rounded-xl
                        border border-white/15
                        bg-white/10
                        p-5
                    "
                >

                    <div class="flex items-start gap-4">

                        <div
                            class="
                                flex h-10 w-10
                                shrink-0
                                items-center justify-center
                                rounded-lg
                                bg-white/10
                            "
                        >
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>


                        <div>

                            <p class="text-sm font-semibold">
                                Your account information stays unchanged
                            </p>

                            <p class="mt-1 text-xs leading-5 text-sky-100/80">
                                Resetting your password does not change
                                your Consumer registration or SWD
                                verification status.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
