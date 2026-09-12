@extends('consumer.auth.layout')

@section('title', 'Create Consumer Account')

@section('content')

    <div class="min-h-screen bg-slate-50 py-12 px-6">

        <div class="max-w-4xl mx-auto">

            {{-- Header --}}
            <div class="text-center mb-10">

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
                    Create Consumer Account
                </h2>

                <p class="mt-2 text-slate-500 max-w-2xl mx-auto">

                    Register your water service account to submit and monitor
                    complaints through the iSWD Consumer Portal.

                </p>

            </div>


            {{-- Validation Summary --}}
            @if ($errors->any())

                <div class="mb-6 bg-red-50 border border-red-200
                        rounded-2xl p-5">

                    <div class="flex gap-3">

                        <i class="fas fa-circle-exclamation text-red-600 mt-1"></i>

                        <div>

                            <h3 class="font-semibold text-red-800">
                                Please check the following:
                            </h3>

                            <ul class="mt-2 list-disc list-inside
                                   text-sm text-red-700">

                                @foreach ($errors->all() as $error)
                                    <li>
                                        {{ $error }}
                                    </li>
                                @endforeach

                            </ul>

                        </div>

                    </div>

                </div>

            @endif


            {{-- Registration Card --}}
            <div class="bg-white rounded-3xl shadow-lg border
                    border-slate-200 overflow-hidden">

                <form method="POST" action="{{ route('consumer.register.store') }}" class="..." x-data="{ loading: false }"
                    @submit="loading=true">

                    @csrf



                    {{-- Account Information --}}
                    <div class="p-8 border-b">

                        <div class="mb-6">

                            <h3 class="text-xl font-bold text-slate-800">

                                Water Service Account

                            </h3>

                            <p class="text-sm text-slate-500 mt-1">

                                Use the account number shown on your water bill.

                            </p>

                        </div>

                        <div>

                            <label for="account_number"
                                class="block text-sm font-semibold
                                   text-slate-700 mb-2">
                                Account Number <span class="text-red-500">*</span>
                            </label>

                            <input id="account_number" type="text" name="account_number"
                                value="{{ old('account_number') }}" required
                                class="w-full rounded-xl border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-4 py-3"
                                placeholder="Enter your water account number">

                            <p class="mt-2 text-xs text-slate-500">

                                This helps identify your water service account.

                            </p>

                        </div>

                    </div>


                    {{-- Personal Information --}}
                    <div class="p-8 border-b">

                        <div class="mb-6">

                            <h3 class="text-xl font-bold text-slate-800">
                                Personal Information
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Enter the information associated with your water service account.
                            </p>

                        </div>

                        <div class="grid md:grid-cols-2 gap-6">

                            {{-- First Name --}}
                            <div>

                                <label for="first_name"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>

                                <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}"
                                    required
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Middle Name --}}
                            <div>

                                <label for="middle_name"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Middle Name
                                </label>

                                <input id="middle_name" type="text" name="middle_name" value="{{ old('middle_name') }}"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Last Name --}}
                            <div>

                                <label for="last_name"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>

                                <input id="last_name" type="text" name="last_name" value="{{ old('last_name') }}"
                                    required
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Suffix --}}
                            <div>

                                <label for="suffix"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Suffix
                                </label>

                                <input id="suffix" type="text" name="suffix" value="{{ old('suffix') }}"
                                    placeholder="Jr., Sr., III"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Sex --}}
                            <div>

                                <label for="sex"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Sex <span class="text-red-500">*</span>
                                </label>

                                <select id="sex" name="sex" required
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                                    <option value="">
                                        Select sex
                                    </option>

                                    <option value="Male" @selected(old('sex') === 'Male')>
                                        Male
                                    </option>

                                    <option value="Female" @selected(old('sex') === 'Female')>
                                        Female
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    {{-- Contact Information --}}
                    <div class="p-8 border-b">

                        <div class="mb-6">

                            <h3 class="text-xl font-bold text-slate-800">
                                Contact Information
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Used for account access and complaint updates.
                            </p>

                        </div>

                        <div class="grid md:grid-cols-2 gap-6">

                            {{-- Phone --}}
                            <div>

                                <label for="phone"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Mobile Number <span class="text-red-500">*</span>
                                </label>

                                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" required
                                    placeholder="09XXXXXXXXX"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Email --}}
                            <div>

                                <label for="email"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>

                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    required placeholder="you@example.com"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>

                        </div>

                    </div>


                    {{-- Address --}}
                    <div class="p-8 border-b">

                        <div class="mb-6">

                            <h3 class="text-xl font-bold text-slate-800">
                                Service Address
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Provide the location of the registered water service.
                            </p>

                        </div>

                        <div class="grid md:grid-cols-2 gap-6">

                            {{-- House --}}
                            <div>

                                <label for="house_no"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    House / Building No.
                                </label>

                                <input id="house_no" type="text" name="house_no" value="{{ old('house_no') }}"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Street --}}
                            <div>

                                <label for="street"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Street
                                </label>

                                <input id="street" type="text" name="street" value="{{ old('street') }}"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Purok --}}
                            <div>

                                <label for="purok"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Purok
                                </label>

                                <input id="purok" type="text" name="purok" value="{{ old('purok') }}"
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3">

                            </div>


                            {{-- Barangay --}}
                            <div>

                                <label for="barangay"
                                    class="block text-sm font-semibold
                                       text-slate-700 mb-2">
                                    Barangay <span class="text-red-500">*</span>
                                </label>

                                <input id="barangay" type="text" name="barangay" value="{{ old('barangay') }}"
                                    required
                                    class="w-full rounded-xl border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                                    placeholder="Barangay">

                            </div>


                            {{-- Municipality --}}
                            <div>

                                <label
                                    class="block text-sm font-semibold
                                          text-slate-700 mb-2">
                                    Municipality / City
                                </label>

                                <input type="text" value="Sagay City" disabled
                                    class="w-full rounded-xl border-slate-200
                                       bg-slate-100
                                       text-slate-500
                                       px-4 py-3">

                            </div>


                            {{-- Province --}}
                            <div>

                                <label
                                    class="block text-sm font-semibold
                                          text-slate-700 mb-2">
                                    Province
                                </label>

                                <input type="text" value="Negros Occidental" disabled
                                    class="w-full rounded-xl border-slate-200
                                       bg-slate-100
                                       text-slate-500
                                       px-4 py-3">

                            </div>

                        </div>

                    </div>


                    {{-- Password --}}
                    <div class="p-8 border-b">

                        <div class="mb-6">

                            <h3 class="text-xl font-bold text-slate-800">
                                Account Security
                            </h3>

                        </div>

                        <div class="grid md:grid-cols-2 gap-6">

                            {{-- Password --}}
                            <div>

                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">

                                    Password <span class="text-red-500">*</span>

                                </label>

                                <div class="relative">

                                    <input id="password" type="password" name="password" required
                                        autocomplete="new-password"
                                        class="w-full rounded-xl border border-slate-300
                       focus:border-sky-500
                       focus:ring-sky-500
                       px-4 py-3 pr-12"
                                        placeholder="Minimum 8 characters">

                                    <button type="button" onclick="togglePassword('password', 'passwordEye')"
                                        class="absolute inset-y-0 right-0
                       flex items-center px-4
                       text-slate-400
                       hover:text-sky-600
                       transition"
                                        aria-label="Show password">

                                        <i id="passwordEye" class="fas fa-eye"></i>

                                    </button>

                                </div>

                                @error('password')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>


                            {{-- Confirm Password --}}
                            <div>

                                <label for="password_confirmation"
                                    class="block text-sm font-semibold text-slate-700 mb-2">

                                    Confirm Password <span class="text-red-500">*</span>

                                </label>

                                <div class="relative">

                                    <input id="password_confirmation" type="password" name="password_confirmation"
                                        required autocomplete="new-password"
                                        class="w-full rounded-xl border border-slate-300
                       focus:border-sky-500
                       focus:ring-sky-500
                       px-4 py-3 pr-12"
                                        placeholder="Repeat your password">

                                    <button type="button"
                                        onclick="togglePassword(
                    'password_confirmation',
                    'passwordConfirmationEye'
                )"
                                        class="absolute inset-y-0 right-0
                       flex items-center px-4
                       text-slate-400
                       hover:text-sky-600
                       transition"
                                        aria-label="Show password">

                                        <i id="passwordConfirmationEye" class="fas fa-eye"></i>

                                    </button>

                                </div>

                                @error('password_confirmation')
                                    <p class="mt-2 text-sm text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- Terms --}}
                    <div class="p-8">

                        <label class="flex items-start gap-3">

                            <input type="checkbox" name="terms" value="1" required
                                class="mt-1 rounded
                                   border-slate-300
                                   text-sky-600
                                   focus:ring-sky-500">

                            <span class="text-sm text-slate-600 leading-6">

                                I certify that the information provided is accurate
                                and belongs to the registered water service account.
                                I understand that false information may result in
                                account restriction or complaint verification.

                            </span>

                        </label>


                        {{-- Buttons --}}
                        <div class="mt-8 flex flex-col sm:flex-row
                                justify-end gap-3">

                            <a href="{{ route('consumer.login') }}"
                                class="px-6 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   text-center
                                   hover:bg-slate-50">

                                Already have an account?

                            </a>

                            {{-- Create Account --}}
                            <button type="submit" :disabled="loading"
                                class="px-7 py-3 rounded-xl
           bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
           hover:scale-[1.02]
           text-white
           font-semibold
           shadow-lg
           transition
           duration-300
           disabled:opacity-70
           disabled:cursor-not-allowed
           disabled:hover:scale-100">

                                {{-- Normal State --}}
                                <span x-show="!loading" class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-user-plus"></i>

                                    Create Account

                                </span>

                                {{-- Loading State --}}
                                <span x-show="loading" class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-spinner animate-spin"></i>

                                    Creating Account...

                                </span>

                            </button>

                        </div>

                    </div>

                </form>

            </div>


            <p class="text-center text-xs text-slate-400 mt-6">

                Sagay Water District Consumer Portal

            </p>

        </div>

    </div>

    <script>
        function togglePassword(inputId, iconId) {

            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {

                input.type = 'text';

                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');

            } else {

                input.type = 'password';

                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');

            }
        }
    </script>

@endsection
