
@extends('admin.layouts.app')

@section('title', 'Consumer Management')

@section('content')
<div class="max-w-7xl mx-auto space-y-5">

    {{-- Header --}}
    <div>
        <p class="text-sm font-semibold text-sky-600">Administration</p>
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
            Consumer Management
        </h1>
        <p class="text-sm text-slate-500 mt-1">
            View consumer accounts, contact information, and service addresses.
        </p>
    </div>

    {{-- Statistics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-medium text-slate-500">Total Consumers</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">
                {{ number_format($totalConsumers) }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-medium text-slate-500">Active</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ number_format($activeConsumers) }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-medium text-slate-500">Inactive</p>
            <p class="text-2xl font-bold text-red-600 mt-1">
                {{ number_format($inactiveConsumers) }}
            </p>
        </div>

        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <p class="text-xs font-medium text-slate-500">Registered Today</p>
            <p class="text-2xl font-bold text-sky-700 mt-1">
                {{ number_format($registeredToday) }}
            </p>
        </div>

    </div>

    {{-- Search and filter --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4">
        <form method="GET"
              action="{{ route('admin.consumers.index') }}"
              class="grid grid-cols-1 md:grid-cols-12 gap-4">

            <div class="md:col-span-8">
                <label for="search"
                       class="block text-sm font-semibold text-slate-700 mb-2">
                    Search consumers
                </label>

                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                    <input id="search"
                           type="search"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Account number, name, phone, email, or address..."
                           class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500 pl-10 pr-3 py-2.5 text-sm">
                </div>
            </div>

            <div class="md:col-span-2">
                <label for="status"
                       class="block text-sm font-semibold text-slate-700 mb-2">
                    Account Status
                </label>

                <select id="status"
                        name="status"
                        class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500 px-3 py-2.5 text-sm">
                    <option value="all" @selected(request('status', 'all') === 'all')>
                        All Statuses
                    </option>
                    <option value="1" @selected(request('status') === '1')>
                        Active
                    </option>
                    <option value="0" @selected(request('status') === '0')>
                        Inactive
                    </option>
                </select>
            </div>

            <div class="md:col-span-1 flex items-end">
                <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-xl bg-sky-700 text-white text-sm font-semibold hover:bg-sky-800">
                    <i class="fa-solid fa-filter text-xs"></i>
                    <span class="xl:inline">Filter</span>
                </button>
            </div>

            <div class="md:col-span-1 flex items-end">
                <a href="{{ route('admin.consumers.index') }}"
                   title="Reset filters"
                   class="w-full inline-flex items-center justify-center px-3 py-2.5 rounded-xl border border-slate-300 text-slate-600 text-sm hover:bg-slate-50">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- Consumer list --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">

        <div class="px-4 py-4 border-b border-slate-200 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h2 class="font-semibold text-slate-900">Consumers</h2>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ number_format($consumers->total()) }}
                    {{ \Illuminate\Support\Str::plural('consumer', $consumers->total()) }}
                    found
                </p>
            </div>

            <a href="{{ route('admin.consumers.print-report', request()->query()) }}"
               target="_blank"
               rel="noopener"
               class="inline-flex items-center justify-center gap-2 px-3.5 py-2 rounded-lg border border-slate-300 text-slate-600 text-xs sm:text-sm font-semibold hover:bg-slate-50">
                <i class="fa-solid fa-print"></i>
                Print Report
            </a>
        </div>

        {{-- Desktop table --}}
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full table-fixed">
                <thead class="bg-slate-50">
                    <tr class="text-left text-[11px] uppercase tracking-wide font-semibold text-slate-500">
                        <th class="w-[28%] px-4 py-3">Consumer</th>
                        <th class="w-[15%] px-4 py-3">Account Number</th>
                        <th class="w-[15%] px-4 py-3">Contact</th>
                        <th class="w-[24%] px-4 py-3">Service Address</th>
                        <th class="w-[9%] px-4 py-3">Status</th>
                        <th class="w-[9%] px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-100">
                    @forelse ($consumers as $consumer)
                        @php
                            $address = $consumer->address;
                            $serviceAddress = $address
                                ? collect([
                                    $address->house_no,
                                    $address->street,
                                    $address->purok,
                                    $address->barangay,
                                    $address->municipality,
                                    $address->province,
                                ])->filter()->implode(', ')
                                : '';
                        @endphp

                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <a href="{{ route('admin.consumers.show', $consumer) }}"
                                   class="block text-sm font-semibold text-slate-900 hover:text-sky-700 truncate">
                                    {{ $consumer->full_name }}
                                </a>
                                <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                    {{ $consumer->email ?: ($consumer->user?->email ?? 'No email') }}
                                </p>
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700 break-words">
                                {{ $consumer->account_number ?: '—' }}
                            </td>

                            <td class="px-4 py-3 text-sm text-slate-700 break-words">
                                {{ $consumer->phone ?: '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <p class="text-xs text-slate-600 break-words">
                                    {{ $serviceAddress ?: 'Address unavailable' }}
                                </p>
                            </td>

                            <td class="px-4 py-3">
                                @if ($consumer->is_active)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-green-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-red-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('admin.consumers.show', $consumer) }}"
                                   title="View consumer"
                                   class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-sky-700 hover:bg-sky-50">
                                    <i class="fa-solid fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-14 text-center">
                                <i class="fa-solid fa-users text-2xl text-slate-300"></i>
                                <p class="text-sm font-semibold text-slate-600 mt-3">
                                    No consumers found
                                </p>
                                <p class="text-xs text-slate-400 mt-1">
                                    Try changing your search or status filter.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile and tablet cards --}}
        <div class="lg:hidden divide-y divide-slate-100">
            @forelse ($consumers as $consumer)
                @php
                    $address = $consumer->address;
                    $serviceAddress = $address
                        ? collect([
                            $address->house_no,
                            $address->street,
                            $address->purok,
                            $address->barangay,
                            $address->municipality,
                            $address->province,
                        ])->filter()->implode(', ')
                        : '';
                @endphp

                <div class="p-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user"></i>
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.consumers.show', $consumer) }}"
                                       class="block text-sm font-semibold text-slate-900 hover:text-sky-700">
                                        {{ $consumer->full_name }}
                                    </a>
                                    <p class="text-[11px] text-slate-500 mt-0.5 break-words">
                                        Account: {{ $consumer->account_number ?: '—' }}
                                    </p>
                                </div>

                                @if ($consumer->is_active)
                                    <span class="text-[10px] font-semibold text-green-700 whitespace-nowrap">Active</span>
                                @else
                                    <span class="text-[10px] font-semibold text-red-600 whitespace-nowrap">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 space-y-2">
                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-phone w-4 mt-0.5 text-xs text-slate-400"></i>
                            <p class="text-xs text-slate-600">{{ $consumer->phone ?: 'No phone number' }}</p>
                        </div>

                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-envelope w-4 mt-0.5 text-xs text-slate-400"></i>
                            <p class="text-xs text-slate-600 break-all">
                                {{ $consumer->email ?: ($consumer->user?->email ?? 'No email') }}
                            </p>
                        </div>

                        <div class="flex items-start gap-2">
                            <i class="fa-solid fa-location-dot w-4 mt-0.5 text-xs text-slate-400"></i>
                            <p class="text-xs text-slate-600">
                                {{ $serviceAddress ?: 'Address unavailable' }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100">
                        <a href="{{ route('admin.consumers.show', $consumer) }}"
                           class="w-full inline-flex items-center justify-center gap-2 h-9 rounded-lg bg-sky-50 text-sky-700 text-xs font-semibold">
                            <i class="fa-solid fa-eye"></i>
                            View Consumer
                        </a>
                    </div>
                </div>
            @empty
                <div class="py-12 text-center">
                    <i class="fa-solid fa-users text-2xl text-slate-300"></i>
                    <p class="text-sm font-semibold text-slate-600 mt-3">No consumers found</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if ($consumers->hasPages())
            <div class="px-4 py-4 border-t border-slate-200">
                {{ $consumers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
