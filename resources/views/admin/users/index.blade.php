@extends('admin.layouts.app')

@section('title', 'Employee Management')

@section('content')

    <div class="max-w-7xl mx-auto space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div>

            <p class="text-sm font-semibold text-sky-600">
                Administration
            </p>

            <h1 class="text-2xl sm:text-3xl
                       font-bold text-slate-900 mt-1">
                Employee Management
            </h1>

            <p class="text-sm text-slate-500 mt-1">
                Manage employee accounts, roles and account access.
            </p>

        </div>


        {{-- ========================================================= --}}
        {{-- MINIMAL STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-3 gap-2 sm:gap-3">

            {{-- Total --}}

            <div
                class="bg-white
                        border border-slate-200
                        rounded-xl
                        px-3 py-3 sm:px-4 sm:py-4">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-xs
                                  font-medium text-slate-500">
                            Total
                        </p>

                        <p class="text-xl sm:text-2xl
                                  font-bold text-slate-900 mt-0.5">
                            {{ number_format($totalEmployees) }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-8 h-8 rounded-lg
                                bg-sky-50 text-sky-600
                                items-center justify-center">

                        <i class="fa-solid fa-users text-sm"></i>

                    </div>

                </div>

            </div>


            {{-- Active --}}

            <div
                class="bg-white
                        border border-slate-200
                        rounded-xl
                        px-3 py-3 sm:px-4 sm:py-4">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-xs
                                  font-medium text-slate-500">
                            Active
                        </p>

                        <p class="text-xl sm:text-2xl
                                  font-bold text-green-600 mt-0.5">
                            {{ number_format($activeEmployees) }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-8 h-8 rounded-lg
                                bg-green-50 text-green-600
                                items-center justify-center">

                        <i class="fa-solid fa-user-check text-sm"></i>

                    </div>

                </div>

            </div>


            {{-- Inactive --}}

            <div
                class="bg-white
                        border border-slate-200
                        rounded-xl
                        px-3 py-3 sm:px-4 sm:py-4">

                <div class="flex items-center
                            justify-between gap-2">

                    <div>

                        <p class="text-[10px] sm:text-xs
                                  font-medium text-slate-500">
                            Inactive
                        </p>

                        <p class="text-xl sm:text-2xl
                                  font-bold text-red-600 mt-0.5">
                            {{ number_format($inactiveEmployees) }}
                        </p>

                    </div>

                    <div
                        class="hidden sm:flex
                                w-8 h-8 rounded-lg
                                bg-red-50 text-red-600
                                items-center justify-center">

                        <i class="fa-solid fa-user-xmark text-sm"></i>

                    </div>

                </div>

            </div>

        </div>
        {{-- ========================================================= --}}
        {{-- SEARCH & FILTER --}}
        {{-- ========================================================= --}}

        <div class="bg-white
            rounded-2xl
            border border-slate-200
            shadow-sm p-4">

            <form method="GET" action="{{ route('admin.users.index') }}"
                class="grid grid-cols-1
               md:grid-cols-12
               gap-4">

                {{-- Search --}}
                <div class="md:col-span-5">

                    <label for="search"
                        class="block text-sm
                       font-semibold
                       text-slate-700 mb-2">

                        Search

                    </label>

                    <div class="relative">

                        <i
                            class="fa-solid fa-magnifying-glass
                          absolute left-4 top-1/2
                          -translate-y-1/2
                          text-slate-400">
                        </i>

                        <input id="search" type="search" name="search" value="{{ request('search') }}"
                            placeholder="Name, ID, department, position..."
                            class="w-full rounded-xl
                           border-slate-300
                           focus:border-sky-500
                           focus:ring-sky-500
                           pl-10 pr-3 py-2.5
                           text-sm">

                    </div>

                </div>


                {{-- Role --}}
                <div class="md:col-span-3">

                    <label for="role"
                        class="block text-sm
                       font-semibold
                       text-slate-700 mb-2">

                        Role

                    </label>

                    <select id="role" name="role"
                        class="w-full rounded-xl
                       border-slate-300
                       focus:border-sky-500
                       focus:ring-sky-500
                       px-3 py-2.5
                       text-sm">

                        <option value="">
                            All Roles
                        </option>

                        @foreach ($roles as $role)
                            <option value="{{ $role }}" @selected(request('role') === $role)>

                                {{ $role }}

                            </option>
                        @endforeach

                    </select>

                </div>


                {{-- Status --}}
                <div class="md:col-span-2">

                    <label for="status"
                        class="block text-sm
                       font-semibold
                       text-slate-700 mb-2">

                        Status

                    </label>

                    <select id="status" name="status"
                        class="w-full rounded-xl
                       border-slate-300
                       focus:border-sky-500
                       focus:ring-sky-500
                       px-3 py-2.5
                       text-sm">

                        <option value="">
                            All
                        </option>

                        <option value="1" @selected(request('status') === '1')>

                            Active

                        </option>

                        <option value="0" @selected(request('status') === '0')>

                            Inactive

                        </option>

                    </select>

                </div>


                {{-- Filter --}}
                <div class="md:col-span-1
                    flex items-end">

                    <button type="submit"
                        class="w-full
                       inline-flex items-center
                       justify-center gap-2
                       px-3 py-2.5
                       rounded-xl
                       bg-sky-700
                       text-white
                       text-sm font-semibold
                       hover:bg-sky-800
                       transition">

                        <i class="fa-solid fa-filter
                          text-xs">
                        </i>

                        <span class="md:hidden xl:inline">
                            Filter
                        </span>

                    </button>

                </div>


                {{-- Reset --}}
                <div class="md:col-span-1
                    flex items-end">

                    <a href="{{ route('admin.users.index') }}" title="Reset filters"
                        class="w-full
                       inline-flex items-center
                       justify-center
                       px-3 py-2.5
                       rounded-xl
                       border border-slate-300
                       text-slate-600
                       text-sm
                       hover:bg-slate-50
                       transition">

                        <i class="fa-solid fa-rotate-left"></i>

                    </a>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- EMPLOYEES --}}
        {{-- ========================================================= --}}

        <div class="bg-white
                    border border-slate-200
                    rounded-xl overflow-hidden">

            {{-- Table/Card Header --}}

            <div
                class="px-4 py-4
                        border-b border-slate-200
                        flex flex-col sm:flex-row
                        sm:items-center sm:justify-between
                        gap-3">

                <div>

                    <h2 class="font-semibold text-slate-900">
                        Employees
                    </h2>

                    <p class="text-xs text-slate-500 mt-0.5">

                        {{ number_format($users->total()) }}

                        {{ Str::plural('employee', $users->total()) }}

                        found

                    </p>

                </div>


                {{-- Actions beside table --}}

                <div class="flex items-center gap-2">

                    <a href="{{ route('admin.users.print', request()->query()) }}"
                        target="_blank"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-3.5 py-2 rounded-lg
                               border border-slate-300
                               text-slate-600
                               text-xs sm:text-sm
                               font-semibold
                               hover:bg-slate-50">

                        <i class="fa-solid fa-print"></i>

                        Print

                    </a>


                    <a href="{{ route('admin.users.create') }}"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-3.5 py-2 rounded-lg
                               bg-sky-700 text-white
                               text-xs sm:text-sm
                               font-semibold
                               hover:bg-sky-800">

                        <i class="fa-solid fa-user-plus"></i>

                        Add Employee

                    </a>

                </div>

            </div>


            {{-- ===================================================== --}}
            {{-- DESKTOP / LAPTOP TABLE --}}
            {{-- ===================================================== --}}

            <div class="hidden lg:block">

                <table class="w-full table-fixed">

                    <thead class="bg-slate-50">

                        <tr
                            class="text-left
                                   text-[11px]
                                   uppercase tracking-wide
                                   font-semibold text-slate-500">

                            <th class="w-[29%] px-4 py-3">
                                Employee
                            </th>

                            <th class="w-[24%] px-4 py-3">
                                Work Assignment
                            </th>

                            <th class="w-[20%] px-4 py-3">
                                Role
                            </th>

                            <th class="w-[10%] px-4 py-3">
                                Status
                            </th>

                            <th class="w-[17%] px-4 py-3 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($users as $user)

                            <tr class="hover:bg-slate-50/70
                                       transition">

                                {{-- Employee --}}

                                <td class="px-4 py-3">

                                    <div
                                        class="flex items-center
                                                gap-3 min-w-0">

                                        {{-- Avatar --}}

                                        @if ($user->avatar)
                                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                                alt="{{ $user->full_name }}"
                                                class="w-9 h-9
                                                       rounded-full
                                                       object-cover
                                                       border border-slate-200
                                                       shrink-0">
                                        @else
                                            <div
                                                class="w-9 h-9
                                                        rounded-full
                                                        bg-sky-100
                                                        text-sky-700
                                                        flex items-center
                                                        justify-center
                                                        text-xs font-bold
                                                        shrink-0">

                                                {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}

                                                {{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}

                                            </div>
                                        @endif


                                        <div class="min-w-0">

                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="block
                                                       text-sm font-semibold
                                                       text-slate-900
                                                       hover:text-sky-700
                                                       truncate">

                                                {{ $user->full_name }}

                                            </a>

                                            <div
                                                class="flex items-center
                                                        gap-2 mt-0.5
                                                        min-w-0">

                                                <span
                                                    class="text-[11px]
                                                             text-slate-500
                                                             whitespace-nowrap">

                                                    {{ $user->employee_id ?: 'No ID' }}

                                                </span>

                                                <span class="text-slate-300">
                                                    •
                                                </span>

                                                <span
                                                    class="text-[11px]
                                                             text-slate-400
                                                             truncate">

                                                    {{ $user->email }}

                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Department / Position --}}

                                <td class="px-4 py-3">

                                    <p class="text-sm
                                              font-medium
                                              text-slate-800
                                              truncate"
                                        title="{{ $user->department?->department_name ?? 'No Department' }}">

                                        {{ $user->department?->department_name ?? 'No Department' }}

                                    </p>

                                    <p class="text-[11px]
                                              text-slate-500 mt-0.5
                                              truncate"
                                        title="{{ $user->position?->position_name ?? 'No Position' }}">

                                        {{ $user->position?->position_name ?? 'No Position' }}

                                    </p>

                                </td>


                                {{-- Role --}}

                                <td class="px-4 py-3">

                                    @forelse ($user->roles as $role)
                                        <span
                                            class="inline-block
                                                     max-w-full
                                                     px-2 py-1
                                                     rounded-md
                                                     bg-blue-50
                                                     text-blue-700
                                                     text-[11px]
                                                     font-semibold
                                                     truncate">

                                            {{ $role->name }}

                                        </span>

                                    @empty

                                        <span
                                            class="text-xs
                                                     text-slate-400">
                                            No Role
                                        </span>
                                    @endforelse

                                </td>


                                {{-- Status --}}

                                <td class="px-4 py-3">

                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex
                                                     items-center gap-1.5
                                                     text-xs
                                                     font-semibold
                                                     text-green-700">

                                            <span
                                                class="w-1.5 h-1.5
                                                         rounded-full
                                                         bg-green-500">
                                            </span>

                                            Active

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                     items-center gap-1.5
                                                     text-xs
                                                     font-semibold
                                                     text-red-600">

                                            <span
                                                class="w-1.5 h-1.5
                                                         rounded-full
                                                         bg-red-500">
                                            </span>

                                            Inactive

                                        </span>
                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td class="px-4 py-3">

                                    <div
                                        class="flex items-center
                                                justify-end gap-1">

                                        {{-- View --}}

                                        <a href="{{ route('admin.users.show', $user) }}"
                                            title="View"
                                            class="w-8 h-8
                                                   rounded-lg
                                                   text-sky-700
                                                   hover:bg-sky-50
                                                   flex items-center
                                                   justify-center">

                                            <i
                                                class="fa-solid
                                                      fa-eye
                                                      text-xs">
                                            </i>

                                        </a>


                                        {{-- Edit --}}

                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            title="Edit"
                                            class="w-8 h-8
                                                   rounded-lg
                                                   text-amber-600
                                                   hover:bg-amber-50
                                                   flex items-center
                                                   justify-center">

                                            <i
                                                class="fa-solid
                                                      fa-pen
                                                      text-xs">
                                            </i>

                                        </a>


                                        {{-- Reset Password --}}

                                        <form method="POST"
                                            action="{{ route('admin.users.reset', $user) }}"
                                            onsubmit="return confirm(
                                                'Generate a new temporary password for this employee?'
                                            )">

                                            @csrf

                                            <button type="submit" title="Reset Password"
                                                class="w-8 h-8
                                                       rounded-lg
                                                       text-purple-600
                                                       hover:bg-purple-50">

                                                <i
                                                    class="fa-solid
                                                          fa-key
                                                          text-xs">
                                                </i>

                                            </button>

                                        </form>


                                        {{-- Delete --}}

                                        @if (auth()->id() !== $user->id)
                                            <form method="POST"
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                onsubmit="return confirm(
                                                    'Are you sure you want to delete this employee?'
                                                )">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Delete"
                                                    class="w-8 h-8
                                                           rounded-lg
                                                           text-red-600
                                                           hover:bg-red-50">

                                                    <i
                                                        class="fa-solid
                                                              fa-trash
                                                              text-xs">
                                                    </i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="5" class="py-14 text-center">

                                    <i
                                        class="fa-solid fa-users
                                              text-2xl text-slate-300">
                                    </i>

                                    <p
                                        class="text-sm font-semibold
                                              text-slate-600 mt-3">
                                        No employees found
                                    </p>

                                    <p class="text-xs
                                              text-slate-400 mt-1">
                                        Try changing your search or filters.
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

            <div class="lg:hidden
                        divide-y divide-slate-100">

                @forelse ($users as $user)

                    <div class="p-4">

                        {{-- Employee Header --}}

                        <div class="flex items-start gap-3">

                            @if ($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                    alt="{{ $user->full_name }}"
                                    class="w-10 h-10
                                           rounded-full
                                           object-cover
                                           border border-slate-200
                                           shrink-0">
                            @else
                                <div
                                    class="w-10 h-10
                                            rounded-full
                                            bg-sky-100
                                            text-sky-700
                                            flex items-center
                                            justify-center
                                            text-xs font-bold
                                            shrink-0">

                                    {{ strtoupper(substr($user->first_name ?? 'U', 0, 1)) }}

                                    {{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}

                                </div>
                            @endif


                            <div class="flex-1 min-w-0">

                                <div
                                    class="flex items-start
                                            justify-between gap-2">

                                    <div class="min-w-0">

                                        <a href="{{ route('admin.users.show', $user) }}"
                                            class="block
                                                   text-sm font-semibold
                                                   text-slate-900
                                                   truncate">

                                            {{ $user->full_name }}

                                        </a>

                                        <p
                                            class="text-[11px]
                                                  text-slate-500 mt-0.5">

                                            {{ $user->employee_id ?: 'No Employee ID' }}

                                        </p>

                                    </div>


                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex
                                                     items-center gap-1
                                                     text-[10px]
                                                     font-semibold
                                                     text-green-700
                                                     whitespace-nowrap">

                                            <span
                                                class="w-1.5 h-1.5
                                                         rounded-full
                                                         bg-green-500">
                                            </span>

                                            Active

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                     items-center gap-1
                                                     text-[10px]
                                                     font-semibold
                                                     text-red-600
                                                     whitespace-nowrap">

                                            <span
                                                class="w-1.5 h-1.5
                                                         rounded-full
                                                         bg-red-500">
                                            </span>

                                            Inactive

                                        </span>
                                    @endif

                                </div>

                            </div>

                        </div>


                        {{-- Employee Details --}}

                        <div class="mt-3 space-y-1.5">

                            <div class="flex items-start gap-2">

                                <i
                                    class="fa-solid fa-building
                                          w-4 mt-0.5
                                          text-xs text-slate-400">
                                </i>

                                <div class="min-w-0">

                                    <p
                                        class="text-xs
                                              font-medium
                                              text-slate-700">

                                        {{ $user->department?->department_name ?? 'No Department' }}

                                    </p>

                                    <p class="text-[11px]
                                              text-slate-400">

                                        {{ $user->position?->position_name ?? 'No Position' }}

                                    </p>

                                </div>

                            </div>


                            <div class="flex items-center gap-2">

                                <i
                                    class="fa-solid fa-user-shield
                                          w-4 text-xs
                                          text-slate-400">
                                </i>

                                <div class="flex flex-wrap gap-1">

                                    @forelse ($user->roles as $role)
                                        <span
                                            class="text-[11px]
                                                     font-medium
                                                     text-blue-700">

                                            {{ $role->name }}

                                        </span>

                                    @empty

                                        <span
                                            class="text-[11px]
                                                     text-slate-400">
                                            No Role
                                        </span>
                                    @endforelse

                                </div>

                            </div>


                            <div class="flex items-center gap-2">

                                <i
                                    class="fa-solid fa-envelope
                                          w-4 text-xs
                                          text-slate-400">
                                </i>

                                <p
                                    class="text-[11px]
                                          text-slate-500
                                          truncate">

                                    {{ $user->email }}

                                </p>

                            </div>

                        </div>


                        {{-- Mobile Actions --}}

                        <div
                            class="mt-3 pt-3
                                    border-t border-slate-100
                                    flex items-center gap-2">

                            <a href="{{ route('admin.users.show', $user) }}"
                                class="flex-1
                                       inline-flex items-center
                                       justify-center gap-1.5
                                       h-9 rounded-lg
                                       bg-sky-50
                                       text-sky-700
                                       text-xs font-semibold">

                                <i class="fa-solid fa-eye"></i>

                                View

                            </a>


                            <a href="{{ route('admin.users.edit', $user) }}"
                                title="Edit"
                                class="w-9 h-9 rounded-lg
                                       bg-amber-50
                                       text-amber-600
                                       flex items-center
                                       justify-center">

                                <i class="fa-solid fa-pen text-xs"></i>

                            </a>


                            <form method="POST"
                                action="{{ route('admin.users.reset', $user) }}"
                                onsubmit="return confirm(
                                    'Generate a new temporary password for this employee?'
                                )">

                                @csrf

                                <button type="submit" title="Reset Password"
                                    class="w-9 h-9 rounded-lg
                                           bg-purple-50
                                           text-purple-600">

                                    <i class="fa-solid fa-key text-xs"></i>

                                </button>

                            </form>


                            @if (auth()->id() !== $user->id)
                                <form method="POST"
                                    action="{{ route('admin.users.destroy', $user) }}"
                                    onsubmit="return confirm(
                                        'Are you sure you want to delete this employee?'
                                    )">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" title="Delete"
                                        class="w-9 h-9 rounded-lg
                                               bg-red-50
                                               text-red-600">

                                        <i
                                            class="fa-solid
                                                  fa-trash
                                                  text-xs">
                                        </i>

                                    </button>

                                </form>
                            @endif

                        </div>

                    </div>

                @empty

                    <div class="py-12 text-center">

                        <i class="fa-solid fa-users
                                  text-2xl text-slate-300">
                        </i>

                        <p class="text-sm font-semibold
                                  text-slate-600 mt-3">
                            No employees found
                        </p>

                    </div>

                @endforelse

            </div>


            {{-- ===================================================== --}}
            {{-- PAGINATION --}}
            {{-- ===================================================== --}}

            @if ($users->hasPages())
                <div class="px-4 py-4
                            border-t border-slate-200">

                    {{ $users->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
