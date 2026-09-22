@extends('admin.layouts.app')

@section('title', 'Review Consumer Registration')

@section('content')

    <div class="max-w-6xl mx-auto space-y-6"
        x-data="{
            rejectModal: {{ $errors->has('verification_reason') ? 'true' : 'false' }},
            approveModal: false,
            approving: false,
            rejecting: false
        }">

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">

            <div>

                <a href="{{ route('admin.consumer-verifications.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-medium text-sky-700 hover:text-sky-800">

                    <i class="fa-solid fa-arrow-left"></i>

                    Back to Consumer Verifications

                </a>

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-4">
                    Review Consumer Registration
                </h1>

                <p class="text-sm text-slate-500 mt-2">
                    Verify the submitted information against official
                    Sagay Water District records before approving access.
                </p>

            </div>

            <div>

                @if ($consumer->verification_status === 'Verified')

                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-green-100 text-green-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-circle-check"></i>

                        Verified

                    </span>

                @elseif ($consumer->verification_status === 'Rejected')

                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-red-100 text-red-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-circle-xmark"></i>

                        Rejected

                    </span>

                @else

                    <span
                        class="inline-flex items-center gap-2 rounded-full bg-amber-100 text-amber-700 px-4 py-2 text-sm font-semibold">

                        <i class="fa-solid fa-clock"></i>

                        Pending Verification

                    </span>

                @endif

            </div>

        </div>


        @if ($consumer->verification_status === 'Pending Verification')

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center shrink-0">

                        <i class="fa-solid fa-shield-halved"></i>

                    </div>

                    <div>

                        <h2 class="font-semibold text-amber-900">
                            Verification Required
                        </h2>

                        <p class="text-sm text-amber-800 mt-1 leading-6">
                            Confirm that the account number and consumer
                            name match official Sagay Water District records
                            before approving this registration.
                        </p>

                    </div>

                </div>

            </div>

        @endif


        <div class="grid lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">

                                <i class="fa-solid fa-droplet"></i>

                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Water Service Account
                                </h2>

                                <p class="text-xs text-slate-500 mt-1">
                                    Verify against the official SWD account record.
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="p-6">

                        <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">
                            Account Number
                        </p>

                        <p class="text-2xl font-bold text-sky-700 mt-2">
                            {{ $consumer->account_number }}
                        </p>

                    </div>

                </div>


                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <h2 class="font-bold text-slate-900">
                                Consumer Information
                            </h2>

                        </div>

                    </div>


                    <div class="p-6 grid sm:grid-cols-2 gap-6">

                        <div class="sm:col-span-2">

                            <p class="text-xs text-slate-500">
                                Registered Consumer Name
                            </p>

                            <p class="font-semibold text-slate-900 mt-1">
                                {{ $consumer->full_name }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-500">
                                Sex
                            </p>

                            <p class="font-medium text-slate-800 mt-1">
                                {{ $consumer->sex ?? '—' }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-500">
                                Registration Source
                            </p>

                            <p class="font-medium text-slate-800 mt-1">
                                {{ $consumer->registration_source }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-500">
                                Email Address
                            </p>

                            <p class="font-medium text-slate-800 mt-1 break-all">
                                {{ $consumer->email }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-500">
                                Mobile Number
                            </p>

                            <p class="font-medium text-slate-800 mt-1">
                                {{ $consumer->phone }}
                            </p>

                        </div>

                    </div>

                </div>


                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                    <div class="px-6 py-5 border-b border-slate-200">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-10 h-10 rounded-xl bg-cyan-100 text-cyan-700 flex items-center justify-center">

                                <i class="fa-solid fa-location-dot"></i>

                            </div>

                            <h2 class="font-bold text-slate-900">
                                Service Address
                            </h2>

                        </div>

                    </div>


                    <div class="p-6">

                        @if ($consumer->address)

                            <p class="font-medium text-slate-800 leading-7">
                                {{ $consumer->address->full_address ?: 'No complete address provided.' }}
                            </p>

                            @if ($consumer->address->zip_code)

                                <p class="text-sm text-slate-500 mt-1">
                                    ZIP Code:
                                    {{ $consumer->address->zip_code }}
                                </p>

                            @endif

                        @else

                            <p class="text-sm text-slate-500">
                                No service address record is available.
                            </p>

                        @endif

                    </div>

                </div>

            </div>


            <div class="space-y-6">

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                    <h2 class="font-bold text-slate-900">
                        Registration Details
                    </h2>

                    <div class="mt-5 space-y-5">

                        <div>

                            <p class="text-xs text-slate-500">
                                Submitted
                            </p>

                            <p class="text-sm font-medium text-slate-800 mt-1">
                                {{ $consumer->created_at?->format('F d, Y') }}
                            </p>

                            <p class="text-xs text-slate-400 mt-1">
                                {{ $consumer->created_at?->format('h:i A') }}
                            </p>

                        </div>


                        <div>

                            <p class="text-xs text-slate-500">
                                Portal Account
                            </p>

                            @if ($consumer->user?->is_active)

                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-green-600">

                                    <i class="fa-solid fa-circle-check"></i>

                                    Active

                                </span>

                            @else

                                <span class="inline-flex items-center gap-1 mt-1 text-sm font-semibold text-slate-500">

                                    <i class="fa-solid fa-circle-pause"></i>

                                    Inactive

                                </span>

                            @endif

                        </div>


                        @if ($consumer->verified_at)

                            <div>

                                <p class="text-xs text-slate-500">
                                    Reviewed
                                </p>

                                <p class="text-sm font-medium text-slate-800 mt-1">
                                    {{ $consumer->verified_at->format('F d, Y h:i A') }}
                                </p>

                            </div>

                        @endif


                        @if ($consumer->verifier)

                            <div>

                                <p class="text-xs text-slate-500">
                                    Reviewed By
                                </p>

                                <p class="text-sm font-medium text-slate-800 mt-1">
                                    {{ $consumer->verifier->full_name }}
                                </p>

                            </div>

                        @endif

                    </div>

                </div>


                @if ($consumer->verification_status === 'Pending Verification')

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6">

                        <h2 class="font-bold text-slate-900">
                            Verification Decision
                        </h2>

                        <p class="text-sm text-slate-500 mt-2 leading-6">
                            Approve the registration if the submitted information
                            matches official SWD records.
                        </p>


                        <div class="mt-5 space-y-3">

                            <button
                                type="button"
                                @click="approveModal = true"
                                class="w-full inline-flex items-center justify-center gap-2
                                       rounded-xl bg-green-600 text-white
                                       px-5 py-3 font-semibold
                                       hover:bg-green-700
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-green-500
                                       focus:ring-offset-2
                                       transition">

                                <i class="fa-solid fa-check"></i>

                                Approve Registration

                            </button>


                            <button
                                type="button"
                                @click="rejectModal = true"
                                class="w-full inline-flex items-center justify-center gap-2
                                       rounded-xl border border-red-300
                                       bg-white text-red-700
                                       px-5 py-3 font-semibold
                                       hover:bg-red-50
                                       focus:outline-none
                                       focus:ring-2
                                       focus:ring-red-500
                                       focus:ring-offset-2
                                       transition">

                                <i class="fa-solid fa-xmark"></i>

                                Reject Registration

                            </button>

                        </div>

                    </div>

                @endif


                @if ($consumer->verification_status === 'Rejected')

                    <div class="rounded-2xl border border-red-200 bg-red-50 p-6">

                        <div class="flex items-center gap-2">

                            <i class="fa-solid fa-circle-xmark text-red-600"></i>

                            <h2 class="font-bold text-red-900">
                                Verification Reason
                            </h2>

                        </div>

                        <p class="text-sm text-red-800 mt-3 leading-6">
                            {{ $consumer->verification_reason ?: 'No reason was provided.' }}
                        </p>

                    </div>

                @endif

            </div>

        </div>


        <div
            x-show="approveModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4">

            <div
                @click.outside="approveModal = false"
                class="w-full max-w-md rounded-2xl bg-white shadow-2xl overflow-hidden">

                <div class="p-6">

                    <div
                        class="w-14 h-14 rounded-full bg-green-100 text-green-600 flex items-center justify-center mx-auto">

                        <i class="fa-solid fa-user-check text-2xl"></i>

                    </div>


                    <div class="text-center mt-4">

                        <h2 class="text-xl font-bold text-slate-900">
                            Approve Registration?
                        </h2>

                        <p class="text-sm text-slate-500 mt-2 leading-6">
                            You are approving the registration for
                            <strong class="text-slate-700">
                                {{ $consumer->full_name }}
                            </strong>.

                            Their iSWD Consumer Portal account will become active.
                        </p>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('admin.consumer-verifications.approve', $consumer) }}"
                        @submit="approving = true"
                        class="mt-6">

                        @csrf
                        @method('PATCH')


                        <div class="grid grid-cols-2 gap-3">

                            <button
                                type="button"
                                @click="approveModal = false"
                                :disabled="approving"
                                class="inline-flex items-center justify-center
                                       rounded-xl border border-slate-300
                                       px-4 py-3 font-semibold text-slate-700
                                       hover:bg-slate-50
                                       disabled:opacity-60
                                       disabled:cursor-not-allowed
                                       transition">

                                Cancel

                            </button>


                            <button
                                type="submit"
                                :disabled="approving"
                                class="inline-flex items-center justify-center
                                       rounded-xl bg-green-600
                                       px-4 py-3 font-semibold text-white
                                       hover:bg-green-700
                                       disabled:opacity-60
                                       disabled:cursor-not-allowed
                                       transition">

                                <span
                                    x-show="!approving"
                                    class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-check"></i>

                                    Approve

                                </span>


                                <span
                                    x-show="approving"
                                    x-cloak
                                    class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-spinner animate-spin"></i>

                                    Approving...

                                </span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <div
            x-show="rejectModal"
            x-cloak
            x-transition.opacity
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 p-4">

            <div
                @click.outside="rejectModal = false"
                class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">

                <div class="p-6">

                    <div class="flex items-start gap-4">

                        <div
                            class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0">

                            <i class="fa-solid fa-user-xmark text-xl"></i>

                        </div>


                        <div>

                            <h2 class="text-xl font-bold text-slate-900">
                                Reject Registration
                            </h2>

                            <p class="text-sm text-slate-500 mt-1 leading-6">
                                Provide a clear reason for rejecting this
                                consumer registration.
                            </p>

                        </div>

                    </div>


                    <form
                        method="POST"
                        action="{{ route('admin.consumer-verifications.reject', $consumer) }}"
                        @submit="rejecting = true"
                        class="mt-6">

                        @csrf
                        @method('PATCH')


                        <label
                            for="verification_reason"
                            class="block text-sm font-semibold text-slate-700 mb-2">

                            Verification Reason

                            <span class="text-red-500">*</span>

                        </label>


                        <textarea
                            id="verification_reason"
                            name="verification_reason"
                            rows="5"
                            required
                            maxlength="1000"
                            placeholder="Example: The submitted account number does not match the registered consumer name in Sagay Water District records."
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-red-500
                                   focus:ring-red-500
                                   px-4 py-3">{{ old('verification_reason') }}</textarea>


                        @error('verification_reason')

                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>

                        @enderror


                        <div class="mt-6 grid grid-cols-2 gap-3">

                            <button
                                type="button"
                                @click="rejectModal = false"
                                :disabled="rejecting"
                                class="inline-flex items-center justify-center
                                       rounded-xl border border-slate-300
                                       px-4 py-3 font-semibold text-slate-700
                                       hover:bg-slate-50
                                       disabled:opacity-60
                                       disabled:cursor-not-allowed
                                       transition">

                                Cancel

                            </button>


                            <button
                                type="submit"
                                :disabled="rejecting"
                                class="inline-flex items-center justify-center
                                       rounded-xl bg-red-600
                                       px-4 py-3 font-semibold text-white
                                       hover:bg-red-700
                                       disabled:opacity-60
                                       disabled:cursor-not-allowed
                                       transition">

                                <span
                                    x-show="!rejecting"
                                    class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-xmark"></i>

                                    Confirm Rejection

                                </span>


                                <span
                                    x-show="rejecting"
                                    x-cloak
                                    class="flex items-center justify-center gap-2">

                                    <i class="fa-solid fa-spinner animate-spin"></i>

                                    Rejecting...

                                </span>

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection
