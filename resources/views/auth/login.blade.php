@extends('layouts.auth')

@section('content')
    <div class="min-h-screen flex">

        <!-- LEFT PANEL -->
        <div class="hidden lg:flex w-1/2 bg-gradient-to-br from-sky-700 via-blue-700 to-cyan-600 text-white p-16">

            <div class="my-auto">

                <h1 class="text-5xl font-bold">

                    KnowledgeRetain AI

                </h1>

                <p class="mt-6 text-xl">

                    Intelligent Maintenance Knowledge Management System

                </p>

                <p class="mt-4 text-blue-100">

                    Developed for

                    <strong>Sagay Water District</strong>

                </p>

                <div class="mt-12 space-y-4">

                    <div>✔ Secure Authentication</div>

                    <div>✔ AI Repair Recommendation</div>

                    <div>✔ Knowledge Repository</div>

                    <div>✔ Enterprise Maintenance Platform</div>

                </div>

            </div>

        </div>

        <!-- RIGHT PANEL -->

        <div class="w-full lg:w-1/2 flex justify-center items-center">

            <div class="bg-white shadow-xl rounded-3xl p-10 w-full max-w-md">

                <h2 class="text-3xl font-bold mb-2">

                    Employee Login

                </h2>

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4">

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                            <span class="font-semibold text-red-700">

                                Login Failed

                            </span>

                        </div>

                        <ul class="mt-2 text-sm text-red-600">

                            @foreach ($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach

                        </ul>

                    </div>
                @endif

                <p class="text-slate-500 mb-8">

                    Sign in to continue.

                </p>

                <form method="POST" action="{{ route('login') }}">

                    @csrf

                    <div class="mb-5">

                        <label class="block mb-2 font-semibold">

                            Email

                        </label>

                        <input type="email" name="email" value="{{ old('email') }}"
                            class="w-full rounded-xl border-gray-300" required>

                    </div>

                    <div class="mb-6">

                        <label class="block mb-2 font-semibold">

                            Password

                        </label>

                        <input type="password" name="password" class="w-full rounded-xl border-gray-300" required>

                    </div>

                    <div class="flex justify-between mb-6">

                        <label class="flex items-center gap-2">

                            <input type="checkbox" name="remember">

                            Remember Me

                        </label>

                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sky-700">

                                Forgot Password?

                            </a>
                        @endif

                    </div>

                    <button class="w-full bg-sky-700 text-white py-3 rounded-xl hover:bg-sky-800">

                        Login

                    </button>

                </form>

                <hr class="my-8">

                <div class="text-center">

                    <p class="text-slate-500">

                        Need to report a water issue?

                    </p>

                    <a href="#" class="text-sky-700 font-semibold">

                        Open Consumer Portal

                    </a>

                </div>

            </div>

        </div>

    </div>
@endsection
