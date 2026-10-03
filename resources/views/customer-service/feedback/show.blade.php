@extends('customer-service.layouts.app')

@section('title', 'Feedback Details')

@section('content')

    @php
        $complaint = $feedback->complaint;
        $consumer = $feedback->consumer;

        $averageRating = (
            $feedback->overall_rating +
            $feedback->service_quality_rating +
            $feedback->response_time_rating +
            $feedback->personnel_courtesy_rating
        ) / 4;

        $consumerNeedsAttention = in_array(
            $feedback->resolution_status,
            ['Partially Resolved', 'Not Resolved'],
            true
        );

        $handlingStatus = $feedback->handling_status ?? 'New';
    @endphp

    <div class="mx-auto max-w-6xl space-y-6">

        @if (session('success'))

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-check mt-0.5 text-emerald-600"></i>

                    <p class="text-sm font-medium text-emerald-800">
                        {{ session('success') }}
                    </p>

                </div>

            </div>

        @endif

        @if (session('warning'))

            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-triangle-exclamation mt-0.5 text-amber-600"></i>

                    <p class="text-sm font-medium text-amber-800">
                        {{ session('warning') }}
                    </p>

                </div>

            </div>

        @endif

        @if ($errors->any())

            <div class="rounded-2xl border border-red-200 bg-red-50 px-5 py-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation mt-0.5 text-red-600"></i>

                    <div>

                        <p class="text-sm font-bold text-red-800">
                            Please correct the following:
                        </p>

                        <ul class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

            <div>

                <a
                    href="{{ route('customer-service.feedback.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-sky-700">

                    <i class="fas fa-arrow-left text-xs"></i>

                    Consumer Feedback

                </a>

                <div class="mt-3 flex flex-wrap items-center gap-3">

                    <h1 class="text-2xl font-bold tracking-tight text-slate-900">
                        {{ $complaint?->complaint_no ?? 'Feedback Details' }}
                    </h1>

                    @switch($handlingStatus)

                        @case('New')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-50 px-3 py-1 text-xs font-semibold text-blue-700">

                                <i class="fas fa-circle text-[7px]"></i>

                                New Feedback

                            </span>

                            @break

                        @case('Reviewed')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-700">

                                <i class="fas fa-eye text-[10px]"></i>

                                Reviewed

                            </span>

                            @break

                        @case('Follow-up Required')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">

                                <i class="fas fa-triangle-exclamation text-[10px]"></i>

                                Follow-up Required

                            </span>

                            @break

                        @case('Follow-up In Progress')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-violet-50 px-3 py-1 text-xs font-semibold text-violet-700">

                                <i class="fas fa-spinner text-[10px]"></i>

                                Follow-up In Progress

                            </span>

                            @break

                        @case('Resolved')

                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">

                                <i class="fas fa-circle-check text-[10px]"></i>

                                Follow-up Resolved

                            </span>

                            @break

                    @endswitch

                </div>

                <p class="mt-2 text-sm text-slate-500">
                    Consumer feedback submitted
                    {{ $feedback->created_at->format('M d, Y \a\t h:i A') }}
                </p>

            </div>

            @if ($complaint)

                <a
                    href="{{ route('customer-service.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">

                    <i class="fas fa-file-lines"></i>

                    View Complaint

                </a>

            @endif

        </div>

        @if ($consumerNeedsAttention)

            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                <div class="flex items-start gap-3">

                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600">

                        <i class="fas fa-triangle-exclamation"></i>

                    </div>

                    <div>

                        <h2 class="text-sm font-bold text-amber-900">
                            Consumer indicated that the concern may require follow-up
                        </h2>

                        <p class="mt-1 text-sm leading-6 text-amber-800">
                            The consumer selected
                            <span class="font-bold">{{ $feedback->resolution_status }}</span>.
                            Review the feedback and complaint before deciding whether
                            additional service coordination is necessary.
                        </p>

                    </div>

                </div>

            </div>

        @endif

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                <div>

                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                        Internal Handling
                    </p>

                    <div class="mt-2">

                        @switch($handlingStatus)

                            @case('New')

                                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-sm font-semibold text-blue-700">

                                    <i class="fas fa-circle text-[7px]"></i>

                                    New Feedback

                                </span>

                                @break

                            @case('Reviewed')

                                <span class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-sm font-semibold text-slate-700">

                                    <i class="fas fa-eye"></i>

                                    Reviewed

                                </span>

                                @break

                            @case('Follow-up Required')

                                <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-700">

                                    <i class="fas fa-triangle-exclamation"></i>

                                    Follow-up Required

                                </span>

                                @break

                            @case('Follow-up In Progress')

                                <span class="inline-flex items-center gap-2 rounded-full bg-violet-50 px-3 py-1.5 text-sm font-semibold text-violet-700">

                                    <i class="fas fa-spinner"></i>

                                    Follow-up In Progress

                                </span>

                                @break

                            @case('Resolved')

                                <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700">

                                    <i class="fas fa-circle-check"></i>

                                    Follow-up Resolved

                                </span>

                                @break

                        @endswitch

                    </div>

                </div>

                @if ($feedback->reviewer)

                    <div class="text-left sm:text-right">

                        <p class="text-xs text-slate-400">
                            Reviewed by
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-700">
                            {{ $feedback->reviewer->full_name }}
                        </p>

                        @if ($feedback->reviewed_at)

                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $feedback->reviewed_at->format('M d, Y \a\t h:i A') }}
                            </p>

                        @endif

                    </div>

                @endif

            </div>

            <div class="p-6">

                @if ($handlingStatus === 'New')

                    <div class="grid gap-4 lg:grid-cols-2">

                        <div class="rounded-2xl border border-slate-200 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-slate-100 text-slate-600">

                                    <i class="fas fa-eye"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-slate-800">
                                        No Additional Follow-up Required
                                    </h3>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Mark this feedback as reviewed when Customer Service
                                        has evaluated it and no additional service action is required.
                                    </p>

                                </div>

                            </div>

                            <form
                                method="POST"
                                action="{{ route('customer-service.feedback.review', $feedback) }}"
                                class="mt-5">

                                @csrf
                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                                    <i class="fas fa-check"></i>

                                    Mark as Reviewed

                                </button>

                            </form>

                        </div>

                        <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-amber-900">
                                        Additional Follow-up Required
                                    </h3>

                                    <p class="mt-1 text-xs leading-5 text-amber-700">
                                        Use this when the consumer's feedback requires
                                        additional coordination or service action.
                                    </p>

                                </div>

                            </div>

                            <form
                                method="POST"
                                action="{{ route('customer-service.feedback.require-follow-up', $feedback) }}"
                                class="mt-5 space-y-3">

                                @csrf
                                @method('PATCH')

                                <div>

                                    <label
                                        for="new_follow_up_notes"
                                        class="mb-1.5 block text-xs font-semibold text-amber-900">
                                        Reason for Follow-up
                                    </label>

                                    <textarea
                                        id="new_follow_up_notes"
                                        name="follow_up_notes"
                                        rows="4"
                                        maxlength="2000"
                                        required
                                        placeholder="Explain why additional follow-up is required..."
                                        class="w-full rounded-xl border-amber-200 bg-white px-3 py-2.5 text-sm focus:border-amber-400 focus:ring-amber-400">{{ old('follow_up_notes') }}</textarea>

                                </div>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">

                                    <i class="fas fa-arrow-right"></i>

                                    Require Follow-up

                                </button>

                            </form>

                        </div>

                    </div>

                @elseif ($handlingStatus === 'Reviewed')

                    <div class="grid gap-5 lg:grid-cols-[minmax(0,1fr)_420px] lg:items-start">

                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600">

                                    <i class="fas fa-check"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-emerald-900">
                                        Feedback Reviewed
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-emerald-800">
                                        Customer Service has reviewed this feedback and
                                        no additional follow-up is currently recorded.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="rounded-2xl border border-amber-200 p-5">

                            <h3 class="text-sm font-bold text-slate-800">
                                Need Additional Action?
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                A reviewed feedback record can still be moved to
                                follow-up when additional action becomes necessary.
                            </p>

                            <form
                                method="POST"
                                action="{{ route('customer-service.feedback.require-follow-up', $feedback) }}"
                                class="mt-4 space-y-3">

                                @csrf
                                @method('PATCH')

                                <textarea
                                    name="follow_up_notes"
                                    rows="3"
                                    maxlength="2000"
                                    required
                                    placeholder="Reason for follow-up..."
                                    class="w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:border-amber-500 focus:ring-amber-500">{{ old('follow_up_notes') }}</textarea>

                                <button
                                    type="submit"
                                    class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700">

                                    <i class="fas fa-triangle-exclamation"></i>

                                    Require Follow-up

                                </button>

                            </form>

                        </div>

                    </div>

                @elseif ($handlingStatus === 'Follow-up Required')

                    <div class="space-y-5">

                        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-amber-600">

                                    <i class="fas fa-triangle-exclamation"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-amber-900">
                                        Follow-up Required
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-amber-800">
                                        Additional action has been identified but has
                                        not yet been started.
                                    </p>

                                </div>

                            </div>

                        </div>

                        @if ($feedback->follow_up_notes)

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Follow-up Notes
                                </p>

                                <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $feedback->follow_up_notes }}</p>

                                </div>

                            </div>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('customer-service.feedback.start-follow-up', $feedback) }}"
                            class="rounded-2xl border border-slate-200 p-5">

                            @csrf
                            @method('PATCH')

                            <label
                                for="start_follow_up_notes"
                                class="block text-sm font-semibold text-slate-700">
                                Additional Note
                            </label>

                            <p class="mt-1 text-xs text-slate-500">
                                Optional. Record what Customer Service is doing to begin the follow-up.
                            </p>

                            <textarea
                                id="start_follow_up_notes"
                                name="follow_up_notes"
                                rows="3"
                                maxlength="2000"
                                placeholder="Optional note about the follow-up action..."
                                class="mt-3 w-full rounded-xl border-slate-300 px-3 py-2.5 text-sm focus:border-violet-500 focus:ring-violet-500"></textarea>

                            <button
                                type="submit"
                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-violet-700">

                                <i class="fas fa-play"></i>

                                Start Follow-up

                            </button>

                        </form>

                    </div>

                @elseif ($handlingStatus === 'Follow-up In Progress')

                    <div class="space-y-5">

                        <div class="rounded-2xl border border-violet-200 bg-violet-50 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-violet-600">

                                    <i class="fas fa-spinner"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-violet-900">
                                        Follow-up In Progress
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-violet-800">
                                        Customer Service is currently coordinating
                                        additional action for this feedback.
                                    </p>

                                    @if ($feedback->follow_up_at)

                                        <p class="mt-2 text-xs font-medium text-violet-700">
                                            Started
                                            {{ $feedback->follow_up_at->format('M d, Y \a\t h:i A') }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>

                        @if ($feedback->follow_up_notes)

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Follow-up Notes
                                </p>

                                <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $feedback->follow_up_notes }}</p>

                                </div>

                            </div>

                        @endif

                        <form
                            method="POST"
                            action="{{ route('customer-service.feedback.resolve', $feedback) }}"
                            class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-5">

                            @csrf
                            @method('PATCH')

                            <h3 class="text-sm font-bold text-emerald-900">
                                Complete Follow-up
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-emerald-700">
                                Record the result of the follow-up before marking it resolved.
                            </p>

                            <label
                                for="resolution_notes"
                                class="mt-4 block text-sm font-semibold text-slate-700">
                                Resolution Notes
                            </label>

                            <textarea
                                id="resolution_notes"
                                name="follow_up_notes"
                                rows="4"
                                maxlength="2000"
                                required
                                placeholder="Describe the action taken and how the follow-up was resolved..."
                                class="mt-2 w-full rounded-xl border-emerald-200 bg-white px-3 py-2.5 text-sm focus:border-emerald-500 focus:ring-emerald-500">{{ old('follow_up_notes') }}</textarea>

                            <button
                                type="submit"
                                class="mt-4 inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-emerald-700">

                                <i class="fas fa-circle-check"></i>

                                Mark Follow-up Resolved

                            </button>

                        </form>

                    </div>

                @elseif ($handlingStatus === 'Resolved')

                    <div class="space-y-5">

                        <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5">

                            <div class="flex items-start gap-3">

                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-600">

                                    <i class="fas fa-circle-check"></i>

                                </div>

                                <div>

                                    <h3 class="text-sm font-bold text-emerald-900">
                                        Follow-up Resolved
                                    </h3>

                                    <p class="mt-1 text-sm leading-6 text-emerald-800">
                                        Customer Service has completed the additional
                                        follow-up associated with this feedback.
                                    </p>

                                    @if ($feedback->resolved_at)

                                        <p class="mt-2 text-xs font-medium text-emerald-700">
                                            Resolved
                                            {{ $feedback->resolved_at->format('M d, Y \a\t h:i A') }}
                                        </p>

                                    @endif

                                </div>

                            </div>

                        </div>

                        @if ($feedback->follow_up_notes)

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Follow-up Record
                                </p>

                                <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $feedback->follow_up_notes }}</p>

                                </div>

                            </div>

                        @endif

                    </div>

                @endif

            </div>

        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_320px]">

            <div class="space-y-6">

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-6 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-50 text-amber-500">
                                <i class="fas fa-star"></i>
                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Service Ratings
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Consumer evaluation of the delivered service
                                </p>

                            </div>

                        </div>

                        <div class="text-right">

                            <p class="text-2xl font-bold text-slate-900">
                                {{ number_format($averageRating, 1) }}
                            </p>

                            <p class="text-xs text-slate-400">
                                Average / 5
                            </p>

                        </div>

                    </div>

                    <div class="grid gap-4 p-6 sm:grid-cols-2">

                        @php
                            $ratings = [
                                [
                                    'label' => 'Overall Experience',
                                    'value' => $feedback->overall_rating,
                                    'icon' => 'fa-star',
                                ],
                                [
                                    'label' => 'Service Quality',
                                    'value' => $feedback->service_quality_rating,
                                    'icon' => 'fa-screwdriver-wrench',
                                ],
                                [
                                    'label' => 'Response Time',
                                    'value' => $feedback->response_time_rating,
                                    'icon' => 'fa-clock',
                                ],
                                [
                                    'label' => 'Personnel Courtesy',
                                    'value' => $feedback->personnel_courtesy_rating,
                                    'icon' => 'fa-user-group',
                                ],
                            ];
                        @endphp

                        @foreach ($ratings as $rating)

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-5">

                                <div class="flex items-center gap-2">

                                    <i class="fas {{ $rating['icon'] }} text-sm text-sky-600"></i>

                                    <p class="text-sm font-semibold text-slate-700">
                                        {{ $rating['label'] }}
                                    </p>

                                </div>

                                <div class="mt-4 flex items-center gap-1">

                                    @for ($i = 1; $i <= 5; $i++)

                                        <i
                                            class="fas fa-star {{ $i <= $rating['value'] ? 'text-amber-400' : 'text-slate-200' }}">
                                        </i>

                                    @endfor

                                </div>

                                <p class="mt-2 text-sm font-bold text-slate-800">
                                    {{ $rating['value'] }}/5
                                </p>

                            </div>

                        @endforeach

                    </div>

                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                <i class="fas fa-comment-dots"></i>
                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Consumer Response
                                </h2>

                                <p class="mt-0.5 text-xs text-slate-500">
                                    Original resolution assessment and comments submitted by the consumer
                                </p>

                            </div>

                        </div>

                    </div>

                    <div class="space-y-5 p-6">

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Consumer Resolution Status
                            </p>

                            <div class="mt-2">

                                @if ($feedback->resolution_status === 'Resolved')

                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1.5 text-sm font-semibold text-emerald-700">

                                        <i class="fas fa-circle-check"></i>

                                        Resolved

                                    </span>

                                @elseif ($feedback->resolution_status === 'Partially Resolved')

                                    <span class="inline-flex items-center gap-2 rounded-full bg-amber-50 px-3 py-1.5 text-sm font-semibold text-amber-700">

                                        <i class="fas fa-circle-half-stroke"></i>

                                        Partially Resolved

                                    </span>

                                @else

                                    <span class="inline-flex items-center gap-2 rounded-full bg-red-50 px-3 py-1.5 text-sm font-semibold text-red-700">

                                        <i class="fas fa-circle-xmark"></i>

                                        Not Resolved

                                    </span>

                                @endif

                            </div>

                            <p class="mt-2 text-xs leading-5 text-slate-400">
                                This is the consumer's original assessment and is not changed
                                by Customer Service follow-up.
                            </p>

                        </div>

                        <div>

                            <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Comments
                            </p>

                            @if ($feedback->comments)

                                <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 p-5">

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $feedback->comments }}</p>

                                </div>

                            @else

                                <p class="mt-2 text-sm italic text-slate-400">
                                    No additional comments were provided.
                                </p>

                            @endif

                        </div>

                    </div>

                </section>

                @if ($complaint)

                    <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5">

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-violet-50 text-violet-600">
                                    <i class="fas fa-file-lines"></i>
                                </div>

                                <div>

                                    <h2 class="font-bold text-slate-900">
                                        Related Complaint
                                    </h2>

                                    <p class="mt-0.5 text-xs text-slate-500">
                                        Service concern connected to this feedback
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-6">

                            <div class="grid gap-5 sm:grid-cols-2">

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Complaint Number
                                    </p>

                                    <p class="mt-1 text-sm font-bold text-slate-800">
                                        {{ $complaint->complaint_no }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Status
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $complaint->status }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Division
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $complaint->division?->name ?? '—' }}
                                    </p>

                                </div>

                                <div>

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Complaint Type
                                    </p>

                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $complaint->category?->name ?? '—' }}
                                    </p>

                                </div>

                            </div>

                            <div class="mt-5 border-t border-slate-100 pt-5">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Description
                                </p>

                                <p class="mt-2 text-sm leading-7 text-slate-600">
                                    {{ $complaint->description }}
                                </p>

                            </div>

                            @if ($complaint->technicians->count())

                                <div class="mt-5 border-t border-slate-100 pt-5">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Assigned Service Personnel
                                    </p>

                                    <div class="mt-3 flex flex-wrap gap-2">

                                        @foreach ($complaint->technicians as $technician)

                                            <span class="inline-flex items-center gap-2 rounded-xl bg-slate-100 px-3 py-2 text-xs font-semibold text-slate-600">

                                                <i class="fas fa-user-gear text-slate-400"></i>

                                                {{ $technician->full_name }}

                                            </span>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                            <div class="mt-5 border-t border-slate-100 pt-5">

                                <a
                                    href="{{ route('customer-service.complaints.show', $complaint) }}"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800">

                                    <i class="fas fa-arrow-up-right-from-square"></i>

                                    Open Complaint Details

                                </a>

                            </div>

                        </div>

                    </section>

                @endif

            </div>

            <aside class="space-y-6">

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="font-bold text-slate-900">
                            Consumer Information
                        </h2>

                    </div>

                    <div class="space-y-4 p-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                                <i class="fas fa-user"></i>
                            </div>

                            <div class="min-w-0">

                                <p class="truncate text-sm font-bold text-slate-800">
                                    {{ $consumer?->full_name ?? '—' }}
                                </p>

                                <p class="mt-0.5 text-xs text-slate-400">
                                    Consumer
                                </p>

                            </div>

                        </div>

                        <div class="border-t border-slate-100 pt-4">

                            <dl class="space-y-4">

                                <div>

                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Account Number
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-slate-700">
                                        {{ $consumer?->account_number ?? '—' }}
                                    </dd>

                                </div>

                                <div>

                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Phone
                                    </dt>

                                    <dd class="mt-1 text-sm font-medium text-slate-700">
                                        {{ $consumer?->phone ?? '—' }}
                                    </dd>

                                </div>

                                <div>

                                    <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                        Email
                                    </dt>

                                    <dd class="mt-1 break-all text-sm font-medium text-slate-700">
                                        {{ $consumer?->email ?? '—' }}
                                    </dd>

                                </div>

                                @if ($consumer?->address)

                                    <div>

                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                            Address
                                        </dt>

                                        <dd class="mt-1 text-sm leading-6 text-slate-700">
                                            {{ $consumer->address->full_address ?: '—' }}
                                        </dd>

                                    </div>

                                @endif

                            </dl>

                        </div>

                    </div>

                </section>

                <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-5 py-4">

                        <h2 class="font-bold text-slate-900">
                            Handling Information
                        </h2>

                    </div>

                    <div class="p-5">

                        <dl class="space-y-4">

                            <div>

                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Current Status
                                </dt>

                                <dd class="mt-1 text-sm font-semibold text-slate-700">
                                    {{ $handlingStatus }}
                                </dd>

                            </div>

                            <div>

                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Reviewed By
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $feedback->reviewer?->full_name ?? 'Not yet reviewed' }}
                                </dd>

                            </div>

                            <div>

                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Reviewed At
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $feedback->reviewed_at
                                        ? $feedback->reviewed_at->format('M d, Y h:i A')
                                        : '—' }}
                                </dd>

                            </div>

                            <div>

                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Follow-up Started
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $feedback->follow_up_at
                                        ? $feedback->follow_up_at->format('M d, Y h:i A')
                                        : '—' }}
                                </dd>

                            </div>

                            <div>

                                <dt class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Follow-up Resolved
                                </dt>

                                <dd class="mt-1 text-sm font-medium text-slate-700">
                                    {{ $feedback->resolved_at
                                        ? $feedback->resolved_at->format('M d, Y h:i A')
                                        : '—' }}
                                </dd>

                            </div>

                        </dl>

                    </div>

                </section>

                <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-start gap-3">

                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-600">
                            <i class="fas fa-circle-info"></i>
                        </div>

                        <div>

                            <h3 class="text-sm font-bold text-slate-800">
                                Feedback Follow-up
                            </h3>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                The consumer's original resolution assessment remains
                                unchanged. Internal handling records how Customer Service
                                reviewed and responded to the feedback.
                            </p>

                        </div>

                    </div>

                </section>

            </aside>

        </div>

    </div>

@endsection
