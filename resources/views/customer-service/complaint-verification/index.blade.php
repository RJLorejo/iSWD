@extends('customer-service.layouts.app')

@section('title', 'Complaint Verification')

@section('content')

<div class="space-y-5 sm:space-y-6">

    {{-- ========================================================= --}}
    {{-- PAGE HEADER --}}
    {{-- ========================================================= --}}

    <x-form.page-header
        title="Complaint Verification"
        subtitle="Review and verify complaints submitted by consumers."
    />


    {{-- ========================================================= --}}
    {{-- STATISTICS --}}
    {{-- ========================================================= --}}

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">

        {{-- Pending --}}
        <x-form.card>

            <div class="p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500 truncate">
                            Pending
                        </p>

                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                            {{ $pendingCount }}
                        </p>

                        <p class="hidden sm:block text-xs text-yellow-600 mt-1">
                            Awaiting review
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11
                               rounded-xl
                               bg-yellow-100
                               text-yellow-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fas fa-clock"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- Verified --}}
        <x-form.card>

            <div class="p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500 truncate">
                            Verified
                        </p>

                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                            {{ $verifiedCount }}
                        </p>

                        <p class="hidden sm:block text-xs text-green-600 mt-1">
                            Successfully verified
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11
                               rounded-xl
                               bg-green-100
                               text-green-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fas fa-circle-check"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- Total --}}
        <x-form.card>

            <div class="p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500 truncate">
                            Total
                        </p>

                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                            {{ $totalCount }}
                        </p>

                        <p class="hidden sm:block text-xs text-blue-600 mt-1">
                            All complaints
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11
                               rounded-xl
                               bg-blue-100
                               text-blue-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fas fa-file-circle-exclamation"></i>

                    </div>

                </div>

            </div>

        </x-form.card>


        {{-- Rejected --}}
        <x-form.card>

            <div class="p-4 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div class="min-w-0">

                        <p class="text-xs sm:text-sm text-gray-500 truncate">
                            Rejected
                        </p>

                        <p class="text-2xl sm:text-3xl font-bold text-gray-900 mt-1">
                            {{ $rejectedCount }}
                        </p>

                        <p class="hidden sm:block text-xs text-red-600 mt-1">
                            Rejected complaints
                        </p>

                    </div>

                    <div
                        class="w-10 h-10 sm:w-11 sm:h-11
                               rounded-xl
                               bg-red-100
                               text-red-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fas fa-circle-xmark"></i>

                    </div>

                </div>

            </div>

        </x-form.card>

    </div>


    {{-- ========================================================= --}}
    {{-- SEARCH --}}
    {{-- ========================================================= --}}

    <x-form.card>

        <form method="GET" class="p-4 sm:p-5">

            <div class="flex flex-col sm:flex-row gap-3">

                {{-- Search --}}
                <div class="relative flex-1">

                    <i
                        class="fas fa-search
                               absolute left-4 top-1/2
                               -translate-y-1/2
                               text-gray-400">
                    </i>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search complaint, consumer, subject or address..."
                        class="w-full
                               pl-11 pr-4
                               py-2.5
                               rounded-xl
                               border-gray-300
                               text-sm
                               focus:border-blue-500
                               focus:ring-blue-500">

                </div>


                {{-- Search button --}}
                <button
                    type="submit"
                    class="w-full sm:w-auto
                           inline-flex
                           items-center
                           justify-center
                           px-5 py-2.5
                           rounded-xl
                           bg-blue-600
                           text-white
                           text-sm font-medium
                           hover:bg-blue-700
                           transition">

                    <i class="fas fa-search mr-2"></i>

                    Search

                </button>


                {{-- Clear --}}
                @if (request()->filled('search'))

                    <a
                        href="{{ route('customer-service.complaint-verification.index') }}"
                        class="w-full sm:w-auto
                               inline-flex
                               items-center
                               justify-center
                               px-5 py-2.5
                               rounded-xl
                               border border-gray-300
                               text-gray-700
                               text-sm font-medium
                               hover:bg-gray-50
                               transition">

                        <i class="fas fa-xmark mr-2"></i>

                        Clear

                    </a>

                @endif

            </div>

        </form>

    </x-form.card>


    {{-- ========================================================= --}}
    {{-- PENDING VERIFICATION --}}
    {{-- ========================================================= --}}

    <x-form.card>

        {{-- CARD HEADER --}}
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100">

            <div
                class="flex flex-col sm:flex-row
                       sm:items-center
                       sm:justify-between
                       gap-3">

                <div class="flex items-start gap-3">

                    <div
                        class="w-10 h-10
                               rounded-xl
                               bg-indigo-100
                               text-indigo-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fas fa-shield-halved"></i>

                    </div>

                    <div class="min-w-0">

                        <h3 class="font-semibold text-gray-900">

                            Complaints Awaiting Verification

                        </h3>

                        <p class="text-sm text-gray-500 mt-1">

                            Review each complaint before confirming it.

                        </p>

                    </div>

                </div>


                <span
                    class="self-start sm:self-auto
                           px-3 py-1.5
                           rounded-full
                           bg-yellow-100
                           text-yellow-700
                           text-xs sm:text-sm
                           font-medium
                           whitespace-nowrap">

                    {{ $pendingCount }} Pending

                </span>

            </div>

        </div>


        {{-- ===================================================== --}}
        {{-- DESKTOP TABLE --}}
        {{-- ===================================================== --}}

        <div class="hidden md:block overflow-x-auto">

            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200">

                    <tr>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Complaint
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Consumer
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Category
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Priority
                        </th>

                        <th class="px-6 py-4 text-left font-semibold text-gray-600">
                            Reported
                        </th>

                        <th class="px-6 py-4 text-right font-semibold text-gray-600">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-gray-100">

                    @forelse ($pendingComplaints as $complaint)

                        <tr class="hover:bg-gray-50 transition">

                            {{-- Complaint --}}
                            <td class="px-6 py-4">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-gray-500 mt-1 max-w-xs">
                                    {{ Str::limit($complaint->subject, 45) }}
                                </p>

                            </td>


                            {{-- Consumer --}}
                            <td class="px-6 py-4">

                                <p class="font-medium text-gray-900">

                                    {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                                </p>

                                <p class="text-xs text-gray-500 mt-1">

                                    {{ $complaint->consumer?->consumer_no ?? '—' }}

                                </p>

                            </td>


                            {{-- Category --}}
                            <td class="px-6 py-4">

                                <span class="text-gray-700">

                                    {{ $complaint->category?->name ?? '—' }}

                                </span>

                            </td>


                            {{-- Priority --}}
                            <td class="px-6 py-4">

                                <span
                                    class="inline-flex
                                           items-center
                                           px-2.5 py-1
                                           rounded-full
                                           text-xs
                                           font-medium

                                    @if ($complaint->priority === 'Critical')
                                        bg-red-100 text-red-700
                                    @elseif ($complaint->priority === 'High')
                                        bg-orange-100 text-orange-700
                                    @elseif ($complaint->priority === 'Medium')
                                        bg-yellow-100 text-yellow-700
                                    @else
                                        bg-green-100 text-green-700
                                    @endif">

                                    <i class="fas fa-flag mr-1.5"></i>

                                    {{ $complaint->priority }}

                                </span>

                            </td>


                            {{-- Reported --}}
                            <td class="px-6 py-4 text-gray-500">

                                {{ $complaint->created_at?->format('M d, Y') }}

                                <p class="text-xs text-gray-400 mt-1">

                                    {{ $complaint->created_at?->format('h:i A') }}

                                </p>

                            </td>


                            {{-- Action --}}
                            <td class="px-6 py-4">

                                <div class="flex justify-end">

                                    <a
                                        href="{{ route('customer-service.complaints.show', $complaint) }}"
                                        title="Review complaint"
                                        class="inline-flex
                                               items-center
                                               justify-center
                                               gap-2
                                               px-3.5 py-2
                                               rounded-lg
                                               bg-blue-600
                                               text-white
                                               text-sm
                                               font-medium
                                               hover:bg-blue-700
                                               hover:shadow-md
                                               transition">

                                        <i class="fas fa-eye"></i>

                                        <span>
                                            Review
                                        </span>

                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="px-6 py-14 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto
                                               rounded-2xl
                                               bg-green-100
                                               text-green-600
                                               flex items-center justify-center">

                                        <i class="fas fa-circle-check text-xl"></i>

                                    </div>

                                    <h4 class="font-semibold text-gray-900 mt-4">

                                        No Pending Complaints

                                    </h4>

                                    <p class="text-sm text-gray-500 mt-1">

                                        All complaints have been reviewed.

                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ===================================================== --}}
        {{-- MOBILE COMPLAINT CARDS --}}
        {{-- ===================================================== --}}

        <div class="md:hidden divide-y divide-gray-100">

            @forelse ($pendingComplaints as $complaint)

                <div class="p-4 sm:p-5">

                    {{-- Top --}}
                    <div class="flex items-start justify-between gap-3">

                        <div class="min-w-0">

                            <p class="font-bold text-gray-900 truncate">

                                {{ $complaint->complaint_no }}

                            </p>

                            <p class="text-sm text-gray-500 mt-1">

                                {{ Str::limit($complaint->subject, 60) }}

                            </p>

                        </div>


                        {{-- Priority --}}
                        <span
                            class="shrink-0
                                   px-2.5 py-1
                                   rounded-full
                                   text-xs
                                   font-medium

                            @if ($complaint->priority === 'Critical')
                                bg-red-100 text-red-700
                            @elseif ($complaint->priority === 'High')
                                bg-orange-100 text-orange-700
                            @elseif ($complaint->priority === 'Medium')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-green-100 text-green-700
                            @endif">

                            {{ $complaint->priority }}

                        </span>

                    </div>


                    {{-- Details --}}
                    <div class="mt-4 space-y-3">

                        {{-- Consumer --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-8 h-8 rounded-lg
                                       bg-blue-50
                                       text-blue-600
                                       flex items-center justify-center
                                       shrink-0">

                                <i class="fas fa-user text-xs"></i>

                            </div>

                            <div class="min-w-0">

                                <p class="text-xs text-gray-500">
                                    Consumer
                                </p>

                                <p class="text-sm font-medium text-gray-900">

                                    {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}

                                </p>

                                @if ($complaint->consumer?->consumer_no)

                                    <p class="text-xs text-gray-500">

                                        {{ $complaint->consumer->consumer_no }}

                                    </p>

                                @endif

                            </div>

                        </div>


                        {{-- Category --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-8 h-8 rounded-lg
                                       bg-purple-50
                                       text-purple-600
                                       flex items-center justify-center
                                       shrink-0">

                                <i class="fas fa-layer-group text-xs"></i>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Category
                                </p>

                                <p class="text-sm font-medium text-gray-900">

                                    {{ $complaint->category?->name ?? '—' }}

                                </p>

                            </div>

                        </div>


                        {{-- Location --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-8 h-8 rounded-lg
                                       bg-green-50
                                       text-green-600
                                       flex items-center justify-center
                                       shrink-0">

                                <i class="fas fa-location-dot text-xs"></i>

                            </div>

                            <div class="min-w-0">

                                <p class="text-xs text-gray-500">
                                    Reported Location
                                </p>

                                <p class="text-sm text-gray-900">

                                    {{ Str::limit($complaint->address, 70) }}

                                </p>

                            </div>

                        </div>


                        {{-- Date --}}
                        <div class="flex items-start gap-3">

                            <div
                                class="w-8 h-8 rounded-lg
                                       bg-gray-100
                                       text-gray-600
                                       flex items-center justify-center
                                       shrink-0">

                                <i class="fas fa-calendar text-xs"></i>

                            </div>

                            <div>

                                <p class="text-xs text-gray-500">
                                    Reported
                                </p>

                                <p class="text-sm font-medium text-gray-900">

                                    {{ $complaint->created_at?->format('M d, Y h:i A') }}

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Action --}}
                    <div class="mt-5 pt-4 border-t border-gray-100">

                        <a
                            href="{{ route('customer-service.complaints.show', $complaint) }}"
                            class="w-full
                                   inline-flex
                                   items-center
                                   justify-center
                                   gap-2
                                   px-4 py-2.5
                                   rounded-xl
                                   bg-blue-600
                                   text-white
                                   text-sm
                                   font-medium
                                   hover:bg-blue-700
                                   transition">

                            <i class="fas fa-eye"></i>

                            View & Review Complaint

                        </a>

                    </div>

                </div>

            @empty

                <div class="px-5 py-14 text-center">

                    <div
                        class="w-14 h-14 mx-auto
                               rounded-2xl
                               bg-green-100
                               text-green-600
                               flex items-center justify-center">

                        <i class="fas fa-circle-check text-xl"></i>

                    </div>

                    <h4 class="font-semibold text-gray-900 mt-4">

                        No Pending Complaints

                    </h4>

                    <p class="text-sm text-gray-500 mt-1">

                        All complaints have been reviewed.

                    </p>

                </div>

            @endforelse

        </div>


        {{-- ===================================================== --}}
        {{-- PAGINATION --}}
        {{-- ===================================================== --}}

        @if ($pendingComplaints->hasPages())

            <div class="px-4 sm:px-6 py-4 border-t border-gray-100 overflow-x-auto">

                {{ $pendingComplaints->links() }}

            </div>

        @endif

    </x-form.card>

</div>

@endsection
