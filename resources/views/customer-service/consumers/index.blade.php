@extends('customer-service.layouts.app')

@section('title', 'Consumers')

@section('content')

    <div class="space-y-5">

        @if (session('success'))
            <div class="rounded-xl border border-green-200 bg-green-50 p-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <p class="text-sm font-medium text-green-800">
                        {{ session('success') }}
                    </p>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-circle-exclamation text-red-600"></i>
                    <p class="text-sm font-medium text-red-800">
                        {{ session('error') }}
                    </p>
                </div>
            </div>
        @endif

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Consumers
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Manage registered Sagay Water District consumers and portal access.
                </p>
            </div>

            <a href="{{ route('customer-service.consumers.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg
                       bg-blue-600 px-4 py-2.5 text-sm font-medium text-white
                       transition hover:bg-blue-700">

                <i class="fa-solid fa-user-plus"></i>
                Register Consumer

            </a>

        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">

                    <div>
                        <p class="text-xs font-medium text-gray-500">
                            Total Consumers
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($totalConsumers) }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-users"></i>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">

                    <div>
                        <p class="text-xs font-medium text-gray-500">
                            Active
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($activeConsumers) }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-green-50 text-green-600">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">

                    <div>
                        <p class="text-xs font-medium text-gray-500">
                            Inactive
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($inactiveConsumers) }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-red-50 text-red-600">
                        <i class="fa-solid fa-circle-xmark"></i>
                    </div>

                </div>
            </div>

            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between gap-3">

                    <div>
                        <p class="text-xs font-medium text-gray-500">
                            Registered Today
                        </p>

                        <p class="mt-1 text-2xl font-bold text-gray-900">
                            {{ number_format($todayConsumers) }}
                        </p>
                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-50 text-purple-600">
                        <i class="fa-solid fa-calendar-day"></i>
                    </div>

                </div>
            </div>

        </div>

        {{-- ========================================================= --}}
        {{-- SEARCH / FILTER --}}
        {{-- ========================================================= --}}

        <div class="rounded-xl border border-gray-200 bg-white shadow-sm">

            <form method="GET" class="p-4">

                <div class="grid grid-cols-1 gap-3 md:grid-cols-12">

                    {{-- Search --}}
                    <div class="md:col-span-6">

                        <label for="search" class="mb-1 block text-sm font-medium text-gray-700">
                            Search Consumer
                        </label>

                        <div class="relative">

                            <i
                                class="fa-solid fa-magnifying-glass
                               absolute left-3 top-1/2
                               -translate-y-1/2 text-sm text-gray-400">
                            </i>

                            <input type="text" name="search" id="search" value="{{ request('search') }}"
                                placeholder="Account number, name, phone, email or address"
                                class="w-full rounded-lg border-gray-300
                               py-2 pl-9 pr-3 text-sm
                               focus:border-blue-500
                               focus:ring-blue-500">

                        </div>

                    </div>


                    {{-- Status --}}
                    <div class="md:col-span-3">

                        <label for="status" class="mb-1 block text-sm font-medium text-gray-700">
                            Status
                        </label>

                        <select name="status" id="status"
                            class="w-full rounded-lg border-gray-300
                           py-2 text-sm
                           focus:border-blue-500
                           focus:ring-blue-500">

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


                    {{-- Actions --}}
                    <div class="flex items-end gap-2 md:col-span-3">

                        {{-- Filter --}}
                        <button type="submit"
                            class="inline-flex flex-1 items-center
                           justify-center gap-2 rounded-lg
                           bg-blue-600 px-3 py-2
                           text-sm font-medium text-white
                           transition hover:bg-blue-700">

                            <i class="fa-solid fa-filter"></i>

                            Filter

                        </button>


                        {{-- Clear --}}
                        @if (request()->filled('search') || request()->filled('status'))
                            <a href="{{ route('customer-service.consumers.index') }}"
                                class="inline-flex h-[38px] w-[38px]
                               shrink-0 items-center justify-center
                               rounded-lg border border-gray-300
                               text-gray-500 transition
                               hover:bg-gray-50 hover:text-gray-700"
                                title="Clear filters">

                                <i class="fa-solid fa-xmark"></i>

                            </a>
                        @endif


                        {{-- Print --}}
                        <a href="{{ route('customer-service.consumers.print-report', request()->only(['search', 'status'])) }}"
                            target="_blank"
                            class="inline-flex h-[38px] w-[38px]
                           shrink-0 items-center justify-center
                           rounded-lg border border-gray-300
                           bg-white text-gray-600
                           transition
                           hover:bg-gray-50 hover:text-gray-900"
                            title="Print Consumers">

                            <i class="fa-solid fa-print"></i>


                        </a>

                    </div>

                </div>

            </form>

        </div>

        <div class="space-y-3 lg:hidden">

            @forelse ($consumers as $consumer)
                <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">

                    <div class="flex items-start justify-between gap-3">

                        <div class="flex min-w-0 items-center gap-3">

                            <div
                                class="flex h-10 w-10 shrink-0
                                       items-center justify-center rounded-lg
                                       bg-blue-50 text-blue-600">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <div class="min-w-0">

                                <p class="truncate font-semibold text-gray-900">
                                    {{ $consumer->full_name }}
                                </p>

                                <p class="mt-0.5 text-xs font-medium text-gray-500">
                                    {{ $consumer->account_number }}
                                </p>

                            </div>

                        </div>

                        @if ($consumer->is_active)
                            <span
                                class="inline-flex shrink-0 items-center gap-1
                                       rounded-full bg-green-50 px-2.5 py-1
                                       text-xs font-semibold text-green-700">

                                <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                Active

                            </span>
                        @else
                            <span
                                class="inline-flex shrink-0 items-center gap-1
                                       rounded-full bg-red-50 px-2.5 py-1
                                       text-xs font-semibold text-red-700">

                                <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                Inactive

                            </span>
                        @endif

                    </div>

                    <div class="mt-4 grid grid-cols-1 gap-2.5 text-sm sm:grid-cols-2">

                        <div class="flex items-start gap-2.5">

                            <i class="fa-solid fa-phone mt-1 w-4 text-xs text-gray-400"></i>

                            <span class="text-gray-700">
                                {{ $consumer->phone ?: 'No contact number' }}
                            </span>

                        </div>

                        <div class="flex min-w-0 items-start gap-2.5">

                            <i class="fa-solid fa-envelope mt-1 w-4 text-xs text-gray-400"></i>

                            <span class="min-w-0 break-all text-gray-700">
                                {{ $consumer->email ?: 'No email address' }}
                            </span>

                        </div>

                        <div class="flex items-start gap-2.5">

                            <i class="fa-solid fa-location-dot mt-1 w-4 text-xs text-gray-400"></i>

                            <span class="text-gray-700">

                                @if ($consumer->address)
                                    {{ collect([$consumer->address->barangay, $consumer->address->municipality, $consumer->address->province])->filter()->implode(', ') ?:
                                        'No address recorded' }}
                                @else
                                    No address recorded
                                @endif

                            </span>

                        </div>

                        <div class="flex items-start gap-2.5">

                            <i class="fa-solid fa-globe mt-1 w-4 text-xs text-gray-400"></i>

                            @if ($consumer->user && $consumer->user->is_active)
                                <span class="font-medium text-green-600">
                                    Portal Active
                                </span>
                            @elseif ($consumer->user)
                                <span class="font-medium text-red-600">
                                    Portal Inactive
                                </span>
                            @else
                                <span class="font-medium text-amber-600">
                                    Account Missing
                                </span>
                            @endif

                        </div>

                    </div>

                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3">

                        <a href="{{ route('customer-service.consumers.show', $consumer) }}"
                            class="inline-flex flex-1 items-center justify-center gap-2
                                   rounded-lg bg-blue-50 py-2
                                   text-sm font-medium text-blue-700
                                   transition hover:bg-blue-100">

                            <i class="fa-solid fa-eye"></i>
                            View

                        </a>

                        @if ($consumer->registration_source !== 'Self Registration' || $consumer->verification_status === 'Verified')
                            <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                                class="inline-flex flex-1 items-center justify-center gap-2
               rounded-lg bg-amber-50 py-2
               text-sm font-medium text-amber-700
               transition hover:bg-amber-100">

                                <i class="fa-solid fa-pen-to-square"></i>

                                Edit

                            </a>
                        @endif

                    </div>

                </div>

            @empty

                <div class="rounded-xl border border-gray-200 bg-white p-8 text-center">

                    <div
                        class="mx-auto flex h-12 w-12 items-center
                               justify-center rounded-xl bg-gray-50 text-gray-300">

                        <i class="fa-solid fa-users text-xl"></i>

                    </div>

                    <h3 class="mt-3 font-semibold text-gray-900">
                        No consumers found
                    </h3>

                    <p class="mt-1 text-sm text-gray-500">
                        Try changing your search or filter.
                    </p>

                </div>
            @endforelse

        </div>

        <div
            class="hidden overflow-hidden rounded-xl border
                   border-gray-200 bg-white shadow-sm lg:block">

            <div class="flex items-center justify-between
                       border-b border-gray-200 px-5 py-4">

                <div>
                    <h2 class="font-semibold text-gray-900">
                        Registered Consumers
                    </h2>

                    <p class="mt-0.5 text-xs text-gray-500">
                        Consumer records registered in the system.
                    </p>
                </div>

                <span class="text-xs font-medium text-gray-500">
                    {{ number_format($consumers->total()) }} records
                </span>

            </div>

            <div class="overflow-x-auto">

                <table class="min-w-full divide-y divide-gray-200">

                    <thead class="bg-gray-50">

                        <tr>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Consumer
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Account
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Contact
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Location
                            </th>

                            <th
                                class="px-5 py-3 text-left
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Status
                            </th>

                            <th
                                class="px-5 py-3 text-right
                                       text-xs font-semibold uppercase
                                       tracking-wide text-gray-500">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody class="divide-y divide-gray-100">

                        @forelse ($consumers as $consumer)
                            <tr class="transition hover:bg-gray-50">

                                <td class="px-5 py-3.5">

                                    <div class="flex items-start gap-3">

                                        <div
                                            class="flex h-9 w-9 shrink-0
                   items-center justify-center
                   rounded-lg bg-blue-50 text-blue-600">

                                            <i class="fa-solid fa-user text-sm"></i>

                                        </div>

                                        <div class="min-w-0">

                                            {{-- Consumer Name --}}
                                            <p class="font-medium text-gray-900">
                                                {{ $consumer->full_name }}
                                            </p>


                                            {{-- Email --}}
                                            @if ($consumer->email)
                                                <p
                                                    class="mt-0.5 max-w-[220px]
                          truncate text-xs text-gray-500">

                                                    {{ $consumer->email }}

                                                </p>
                                            @endif


                                            {{-- Portal Status --}}
                                            <div class="mt-1.5">

                                                @if ($consumer->user && $consumer->user->is_active)
                                                    <span
                                                        class="inline-flex items-center gap-1
                               text-[10px] font-medium text-green-600">

                                                        <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>

                                                        Portal Active

                                                    </span>
                                                @elseif ($consumer->user)
                                                    <span
                                                        class="inline-flex items-center gap-1
                               text-[10px] font-medium text-red-600">

                                                        <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>

                                                        Portal Inactive

                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center gap-1
                               text-[10px] font-medium text-amber-600">

                                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>

                                                        Portal Missing

                                                    </span>
                                                @endif

                                            </div>

                                        </div>

                                    </div>

                                </td>

                                <td class="whitespace-nowrap px-5 py-3.5">

                                    <span class="text-sm font-medium text-gray-800">
                                        {{ $consumer->account_number }}
                                    </span>

                                </td>

                                <td class="whitespace-nowrap px-5 py-3.5">

                                    <p class="text-sm text-gray-700">
                                        {{ $consumer->phone ?: '—' }}
                                    </p>

                                </td>

                                <td class="px-5 py-3.5">

                                    @if ($consumer->address)
                                        <p class="text-sm text-gray-800">
                                            {{ $consumer->address->barangay ?: '—' }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-500">
                                            {{ collect([$consumer->address->municipality, $consumer->address->province])->filter()->implode(', ') }}
                                        </p>
                                    @else
                                        <span class="text-sm text-gray-400">
                                            No address
                                        </span>
                                    @endif

                                </td>


                                <td class="whitespace-nowrap px-5 py-3.5">

                                    @if ($consumer->is_active)
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   rounded-full bg-green-50 px-2.5 py-1
                                                   text-xs font-semibold text-green-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-green-500"></span>
                                            Active

                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center gap-1.5
                                                   rounded-full bg-red-50 px-2.5 py-1
                                                   text-xs font-semibold text-red-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-red-500"></span>
                                            Inactive

                                        </span>
                                    @endif

                                </td>

                                <td class="whitespace-nowrap px-5 py-3.5">

                                    <div class="flex items-center justify-end gap-1.5">

                                        <a href="{{ route('customer-service.consumers.show', $consumer) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center
                                                   rounded-lg bg-blue-50 text-blue-600
                                                   transition hover:bg-blue-100"
                                            title="View Consumer">

                                            <i class="fa-solid fa-eye text-xs"></i>

                                        </a>

                                        @if ($consumer->registration_source !== 'Self Registration' || $consumer->verification_status === 'Verified')
                                            <a href="{{ route('customer-service.consumers.edit', $consumer) }}"
                                                class="inline-flex h-8 w-8 items-center justify-center
               rounded-lg bg-amber-50 text-amber-600
               transition hover:bg-amber-100"
                                                title="Edit Consumer">

                                                <i class="fa-solid fa-pen-to-square text-xs"></i>

                                            </a>
                                        @endif

                                        <form action="{{ route('customer-service.consumers.destroy', $consumer) }}"
                                            method="POST"
                                            onsubmit="return confirm('Permanently delete this consumer account? This action cannot be undone.');">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center
                                                       rounded-lg bg-red-50 text-red-600
                                                       transition hover:bg-red-100"
                                                title="Delete Consumer">

                                                <i class="fa-solid fa-trash text-xs"></i>

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="6" class="px-6 py-12 text-center">

                                    <div
                                        class="mx-auto flex h-12 w-12 items-center
                                               justify-center rounded-xl
                                               bg-gray-50 text-gray-300">

                                        <i class="fa-solid fa-users text-xl"></i>

                                    </div>

                                    <h3 class="mt-3 font-semibold text-gray-900">
                                        No consumers found
                                    </h3>

                                    <p class="mt-1 text-sm text-gray-500">
                                        Try changing your search or filter.
                                    </p>

                                </td>

                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

        @if ($consumers->hasPages())
            <div>
                {{ $consumers->withQueryString()->links() }}
            </div>
        @endif

    </div>

@endsection
