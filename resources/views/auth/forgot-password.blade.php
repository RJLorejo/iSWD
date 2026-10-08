
@extends('layouts.auth')

@section('title', 'Employee Password Recovery')

@section('content')

<div class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
     x-data="{ loading: false }">

    <div class="flex min-h-screen items-center justify-center px-4 py-6 sm:px-6 lg:px-10 xl:px-16">
        <div class="w-full max-w-md">

            <a href="{{ route('landing') }}" class="mb-5 flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-sky-100 bg-white shadow-sm">
                    <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD" class="h-9 w-9 object-contain">
                </div>
                <div>
                    <p class="text-xl font-semibold text-sky-700">iSWD</p>
                    <p class="text-[10px] uppercase tracking-widest text-slate-500">Sagay Water District</p>
                </div>
            </a>

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xl shadow-slate-200/60 sm:p-7">

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                    <i class="fa-solid fa-key"></i>
                </div>

                <h1 class="mt-5 text-2xl font-semibold text-slate-900">Forgot Password?</h1>
                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Enter your registered employee email address. We'll send a six-digit verification code.
                </p>

                @if (session('status'))
                    <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm text-emerald-700">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}"
                      class="mt-6 space-y-5" @submit="loading = true">
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Employee Email Address
                        </label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-3.5 text-slate-400"></i>
                            <input id="email" name="email" type="email"
                                   value="{{ old('email') }}" required autofocus
                                   autocomplete="email"
                                   placeholder="employee@example.com"
                                   class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-4 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                        </div>
                    </div>

                    <button type="submit" :disabled="loading"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 font-semibold text-white shadow-md disabled:opacity-60">
                        <template x-if="!loading">
                            <span><i class="fa-solid fa-paper-plane mr-2"></i>Send Verification Code</span>
                        </template>
                        <template x-if="loading">
                            <span><i class="fa-solid fa-spinner fa-spin mr-2"></i>Sending Code...</span>
                        </template>
                    </button>
                </form>

                <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50 p-3.5 text-xs leading-5 text-slate-600">
                    <i class="fa-solid fa-circle-info mr-1 text-sky-700"></i>
                    Verification codes expire after 5 minutes. Only eligible employee accounts can receive a code.
                </div>

                <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-sky-700 hover:text-sky-800">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Back to Employee Login
                    </a>
                </div>
            </div>

            <p class="mt-5 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Sagay Water District
            </p>
        </div>
    </div>

    <div class="relative hidden min-h-screen items-center justify-center overflow-hidden bg-gradient-to-br from-sky-800 via-blue-700 to-cyan-600 px-10 text-white lg:flex">
        <div class="relative z-10 max-w-lg">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10">
                <i class="fa-solid fa-shield-halved text-3xl"></i>
            </div>

            <h2 class="mt-8 text-3xl font-semibold">Secure Employee Account Recovery</h2>
            <p class="mt-4 text-sm leading-7 text-sky-100">
                Recover access to your iSWD employee account through secure email verification.
            </p>

            <div class="mt-9 space-y-4">
                @foreach ([
                    ['fa-envelope', '1. Enter Your Email', 'Use your registered employee email address.'],
                    ['fa-shield-halved', '2. Verify Your Code', 'Enter the six-digit code delivered to your inbox.'],
                    ['fa-lock', '3. Create a New Password', 'Set a strong password and sign in again.']
                ] as [$icon, $heading, $description])
                    <div class="flex gap-4 rounded-xl border border-white/15 bg-white/10 p-4">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white/10">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold">{{ $heading }}</p>
                            <p class="mt-1 text-xs leading-5 text-sky-100">{{ $description }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

            <p class="mt-9 border-t border-white/20 pt-6 text-xs text-sky-100">
                <i class="fa-solid fa-user-shield mr-2"></i>
                Authorized Sagay Water District personnel only.
            </p>
        </div>
    </div>
</div>

@endsection
