@extends('customer-service.layouts.app')

@section('title', 'Divisions')

@section('content')
<div class="mx-auto max-w-7xl space-y-6 px-0 sm:px-1">
    {{-- Page heading --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <p class="text-sm font-medium text-sky-700">Complaint Management</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 sm:text-3xl">Divisions</h1>
            <p class="mt-1 text-sm text-gray-500">Manage divisions used to classify and route consumer complaints.</p>
        </div>
        <a href="{{ route('customer-service.divisions.create') }}"
           class="inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2 sm:w-auto">
            <i class="fas fa-plus" aria-hidden="true"></i> Add Division
        </a>
    </div>

    {{-- Flash messages --}}
    @if (session('success'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800">
            <i class="fas fa-circle-check mt-0.5" aria-hidden="true"></i><p>{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div role="alert" class="flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800">
            <i class="fas fa-circle-exclamation mt-0.5" aria-hidden="true"></i><p>{{ session('error') }}</p>
        </div>
    @endif

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-gray-500">Total Divisions</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $totalDivisions }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><i class="fas fa-building" aria-hidden="true"></i></span></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-gray-500">Active</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $activeDivisions }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-green-50 text-green-700"><i class="fas fa-circle-check" aria-hidden="true"></i></span></div>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-center justify-between gap-3"><div><p class="text-sm text-gray-500">Inactive</p><p class="mt-1 text-2xl font-bold text-gray-900">{{ $inactiveDivisions }}</p></div><span class="flex h-11 w-11 items-center justify-center rounded-xl bg-gray-100 text-gray-600"><i class="fas fa-circle-minus" aria-hidden="true"></i></span></div>
        </div>
    </div>

    {{-- Search and filters --}}
    <section class="rounded-2xl border border-gray-200 bg-white p-4 shadow-sm sm:p-5">
        <form method="GET" action="{{ route('customer-service.divisions.index') }}" class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">
            <div class="md:col-span-7">
                <label for="search" class="mb-1.5 block text-sm font-medium text-gray-700">Search divisions</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex w-11 items-center justify-center text-gray-400"><i class="fas fa-magnifying-glass" aria-hidden="true"></i></span>
                    <input type="search" id="search" name="search" value="{{ request('search') }}" placeholder="Search by name or description..."
                           class="block min-h-11 w-full rounded-xl border border-gray-300 bg-white py-2.5 pl-11 pr-4 text-sm text-gray-900 placeholder:text-gray-400 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                </div>
            </div>
            <div class="md:col-span-3">
                <label for="status" class="mb-1.5 block text-sm font-medium text-gray-700">Status</label>
                <select id="status" name="status" class="block min-h-11 w-full rounded-xl border border-gray-300 bg-white px-3 py-2.5 text-sm text-gray-900 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-100">
                    <option value="">All statuses</option>
                    <option value="1" @selected(request('status') === '1')>Active</option>
                    <option value="0" @selected(request('status') === '0')>Inactive</option>
                </select>
            </div>
            <div class="flex flex-col gap-2 sm:flex-row md:col-span-2">
                <button type="submit" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2"><i class="fas fa-filter" aria-hidden="true"></i><span>Filter</span></button>
                @if (request()->filled('search') || request()->filled('status'))
                    <a href="{{ route('customer-service.divisions.index') }}" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 transition hover:bg-gray-50"><i class="fas fa-rotate-left" aria-hidden="true"></i><span>Reset</span></a>
                @endif
            </div>
        </form>
    </section>

    {{-- Records --}}
    <section class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div><h2 class="font-semibold text-gray-900">Division Records</h2><p class="mt-1 text-sm text-gray-500">Divisions available for complaint classification.</p></div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $divisions->total() }} records</span>
            </div>
        </div>

        {{-- Mobile cards --}}
        <div class="divide-y divide-gray-100 lg:hidden">
            @forelse ($divisions as $division)
                <article class="p-4 sm:p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700"><i class="fas fa-building" aria-hidden="true"></i></span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <h3 class="break-words font-semibold text-gray-900">{{ $division->name }}</h3>
                                @if ($division->is_active)
                                    <span class="rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">Active</span>
                                @else
                                    <span class="rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600">Inactive</span>
                                @endif
                            </div>
                            <p class="mt-1 text-sm leading-5 text-gray-500">{{ $division->description ?: 'No description provided.' }}</p>
                        </div>
                    </div>
                    <div class="mt-4 grid grid-cols-2 gap-2">
                        <div class="rounded-lg bg-gray-50 px-3 py-2"><p class="text-xs text-gray-500">Complaint types</p><p class="mt-0.5 text-sm font-semibold text-gray-900">{{ $division->complaint_types_count }}</p></div>
                        <div class="rounded-lg bg-gray-50 px-3 py-2"><p class="text-xs text-gray-500">Complaints</p><p class="mt-0.5 text-sm font-semibold text-gray-900">{{ $division->complaints_count }}</p></div>
                    </div>
                    <div class="mt-4 flex gap-2 border-t border-gray-100 pt-3">
                        <a href="{{ route('customer-service.divisions.edit', $division) }}" class="inline-flex min-h-10 flex-1 items-center justify-center gap-2 rounded-lg bg-sky-50 px-3 py-2 text-sm font-semibold text-sky-700 transition hover:bg-sky-100"><i class="fas fa-pen-to-square" aria-hidden="true"></i> Edit</a>
                        <form action="{{ route('customer-service.divisions.destroy', $division) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this division?');" class="flex-1">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg border border-red-200 px-3 py-2 text-sm font-semibold text-red-600 transition hover:bg-red-50"><i class="fas fa-trash" aria-hidden="true"></i> Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-5 py-14 text-center"><span class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 text-gray-400"><i class="fas fa-building text-lg" aria-hidden="true"></i></span><h3 class="mt-3 font-semibold text-gray-900">No divisions found</h3><p class="mt-1 text-sm text-gray-500">Try changing your search or status filter.</p></div>
            @endforelse
        </div>

        {{-- Desktop table --}}
        <div class="hidden overflow-x-auto lg:block">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50"><tr>
                    <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Division</th>
                    <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Description</th>
                    <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Complaint types</th>
                    <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Complaints</th>
                    <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                    <th scope="col" class="px-5 py-3.5 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($divisions as $division)
                        <tr class="transition hover:bg-gray-50/70">
                            <td class="whitespace-nowrap px-5 py-4"><div class="flex items-center gap-3"><span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-sky-50 text-sky-700"><i class="fas fa-building" aria-hidden="true"></i></span><span class="font-semibold text-gray-900">{{ $division->name }}</span></div></td>
                            <td class="max-w-sm px-5 py-4 text-sm leading-5 text-gray-600"><p class="line-clamp-2">{{ $division->description ?: 'No description provided.' }}</p></td>
                            <td class="px-5 py-4 text-center"><span class="inline-flex min-w-9 justify-center rounded-full bg-sky-50 px-2.5 py-1 text-sm font-semibold text-sky-700">{{ $division->complaint_types_count }}</span></td>
                            <td class="px-5 py-4 text-center"><span class="inline-flex min-w-9 justify-center rounded-full bg-gray-100 px-2.5 py-1 text-sm font-semibold text-gray-700">{{ $division->complaints_count }}</span></td>
                            <td class="px-5 py-4 text-center">@if ($division->is_active)<span class="inline-flex items-center gap-1.5 rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700"><i class="fas fa-circle-check" aria-hidden="true"></i> Active</span>@else<span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1 text-xs font-semibold text-gray-600"><i class="fas fa-circle-minus" aria-hidden="true"></i> Inactive</span>@endif</td>
                            <td class="px-5 py-4"><div class="flex items-center justify-center gap-2">
                                <a href="{{ route('customer-service.divisions.edit', $division) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-sky-50 text-sky-700 transition hover:bg-sky-100" title="Edit division" aria-label="Edit {{ $division->name }}"><i class="fas fa-pen-to-square" aria-hidden="true"></i></a>
                                <form action="{{ route('customer-service.divisions.destroy', $division) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this division?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-red-50 text-red-600 transition hover:bg-red-100" title="Delete division" aria-label="Delete {{ $division->name }}"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                </form>
                            </div></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-14 text-center"><i class="fas fa-building text-2xl text-gray-300" aria-hidden="true"></i><h3 class="mt-3 font-semibold text-gray-900">No divisions found</h3><p class="mt-1 text-sm text-gray-500">Try changing your search or status filter.</p></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($divisions->hasPages())
            <div class="border-t border-gray-100 px-4 py-4 sm:px-6">{{ $divisions->withQueryString()->links() }}</div>
        @endif
    </section>
</div>

<style>
@media print {
    body * { visibility: hidden !important; }
    .max-w-7xl, .max-w-7xl * { visibility: visible !important; }
    .no-print, button, form, a[href*="/edit"], a[href*="/create"] { display: none !important; }
    .max-w-7xl { max-width: 100% !important; margin: 0 !important; }
    .max-w-7xl > section:last-of-type { border: 0 !important; box-shadow: none !important; }
    .max-w-7xl > section:last-of-type > div.lg\:hidden { display: none !important; }
    .max-w-7xl .hidden.lg\:block { display: block !important; }
    .overflow-x-auto { overflow: visible !important; }
    table { width: 100% !important; border-collapse: collapse !important; }
    th, td { border: 1px solid #d1d5db !important; padding: 8px !important; color: #111827 !important; }
    thead { display: table-header-group; }
    tr { break-inside: avoid; }
}
</style>
@endsection
