@extends('admin.layouts.app')

@section('title', 'Employee Profile')

@section('content')

    <div class="space-y-8">

        {{-- Header --}}
        <div class="flex items-center justify-between">

            <div>

                <p class="text-gray-500">

                    View employee information and account details.

                </p>

            </div>

            <div class="flex gap-3">

                <a href="{{ route('admin.users.edit', $user) }}"
                    class="px-5 py-3 rounded-xl bg-yellow-500 text-white hover:bg-yellow-600">

                    <i class="fa-solid fa-pen mr-2"></i>

                    Edit

                </a>

                <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl border hover:bg-gray-50">

                    Back

                </a>

            </div>

        </div>

        {{-- Profile Card --}}
        <div class="bg-white rounded-3xl shadow border overflow-hidden">

            <div class="bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 h-40"></div>

            <div class="px-10 pb-10">

                <div class="-mt-16 flex flex-col lg:flex-row gap-8">

                    {{-- Avatar --}}
                    <div>

                        @if ($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}"
                                class="w-36 h-36 rounded-full border-4 border-white object-cover shadow">
                        @else
                            <img src="https://ui-avatars.com/api/?background=0D8ABC&color=fff&size=256&name={{ urlencode($user->first_name . ' ' . $user->last_name) }}"
                                class="w-36 h-36 rounded-full border-4 border-white shadow">
                        @endif

                    </div>

                    {{-- Name --}}
                    <div class="flex-1 pt-5">

                        <h2 class="text-3xl font-bold">

                            {{ $user->first_name }}
                            {{ $user->middle_name }}
                            {{ $user->last_name }}
                            {{ $user->suffix }}

                        </h2>

                        <p class="text-gray-500 mt-1">

                            {{ $user->position->position_name ?? 'No Position' }}

                        </p>

                        <div class="mt-5 flex flex-wrap gap-3">

                            @if ($user->is_active)
                                <span class="px-4 py-1 rounded-full bg-green-100 text-green-700">

                                    Active

                                </span>
                            @else
                                <span class="px-4 py-1 rounded-full bg-red-100 text-red-700">

                                    Inactive

                                </span>
                            @endif

                            @foreach ($user->roles as $role)
                                <span class="px-4 py-1 rounded-full bg-blue-100 text-blue-700">

                                    {{ $role->name }}

                                </span>
                            @endforeach

                        </div>

                    </div>

                </div>

            </div>

        </div>

        {{-- Information --}}
        <div class="grid lg:grid-cols-2 gap-8">

            {{-- Personal Information --}}
            <div class="bg-white rounded-2xl shadow border">

                <div class="border-b p-5">

                    <h3 class="font-bold text-lg">

                        Personal Information

                    </h3>

                </div>

                <div class="p-6 space-y-5">

                    <x-admin.info-row label="Employee ID" :value="$user->employee_id" />

                    <x-admin.info-row label="Email" :value="$user->email" />

                    <x-admin.info-row label="Phone" :value="$user->phone ?: '-'" />

                    <x-admin.info-row label="Department" :value="$user->department->department_name ?? '-'" />

                    <x-admin.info-row label="Position" :value="$user->position->position_name ?? '-'" />

                </div>

            </div>

            {{-- Account Information --}}
            <div class="bg-white rounded-2xl shadow border">

                <div class="border-b p-5">

                    <h3 class="font-bold text-lg">

                        Account Information

                    </h3>

                </div>

                <div class="p-6 space-y-5">

                    <x-admin.info-row label="Account Status" :value="$user->is_active ? 'Active' : 'Inactive'" />

                    <x-admin.info-row label="Role" :value="$user->roles->pluck('name')->implode(', ')" />

                    <x-admin.info-row label="Last Login" :value="$user->last_login_at ?? 'Never Logged In'" />

                    <x-admin.info-row label="Last IP Address" :value="$user->last_login_ip ?? '-'" />

                    <x-admin.info-row label="Registered" :value="$user->created_at->format('F d, Y h:i A')" />

                </div>

            </div>

        </div>

    </div>

@endsection
