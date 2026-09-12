@extends('consumer.auth.layout')

@section('title', 'Consumer Login')

@section('content')

    <div class="min-h-screen bg-slate-50 flex items-center justify-center px-6 py-12">

        <div class="w-full max-w-md">

            {{-- Logo / Branding --}}
            <div class="text-center mb-8">

                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3">

                    <img src="{{ asset('images/logo/logo.png') }}" alt="iSWD" class="w-14 h-14 object-contain">

                    <div class="text-left">

                        <h1 class="text-2xl font-bold text-sky-700">
                            iSWD
                        </h1>

                        <p class="text-xs text-gray-500">
                            Sagay Water District
                        </p>

                    </div>

                </a>

                <h2 class="mt-8 text-3xl font-bold text-slate-800">
                    Consumer Portal
                </h2>

                <p class="mt-2 text-slate-500">
                    Sign in to report and track your water service concerns.
                </p>

            </div>


            {{-- Login Card --}}
            <div class="bg-white rounded-3xl shadow-lg border border-slate-200 p-8">

                {{-- Success --}}
                @if (session('success'))
                    <div
                        class="mb-6 rounded-xl bg-green-50 border border-green-200
                            px-4 py-3 text-sm text-green-700">

                        {{ session('success') }}

                    </div>
                @endif


                {{-- Error --}}
                @if ($errors->any())
                    <div
                        class="mb-6 rounded-xl bg-red-50 border border-red-200
                            px-4 py-3 text-sm text-red-700">

                        {{ $errors->first() }}

                    </div>
                @endif


                <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ loading: false }"
                    @submit="loading=true">

                    @csrf



                    {{-- Email --}}
                    <div>

                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                            Email Address
                        </label>

                        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                            autocomplete="email"
                            class="w-full rounded-xl border-slate-300
                               focus:border-sky-500
                               focus:ring-sky-500
                               px-4 py-3"
                            placeholder="you@example.com">

                        @error('email')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Password --}}
                    <div>

                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                            Password
                        </label>

                        <div class="relative">

                            <input id="password" type="password" name="password" required autocomplete="current-password"
                                class="w-full rounded-xl border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-4 py-3 pr-12"
                                placeholder="Enter your password">

                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2
                                   -translate-y-1/2
                                   text-slate-400
                                   hover:text-sky-600">
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>

                        </div>

                        @error('password')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    {{-- Remember --}}
                    <div class="flex items-center">

                        <label class="inline-flex items-center gap-2">

                            <input type="checkbox" name="remember" value="1"
                                class="rounded border-slate-300
                                   text-sky-600
                                   focus:ring-sky-500">

                            <span class="text-sm text-slate-600">
                                Remember me
                            </span>

                        </label>

                    </div>


                    {{-- Login --}}
                    <button type="submit" :disabled="loading"
                        class="w-full py-3.5 rounded-xl
           bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
           text-white
           font-semibold
           shadow-lg
           transition
           duration-300
           hover:scale-[1.02]
           disabled:opacity-70
           disabled:cursor-not-allowed
           disabled:hover:scale-100">

                        {{-- Normal State --}}
                        <span x-show="!loading" class="flex items-center justify-center gap-2">

                            <i class="fa-solid fa-right-to-bracket"></i>

                            Sign In

                        </span>

                        {{-- Loading State --}}
                        <span x-show="loading" class="flex items-center justify-center gap-2">

                            <i class="fa-solid fa-spinner animate-spin"></i>

                            Signing In...

                        </span>

                    </button>

                </form>


                {{-- Register --}}
                <div class="mt-8 text-center border-t pt-6">

                    <p class="text-sm text-slate-500">

                        Don't have a consumer account?

                    </p>

                    <a href="{{ route('consumer.register') }}"
                        class="inline-block mt-2
                           text-sky-700
                           font-semibold
                           hover:text-sky-800">

                        Create Consumer Account

                    </a>

                </div>


                <div class="mt-5 text-center">

                    <a href="{{ url('/') }}"
                        class="text-sm text-gray-500
                               hover:text-sky-700">

                        <i class="fas fa-arrow-left mr-1"></i>

                        Back to iSWD

                    </a>

                </div>

            </div>

        </div>

    </div>


    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('password-icon');

            if (password.type === 'password') {

                password.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                password.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }
        }
    </script>

@endsection
