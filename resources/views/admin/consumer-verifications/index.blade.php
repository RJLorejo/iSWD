@extends('admin.layouts.app')

@section('title', 'Consumer Verifications')

@section('content')

    <div class="max-w-7xl mx-auto space-y-6">


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

                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
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
                           px-4 py-3">

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

        <div class="grid sm:grid-cols-2 xl:grid-cols-4 gap-4">


            {{-- Total --}}

            <div
                class="bg-white rounded-2xl
                       border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Total Registrations
                        </p>

                        <p class="text-3xl font-bold text-slate-900 mt-2">
                            {{ number_format($totalCount) }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-slate-100 text-slate-600
                               flex items-center justify-center">

                        <i class="fa-solid fa-users text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Pending --}}

            <div
                class="bg-white rounded-2xl
                       border border-amber-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Pending
                        </p>

                        <p class="text-3xl font-bold text-amber-600 mt-2">
                            {{ number_format($pendingCount) }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-amber-100 text-amber-600
                               flex items-center justify-center">

                        <i class="fa-solid fa-clock text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Verified --}}

            <div
                class="bg-white rounded-2xl
                       border border-green-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Verified
                        </p>

                        <p class="text-3xl font-bold text-green-600 mt-2">
                            {{ number_format($verifiedCount) }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-green-100 text-green-600
                               flex items-center justify-center">

                        <i class="fa-solid fa-circle-check text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Rejected --}}

            <div
                class="bg-white rounded-2xl
                       border border-red-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between">

                    <div>

                        <p class="text-sm text-slate-500">
                            Rejected
                        </p>

                        <p class="text-3xl font-bold text-red-600 mt-2">
                            {{ number_format($rejectedCount) }}
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-red-100 text-red-600
                               flex items-center justify-center">

                        <i class="fa-solid fa-circle-xmark text-xl"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- FILTERS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm p-5">

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
                                   pl-11 pr-4 py-3">

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
                               px-4 py-3">

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
                        class="flex-1 px-4 py-3 rounded-xl
                               bg-sky-700 text-white
                               font-semibold
                               hover:bg-sky-800
                               transition">

                        Filter

                    </button>

                    <a href="{{ route('admin.consumer-verifications.index') }}" title="Reset filters"
                        class="px-4 py-3 rounded-xl
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

        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm overflow-hidden">

            <div class="px-6 py-5
                       border-b border-slate-200">

                <h2 class="font-bold text-slate-900">
                    Registration Requests
                </h2>

                <p class="text-sm text-slate-500 mt-1">
                    {{ $consumers->total() }}
                    {{ Str::plural('record', $consumers->total()) }}
                    found
                </p>

            </div>


            <div class="overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr
                            class="text-left text-xs
                                   font-semibold uppercase
                                   tracking-wider text-slate-500">

                            <th class="px-6 py-4">
                                Account
                            </th>

                            <th class="px-6 py-4">
                                Consumer
                            </th>

                            <th class="px-6 py-4">
                                Contact
                            </th>

                            <th class="px-6 py-4">
                                Submitted
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4 text-right">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($consumers as $consumer)
                            <tr class="hover:bg-slate-50/70 transition">

                                {{-- Account --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="font-semibold
                                               text-slate-900">
                                        {{ $consumer->account_number }}
                                    </div>

                                    <div class="text-xs
                                               text-slate-400 mt-1">
                                        Self Registration
                                    </div>

                                </td>


                                {{-- Consumer --}}

                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-full
                                                   bg-sky-100
                                                   text-sky-700
                                                   flex items-center
                                                   justify-center
                                                   font-bold shrink-0">

                                            {{ strtoupper(substr($consumer->first_name ?? 'C', 0, 1)) }}

                                        </div>


                                        <div>

                                            <div
                                                class="font-semibold
                                                       text-slate-900">
                                                {{ $consumer->full_name }}
                                            </div>

                                            <div
                                                class="text-xs
                                                       text-slate-500 mt-1">
                                                {{ $consumer->sex ?? '—' }}
                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Contact --}}

                                <td class="px-6 py-5">

                                    <div class="text-sm text-slate-700">
                                        {{ $consumer->email }}
                                    </div>

                                    <div class="text-xs text-slate-500 mt-1">
                                        {{ $consumer->phone }}
                                    </div>

                                </td>


                                {{-- Submitted --}}

                                <td class="px-6 py-5">

                                    <div class="text-sm text-slate-700">
                                        {{ $consumer->created_at?->format('M d, Y') }}
                                    </div>

                                    <div class="text-xs text-slate-400 mt-1">
                                        {{ $consumer->created_at?->format('h:i A') }}
                                    </div>

                                </td>


                                {{-- Status --}}

                                <td class="px-6 py-5">

                                    @if ($consumer->verification_status === 'Verified')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   rounded-full
                                                   bg-green-100
                                                   text-green-700
                                                   px-3 py-1
                                                   text-xs font-semibold">

                                            <i class="fa-solid fa-circle-check"></i>

                                            Verified

                                        </span>
                                    @elseif ($consumer->verification_status === 'Rejected')
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   rounded-full
                                                   bg-red-100
                                                   text-red-700
                                                   px-3 py-1
                                                   text-xs font-semibold">

                                            <i class="fa-solid fa-circle-xmark"></i>

                                            Rejected

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   rounded-full
                                                   bg-amber-100
                                                   text-amber-700
                                                   px-3 py-1
                                                   text-xs font-semibold">

                                            <i class="fa-solid fa-clock"></i>

                                            Pending Verification

                                        </span>
                                    @endif

                                </td>


                                {{-- Action --}}

                                <td class="px-6 py-5 text-right">

                                    <a href="{{ route('admin.consumer-verifications.show', $consumer) }}"
                                        class="inline-flex items-center gap-2
                                               rounded-xl
                                               bg-sky-50
                                               text-sky-700
                                               px-4 py-2
                                               text-sm font-semibold
                                               hover:bg-sky-100
                                               transition">

                                        <i class="fa-solid fa-eye"></i>

                                        Review

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-16 text-center">

                                    <div
                                        class="w-16 h-16 rounded-full
                                               bg-slate-100
                                               text-slate-400
                                               flex items-center
                                               justify-center
                                               mx-auto">

                                        <i
                                            class="fa-solid fa-user-check
                                                   text-2xl"></i>

                                    </div>

                                    <h3
                                        class="font-semibold
                                               text-slate-800 mt-4">
                                        No registrations found
                                    </h3>

                                    <p class="text-sm
                                               text-slate-500 mt-1">
                                        There are no consumer registrations
                                        matching the selected filters.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            @if ($consumers->hasPages())
                <div class="px-6 py-4
                           border-t border-slate-200">

                    {{ $consumers->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
