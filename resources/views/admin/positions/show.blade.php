@extends('admin.layouts.app')

@section('title', 'Position Details')

@section('content')

    <div class="flex justify-between items-center mb-6">

        <x-form.page-header title="Position Details" subtitle="View complete position information" />

        <a href="{{ route('admin.positions.edit', $position) }}" class="px-5 py-2 bg-yellow-500 text-white rounded-xl">

            Edit

        </a>

    </div>

    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-8">

                <div class="grid grid-cols-2 gap-8">

                    <x-form.readonly label="Position Code" :value="$position->position_code" />

                    <x-form.readonly label="Position Name" :value="$position->position_name" />

                    <x-form.readonly label="Department" :value="$position->department?->department_name ?? 'Not Assigned'" />

                    <x-form.readonly label="Status" :value="$position->is_active ? 'Active' : 'Inactive'" />

                </div>

                <div class="mt-8">

                    <label class="font-semibold">

                        Description

                    </label>

                    <div class="mt-2 rounded-xl border bg-gray-50 p-4">

                        {{ $position->description ?: 'No description available.' }}

                    </div>

                </div>

            </div>

        </div>

        <div class="bg-white rounded-2xl shadow border mt-8">

            <div class="p-6 border-b">

                <h2 class="text-lg font-bold">

                    Employees Assigned

                </h2>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full">

                    <thead class="bg-gray-100">

                        <tr>

                            <th class="px-6 py-3 text-left">Employee ID</th>

                            <th class="px-6 py-3 text-left">Name</th>

                            <th class="px-6 py-3 text-left">Email</th>

                            <th class="px-6 py-3 text-left">Department</th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse($position->users as $user)
                            <tr class="border-b">

                                <td class="px-6 py-4">

                                    {{ $user->employee_id }}

                                </td>

                                <td class="px-6 py-4">

                                    {{ $user->full_name }}

                                </td>

                                <td class="px-6 py-4">

                                    {{ $user->email }}

                                </td>

                                <td class="px-6 py-4">

                                    {{ $user->department?->department_name }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="4" class="text-center py-8 text-gray-500">

                                    No employees assigned.

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        <div class="flex justify-end mt-6">

            <a href="{{ route('admin.positions.index') }}" class="px-6 py-2 rounded-xl border">

                Back

            </a>

        </div>

    </div>

@endsection
