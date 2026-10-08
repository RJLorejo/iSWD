@extends('consumer.auth.layout')

@section('title', 'Forgot Password')

@section('content')

    <div
        class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
        x-data="{
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


                    <a
                        href="{{ route('consumer.login') }}"
                        class="
                            flex h-9 w-9
                            items-center justify-center
                            rounded-lg
                            border border-slate-200
                            bg-white
                            text-slate-500
                            transition
                            hover:border-sky-200
                            hover:text-sky-700
                        "
                    >
                        <i class="fa-solid fa-arrow-left text-xs"></i>
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
                            bg-sky-50
                            text-sky-700
                        "
                    >
                        <i class="fa-solid fa-key"></i>
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
                        Forgot Password?
                    </h1>


                    <p class="mt-1.5 text-sm leading-6 text-slate-500">
                        Enter the email address registered to your
                        Consumer Portal account. We'll send a
                        6-digit verification code to your email.
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

                            <i
                                class="
                                    fa-solid fa-circle-check
                                    mt-0.5
                                    text-emerald-600
                                "
                            ></i>

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

                            <i
                                class="
                                    fa-solid fa-circle-exclamation
                                    mt-0.5
                                    text-red-600
                                "
                            ></i>

                            <p class="text-sm leading-5 text-red-700">
                                {{ $errors->first() }}
                            </p>

                        </div>

                    @endif


                    <form
                        method="POST"
                        action="{{ route('consumer.password.email') }}"
                        class="mt-6 space-y-5"
                        @submit="loading = true"
                    >

                        @csrf


                        <div>

                            <label
                                for="email"
                                class="
                                    mb-1.5
                                    block
                                    text-sm font-medium
                                    text-slate-700
                                "
                            >
                                Registered Email Address
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
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>


                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autofocus
                                    autocomplete="email"
                                    placeholder="you@example.com"
                                    class="
                                        w-full
                                        rounded-xl
                                        border border-slate-300
                                        bg-white
                                        py-2.5
                                        pl-10
                                        pr-4
                                        text-sm
                                        text-slate-800
                                        outline-none
                                        transition
                                        placeholder:text-slate-400
                                        focus:border-sky-500
                                        focus:ring-2
                                        focus:ring-sky-100
                                        @error('email')
                                            border-red-300
                                        @enderror
                                    "
                                >

                            </div>

                        </div>


                        <div
                            class="
                                rounded-xl
                                border border-sky-100
                                bg-sky-50
                                p-3.5
                            "
                        >

                            <div class="flex items-start gap-3">

                                <i
                                    class="
                                        fa-solid fa-shield-halved
                                        mt-0.5
                                        text-sky-600
                                    "
                                ></i>

                                <p class="text-xs leading-5 text-sky-700">
                                    The verification code contains
                                    6 digits and expires after
                                    5 minutes.
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
                                <i class="fa-solid fa-paper-plane"></i>
                                Send Verification Code
                            </span>


                            <span
                                x-cloak
                                x-show="loading"
                                class="flex items-center gap-2"
                            >
                                <i class="fa-solid fa-spinner animate-spin"></i>
                                Sending Code...
                            </span>

                        </button>

                    </form>


                    <div class="mt-5 border-t border-slate-100 pt-5 text-center">

                        <p class="text-xs text-slate-500">
                            Remember your password?
                        </p>


                        <a
                            href="{{ route('consumer.login') }}"
                            class="
                                mt-2
                                inline-flex
                                items-center gap-2
                                text-sm font-semibold
                                text-sky-700
                                transition
                                hover:text-sky-800
                            "
                        >
                            <i class="fa-solid fa-arrow-left text-xs"></i>
                            Back to Consumer Login
                        </a>

                    </div>

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
                    <i class="fa-solid fa-shield-halved"></i>
                    Secure Account Recovery
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
                    Verify your identity before changing your password
                </h2>


                <p class="mt-4 text-sm leading-7 text-sky-50/90 xl:text-base">
                    A one-time verification code will be sent to
                    your registered Consumer Portal email address.
                </p>


                <div class="mt-8 space-y-3">

                    <div
                        class="
                            flex items-center gap-4
                            rounded-xl
                            border border-white/15
                            bg-white/10
                            p-4
                        "
                    >

                        <div
                            class="
                                flex h-10 w-10
                                items-center justify-center
                                rounded-lg
                                bg-white/10
                            "
                        >
                            <i class="fa-solid fa-envelope"></i>
                        </div>


                        <div>

                            <p class="text-sm font-semibold">
                                Email Verification
                            </p>

                            <p class="mt-0.5 text-xs text-sky-100/80">
                                Receive a secure 6-digit code through
                                your registered email.
                            </p>

                        </div>

                    </div>


                    <div
                        class="
                            flex items-center gap-4
                            rounded-xl
                            border border-white/15
                            bg-white/10
                            p-4
                        "
                    >

                        <div
                            class="
                                flex h-10 w-10
                                items-center justify-center
                                rounded-lg
                                bg-white/10
                            "
                        >
                            <i class="fa-solid fa-clock"></i>
                        </div>


                        <div>

                            <p class="text-sm font-semibold">
                                Time-Limited Code
                            </p>

                            <p class="mt-0.5 text-xs text-sky-100/80">
                                The code expires after 5 minutes for
                                additional account protection.
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
