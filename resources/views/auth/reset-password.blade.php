
@extends('layouts.auth')

@section('title', 'Reset Employee Password')

@section('content')

<div class="min-h-screen bg-slate-50 lg:grid lg:grid-cols-[1.08fr_.92fr]"
     x-data="{ loading: false, showPassword: false, showConfirmation: false }">

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

                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                    <i class="fa-solid fa-lock-open"></i>
                </div>

                <h1 class="mt-5 text-2xl font-semibold text-slate-900">Create New Password</h1>

                <p class="mt-2 text-sm leading-6 text-slate-500">
                    Your email has been verified. Create a new password for your employee account.
                </p>

                <p class="mt-2 break-all text-sm font-semibold text-sky-700">{{ $email }}</p>

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

                <form method="POST" action="{{ route('password.store') }}"
                      class="mt-6 space-y-5" @submit="loading = true">
                    @csrf

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">
                            New Password
                        </label>

                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400"></i>

                            <input id="password" name="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   required minlength="8" autocomplete="new-password"
                                   placeholder="Enter new password"
                                   class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-12 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                            <button type="button" @click="showPassword = !showPassword"
                                    class="absolute right-3.5 top-3.5 text-slate-400 hover:text-sky-700"
                                    aria-label="Toggle password visibility">
                                <i class="fa-solid" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-slate-700">
                            Confirm New Password
                        </label>

                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400"></i>

                            <input id="password_confirmation" name="password_confirmation"
                                   :type="showConfirmation ? 'text' : 'password'"
                                   required minlength="8" autocomplete="new-password"
                                   placeholder="Confirm new password"
                                   class="w-full rounded-xl border border-slate-300 py-3 pl-10 pr-12 text-sm outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-100">

                            <button type="button" @click="showConfirmation = !showConfirmation"
                                    class="absolute right-3.5 top-3.5 text-slate-400 hover:text-sky-700"
                                    aria-label="Toggle confirmation visibility">
                                <i class="fa-solid" :class="showConfirmation ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>

                    <div class="rounded-xl border border-sky-100 bg-sky-50 p-3 text-xs leading-5 text-slate-600">
                        <i class="fa-solid fa-circle-info mr-1 text-sky-700"></i>
                        Use at least 8 characters, including uppercase and lowercase letters and a number.
                    </div>

                    <button type="submit" :disabled="loading"
                            class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-5 py-3 font-semibold text-white shadow-md disabled:opacity-60">
                        <span x-show="!loading">
                            <i class="fa-solid fa-check mr-2"></i>Reset Password
                        </span>
                        <span x-cloak x-show="loading">
                            <i class="fa-solid fa-spinner fa-spin mr-2"></i>Updating Password...
                        </span>
                    </button>
                </form>

                <div class="mt-6 border-t border-slate-100 pt-5 text-center">
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-sky-700">
                        <i class="fa-solid fa-arrow-left mr-2"></i>Back to Employee Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="hidden min-h-screen items-center justify-center bg-gradient-to-br from-sky-800 via-blue-700 to-cyan-600 px-10 text-white lg:flex">
        <div class="max-w-lg">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl border border-white/20 bg-white/10">
                <i class="fa-solid fa-user-shield text-3xl"></i>
            </div>

            <h2 class="mt-8 text-3xl font-semibold">Protect Your Employee Account</h2>

            <p class="mt-4 text-sm leading-7 text-sky-100">
                Choose a strong password to keep your iSWD account and Sagay Water District information secure.
            </p>

            <div class="mt-9 space-y-4">
                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-shield-halved mr-2"></i>
                    Use a unique password.
                </div>

                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-lock mr-2"></i>
                    Keep your login credentials private.
                </div>

                <div class="rounded-xl border border-white/15 bg-white/10 p-5">
                    <i class="fa-solid fa-check-circle mr-2"></i>
                    Sign in again after resetting your password.
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
