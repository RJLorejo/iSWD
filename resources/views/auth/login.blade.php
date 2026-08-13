@extends('layouts.auth')

@section('title', 'Employee Login')

@section('content')

    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-sky-900 via-blue-700 to-cyan-600">

        {{-- Background --}}
        <div class="absolute inset-0">

            <div class="absolute -top-20 -left-20 w-96 h-96 bg-cyan-400/20 rounded-full blur-3xl animate-pulse"></div>

            <div class="absolute bottom-0 right-0 w-[500px] h-[500px] bg-blue-400/20 rounded-full blur-3xl animate-pulse">
            </div>

        </div>

        <div class="relative z-10 flex min-h-screen">

            {{-- LEFT SIDE --}}

            <div class="hidden lg:flex w-1/2 text-white p-16">

                <div class="my-auto max-w-xl">

                    <div class="flex items-center gap-4 mb-10">

                        <div
                            class="w-20 h-20 rounded-2xl bg-white/20 backdrop-blur-xl flex items-center justify-center shadow-lg">

                        <img src="{{ asset('images/logo/logo.png') }}" alt="Logo" class="w-full h-full object-cover rounded-2xl">

                        </div>

                        <div>

                            <h1 class="text-5xl font-black">

                                Sagay Water District

                            </h1>

                        </div>

                    </div>

                    <h2 class="text-2xl font-bold leading-snug">

                        <span class="text-cyan-400 text-3xl">iSWD</span>: A Maintenance Knowledge Management System with AI-Based Repair Case Recommendation for Sagay Water
                        District

                    </h2>

                    <p class="mt-6 text-cyan-100 leading-8">

                        Centralize maintenance knowledge, streamline repair workflows,
                        recommend historical repair cases using Artificial Intelligence,
                        and improve maintenance decision-making across Sagay Water District.

                    </p>

                    <div class="grid grid-cols-2 gap-5 mt-12">

                        <div class="rounded-2xl bg-white/10 backdrop-blur-xl p-5 border border-white/20">

                            <i class="fa-solid fa-brain text-3xl mb-3"></i>

                            <h3 class="font-bold">

                                AI Recommendation

                            </h3>

                            <p class="text-sm text-cyan-100 mt-2">

                                Similar repair case suggestions.

                            </p>

                        </div>

                        <div class="rounded-2xl bg-white/10 backdrop-blur-xl p-5 border border-white/20">

                            <i class="fa-solid fa-book-open text-3xl mb-3"></i>

                            <h3 class="font-bold">

                                Knowledge Repository

                            </h3>

                            <p class="text-sm text-cyan-100 mt-2">

                                Preserve maintenance experience.

                            </p>

                        </div>

                        <div class="rounded-2xl bg-white/10 backdrop-blur-xl p-5 border border-white/20">

                            <i class="fa-solid fa-screwdriver-wrench text-3xl mb-3"></i>

                            <h3 class="font-bold">

                                Work Orders

                            </h3>

                            <p class="text-sm text-cyan-100 mt-2">

                                Digital maintenance operations.

                            </p>

                        </div>

                        <div class="rounded-2xl bg-white/10 backdrop-blur-xl p-5 border border-white/20">

                            <i class="fa-solid fa-chart-line text-3xl mb-3"></i>

                            <h3 class="font-bold">

                                Analytics

                            </h3>

                            <p class="text-sm text-cyan-100 mt-2">

                                Maintenance reports & KPIs.

                            </p>

                        </div>

                    </div>

                </div>

            </div>

            {{-- RIGHT SIDE --}}

            <div class="w-full lg:w-1/2 flex items-center justify-center p-8">

                <div x-data="{ show: false, loading: false }"
                    class="w-full max-w-md rounded-3xl bg-white/95 backdrop-blur-xl shadow-2xl border border-white p-10">

                    <div class="text-center">

                        <div
                            class="mx-auto w-20 h-20 rounded-full bg-gradient-to-r from-sky-700 to-cyan-600 text-white flex items-center justify-center shadow-lg">

                            <i class="fa-solid fa-user-shield text-3xl"></i>

                        </div>

                        <h2 class="mt-4 text-3xl font-bold text-slate-800">

                            Employee Login

                        </h2>

                        <p class="mt-2 text-slate-500">

                            Sign in using your employee credentials.

                        </p>

                    </div>

                    @if ($errors->any())
                        <div class="mt-8 rounded-2xl bg-red-50 border border-red-200 p-4">

                            <div class="flex items-center gap-3">

                                <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                                <span class="font-semibold text-red-700">

                                    Invalid email or password.

                                </span>

                            </div>

                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6" @submit="loading=true">

                        @csrf

                        <div>

                            <label class="font-semibold text-slate-700">

                                Email Address

                            </label>

                            <div class="relative mt-2">

                                <i class="fa-solid fa-envelope absolute left-4 top-4 text-slate-400"></i>

                                <input type="email" name="email" value="{{ old('email') }}" required
                                    class="w-full pl-12 pr-4 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500 focus:border-sky-500">

                            </div>

                        </div>

                        <div>

                            <label class="font-semibold text-slate-700">

                                Password

                            </label>

                            <div class="relative mt-2">

                                <i class="fa-solid fa-lock absolute left-4 top-4 text-slate-400"></i>

                                <input :type="show ? 'text' : 'password'" name="password" required
                                    class="w-full pl-12 pr-12 py-3 rounded-xl border border-slate-300 focus:ring-2 focus:ring-sky-500">

                                <button type="button" @click="show=!show"
                                    class="absolute right-4 top-4 text-slate-500 hover:text-sky-700">

                                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>

                                </button>

                            </div>

                        </div>

                        <div class="flex justify-between items-center">

                            <label class="flex items-center gap-2">

                                <input type="checkbox" name="remember">

                                <span class="text-sm">

                                    Remember Me

                                </span>

                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-sm text-sky-700 hover:underline">

                                    Forgot Password?

                                </a>
                            @endif

                        </div>

                        <button type="submit" :disabled="loading"
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white font-semibold shadow-lg hover:scale-[1.02] duration-300 disabled:opacity-70">

                            <span x-show="!loading">

                                Login

                            </span>

                            <span x-show="loading" class="flex items-center justify-center gap-2">

                                <i class="fa-solid fa-spinner animate-spin"></i>

                                Signing In...

                            </span>

                        </button>

                    </form>

                    <hr class="my-5 border-slate-300">

                    <div class="text-center">

                        <p class="text-slate-500">

                            Need to report a water issue?

                        </p>

                        <a href="#" class="text-sky-700 font-semibold">

                            Open Consumer Portal

                        </a>

                    </div>

                    <div class="mt-10 text-center text-sm text-slate-500">

                        © {{ date('Y') }}

                        Sagay Water District

                        <br>

                        ISWD • Maintenance Knowledge Management System

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
