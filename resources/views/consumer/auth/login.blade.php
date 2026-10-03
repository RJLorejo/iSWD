@extends('consumer.auth.layout')

@section('title', 'Consumer Login')

@section('content')

    <div class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]" x-data="{
        showPassword: false,
        loading: false
    }">
        <div class="flex min-h-screen items-center justify-center px-4 py-5 sm:px-6 lg:px-10 xl:px-16">
            <div class="w-full max-w-md">
                <div class="mb-5 flex items-center justify-between">
                    <a href="{{ route('landing') }}" class="flex items-center gap-3">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white shadow-sm">
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

                    <a href="{{ route('landing') }}"
                        class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:border-sky-200 hover:text-sky-700"
                        aria-label="Back to iSWD home">
                        <i class="fa-solid fa-house text-xs"></i>
                    </a>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-7">
                    <div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                            Consumer Portal
                        </h1>

                        <p class="mt-1.5 text-sm leading-6 text-slate-500">
                            Sign in to submit and monitor your water service concerns.
                        </p>
                    </div>

                    @if (session('success'))
                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5">
                            <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>

                            <p class="text-sm leading-5 text-emerald-700">
                                {{ session('success') }}
                            </p>
                        </div>
                    @endif

                    @if (session('status'))
                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-sky-200 bg-sky-50 p-3.5">
                            <i class="fa-solid fa-circle-info mt-0.5 text-sky-600"></i>

                            <p class="text-sm leading-5 text-sky-700">
                                {{ session('status') }}
                            </p>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-3.5">
                            <i class="fa-solid fa-circle-exclamation mt-0.5 text-red-600"></i>

                            <p class="text-sm leading-5 text-red-700">
                                {{ $errors->first() }}
                            </p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('consumer.login') }}" class="mt-6 space-y-4"
                        @submit="loading = true">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">
                                Email Address
                            </label>

                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fa-solid fa-envelope text-sm"></i>
                                </div>

                                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                                    autofocus autocomplete="email" placeholder="you@example.com"
                                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('email') border-red-300 @enderror">
                            </div>

                            @error('email')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
                                Password
                            </label>

                            <div class="relative">
                                <div
                                    class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                    <i class="fa-solid fa-lock text-sm"></i>
                                </div>

                                <input id="password" :type="showPassword ? 'text' : 'password'" name="password" required
                                    autocomplete="current-password" placeholder="Enter your password"
                                    class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100 @error('password') border-red-300 @enderror">

                                <button type="button" @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                    :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-1.5 flex items-center gap-1.5 text-xs text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" name="remember" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                                <span class="text-sm text-slate-600">
                                    Remember me
                                </span>
                            </label>

                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-400">
                                <i class="fa-solid fa-lock"></i>
                                Secure access
                            </span>
                        </div>

                        <button type="submit" :disabled="loading"
                            class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70">
                            <span x-show="!loading" class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-right-to-bracket"></i>
                                Sign In to Consumer Portal
                            </span>

                            <span x-cloak x-show="loading" class="flex items-center justify-center gap-2">
                                <i class="fa-solid fa-spinner animate-spin"></i>
                                Signing In...
                            </span>
                        </button>
                    </form>

                    <div class="mt-5 border-t border-slate-100 pt-5">
                        <div class="rounded-xl bg-slate-50 p-4 text-center">
                            <p class="text-xs text-slate-500">
                                Don't have a consumer portal account?
                            </p>

                            <a href="{{ route('consumer.register') }}"
                                class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 transition hover:text-sky-800">
                                <i class="fa-solid fa-user-plus text-xs"></i>
                                Create Consumer Account
                                <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <p class="mt-4 text-center text-[11px] text-slate-400">
                    &copy; {{ date('Y') }} Sagay Water District &middot; iSWD Consumer Portal
                </p>
            </div>
        </div>

        <div
            class="relative hidden overflow-hidden bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700 text-white lg:flex lg:items-center">
            <div class="pointer-events-none absolute inset-0">
                <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full bg-white/10 blur-3xl"></div>
                <div class="absolute -bottom-32 -left-20 h-96 w-96 rounded-full bg-cyan-300/10 blur-3xl"></div>

                <div class="absolute inset-0 opacity-[0.05]"
                    style="background-image:
                    linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px);
                    background-size: 48px 48px;">
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-lg px-10 xl:px-14">
                <span
                    class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-medium text-sky-50 backdrop-blur-sm">
                    <i class="fa-solid fa-droplet"></i>
                    Consumer Service Access
                </span>

                <h2 class="mt-5 text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">
                    Your water service concerns, easier to submit and track
                </h2>

                <p class="mt-4 text-sm leading-7 text-sky-50/90 xl:text-base">
                    Use the iSWD Consumer Portal to report service concerns, provide complaint information, and follow the
                    progress of your complaint through resolution.
                </p>

                <div class="mt-8 space-y-3">
                    <div
                        class="flex items-center gap-4 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                            <i class="fa-solid fa-file-circle-plus"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">
                                Submit Service Concerns
                            </p>

                            <p class="mt-0.5 text-xs text-sky-100/80">
                                Provide complaint details, location, and supporting information.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                            <i class="fa-solid fa-list-check"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">
                                Monitor Complaint Progress
                            </p>

                            <p class="mt-0.5 text-xs text-sky-100/80">
                                Follow your concern as it moves through the service workflow.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center gap-4 rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>

                        <div>
                            <p class="text-sm font-semibold">
                                Verified Consumer Access
                            </p>

                            <p class="mt-0.5 text-xs text-sky-100/80">
                                Self-registered accounts are reviewed before portal activation.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-7 border-t border-white/15 pt-5">
                    <a href="{{ route('landing') }}"
                        class="inline-flex items-center gap-2 text-sm font-medium text-sky-100 transition hover:text-white">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Return to iSWD Home
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
