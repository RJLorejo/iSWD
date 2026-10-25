
@extends('customer-service.layouts.app')

@section('title', 'Consumer Feedback')

@section('content')
@php
    $hasActiveFilters =
        request()->filled('search') ||
        request()->filled('resolution_status') ||
        request()->filled('rating') ||
        request()->filled('from') ||
        request()->filled('to');

    $filterCount = collect([
        request()->filled('search'),
        request()->filled('resolution_status'),
        request()->filled('rating'),
        request()->filled('from'),
        request()->filled('to'),
    ])->filter()->count();

    $resolutionStyles = [
        'Resolved' => 'bg-emerald-50 text-emerald-700',
        'Partially Resolved' => 'bg-amber-50 text-amber-700',
        'Not Resolved' => 'bg-rose-50 text-rose-700',
    ];
@endphp

<style>
    .feedback-input {
        width: 100%;
        min-height: 42px;
        border: 1px solid #cbd5e1;
        border-radius: .75rem;
        background: #fff;
        padding: .625rem .75rem;
        color: #334155;
        font-size: .875rem;
    }

    .feedback-input:focus {
        border-color: #0284c7;
        outline: none;
        box-shadow: 0 0 0 3px rgb(14 165 233 / 12%);
    }

    .feedback-input.search-input {
        padding-left: 2.65rem !important;
        padding-right: .75rem !important;
        appearance: none;
        -webkit-appearance: none;
    }

    .feedback-input.search-input::-webkit-search-decoration,
    .feedback-input.search-input::-webkit-search-cancel-button,
    .feedback-input.search-input::-webkit-search-results-button,
    .feedback-input.search-input::-webkit-search-results-decoration {
        -webkit-appearance: none;
    }

    .feedback-filter-grid {
        display: grid;
        grid-template-columns:
            minmax(0, 2.5fr)
            minmax(130px, 1fr)
            minmax(170px, 1.35fr);
        gap: 12px;
        align-items: end;
    }

    .feedback-filter-row-two {
        grid-column: 1 / -1;
        display: grid;
        grid-template-columns:
            minmax(130px, 170px)
            minmax(130px, 170px)
            minmax(0, 1fr);
        gap: 12px;
        align-items: end;
    }

    .feedback-filter-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        flex-wrap: wrap;
    }

    .feedback-filter-actions > * {
        min-width: 125px;
    }

    .feedback-mobile-card {
        display: none;
    }

    @media (max-width: 767px) {
        .feedback-filter-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .feedback-search-field {
            grid-column: 1 / -1;
        }

        .feedback-filter-row-two {
            grid-column: 1 / -1;
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .feedback-filter-actions {
            grid-column: 1 / -1;
            justify-content: stretch;
        }

        .feedback-filter-actions > * {
            flex: 1;
            min-width: 0;
        }

        .feedback-filter-grid .feedback-field {
            min-width: 0;
        }

        .feedback-desktop-table {
            display: none !important;
        }

        .feedback-mobile-card {
            display: block !important;
        }
    }

    @media print {
        @page {
            size: A4 landscape;
            margin: 10mm;
        }

        .no-print,
        nav,
        aside,
        header,
        footer {
            display: none !important;
        }

        .feedback-mobile-card {
            display: none !important;
        }

        .feedback-desktop-table {
            display: block !important;
            overflow: visible !important;
        }

        .feedback-desktop-table table {
            min-width: 0 !important;
        }

        body {
            background: white !important;
        }
    }
</style>

<div class="mx-auto w-full max-w-[1600px] space-y-6">

    {{-- Page heading --}}
    <section class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
            <p class="mb-2 text-xs font-bold uppercase tracking-[.16em] text-sky-700">
                Consumer Experience
            </p>

            <h1 class="text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                Consumer Feedback
            </h1>

            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                Review consumer satisfaction, read service comments, and respond to feedback submitted after complaint closure.
            </p>
        </div>

        <div class="no-print flex flex-col gap-2 sm:flex-row sm:flex-wrap">
            <a
                href="{{ route('customer-service.feedback.index', ['handling_status' => 'New']) }}"
                class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 hover:bg-blue-100"
            >
                <i class="fas fa-inbox"></i>
                New Feedback

                @if(($newCount ?? 0) > 0)
                    <span class="rounded-full bg-blue-600 px-2 py-0.5 text-xs text-white">
                        {{ number_format($newCount) }}
                    </span>
                @endif
            </a>

            <a
                href="{{ route('customer-service.feedback.index', ['handling_status' => 'Reviewed']) }}"
                class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-2.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-100"
            >
                <i class="fas fa-clipboard-check"></i>
                Reviewed Feedback

                @if(($reviewedCount ?? 0) > 0)
                    <span class="rounded-full bg-emerald-600 px-2 py-0.5 text-xs text-white">
                        {{ number_format($reviewedCount) }}
                    </span>
                @endif
            </a>
        </div>
    </section>

    {{-- Summary cards --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">

        @foreach ([
            [
                'Total Feedback',
                $totalFeedback ?? 0,
                'All submitted responses',
                'fa-comments',
                'bg-sky-50 text-sky-700'
            ],
            [
                'New Feedback',
                $newCount ?? 0,
                'Awaiting review',
                'fa-inbox',
                'bg-blue-50 text-blue-700'
            ],
            [
                'Reviewed Feedback',
                $reviewedCount ?? 0,
                'Reviewed or previously processed',
                'fa-clipboard-check',
                'bg-emerald-50 text-emerald-700'
            ],
        ] as [$label, $value, $description, $icon, $iconStyle])

            <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                            {{ $label }}
                        </p>

                        <p class="mt-3 text-2xl font-bold tabular-nums text-slate-900">
                            {{ number_format($value) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            {{ $description }}
                        </p>
                    </div>

                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $iconStyle }}">
                        <i class="fas {{ $icon }}"></i>
                    </span>
                </div>
            </article>

        @endforeach

        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Average Rating
                    </p>

                    <p class="mt-3 text-2xl font-bold text-slate-900">
                        {{ number_format((float) ($averageRating ?? 0), 1) }}
                        <span class="text-sm text-slate-400">/ 5</span>
                    </p>

                    <div class="mt-2 flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-xs {{ $i <= (int) round((float) ($averageRating ?? 0)) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                </div>

                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                    <i class="fas fa-star"></i>
                </span>
            </div>
        </article>

    </section>

    {{-- Filters --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-1 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-900">
                    <i class="fas fa-sliders mr-2 text-sky-700"></i>
                    Filter Feedback
                </h2>

                <p class="mt-1 text-xs text-slate-500">
                    Search by complaint number, consumer, service, rating, resolution, or submission date.
                </p>
            </div>

            @if($hasActiveFilters)
                <span class="w-fit rounded-full bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700">
                    {{ $filterCount }} active {{ $filterCount === 1 ? 'filter' : 'filters' }}
                </span>
            @endif
        </div>

        <form
            method="GET"
            action="{{ route('customer-service.feedback.index') }}"
            class="p-4 sm:p-5"
        >
            <div class="feedback-filter-grid">

                {{-- Search --}}
                <div class="feedback-search-field feedback-field">
                    <label for="search" class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Search
                    </label>

                    <div class="relative">
                        <i
                            aria-hidden="true"
                            class="fas fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"
                        ></i>

                        <input
                            id="search"
                            type="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Complaint no., consumer, service..."
                            class="feedback-input search-input"
                            autocomplete="off"
                        >
                    </div>
                </div>

                {{-- Rating --}}
                <div class="feedback-field">
                    <label for="rating" class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Rating
                    </label>

                    <select id="rating" name="rating" class="feedback-input">
                        <option value="">All ratings</option>

                        @for($rating = 5; $rating >= 1; $rating--)
                            <option
                                value="{{ $rating }}"
                                @selected((string) request('rating') === (string) $rating)
                            >
                                {{ $rating }} {{ $rating === 1 ? 'Star' : 'Stars' }}
                            </option>
                        @endfor
                    </select>
                </div>

                {{-- Consumer resolution --}}
                <div class="feedback-field">
                    <label for="resolution_status" class="mb-1.5 block text-xs font-semibold text-slate-700">
                        Consumer Resolution
                    </label>

                    <select id="resolution_status" name="resolution_status" class="feedback-input">
                        <option value="">All resolutions</option>

                        @foreach(['Resolved', 'Partially Resolved', 'Not Resolved'] as $status)
                            <option
                                value="{{ $status }}"
                                @selected(request('resolution_status') === $status)
                            >
                                {{ $status }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Dates and actions --}}
                <div class="feedback-filter-row-two">

                    <div class="feedback-field">
                        <label for="from" class="mb-1.5 block text-xs font-semibold text-slate-700">
                            From date
                        </label>

                        <input
                            id="from"
                            type="date"
                            name="from"
                            value="{{ request('from') }}"
                            max="{{ request('to') ?: '' }}"
                            class="feedback-input"
                        >
                    </div>

                    <div class="feedback-field">
                        <label for="to" class="mb-1.5 block text-xs font-semibold text-slate-700">
                            To date
                        </label>

                        <input
                            id="to"
                            type="date"
                            name="to"
                            value="{{ request('to') }}"
                            min="{{ request('from') ?: '' }}"
                            class="feedback-input"
                        >
                    </div>

                    <div class="feedback-filter-actions">

                        <button
                            type="submit"
                            class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800"
                        >
                            <i class="fas fa-filter"></i>
                            Apply Filters
                        </button>

                        @if($hasActiveFilters)
                            <a
                                href="{{ route('customer-service.feedback.index') }}"
                                class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"
                            >
                                <i class="fas fa-rotate-left"></i>
                                Reset
                            </a>
                        @endif

                        <a
                            href="{{ route('customer-service.feedback.print', request()->query()) }}"
                            target="_blank"
                            rel="noopener"
                            class="inline-flex min-h-[42px] items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                        >
                            <i class="fas fa-print"></i>
                            Print Report
                        </a>

                    </div>
                </div>
            </div>
        </form>
    </section>

    {{-- Feedback records --}}
    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-bold text-slate-900">
                    Feedback Records
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    {{ number_format($feedback->total()) }} records found

                    @if($feedback->total() > 0)
                        · Showing {{ $feedback->firstItem() }}–{{ $feedback->lastItem() }}
                    @endif
                </p>
            </div>

            @if($hasActiveFilters)
                <a
                    href="{{ route('customer-service.feedback.index') }}"
                    class="no-print text-xs font-semibold text-slate-500 hover:text-sky-700"
                >
                    <i class="fas fa-xmark mr-1"></i>
                    Clear filters
                </a>
            @endif
        </div>

        @if($feedback->count())

            {{-- Desktop table --}}
            <div class="feedback-desktop-table w-full overflow-x-auto">
                <table class="w-full min-w-[850px] divide-y divide-slate-100">

                    <thead class="bg-slate-50">
                        <tr>
                            @foreach([
                                'Complaint',
                                'Consumer',
                                'Service / Division',
                                'Rating',
                                'Consumer Resolution',
                                'Submitted'
                            ] as $heading)
                                <th
                                    scope="col"
                                    class="px-4 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500"
                                >
                                    {{ $heading }}
                                </th>
                            @endforeach

                            <th class="no-print px-4 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @foreach($feedback as $item)
                            @php
                                $resolution = $item->resolution_status ?? 'Not Resolved';
                            @endphp

                            <tr class="hover:bg-slate-50/70">

                                {{-- Complaint --}}
                                <td class="px-4 py-4 align-top">
                                    <p class="text-sm font-bold text-slate-800">
                                        {{ $item->complaint?->complaint_no ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->complaint?->status ?? '—' }}
                                    </p>
                                </td>

                                {{-- Consumer --}}
                                <td class="px-4 py-4 align-top">
                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $item->consumer?->full_name ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->consumer?->account_number ?? 'No account number' }}
                                    </p>
                                </td>

                                {{-- Service / Division --}}
                                <td class="px-4 py-4 align-top">
                                    <p class="text-sm text-slate-700">
                                        {{ $item->complaint?->category?->name ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->complaint?->division?->name ?? 'No division' }}
                                    </p>
                                </td>

                                {{-- Rating --}}
                                <td class="whitespace-nowrap px-4 py-4 align-top">
                                    <div class="flex gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-[10px] {{ $i <= (int) ($item->overall_rating ?? 0) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                        @endfor
                                    </div>

                                    <p class="mt-1 text-xs font-semibold text-slate-600">
                                        {{ $item->overall_rating ?? 0 }}/5
                                    </p>
                                </td>

                                {{-- Consumer resolution --}}
                                <td class="px-4 py-4 align-top">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $resolutionStyles[$resolution] ?? 'bg-slate-100 text-slate-700' }}">
                                        {{ $resolution }}
                                    </span>
                                </td>

                                {{-- Submitted --}}
                                <td class="whitespace-nowrap px-4 py-4 align-top">
                                    <p class="text-sm text-slate-700">
                                        {{ $item->created_at?->format('M d, Y') ?? '—' }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $item->created_at?->format('h:i A') ?? '' }}
                                    </p>
                                </td>

                                {{-- Action --}}
                                <td class="no-print whitespace-nowrap px-4 py-4 text-right align-top">
                                    <a
                                        href="{{ route('customer-service.feedback.show', $item) }}"
                                        class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 hover:border-sky-200 hover:bg-sky-50"
                                    >
                                        <i class="fas fa-eye"></i>
                                        Review
                                    </a>
                                </td>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile cards --}}
            <div class="feedback-mobile-card divide-y divide-slate-100 md:hidden">
                @foreach($feedback as $item)
                    @php
                        $resolution = $item->resolution_status ?? 'Not Resolved';
                    @endphp

                    <article class="p-4 sm:p-5">

                        {{-- Complaint and submission date --}}
                        <div class="min-w-0">
                            <p class="break-words text-sm font-bold text-slate-900">
                                {{ $item->complaint?->complaint_no ?? 'No complaint number' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->created_at?->format('M d, Y · h:i A') ?? '—' }}
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Complaint status: {{ $item->complaint?->status ?? '—' }}
                            </p>
                        </div>

                        <div class="mt-4 space-y-3">

                            {{-- Consumer --}}
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Consumer
                                </p>

                                <p class="mt-1 break-words text-sm font-medium text-slate-800">
                                    {{ $item->consumer?->full_name ?? '—' }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    {{ $item->consumer?->account_number ?? 'No account number' }}
                                </p>
                            </div>

                            {{-- Service and rating --}}
                            <div class="grid grid-cols-2 gap-3">

                                <div class="min-w-0">
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Service / Division
                                    </p>

                                    <p class="mt-1 break-words text-sm text-slate-700">
                                        {{ $item->complaint?->category?->name ?? '—' }}
                                    </p>

                                    <p class="mt-0.5 break-words text-xs text-slate-500">
                                        {{ $item->complaint?->division?->name ?? 'No division' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                        Rating
                                    </p>

                                    <div class="mt-1 flex flex-wrap items-center gap-0.5">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star text-[10px] {{ $i <= (int) ($item->overall_rating ?? 0) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                                        @endfor

                                        <span class="ml-1 text-xs font-semibold text-slate-600">
                                            {{ $item->overall_rating ?? 0 }}/5
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Consumer resolution --}}
                            <div>
                                <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">
                                    Consumer Resolution
                                </p>

                                <span class="mt-1 inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $resolutionStyles[$resolution] ?? 'bg-slate-100 text-slate-700' }}">
                                    {{ $resolution }}
                                </span>
                            </div>

                        </div>

                        {{-- Review action --}}
                        <div class="no-print mt-4 border-t border-slate-100 pt-3">
                            <a
                                href="{{ route('customer-service.feedback.show', $item) }}"
                                class="inline-flex min-h-[38px] w-full items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-sky-50"
                            >
                                <i class="fas fa-eye"></i>
                                Review Feedback
                            </a>
                        </div>

                    </article>
                @endforeach
            </div>

        @else

            {{-- Empty state --}}
            <div class="px-5 py-16 text-center">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                    <i class="fas fa-comments text-xl"></i>
                </div>

                <h3 class="mt-4 font-bold text-slate-900">
                    No feedback found
                </h3>

                <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                    No records match these filters. Try another keyword or reset your filters.
                </p>

                @if($hasActiveFilters)
                    <a
                        href="{{ route('customer-service.feedback.index') }}"
                        class="no-print mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700"
                    >
                        <i class="fas fa-rotate-left"></i>
                        Reset Filters
                    </a>
                @endif

            </div>

        @endif

        {{-- Pagination --}}
        @if($feedback->hasPages())
            <div class="no-print flex flex-col gap-3 border-t border-slate-100 px-4 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-5">

                <p class="text-xs text-slate-500">
                    Page {{ $feedback->currentPage() }} of {{ $feedback->lastPage() }}
                </p>

                <div class="min-w-0 overflow-x-auto">
                    {{ $feedback->withQueryString()->onEachSide(1)->links() }}
                </div>

            </div>
        @endif

    </section>
</div>
@endsection
