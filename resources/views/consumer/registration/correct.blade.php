@extends('consumer.auth.layout')

@section('title', 'Correct Registration')

@section('content')

    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">

        <div class="max-w-4xl mx-auto">

            <div class="mb-8">

                <a
                    href="{{ route('consumer.registration.status') }}"
                    class="inline-flex items-center gap-2
                           text-sm font-semibold
                           text-sky-700
                           hover:text-sky-800"
                >

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Registration Status

                </a>


                <h1
                    class="text-3xl font-bold
                           text-slate-900 mt-5"
                >
                    Correct Registration
                </h1>

                <p
                    class="text-slate-500 mt-2
                           leading-6"
                >
                    Correct the information that could not be
                    verified and resubmit your registration.
                </p>

            </div>


            <div
                class="mb-6 rounded-2xl
                       border border-red-200
                       bg-red-50 p-5"
            >

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-red-100 text-red-600
                               flex items-center
                               justify-center shrink-0"
                    >

                        <i class="fa-solid fa-circle-exclamation"></i>

                    </div>

                    <div>

                        <p
                            class="text-sm font-semibold
                                   text-red-900"
                        >
                            Reason for Previous Rejection
                        </p>

                        <p
                            class="text-sm text-red-800
                                   mt-1 leading-6"
                        >
                            {{ $consumer->verification_reason ?: 'The submitted registration information could not be verified.' }}
                        </p>

                    </div>

                </div>

            </div>


            @if ($errors->any())

                <div
                    class="mb-6 rounded-2xl
                           border border-red-200
                           bg-red-50 p-5"
                >

                    <h2
                        class="font-semibold
                               text-red-800"
                    >
                        Please correct the following:
                    </h2>

                    <ul
                        class="mt-2 list-disc
                               list-inside
                               text-sm text-red-700
                               space-y-1"
                    >

                        @foreach ($errors->all() as $error)

                            <li>
                                {{ $error }}
                            </li>

                        @endforeach

                    </ul>

                </div>

            @endif


            <form
                method="POST"
                action="{{ route('consumer.registration.update') }}"
                x-data="{ loading: false }"
                @submit="loading = true"
                class="bg-white rounded-3xl
                       border border-slate-200
                       shadow-lg overflow-hidden"
            >

                @csrf
                @method('PUT')


                <div class="p-6 sm:p-8 border-b border-slate-200">

                    <div class="flex items-center gap-3 mb-6">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-sky-100 text-sky-700
                                   flex items-center
                                   justify-center"
                        >

                            <i class="fa-solid fa-droplet"></i>

                        </div>

                        <div>

                            <h2
                                class="text-xl font-bold
                                       text-slate-800"
                            >
                                Water Service Account
                            </h2>

                            <p
                                class="text-sm
                                       text-slate-500 mt-1"
                            >
                                Enter the account number exactly
                                as shown on your SWD bill.
                            </p>

                        </div>

                    </div>


                    <label
                        for="account_number"
                        class="block text-sm
                               font-semibold
                               text-slate-700 mb-2"
                    >
                        Account Number
                        <span class="text-red-500">*</span>
                    </label>


                    <input
                        id="account_number"
                        type="text"
                        name="account_number"
                        required
                        value="{{ old('account_number', $consumer->account_number) }}"
                        class="w-full rounded-xl
                               border-slate-300
                               focus:border-sky-500
                               focus:ring-sky-500
                               px-4 py-3
                               @error('account_number')
                                   border-red-400
                               @enderror"
                    >


                    @error('account_number')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>


                <div class="p-6 sm:p-8 border-b border-slate-200">

                    <div class="flex items-center gap-3 mb-6">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-blue-100 text-blue-700
                                   flex items-center
                                   justify-center"
                        >

                            <i class="fa-solid fa-user"></i>

                        </div>

                        <div>

                            <h2
                                class="text-xl font-bold
                                       text-slate-800"
                            >
                                Personal Information
                            </h2>

                            <p class="text-sm text-slate-500 mt-1">
                                Make sure the name matches the registered
                                SWD account holder.
                            </p>

                        </div>

                    </div>


                    <div class="grid md:grid-cols-2 gap-6">

                        <div>

                            <label
                                for="first_name"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                First Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="first_name"
                                type="text"
                                name="first_name"
                                required
                                value="{{ old('first_name', $consumer->first_name) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="middle_name"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Middle Name
                            </label>

                            <input
                                id="middle_name"
                                type="text"
                                name="middle_name"
                                value="{{ old('middle_name', $consumer->middle_name) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="last_name"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Last Name
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="last_name"
                                type="text"
                                name="last_name"
                                required
                                value="{{ old('last_name', $consumer->last_name) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="suffix"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Suffix
                            </label>

                            <input
                                id="suffix"
                                type="text"
                                name="suffix"
                                value="{{ old('suffix', $consumer->suffix) }}"
                                placeholder="Jr., Sr., III"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="sex"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Sex
                                <span class="text-red-500">*</span>
                            </label>

                            <select
                                id="sex"
                                name="sex"
                                required
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                                <option value="">
                                    Select sex
                                </option>

                                <option
                                    value="Male"
                                    @selected(old('sex', $consumer->sex) === 'Male')
                                >
                                    Male
                                </option>

                                <option
                                    value="Female"
                                    @selected(old('sex', $consumer->sex) === 'Female')
                                >
                                    Female
                                </option>

                            </select>

                        </div>

                    </div>

                </div>


                <div class="p-6 sm:p-8 border-b border-slate-200">

                    <div class="flex items-center gap-3 mb-6">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-cyan-100 text-cyan-700
                                   flex items-center
                                   justify-center"
                        >

                            <i class="fa-solid fa-address-book"></i>

                        </div>

                        <div>

                            <h2
                                class="text-xl font-bold
                                       text-slate-800"
                            >
                                Contact Information
                            </h2>

                        </div>

                    </div>


                    <div class="grid md:grid-cols-2 gap-6">

                        <div>

                            <label
                                for="phone"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Mobile Number
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="phone"
                                type="text"
                                name="phone"
                                required
                                value="{{ old('phone', $consumer->phone) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Email Address
                            </label>

                            <input
                                type="email"
                                value="{{ $consumer->email }}"
                                disabled
                                class="w-full rounded-xl
                                       border-slate-200
                                       bg-slate-100
                                       text-slate-500
                                       px-4 py-3"
                            >

                            <p class="mt-2 text-xs text-slate-500">
                                Your login email remains unchanged.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-6 sm:p-8 border-b border-slate-200">

                    <div class="flex items-center gap-3 mb-6">

                        <div
                            class="w-10 h-10 rounded-xl
                                   bg-indigo-100 text-indigo-700
                                   flex items-center
                                   justify-center"
                        >

                            <i class="fa-solid fa-location-dot"></i>

                        </div>

                        <div>

                            <h2
                                class="text-xl font-bold
                                       text-slate-800"
                            >
                                Service Address
                            </h2>

                        </div>

                    </div>


                    <div class="grid md:grid-cols-2 gap-6">

                        <div>

                            <label
                                for="house_no"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                House / Building No.
                            </label>

                            <input
                                id="house_no"
                                type="text"
                                name="house_no"
                                value="{{ old('house_no', $consumer->address?->house_no) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="street"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Street
                            </label>

                            <input
                                id="street"
                                type="text"
                                name="street"
                                value="{{ old('street', $consumer->address?->street) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="purok"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Purok
                            </label>

                            <input
                                id="purok"
                                type="text"
                                name="purok"
                                value="{{ old('purok', $consumer->address?->purok) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                for="barangay"
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Barangay
                                <span class="text-red-500">*</span>
                            </label>

                            <input
                                id="barangay"
                                type="text"
                                name="barangay"
                                required
                                value="{{ old('barangay', $consumer->address?->barangay) }}"
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Municipality / City
                            </label>

                            <input
                                type="text"
                                value="Sagay City"
                                disabled
                                class="w-full rounded-xl
                                       border-slate-200
                                       bg-slate-100
                                       text-slate-500
                                       px-4 py-3"
                            >

                        </div>


                        <div>

                            <label
                                class="block text-sm
                                       font-semibold
                                       text-slate-700 mb-2"
                            >
                                Province
                            </label>

                            <input
                                type="text"
                                value="Negros Occidental"
                                disabled
                                class="w-full rounded-xl
                                       border-slate-200
                                       bg-slate-100
                                       text-slate-500
                                       px-4 py-3"
                            >

                        </div>

                    </div>

                </div>


                <div class="p-6 sm:p-8">

                    <label class="flex items-start gap-3 cursor-pointer">

                        <input
                            type="checkbox"
                            name="terms"
                            value="1"
                            required
                            @checked(old('terms'))
                            class="mt-1 rounded
                                   border-slate-300
                                   text-sky-600
                                   focus:ring-sky-500"
                        >

                        <span
                            class="text-sm text-slate-600
                                   leading-6"
                        >
                            I certify that the corrected information
                            provided is accurate and belongs to my
                            registered Sagay Water District service
                            account.
                        </span>

                    </label>


                    @error('terms')

                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>

                    @enderror


                    <div
                        class="mt-8 flex flex-col-reverse
                               sm:flex-row
                               sm:justify-end gap-3"
                    >

                        <a
                            href="{{ route('consumer.registration.status') }}"
                            class="px-6 py-3 rounded-xl
                                   border border-slate-300
                                   text-slate-700
                                   font-semibold text-center
                                   hover:bg-slate-50
                                   transition"
                        >
                            Cancel
                        </a>


                        <button
                            type="submit"
                            :disabled="loading"
                            class="px-7 py-3 rounded-xl
                                   bg-sky-700
                                   hover:bg-sky-800
                                   text-white
                                   font-semibold
                                   transition
                                   disabled:opacity-60
                                   disabled:cursor-not-allowed"
                        >

                            <span
                                x-show="!loading"
                                class="flex items-center
                                       justify-center gap-2"
                            >

                                <i class="fa-solid fa-paper-plane"></i>

                                Resubmit Registration

                            </span>


                            <span
                                x-show="loading"
                                x-cloak
                                class="flex items-center
                                       justify-center gap-2"
                            >

                                <i
                                    class="fa-solid fa-spinner
                                           animate-spin"
                                ></i>

                                Resubmitting...

                            </span>

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
