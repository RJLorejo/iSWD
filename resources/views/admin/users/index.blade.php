@extends('admin.layouts.app')

@section('title', 'User Management')

@section('content')

    <div class="bg-white rounded-2xl shadow border">

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">

            <x-admin.stat-card title="Total Employees" :value="$totalEmployees" subtitle="Registered employees"
                icon="fa-solid fa-users" />

            <x-admin.stat-card title="Active Employees" :value="$activeEmployees" subtitle="Currently active"
                icon="fa-solid fa-user-check" />

            <x-admin.stat-card title="Inactive Employees" :value="$inactiveEmployees" subtitle="Disabled accounts"
                icon="fa-solid fa-user-xmark" />


            <x-admin.stat-card title="Customer Service" :value="$customer_services" subtitle="Customer service representatives"
                icon="fa-solid fa-users-gear" />

            <x-admin.stat-card title="Maintenance Managers" :value="$maintenance_managers" subtitle="Maintenance managers"
                icon="fa-solid fa-user-gear" />

            <x-admin.stat-card title="Maintenance Technicians" :value="$maintenance_technicians" subtitle="Maintenance personnel"
                icon="fa-solid fa-screwdriver-wrench" />
        </div>

        <div class="p-6 border-b flex justify-between items-center">

            <div>

                <h2 class="text-2xl font-bold">

                    Employees

                </h2>

                <p class="text-gray-500">

                    Manage employee accounts.

                </p>

            </div>

            <a href="{{ route('admin.users.create') }}"
                class="px-5 py-3 bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white rounded-xl hover:opacity-90">

                + Add Employee

            </a>

        </div>

        <div class="bg-white rounded-2xl shadow border p-6 mb-8">

            <form>

                <div class="grid lg:grid-cols-5 gap-4">

                    <input type="text" name="search" placeholder="Search employee..." value="{{ request('search') }}"
                        class="rounded-xl border-gray-300">

                    <select name="department" class="rounded-xl">

                        <option value="">

                            Department

                        </option>

                        @foreach ($departments as $id => $department)
                            <option value="{{ $id }}" @selected(request('department') == $id)>

                                {{ $department }}

                            </option>
                        @endforeach

                    </select>

                    <select name="position" class="rounded-xl">

                        <option value="">

                            Position

                        </option>

                        @foreach ($positions as $id => $position)
                            <option value="{{ $id }}" @selected(request('position') == $id)>

                                {{ $position }}

                            </option>
                        @endforeach

                    </select>

                    <select name="status" class="rounded-xl border-gray-300">

                        <option value="">

                            Status

                        </option>

                        <option value="1">

                            Active

                        </option>

                        <option value="0">

                            Inactive

                        </option>

                    </select>

                    <div class="flex gap-2">

                        <button class="flex-1 bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 text-white rounded-xl">

                            <i class="fas fa-search"></i>

                            Search

                        </button>

                        <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded-xl">

                            Reset

                        </a>

                    </div>

                </div>

            </form>

        </div>

        <div class="overflow-x-auto">

            <table class="w-full">

                <thead class="bg-slate-100 text-slate-700">

                    <tr>

                        <th class="w-20 p-4">Photo</th>

                        <th>Employee</th>

                        <th>Department</th>

                        <th>Position</th>

                        <th>Role</th>

                        <th>Status</th>

                        <th>Last Login</th>

                        <th class="w-60 text-center">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($users as $user)

                        <tr class="border-t hover:bg-slate-50 transition">

                            {{-- Avatar --}}
                            <td class="p-4">

                                @if ($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                        class="w-12 h-12 rounded-full object-cover">
                                @else
                                    <img src="https://ui-avatars.com/api/?background=0D8ABC&color=fff&name={{ urlencode($user->first_name . ' ' . $user->last_name) }}"
                                        class="w-12 h-12 rounded-full">
                                @endif

                            </td>

                            {{-- Employee --}}
                            <td class="py-4">

                                <div class="font-semibold text-slate-800">

                                    {{ $user->first_name }}
                                    {{ $user->middle_name }}
                                    {{ $user->last_name }}
                                    {{ $user->suffix }}

                                </div>

                                <div class="text-sm text-gray-500">

                                    {{ $user->employee_id }}

                                </div>

                                <div class="text-xs text-gray-400">

                                    {{ $user->email }}

                                </div>

                            </td>

                            {{-- Department --}}
                            <td>

                                {{ optional($user->department)->department_name ?? '-' }}

                            </td>

                            {{-- Position --}}
                            <td>

                                {{ optional($user->position)->position_name ?? '-' }}

                            </td>

                            {{-- Role --}}
                            <td>

                                @foreach ($user->roles as $role)
                                    <span class="inline-flex px-3 py-1 rounded-full bg-blue-100 text-blue-700 text-xs">

                                        {{ $role->name }}

                                    </span>
                                @endforeach

                            </td>

                            {{-- Status --}}
                            <td>

                                @if ($user->is_active)
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">

                                        Active

                                    </span>
                                @else
                                    <span
                                        class="inline-flex px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                        Inactive

                                    </span>
                                @endif

                            </td>

                            {{-- Last Login --}}
                            <td class="text-sm text-gray-500">

                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never Logged In' }}

                            </td>

                            {{-- Actions --}}
                            <td>

                                <div class="flex items-center gap-2">

                                    <a href="{{ route('admin.users.show', $user) }}"
                                        class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center hover:bg-sky-200"
                                        title="View">

                                        <i class="fa-solid fa-eye"></i>

                                    </a>

                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200"
                                        title="Edit">

                                        <i class="fa-solid fa-pen"></i>

                                    </a>

                                    <form action="{{ route('admin.users.reset', $user) }}" method="POST">

                                        @csrf

                                        <button type="submit"
                                            class="w-10 h-10 rounded-lg bg-purple-100 text-purple-700 hover:bg-purple-200"
                                            title="Reset Password">

                                            <i class="fa-solid fa-key"></i>

                                        </button>

                                    </form>

                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('Delete this employee?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                            class="w-10 h-10 rounded-lg bg-red-100 text-red-600 hover:bg-red-200"
                                            title="Delete">

                                            <i class="fa-solid fa-trash"></i>

                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="8" class="py-12 text-center text-gray-500">

                                <i class="fa-solid fa-users text-4xl mb-3 block"></i>

                                No employees found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        <div class="p-6">

            {{ $users->links() }}

        </div>

    </div>

@endsection
