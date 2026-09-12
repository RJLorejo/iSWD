@extends($layout)

@section('title', 'My Profile')

@section('content')

    <div class="max-w-7xl mx-auto space-y-8">


        {{-- PAGE HEADER --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    My Profile
                </h1>

                <p class="text-gray-500 mt-1">
                    Manage your personal account information and security settings.
                </p>
            </div>

            <a href="{{ route('profile.edit') }}"
                class="inline-flex items-center justify-center gap-2
                   px-5 py-3 rounded-xl
                   bg-sky-700 text-white
                   font-medium
                   hover:bg-sky-800
                   transition">
                <i class="fas fa-pen"></i>

                Edit Profile
            </a>

        </div>


        {{-- PROFILE --}}
        <div class="grid lg:grid-cols-3 gap-8">

            {{-- PROFILE CARD --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

                <div class="text-center">

                    {{-- AVATAR --}}
                    <div class="relative inline-block">

                        <img class="w-36 h-36 rounded-full mx-auto object-cover
                               border-4 border-sky-100 shadow-sm"
                            src="{{ $user->avatar
                                ? asset('storage/' . $user->avatar)
                                : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&size=256&name=' .
                                    urlencode(trim($user->first_name . ' ' . $user->last_name)) }}"
                            alt="Profile photo">

                    </div>


                    {{-- NAME --}}
                    <h2 class="mt-5 text-2xl font-bold text-gray-900">

                        {{ $user->first_name }}

                        @if ($user->middle_name)
                            {{ $user->middle_name }}
                        @endif

                        {{ $user->last_name }}

                        @if ($user->suffix)
                            {{ $user->suffix }}
                        @endif

                    </h2>


                    {{-- ROLE --}}
                    <p class="mt-2 text-gray-500">

                        {{ $user->getRoleNames()->implode(', ') }}

                    </p>


                    {{-- STATUS --}}
                    @if ($user->is_active)
                        <span
                            class="mt-4 inline-flex items-center gap-2
                               px-4 py-2 rounded-full
                               bg-green-100 text-green-700
                               text-sm font-medium">

                            <span class="w-2 h-2 rounded-full bg-green-500"></span>

                            Active Account

                        </span>
                    @else
                        <span
                            class="mt-4 inline-flex items-center gap-2
                               px-4 py-2 rounded-full
                               bg-red-100 text-red-700
                               text-sm font-medium">

                            <span class="w-2 h-2 rounded-full bg-red-500"></span>

                            Inactive Account

                        </span>
                    @endif

                </div>

            </div>


            {{-- INFORMATION --}}
            <div class="lg:col-span-2">

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200">

                    {{-- HEADER --}}
                    <div class="border-b border-gray-200 p-6">

                        <h2 class="font-bold text-xl text-gray-900">
                            Employee Information
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Your registered account and employment information.
                        </p>

                    </div>


                    {{-- INFORMATION GRID --}}
                    <div class="grid md:grid-cols-2 gap-6 p-8">

                        <x-form.readonly label="Employee ID" :value="$user->employee_id ?? '—'" />

                        <x-form.readonly label="Email" :value="$user->email ?? '—'" />

                        <x-form.readonly label="Phone" :value="$user->phone ?? '—'" />

                        <x-form.readonly label="Department" :value="optional($user->department)->department_name ?? '—'" />

                        <x-form.readonly label="Position" :value="optional($user->position)->position_name ?? '—'" />

                        <x-form.readonly label="Role" :value="$user->getRoleNames()->implode(', ') ?: '—'" />

                        <x-form.readonly label="Last Login" :value="$user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'Never'" />

                        <x-form.readonly label="Last Login IP" :value="$user->last_login_ip ?? '—'" />

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
