
@extends('consumer.layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="mx-auto max-w-5xl space-y-6">

    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm font-medium text-blue-700">Account Settings</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                My Profile
            </h1>
            <p class="mt-1 text-sm text-slate-500">
                View your registered information and service address.
            </p>
        </div>

        <a href="{{ route('consumer.profile.edit') }}"
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-800">
            <i class="fa-solid fa-pen-to-square"></i>
            Edit Profile
        </a>
    </div>

    @if (session('success'))
        <div role="status"
             class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
            <i class="fa-solid fa-circle-check mr-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="bg-gradient-to-r from-blue-800 to-blue-600 px-6 py-7 text-white sm:px-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold ring-1 ring-white/30">
                    {{ strtoupper(substr($consumer->first_name ?? 'C', 0, 1)) }}
                </div>

                <div class="min-w-0 flex-1">
                    <h2 class="text-xl font-bold">
                        {{ $consumer->full_name }}
                    </h2>
                    <p class="mt-1 text-sm text-blue-100">
                        Consumer Account
                    </p>
                    <p class="mt-2 text-sm">
                        Account No.:
                        <span class="font-semibold">{{ $consumer->account_number }}</span>
                    </p>
                </div>

                <div class="self-start rounded-full bg-white/15 px-3 py-1.5 text-xs font-semibold ring-1 ring-white/20">
                    {{ $consumer->verification_status ?? 'Pending Verification' }}
                </div>
            </div>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-base font-bold text-slate-900">Personal Information</h3>

            <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">First Name</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->first_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Middle Name</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->middle_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Last Name</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->last_name ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Suffix</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->suffix ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Sex</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->sex ?: '—' }}</dd>
                </div>
            </dl>

            <div class="my-7 border-t border-slate-100"></div>

            <h3 class="text-base font-bold text-slate-900">Contact Information</h3>

            <dl class="mt-5 grid grid-cols-1 gap-x-8 gap-y-5 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Email Address</dt>
                    <dd class="mt-1 break-words text-sm font-medium text-slate-800">{{ $consumer->email ?: '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Phone Number</dt>
                    <dd class="mt-1 text-sm font-medium text-slate-800">{{ $consumer->phone ?: '—' }}</dd>
                </div>
            </dl>

            <div class="my-7 border-t border-slate-100"></div>

            <h3 class="text-base font-bold text-slate-900">Service Address</h3>

            @php($address = $consumer->address)

            @if ($address)
                <p class="mt-3 text-sm leading-7 text-slate-600">
                    {{ $address->house_no ? $address->house_no . ', ' : '' }}
                    {{ $address->street ? $address->street . ', ' : '' }}
                    {{ $address->purok ? $address->purok . ', ' : '' }}
                    {{ $address->barangay }},
                    {{ $address->municipality ? $address->municipality . ', ' : '' }}
                    {{ $address->province }}
                    {{ $address->zip_code ? $address->zip_code : '' }}
                </p>
            @else
                <p class="mt-3 text-sm text-slate-500">No service address has been recorded.</p>
            @endif

            <div class="mt-6 rounded-xl border border-blue-100 bg-blue-50 p-4">
                <div class="flex gap-3">
                    <i class="fa-solid fa-shield-halved mt-0.5 text-blue-700"></i>
                    <div>
                        <p class="text-sm font-semibold text-blue-900">Account verification</p>
                        <p class="mt-1 text-sm leading-6 text-blue-800">
                            Your account number and verification status are managed by Sagay Water District.
                            Contact the appropriate office if your verification details need correction.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
