@extends('admin.layouts.app')

@section('title', 'Employee Management')

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

                <p class="text-sm font-semibold text-sky-600">
                    Administration
                </p>

                <h1 class="text-2xl sm:text-3xl
                           font-bold text-slate-900 mt-1">
                    Employee Management
                </h1>

                <p class="text-sm text-slate-500 mt-2 max-w-2xl">
                    Manage employee accounts, roles, departments,
                    positions, and account access.
                </p>

            </div>


<div class="flex flex-col sm:flex-row gap-3">

    <a
        href="{{ route('admin.users.print', request()->query()) }}"
        target="_blank"
        class="inline-flex items-center
               justify-center gap-2
               px-5 py-3 rounded-xl
               border border-slate-300
               bg-white
               text-slate-700
               font-semibold
               shadow-sm
               hover:bg-slate-50
               transition"
    >

        <i class="fa-solid fa-print"></i>

        Print Report

    </a>


    <a
        href="{{ route('admin.users.create') }}"
        class="inline-flex items-center
               justify-center gap-2
               px-5 py-3 rounded-xl
               bg-gradient-to-r
               from-sky-700
               via-blue-700
               to-cyan-600
               text-white font-semibold
               shadow-sm
               hover:scale-[1.02]
               transition"
    >

        <i class="fa-solid fa-user-plus"></i>

        Add Employee

    </a>

</div>

        </div>


        {{-- ========================================================= --}}
        {{-- PRIMARY STATISTICS --}}
        {{-- ========================================================= --}}

        <div
            class="grid grid-cols-1
                   sm:grid-cols-2
                   xl:grid-cols-3
                   gap-4">


            {{-- Total --}}

            <div
                class="bg-white rounded-2xl
                       border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Total Employees
                        </p>

                        <p class="text-3xl font-bold
                                   text-slate-900 mt-2">
                            {{ number_format($totalEmployees) }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Registered employee accounts
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-sky-100
                               text-sky-700
                               flex items-center
                               justify-center shrink-0">

                        <i class="fa-solid fa-users text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Active --}}

            <div
                class="bg-white rounded-2xl
                       border border-green-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Active Employees
                        </p>

                        <p class="text-3xl font-bold
                                   text-green-600 mt-2">
                            {{ number_format($activeEmployees) }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Currently active accounts
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-green-100
                               text-green-600
                               flex items-center
                               justify-center shrink-0">

                        <i class="fa-solid fa-user-check text-xl"></i>

                    </div>

                </div>

            </div>


            {{-- Inactive --}}

            <div
                class="bg-white rounded-2xl
                       border border-red-200
                       shadow-sm p-5">

                <div class="flex items-center justify-between gap-4">

                    <div>

                        <p class="text-sm font-medium text-slate-500">
                            Inactive Employees
                        </p>

                        <p class="text-3xl font-bold
                                   text-red-600 mt-2">
                            {{ number_format($inactiveEmployees) }}
                        </p>

                        <p class="text-xs text-slate-400 mt-1">
                            Disabled employee accounts
                        </p>

                    </div>

                    <div
                        class="w-12 h-12 rounded-2xl
                               bg-red-100
                               text-red-600
                               flex items-center
                               justify-center shrink-0">

                        <i class="fa-solid fa-user-xmark text-xl"></i>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================= --}}
        {{-- ROLE STATISTICS --}}
        {{-- ========================================================= --}}

        <div class="grid grid-cols-1
                   md:grid-cols-3 gap-4">


            <div
                class="bg-white rounded-2xl
                       border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-center gap-4">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-indigo-100
                               text-indigo-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fa-solid fa-headset"></i>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Customer Service
                        </p>

                        <p class="text-xl font-bold
                                   text-slate-900 mt-1">
                            {{ number_format($customer_services) }}
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="bg-white rounded-2xl
                       border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-center gap-4">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-purple-100
                               text-purple-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fa-solid fa-user-gear"></i>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Maintenance Managers
                        </p>

                        <p class="text-xl font-bold
                                   text-slate-900 mt-1">
                            {{ number_format($maintenance_managers) }}
                        </p>

                    </div>

                </div>

            </div>


            <div
                class="bg-white rounded-2xl
                       border border-slate-200
                       shadow-sm p-5">

                <div class="flex items-center gap-4">

                    <div
                        class="w-11 h-11 rounded-xl
                               bg-orange-100
                               text-orange-600
                               flex items-center justify-center
                               shrink-0">

                        <i class="fa-solid fa-screwdriver-wrench"></i>

                    </div>

                    <div>

                        <p class="text-xs text-slate-500">
                            Maintenance Technicians
                        </p>

                        <p class="text-xl font-bold
                                   text-slate-900 mt-1">
                            {{ number_format($maintenance_technicians) }}
                        </p>

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

            <div class="flex items-center
                       justify-between gap-4
                       mb-5">

                <div>

                    <h2 class="font-bold text-slate-900">
                        Search & Filter
                    </h2>

                    <p class="text-xs text-slate-500 mt-1">
                        Find employee accounts using the filters below.
                    </p>

                </div>

                @if (request()->filled('search') ||
                        request()->filled('department') ||
                        request()->filled('position') ||
                        request()->filled('role') ||
                        request()->filled('status'))
                    <a href="{{ route('admin.users.index') }}"
                        class="text-sm font-semibold
                               text-sky-700
                               hover:text-sky-800">
                        Clear Filters
                    </a>
                @endif

            </div>


            <form method="GET" action="{{ route('admin.users.index') }}">

                <div
                    class="grid sm:grid-cols-2
                           lg:grid-cols-3
                           xl:grid-cols-6
                           gap-4">


                    {{-- Search --}}

                    <div class="sm:col-span-2 xl:col-span-2">

                        <label for="search"
                            class="block text-xs
                                   font-semibold
                                   text-slate-600 mb-2">
                            Search Employee
                        </label>

                        <div class="relative">

                            <i
                                class="fa-solid fa-magnifying-glass
                                       absolute left-4 top-1/2
                                       -translate-y-1/2
                                       text-slate-400"></i>

                            <input id="search" type="text" name="search" value="{{ request('search') }}"
                                placeholder="Name, ID, email or phone..."
                                class="w-full rounded-xl
                                       border-slate-300
                                       focus:border-sky-500
                                       focus:ring-sky-500
                                       pl-11 pr-4 py-3">

                        </div>

                    </div>


                    {{-- Department --}}

                    <div>

                        <label for="department"
                            class="block text-xs
                                   font-semibold
                                   text-slate-600 mb-2">
                            Department
                        </label>

                        <select id="department" name="department"
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-3 py-3">

                            <option value="">
                                All Departments
                            </option>

                            @foreach ($departments as $id => $department)
                                <option value="{{ $id }}" @selected(request('department') == $id)>
                                    {{ $department }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Position --}}

                    <div>

                        <label for="position"
                            class="block text-xs
                                   font-semibold
                                   text-slate-600 mb-2">
                            Position
                        </label>

                        <select id="position" name="position"
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-3 py-3">

                            <option value="">
                                All Positions
                            </option>

                            @foreach ($positions as $id => $position)
                                <option value="{{ $id }}" @selected(request('position') == $id)>
                                    {{ $position }}
                                </option>
                            @endforeach

                        </select>

                    </div>


                    {{-- Role --}}

                    <div>

                        <label for="role"
                            class="block text-xs
                                   font-semibold
                                   text-slate-600 mb-2">
                            Role
                        </label>

                        <select id="role" name="role"
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-3 py-3">

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

                    <div>

                        <label for="status"
                            class="block text-xs
                                   font-semibold
                                   text-slate-600 mb-2">
                            Status
                        </label>

                        <select id="status" name="status"
                            class="w-full rounded-xl
                                   border-slate-300
                                   focus:border-sky-500
                                   focus:ring-sky-500
                                   px-3 py-3">

                            <option value="">
                                All Statuses
                            </option>

                            <option value="1" @selected(request('status') === '1')>
                                Active
                            </option>

                            <option value="0" @selected(request('status') === '0')>
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>


                <div class="mt-4 flex
                           justify-end gap-3">

                    <a href="{{ route('admin.users.index') }}"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-5 py-3 rounded-xl
                               border border-slate-300
                               text-slate-700
                               font-semibold
                               hover:bg-slate-50
                               transition">

                        <i class="fa-solid fa-rotate-left"></i>

                        Reset

                    </a>


                    <button type="submit"
                        class="inline-flex items-center
                               justify-center gap-2
                               px-6 py-3 rounded-xl
                               bg-sky-700
                               text-white font-semibold
                               hover:bg-sky-800
                               transition">

                        <i class="fa-solid fa-filter"></i>

                        Apply Filters

                    </button>

                </div>

            </form>

        </div>


        {{-- ========================================================= --}}
        {{-- EMPLOYEE TABLE --}}
        {{-- ========================================================= --}}

        <div
            class="bg-white rounded-2xl
                   border border-slate-200
                   shadow-sm overflow-hidden">

            {{-- Table Header --}}

            <div
                class="px-6 py-5
                       border-b border-slate-200
                       flex flex-col sm:flex-row
                       sm:items-center
                       sm:justify-between
                       gap-3">

                <div>

                    <h2 class="font-bold text-slate-900">
                        Employees
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        {{ number_format($users->total()) }}
                        {{ Str::plural('employee', $users->total()) }}
                        found
                    </p>

                </div>


                @if ($users->total() > 0)
                    <div class="text-xs text-slate-400">

                        Showing

                        {{ $users->firstItem() }}

                        –

                        {{ $users->lastItem() }}

                        of

                        {{ $users->total() }}

                    </div>
                @endif

            </div>


            <div class="overflow-x-auto">

                <table class="w-full min-w-[1100px]">

                    <thead class="bg-slate-50">

                        <tr
                            class="text-left text-xs
                                   uppercase tracking-wider
                                   font-semibold text-slate-500">

                            <th class="px-6 py-4">
                                Employee
                            </th>

                            <th class="px-6 py-4">
                                Department / Position
                            </th>

                            <th class="px-6 py-4">
                                Role
                            </th>

                            <th class="px-6 py-4">
                                Status
                            </th>

                            <th class="px-6 py-4">
                                Last Login
                            </th>

                            <th class="px-6 py-4 text-right">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse ($users as $user)

                            <tr class="hover:bg-slate-50/70
                                       transition">


                                {{-- Employee --}}

                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-4">

                                        <div class="relative shrink-0">

                                            @if ($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}"
                                                    alt="{{ $user->full_name }}"
                                                    class="w-12 h-12
                                                           rounded-full
                                                           object-cover
                                                           border border-slate-200">
                                            @else
                                                <img src="https://ui-avatars.com/api/?background=0ea5e9&color=ffffff&name={{ urlencode($user->full_name) }}"
                                                    alt="{{ $user->full_name }}"
                                                    class="w-12 h-12
                                                           rounded-full
                                                           object-cover">
                                            @endif


                                            <span
                                                class="absolute bottom-0 right-0
                                                       w-3.5 h-3.5
                                                       rounded-full
                                                       border-2 border-white
                                                       {{ $user->is_active ? 'bg-green-500' : 'bg-slate-400' }}"></span>

                                        </div>


                                        <div class="min-w-0">

                                            <a href="{{ route('admin.users.show', $user) }}"
                                                class="font-semibold
                                                       text-slate-900
                                                       hover:text-sky-700
                                                       transition">
                                                {{ $user->full_name }}
                                            </a>

                                            <div
                                                class="flex flex-wrap
                                                       items-center gap-x-3
                                                       gap-y-1 mt-1">

                                                <span
                                                    class="text-xs
                                                           text-slate-500">
                                                    <i
                                                        class="fa-solid
                                                               fa-id-badge
                                                               mr-1"></i>

                                                    {{ $user->employee_id ?: 'No Employee ID' }}
                                                </span>

                                                <span
                                                    class="text-xs
                                                           text-slate-400">
                                                    {{ $user->email }}
                                                </span>

                                            </div>

                                        </div>

                                    </div>

                                </td>


                                {{-- Department / Position --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="font-medium
                                               text-sm text-slate-800">
                                        {{ $user->department?->department_name ?? 'No Department' }}
                                    </div>

                                    <div
                                        class="text-xs
                                               text-slate-500 mt-1">
                                        {{ $user->position?->position_name ?? 'No Position' }}
                                    </div>

                                </td>


                                {{-- Role --}}

                                <td class="px-6 py-5">

                                    <div class="flex flex-wrap gap-1">

                                        @forelse ($user->roles as $role)
                                            <span
                                                class="inline-flex
                                                       items-center
                                                       rounded-full
                                                       bg-blue-100
                                                       text-blue-700
                                                       px-3 py-1
                                                       text-xs
                                                       font-semibold">
                                                {{ $role->name }}
                                            </span>

                                        @empty

                                            <span
                                                class="text-xs
                                                       text-slate-400">
                                                No Role
                                            </span>
                                        @endforelse

                                    </div>

                                </td>


                                {{-- Status --}}

                                <td class="px-6 py-5">

                                    @if ($user->is_active)
                                        <span
                                            class="inline-flex
                                                   items-center gap-1.5
                                                   rounded-full
                                                   bg-green-100
                                                   text-green-700
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold">

                                            <span
                                                class="w-1.5 h-1.5
                                                       rounded-full
                                                       bg-green-500"></span>

                                            Active

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex
                                                   items-center gap-1.5
                                                   rounded-full
                                                   bg-red-100
                                                   text-red-700
                                                   px-3 py-1
                                                   text-xs
                                                   font-semibold">

                                            <span
                                                class="w-1.5 h-1.5
                                                       rounded-full
                                                       bg-red-500"></span>

                                            Inactive

                                        </span>
                                    @endif

                                </td>


                                {{-- Last Login --}}

                                <td class="px-6 py-5">

                                    @if ($user->last_login_at)
                                        <div
                                            class="text-sm
                                                   text-slate-700">
                                            {{ $user->last_login_at->format('M d, Y') }}
                                        </div>

                                        <div
                                            class="text-xs
                                                   text-slate-400 mt-1">
                                            {{ $user->last_login_at->format('h:i A') }}
                                        </div>
                                    @else
                                        <span
                                            class="text-xs
                                                   text-slate-400">
                                            Never logged in
                                        </span>
                                    @endif

                                </td>


                                {{-- Actions --}}

                                <td class="px-6 py-5">

                                    <div
                                        class="flex items-center
                                               justify-end gap-2">


                                        {{-- View --}}

                                        <a href="{{ route('admin.users.show', $user) }}"
                                            title="View Employee"
                                            class="w-10 h-10
                                                   rounded-xl
                                                   bg-sky-50
                                                   text-sky-700
                                                   flex items-center
                                                   justify-center
                                                   hover:bg-sky-100
                                                   transition">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>


                                        {{-- Edit --}}

                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            title="Edit Employee"
                                            class="w-10 h-10
                                                   rounded-xl
                                                   bg-amber-50
                                                   text-amber-700
                                                   flex items-center
                                                   justify-center
                                                   hover:bg-amber-100
                                                   transition">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>


                                        {{-- Reset Password --}}

                                        <form
                                            action="{{ route('admin.users.reset', $user) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                                'Generate a new temporary password for this employee?'
                                            )">

                                            @csrf

                                            <button type="submit" title="Reset Password"
                                                class="w-10 h-10
                                                       rounded-xl
                                                       bg-purple-50
                                                       text-purple-700
                                                       hover:bg-purple-100
                                                       transition">

                                                <i class="fa-solid fa-key"></i>

                                            </button>

                                        </form>


                                        {{-- Delete --}}

                                        @if (auth()->id() !== $user->id)
                                            <form
                                                action="{{ route('admin.users.destroy', $user) }}"
                                                method="POST"
                                                onsubmit="return confirm(
                                                    'Are you sure you want to delete this employee?'
                                                )">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" title="Delete Employee"
                                                    class="w-10 h-10
                                                           rounded-xl
                                                           bg-red-50
                                                           text-red-600
                                                           hover:bg-red-100
                                                           transition">

                                                    <i class="fa-solid fa-trash"></i>

                                                </button>

                                            </form>
                                        @endif

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6"
                                    class="px-6 py-16
                                           text-center">

                                    <div
                                        class="w-16 h-16
                                               rounded-full
                                               bg-slate-100
                                               text-slate-400
                                               flex items-center
                                               justify-center
                                               mx-auto">

                                        <i
                                            class="fa-solid
                                                   fa-users
                                                   text-2xl"></i>

                                    </div>

                                    <h3
                                        class="font-semibold
                                               text-slate-800 mt-4">
                                        No employees found
                                    </h3>

                                    <p class="text-sm
                                               text-slate-500 mt-1">
                                        Try changing your search
                                        or filter criteria.
                                    </p>

                                    @if (request()->filled('search') ||
                                            request()->filled('department') ||
                                            request()->filled('position') ||
                                            request()->filled('role') ||
                                            request()->filled('status'))
                                        <a href="{{ route('admin.users.index') }}"
                                            class="inline-flex
                                                   items-center gap-2
                                                   mt-4
                                                   text-sm font-semibold
                                                   text-sky-700">

                                            <i
                                                class="fa-solid
                                                       fa-rotate-left"></i>

                                            Clear Filters

                                        </a>
                                    @endif

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- ===================================================== --}}
            {{-- PAGINATION --}}
            {{-- ===================================================== --}}

            @if ($users->hasPages())
                <div class="px-6 py-4
                           border-t border-slate-200">

                    {{ $users->links() }}

                </div>
            @endif

        </div>

    </div>

@endsection
