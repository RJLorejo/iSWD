@extends('maintenance-manager.layouts.app')

@section('title', 'Complaint Assignment')

@section('content')

    <div class="space-y-6">

        <x-form.page-header title="Complaint Assignment"
            subtitle="Review verified complaints and assign them to Maintenance Technicians." />


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Verified --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Awaiting Assignment
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $verifiedCount }}
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                                bg-indigo-100 text-indigo-600
                                flex items-center justify-center">

                            <i class="fas fa-clipboard-check"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Assigned --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Assigned
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $assignedCount }}
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                                bg-blue-100 text-blue-600
                                flex items-center justify-center">

                            <i class="fas fa-user-check"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- In Progress --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                In Progress
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $inProgressCount }}
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                                bg-orange-100 text-orange-600
                                flex items-center justify-center">

                            <i class="fas fa-screwdriver-wrench"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>


            {{-- Critical --}}
            <x-form.card>

                <div class="p-5">

                    <div class="flex items-center justify-between gap-4">

                        <div class="min-w-0">

                            <p class="text-sm text-gray-500">
                                Critical
                            </p>

                            <p class="text-3xl font-bold text-gray-900 mt-1">
                                {{ $criticalCount }}
                            </p>

                        </div>

                        <div
                            class="w-11 h-11 shrink-0 rounded-xl
                                bg-red-100 text-red-600
                                flex items-center justify-center">

                            <i class="fas fa-triangle-exclamation"></i>

                        </div>

                    </div>

                </div>

            </x-form.card>

        </div>


        {{-- ========================================================= --}}
        {{-- SEARCH --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <form method="GET" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-4 gap-3">

                    {{-- SEARCH --}}
                    <div class="relative md:col-span-2">

                        <i
                            class="fas fa-search absolute
                left-4 top-1/2
                -translate-y-1/2
                text-gray-400">
                        </i>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search complaint number, consumer, subject..."
                            class="w-full pl-11 rounded-xl
                       border-gray-300
                       focus:border-indigo-500
                       focus:ring-indigo-500">

                    </div>


                    {{-- STATUS --}}
                    <select name="status"
                        class="rounded-xl border-gray-300
                   focus:border-indigo-500
                   focus:ring-indigo-500">

                        <option value="">All Active Complaints</option>

                        <option value="Verified" @selected(request('status') === 'Verified')>
                            Verified
                        </option>

                        <option value="Assigned" @selected(request('status') === 'Assigned')>
                            Assigned
                        </option>

                        <option value="In Progress" @selected(request('status') === 'In Progress')>
                            In Progress
                        </option>

                    </select>




                    <div class="flex flex-wrap gap-3">

                        <button type="submit"
                            class="inline-flex items-center gap-2
                   px-5 py-2.5
                   rounded-xl
                   bg-indigo-600
                   text-white
                   hover:bg-indigo-700
                   transition">

                            <i class="fas fa-search"></i>

                            Search

                        </button>


                        @if (request()->filled('search') || request()->filled('status'))
                            <a href="{{ route('maintenance-manager.complaints.index') }}"
                                class="inline-flex items-center gap-2
                       px-5 py-2.5
                       rounded-xl
                       border border-gray-300
                       text-gray-700
                       hover:bg-gray-50">

                                <i class="fas fa-xmark"></i>

                                Clear

                            </a>
                        @endif

                    </div>

                </div>
            </form>


        </x-form.card>


        {{-- ========================================================= --}}
        {{-- VERIFIED COMPLAINTS --}}
        {{-- ========================================================= --}}

        <x-form.card>

            <div class="px-4 sm:px-6 py-5 border-b border-gray-100">

                <div class="flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3">

                    <div>

                        <h3 class="font-semibold text-gray-900">

                            <i class="fas fa-clipboard-check
                                  text-indigo-600 mr-2">
                            </i>

                            Verified Complaints

                        </h3>

                        <p class="text-sm text-gray-500 mt-1">

                            Complaints that are ready for technician assignment.

                        </p>

                    </div>


                    <span
                        class="self-start sm:self-auto
                             px-3 py-1.5 rounded-full
                             bg-indigo-100 text-indigo-700
                             text-sm font-medium">

                        {{ $verifiedCount }} Waiting

                    </span>

                </div>

            </div>


            {{-- Desktop/tablet table --}}
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
                                Verified
                            </th>

                            <th class="px-6 py-4 text-right font-semibold text-gray-600">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse($complaints as $complaint)
                            <tr class="hover:bg-gray-50 transition">

                                <td class="px-6 py-4">

                                    <p class="font-semibold text-gray-900">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                    <p class="text-gray-500 mt-1">
                                        {{ Str::limit($complaint->subject, 45) }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    <p class="font-medium text-gray-900">
                                        {{ $complaint->consumer?->full_name ?? 'Walk-in Consumer' }}
                                    </p>

                                    <p class="text-xs text-gray-500 mt-1">
                                        {{ $complaint->consumer?->consumer_no ?? '—' }}
                                    </p>

                                </td>


                                <td class="px-6 py-4 text-gray-600">

                                    {{ $complaint->category?->name ?? '—' }}

                                </td>


                                <td class="px-6 py-4">

                                    <span
                                        class="px-2.5 py-1 rounded-full
                                    text-xs font-medium
                                    @if ($complaint->priority === 'Critical') bg-red-100 text-red-700
                                    @elseif($complaint->priority === 'High')
                                        bg-orange-100 text-orange-700
                                    @elseif($complaint->priority === 'Medium')
                                        bg-yellow-100 text-yellow-700
                                    @else
                                        bg-green-100 text-green-700 @endif">

                                        {{ $complaint->priority }}

                                    </span>

                                </td>


                                <td class="px-6 py-4 text-gray-500">

                                    {{ $complaint->verified_at?->format('M d, Y h:i A') ?? '—' }}

                                </td>

                                <td class="px-4 sm:px-6 py-4 text-right">
                                    <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                                        class="inline-flex items-center justify-center
           gap-2
           px-3 py-2
           rounded-lg
           bg-indigo-600
           text-white
           hover:bg-indigo-700
           transition"
                                        title="View complaint">

                                        <i class="fas fa-eye"></i>

                                        <span class="hidden sm:inline">
                                            View
                                        </span>

                                    </a>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-12 text-center">

                                    <div
                                        class="w-14 h-14 mx-auto rounded-2xl
                                            bg-green-100 text-green-600
                                            flex items-center justify-center">

                                        <i class="fas fa-circle-check text-xl"></i>

                                    </div>

                                    <h4 class="font-semibold text-gray-900 mt-4">
                                        No Complaints Awaiting Assignment
                                    </h4>

                                    <p class="text-sm text-gray-500 mt-1">
                                        All verified complaints have been assigned.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Mobile cards --}}
            <div class="md:hidden divide-y divide-gray-100">

                @forelse($complaints as $complaint)
                    <div class="p-4 space-y-4">

                        <div class="flex items-start justify-between gap-3">

                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900">
                                    {{ $complaint->complaint_no }}
                                </p>

                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::limit($complaint->subject, 70) }}
                                </p>

                            </div>

                            <span
                                class="shrink-0 px-2.5 py-1 rounded-full
                            text-xs font-medium
                            @if ($complaint->priority === 'Critical') bg-red-100 text-red-700
                            @elseif($complaint->priority === 'High')
                                bg-orange-100 text-orange-700
                            @elseif($complaint->priority === 'Medium')
                                bg-yellow-100 text-yellow-700
                            @else
                                bg-green-100 text-green-700 @endif">

                                {{ $complaint->priority }}

                            </span>

                        </div>


                        <div class="grid grid-cols-2 gap-4 text-sm">

                            <div>

                                <p class="text-xs text-gray-400 uppercase">
                                    Consumer
                                </p>

                                <p class="font-medium text-gray-800 mt-1">
                                    {{ $complaint->consumer?->full_name ?? 'Walk-in' }}
                                </p>

                            </div>


                            <div>

                                <p class="text-xs text-gray-400 uppercase">
                                    Category
                                </p>

                                <p class="font-medium text-gray-800 mt-1">
                                    {{ $complaint->category?->name ?? '—' }}
                                </p>

                            </div>


                            <div class="col-span-2">

                                <p class="text-xs text-gray-400 uppercase">
                                    Verified
                                </p>

                                <p class="text-gray-600 mt-1">
                                    {{ $complaint->verified_at?->format('M d, Y h:i A') ?? '—' }}
                                </p>

                            </div>

                        </div>


                        <a href="{{ route('maintenance-manager.complaints.show', $complaint) }}"
                            class="w-full inline-flex items-center
                               justify-center gap-2
                               px-4 py-2.5 rounded-xl
                               bg-blue-600 text-white
                               hover:bg-blue-700 transition">

                            <i class="fas fa-eye"></i>

                            Review & Assign

                        </a>

                    </div>

                @empty

                    <div class="p-10 text-center">

                        <i class="fas fa-circle-check text-3xl text-green-500"></i>

                        <p class="font-semibold text-gray-900 mt-4">
                            No Complaints Awaiting Assignment
                        </p>

                    </div>
                @endforelse

            </div>


            @if ($complaints->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100">

                    {{ $complaints->links() }}

                </div>
            @endif

        </x-form.card>

    </div>

@endsection
