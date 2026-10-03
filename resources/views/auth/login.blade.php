@extends('layouts.auth')

@section('title', 'Employee Login')

@section('content')

    <div class="relative min-h-screen overflow-hidden bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700"
        x-data="{
            showPassword: false,
            loading: false
        }">
        <div class="pointer-events-none absolute inset-0 overflow-hidden">
            <div class="absolute -left-32 -top-32 h-96 w-96 rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-24 h-[28rem] w-[28rem] rounded-full bg-cyan-300/10 blur-3xl"></div>

            <div class="absolute inset-0 opacity-[0.05]"
                style="background-image:
                linear-gradient(rgba(255,255,255,.8) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.8) 1px, transparent 1px);
                background-size: 48px 48px;">
            </div>
        </div>

        <div class="relative z-10 flex min-h-screen">
            <div class="hidden w-[46%] items-center px-10 py-10 text-white lg:flex xl:px-16">
                <div class="mx-auto w-full max-w-xl">
                    <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl border border-white/20 bg-white p-1.5 shadow-lg">
                            <img src="{{ asset('images/logo/logo.png') }}" alt="Sagay Water District Logo"
                                class="h-full w-full object-contain">
                        </div>

                        <div>
                            <p class="text-xl font-semibold tracking-tight">
                                iSWD
                            </p>

                            <p class="text-xs font-medium text-sky-100">
                                Sagay Water District
                            </p>
                        </div>
                    </a>

                    <div class="mt-10">
                        <span
                            class="inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-xs font-medium text-sky-50 backdrop-blur-sm">
                            <i class="fa-solid fa-shield-halved"></i>
                            Authorized Personnel Access
                        </span>

                        <h1 class="mt-5 max-w-lg text-3xl font-semibold leading-tight tracking-tight xl:text-4xl">
                            Complaint Management and Service Support in One Platform
                        </h1>

                        <p class="mt-5 max-w-lg text-sm leading-7 text-sky-50/90 xl:text-base">
                            iSWD helps Sagay Water District personnel receive, assess, route, monitor, and resolve consumer
                            service concerns through a coordinated digital workflow supported by AI-assisted
                            recommendations.
                        </p>
                    </div>

                    <div class="mt-8 grid grid-cols-2 gap-3">
                        <div class="rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                    <i class="fa-solid fa-file-circle-check text-sm"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        Complaint Management
                                    </p>
                                    <p class="mt-0.5 text-xs text-sky-100/80">
                                        Verify and track concerns
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                    <i class="fa-solid fa-wand-magic-sparkles text-sm"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        AI Assistance
                                    </p>
                                    <p class="mt-0.5 text-xs text-sky-100/80">
                                        Decision-support recommendations
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                    <i class="fa-solid fa-route text-sm"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        Service Coordination
                                    </p>
                                    <p class="mt-0.5 text-xs text-sky-100/80">
                                        Route and assign service work
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-white/15 bg-white/10 p-4 backdrop-blur-sm">
                            <div class="flex items-center gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white/10">
                                    <i class="fa-solid fa-chart-line text-sm"></i>
                                </div>

                                <div>
                                    <p class="text-sm font-semibold">
                                        Monitoring
                                    </p>
                                    <p class="mt-0.5 text-xs text-sky-100/80">
                                        Track service performance
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex items-center gap-3 border-t border-white/15 pt-5">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-white/10">
                            <i class="fa-solid fa-user-lock text-sm"></i>
                        </div>

                        <p class="text-xs leading-5 text-sky-100/80">
                            Access is restricted to authorized Sagay Water District personnel.
                        </p>
                    </div>
                </div>
            </div>

            <div
                class="flex w-full items-center justify-center bg-slate-50 px-4 py-5 sm:px-6 lg:w-[54%] lg:rounded-l-[2.5rem] lg:px-10 xl:px-16">
                <div class="w-full max-w-md">
                    <div class="mb-5 flex items-center justify-between lg:hidden">
                        <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
                            <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD Logo" class="h-10 w-10 object-contain">

                            <div>
                                <p class="text-lg font-semibold leading-none text-sky-700">
                                    iSWD
                                </p>

                                <p class="mt-1 text-[10px] font-medium uppercase tracking-wider text-slate-500">
                                    Sagay Water District
                                </p>
                            </div>
                        </a>

                        <a href="{{ route('landing') }}"
                            class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-500 transition hover:text-sky-700"
                            aria-label="Back to home">
                            <i class="fa-solid fa-house text-xs"></i>
                        </a>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-7">
                        <div>
                            <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <i class="fa-solid fa-user-shield"></i>
                            </div>

                            <h2 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                                Employee Login
                            </h2>

                            <p class="mt-1.5 text-sm leading-6 text-slate-500">
                                Sign in with your authorized employee account.
                            </p>
                        </div>

                        @if (session('status'))
                            <div
                                class="mt-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5">
                                <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>

                                <p class="text-sm leading-5 text-emerald-700">
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

                        <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-4" @submit="loading = true">
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
                                        autofocus autocomplete="email" placeholder="employee@example.com"
                                        class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                </div>
                            </div>

                            <div>
                                <div class="mb-1.5 flex items-center justify-between">
                                    <label for="password" class="block text-sm font-medium text-slate-700">
                                        Password
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}"
                                            class="text-xs font-semibold text-sky-700 transition hover:text-sky-800">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <div class="relative">
                                    <div
                                        class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                                        <i class="fa-solid fa-lock text-sm"></i>
                                    </div>

                                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password"
                                        required autocomplete="current-password" placeholder="Enter your password"
                                        class="w-full rounded-xl border border-slate-300 bg-white py-2.5 pl-10 pr-11 text-sm text-slate-800 outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                                    <button type="button" @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 flex w-11 items-center justify-center text-slate-400 transition hover:text-sky-700"
                                        :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                        <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                    </button>
                                </div>
                            </div>

                            <label class="inline-flex cursor-pointer items-center gap-2.5">
                                <input type="checkbox" name="remember" value="1"
                                    class="rounded border-slate-300 text-sky-600 focus:ring-sky-500">

                                <span class="text-sm text-slate-600">
                                    Remember me
                                </span>
                            </label>

                            <button type="submit" :disabled="loading"
                                class="inline-flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg disabled:cursor-not-allowed disabled:opacity-70">
                                <span x-show="!loading" class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-right-to-bracket"></i>
                                    Sign In to iSWD
                                </span>

                                <span x-cloak x-show="loading" class="flex items-center justify-center gap-2">
                                    <i class="fa-solid fa-spinner animate-spin"></i>
                                    Signing In...
                                </span>
                            </button>
                        </form>

                        <div class="mt-5 border-t border-slate-100 pt-5 text-center">
                            <p class="text-xs text-slate-500">
                                Are you a water service consumer?
                            </p>

                            <a href="{{ route('consumer.login') }}"
                                class="mt-2 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 transition hover:text-sky-800">
                                <i class="fa-solid fa-droplet text-xs"></i>
                                Open Consumer Portal
                            </a>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-center gap-3 text-[11px] text-slate-400">
                        <span>
                            &copy; {{ date('Y') }} Sagay Water District
                        </span>

                        <span class="h-1 w-1 rounded-full bg-slate-300"></span>

                        <a href="{{ route('landing') }}" class="transition hover:text-sky-700">
                            Back to iSWD
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
