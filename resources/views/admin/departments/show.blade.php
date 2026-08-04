@extends('admin.layouts.app')

@section('title', 'Department Details')

@section('content')


    <div class="max-w-5xl mx-auto">

        <div class="bg-white rounded-2xl shadow border">

            <div class="p-8 border-b flex justify-between items-center">

                <div>

                    <h2 class="text-3xl font-bold">

                        {{ $department->department_name }}

                    </h2>

                    <p class="text-gray-500 mt-1">

                        Department Information

                    </p>

                </div>

                <a href="{{ route('admin.departments.edit', $department) }}"
                    class="px-5 py-2 bg-yellow-500 text-white rounded-xl">

                    Edit

                </a>

            </div>

            <div class="grid md:grid-cols-2 gap-8 p-8">

                <div>

                    <label class="text-sm text-gray-500">

                        Department Name

                    </label>

                    <div class="font-semibold text-lg">

                        {{ $department->department_name }}

                    </div>

                </div>

                <div>

                    <label class="text-sm text-gray-500">

                        Total Employees

                    </label>

                    <div class="font-semibold text-lg">

                        {{ $department->users()->count() }}

                    </div>

                </div>

                <div class="md:col-span-2">

                    <label class="text-sm text-gray-500">

                        Description

                    </label>

                    <div class="mt-2">

                        {{ $department->description ?: 'No description available.' }}

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
