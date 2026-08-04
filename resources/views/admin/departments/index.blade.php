@extends('admin.layouts.app')

@section('title', 'Department Management')

@section('content')

    <div class="space-y-6">

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            <x-admin.stat-card title="Total Departments" :value="$departments->total($departments->total())" subtitle="Registered departments"
                icon="fa-solid fa-building" />

            <x-admin.stat-card title="Active" :value="$activeDepartments" subtitle="Available departments"
                icon="fa-solid fa-circle-check" />

            <x-admin.stat-card title="Inactive" :value="$inactiveDepartments" subtitle="Disabled departments"
                icon="fa-solid fa-circle-xmark" />

        </div>

        {{-- Table Card --}}
        <div class="bg-white rounded-2xl shadow border">

            {{-- Header --}}
            <div class="flex justify-between items-center p-6 border-b">

                <div>

                    <h2 class="text-2xl font-bold text-slate-700">
                        Departments
                    </h2>

                    <p class="text-gray-500">
                        Manage maintenance department records.
                    </p>

                </div>

                <a href="{{ route('admin.departments.create') }}"
                    class="px-5 py-3 rounded-xl bg-gradient-to-r from-blue-700 via-sky-700 to-cyan-600 text-white font-medium hover:opacity-90">

                    <i class="fa-solid fa-plus mr-2"></i>

                    Add Department

                </a>

            </div>

            {{-- Search --}}
            <div class="p-6 border-b">

                <form method="GET" action="{{ route('admin.departments.index') }}">

                    <div class="flex gap-3">

                        <div class="relative flex-1">

                            <i class="fa-solid fa-search absolute left-4 top-3 text-gray-400"></i>

                            <input type="search" name="search" value="{{ request('search') }}"
                                placeholder="Search department..."
                                class="pl-11 pr-10 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">

                            @if (request('search'))
                                <a href="{{ route('admin.departments.index') }}"
                                    class="absolute right-4 top-3 text-gray-400 hover:text-red-500">

                                    <i class="fa-solid fa-xmark"></i>

                                </a>
                            @endif

                        </div>

                        <button type="submit" class="px-6 bg-blue-700 text-white rounded-xl hover:bg-blue-800">

                            <i class="fa-solid fa-search mr-2"></i>

                            Search

                        </button>

                    </div>

                </form>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-100">

                        <tr>

                            <th class="px-6 py-4 text-left">
                                Department Code
                            </th>

                            <th class="text-left">
                                Department Name
                            </th>

                            <th class="text-left">
                                Description
                            </th>

                            <th class="text-left">
                                Employees
                            </th>

                            <th class="text-left">
                                Status
                            </th>

                            <th width="220">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($departments as $department)
                            <tr class="border-t hover:bg-slate-50">

                                <td class="px-6 py-4 font-semibold">

                                    {{ $department->department_code }}

                                </td>

                                <td>

                                    {{ $department->department_name }}

                                </td>

                                <td>

                                    {{ $department->description }}

                                </td>

                                <td>

                                    {{ $department->users_count }}

                                </td>

                                <td>

                                    @if ($department->is_active)
                                        <span
                                            class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">

                                            Active

                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold">

                                            Inactive

                                        </span>
                                    @endif

                                </td>

                                <td>

                                    <div class="flex items-center gap-2">

                                        <a href="{{ route('admin.departments.show', $department) }}"
                                            class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center hover:bg-sky-200">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.departments.edit', $department) }}"
                                            class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>

                                        <form method="POST" action="{{ route('admin.departments.destroy', $department) }}"
                                            onsubmit="return confirm('Delete this department?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="w-10 h-10 rounded-lg bg-red-100 text-red-700 hover:bg-red-200">

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="py-12 text-center text-gray-400">

                                    No departments found.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="p-6">

                {{ $departments->links() }}

            </div>

        </div>

    </div>

@endsection
