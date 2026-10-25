@extends($layout)

@section('title', 'My Profile')

@section('content')
    <div class="mx-auto max-w-7xl space-y-6 sm:space-y-8">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-2 inline-flex items-center gap-2 rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-sky-700 ring-1 ring-inset ring-sky-100">
                    <i class="fas fa-user-circle"></i>
                    Account Overview
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">
                    My Profile
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-gray-500 sm:text-base">
                    View your personal details, employment information, and account activity in one place.
                </p>
            </div>

            <a href="{{ route('profile.edit') }}"
               class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
                <i class="fas fa-pen-to-square"></i>
                Edit Profile
            </a>
        </div>

        {{-- SUCCESS / STATUS MESSAGES --}}
        @if (session('success'))
            <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                <i class="fas fa-circle-check mt-0.5"></i>
                <p>{{ session('success') }}</p>
            </div>
        @endif

        @if (session('error'))
            <div role="alert" class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                <i class="fas fa-circle-exclamation mt-0.5"></i>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        {{-- MAIN PROFILE LAYOUT --}}
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-3 lg:gap-8">

            {{-- PROFILE SUMMARY CARD --}}
            <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="h-24 bg-gradient-to-r from-sky-700 via-sky-600 to-cyan-500"></div>

                <div class="px-5 pb-6 sm:px-7">
                    <div class="-mt-14 flex justify-center">
                        <img
                            class="h-28 w-28 rounded-full border-4 border-white bg-white object-cover shadow-md sm:h-32 sm:w-32"
                            src="{{ $user->avatar
                                ? asset('storage/' . $user->avatar)
                                : 'https://ui-avatars.com/api/?background=0D8ABC&color=fff&size=256&name=' . urlencode(trim($user->first_name . ' ' . $user->last_name)) }}"
                            alt="Profile photo of {{ trim($user->first_name . ' ' . $user->last_name) }}"
                            loading="lazy"
                        >

                    </div>

                    <div class="mt-4 text-center">
                        <h2 class="break-words text-xl font-bold text-gray-900 sm:text-2xl">
                            {{ trim(implode(' ', array_filter([
                                $user->first_name,
                                $user->middle_name,
                                $user->last_name,
                                $user->suffix,
                            ]))) ?: 'Name not provided' }}
                        </h2>

                        <p class="mt-2 break-words text-sm text-gray-500">
                            {{ $user->getRoleNames()->implode(', ') ?: 'No role assigned' }}
                        </p>

                        @if ($user->is_active)
                            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-200">
                                <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                Active Account
                            </span>
                        @else
                            <span class="mt-4 inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-200">
                                <span class="h-2 w-2 rounded-full bg-red-500"></span>
                                Inactive Account
                            </span>
                        @endif
                    </div>

                    <div class="mt-6 border-t border-gray-100 pt-5">
                        <p class="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                            Quick Details
                        </p>

                        <dl class="space-y-4">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                                    <i class="fas fa-id-card"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <dt class="text-xs font-medium text-gray-500">Employee ID</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $user->employee_id ?: '—' }}</dd>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <dt class="text-xs font-medium text-gray-500">Email Address</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $user->email ?: '—' }}</dd>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700">
                                    <i class="fas fa-phone"></i>
                                </span>
                                <div class="min-w-0 flex-1">
                                    <dt class="text-xs font-medium text-gray-500">Phone Number</dt>
                                    <dd class="mt-1 break-words text-sm font-semibold text-gray-900">{{ $user->phone ?: '—' }}</dd>
                                </div>
                            </div>
                        </dl>
                    </div>

                    <a href="{{ route('profile.edit') }}"
                       class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-800 transition hover:border-sky-300 hover:bg-sky-100 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                        <i class="fas fa-user-pen"></i>
                        Update My Details
                    </a>
                </div>
            </section>

            {{-- DETAILS AND ACCOUNT ACTIVITY --}}
            <div class="space-y-6 lg:col-span-2">

                {{-- EMPLOYMENT INFORMATION --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">
                                <i class="fas fa-briefcase"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Employee Information</h2>
                                <p class="mt-1 text-sm text-gray-500">Your registered employment and account details.</p>
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-8 gap-y-6 px-5 py-6 sm:grid-cols-2 sm:px-7">
                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Employee ID</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ $user->employee_id ?: '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Email Address</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ $user->email ?: '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Phone Number</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ $user->phone ?: '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Department</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ optional($user->department)->department_name ?? '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Position</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ optional($user->position)->position_name ?? '—' }}</dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Service Area</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ optional($user->serviceArea)->name ?? '—' }}</dd>
                        </div>

                        <div class="min-w-0 sm:col-span-2">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Assigned Role(s)</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ $user->getRoleNames()->implode(', ') ?: '—' }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- ACCOUNT ACTIVITY --}}
                <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                    <div class="border-b border-gray-100 px-5 py-5 sm:px-7">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                                <i class="fas fa-shield-halved"></i>
                            </span>
                            <div>
                                <h2 class="text-lg font-bold text-gray-900 sm:text-xl">Account Activity</h2>
                                <p class="mt-1 text-sm text-gray-500">Information about your most recent recorded sign-in.</p>
                            </div>
                        </div>
                    </div>

                    <dl class="grid grid-cols-1 gap-x-8 gap-y-6 px-5 py-6 sm:grid-cols-2 sm:px-7">
                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last Login</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">
                                {{ $user->last_login_at ? $user->last_login_at->format('M d, Y h:i A') : 'No recorded login' }}
                            </dd>
                        </div>

                        <div class="min-w-0">
                            <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last Login IP</dt>
                            <dd class="mt-2 break-words text-sm font-medium text-gray-900">{{ $user->last_login_ip ?: '—' }}</dd>
                        </div>
                    </dl>
                </section>

                {{-- PROFILE MANAGEMENT NOTE --}}
                <div class="flex items-start gap-3 rounded-xl border border-sky-100 bg-sky-50 p-4">
                    <i class="fas fa-circle-info mt-0.5 text-sky-700"></i>
                    <p class="text-sm leading-6 text-sky-900">
                        Keep your contact information up to date so your account records remain accurate. You can update your editable details from the Edit Profile page.
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
