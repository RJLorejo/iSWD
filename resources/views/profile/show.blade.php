@extends('admin.layouts.app')

@section('title', 'My Profile')

@section('content')


    <div class="grid lg:grid-cols-3 gap-8">

        <div class="bg-white rounded-2xl shadow border p-8">

            <div class="text-center">

                <img class="w-36 h-36 rounded-full mx-auto object-cover border-4 border-sky-100"
                    src="{{ $user->avatar
                        ? asset('storage/' . $user->avatar)
                        : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&name=' .
                            urlencode($user->first_name . ' ' . $user->last_name) }}">

                <h2 class="mt-5 text-2xl font-bold">

                    {{ $user->first_name }}
                    {{ $user->last_name }}

                </h2>

                <p class="text-gray-500">

                    {{ $user->getRoleNames()->implode(', ') }}

                </p>

                @if ($user->is_active)
                    <span class="mt-4 inline-flex px-4 py-2 rounded-full bg-green-100 text-green-700">

                        Active Account

                    </span>
                @endif

            </div>

        </div>

        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow border">

                <div class="border-b p-6 flex justify-between">

                    <h2 class="font-bold text-xl">

                        Employee Information

                    </h2>

                    <a href="{{ route('profile.edit') }}" class="px-5 py-2 rounded-xl bg-sky-700 text-white">

                        Edit Profile

                    </a>

                </div>

                <div class="grid md:grid-cols-2 gap-6 p-8">

                    <x-form.readonly label="Employee ID" :value="$user->employee_id" />

                    <x-form.readonly label="Email" :value="$user->email" />

                    <x-form.readonly label="Phone" :value="$user->phone" />

                    <x-form.readonly label="Department" :value="optional($user->department)->department_name" />

                    <x-form.readonly label="Position" :value="optional($user->position)->position_name" />

                    <x-form.readonly label="Role" :value="$user->getRoleNames()->implode(', ')" />

                    <x-form.readonly label="Last Login" :value="$user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never'" />

                    <x-form.readonly label="Last Login IP" :value="$user->last_login_ip" />

                </div>

            </div>

        </div>

    </div>

@endsection
