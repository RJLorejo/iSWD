@extends('consumer.layouts.app')

@section('title', 'Service Feedback')

@section('content')

    <div class="mx-auto max-w-4xl space-y-6">

        <div
            class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

            <div
                class="flex flex-col gap-5 p-6 sm:flex-row sm:items-center sm:justify-between md:p-8">

                <div class="flex items-start gap-4">

                    <div
                        class="hidden h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-amber-600 sm:flex">

                        <i class="fas fa-star text-lg"></i>

                    </div>

                    <div>

                        <p
                            class="text-xs font-bold uppercase tracking-wider text-sky-600">
                            Complaint {{ $complaint->complaint_no }}
                        </p>

                        <h1
                            class="mt-1 text-2xl font-bold tracking-tight text-slate-900 md:text-3xl">
                            Service Feedback
                        </h1>

                        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">
                            Tell us about your experience with the service provided
                            for this complaint. Your feedback helps Sagay Water
                            District improve its consumer services.
                        </p>

                    </div>

                </div>

                <a
                    href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50 hover:text-slate-900">

                    <i class="fas fa-arrow-left text-xs"></i>

                    Complaint Details

                </a>

            </div>

        </div>

        <div
            class="grid gap-4 sm:grid-cols-3">

            <div
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                <p
                    class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Complaint
                </p>

                <p class="mt-2 text-sm font-bold text-slate-800">
                    {{ $complaint->complaint_no }}
                </p>

            </div>

            <div
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                <p
                    class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Complaint Type
                </p>

                <p class="mt-2 text-sm font-bold text-slate-800">
                    {{ $complaint->category?->name ?? 'Not specified' }}
                </p>

            </div>

            <div
                class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">

                <p
                    class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                    Status
                </p>

                <div class="mt-2">

                    <span
                        class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                        <i class="fas fa-circle-check text-[10px]"></i>

                        {{ $complaint->status }}

                    </span>

                </div>

            </div>

        </div>

        @if ($errors->any())

            <div
                class="rounded-2xl border border-red-200 bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-red-600">

                        <i class="fas fa-circle-exclamation"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-red-900">
                            Please review your feedback
                        </p>

                        <ul
                            class="mt-2 list-inside list-disc space-y-1 text-sm text-red-700">

                            @foreach ($errors->all() as $error)

                                <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

            </div>

        @endif

        <form
            method="POST"
            action="{{ route('consumer.complaints.feedback.store', $complaint) }}"
            class="space-y-6"
            x-data="{ submitting: false }"
            @submit="submitting = true">

            @csrf

            <section
                class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div
                    class="border-b border-slate-100 px-6 py-5 md:px-8">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                            <i class="fas fa-star"></i>

                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Overall Service Experience
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                How would you rate your overall experience?
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-6 md:p-8">

                    <div
                        x-data="{ rating: {{ (int) old('overall_rating', 0) }}, hover: 0 }">

                        <div class="flex flex-wrap items-center gap-2">

                            @for ($i = 1; $i <= 5; $i++)

                                <button
                                    type="button"
                                    @mouseenter="hover = {{ $i }}"
                                    @mouseleave="hover = 0"
                                    @click="rating = {{ $i }}"
                                    class="flex h-12 w-12 items-center justify-center rounded-xl border transition"
                                    :class="(hover || rating) >= {{ $i }}
                                        ? 'border-amber-300 bg-amber-50 text-amber-500'
                                        : 'border-slate-200 bg-white text-slate-300 hover:border-amber-200'">

                                    <i class="fas fa-star text-xl"></i>

                                </button>

                            @endfor

                        </div>

                        <input
                            type="hidden"
                            name="overall_rating"
                            :value="rating">

                        <div class="mt-3 min-h-5">

                            <span
                                x-show="rating === 1"
                                class="text-sm font-semibold text-slate-600">
                                Very Dissatisfied
                            </span>

                            <span
                                x-show="rating === 2"
                                class="text-sm font-semibold text-slate-600">
                                Dissatisfied
                            </span>

                            <span
                                x-show="rating === 3"
                                class="text-sm font-semibold text-slate-600">
                                Satisfied
                            </span>

                            <span
                                x-show="rating === 4"
                                class="text-sm font-semibold text-slate-600">
                                Very Satisfied
                            </span>

                            <span
                                x-show="rating === 5"
                                class="text-sm font-semibold text-slate-600">
                                Excellent
                            </span>

                        </div>

                    </div>

                    @error('overall_rating')

                        <p class="mt-2 text-sm font-medium text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </section>

            <section
                class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div
                    class="border-b border-slate-100 px-6 py-5 md:px-8">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                            <i class="fas fa-chart-simple"></i>

                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Service Ratings
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Rate the following aspects of the service from
                                1 to 5.
                            </p>

                        </div>

                    </div>

                </div>

                <div
                    class="divide-y divide-slate-100 px-6 md:px-8">

                    <div
                        class="grid gap-4 py-6 md:grid-cols-[1fr_auto] md:items-center">

                        <div>

                            <div class="flex items-center gap-2">

                                <i class="fas fa-screwdriver-wrench text-sm text-sky-600"></i>

                                <p class="text-sm font-semibold text-slate-800">
                                    Service Quality
                                </p>

                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Quality of the service or action provided for your
                                concern.
                            </p>

                        </div>

                        <div
                            x-data="{ rating: {{ (int) old('service_quality_rating', 0) }} }">

                            <div class="flex gap-1.5">

                                @for ($i = 1; $i <= 5; $i++)

                                    <button
                                        type="button"
                                        @click="rating = {{ $i }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg transition"
                                        :class="rating >= {{ $i }}
                                            ? 'bg-amber-50 text-amber-500'
                                            : 'bg-slate-50 text-slate-300 hover:bg-amber-50 hover:text-amber-400'">

                                        <i class="fas fa-star"></i>

                                    </button>

                                @endfor

                            </div>

                            <input
                                type="hidden"
                                name="service_quality_rating"
                                :value="rating">

                        </div>

                        @error('service_quality_rating')

                            <p class="text-sm text-red-600 md:col-span-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div
                        class="grid gap-4 py-6 md:grid-cols-[1fr_auto] md:items-center">

                        <div>

                            <div class="flex items-center gap-2">

                                <i class="fas fa-clock text-sm text-sky-600"></i>

                                <p class="text-sm font-semibold text-slate-800">
                                    Response Time
                                </p>

                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                How satisfied were you with the time taken to
                                respond to your concern?
                            </p>

                        </div>

                        <div
                            x-data="{ rating: {{ (int) old('response_time_rating', 0) }} }">

                            <div class="flex gap-1.5">

                                @for ($i = 1; $i <= 5; $i++)

                                    <button
                                        type="button"
                                        @click="rating = {{ $i }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg transition"
                                        :class="rating >= {{ $i }}
                                            ? 'bg-amber-50 text-amber-500'
                                            : 'bg-slate-50 text-slate-300 hover:bg-amber-50 hover:text-amber-400'">

                                        <i class="fas fa-star"></i>

                                    </button>

                                @endfor

                            </div>

                            <input
                                type="hidden"
                                name="response_time_rating"
                                :value="rating">

                        </div>

                        @error('response_time_rating')

                            <p class="text-sm text-red-600 md:col-span-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div
                        class="grid gap-4 py-6 md:grid-cols-[1fr_auto] md:items-center">

                        <div>

                            <div class="flex items-center gap-2">

                                <i class="fas fa-user-group text-sm text-sky-600"></i>

                                <p class="text-sm font-semibold text-slate-800">
                                    Personnel Courtesy
                                </p>

                            </div>

                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Courtesy and professionalism of Sagay Water District
                                personnel who handled your concern.
                            </p>

                        </div>

                        <div
                            x-data="{ rating: {{ (int) old('personnel_courtesy_rating', 0) }} }">

                            <div class="flex gap-1.5">

                                @for ($i = 1; $i <= 5; $i++)

                                    <button
                                        type="button"
                                        @click="rating = {{ $i }}"
                                        class="flex h-9 w-9 items-center justify-center rounded-lg transition"
                                        :class="rating >= {{ $i }}
                                            ? 'bg-amber-50 text-amber-500'
                                            : 'bg-slate-50 text-slate-300 hover:bg-amber-50 hover:text-amber-400'">

                                        <i class="fas fa-star"></i>

                                    </button>

                                @endfor

                            </div>

                            <input
                                type="hidden"
                                name="personnel_courtesy_rating"
                                :value="rating">

                        </div>

                        @error('personnel_courtesy_rating')

                            <p class="text-sm text-red-600 md:col-span-2">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>

            </section>

            <section
                class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div
                    class="border-b border-slate-100 px-6 py-5 md:px-8">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">

                            <i class="fas fa-circle-check"></i>

                        </div>

                        <div>

                            <h2 class="font-bold text-slate-900">
                                Concern Resolution
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Let us know whether the service resolved your
                                reported concern.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="space-y-6 p-6 md:p-8">

                    <div>

                        <label class="mb-3 block text-sm font-semibold text-slate-700">
                            Was your concern resolved?
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="grid gap-3 sm:grid-cols-3">

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="resolution_status"
                                    value="Resolved"
                                    class="peer sr-only"
                                    {{ old('resolution_status') === 'Resolved' ? 'checked' : '' }}>

                                <div
                                    class="rounded-2xl border border-slate-200 p-4 transition peer-checked:border-emerald-400 peer-checked:bg-emerald-50 peer-checked:ring-1 peer-checked:ring-emerald-300">

                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">

                                        <i class="fas fa-circle-check"></i>

                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-800">
                                        Resolved
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        My reported concern was resolved.
                                    </p>

                                </div>

                            </label>

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="resolution_status"
                                    value="Partially Resolved"
                                    class="peer sr-only"
                                    {{ old('resolution_status') === 'Partially Resolved' ? 'checked' : '' }}>

                                <div
                                    class="rounded-2xl border border-slate-200 p-4 transition peer-checked:border-amber-400 peer-checked:bg-amber-50 peer-checked:ring-1 peer-checked:ring-amber-300">

                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-amber-50 text-amber-600">

                                        <i class="fas fa-circle-half-stroke"></i>

                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-800">
                                        Partially Resolved
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        The situation improved but still needs attention.
                                    </p>

                                </div>

                            </label>

                            <label class="cursor-pointer">

                                <input
                                    type="radio"
                                    name="resolution_status"
                                    value="Not Resolved"
                                    class="peer sr-only"
                                    {{ old('resolution_status') === 'Not Resolved' ? 'checked' : '' }}>

                                <div
                                    class="rounded-2xl border border-slate-200 p-4 transition peer-checked:border-red-400 peer-checked:bg-red-50 peer-checked:ring-1 peer-checked:ring-red-300">

                                    <div
                                        class="flex h-9 w-9 items-center justify-center rounded-xl bg-red-50 text-red-600">

                                        <i class="fas fa-circle-xmark"></i>

                                    </div>

                                    <p class="mt-3 text-sm font-semibold text-slate-800">
                                        Not Resolved
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        The reported concern remains unresolved.
                                    </p>

                                </div>

                            </label>

                        </div>

                        @error('resolution_status')

                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                    <div>

                        <div
                            class="mb-2 flex items-center justify-between gap-4">

                            <label
                                for="comments"
                                class="text-sm font-semibold text-slate-700">

                                Additional Comments

                                <span class="font-normal text-slate-400">
                                    (Optional)
                                </span>

                            </label>

                            <span
                                id="commentCount"
                                class="text-xs text-slate-400">
                                0 / 2000
                            </span>

                        </div>

                        <textarea
                            id="comments"
                            name="comments"
                            rows="5"
                            maxlength="2000"
                            placeholder="Tell us what went well or what Sagay Water District can improve..."
                            class="w-full resize-y rounded-2xl border-slate-300 px-4 py-3 text-sm leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">{{ old('comments') }}</textarea>

                        @error('comments')

                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>

                </div>

            </section>

            <div
                class="rounded-2xl border border-sky-100 bg-sky-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-sky-600">

                        <i class="fas fa-circle-info"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-sky-900">
                            Your feedback matters
                        </p>

                        <p class="mt-1 text-xs leading-5 text-sky-800">
                            Feedback is connected to this complaint and may be used
                            by Sagay Water District to evaluate and improve consumer
                            service delivery.
                        </p>

                    </div>

                </div>

            </div>

            <div
                class="flex flex-col-reverse gap-3 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:flex-row sm:items-center sm:justify-between sm:p-5">

                <p class="hidden text-xs text-slate-400 md:block">
                    All service rating fields are required.
                </p>

                <div
                    class="flex flex-col-reverse gap-3 sm:ml-auto sm:flex-row">

                    <a
                        href="{{ route('consumer.complaints.show', $complaint) }}"
                        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                        Cancel

                    </a>

                    <button
                        type="submit"
                        :disabled="submitting"
                        class="inline-flex min-w-[180px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-70">

                        <span
                            x-show="!submitting"
                            class="inline-flex items-center gap-2">

                            <i class="fas fa-paper-plane"></i>

                            Submit Feedback

                        </span>

                        <span
                            x-cloak
                            x-show="submitting"
                            class="inline-flex items-center gap-2">

                            <i class="fas fa-spinner animate-spin"></i>

                            Submitting...

                        </span>

                    </button>

                </div>

            </div>

        </form>

    </div>

@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const comments =
                document.getElementById('comments');

            const commentCount =
                document.getElementById('commentCount');

            function updateCommentCount() {
                if (!comments || !commentCount) {
                    return;
                }

                commentCount.textContent =
                    comments.value.length + ' / 2000';
            }

            if (comments) {
                updateCommentCount();

                comments.addEventListener(
                    'input',
                    updateCommentCount
                );
            }
        });
    </script>

@endpush
