@extends('customer-service.layouts.app')

@section('title', 'Complaint Types')

@section('content')
@php
    $hasActiveFilters = request()->filled('search') || request()->filled('status');
    $filterCount = (int) request()->filled('search') + (int) request()->filled('status');
@endphp

<style>
    .ct-input{width:100%;min-height:42px;border:1px solid #cbd5e1;border-radius:.75rem;background:#fff;padding:.625rem .75rem;color:#334155;font-size:.875rem}\n    .ct-search-input{padding-left:2.75rem!important;padding-right:.75rem!important}\n    .ct-search-wrap{position:relative;width:100%}\n    .ct-search-icon{position:absolute;left:1rem;top:50%;z-index:1;transform:translateY(-50%);pointer-events:none;color:#94a3b8}
    .ct-input:focus{border-color:#0284c7;outline:none;box-shadow:0 0 0 3px rgb(14 165 233 / 12%)}
    .ct-mobile-list{display:none}
    @media(max-width:767px){.ct-desktop-table{display:none!important}.ct-mobile-list{display:grid!important}}
    @media print{
        @page{size:A4 landscape;margin:12mm}
        body{background:#fff!important;color:#111827!important;font-size:10pt}
        .ct-no-print,nav,aside,header,footer{display:none!important}
        .ct-print-heading{display:block!important;margin-bottom:16px}
        .ct-stats{display:none!important}
        .ct-filter-card{display:none!important}
        .ct-mobile-list{display:none!important}
        .ct-desktop-table{display:block!important;overflow:visible!important}
        .ct-table-wrap{overflow:visible!important;border:0!important;box-shadow:none!important}
        .ct-table{width:100%;border-collapse:collapse!important}
        .ct-table th,.ct-table td{border:1px solid #d1d5db!important;padding:7px!important;color:#111827!important;white-space:normal!important}
        .ct-table thead{display:table-header-group}
        .ct-table tr{break-inside:avoid;page-break-inside:avoid}
        .ct-table th{background:#f3f4f6!important;print-color-adjust:exact;-webkit-print-color-adjust:exact}
        .ct-description{max-width:none!important;overflow:visible!important;white-space:normal!important}
    }
</style>

<div class="mx-auto w-full max-w-[1600px] space-y-6">
    {{-- Page heading and actions --}}
    <section class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="mb-1 text-xs font-bold uppercase tracking-[.16em] text-sky-700">Configuration</p>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">Complaint Types</h1>
            <p class="mt-1 text-sm leading-6 text-slate-500">Manage complaint categories, assigned divisions, and availability.</p>
        </div>
        <div class="ct-no-print flex flex-col gap-2 sm:flex-row">
            <a href="{{ route('customer-service.complaint-categories.print', request()->query()) }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-sky-100">
                <i class="fas fa-print text-slate-500" aria-hidden="true"></i> Print Report
            </a>
            <a href="{{ route('customer-service.complaint-categories.create') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:shadow-md focus:outline-none focus:ring-4 focus:ring-sky-100">
                <i class="fas fa-plus" aria-hidden="true"></i> Add Complaint Type
            </a>
        </div>
    </section>

    <div class="ct-print-heading hidden">
        <h1 class="text-2xl font-bold">Complaint Types</h1>
        <p class="mt-1 text-sm text-gray-600">Sagay Water District · Complaint category listing</p>
        @if($hasActiveFilters)
            <p class="mt-2 text-xs">Filters: @if(request()->filled('search')) Search: {{ request('search') }} @endif @if(request()->filled('status')) Status: {{ request('status') === '1' ? 'Active' : 'Inactive' }} @endif</p>
        @endif
    </div>

    {{-- Summary cards --}}
    <section class="ct-stats grid grid-cols-1 gap-4 sm:grid-cols-3 sm:gap-5">
        <x-admin.stat-card title="Total Complaint Types" :value="$totalCategories" icon="fas fa-list" color="blue" />
        <x-admin.stat-card title="Active" :value="$activeCategories" icon="fas fa-check-circle" color="green" />
        <x-admin.stat-card title="Inactive" :value="$inactiveCategories" icon="fas fa-ban" color="red" />
    </section>

    @if(session('success'))
        <div role="status" class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
            <i class="fas fa-circle-check mt-0.5" aria-hidden="true"></i><p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif
    @if(session('error'))
        <div role="alert" class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-800">
            <i class="fas fa-circle-exclamation mt-0.5" aria-hidden="true"></i><p class="text-sm font-medium">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Search and filters --}}
    <section class="ct-filter-card overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="flex flex-col gap-1 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div>
                <h2 class="font-bold text-slate-900"><i class="fas fa-sliders mr-2 text-sky-700" aria-hidden="true"></i>Filter Complaint Types</h2>
                <p class="mt-1 text-xs text-slate-500">Search by complaint type or narrow the list by status.</p>
            </div>
            @if($hasActiveFilters)
                <span class="mt-2 w-fit rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 sm:mt-0">{{ $filterCount }} active {{ $filterCount === 1 ? 'filter' : 'filters' }}</span>
            @endif
        </div>
        <form method="GET" action="{{ route('customer-service.complaint-categories.index') }}" class="p-4 sm:p-5">
            <div class="grid grid-cols-1 items-end gap-3 sm:grid-cols-2 lg:grid-cols-12 lg:gap-4">
                <div class="sm:col-span-2 lg:col-span-6">
                    <label for="ct-search" class="mb-1.5 block text-xs font-semibold text-slate-700">Search</label>
                    <div class="ct-search-wrap">
                        <i class="ct-search-icon fas fa-magnifying-glass text-xs" aria-hidden="true"></i>
                        <input id="ct-search" type="search" name="search" value="{{ request('search') }}" placeholder="Search complaint type or description..." class="ct-input ct-search-input" autocomplete="off">
                    </div>
                </div>
                <div class="lg:col-span-3">
                    <label for="ct-status" class="mb-1.5 block text-xs font-semibold text-slate-700">Status</label>
                    <select id="ct-status" name="status" class="ct-input">
                        <option value="">All statuses</option>
                        <option value="1" @selected(request('status') === '1')>Active</option>
                        <option value="0" @selected(request('status') === '0')>Inactive</option>
                    </select>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row lg:col-span-3">
                    <button type="submit" class="inline-flex min-h-[42px] flex-1 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 focus:outline-none focus:ring-4 focus:ring-sky-100">
                        <i class="fas fa-search" aria-hidden="true"></i> Apply
                    </button>
                    @if($hasActiveFilters)
                        <a href="{{ route('customer-service.complaint-categories.index') }}" class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50" aria-label="Reset filters" title="Reset filters">
                            <i class="fas fa-rotate-left" aria-hidden="true"></i><span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </section>

    {{-- Desktop and tablet table --}}
    <section class="ct-table-wrap overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
        <div class="ct-no-print flex flex-col gap-1 border-b border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div><h2 class="font-bold text-slate-900">Complaint Type Directory</h2><p class="mt-1 text-xs text-slate-500">Review assigned divisions, descriptions, and current status.</p></div>
            <p class="text-xs text-slate-500">{{ $categories->total() }} {{ $categories->total() === 1 ? 'record' : 'records' }}</p>
        </div>
        <div class="ct-desktop-table overflow-x-auto">
            <table class="ct-table min-w-full divide-y divide-slate-200">
                <thead class="bg-slate-50">
                    <tr>
                        <th scope="col" class="whitespace-nowrap px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-5">Division</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-5">Complaint Type</th>
                        <th scope="col" class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-5">Description</th>
                        <th scope="col" class="whitespace-nowrap px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-5">Status</th>
                        <th scope="col" class="ct-no-print whitespace-nowrap px-4 py-3 text-center text-[11px] font-bold uppercase tracking-wider text-slate-500 sm:px-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($categories as $category)
                        <tr class="transition hover:bg-slate-50/80">
                            <td class="px-4 py-4 sm:px-5">
                                @if($category->division)
                                    <span class="inline-flex rounded-lg bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">{{ $category->division->name }}</span>
                                @else
                                    <span class="text-sm text-slate-400">No division assigned</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 sm:px-5"><div class="font-semibold text-slate-900">{{ $category->name }}</div></td>
                            <td class="px-4 py-4 sm:px-5"><div class="ct-description max-w-md truncate text-sm text-slate-500">{{ $category->description ?: 'No description provided.' }}</div></td>
                            <td class="whitespace-nowrap px-4 py-4 text-center sm:px-5">
                                @if($category->is_active)
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700"><i class="fas fa-circle text-[6px]" aria-hidden="true"></i>Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700"><i class="fas fa-circle text-[6px]" aria-hidden="true"></i>Inactive</span>
                                @endif
                            </td>
                            <td class="ct-no-print whitespace-nowrap px-4 py-4 sm:px-5">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('customer-service.complaint-categories.edit', $category) }}" title="Edit complaint type" aria-label="Edit {{ $category->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-amber-50 text-amber-700 transition hover:bg-amber-100 focus:outline-none focus:ring-4 focus:ring-amber-100"><i class="fas fa-pen" aria-hidden="true"></i></a>
                                    <form action="{{ route('customer-service.complaint-categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this complaint type?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" title="Delete complaint type" aria-label="Delete {{ $category->name }}" class="inline-flex h-9 w-9 items-center justify-center rounded-lg bg-rose-50 text-rose-700 transition hover:bg-rose-100 focus:outline-none focus:ring-4 focus:ring-rose-100"><i class="fas fa-trash" aria-hidden="true"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-6 py-14 text-center"><div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"><i class="fas fa-list text-lg" aria-hidden="true"></i></div><h3 class="mt-3 font-semibold text-slate-900">No complaint types found</h3><p class="mt-1 text-sm text-slate-500">Try changing your search or status filter.</p>@if($hasActiveFilters)<a href="{{ route('customer-service.complaint-categories.index') }}" class="ct-no-print mt-4 inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">Clear filters <i class="fas fa-arrow-right text-xs" aria-hidden="true"></i></a>@endif</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile cards --}}
        <div class="ct-mobile-list gap-3 p-3">
            @forelse($categories as $category)
                <article class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <h3 class="break-words font-bold text-slate-900">{{ $category->name }}</h3>
                        </div>
                        @if($category->is_active)
                            <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-700"><i class="fas fa-circle text-[6px]" aria-hidden="true"></i>Active</span>
                        @else
                            <span class="shrink-0 inline-flex items-center gap-1.5 rounded-full bg-rose-50 px-2.5 py-1 text-[11px] font-semibold text-rose-700"><i class="fas fa-circle text-[6px]" aria-hidden="true"></i>Inactive</span>
                        @endif
                    </div>
                    <dl class="mt-4 space-y-3 border-t border-slate-100 pt-3">
                        <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Division</dt><dd class="mt-1 text-sm text-slate-700">{{ $category->division?->name ?? 'No division assigned' }}</dd></div>
                        <div><dt class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Description</dt><dd class="mt-1 break-words text-sm leading-5 text-slate-600">{{ $category->description ?: 'No description provided.' }}</dd></div>
                    </dl>
                    <div class="ct-no-print mt-4 flex gap-2 border-t border-slate-100 pt-3">
                        <a href="{{ route('customer-service.complaint-categories.edit', $category) }}" class="inline-flex min-h-[38px] flex-1 items-center justify-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-800 transition hover:bg-amber-100"><i class="fas fa-pen" aria-hidden="true"></i>Edit</a>
                        <form action="{{ route('customer-service.complaint-categories.destroy', $category) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this complaint type?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex min-h-[38px] w-full items-center justify-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-800 transition hover:bg-rose-100"><i class="fas fa-trash" aria-hidden="true"></i>Delete</button>
                        </form>
                    </div>
                </article>
            @empty
                <div class="px-4 py-10 text-center"><i class="fas fa-list text-2xl text-slate-300" aria-hidden="true"></i><h3 class="mt-3 font-semibold text-slate-900">No complaint types found</h3><p class="mt-1 text-sm text-slate-500">Try changing your search or status filter.</p></div>
            @endforelse
        </div>

        @if($categories->hasPages())
            <div class="ct-no-print flex flex-col gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="text-xs text-slate-500">Showing {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }}</p>
                <div class="min-w-0 overflow-x-auto">{{ $categories->withQueryString()->onEachSide(1)->links() }}</div>
            </div>
        @endif
    </section>
</div>
@endsection
