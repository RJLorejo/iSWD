@extends('customer-service.layouts.app')

@section('title', 'Consumer Feedback')

@section('content')

    <div class="space-y-6">

        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">

            <div>

                <p class="text-xs font-bold uppercase tracking-[0.16em] text-sky-600">
                    Consumer Experience
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                    Consumer Feedback
                </h1>

                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                    Review consumer service feedback, monitor follow-up actions,
                    and identify concerns requiring additional attention.
                </p>

            </div>

            <div class="flex flex-wrap gap-2">

                <a
                    href="{{ route('customer-service.feedback.index', ['handling_status' => 'New']) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-700 transition hover:bg-blue-100">

                    <i class="fas fa-inbox"></i>

                    New Feedback

                    @if ($newCount > 0)

                        <span class="rounded-full bg-blue-600 px-2 py-0.5 text-xs font-bold text-white">
                            {{ $newCount }}
                        </span>

                    @endif

                </a>

                <a
                    href="{{ route('customer-service.feedback.index', ['needs_attention' => 1]) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2.5 text-sm font-semibold text-amber-700 transition hover:bg-amber-100">

                    <i class="fas fa-triangle-exclamation"></i>

                    Needs Follow-up

                    @if ($needsAttentionCount > 0)

                        <span class="rounded-full bg-amber-600 px-2 py-0.5 text-xs font-bold text-white">
                            {{ $needsAttentionCount }}
                        </span>

                    @endif

                </a>

            </div>

        </div>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Total Feedback
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ number_format($totalFeedback) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Submitted responses
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                        <i class="fas fa-comments"></i>
                    </div>

                </div>

            </div>

            <div class="rounded-2xl border {{ $newCount > 0 ? 'border-blue-200 bg-blue-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            New Feedback
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ number_format($newCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Awaiting CS review
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <i class="fas fa-inbox"></i>
                    </div>

                </div>

            </div>

            <div class="rounded-2xl border {{ $needsAttentionCount > 0 ? 'border-amber-200 bg-amber-50/50' : 'border-slate-200 bg-white' }} p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Needs Follow-up
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ number_format($needsAttentionCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Requires service attention
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600">
                        <i class="fas fa-triangle-exclamation"></i>
                    </div>

                </div>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Resolved Follow-up
                        </p>

                        <p class="mt-2 text-2xl font-bold text-slate-900">
                            {{ number_format($resolvedCount) }}
                        </p>

                        <p class="mt-1 text-xs text-slate-500">
                            Follow-ups completed
                        </p>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                        <i class="fas fa-circle-check"></i>
                    </div>

                </div>

            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <div class="flex items-start justify-between gap-4">

                    <div>

                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                            Average Rating
                        </p>

                        <div class="mt-2 flex items-end gap-1">

                            <p class="text-2xl font-bold text-slate-900">
                                {{ $averageRating ? number_format($averageRating, 1) : '0.0' }}
                            </p>

                            <span class="mb-1 text-sm font-medium text-slate-400">
                                / 5
                            </span>

                        </div>

                        <div class="mt-1 flex items-center gap-1">

                            @php
                                $roundedAverage = $averageRating
                                    ? (int) round($averageRating)
                                    : 0;
                            @endphp

                            @for ($i = 1; $i <= 5; $i++)

                                <i
                                    class="fas fa-star text-xs {{ $i <= $roundedAverage ? 'text-amber-400' : 'text-slate-200' }}">
                                </i>

                            @endfor

                        </div>

                    </div>

                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                        <i class="fas fa-star"></i>
                    </div>

                </div>

            </div>

        </div>

        <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

            <form
                method="GET"
                action="{{ route('customer-service.feedback.index') }}"
                class="p-5">

                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">

                    <div class="md:col-span-2 xl:col-span-2">

                        <label
                            for="search"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Search
                        </label>

                        <div class="relative">

                            <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>

                            <input
                                id="search"
                                type="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Complaint no., consumer..."
                                class="w-full rounded-xl border-slate-300 py-2.5 pl-9 pr-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        </div>

                    </div>

                    <div>

                        <label
                            for="resolution_status"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Consumer Resolution
                        </label>

                        <select
                            id="resolution_status"
                            name="resolution_status"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                All Resolutions
                            </option>

                            <option
                                value="Resolved"
                                {{ request('resolution_status') === 'Resolved' ? 'selected' : '' }}>
                                Resolved
                            </option>

                            <option
                                value="Partially Resolved"
                                {{ request('resolution_status') === 'Partially Resolved' ? 'selected' : '' }}>
                                Partially Resolved
                            </option>

                            <option
                                value="Not Resolved"
                                {{ request('resolution_status') === 'Not Resolved' ? 'selected' : '' }}>
                                Not Resolved
                            </option>

                        </select>

                    </div>

                    <div>

                        <label
                            for="handling_status"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Handling Status
                        </label>

                        <select
                            id="handling_status"
                            name="handling_status"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                All Statuses
                            </option>

                            @foreach ([
                                'New',
                                'Reviewed',
                                'Follow-up Required',
                                'Follow-up In Progress',
                                'Resolved'
                            ] as $status)

                                <option
                                    value="{{ $status }}"
                                    {{ request('handling_status') === $status ? 'selected' : '' }}>
                                    {{ $status }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div>

                        <label
                            for="rating"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Overall Rating
                        </label>

                        <select
                            id="rating"
                            name="rating"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                All Ratings
                            </option>

                            @for ($rating = 5; $rating >= 1; $rating--)

                                <option
                                    value="{{ $rating }}"
                                    {{ (string) request('rating') === (string) $rating ? 'selected' : '' }}>
                                    {{ $rating }} Star{{ $rating > 1 ? 's' : '' }}
                                </option>

                            @endfor

                        </select>

                    </div>

                    <div>

                        <label
                            for="division_id"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            Division
                        </label>

                        <select
                            id="division_id"
                            name="division_id"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                All Divisions
                            </option>

                            @foreach ($divisions as $division)

                                <option
                                    value="{{ $division->id }}"
                                    {{ (string) request('division_id') === (string) $division->id ? 'selected' : '' }}>
                                    {{ $division->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="mt-4 grid gap-4 border-t border-slate-100 pt-4 sm:grid-cols-2 xl:grid-cols-[1fr_1fr_auto]">

                    <div>

                        <label
                            for="from"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            From Date
                        </label>

                        <input
                            id="from"
                            type="date"
                            name="from"
                            value="{{ request('from') }}"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                    </div>

                    <div>

                        <label
                            for="to"
                            class="mb-1.5 block text-xs font-semibold text-slate-600">
                            To Date
                        </label>

                        <input
                            id="to"
                            type="date"
                            name="to"
                            value="{{ request('to') }}"
                            class="w-full rounded-xl border-slate-300 py-2.5 text-sm focus:border-sky-500 focus:ring-sky-500">

                    </div>

                    <div class="flex items-end gap-2">

                        <button
                            type="submit"
                            class="inline-flex h-[42px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 text-sm font-semibold text-white transition hover:bg-sky-800">

                            <i class="fas fa-filter text-xs"></i>

                            Apply Filters

                        </button>

                        <a
                            href="{{ route('customer-service.feedback.index') }}"
                            class="inline-flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-xl border border-slate-300 text-slate-500 transition hover:bg-slate-50"
                            title="Clear filters">

                            <i class="fas fa-rotate-left text-xs"></i>

                        </a>

                    </div>

                </div>

                @if (request()->boolean('needs_attention'))

                    <input
                        type="hidden"
                        name="needs_attention"
                        value="1">

                    <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">

                        <div class="flex items-center gap-2 text-sm font-medium text-amber-700">

                            <i class="fas fa-triangle-exclamation"></i>

                            Showing feedback currently requiring follow-up.

                        </div>

                        <a
                            href="{{ route('customer-service.feedback.index') }}"
                            class="text-xs font-semibold text-amber-700 hover:text-amber-900">
                            Clear
                        </a>

                    </div>

                @endif

            </form>

        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-3 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <h2 class="font-bold text-slate-900">
                        Feedback Records
                    </h2>

                    <p class="mt-0.5 text-xs text-slate-500">
                        {{ $feedback->total() }}
                        record{{ $feedback->total() !== 1 ? 's' : '' }} found
                    </p>

                </div>

                @if (request()->hasAny([
                    'search',
                    'resolution_status',
                    'handling_status',
                    'rating',
                    'division_id',
                    'from',
                    'to',
                    'needs_attention'
                ]))

                    <a
                        href="{{ route('customer-service.feedback.index') }}"
                        class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 transition hover:text-sky-700">

                        <i class="fas fa-xmark"></i>

                        Clear All Filters

                    </a>

                @endif

            </div>

            @if ($feedback->count())

                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-100">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Complaint
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Consumer
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Service
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Rating
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Consumer Resolution
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Handling
                                </th>

                                <th class="px-5 py-3 text-left text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Submitted
                                </th>

                                <th class="px-5 py-3 text-right text-[11px] font-bold uppercase tracking-wider text-slate-500">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody class="divide-y divide-slate-100">

                            @foreach ($feedback as $item)

                                <tr class="transition hover:bg-slate-50/70">

                                    <td class="whitespace-nowrap px-5 py-4">

                                        <p class="text-sm font-bold text-slate-800">
                                            {{ $item->complaint?->complaint_no ?? '—' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->complaint?->status ?? '—' }}
                                        </p>

                                    </td>

                                    <td class="px-5 py-4">

                                        <p class="text-sm font-semibold text-slate-700">
                                            {{ $item->consumer?->full_name ?? '—' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->consumer?->account_number ?? 'No account number' }}
                                        </p>

                                    </td>

                                    <td class="px-5 py-4">

                                        <p class="text-sm font-medium text-slate-700">
                                            {{ $item->complaint?->category?->name ?? '—' }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->complaint?->division?->name ?? '—' }}
                                        </p>

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        <div class="flex items-center gap-1">

                                            @for ($i = 1; $i <= 5; $i++)

                                                <i
                                                    class="fas fa-star text-xs {{ $i <= $item->overall_rating ? 'text-amber-400' : 'text-slate-200' }}">
                                                </i>

                                            @endfor

                                        </div>

                                        <p class="mt-1 text-xs font-semibold text-slate-500">
                                            {{ $item->overall_rating }}/5
                                        </p>

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        @if ($item->resolution_status === 'Resolved')

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                                                <i class="fas fa-circle-check text-[9px]"></i>

                                                Resolved

                                            </span>

                                        @elseif ($item->resolution_status === 'Partially Resolved')

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">

                                                <i class="fas fa-circle-half-stroke text-[9px]"></i>

                                                Partially Resolved

                                            </span>

                                        @else

                                            <span class="inline-flex items-center gap-1.5 rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">

                                                <i class="fas fa-circle-xmark text-[9px]"></i>

                                                Not Resolved

                                            </span>

                                        @endif

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        @switch($item->handling_status)

                                            @case('New')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">

                                                    <i class="fas fa-circle text-[7px]"></i>

                                                    New

                                                </span>

                                                @break

                                            @case('Reviewed')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">

                                                    <i class="fas fa-eye text-[9px]"></i>

                                                    Reviewed

                                                </span>

                                                @break

                                            @case('Follow-up Required')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">

                                                    <i class="fas fa-triangle-exclamation text-[9px]"></i>

                                                    Follow-up Required

                                                </span>

                                                @break

                                            @case('Follow-up In Progress')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700">

                                                    <i class="fas fa-spinner text-[9px]"></i>

                                                    In Progress

                                                </span>

                                                @break

                                            @case('Resolved')

                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                                                    <i class="fas fa-circle-check text-[9px]"></i>

                                                    Resolved

                                                </span>

                                                @break

                                            @default

                                                <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                                    {{ $item->handling_status ?? 'New' }}
                                                </span>

                                        @endswitch

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4">

                                        <p class="text-sm text-slate-600">
                                            {{ $item->created_at->format('M d, Y') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-400">
                                            {{ $item->created_at->format('h:i A') }}
                                        </p>

                                    </td>

                                    <td class="whitespace-nowrap px-5 py-4 text-right">

                                        <a
                                            href="{{ route('customer-service.feedback.show', $item) }}"
                                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                                            <i class="fas fa-eye"></i>

                                            Review

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="px-6 py-16 text-center">

                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                        <i class="fas fa-comments text-xl"></i>
                    </div>

                    <h3 class="mt-4 font-bold text-slate-800">
                        No feedback found
                    </h3>

                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-slate-500">
                        There are no consumer feedback records matching the selected filters.
                    </p>

                    @if (request()->hasAny([
                        'search',
                        'resolution_status',
                        'handling_status',
                        'rating',
                        'division_id',
                        'from',
                        'to',
                        'needs_attention'
                    ]))

                        <a
                            href="{{ route('customer-service.feedback.index') }}"
                            class="mt-5 inline-flex items-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                            <i class="fas fa-rotate-left"></i>

                            Clear Filters

                        </a>

                    @endif

                </div>

            @endif

            @if ($feedback->hasPages())

                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $feedback->links() }}
                </div>

            @endif

        </div>

    </div>

@endsection
