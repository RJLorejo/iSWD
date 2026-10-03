{{-- @extends('consumer.layouts.app')

@section('title', 'AI Water Service Assistant')

@section('content')

    <div class="mx-auto max-w-5xl space-y-6">

        <div
            class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-sky-700 via-blue-700 to-cyan-600 p-6 text-white shadow-sm sm:p-8">

            <div class="relative z-10 max-w-3xl">

                <div
                    class="mb-5 flex h-12 w-12 items-center justify-center rounded-2xl border border-white/20 bg-white/10 backdrop-blur-sm">

                    <i class="fas fa-robot text-xl"></i>

                </div>

                <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-100">
                    iSWD Consumer Support
                </p>

                <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">
                    AI Water Service Assistant
                </h1>

                <p class="mt-3 max-w-2xl text-sm leading-6 text-sky-100 sm:text-base">
                    Get helpful information about common water service concerns,
                    service interruptions, complaint procedures, and other
                    Sagay Water District services.
                </p>

            </div>

            <div
                class="absolute -right-16 -top-20 h-56 w-56 rounded-full bg-white/10">
            </div>

            <div
                class="absolute -bottom-24 right-24 h-64 w-64 rounded-full bg-cyan-300/10">
            </div>

        </div>

        @if (session('ai_error'))

            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">

                <div class="flex items-start gap-3">

                    <div
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-red-600">

                        <i class="fas fa-circle-exclamation"></i>

                    </div>

                    <div>

                        <p class="text-sm font-semibold text-red-900">
                            Unable to process your question
                        </p>

                        <p class="mt-1 text-sm leading-6 text-red-700">
                            {{ session('ai_error') }}
                        </p>

                    </div>

                </div>

            </div>

        @endif

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_280px]">

            <div class="space-y-6">

                <section
                    class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                    <div class="border-b border-slate-100 px-6 py-5 sm:px-7">

                        <div class="flex items-start gap-3">

                            <div
                                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                                <i class="fas fa-comments"></i>

                            </div>

                            <div>

                                <h2 class="font-bold text-slate-900">
                                    Ask a Question
                                </h2>

                                <p class="mt-1 text-sm leading-5 text-slate-500">
                                    Describe your question using clear and specific
                                    details.
                                </p>

                            </div>

                        </div>

                    </div>

                    <form method="POST"
                        action="{{ route('consumer.ai.ask') }}"
                        class="p-6 sm:p-7"
                        x-data="{ loading: false }"
                        @submit="loading = true">

                        @csrf

                        <div>

                            <div class="mb-2 flex items-center justify-between gap-4">

                                <label for="question"
                                    class="text-sm font-semibold text-slate-700">

                                    Your Question

                                </label>

                                <span id="questionCount"
                                    class="text-xs text-slate-400">
                                    0 / 1000
                                </span>

                            </div>

                            <textarea id="question"
                                name="question"
                                rows="5"
                                maxlength="1000"
                                required
                                placeholder="Example: What should I do if there is no water in our area?"
                                class="w-full resize-y rounded-2xl border-slate-300 px-4 py-3.5 text-sm leading-6 text-slate-700 transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">{{ old('question') }}</textarea>

                            @error('question')

                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                        <div class="mt-4">

                            <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">
                                Suggested Questions
                            </p>

                            <div class="flex flex-wrap gap-2">

                                <button type="button"
                                    data-question="What should I do if there is no water in our area?"
                                    class="suggested-question rounded-full border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                                    No water supply

                                </button>

                                <button type="button"
                                    data-question="How can I report a water leak?"
                                    class="suggested-question rounded-full border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                                    Report a water leak

                                </button>

                                <button type="button"
                                    data-question="How can I track the status of my complaint?"
                                    class="suggested-question rounded-full border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                                    Track a complaint

                                </button>

                                <button type="button"
                                    data-question="Where can I view current service announcements?"
                                    class="suggested-question rounded-full border border-slate-200 bg-slate-50 px-3.5 py-2 text-xs font-medium text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                                    Service announcements

                                </button>

                            </div>

                        </div>

                        <div
                            class="mt-6 flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:items-center sm:justify-between">

                            <p class="max-w-md text-xs leading-5 text-slate-400">
                                Avoid including passwords or other sensitive account
                                information in your question.
                            </p>

                            <button type="submit"
                                :disabled="loading"
                                class="inline-flex min-w-[160px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-70">

                                <span x-show="!loading"
                                    class="inline-flex items-center gap-2">

                                    <i class="fas fa-paper-plane"></i>

                                    Ask Assistant

                                </span>

                                <span x-show="loading"
                                    x-cloak
                                    class="inline-flex items-center gap-2">

                                    <i class="fas fa-spinner animate-spin"></i>

                                    Searching...

                                </span>

                            </button>

                        </div>

                    </form>

                </section>

                @if (session('ai_result'))

                    @php
                        $result = session('ai_result');

                        $confidence = isset($result['confidence'])
                            ? min(max((float) $result['confidence'], 0), 1)
                            : null;

                        $confidencePercent = $confidence !== null
                            ? $confidence * 100
                            : null;
                    @endphp

                    <section
                        class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                        <div class="border-b border-slate-100 px-6 py-5 sm:px-7">

                            <div class="flex items-center justify-between gap-4">

                                <div class="flex items-center gap-3">

                                    <div
                                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-sky-100 text-sky-700">

                                        <i class="fas fa-robot"></i>

                                    </div>

                                    <div>

                                        <h2 class="font-bold text-slate-900">
                                            Assistant Response
                                        </h2>

                                        <p class="mt-0.5 text-xs text-slate-500">
                                            Information from the approved knowledge base
                                        </p>

                                    </div>

                                </div>

                                <span
                                    class="hidden items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700 sm:inline-flex">

                                    <i class="fas fa-book-open text-[10px]"></i>

                                    Knowledge Based

                                </span>

                            </div>

                        </div>

                        <div class="p-6 sm:p-7">

                            <div
                                class="rounded-2xl border border-slate-100 bg-slate-50 p-5 sm:p-6">

                                <div class="flex items-start gap-3">

                                    <div
                                        class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-white text-sky-600 shadow-sm">

                                        <i class="fas fa-comment-dots text-sm"></i>

                                    </div>

                                    <p class="whitespace-pre-line text-sm leading-7 text-slate-700">{{ $result['answer'] ?? 'No answer is currently available.' }}</p>

                                </div>

                            </div>

                            @if ($confidence !== null)

                                <div class="mt-5 rounded-2xl border border-slate-100 p-4">

                                    <div class="flex items-center justify-between gap-4">

                                        <div>

                                            <p class="text-sm font-semibold text-slate-700">
                                                Knowledge Match
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Similarity to available approved information
                                            </p>

                                        </div>

                                        <span class="text-sm font-bold text-sky-700">
                                            {{ number_format($confidencePercent, 1) }}%
                                        </span>

                                    </div>

                                    <div
                                        class="mt-3 h-2 overflow-hidden rounded-full bg-slate-100">

                                        <div class="h-full rounded-full bg-sky-500 transition-all"
                                            style="width: {{ $confidencePercent }}%">
                                        </div>

                                    </div>

                                    <p class="mt-2 text-[11px] leading-5 text-slate-400">
                                        This percentage represents the knowledge match,
                                        not a guarantee that the response is correct.
                                    </p>

                                </div>

                            @endif

                            @if (!empty($result['sources']))

                                <div class="mt-7 border-t border-slate-100 pt-6">

                                    <div class="flex items-center gap-2">

                                        <i class="fas fa-book text-sm text-sky-600"></i>

                                        <h3 class="text-sm font-bold text-slate-800">
                                            Relevant Information
                                        </h3>

                                    </div>

                                    <p class="mt-1 text-xs text-slate-500">
                                        Knowledge entries used to support this response.
                                    </p>

                                    <div class="mt-4 space-y-3">

                                        @foreach ($result['sources'] as $source)

                                            <div
                                                class="rounded-2xl border border-slate-200 bg-white p-4">

                                                <div class="flex items-start gap-3">

                                                    <div
                                                        class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-500">

                                                        <i class="fas fa-file-lines text-xs"></i>

                                                    </div>

                                                    <div class="min-w-0">

                                                        <p class="text-sm font-semibold text-slate-800">
                                                            {{ $source['title'] ?? 'Knowledge Entry' }}
                                                        </p>

                                                        @if (!empty($source['content']))

                                                            <p class="mt-1 text-xs leading-6 text-slate-500">
                                                                {{ $source['content'] }}
                                                            </p>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        @endforeach

                                    </div>

                                </div>

                            @endif

                            <div
                                class="mt-6 flex flex-col gap-3 rounded-2xl border border-sky-100 bg-sky-50 p-4 sm:flex-row sm:items-center sm:justify-between">

                                <div>

                                    <p class="text-sm font-semibold text-sky-900">
                                        Need Sagay Water District assistance?
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-sky-700">
                                        Submit a complaint if your concern requires
                                        official review or service action.
                                    </p>

                                </div>

                                <a href="{{ route('consumer.complaints.create') }}"
                                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-xs font-semibold text-white transition hover:bg-sky-800">

                                    <i class="fas fa-file-circle-plus"></i>

                                    Submit Complaint

                                </a>

                            </div>

                        </div>

                    </section>

                @endif

            </div>

            <aside class="space-y-5">

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-sky-50 text-sky-700">

                        <i class="fas fa-circle-info"></i>

                    </div>

                    <h2 class="mt-4 font-bold text-slate-900">
                        What You Can Ask
                    </h2>

                    <div class="mt-4 space-y-4">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-droplet mt-0.5 w-4 text-center text-xs text-sky-600"></i>

                            <p class="text-xs leading-5 text-slate-600">
                                Common water service concerns
                            </p>

                        </div>

                        <div class="flex items-start gap-3">

                            <i class="fas fa-bullhorn mt-0.5 w-4 text-center text-xs text-sky-600"></i>

                            <p class="text-xs leading-5 text-slate-600">
                                Service interruption information
                            </p>

                        </div>

                        <div class="flex items-start gap-3">

                            <i class="fas fa-file-circle-question mt-0.5 w-4 text-center text-xs text-sky-600"></i>

                            <p class="text-xs leading-5 text-slate-600">
                                Complaint procedures and status information
                            </p>

                        </div>

                        <div class="flex items-start gap-3">

                            <i class="fas fa-building mt-0.5 w-4 text-center text-xs text-sky-600"></i>

                            <p class="text-xs leading-5 text-slate-600">
                                Available Sagay Water District service information
                            </p>

                        </div>

                    </div>

                </div>

                <div class="rounded-3xl border border-amber-200 bg-amber-50 p-5">

                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-amber-600">

                        <i class="fas fa-shield-halved"></i>

                    </div>

                    <h2 class="mt-4 text-sm font-bold text-amber-900">
                        AI-Assisted Information
                    </h2>

                    <p class="mt-2 text-xs leading-6 text-amber-800">
                        The assistant provides information based on the approved
                        knowledge base. It does not independently approve, reject,
                        classify, or diagnose complaints.
                    </p>

                    <p class="mt-3 text-xs leading-6 text-amber-800">
                        Official complaint decisions and service actions remain
                        under authorized Sagay Water District personnel.
                    </p>

                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">

                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-9 w-9 items-center justify-center rounded-xl bg-slate-100 text-slate-600">

                            <i class="fas fa-headset"></i>

                        </div>

                        <div>

                            <p class="text-sm font-bold text-slate-800">
                                Need Service Action?
                            </p>

                            <p class="text-xs text-slate-500">
                                File an official complaint
                            </p>

                        </div>

                    </div>

                    <a href="{{ route('consumer.complaints.create') }}"
                        class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-xs font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700">

                        <i class="fas fa-plus"></i>

                        Submit a Complaint

                    </a>

                </div>

            </aside>

        </div>

    </div>

@endsection

@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const question =
                document.getElementById('question');

            const questionCount =
                document.getElementById('questionCount');

            const suggestedQuestions =
                document.querySelectorAll(
                    '.suggested-question'
                );

            function updateQuestionCount() {
                if (!question || !questionCount) {
                    return;
                }

                questionCount.textContent =
                    question.value.length +
                    ' / 1000';
            }

            if (question) {
                updateQuestionCount();

                question.addEventListener(
                    'input',
                    updateQuestionCount
                );
            }

            suggestedQuestions.forEach(
                function(button) {
                    button.addEventListener(
                        'click',
                        function() {
                            if (!question) {
                                return;
                            }

                            question.value =
                                this.dataset.question || '';

                            updateQuestionCount();

                            question.focus();
                        }
                    );
                }
            );
        });
    </script>

@endpush --}}
