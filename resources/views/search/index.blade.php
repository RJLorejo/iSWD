@extends('admin.layouts.app')

@section('title', 'Global Search')

@section('content')

    <div class="space-y-8">

        <div class="bg-white rounded-2xl shadow border p-8">

            <h2 class="text-3xl font-bold">

                Search Results

            </h2>

            <p class="text-gray-500 mt-2">

                Showing results for

                <strong>

                    "{{ $keyword }}"

                </strong>

            </p>

        </div>

        {{-- Employees --}}

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-6 border-b">

                <h3 class="text-xl font-bold">

                    Employees

                    ({{ $users->count() }})

                </h3>

            </div>

            @forelse($users as $user)
                <a href="{{ route('admin.users.show', $user) }}"
                    class="flex justify-between items-center p-5 border-b hover:bg-slate-50">

                    <div>

                        <div class="font-semibold">

                            {{ $user->first_name }}

                            {{ $user->last_name }}

                        </div>

                        <div class="text-gray-500">

                            {{ $user->employee_id }}

                        </div>

                    </div>

                    <div class="text-sm text-gray-500">

                        {{ optional($user->department)->department_name }}

                    </div>

                </a>

            @empty

                <div class="p-6 text-gray-400">

                    No employees found.

                </div>
            @endforelse

        </div>

        {{-- Departments --}}

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-6 border-b">

                <h3 class="text-xl font-bold">

                    Departments

                    ({{ $departments->count() }})

                </h3>

            </div>

            @forelse($departments as $department)
                <div class="p-5 border-b">

                    {{ $department->department_name }}

                </div>

            @empty

                <div class="p-6 text-gray-400">

                    No departments found.

                </div>
            @endforelse

        </div>

        {{-- Positions --}}

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-6 border-b">

                <h3 class="text-xl font-bold">

                    Positions

                    ({{ $positions->count() }})

                </h3>

            </div>

            @forelse($positions as $position)
                <div class="p-5 border-b">

                    {{ $position->position_name }}

                </div>

            @empty

                <div class="p-6 text-gray-400">

                    No positions found.

                </div>
            @endforelse

        </div>

    </div>

@endsection
