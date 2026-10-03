@extends('consumer.auth.layout')

@section('title', 'Registration Status')

@section('content')

<div class="relative min-h-screen overflow-hidden bg-slate-50">
    <div class="absolute inset-x-0 top-0 h-72 bg-gradient-to-b from-sky-700 via-blue-700 to-cyan-700"></div>

    <div class="pointer-events-none absolute inset-x-0 top-0 h-72 overflow-hidden">
        <div class="absolute -left-24 -top-24 h-72 w-72 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -right-20 top-8 h-72 w-72 rounded-full bg-cyan-300/10 blur-3xl"></div>
    </div>

    <div class="relative z-10 mx-auto max-w-3xl px-4 py-6 sm:px-6 sm:py-8">
        <div class="flex items-center justify-between">
            <a
                href="{{ route('landing') }}"
                class="flex items-center gap-3 text-white"
            >
                <div class="flex h-11 w-11 items-center justify-center rounded-xl border border-white/20 bg-white p-1">
                    <img
                        src="{{ asset('images/logo/logo.png') }}"
                        alt="iSWD Logo"
                        class="h-full w-full object-contain"
                    >
                </div>

                <div>
                    <p class="text-xl font-semibold leading-none">
                        iSWD
                    </p>

                    <p class="mt-1 text-[10px] font-medium uppercase tracking-[0.12em] text-sky-100">
                        Sagay Water District
                    </p>
                </div>
            </a>

            <form
                method="POST"
                action="{{ route('logout') }}"
            >
                @csrf

                <button
                    type="submit"
                    class="inline-flex items-center gap-2 rounded-xl border border-white/20 bg-white/10 px-3.5 py-2 text-xs font-medium text-white backdrop-blur-sm transition hover:bg-white/20 sm:px-4 sm:text-sm"
                >
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span class="hidden sm:inline">
                        Sign Out
                    </span>
                </button>
            </form>
        </div>

        <div class="mt-7">
            @if (session('success'))
                <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-3.5 shadow-sm">
                    <i class="fa-solid fa-circle-check mt-0.5 text-emerald-600"></i>

                    <p class="text-sm leading-5 text-emerald-800">
                        {{ session('success') }}
                    </p>
                </div>
            @endif

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl shadow-slate-900/10">
                @if ($consumer->verification_status === 'Pending Verification')

                    <div class="border-b border-slate-100 p-6 text-center sm:p-8">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                            <i class="fa-solid fa-clock text-xl"></i>
                        </div>

                        <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-xs font-semibold text-amber-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                            Pending Verification
                        </span>

                        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                            Registration Under Review
                        </h1>

                        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                            Your consumer registration has been received and is currently being reviewed by Sagay Water District.
                        </p>
                    </div>

                    <div class="grid sm:grid-cols-2">
                        <div class="border-b border-slate-100 p-5 sm:border-b-0 sm:border-r">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-500">
                                    <i class="fa-solid fa-droplet text-xs"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-medium text-slate-400">
                                        Water Account
                                    </p>

                                    <p class="mt-1 truncate text-sm font-semibold text-slate-800">
                                        {{ $consumer->account_number }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                                    <i class="fa-solid fa-magnifying-glass text-xs"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-medium text-slate-400">
                                        Current Stage
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-800">
                                        SWD Verification
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 bg-slate-50 p-5 sm:p-6">
                        <div class="flex items-start gap-3">
                            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-100 text-sky-700">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>

                            <div>
                                <p class="text-sm font-semibold text-slate-800">
                                    What happens next?
                                </p>

                                <p class="mt-1 text-xs leading-5 text-slate-600 sm:text-sm sm:leading-6">
                                    Sagay Water District will verify your submitted account information. Consumer Portal access becomes available after the registration is approved.
                                </p>
                            </div>
                        </div>
                    </div>

                @elseif ($consumer->verification_status === 'Rejected')

                    <div class="border-b border-slate-100 p-6 text-center sm:p-8">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                            <i class="fa-solid fa-file-circle-exclamation text-xl"></i>
                        </div>

                        <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                            Correction Required
                        </span>

                        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                            Registration Needs Correction
                        </h1>

                        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                            Sagay Water District could not verify some of the information submitted with your registration.
                        </p>
                    </div>

                    <div class="p-5 sm:p-6">
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-red-100 text-red-600">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-xs font-semibold uppercase tracking-[0.12em] text-red-600">
                                        Verification Feedback
                                    </p>

                                    <p class="mt-1.5 text-sm leading-6 text-red-800">
                                        {{ $consumer->verification_reason ?: 'Your submitted registration information could not be verified.' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-3 sm:grid-cols-2">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sky-700 shadow-sm">
                                        <i class="fa-solid fa-droplet text-xs"></i>
                                    </div>

                                    <div class="min-w-0">
                                        <p class="text-xs text-slate-400">
                                            Submitted Account
                                        </p>

                                        <p class="mt-0.5 truncate text-sm font-semibold text-slate-800">
                                            {{ $consumer->account_number }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            @if ($consumer->verified_at)
                                <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-slate-500 shadow-sm">
                                            <i class="fa-solid fa-calendar-check text-xs"></i>
                                        </div>

                                        <div>
                                            <p class="text-xs text-slate-400">
                                                Reviewed
                                            </p>

                                            <p class="mt-0.5 text-sm font-semibold text-slate-800">
                                                {{ $consumer->verified_at->format('M d, Y h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <a
                            href="{{ route('consumer.registration.edit') }}"
                            class="mt-5 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg"
                        >
                            <i class="fa-solid fa-pen-to-square"></i>
                            Correct Registration Information
                        </a>

                        <p class="mt-3 text-center text-xs leading-5 text-slate-400">
                            Update the information identified above and submit your registration for another review.
                        </p>
                    </div>

                @elseif ($consumer->verification_status === 'Verified')

                    <div class="p-6 text-center sm:p-8">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <i class="fa-solid fa-circle-check text-xl"></i>
                        </div>

                        <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                            Verified
                        </span>

                        <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">
                            Registration Approved
                        </h1>

                        <p class="mx-auto mt-2 max-w-lg text-sm leading-6 text-slate-500">
                            Your Sagay Water District consumer registration has been verified and your Consumer Portal account is ready for use.
                        </p>

                        <div class="mx-auto mt-5 max-w-sm rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-slate-500">
                                    Account Number
                                </span>

                                <span class="text-sm font-semibold text-slate-800">
                                    {{ $consumer->account_number }}
                                </span>
                            </div>
                        </div>

                        <a
                            href="{{ route('consumer.login') }}"
                            class="mt-5 inline-flex w-full max-w-sm items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-6 py-3 text-sm font-semibold text-white shadow-md transition hover:shadow-lg"
                        >
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Continue to Consumer Portal
                        </a>
                    </div>

                @else

                    <div class="p-7 text-center sm:p-8">
                        <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-500">
                            <i class="fa-solid fa-circle-info text-xl"></i>
                        </div>

                        <h1 class="mt-4 text-xl font-semibold text-slate-900">
                            Registration Status
                        </h1>

                        <p class="mt-2 text-sm text-slate-500">
                            Current status:
                            <span class="font-semibold text-slate-700">
                                {{ $consumer->verification_status }}
                            </span>
                        </p>
                    </div>

                @endif
            </div>

            <div class="mt-4 flex items-center justify-center gap-3 text-[11px] text-slate-400">
                <span>
                    &copy; {{ date('Y') }} Sagay Water District
                </span>

                <span class="h-1 w-1 rounded-full bg-slate-300"></span>

                <a
                    href="{{ route('landing') }}"
                    class="transition hover:text-sky-700"
                >
                    iSWD Home
                </a>
            </div>
        </div>
    </div>
</div>

@endsection
