@extends('admin.layouts.app')

@section('title', 'Position Management')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col sm:flex-row
                    sm:items-center sm:justify-between gap-4">

            <div>

                <p class="text-sm font-semibold text-sky-600">
                    Organization
                </p>

                <h1 class="text-2xl sm:text-3xl
                           font-bold text-slate-900 mt-1">
                    Positions
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Manage employee positions and department assignments.
                </p>

            </div>


            <a href="{{ route('admin.positions.create') }}"
                class="inline-flex items-center justify-center gap-2
                       self-start sm:self-auto
                       px-4 py-2.5 rounded-xl
                       bg-sky-700 text-white
                       text-sm font-semibold
                       hover:bg-sky-800 transition">

                <i class="fa-solid fa-plus"></i>

                Add Position

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4">

            {{-- Total --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-slate-200
                        p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Total
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-slate-900 mt-1">
                            {{ number_format($totalPositions) }}
                        </p>

                    </div>

                    <div class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-sky-50 text-sky-600
                                items-center justify-center">

                        <i class="fa-solid fa-briefcase"></i>

                    </div>

                </div>

                <p class="hidden sm:block
                          text-xs text-slate-400 mt-2">
                    Registered positions
                </p>

            </div>


            {{-- Active --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-slate-200
                        p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Active
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-green-600 mt-1">
                            {{ number_format($activePositions) }}
                        </p>

                    </div>

                    <div class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-green-50 text-green-600
                                items-center justify-center">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                </div>

                <p class="hidden sm:block
                          text-xs text-slate-400 mt-2">
                    Currently active
                </p>

            </div>


            {{-- Inactive --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-slate-200
                        p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Inactive
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-red-600 mt-1">
                            {{ number_format($inactivePositions) }}
                        </p>

                    </div>

                    <div class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-red-50 text-red-600
                                items-center justify-center">

                        <i class="fa-solid fa-circle-xmark"></i>

                    </div>

                </div>

                <p class="hidden sm:block
                          text-xs text-slate-400 mt-2">
                    Disabled positions
                </p>

            </div>


            {{-- Assigned Employees --}}

            <div class="bg-white rounded-xl sm:rounded-2xl
                        border border-slate-200
                        p-3 sm:p-5">

                <div class="flex items-center justify-between gap-3">

                    <div>

                        <p class="text-[10px] sm:text-sm text-slate-500">
                            Assigned
                        </p>

                        <p class="text-xl sm:text-3xl
                                  font-bold text-indigo-600 mt-1">
                            {{ number_format($assignedEmployees) }}
                        </p>

                    </div>

                    <div class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-indigo-50 text-indigo-600
                                items-center justify-center">

                        <i class="fa-solid fa-users"></i>

                    </div>

                </div>

                <p class="hidden sm:block
                          text-xs text-slate-400 mt-2">
                    Employees with positions
                </p>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SEARCH --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-slate-200 p-4 sm:p-5">

            <form method="GET"
                action="{{ route('admin.positions.index') }}">

                <div class="flex flex-col sm:flex-row gap-3">

                    <div class="relative flex-1">

                        <i class="fa-solid fa-magnifying-glass
                                  absolute left-4 top-1/2
                                  -translate-y-1/2
                                  text-slate-400 text-sm">
                        </i>

                        <input type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search position, code, department or description..."
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   pl-11 pr-10 py-2.5
                                   text-sm">

                        @if (request('search'))

                            <a href="{{ route('admin.positions.index') }}"
                                class="absolute right-4 top-1/2
                                       -translate-y-1/2
                                       text-slate-400
                                       hover:text-red-500">

                                <i class="fa-solid fa-xmark"></i>

                            </a>

                        @endif

                    </div>


                    <button type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-5 py-2.5 rounded-xl
                               bg-sky-700 text-white
                               text-sm font-semibold
                               hover:bg-sky-800 transition">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        Search

                    </button>


                    @if (request('search'))

                        <a href="{{ route('admin.positions.index') }}"
                            class="inline-flex items-center
                                   justify-center
                                   px-5 py-2.5 rounded-xl
                                   border border-slate-300
                                   text-slate-600 text-sm
                                   font-semibold
                                   hover:bg-slate-50 transition">

                            Reset

                        </a>

                    @endif

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- POSITION RECORDS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-slate-200
                    overflow-hidden">

            {{-- Header --}}

            <div class="px-4 sm:px-5 py-4
                        border-b border-slate-100
                        flex items-center justify-between gap-4">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Position Records
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ number_format($positions->total()) }}
                        {{ Str::plural('position', $positions->total()) }}
                        found
                    </p>

                </div>


                @if ($positions->total() > 0)

                    <p class="hidden sm:block text-xs text-slate-400">

                        {{ $positions->firstItem() }}
                        –
                        {{ $positions->lastItem() }}
                        of
                        {{ $positions->total() }}

                    </p>

                @endif

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP TABLE --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50">

                        <tr class="text-left text-xs
                                   uppercase tracking-wider
                                   font-semibold text-slate-500">

                            <th class="px-5 py-3.5">
                                Position
                            </th>

                            <th class="px-5 py-3.5">
                                Department
                            </th>

                            <th class="px-5 py-3.5">
                                Employees
                            </th>

                            <th class="px-5 py-3.5">
                                Status
                            </th>

                            <th class="px-5 py-3.5 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($positions as $position)

                            <tr class="hover:bg-slate-50/70 transition">

                                {{-- Position --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 rounded-xl
                                                    bg-sky-50 text-sky-600
                                                    flex items-center
                                                    justify-center shrink-0">

                                            <i class="fa-solid fa-briefcase text-sm"></i>

                                        </div>


                                        <div class="min-w-0">

                                            <a href="{{ route('admin.positions.show', $position) }}"
                                                class="text-sm font-semibold
                                                       text-slate-900
                                                       hover:text-sky-700">

                                                {{ $position->position_name }}

                                            </a>

                                            <div class="flex items-center
                                                        gap-2 mt-0.5">

                                                <span class="text-xs text-slate-400">
                                                    {{ $position->position_code }}
                                                </span>

                                                @if ($position->description)

                                                    <span class="text-slate-300">
                                                        •
                                                    </span>

                                                    <span class="text-xs
                                                                 text-slate-400
                                                                 max-w-[220px]
                                                                 truncate">

                                                        {{ $position->description }}

                                                    </span>

                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Department --}}

                                <td class="px-5 py-4">

                                    <div class="inline-flex items-center gap-2">

                                        <i class="fa-solid fa-building
                                                  text-xs text-slate-400">
                                        </i>

                                        <span class="text-sm text-slate-700">

                                            {{ $position->department?->department_name
                                                ?? 'No Department' }}

                                        </span>

                                    </div>

                                </td>


                                {{-- Employees --}}

                                <td class="px-5 py-4">

                                    <div class="inline-flex items-center gap-2
                                                text-sm text-slate-700">

                                        <i class="fa-solid fa-users
                                                  text-xs text-slate-400">
                                        </i>

                                        <span class="font-semibold">
                                            {{ number_format($position->users_count) }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Status --}}

                                <td class="px-5 py-4">

                                    @if ($position->is_active)

                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     bg-green-50 text-green-700
                                                     border border-green-100
                                                     text-xs font-semibold">

                                            <span class="w-1.5 h-1.5
                                                         rounded-full bg-green-500">
                                            </span>

                                            Active

                                        </span>

                                    @else

                                        <span class="inline-flex items-center gap-1.5
                                                     px-2.5 py-1 rounded-full
                                                     bg-red-50 text-red-700
                                                     border border-red-100
                                                     text-xs font-semibold">

                                            <span class="w-1.5 h-1.5
                                                         rounded-full bg-red-500">
                                            </span>

                                            Inactive

                                        </span>

                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center
                                                justify-end gap-1.5">

                                        <a href="{{ route('admin.positions.show', $position) }}"
                                            title="View Position"
                                            class="w-9 h-9 rounded-lg
                                                   text-sky-700
                                                   hover:bg-sky-50
                                                   flex items-center
                                                   justify-center transition">

                                            <i class="fa-solid fa-eye text-sm"></i>

                                        </a>


                                        <a href="{{ route('admin.positions.edit', $position) }}"
                                            title="Edit Position"
                                            class="w-9 h-9 rounded-lg
                                                   text-amber-600
                                                   hover:bg-amber-50
                                                   flex items-center
                                                   justify-center transition">

                                            <i class="fa-solid fa-pen text-sm"></i>

                                        </a>


                                        <form method="POST"
                                            action="{{ route('admin.positions.destroy', $position) }}"
                                            onsubmit="return confirm('Delete this position?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                title="Delete Position"
                                                class="w-9 h-9 rounded-lg
                                                       text-red-600
                                                       hover:bg-red-50
                                                       flex items-center
                                                       justify-center transition">

                                                <i class="fa-solid fa-trash text-sm"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5"
                                    class="px-6 py-16 text-center">

                                    <div class="w-11 h-11 mx-auto
                                                rounded-xl bg-slate-100
                                                text-slate-400
                                                flex items-center justify-center">

                                        <i class="fa-solid fa-briefcase"></i>

                                    </div>

                                    <p class="text-sm font-semibold
                                              text-slate-700 mt-3">
                                        No positions found
                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">
                                        Try changing your search.
                                    </p>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ===================================================== --}}
            {{-- MOBILE / TABLET CARDS --}}
            {{-- ===================================================== --}}

            <div class="lg:hidden divide-y divide-slate-100">

                @forelse ($positions as $position)

                    <div class="p-4">

                        {{-- Header --}}

                        <div class="flex items-start gap-3">

                            <div class="w-10 h-10 rounded-xl
                                        bg-sky-50 text-sky-600
                                        flex items-center justify-center
                                        shrink-0">

                                <i class="fa-solid fa-briefcase"></i>

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="flex items-start
                                            justify-between gap-3">

                                    <div class="min-w-0">

                                        <a href="{{ route('admin.positions.show', $position) }}"
                                            class="font-semibold text-slate-900
                                                   hover:text-sky-700">

                                            {{ $position->position_name }}

                                        </a>

                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $position->position_code }}
                                        </p>

                                    </div>


                                    @if ($position->is_active)

                                        <span class="px-2 py-1 rounded-full
                                                     bg-green-50 text-green-700
                                                     text-[10px] font-bold
                                                     whitespace-nowrap">
                                            Active
                                        </span>

                                    @else

                                        <span class="px-2 py-1 rounded-full
                                                     bg-red-50 text-red-700
                                                     text-[10px] font-bold
                                                     whitespace-nowrap">
                                            Inactive
                                        </span>

                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- Description --}}

                        @if ($position->description)

                            <p class="text-sm text-slate-500
                                      mt-3 leading-relaxed">

                                {{ Str::limit($position->description, 100) }}

                            </p>

                        @endif


                        {{-- Details --}}

                        <div class="mt-4 grid grid-cols-2 gap-3">

                            <div>

                                <p class="text-[10px] uppercase
                                          tracking-wide font-semibold
                                          text-slate-400">
                                    Department
                                </p>

                                <p class="text-xs font-medium
                                          text-slate-700 mt-1">

                                    {{ $position->department?->department_name
                                        ?? 'No Department' }}

                                </p>

                            </div>


                            <div>

                                <p class="text-[10px] uppercase
                                          tracking-wide font-semibold
                                          text-slate-400">
                                    Employees
                                </p>

                                <p class="text-xs font-medium
                                          text-slate-700 mt-1">

                                    {{ $position->users_count }}
                                    {{ Str::plural('employee', $position->users_count) }}

                                </p>

                            </div>

                        </div>


                        {{-- Actions --}}

                        <div class="mt-4 pt-3
                                    border-t border-slate-100
                                    flex items-center gap-2">

                            <a href="{{ route('admin.positions.show', $position) }}"
                                class="flex-1 inline-flex items-center
                                       justify-center gap-2
                                       px-3 py-2 rounded-lg
                                       bg-sky-50 text-sky-700
                                       text-xs font-semibold">

                                <i class="fa-solid fa-eye"></i>

                                View

                            </a>


                            <a href="{{ route('admin.positions.edit', $position) }}"
                                class="w-9 h-9 rounded-lg
                                       bg-amber-50 text-amber-600
                                       flex items-center justify-center">

                                <i class="fa-solid fa-pen text-sm"></i>

                            </a>


                            <form method="POST"
                                action="{{ route('admin.positions.destroy', $position) }}"
                                onsubmit="return confirm('Delete this position?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit"
                                    class="w-9 h-9 rounded-lg
                                           bg-red-50 text-red-600
                                           flex items-center justify-center">

                                    <i class="fa-solid fa-trash text-sm"></i>

                                </button>

                            </form>

                        </div>

                    </div>

                @empty

                    <div class="px-5 py-12 text-center">

                        <i class="fa-solid fa-briefcase
                                  text-2xl text-slate-300">
                        </i>

                        <p class="text-sm font-semibold
                                  text-slate-700 mt-3">
                            No positions found
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- Pagination --}}

            @if ($positions->hasPages())

                <div class="px-4 sm:px-5 py-4
                            border-t border-slate-100">

                    {{ $positions->links() }}

                </div>

            @endif

        </div>

    </div>

@endsection
