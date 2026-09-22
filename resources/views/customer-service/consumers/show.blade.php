@extends('customer-service.layouts.app')

@section('title', 'Consumer Details')

@section('content')

    <div class="space-y-6">

        {{-- ========================================================= --}}
        {{-- FLASH SUCCESS --}}
        {{-- ========================================================= --}}

        @if (session('success') && !session('temporary_password'))
            <div class="rounded-xl border border-green-200
                       bg-green-50 p-4">

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-check text-green-600"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- ONE-TIME ACCOUNT CREDENTIALS --}}
        {{-- ========================================================= --}}

        @if (session('temporary_password'))
            <div class="rounded-2xl border border-green-300
                       bg-green-50 overflow-hidden shadow-sm">

                <div class="p-5 sm:p-6">

                    <div
                        class="flex flex-col lg:flex-row
                               lg:items-start lg:justify-between
                               gap-6">

                        <div class="flex items-start gap-4">

                            <div
                                class="w-12 h-12 rounded-xl
                                       bg-green-100 text-green-600
                                       flex items-center justify-center
                                       shrink-0">
                                <i class="fa-solid fa-key text-xl"></i>
                            </div>


                            <div>

                                <h2 class="text-lg font-bold text-green-900">
                                    Consumer Online Account Created
                                </h2>

                                <p class="text-sm text-green-700 mt-1 max-w-xl">
                                    The consumer account was successfully created.
                                    Give these login credentials to the consumer.
                                </p>

                            </div>

                        </div>


                        <div
                            class="w-full lg:w-[420px]
                                   rounded-xl bg-white
                                   border border-green-200 p-5">

                            {{-- Email --}}
                            <div>

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-500">
                                    Login Email
                                </p>

                                <div class="flex items-center gap-2 mt-2">

                                    <div id="loginEmail"
                                        class="flex-1 rounded-lg
                                               bg-gray-50 border border-gray-200
                                               px-3 py-2.5
                                               text-sm font-medium
                                               text-gray-900 break-all">
                                        {{ $consumer->user?->email ?? $consumer->email }}
                                    </div>

                                    <button type="button"
                                        onclick="copyCredential(
                                            'loginEmail',
                                            this
                                        )"
                                        class="w-10 h-10 rounded-lg
                                               border border-gray-300
                                               text-gray-600
                                               hover:bg-gray-50
                                               shrink-0"
                                        title="Copy email">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>

                                </div>

                            </div>


                            {{-- Temporary Password --}}
                            <div class="mt-4">

                                <p
                                    class="text-xs uppercase tracking-wide
                                           font-semibold text-gray-500">
                                    Temporary Password
                                </p>

                                <div class="flex items-center gap-2 mt-2">

                                    <div id="temporaryPassword"
                                        class="flex-1 rounded-lg
                                               bg-gray-50 border border-gray-200
                                               px-3 py-2.5
                                               font-mono font-bold
                                               text-gray-900">
                                        {{ session('temporary_password') }}
                                    </div>


                                    <button type="button"
                                        onclick="copyCredential(
                                            'temporaryPassword',
                                            this
                                        )"
                                        class="w-10 h-10 rounded-lg
                                               border border-gray-300
                                               text-gray-600
                                               hover:bg-gray-50
                                               shrink-0"
                                        title="Copy password">
                                        <i class="fa-solid fa-copy"></i>
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    <div
                        class="mt-5 rounded-xl
                               bg-amber-50 border border-amber-200
                               p-4">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-triangle-exclamation
                                       text-amber-600 mt-0.5"></i>

                            <div>

                                <p class="text-sm font-semibold text-amber-900">
                                    Save or give these credentials to the consumer now.
                                </p>

                                <p class="text-sm text-amber-800 mt-1">
                                    The temporary password is displayed only once.
                                    It will not be available again after leaving or refreshing this page.
                                    Customer Service can reset the password from Edit Consumer if necessary.
                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>
        @endif


        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col md:flex-row
                   md:items-center md:justify-between gap-4">

            <div>

                <p class="text-sm text-gray-500">
                    Consumer Management
                </p>

                <h1 class="text-2xl font-bold text-gray-900">
                    {{ $consumer->full_name }}
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Account #{{ $consumer->account_number }}
                </p>

            </div>


            <div class="flex flex-col sm:flex-row gap-3">

                <a href="{{ route('customer-service.consumers.index') }}"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           border border-gray-300
                           text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-arrow-left"></i>
                    Back
                </a>


                <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                    class="inline-flex items-center justify-center gap-2
                           px-4 py-2.5 rounded-xl
                           bg-blue-600 text-white
                           hover:bg-blue-700">
                    <i class="fa-solid fa-pen-to-square"></i>
                    Edit Consumer
                </a>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- CONSUMER INFORMATION --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm">

            <div class="px-5 sm:px-6 py-5
                       border-b border-gray-100">

                <div class="flex flex-col sm:flex-row
                           sm:items-center sm:justify-between gap-3">

                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Consumer Information
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Personal and SWD account information.
                        </p>

                    </div>


                    @if ($consumer->is_active)
                        <span
                            class="inline-flex items-center gap-1.5
                                   self-start px-3 py-1.5 rounded-full
                                   bg-green-100 text-green-700
                                   text-xs font-semibold">
                            <i class="fa-solid fa-circle-check"></i>
                            Active
                        </span>
                    @else
                        <span
                            class="inline-flex items-center gap-1.5
                                   self-start px-3 py-1.5 rounded-full
                                   bg-red-100 text-red-700
                                   text-xs font-semibold">
                            <i class="fa-solid fa-circle-xmark"></i>
                            Inactive
                        </span>
                    @endif

                </div>

            </div>


            <div class="p-5 sm:p-6">

                <div
                    class="grid grid-cols-1
                           sm:grid-cols-2
                           xl:grid-cols-4 gap-6">

                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Account Number
                        </p>

                        <p class="mt-1.5 font-semibold text-gray-900">
                            {{ $consumer->account_number }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Full Name
                        </p>

                        <p class="mt-1.5 font-semibold text-gray-900">
                            {{ $consumer->full_name }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Sex
                        </p>

                        <p class="mt-1.5 text-gray-900">
                            {{ $consumer->sex ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Registered
                        </p>

                        <p class="mt-1.5 text-gray-900">
                            {{ $consumer->created_at?->format('F d, Y') ?? '—' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Contact Number
                        </p>

                        <p class="mt-1.5 text-gray-900">
                            {{ $consumer->phone ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Email Address
                        </p>

                        <p class="mt-1.5 text-gray-900 break-all">
                            {{ $consumer->email ?: '—' }}
                        </p>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Online Account
                        </p>

                        <div class="mt-1.5">

                            @if ($consumer->user)
                                <span
                                    class="inline-flex items-center gap-1.5
                                           text-sm font-medium
                                           {{ $consumer->user->is_active ? 'text-green-600' : 'text-red-600' }}">

                                    <i
                                        class="fa-solid
                                               {{ $consumer->user->is_active ? 'fa-circle-check' : 'fa-circle-xmark' }}"></i>

                                    {{ $consumer->user->is_active ? 'Active' : 'Inactive' }}

                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1.5
                                           text-sm font-medium text-amber-600">
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                    Account Missing
                                </span>
                            @endif

                        </div>

                    </div>


                    <div>

                        <p class="text-xs font-medium uppercase tracking-wide text-gray-500">
                            Record Status
                        </p>

                        <p class="mt-1.5 text-gray-900">
                            {{ $consumer->is_active ? 'Active Consumer' : 'Inactive Consumer' }}
                        </p>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- RESIDENTIAL ADDRESS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm">

            <div class="px-5 sm:px-6 py-5
                       border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-blue-100 text-blue-600
                               flex items-center justify-center">
                        <i class="fa-solid fa-location-dot"></i>
                    </div>


                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Residential Address
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Registered residential address of the consumer.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-5 sm:p-6">

                @if ($consumer->address)
                    @php
                        $addressParts = array_filter([
                            $consumer->address->house_no,
                            $consumer->address->street,
                            $consumer->address->purok,
                            $consumer->address->barangay,
                            $consumer->address->municipality,
                            $consumer->address->province,

                        ]);
                    @endphp


                    <div class="p-4 rounded-xl
                               bg-blue-50 border border-blue-100">
                        <p class="font-medium text-gray-900">
                            {{ implode(', ', $addressParts) }}
                        </p>
                    </div>


                    <div class="grid grid-cols-1 sm:grid-cols-2
                               lg:grid-cols-4 gap-5 mt-6">

                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                House No.
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->house_no ?: '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Street
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->street ?: '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Purok
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->purok ?: '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Barangay
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->barangay ?: '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Municipality
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->municipality ?: '—' }}
                            </p>
                        </div>


                        <div>
                            <p class="text-xs uppercase text-gray-500">
                                Province
                            </p>

                            <p class="mt-1 text-gray-900">
                                {{ $consumer->address->province ?: '—' }}
                            </p>
                        </div>


                    </div>
                @else
                    <div class="py-8 text-center">

                        <i class="fa-solid fa-location-dot
                                   text-3xl text-gray-300"></i>

                        <p class="text-gray-500 mt-3">
                            No residential address recorded.
                        </p>

                    </div>
                @endif

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ONLINE PORTAL --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm">

            <div class="px-5 sm:px-6 py-5
                       border-b border-gray-100">

                <div class="flex items-center gap-3">

                    <div
                        class="w-10 h-10 rounded-xl
                               bg-cyan-100 text-cyan-700
                               flex items-center justify-center">
                        <i class="fa-solid fa-globe"></i>
                    </div>

                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Consumer Portal
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            iSWD online complaint access.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-5 sm:p-6">

                @if ($consumer->user)
                    <div
                        class="rounded-xl
                               {{ $consumer->user->is_active ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}
                               border p-5">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid
                                       {{ $consumer->user->is_active ? 'fa-circle-check text-green-600' : 'fa-circle-xmark text-red-600' }}
                                       mt-1"></i>

                            <div>

                                <p
                                    class="font-semibold
                                           {{ $consumer->user->is_active ? 'text-green-900' : 'text-red-900' }}">
                                    {{ $consumer->user->is_active ? 'Portal Account Active' : 'Portal Account Inactive' }}
                                </p>

                                <p
                                    class="text-sm mt-1
                                           {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $consumer->user->is_active
                                        ? 'This consumer can log in and use the iSWD consumer portal.'
                                        : 'This consumer is currently prevented from accessing the iSWD consumer portal.' }}
                                </p>

                                <p
                                    class="text-sm mt-2
                                           {{ $consumer->user->is_active ? 'text-green-700' : 'text-red-700' }}">
                                    <i class="fa-solid fa-envelope mr-1"></i>
                                    {{ $consumer->user->email }}
                                </p>

                            </div>

                        </div>

                    </div>
                @else
                    <div
                        class="rounded-xl
                               bg-amber-50 border border-amber-200
                               p-5">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-triangle-exclamation
                                       text-amber-600 mt-1"></i>

                            <div>

                                <p class="font-semibold text-amber-900">
                                    Portal Account Missing
                                </p>

                                <p class="text-sm text-amber-700 mt-1">
                                    This consumer record does not have a linked User account.
                                    This may be an older consumer record created before automatic portal accounts were
                                    enabled.
                                </p>

                            </div>

                        </div>

                    </div>
                @endif

            </div>

        </div>

    </div>


    {{-- ========================================================= --}}
    {{-- COPY CREDENTIAL SCRIPT --}}
    {{-- ========================================================= --}}

    @if (session('temporary_password'))
        <script>
            function copyCredential(elementId, button) {

                const element =
                    document.getElementById(elementId);

                if (!element) {
                    return;
                }

                const value =
                    element.innerText.trim();

                navigator.clipboard
                    .writeText(value)
                    .then(function() {

                        const icon =
                            button.querySelector('i');

                        icon.classList.remove('fa-copy');
                        icon.classList.add('fa-check');

                        setTimeout(function() {

                            icon.classList.remove('fa-check');
                            icon.classList.add('fa-copy');

                        }, 1500);

                    });

            }
        </script>
    @endif

@endsection
