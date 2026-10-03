@extends('admin.layouts.app')

@section('title', 'Department Management')

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
                    Departments
                </h1>

                <p class="text-sm text-slate-500 mt-1">
                    Manage departments and employee assignments.
                </p>

            </div>


            <a href="{{ route('admin.departments.create') }}"
                class="inline-flex items-center justify-center gap-2
                       self-start sm:self-auto
                       px-4 py-2.5 rounded-xl
                       bg-sky-700 text-white
                       text-sm font-semibold
                       hover:bg-sky-800 transition">

                <i class="fa-solid fa-plus"></i>

                Add Department

            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-3 gap-2 sm:gap-4">

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
                            {{ number_format($totalDepartments) }}
                        </p>

                    </div>

                    <div class="hidden sm:flex
                                w-10 h-10 rounded-xl
                                bg-sky-50 text-sky-600
                                items-center justify-center">

                        <i class="fa-solid fa-building"></i>

                    </div>

                </div>

                <p class="hidden sm:block
                          text-xs text-slate-400 mt-2">
                    Registered departments
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
                            {{ number_format($activeDepartments) }}
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
                    Available departments
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
                            {{ number_format($inactiveDepartments) }}
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
                    Disabled departments
                </p>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- SEARCH --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-slate-200 p-4 sm:p-5">

            <form method="GET"
                action="{{ route('admin.departments.index') }}">

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
                            placeholder="Search department, code or description..."
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   pl-11 pr-10 py-2.5
                                   text-sm">

                        @if (request('search'))

                            <a href="{{ route('admin.departments.index') }}"
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

                        <a href="{{ route('admin.departments.index') }}"
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
        {{-- RECORDS --}}
        {{-- ========================================================= --}}

        <div class="bg-white rounded-2xl
                    border border-slate-200
                    overflow-hidden">

            {{-- Records Header --}}

            <div class="px-4 sm:px-5 py-4
                        border-b border-slate-100
                        flex items-center justify-between gap-4">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Department Records
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-500 mt-1">
                        {{ number_format($departments->total()) }}
                        {{ Str::plural('department', $departments->total()) }}
                        found
                    </p>

                </div>


                @if ($departments->total() > 0)

                    <p class="hidden sm:block text-xs text-slate-400">

                        {{ $departments->firstItem() }}
                        –
                        {{ $departments->lastItem() }}
                        of
                        {{ $departments->total() }}

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
                                Department
                            </th>

                            <th class="px-5 py-3.5">
                                Description
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

                        @forelse ($departments as $department)

                            <tr class="hover:bg-slate-50/70 transition">

                                {{-- Department --}}

                                <td class="px-5 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="w-9 h-9 rounded-xl
                                                    bg-sky-50 text-sky-600
                                                    flex items-center
                                                    justify-center shrink-0">

                                            <i class="fa-solid fa-building text-sm"></i>

                                        </div>

                                        <div>

                                            <a href="{{ route('admin.departments.show', $department) }}"
                                                class="text-sm font-semibold
                                                       text-slate-900
                                                       hover:text-sky-700">

                                                {{ $department->department_name }}

                                            </a>

                                            <p class="text-xs text-slate-400 mt-0.5">
                                                {{ $department->department_code }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- Description --}}

                                <td class="px-5 py-4">

                                    <p class="text-sm text-slate-600
                                              max-w-md">

                                        {{ Str::limit(
                                            $department->description ?: 'No description',
                                            70
                                        ) }}

                                    </p>

                                </td>


                                {{-- Employees --}}

                                <td class="px-5 py-4">

                                    <div class="inline-flex items-center gap-2
                                                text-sm text-slate-700">

                                        <i class="fa-solid fa-users
                                                  text-xs text-slate-400">
                                        </i>

                                        <span class="font-semibold">
                                            {{ number_format($department->users_count) }}
                                        </span>

                                    </div>

                                </td>


                                {{-- Status --}}

                                <td class="px-5 py-4">

                                    @if ($department->is_active)

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

                                        <a href="{{ route('admin.departments.show', $department) }}"
                                            title="View Department"
                                            class="w-9 h-9 rounded-lg
                                                   text-sky-700
                                                   hover:bg-sky-50
                                                   flex items-center
                                                   justify-center transition">

                                            <i class="fa-solid fa-eye text-sm"></i>

                                        </a>


                                        <a href="{{ route('admin.departments.edit', $department) }}"
                                            title="Edit Department"
                                            class="w-9 h-9 rounded-lg
                                                   text-amber-600
                                                   hover:bg-amber-50
                                                   flex items-center
                                                   justify-center transition">

                                            <i class="fa-solid fa-pen text-sm"></i>

                                        </a>


                                        <form method="POST"
                                            action="{{ route('admin.departments.destroy', $department) }}"
                                            onsubmit="return confirm('Delete this department?')">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                title="Delete Department"
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

                                        <i class="fa-solid fa-building"></i>

                                    </div>

                                    <p class="text-sm font-semibold
                                              text-slate-700 mt-3">
                                        No departments found
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

                @forelse ($departments as $department)

                    <div class="p-4">

                        {{-- Top --}}

                        <div class="flex items-start gap-3">

                            <div class="w-10 h-10 rounded-xl
                                        bg-sky-50 text-sky-600
                                        flex items-center justify-center
                                        shrink-0">

                                <i class="fa-solid fa-building"></i>

                            </div>


                            <div class="min-w-0 flex-1">

                                <div class="flex items-start
                                            justify-between gap-3">

                                    <div class="min-w-0">

                                        <a href="{{ route('admin.departments.show', $department) }}"
                                            class="font-semibold text-slate-900
                                                   hover:text-sky-700">

                                            {{ $department->department_name }}

                                        </a>

                                        <p class="text-xs text-slate-400 mt-0.5">
                                            {{ $department->department_code }}
                                        </p>

                                    </div>


                                    @if ($department->is_active)

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

                        @if ($department->description)

                            <p class="text-sm text-slate-500
                                      mt-3 leading-relaxed">

                                {{ Str::limit($department->description, 100) }}

                            </p>

                        @endif


                        {{-- Info --}}

                        <div class="mt-4 flex items-center gap-2
                                    text-xs text-slate-500">

                            <i class="fa-solid fa-users text-slate-400"></i>

                            <span>
                                {{ $department->users_count }}
                                {{ Str::plural('employee', $department->users_count) }}
                            </span>

                        </div>


                        {{-- Actions --}}

                        <div class="mt-4 pt-3
                                    border-t border-slate-100
                                    flex items-center gap-2">

                            <a href="{{ route('admin.departments.show', $department) }}"
                                class="flex-1 inline-flex items-center
                                       justify-center gap-2
                                       px-3 py-2 rounded-lg
                                       bg-sky-50 text-sky-700
                                       text-xs font-semibold">

                                <i class="fa-solid fa-eye"></i>

                                View

                            </a>


                            <a href="{{ route('admin.departments.edit', $department) }}"
                                class="w-9 h-9 rounded-lg
                                       bg-amber-50 text-amber-600
                                       flex items-center justify-center">

                                <i class="fa-solid fa-pen text-sm"></i>

                            </a>


                            <form method="POST"
                                action="{{ route('admin.departments.destroy', $department) }}"
                                onsubmit="return confirm('Delete this department?')">

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

                        <i class="fa-solid fa-building
                                  text-2xl text-slate-300">
                        </i>

                        <p class="text-sm font-semibold
                                  text-slate-700 mt-3">
                            No departments found
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- Pagination --}}

            @if ($departments->hasPages())

                <div class="px-4 sm:px-5 py-4
                            border-t border-slate-100">

                    {{ $departments->links() }}

                </div>

            @endif

        </div>

    </div>

@endsection
