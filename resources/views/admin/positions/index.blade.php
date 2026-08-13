@extends('admin.layouts.app')

@section('title', 'Position Management')

@section('content')

    <div class="space-y-6">

        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">

            <x-admin.stat-card title="Total Positions" :value="$totalPositions" subtitle="Registered positions"
                icon="fa-solid fa-briefcase" />

            <x-admin.stat-card title="Active Positions" :value="$activePositions" subtitle="Currently active"
                icon="fa-solid fa-circle-check" />

            <x-admin.stat-card title="Inactive Positions" :value="$inactivePositions" subtitle="Disabled positions"
                icon="fa-solid fa-circle-xmark" />

            <x-admin.stat-card title="Assigned Employees" :value="$assignedEmployees" subtitle="Employees with position"
                icon="fa-solid fa-users" />

        </div>

        <div class="bg-white rounded-2xl shadow border">

            {{-- Header --}}
            <div class="flex justify-between items-center p-6 border-b">

                <div>

                    <h2 class="text-2xl font-bold text-slate-700">
                        Position Management
                    </h2>

                    <p class="text-gray-500">
                        Manage employee positions.
                    </p>

                </div>

                <a href="{{ route('admin.positions.create') }}"
                    class="px-5 py-3 rounded-xl bg-gradient-to-r from-blue-700 via-sky-700 to-cyan-600 text-white hover:opacity-90">

                    <i class="fa-solid fa-plus mr-2"></i>

                    Add Position

                </a>

            </div>

            {{-- Search --}}
            <div class="p-6 border-b">

                <form method="GET" action="{{ route('admin.positions.index') }}">

                    <div class="flex gap-3">

                        <div class="relative flex-1">

                            <i class="fa-solid fa-search absolute left-4 top-3 text-gray-400"></i>

                            <input type="search" name="search" value="{{ request('search') }}"
                                placeholder="Search position..."
                                class="pl-11 pr-10 w-full rounded-xl border-gray-300 focus:ring-2 focus:ring-blue-500">

                            @if (request('search'))
                                <a href="{{ route('admin.positions.index') }}"
                                    class="absolute right-4 top-3 text-gray-400 hover:text-red-500">

                                    <i class="fa-solid fa-xmark"></i>

                                </a>
                            @endif

                        </div>

                        <button class="px-6 rounded-xl bg-blue-700 text-white hover:bg-blue-800">

                            <i class="fa-solid fa-search mr-2"></i>

                            Search

                        </button>

                        <a href="{{ route('admin.positions.index') }}"
                            class="px-6 rounded-xl border hover:bg-gray-100 flex items-center">

                            Reset

                        </a>

                    </div>

                </form>

            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-slate-100">

                        <tr>

                            <th class="px-6 py-4 text-left">
                                Code
                            </th>

                            <th class="text-left">
                                Position
                            </th>

                            <th class="text-left">
                                Department
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

                        @forelse($positions as $position)
                            <tr class="border-t hover:bg-slate-50">

                                <td class="px-6 py-4 font-semibold">

                                    {{ $position->position_code }}

                                </td>

                                <td>

                                    <div>

                                        <div class="font-semibold">

                                            {{ $position->position_name }}

                                        </div>

                                        <div class="text-sm text-gray-500">

                                            {{ Str::limit($position->description, 40) }}

                                        </div>

                                    </div>

                                </td>

                                <td>

                                    {{ $position->department->department_name }}

                                </td>

                                <td>

                                    {{ $position->users_count }}

                                </td>

                                <td>

                                    @if ($position->is_active)
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

                                    <div class="flex gap-2">

                                        <a href="{{ route('admin.positions.show', $position) }}"
                                            class="w-10 h-10 rounded-lg bg-sky-100 text-sky-700 flex items-center justify-center hover:bg-sky-200">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.positions.edit', $position) }}"
                                            class="w-10 h-10 rounded-lg bg-yellow-100 text-yellow-700 flex items-center justify-center hover:bg-yellow-200">

                                            <i class="fa-solid fa-pen"></i>

                                        </a>

                                        <form method="POST" action="{{ route('admin.positions.destroy', $position) }}"
                                            onsubmit="return confirm('Delete this position?')">

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

                                <td colspan="6" class="py-16 text-center text-gray-400">

                                    No positions found.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="p-6">

                {{ $positions->links() }}

            </div>

        </div>

    </div>

@endsection
