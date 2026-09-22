@extends('customer-service.layouts.app')

@section('title', 'Consumers')

@section('content')

    <div class="space-y-6">

        {{-- ===================================================== --}}
        {{-- HEADER --}}
        {{-- ===================================================== --}}

        <div
            class="flex flex-col md:flex-row
                   md:items-center md:justify-between gap-4"
        >

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Consumers
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage registered Sagay Water District consumers.
                </p>

            </div>


            <a
                href="{{ route('customer-service.consumers.create') }}"
                class="inline-flex items-center justify-center gap-2
                       px-5 py-2.5 rounded-xl
                       bg-gradient-to-r
                       from-sky-700 via-blue-700 to-cyan-600
                       text-white font-medium
                       hover:from-sky-800
                       hover:via-blue-800
                       hover:to-cyan-700
                       transition shadow-sm"
            >

                <i class="fa-solid fa-user-plus"></i>

                Register Consumer

            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- FLASH MESSAGES --}}
        {{-- ===================================================== --}}

        @if (session('success'))

            <div
                class="rounded-xl border border-green-200
                       bg-green-50 p-4"
            >

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-check text-green-600"></i>

                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif


        @if (session('error'))

            <div
                class="rounded-xl border border-red-200
                       bg-red-50 p-4"
            >

                <div class="flex items-center gap-3">

                    <i class="fa-solid fa-circle-exclamation text-red-600"></i>

                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>

                </div>

            </div>

        @endif


        {{-- ===================================================== --}}
        {{-- STATISTICS --}}
        {{-- ===================================================== --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            <x-admin.stat-card
                title="Total Consumers"
                :value="$totalConsumers"
                icon="fa-solid fa-users"
                color="blue"
            />

            <x-admin.stat-card
                title="Active Consumers"
                :value="$activeConsumers"
                icon="fa-solid fa-circle-check"
                color="green"
            />

            <x-admin.stat-card
                title="Inactive Consumers"
                :value="$inactiveConsumers"
                icon="fa-solid fa-circle-xmark"
                color="red"
            />

            <x-admin.stat-card
                title="Registered Today"
                :value="$todayConsumers"
                icon="fa-solid fa-calendar-day"
                color="purple"
            />

        </div>


        {{-- ===================================================== --}}
        {{-- FILTERS --}}
        {{-- ===================================================== --}}

        <div
            class="bg-white rounded-2xl
                   border border-gray-200 shadow-sm"
        >

            <form method="GET" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <div class="md:col-span-7">

                        <label
                            class="block text-sm font-medium
                                   text-gray-700 mb-2"
                        >
                            Search Consumer
                        </label>


                        <div class="relative">

                            <i
                                class="fa-solid fa-magnifying-glass
                                       absolute left-4 top-1/2
                                       -translate-y-1/2 text-gray-400"
                            ></i>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Account number, name, phone, email, address..."
                                class="w-full pl-11 pr-4 py-2.5
                                       rounded-xl border-gray-300
                                       focus:border-blue-500
                                       focus:ring-blue-500"
                            >

                        </div>

                    </div>


                    <div class="md:col-span-3">

                        <label
                            class="block text-sm font-medium
                                   text-gray-700 mb-2"
                        >
                            Status
                        </label>

                        <select
                            name="status"
                            class="w-full py-2.5 rounded-xl
                                   border-gray-300
                                   focus:border-blue-500
                                   focus:ring-blue-500"
                        >

                            <option value="">
                                All Consumers
                            </option>

                            <option
                                value="1"
                                @selected(request('status') === '1')
                            >
                                Active
                            </option>

                            <option
                                value="0"
                                @selected(request('status') === '0')
                            >
                                Inactive
                            </option>

                        </select>

                    </div>


                    <div class="md:col-span-2 flex items-end">

                        <button
                            type="submit"
                            class="w-full inline-flex
                                   items-center justify-center gap-2
                                   py-2.5 rounded-xl
                                   bg-gradient-to-r
                                   from-sky-700 via-blue-700 to-cyan-600
                                   text-white font-medium
                                   hover:from-sky-800
                                   hover:via-blue-800
                                   hover:to-cyan-700 transition"
                        >

                            <i class="fa-solid fa-filter"></i>

                            Filter

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- ===================================================== --}}
        {{-- MOBILE CARDS --}}
        {{-- ===================================================== --}}

        <div class="lg:hidden space-y-4">

            @forelse ($consumers as $consumer)

                <div
                    class="bg-white rounded-2xl
                           border border-gray-200
                           shadow-sm p-5"
                >

                    <div class="flex items-start justify-between gap-3">

                        <div class="flex items-center gap-3 min-w-0">

                            <div
                                class="w-11 h-11 rounded-xl
                                       bg-blue-100 text-blue-700
                                       flex items-center justify-center
                                       shrink-0"
                            >

                                <i class="fa-solid fa-user"></i>

                            </div>


                            <div class="min-w-0">

                                <p class="font-semibold text-gray-900 truncate">
                                    {{ $consumer->full_name }}
                                </p>

                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $consumer->account_number }}
                                </p>

                            </div>

                        </div>


                        @if ($consumer->is_active)

                            <x-admin.badge color="green">
                                Active
                            </x-admin.badge>

                        @else

                            <x-admin.badge color="red">
                                Inactive
                            </x-admin.badge>

                        @endif

                    </div>


                    <div class="mt-5 space-y-3 text-sm">

                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-phone
                                       text-gray-400 mt-1 w-4"
                            ></i>

                            <span class="text-gray-700">
                                {{ $consumer->phone }}
                            </span>

                        </div>


                        @if ($consumer->email)

                            <div class="flex items-start gap-3">

                                <i
                                    class="fa-solid fa-envelope
                                           text-gray-400 mt-1 w-4"
                                ></i>

                                <span class="text-gray-700 break-all">
                                    {{ $consumer->email }}
                                </span>

                            </div>

                        @endif


                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-location-dot
                                       text-gray-400 mt-1 w-4"
                            ></i>

                            <span class="text-gray-700">

                                @if ($consumer->address)

                                    {{ $consumer->address->barangay }},
                                    {{ $consumer->address->municipality }},
                                    {{ $consumer->address->province }}

                                @else

                                    No address recorded

                                @endif

                            </span>

                        </div>


                        <div class="flex items-start gap-3">

                            <i
                                class="fa-solid fa-globe
                                       text-gray-400 mt-1 w-4"
                            ></i>

                            <span class="text-gray-700">

                                {{ $consumer->user
                                    ? 'Portal Account Active'
                                    : 'Offline Consumer' }}

                            </span>

                        </div>

                    </div>


                    <div
                        class="mt-5 pt-4 border-t border-gray-100
                               flex items-center gap-2"
                    >

                        <a
                            href="{{ route(
                                'customer-service.consumers.show',
                                $consumer
                            ) }}"
                            class="flex-1 inline-flex items-center
                                   justify-center gap-2
                                   py-2.5 rounded-xl
                                   bg-blue-50 text-blue-700
                                   hover:bg-blue-100"
                        >

                            <i class="fa-solid fa-eye"></i>

                            View

                        </a>


                        <a
                            href="{{ route(
                                'customer-service.consumers.edit',
                                $consumer
                            ) }}"
                            class="flex-1 inline-flex items-center
                                   justify-center gap-2
                                   py-2.5 rounded-xl
                                   bg-amber-50 text-amber-700
                                   hover:bg-amber-100"
                        >

                            <i class="fa-solid fa-pen-to-square"></i>

                            Edit

                        </a>

                    </div>

                </div>

            @empty

                <div
                    class="bg-white rounded-2xl border
                           border-gray-200 p-10 text-center"
                >

                    <i class="fa-solid fa-users text-3xl text-gray-300"></i>

                    <h3 class="font-semibold text-gray-900 mt-4">
                        No consumers found
                    </h3>

                    <p class="text-sm text-gray-500 mt-1">
                        Try changing your filters or register a new consumer.
                    </p>

                </div>

            @endforelse

        </div>


        {{-- ===================================================== --}}
        {{-- DESKTOP TABLE --}}
        {{-- ===================================================== --}}

        <div
            class="hidden lg:block bg-white rounded-2xl
                   border border-gray-200
                   shadow-sm overflow-hidden"
        >

            <div
                class="px-6 py-5 border-b border-gray-200
                       flex items-center justify-between"
            >

                <div>

                    <h2 class="font-semibold text-gray-900">
                        Registered Consumers
                    </h2>

                    <p class="text-sm text-gray-500 mt-1">
                        Consumer records registered in the system.
                    </p>

                </div>

                <span class="text-sm text-gray-500">
                    {{ $consumers->total() }} records
                </span>

            </div>


            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Consumer
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Account
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Location
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Contact
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Portal
                            </th>

                            <th
                                class="px-6 py-4 text-left
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Status
                            </th>

                            <th
                                class="px-6 py-4 text-right
                                       text-xs font-semibold
                                       text-gray-500 uppercase"
                            >
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100">

                        @forelse ($consumers as $consumer)

                            <tr class="hover:bg-gray-50">

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                                   bg-blue-100 text-blue-700
                                                   flex items-center justify-center
                                                   shrink-0"
                                        >

                                            <i class="fa-solid fa-user"></i>

                                        </div>


                                        <p class="font-semibold text-gray-900">
                                            {{ $consumer->full_name }}
                                        </p>

                                    </div>

                                </td>


                                <td class="px-6 py-4">

                                    <p class="text-sm font-medium text-gray-900">
                                        {{ $consumer->account_number }}
                                    </p>

                                </td>


                                <td class="px-6 py-4">

                                    @if ($consumer->address)

                                        <p class="text-sm text-gray-900">
                                            {{ $consumer->address->barangay }}
                                        </p>

                                        <p class="text-xs text-gray-500 mt-1">

                                            {{ $consumer->address->municipality }},
                                            {{ $consumer->address->province }}

                                        </p>

                                    @else

                                        <span class="text-sm text-gray-400">
                                            No address
                                        </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <p class="text-sm text-gray-700">
                                        {{ $consumer->phone }}
                                    </p>

                                    @if ($consumer->email)

                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $consumer->email }}
                                        </p>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if ($consumer->user)

                                        <x-admin.badge color="green">
                                            Portal Active
                                        </x-admin.badge>

                                    @else

                                        <x-admin.badge color="gray">
                                            Offline
                                        </x-admin.badge>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if ($consumer->is_active)

                                        <x-admin.badge color="green">
                                            Active
                                        </x-admin.badge>

                                    @else

                                        <x-admin.badge color="red">
                                            Inactive
                                        </x-admin.badge>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <div
                                        class="flex items-center
                                               justify-end gap-2"
                                    >

                                        <a
                                            href="{{ route(
                                                'customer-service.consumers.show',
                                                $consumer
                                            ) }}"
                                            class="w-9 h-9 rounded-lg
                                                   inline-flex items-center
                                                   justify-center
                                                   bg-blue-50 text-blue-600
                                                   hover:bg-blue-100"
                                            title="View Consumer"
                                        >

                                            <i class="fa-solid fa-eye"></i>

                                        </a>


                                        <a
                                            href="{{ route(
                                                'customer-service.consumers.edit',
                                                $consumer
                                            ) }}"
                                            class="w-9 h-9 rounded-lg
                                                   inline-flex items-center
                                                   justify-center
                                                   bg-amber-50 text-amber-600
                                                   hover:bg-amber-100"
                                            title="Edit Consumer"
                                        >

                                            <i class="fa-solid fa-pen-to-square"></i>

                                        </a>


                                        <form
                                            action="{{ route(
                                                'customer-service.consumers.destroy',
                                                $consumer
                                            ) }}"
                                            method="POST"
                                            onsubmit="
                                                return confirm(
                                                    'Are you sure you want to delete this consumer?'
                                                );
                                            "
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="w-9 h-9 rounded-lg
                                                       inline-flex items-center
                                                       justify-center
                                                       bg-red-50 text-red-600
                                                       hover:bg-red-100"
                                                title="Delete Consumer"
                                            >

                                                <i class="fa-solid fa-trash"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="7"
                                    class="px-6 py-16 text-center"
                                >

                                    <i
                                        class="fa-solid fa-users
                                               text-3xl text-gray-300"
                                    ></i>

                                    <h3 class="font-semibold text-gray-900 mt-4">
                                        No consumers found
                                    </h3>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- Pagination --}}
        @if ($consumers->hasPages())

            <div>
                {{ $consumers->links() }}
            </div>

        @endif

    </div>

@endsection
