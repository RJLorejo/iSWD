@extends('admin.layouts.app')

@section('title', 'Consumer Verifications')

@section('content')

    <div class="max-w-7xl mx-auto space-y-4">

        {{-- ========================================================= --}}

        {{-- HEADER --}}

        {{-- ========================================================= --}}

        <div
            class="flex flex-col lg:flex-row

                   lg:items-center lg:justify-between

                   gap-4">

            <div>

                <p class="text-sm font-medium text-sky-600">

                    Consumer Management

                </p>

                <h1 class="text-base sm:text-2xl font-bold text-slate-900 mt-1">

                    Consumer Verifications

                </h1>

                <p class="text-sm text-slate-500 mt-2 max-w-2xl">

                    Review self-registered consumer accounts before allowing

                    access to the iSWD Consumer Portal.

                </p>

            </div>

            @if ($pendingCount > 0)
                <div
                    class="inline-flex items-center gap-2

                           rounded-xl

                           bg-amber-50

                           border border-amber-200

                           text-amber-800

                           px-3 py-2.5 text-sm">

                    <i class="fa-solid fa-clock"></i>

                    <span class="text-sm font-semibold">

                        {{ $pendingCount }}

                        {{ Str::plural('registration', $pendingCount) }}

                        awaiting review

                    </span>

                </div>
            @endif

        </div>

        {{-- ========================================================= --}}

        {{-- STATISTICS --}}

        {{-- ========================================================= --}}

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">

            {{-- Total --}}

            <div
                class="bg-white rounded-2xl

                       border border-slate-200

                       shadow-sm p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">

                            Total Registrations

                        </p>

                        <p class="text-2xl font-bold text-slate-900 mt-2">

                            {{ number_format($totalCount) }}

                        </p>

                    </div>

                    <div
                        class="w-9 h-9 rounded-xl

                               bg-slate-100 text-slate-600

                               flex items-center justify-center">

                        <i class="fa-solid fa-users text-base"></i>

                    </div>

                </div>

            </div>

            {{-- Pending --}}

            <div
                class="bg-white rounded-2xl

                       border border-amber-200

                       shadow-sm p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">

                            Pending

                        </p>

                        <p class="text-2xl font-bold text-amber-600 mt-2">

                            {{ number_format($pendingCount) }}

                        </p>

                    </div>

                    <div
                        class="w-9 h-9 rounded-xl

                               bg-amber-100 text-amber-600

                               flex items-center justify-center">

                        <i class="fa-solid fa-clock text-base"></i>

                    </div>

                </div>

            </div>

            {{-- Verified --}}

            <div
                class="bg-white rounded-2xl

                       border border-green-200

                       shadow-sm p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">

                            Verified

                        </p>

                        <p class="text-2xl font-bold text-green-600 mt-2">

                            {{ number_format($verifiedCount) }}

                        </p>

                    </div>

                    <div
                        class="w-9 h-9 rounded-xl

                               bg-green-100 text-green-600

                               flex items-center justify-center">

                        <i class="fa-solid fa-circle-check text-base"></i>

                    </div>

                </div>

            </div>

            {{-- Rejected --}}

            <div
                class="bg-white rounded-2xl

                       border border-red-200

                       shadow-sm p-4">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">

                            Rejected

                        </p>

                        <p class="text-2xl font-bold text-red-600 mt-2">

                            {{ number_format($rejectedCount) }}

                        </p>

                    </div>

                    <div
                        class="w-9 h-9 rounded-xl

                               bg-red-100 text-red-600

                               flex items-center justify-center">

                        <i class="fa-solid fa-circle-xmark text-base"></i>

                    </div>

                </div>

            </div>

        </div>

        {{-- ========================================================= --}}

        {{-- FILTERS --}}

        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl

                   border border-slate-200

                   shadow-sm p-4">

            <form method="GET" action="{{ route('admin.consumer-verifications.index') }}"
                class="grid md:grid-cols-12 gap-4">

                {{-- Search --}}

                <div class="md:col-span-7">

                    <label for="search"
                        class="block text-sm font-semibold

                               text-slate-700 mb-2">

                        Search

                    </label>

                    <div class="relative">

                        <i
                            class="fa-solid fa-magnifying-glass

                                   absolute left-4 top-1/2

                                   -translate-y-1/2

                                   text-slate-400"></i>

                        <input id="search" type="text" name="search" value="{{ request('search') }}"
                            placeholder="Account number, consumer name, email or phone..."
                            class="w-full rounded-xl

                                   border-slate-300

                                   focus:border-sky-500

                                   focus:ring-sky-500

                                   pl-10 pr-3 py-2.5 text-sm">

                    </div>

                </div>

                {{-- Status --}}

                <div class="md:col-span-3">

                    <label for="status"
                        class="block text-sm font-semibold

                               text-slate-700 mb-2">

                        Status

                    </label>

                    <select id="status" name="status"
                        class="w-full rounded-xl

                               border-slate-300

                               focus:border-sky-500

                               focus:ring-sky-500

                               px-3 py-2.5 text-sm">

                        <option value="Pending Verification" @selected($status === 'Pending Verification')>

                            Pending Verification

                        </option>

                        <option value="Verified" @selected($status === 'Verified')>

                            Verified

                        </option>

                        <option value="Rejected" @selected($status === 'Rejected')>

                            Rejected

                        </option>

                        <option value="All" @selected($status === 'All')>

                            All Registrations

                        </option>

                    </select>

                </div>

                {{-- Buttons --}}

                <div class="md:col-span-2

                           flex items-end gap-2">

                    <button type="submit"
                        class="flex-1 px-3 py-2.5 rounded-xl text-sm

                               bg-sky-700 text-white

                               font-semibold

                               hover:bg-sky-800

                               transition">

                        <i class="fa-solid fa-filter
                          text-xs">
                        </i>

                        Filter

                    </button>

                    <a href="{{ route('admin.consumer-verifications.index') }}" title="Reset filters"
                        class="px-3 py-2.5 rounded-xl text-sm

                               border border-slate-300

                               text-slate-600

                               hover:bg-slate-50

                               transition">

                        <i class="fa-solid fa-rotate-left"></i>

                    </a>

                </div>

            </form>

        </div>

        {{-- ========================================================= --}}

        {{-- REGISTRATIONS TABLE --}}

        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-200">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900">
                            Registration Requests
                        </h2>
                        <p class="text-xs text-slate-500 mt-0.5">
                            {{ $consumers->total() }} {{ Str::plural('record', $consumers->total()) }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Mobile / Tablet Cards --}}
            <div class="lg:hidden divide-y divide-slate-100">
                @forelse ($consumers as $consumer)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="text-sm font-bold text-slate-900">
                                        {{ $consumer->account_number }}
                                    </span>

                                    @if ($consumer->verification_status === 'Verified')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-0.5 text-[11px] font-semibold text-green-700">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Verified
                                        </span>
                                    @elseif ($consumer->verification_status === 'Rejected')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-0.5 text-[11px] font-semibold text-red-700">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            Rejected
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-700">
                                            <i class="fa-solid fa-clock"></i>
                                            Pending
                                        </span>
                                    @endif
                                </div>

                                <p class="mt-2 text-sm font-semibold text-slate-800">
                                    {{ $consumer->full_name }}
                                </p>

                                <p class="mt-1 break-all text-xs text-slate-500">
                                    {{ $consumer->email }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $consumer->phone }}
                                </p>
                            </div>

                            <div
                                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                                {{ strtoupper(substr($consumer->first_name ?? 'C', 0, 1)) }}
                            </div>
                        </div>

                        <div class="mt-3 flex items-center justify-between gap-3 border-t border-slate-100 pt-3">
                            <div class="text-xs text-slate-500">
                                <span>{{ $consumer->created_at?->format('M d, Y') }}</span>
                                <span class="text-slate-300">•</span>
                                <span>{{ $consumer->created_at?->format('h:i A') }}</span>
                            </div>

                            <a href="{{ route('admin.consumer-verifications.show', $consumer) }}"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                <i class="fa-solid fa-eye"></i>
                                Review
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div
                            class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <p class="mt-3 text-sm font-semibold text-slate-800">
                            No registrations found
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Desktop Table --}}
            <div class="hidden lg:block overflow-x-auto">
                <table class="w-full table-fixed">
                    <thead class="bg-slate-50">
                        <tr class="text-left text-[11px] font-semibold uppercase tracking-wide text-slate-500">
                            <th class="w-[15%] px-3 py-2.5 text-sm">Account</th>
                            <th class="w-[24%] px-3 py-2.5 text-sm">Consumer</th>
                            <th class="w-[25%] px-3 py-2.5 text-sm">Contact</th>
                            <th class="w-[14%] px-3 py-2.5 text-sm">Submitted</th>
                            <th class="w-[13%] px-3 py-2.5 text-sm">Status</th>
                            <th class="w-[9%] px-4 py-3 text-right">Action</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse ($consumers as $consumer)
                            <tr class="hover:bg-slate-50/70">
                                <td class="px-4 py-4 align-top">
                                    <p class="truncate text-sm font-semibold text-slate-900"
                                        title="{{ $consumer->account_number }}">
                                        {{ $consumer->account_number }}
                                    </p>
                                    <p class="mt-1 text-[11px] text-slate-400">
                                        Self Registration
                                    </p>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        <div
                                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700">
                                            {{ strtoupper(substr($consumer->first_name ?? 'C', 0, 1)) }}
                                        </div>

                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-semibold text-slate-900"
                                                title="{{ $consumer->full_name }}">
                                                {{ $consumer->full_name }}
                                            </p>
                                            <p class="mt-0.5 text-[11px] text-slate-500">
                                                {{ $consumer->sex ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <p class="truncate text-sm text-slate-700" title="{{ $consumer->email }}">
                                        {{ $consumer->email }}
                                    </p>
                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $consumer->phone }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    <p class="text-sm text-slate-700">
                                        {{ $consumer->created_at?->format('M d, Y') }}
                                    </p>
                                    <p class="mt-1 text-[11px] text-slate-400">
                                        {{ $consumer->created_at?->format('h:i A') }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 align-top">
                                    @if ($consumer->verification_status === 'Verified')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-[11px] font-semibold text-green-700 whitespace-nowrap">
                                            <i class="fa-solid fa-circle-check"></i>
                                            Verified
                                        </span>
                                    @elseif ($consumer->verification_status === 'Rejected')
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-[11px] font-semibold text-red-700 whitespace-nowrap">
                                            <i class="fa-solid fa-circle-xmark"></i>
                                            Rejected
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-1 text-[11px] font-semibold text-amber-700 whitespace-nowrap">
                                            <i class="fa-solid fa-clock"></i>
                                            Pending
                                        </span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 text-right align-top">
                                    <a href="{{ route('admin.consumer-verifications.show', $consumer) }}"
                                        class="inline-flex items-center gap-1.5 rounded-lg bg-sky-50 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                        <i class="fa-solid fa-eye"></i>
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-10 text-center">


                                    class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                    <i class="fa-solid fa-user-check"></i>
            </div>
            <p class="mt-3 text-sm font-semibold text-slate-800">
                No registrations found
            </p>
            </td>
            </tr>
            @endforelse
            </tbody>
            </table>
        </div>

        @if ($consumers->hasPages())
            <div class="border-t border-slate-200 px-4 sm:px-5 py-3">
                {{ $consumers->links() }}
            </div>
        @endif
    </div>

    </div>

@endsection
