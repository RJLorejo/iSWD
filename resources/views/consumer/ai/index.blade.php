@extends('consumer.layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Header --}}
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                AI Water Service Assistant
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Ask questions about water service concerns and available
                service information.
            </p>
        </div>


        {{-- Information --}}
        <div class="rounded-2xl border border-sky-100 bg-sky-50 p-5">

            <div class="flex gap-3">

                <div
                    class="flex h-10 w-10 shrink-0 items-center justify-center
                        rounded-xl bg-sky-600 text-white">

                    <i class="fas fa-robot"></i>

                </div>

                <div>

                    <h2 class="font-semibold text-sky-900">
                        How can I help?
                    </h2>

                    <p class="mt-1 text-sm leading-6 text-sky-800">
                        You can ask about common water service concerns,
                        service interruptions, complaints, and available
                        service information.
                    </p>

                </div>

            </div>

        </div>


        {{-- Error --}}
        @if (session('ai_error'))
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">

                <i class="fas fa-circle-exclamation mr-2"></i>

                {{ session('ai_error') }}

            </div>
        @endif


        {{-- Question form --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

            <form method="POST" action="{{ route('consumer.ai.ask') }}" class="space-y-4">

                @csrf

                <div>

                    <label for="question" class="mb-2 block text-sm font-semibold text-slate-700">
                        Your question
                    </label>

                    <textarea id="question" name="question" rows="5" maxlength="1000" required
                        placeholder="Example: What should I do if there is no water?"
                        class="w-full rounded-xl border border-slate-300
                           px-4 py-3 text-sm
                           focus:border-sky-500
                           focus:ring-2 focus:ring-sky-200
                           focus:outline-none">{{ old('question') }}</textarea>

                    @error('question')
                        <p class="mt-1 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>


                <div class="flex justify-end">

                    <button type="submit"
                        class="inline-flex items-center gap-2 rounded-xl
                           bg-sky-600 px-5 py-3
                           text-sm font-semibold text-white
                           transition hover:bg-sky-700">

                        <i class="fas fa-paper-plane"></i>

                        Ask Assistant

                    </button>

                </div>

            </form>

        </div>


        {{-- AI Result --}}
        @if (session('ai_result'))

            @php
                $result = session('ai_result');
            @endphp

            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">

                <div class="flex items-center gap-3">

                    <div
                        class="flex h-10 w-10 items-center justify-center
                            rounded-xl bg-sky-100 text-sky-700">

                        <i class="fas fa-robot"></i>

                    </div>

                    <div>

                        <h2 class="font-semibold text-slate-800">
                            AI Assistant Response
                        </h2>

                        <p class="text-xs text-slate-500">
                            Based on the approved knowledge base
                        </p>

                    </div>

                </div>


                <div class="mt-5 rounded-xl bg-slate-50 p-5">

                    <p class="text-sm leading-7 text-slate-700">
                        {{ $result['answer'] ?? 'No answer available.' }}
                    </p>

                </div>


                {{-- Confidence --}}
                @if (isset($result['confidence']))
                    <div class="mt-4">

                        <div class="flex items-center justify-between text-xs">

                            <span class="font-medium text-slate-600">
                                Match confidence
                            </span>

                            <span class="font-semibold text-sky-700">
                                {{ number_format(($result['confidence'] ?? 0) * 100, 1) }}%
                            </span>

                        </div>

                        <div class="mt-2 h-2 overflow-hidden rounded-full bg-slate-200">

                            <div class="h-full rounded-full bg-sky-500"
                                style="width: {{ min(($result['confidence'] ?? 0) * 100, 100) }}%"></div>

                        </div>

                    </div>
                @endif


                {{-- Sources --}}
                @if (!empty($result['sources']))
                    <div class="mt-6">

                        <h3 class="text-sm font-semibold text-slate-700">
                            Relevant knowledge
                        </h3>

                        <div class="mt-3 space-y-3">

                            @foreach ($result['sources'] as $source)
                                <div class="rounded-xl border border-slate-200 p-4">

                                    <p class="text-sm font-semibold text-slate-800">
                                        {{ $source['title'] }}
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        {{ $source['content'] }}
                                    </p>

                                </div>
                            @endforeach

                        </div>

                    </div>
                @endif

            </div>

        @endif


        {{-- Important notice --}}
        <div class="rounded-xl border border-amber-200 bg-amber-50 p-4">

            <p class="text-xs leading-5 text-amber-800">

                <i class="fas fa-circle-info mr-1"></i>

                The AI assistant provides information based on the approved
                knowledge base. It does not replace Customer Service and
                does not independently approve, reject, or diagnose complaints.

            </p>

        </div>

    </div>

@endsection
