@extends('customer-service.layouts.app')

@section('title', 'Consumers')

@section('content')

    <div class="space-y-6">

        {{-- ===================================================== --}}
        {{-- PAGE HEADER --}}
        {{-- ===================================================== --}}

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>

                <h1 class="text-2xl font-bold text-gray-900">
                    Consumers
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Manage registered consumers, accounts, and water service connections.
                </p>

            </div>

            <a href="{{ route('customer-service.consumers.create') }}"
                class="inline-flex items-center justify-center gap-2
                   px-5 py-2.5 rounded-xl
                   bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
                   text-white font-medium
                   hover:from-sky-800 hover:via-blue-800 hover:to-cyan-700
                   transition shadow-sm">
                <i class="fa-solid fa-user-plus"></i>
                Register Consumer
            </a>

        </div>


        {{-- ===================================================== --}}
        {{-- STATISTICS --}}
        {{-- ===================================================== --}}

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">

            <x-admin.stat-card title="Total Consumers" :value="$totalConsumers" icon="fa-solid fa-users" color="blue" />

            <x-admin.stat-card title="Active Consumers" :value="$activeConsumers" icon="fa-solid fa-circle-check" color="green" />

            <x-admin.stat-card title="Inactive Consumers Accounts" :value="$inactiveConsumers" icon="fa-solid fa-circle-xmark"
                color="red" />

            <x-admin.stat-card title="Registered Today" :value="$todayConsumers" icon="fa-solid fa-calendar-day" color="purple" />

        </div>


        {{-- ===================================================== --}}
        {{-- SEARCH / FILTER --}}
        {{-- ===================================================== --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm">

            <form method="GET" class="p-5">

                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    {{-- Search --}}
                    <div class="md:col-span-7">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Search Consumer
                        </label>

                        <div class="relative">

                            <i
                                class="fa-solid fa-magnifying-glass
                                   absolute left-4 top-1/2
                                   -translate-y-1/2
                                   text-gray-400"></i>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Consumer no., name, phone, email..."
                                class="w-full pl-11 pr-4 py-2.5
                                   rounded-xl
                                   border-gray-300
                                   focus:border-blue-500
                                   focus:ring-blue-500">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="md:col-span-3">

                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Status
                        </label>

                        <select name="status"
                            class="w-full py-2.5 rounded-xl border-gray-300
                               focus:border-blue-500 focus:ring-blue-500">

                            <option value="">
                                All Consumers
                            </option>

                            <option value="1" @selected(request('status') === '1')>
                                Active
                            </option>

                            <option value="0" @selected(request('status') === '0')>
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Search Button --}}
                    <div class="md:col-span-2 flex items-end">

                        <button type="submit"
                            class="w-full inline-flex items-center justify-center gap-2
                               py-2.5 rounded-xl
                               bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600
                               text-white font-medium
                               hover:from-sky-800 hover:via-blue-800 hover:to-cyan-700
                               transition">

                            <i class="fa-solid fa-filter"></i>

                            Filter

                        </button>

                    </div>

                </div>

            </form>

        </div>


        {{-- ===================================================== --}}
        {{-- CONSUMERS TABLE --}}
        {{-- ===================================================== --}}

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">

            <div class="px-6 py-5 border-b border-gray-200">

                <div class="flex items-center justify-between">

                    <div>

                        <h2 class="font-semibold text-gray-900">
                            Registered Consumers
                        </h2>

                        <p class="text-sm text-gray-500 mt-1">
                            Consumer records registered in the system.
                        </p>

                    </div>

                    <div class="text-sm text-gray-500">

                        {{ $consumers->total() }} records

                    </div>

                </div>

            </div>


            {{-- Responsive table --}}
            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Consumer
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Account
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Location
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Service
                            </th>

                            <th
                                class="px-6 py-4 text-left text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Consumer Status
                            </th>

                            <th
                                class="px-6 py-4 text-right text-xs font-semibold
                                   text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-gray-100 bg-white">

                        @forelse($consumers as $consumer)
                            @php
                                $connection = $consumer->serviceConnections->first();
                            @endphp

                            <tr class="hover:bg-gray-50 transition">

                                {{-- ================================= --}}
                                {{-- CONSUMER --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div
                                            class="w-10 h-10 rounded-xl
                                               bg-blue-100 text-blue-700
                                               flex items-center justify-center
                                               flex-shrink-0">

                                            <i class="fa-solid fa-user"></i>

                                        </div>

                                        <div>

                                            <p class="font-semibold text-gray-900">
                                                {{ $consumer->full_name }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-0.5">
                                                {{ $consumer->consumer_no }}
                                            </p>

                                        </div>

                                    </div>

                                </td>


                                {{-- ================================= --}}
                                {{-- ACCOUNT --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    @if ($connection)
                                        <div>

                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $connection->account_number }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                Meter:
                                                {{ $connection->meter_number }}
                                            </p>

                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">
                                            No service connection
                                        </span>
                                    @endif

                                </td>


                                {{-- ================================= --}}
                                {{-- LOCATION --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    @if ($consumer->address)
                                        <div class="flex items-start gap-2">

                                            <i
                                                class="fa-solid fa-location-dot
                                                  text-gray-400 mt-1"></i>

                                            <div>

                                                <p class="text-sm text-gray-900">
                                                    {{ $consumer->address->barangay }}
                                                </p>

                                                <p class="text-xs text-gray-500">
                                                    {{ $consumer->address->municipality }},
                                                    {{ $consumer->address->province }}
                                                </p>

                                            </div>

                                        </div>
                                    @else
                                        <span class="text-sm text-gray-400">
                                            No address
                                        </span>
                                    @endif

                                </td>


                                {{-- ================================= --}}
                                {{-- CONTACT --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    <div class="space-y-1">

                                        <div class="flex items-center gap-2">

                                            <i
                                                class="fa-solid fa-phone
                                                  text-gray-400 text-xs"></i>

                                            <span class="text-sm text-gray-700">
                                                {{ $consumer->phone }}
                                            </span>

                                        </div>

                                        @if ($consumer->email)
                                            <div class="flex items-center gap-2">

                                                <i
                                                    class="fa-solid fa-envelope
                                                      text-gray-400 text-xs"></i>

                                                <span class="text-xs text-gray-500">
                                                    {{ $consumer->email }}
                                                </span>

                                            </div>
                                        @endif

                                    </div>

                                </td>


                                {{-- ================================= --}}
                                {{-- SERVICE STATUS --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    @if ($connection)
                                        @switch($connection->status)
                                            @case('Active')
                                                <x-admin.badge color="green">
                                                    <i class="fa-solid fa-circle-check mr-1"></i>
                                                    Active
                                                </x-admin.badge>
                                            @break

                                            @case('Inactive')
                                                <x-admin.badge color="gray">
                                                    <i class="fa-solid fa-circle-pause mr-1"></i>
                                                    Inactive
                                                </x-admin.badge>
                                            @break

                                            @case('Disconnected')
                                                <x-admin.badge color="red">
                                                    <i class="fa-solid fa-link-slash mr-1"></i>
                                                    Disconnected
                                                </x-admin.badge>
                                            @break

                                            @case('Temporary')
                                                <x-admin.badge color="yellow">
                                                    <i class="fa-solid fa-clock mr-1"></i>
                                                    Temporary
                                                </x-admin.badge>
                                            @break

                                            @default
                                                <x-admin.badge color="blue">
                                                    <i class="fa-solid fa-hourglass-half mr-1"></i>
                                                    Pending
                                                </x-admin.badge>
                                        @endswitch
                                    @else
                                        <x-admin.badge color="gray">
                                            No Connection
                                        </x-admin.badge>
                                    @endif

                                </td>


                                {{-- ================================= --}}
                                {{-- CONSUMER STATUS --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    @if ($consumer->is_active)
                                        <x-admin.badge color="green">
                                            <i class="fa-solid fa-check mr-1"></i>
                                            Active
                                        </x-admin.badge>
                                    @else
                                        <x-admin.badge color="red">
                                            <i class="fa-solid fa-xmark mr-1"></i>
                                            Inactive
                                        </x-admin.badge>
                                    @endif

                                </td>


                                {{-- ================================= --}}
                                {{-- ACTIONS --}}
                                {{-- ================================= --}}

                                <td class="px-6 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- View --}}
                                        <a href="{{ route('customer-service.consumers.show', $consumer) }}"
                                            title="View Consumer"
                                            class="w-9 h-9 rounded-lg
                                               inline-flex items-center justify-center
                                               bg-blue-50 text-blue-600
                                               hover:bg-blue-100 transition">

                                            <i class="fa-solid fa-eye"></i>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                                            title="Edit Consumer"
                                            class="w-9 h-9 rounded-lg
                                               inline-flex items-center justify-center
                                               bg-amber-50 text-amber-600
                                               hover:bg-amber-100 transition">

                                            <i class="fa-solid fa-pen-to-square"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route('customer-service.consumers.destroy', $consumer) }}"
                                            method="POST"
                                            onsubmit="return confirm(
                                            'Are you sure you want to delete this consumer? This will also remove their related service records.'
                                        );">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Delete Consumer"
                                                class="w-9 h-9 rounded-lg
                                                   inline-flex items-center justify-center
                                                   bg-red-50 text-red-600
                                                   hover:bg-red-100 transition">

                                                <i class="fa-solid fa-trash-alt"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            @empty

                                <tr>

                                    <td colspan="7" class="px-6 py-16 text-center">

                                        <div class="flex flex-col items-center">

                                            <div
                                                class="w-14 h-14 rounded-full
                                               bg-gray-100
                                               flex items-center justify-center
                                               mb-4">

                                                <i
                                                    class="fa-solid fa-users
                                                  text-gray-400 text-xl"></i>

                                            </div>

                                            <h3 class="font-semibold text-gray-900">
                                                No consumers found
                                            </h3>

                                            <p class="text-sm text-gray-500 mt-1">
                                                Try changing your search or register a new consumer.
                                            </p>

                                            <a href="{{ route('customer-service.consumers.create') }}"
                                                class="mt-4 text-sm font-medium text-blue-600
                                               hover:text-blue-700">
                                                <i class="fa-solid fa-user-plus mr-1"></i>
                                                Register Consumer
                                            </a>

                                        </div>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- ================================================= --}}
                {{-- PAGINATION --}}
                {{-- ================================================= --}}

                @if ($consumers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">

                        {{ $consumers->links() }}

                    </div>
                @endif

            </div>

        </div>

    @endsection
