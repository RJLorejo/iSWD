@extends('consumer.auth.layout')

@section('title', 'Registration Status')

@section('content')

    <div class="min-h-screen bg-slate-50 py-12 px-4 sm:px-6">

        <div class="max-w-2xl mx-auto">

            <div class="text-center mb-8">

                <a
                    href="{{ route('landing') }}"
                    class="inline-flex items-center gap-3"
                >

                    <img
                        src="{{ asset('images/logo/logo.png') }}"
                        alt="iSWD"
                        class="w-14 h-14 object-contain"
                    >

                    <div class="text-left">

                        <h1 class="text-2xl font-bold text-sky-700">
                            iSWD
                        </h1>

                        <p class="text-xs text-slate-500">
                            Sagay Water District
                        </p>

                    </div>

                </a>

            </div>


            @if (session('success'))

                <div
                    class="mb-6 rounded-2xl
                           border border-green-200
                           bg-green-50 p-5"
                >

                    <div class="flex items-start gap-3">

                        <i
                            class="fa-solid fa-circle-check
                                   text-green-600 mt-1"
                        ></i>

                        <p class="text-sm text-green-800">
                            {{ session('success') }}
                        </p>

                    </div>

                </div>

            @endif


            <div
                class="bg-white rounded-3xl
                       border border-slate-200
                       shadow-lg overflow-hidden"
            >

                @if (
                    $consumer->verification_status ===
                    'Pending Verification'
                )

                    <div class="p-6 sm:p-8">

                        <div
                            class="w-16 h-16 mx-auto
                                   rounded-full
                                   bg-amber-100
                                   text-amber-600
                                   flex items-center
                                   justify-center"
                        >

                            <i
                                class="fa-solid fa-clock
                                       text-2xl"
                            ></i>

                        </div>


                        <div class="text-center mt-5">

                            <h1
                                class="text-2xl font-bold
                                       text-slate-900"
                            >
                                Registration Pending Verification
                            </h1>

                            <p
                                class="mt-3 text-sm
                                       text-slate-500
                                       leading-6"
                            >
                                Your consumer registration has been
                                received and is currently being reviewed
                                by Sagay Water District.
                            </p>

                        </div>


                        <div
                            class="mt-7 rounded-2xl
                                   bg-slate-50
                                   border border-slate-200
                                   p-5"
                        >

                            <div
                                class="flex items-center
                                       justify-between gap-4"
                            >

                                <span
                                    class="text-sm
                                           text-slate-500"
                                >
                                    Account Number
                                </span>

                                <span
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    {{ $consumer->account_number }}
                                </span>

                            </div>


                            <div
                                class="mt-4 pt-4
                                       border-t border-slate-200
                                       flex items-center
                                       justify-between gap-4"
                            >

                                <span
                                    class="text-sm
                                           text-slate-500"
                                >
                                    Status
                                </span>

                                <span
                                    class="inline-flex
                                           items-center gap-2
                                           rounded-full
                                           bg-amber-100
                                           text-amber-700
                                           px-3 py-1.5
                                           text-xs font-semibold"
                                >

                                    <i class="fa-solid fa-clock"></i>

                                    Pending Verification

                                </span>

                            </div>

                        </div>


                        <div
                            class="mt-6 rounded-2xl
                                   border border-sky-200
                                   bg-sky-50 p-5"
                        >

                            <div class="flex items-start gap-3">

                                <i
                                    class="fa-solid fa-circle-info
                                           text-sky-600 mt-1"
                                ></i>

                                <p
                                    class="text-sm text-sky-800
                                           leading-6"
                                >
                                    You will be able to access the
                                    Consumer Portal after your account
                                    information has been verified and
                                    approved.
                                </p>

                            </div>

                        </div>

                    </div>

                @elseif (
                    $consumer->verification_status ===
                    'Rejected'
                )

                    <div class="p-6 sm:p-8">

                        <div
                            class="w-16 h-16 mx-auto
                                   rounded-full
                                   bg-red-100
                                   text-red-600
                                   flex items-center
                                   justify-center"
                        >

                            <i
                                class="fa-solid fa-circle-xmark
                                       text-2xl"
                            ></i>

                        </div>


                        <div class="text-center mt-5">

                            <h1
                                class="text-2xl font-bold
                                       text-slate-900"
                            >
                                Registration Needs Correction
                            </h1>

                            <p
                                class="mt-3 text-sm
                                       text-slate-500
                                       leading-6"
                            >
                                Sagay Water District could not verify
                                some of the information submitted with
                                your registration.
                            </p>

                        </div>


                        <div
                            class="mt-7 rounded-2xl
                                   border border-red-200
                                   bg-red-50 p-5"
                        >

                            <p
                                class="text-xs font-semibold
                                       uppercase tracking-wider
                                       text-red-600"
                            >
                                Verification Reason
                            </p>

                            <p
                                class="mt-2 text-sm
                                       text-red-800
                                       leading-6"
                            >
                                {{ $consumer->verification_reason ?: 'Your submitted registration information could not be verified.' }}
                            </p>

                        </div>


                        <div
                            class="mt-6 rounded-2xl
                                   bg-slate-50
                                   border border-slate-200
                                   p-5"
                        >

                            <div
                                class="flex items-center
                                       justify-between gap-4"
                            >

                                <span
                                    class="text-sm
                                           text-slate-500"
                                >
                                    Submitted Account Number
                                </span>

                                <span
                                    class="font-semibold
                                           text-slate-900"
                                >
                                    {{ $consumer->account_number }}
                                </span>

                            </div>


                            @if ($consumer->verified_at)

                                <div
                                    class="mt-4 pt-4
                                           border-t border-slate-200"
                                >

                                    <p
                                        class="text-xs
                                               text-slate-500"
                                    >
                                        Reviewed
                                    </p>

                                    <p
                                        class="text-sm
                                               font-medium
                                               text-slate-800
                                               mt-1"
                                    >
                                        {{ $consumer->verified_at->format('F d, Y h:i A') }}
                                    </p>

                                </div>

                            @endif

                        </div>


                        <div class="mt-7">

                            <a
                                href="{{ route('consumer.registration.edit') }}"
                                class="w-full
                                       inline-flex items-center
                                       justify-center gap-2
                                       rounded-xl
                                       bg-sky-700
                                       hover:bg-sky-800
                                       text-white
                                       px-6 py-3.5
                                       font-semibold
                                       transition"
                            >

                                <i class="fa-solid fa-pen-to-square"></i>

                                Correct Registration

                            </a>

                        </div>

                    </div>

                @else

                    <div class="p-8 text-center">

                        <i
                            class="fa-solid fa-circle-info
                                   text-4xl text-slate-400"
                        ></i>

                        <h1
                            class="text-xl font-bold
                                   text-slate-900 mt-4"
                        >
                            Registration Status
                        </h1>

                        <p
                            class="text-sm text-slate-500
                                   mt-2"
                        >
                            Current status:
                            {{ $consumer->verification_status }}
                        </p>

                    </div>

                @endif

            </div>


            <div class="mt-6 text-center">

                <form
                    method="POST"
                    action="{{ route('logout') }}"
                >

                    @csrf

                    <button
                        type="submit"
                        class="inline-flex items-center
                               gap-2 text-sm
                               font-medium text-slate-500
                               hover:text-red-600
                               transition"
                    >

                        <i class="fa-solid fa-right-from-bracket"></i>

                        Sign Out

                    </button>

                </form>

            </div>

        </div>

    </div>

@endsection
