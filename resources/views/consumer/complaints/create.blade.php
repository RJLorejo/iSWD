@extends('consumer.layouts.app')

@section('title', 'Report a Water Service Concern')

@section('content')

    @php
        $consumer = auth()->user()->consumer;
    @endphp

    <div class="mx-auto max-w-5xl space-y-5">

        {{-- ========================================================= --}}
        {{-- HEADER --}}
        {{-- ========================================================= --}}

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-sky-600">
                    Consumer Service
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Report a Water Service Concern
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Tell us what happened in your own words. You do not need to know
                    the official SWD complaint type.
                </p>
            </div>

            <a href="{{ route('consumer.complaints.index') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                <i class="fas fa-arrow-left text-xs"></i>

                My Complaints
            </a>

        </div>


        {{-- ========================================================= --}}
        {{-- VALIDATION ERRORS --}}
        {{-- ========================================================= --}}

        @if ($errors->any())

            <div class="rounded-xl border border-red-200 bg-red-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-exclamation mt-0.5 text-red-600"></i>

                    <div>

                        <p class="text-sm font-semibold text-red-900">
                            Please check your information
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


        <form method="POST"
            action="{{ route('consumer.complaints.store') }}"
            enctype="multipart/form-data"
            class="space-y-5"
            x-data="{ submitting: false }"
            @submit="submitting = true">

            @csrf

            <input type="hidden"
                name="ai_analysis_token"
                id="ai_analysis_token"
                value="">


            {{-- ===================================================== --}}
            {{-- STEP 1 — CONCERN --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sm font-bold text-sky-700">
                            1
                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Tell Us What Happened
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Describe the problem or service you need using your own words.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <div class="mb-2 flex items-center justify-between gap-4">

                        <label for="description"
                            class="text-sm font-semibold text-slate-700">

                            Your Concern

                            <span class="text-red-500">*</span>

                        </label>

                        <span id="descriptionCount"
                            class="text-xs text-slate-400">
                            0 / 5000
                        </span>

                    </div>


                    <textarea id="description"
                        name="description"
                        rows="3"
                        maxlength="5000"
                        required
                        placeholder="Example: Wala kami tubig halin pa sang aga.&#10;&#10;Simply tell us what you noticed and when it started."
                        class="w-full resize-y rounded-xl border-slate-300 px-4 py-3 text-sm leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>


                    <div class="mt-3 flex items-start gap-2">

                        <i class="fas fa-language mt-0.5 text-sm text-sky-600"></i>

                        <p class="text-xs leading-5 text-slate-500">
                            You may explain naturally in English, Filipino, Hiligaynon,
                            or mixed language.
                        </p>

                    </div>


                    @error('description')

                        <p class="mt-2 text-sm font-medium text-red-600">
                            {{ $message }}
                        </p>

                    @enderror


                    {{-- ============================================= --}}
                    {{-- AI HELP --}}
                    {{-- ============================================= --}}

                    <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50/60 p-4">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sky-700">

                                    <i class="fas fa-wand-magic-sparkles"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">
                                        Not sure what complaint type this is?
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Let the system help match your concern with an
                                        official SWD complaint type.
                                    </p>

                                </div>

                            </div>


                            <button type="button"
                                id="analyzeConcern"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-60">

                                <i class="fas fa-wand-magic-sparkles"></i>

                                <span id="analyzeConcernText">
                                    Help Identify My Concern
                                </span>

                            </button>

                        </div>

                    </div>


                    {{-- AI ERROR --}}

                    <div id="aiAnalysisError"
                        class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    </div>


                    {{-- ============================================= --}}
                    {{-- SIMPLE AI RESULT --}}
                    {{-- ============================================= --}}

                    <div id="aiAnalysisCard"
                        class="mt-4 hidden overflow-hidden rounded-xl border border-sky-200 bg-white">

                        <div class="border-b border-sky-100 bg-sky-50 px-4 py-3">

                            <div class="flex items-center gap-3">

                                <div
                                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sky-700">

                                    <i class="fas fa-wand-magic-sparkles text-sm"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">
                                        AI Assistance
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Based on what you described.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="space-y-4 p-4 sm:p-5">

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Suggested SWD Complaint Type
                                </p>

                                <p id="aiComplaintType"
                                    class="mt-1 text-lg font-bold text-slate-900">
                                    —
                                </p>

                            </div>


                            <div>

                                <div class="rounded-xl bg-slate-50 p-3">

                                    <p class="text-xs text-slate-500">
                                        Handled By
                                    </p>

                                    <p id="aiDivision"
                                        class="mt-1 text-sm font-semibold text-slate-800">
                                        —
                                    </p>

                                </div>

                            </div>


                            {{-- Supporting explanation --}}

                            <div id="aiSupportingEvidenceSection"
                                class="hidden rounded-xl border border-slate-200 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Why this was suggested
                                </p>

                                <p id="aiSupportingSummary"
                                    class="mt-2 text-sm leading-6 text-slate-600">
                                </p>

                                <div id="aiSupportingIndicatorsSection"
                                    class="mt-3 hidden">

                                    <ul id="aiSupportingIndicators"
                                        class="space-y-1.5 text-xs leading-5 text-slate-600">
                                    </ul>

                                </div>

                            </div>


                            {{-- Kept for JavaScript compatibility but hidden from consumer --}}

                            <div class="hidden">

                                <p id="aiAnalysisStatus"></p>

                                <div id="aiSignalsSection">
                                    <ul id="aiSignals"></ul>
                                </div>

                                <div id="aiReviewSection">
                                    <p id="aiReview"></p>
                                </div>

                            </div>


                            <div id="aiMatchWarning"
                                class="hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">

                                We could not automatically match this concern with an
                                active SWD complaint type. Please choose the complaint
                                type manually below.

                            </div>


                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="text-xs leading-5 text-slate-500">
                                    Check the suggestion before continuing.
                                </p>

                                <button type="button"
                                    id="useAiClassification"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-50">

                                    <i class="fas fa-check"></i>

                                    Use This Complaint Type

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- STEP 2 — OFFICIAL SWD TYPE --}}
            {{-- ===================================================== --}}

            <section id="classificationSection"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-700">
                            2
                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                SWD Complaint Type
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Confirm the complaint type suggested above or choose it manually.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        {{-- Division --}}

                        <div>

                            <label for="division_id"
                                class="mb-2 block text-sm font-semibold text-slate-700">

                                Service Division

                                <span class="text-red-500">*</span>

                            </label>

                            <select id="division_id"
                                name="division_id"
                                required
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                                <option value="">
                                    Select service division
                                </option>

                                @foreach ($divisions as $division)

                                    <option value="{{ $division->id }}"
                                        {{ old('division_id') == $division->id ? 'selected' : '' }}>

                                        {{ $division->name }}

                                    </option>

                                @endforeach

                            </select>

                            @error('division_id')

                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>


                        {{-- Complaint Type --}}

                        <div>

                            <label for="complaint_category_id"
                                class="mb-2 block text-sm font-semibold text-slate-700">

                                Complaint Type

                                <span class="text-red-500">*</span>

                            </label>

                            <select id="complaint_category_id"
                                name="complaint_category_id"
                                required
                                disabled
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">

                                <option value="">
                                    Select service division first
                                </option>

                            </select>

                            @error('complaint_category_id')

                                <p class="mt-2 text-sm font-medium text-red-600">
                                    {{ $message }}
                                </p>

                            @enderror

                        </div>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- COMMERCIAL ACCOUNT --}}
            {{-- ===================================================== --}}

            <section id="commercialAccountSection"
                class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">

                            <i class="fas fa-user-check text-sm"></i>

                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Your Consumer Account
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                This concern will use your registered SWD account.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 sm:p-6">

                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Account Number
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $consumer?->account_number ?: 'Not available' }}
                        </p>

                    </div>


                    <div class="rounded-xl bg-slate-50 p-4">

                        <p class="text-xs text-slate-500">
                            Account Holder
                        </p>

                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $consumer?->full_name ?: auth()->user()->full_name }}
                        </p>

                    </div>

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- STEP 3 — LOCATION --}}
            {{-- ===================================================== --}}

            <section id="engineeringLocationSection"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700">
                            3
                        </div>

                        <div>

                            <h2 class="font-semibold text-slate-900">
                                Where Is the Problem?
                            </h2>

                            <p class="mt-1 text-sm text-slate-500">
                                Select the location or enter the service address.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="space-y-5 p-5 sm:p-6">

                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <p class="text-sm font-semibold text-slate-700">
                                Pin Location
                                <span class="font-normal text-slate-400">
                                    (Optional)
                                </span>
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Click the map or drag the marker.
                            </p>

                        </div>


                        <button type="button"
                            id="locateMe"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">

                            <i class="fas fa-location-crosshairs"></i>

                            Use My Location

                        </button>

                    </div>


                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">

                        <div id="complaintMap"
                            class="h-72 w-full sm:h-80">
                        </div>

                    </div>


                    <p id="mapAddressStatus"
                        class="text-xs leading-5 text-slate-500">

                        Select a point on the map or enter the address below.

                    </p>


                    <div>

                        <label for="address"
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Service Address

                            <span class="text-red-500">*</span>

                        </label>

                        <input type="text"
                            name="address"
                            id="address"
                            value="{{ old('address') }}"
                            placeholder="Enter the address where the problem is located"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('address')

                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <div>

                        <label for="landmark"
                            class="mb-2 block text-sm font-semibold text-slate-700">

                            Nearby Landmark

                            <span class="font-normal text-slate-400">
                                (Optional)
                            </span>

                        </label>

                        <input type="text"
                            id="landmark"
                            name="landmark"
                            value="{{ old('landmark') }}"
                            maxlength="255"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('landmark')

                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>

                        @enderror

                    </div>


                    <input type="hidden"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude') }}">

                    <input type="hidden"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude') }}">

                </div>

            </section>


            {{-- Hidden compatibility element --}}
            <span id="stepThreeLabel" class="hidden">Location</span>


            {{-- ===================================================== --}}
            {{-- STEP 4 — PHOTO --}}
            {{-- ===================================================== --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">

                    <div class="flex items-start gap-3">

                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-sm font-bold text-amber-700">
                            4
                        </div>

                        <div>

                            <h2 id="evidenceTitle"
                                class="font-semibold text-slate-900">
                                Add a Photo
                            </h2>

                            <p id="evidenceSubtitle"
                                class="mt-1 text-sm text-slate-500">
                                A photo can help SWD personnel understand the concern.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <label for="photo"
                        id="photoDropArea"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-xl border-2 border-dashed border-slate-200 bg-slate-50 px-5 py-7 text-center transition hover:border-sky-300 hover:bg-sky-50">

                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-white text-sky-600">

                            <i id="evidenceIcon"
                                class="fas fa-camera"></i>

                        </div>

                        <p id="evidenceUploadTitle"
                            class="mt-3 text-sm font-semibold text-slate-700">
                            Upload Photo
                        </p>

                        <p id="evidenceUploadHelp"
                            class="mt-1 text-xs text-slate-500">
                            JPG, PNG or WEBP · Maximum 5 MB
                        </p>

                        <input type="file"
                            id="photo"
                            name="photo"
                            accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            class="sr-only">

                    </label>


                    <div id="photoPreviewContainer"
                        class="mt-4 hidden">

                        <div
                            class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center">

                            <div
                                class="h-24 w-full shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-white sm:w-24">

                                <img id="photoPreview"
                                    src=""
                                    alt="Selected photo"
                                    class="h-full w-full object-cover">

                            </div>

                            <div class="min-w-0 flex-1">

                                <p id="photoName"
                                    class="truncate text-sm font-semibold text-slate-700">
                                </p>

                                <p id="photoSize"
                                    class="mt-1 text-xs text-slate-500">
                                </p>

                                <button type="button"
                                    id="removePhoto"
                                    class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-red-600">

                                    <i class="fas fa-trash-can"></i>

                                    Remove

                                </button>

                            </div>

                        </div>

                    </div>


                    @error('photo')

                        <p class="mt-2 text-sm font-medium text-red-600">
                            {{ $message }}
                        </p>

                    @enderror

                </div>

            </section>


            {{-- ===================================================== --}}
            {{-- SUBMIT --}}
            {{-- ===================================================== --}}

            <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-info mt-0.5 text-sky-600"></i>

                    <div>

                        <p class="text-sm font-semibold text-sky-900">
                            Before You Submit
                        </p>

                        <p id="beforeSubmitText"
                            class="mt-1 text-xs leading-5 text-sky-800">

                            Check your concern, complaint type, location, and photo.
                            SWD personnel will review your submission.

                        </p>

                    </div>

                </div>

            </div>


            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a href="{{ route('consumer.complaints.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">

                    Cancel

                </a>


                <button type="submit"
                    :disabled="submitting"
                    class="inline-flex min-w-[180px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-70">

                    <span x-show="!submitting"
                        class="inline-flex items-center gap-2">

                        <i class="fas fa-paper-plane"></i>

                        Submit Complaint

                    </span>

                    <span x-show="submitting"
                        x-cloak
                        class="inline-flex items-center gap-2">

                        <i class="fas fa-spinner animate-spin"></i>

                        Submitting...

                    </span>

                </button>

            </div>

        </form>

    </div>


    @push('scripts')

        <link rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {

                /*
                |--------------------------------------------------------------------------
                | ELEMENTS
                |--------------------------------------------------------------------------
                */

                const divisions = @json($divisions);

                const oldComplaintType =
                    @json(old('complaint_category_id'));

                const aiAnalysisTokenInput =
                    document.getElementById('ai_analysis_token');

                const divisionSelect =
                    document.getElementById('division_id');

                const complaintTypeSelect =
                    document.getElementById('complaint_category_id');

                const classificationSection =
                    document.getElementById('classificationSection');

                const commercialAccountSection =
                    document.getElementById('commercialAccountSection');

                const engineeringLocationSection =
                    document.getElementById('engineeringLocationSection');

                const description =
                    document.getElementById('description');

                const descriptionCount =
                    document.getElementById('descriptionCount');

                const addressInput =
                    document.getElementById('address');

                const evidenceTitle =
                    document.getElementById('evidenceTitle');

                const evidenceSubtitle =
                    document.getElementById('evidenceSubtitle');

                const evidenceUploadTitle =
                    document.getElementById('evidenceUploadTitle');

                const evidenceUploadHelp =
                    document.getElementById('evidenceUploadHelp');

                const evidenceIcon =
                    document.getElementById('evidenceIcon');

                const beforeSubmitText =
                    document.getElementById('beforeSubmitText');

                const photoInput =
                    document.getElementById('photo');

                const photoPreviewContainer =
                    document.getElementById('photoPreviewContainer');

                const photoPreview =
                    document.getElementById('photoPreview');

                const photoName =
                    document.getElementById('photoName');

                const photoSize =
                    document.getElementById('photoSize');

                const removePhoto =
                    document.getElementById('removePhoto');

                const mapElement =
                    document.getElementById('complaintMap');

                const latitudeInput =
                    document.getElementById('latitude');

                const longitudeInput =
                    document.getElementById('longitude');

                const mapAddressStatus =
                    document.getElementById('mapAddressStatus');

                const locateMe =
                    document.getElementById('locateMe');

                const analyzeConcern =
                    document.getElementById('analyzeConcern');

                const analyzeConcernText =
                    document.getElementById('analyzeConcernText');

                const aiAnalysisCard =
                    document.getElementById('aiAnalysisCard');

                const aiAnalysisError =
                    document.getElementById('aiAnalysisError');

                const aiComplaintType =
                    document.getElementById('aiComplaintType');

                const aiDivision =
                    document.getElementById('aiDivision');

                const aiUrgency =
                    document.getElementById('aiUrgency');

                const aiAnalysisStatus =
                    document.getElementById('aiAnalysisStatus');

                const aiSupportingEvidenceSection =
                    document.getElementById('aiSupportingEvidenceSection');

                const aiSupportingSummary =
                    document.getElementById('aiSupportingSummary');

                const aiSupportingIndicatorsSection =
                    document.getElementById('aiSupportingIndicatorsSection');

                const aiSupportingIndicators =
                    document.getElementById('aiSupportingIndicators');

                const aiSignalsSection =
                    document.getElementById('aiSignalsSection');

                const aiSignals =
                    document.getElementById('aiSignals');

                const aiReviewSection =
                    document.getElementById('aiReviewSection');

                const aiReview =
                    document.getElementById('aiReview');

                const aiMatchWarning =
                    document.getElementById('aiMatchWarning');

                const useAiClassification =
                    document.getElementById('useAiClassification');


                let matchedAiCategory = null;

                let map = null;
                let marker = null;
                let mapInitialized = false;


                /*
                |--------------------------------------------------------------------------
                | DESCRIPTION
                |--------------------------------------------------------------------------
                */

                function updateDescriptionCount() {

                    if (!description || !descriptionCount) {
                        return;
                    }

                    descriptionCount.textContent =
                        description.value.length + ' / 5000';

                }


                updateDescriptionCount();


                description?.addEventListener('input', function() {

                    updateDescriptionCount();

                    /*
                     * If the consumer changes what they wrote after AI analysis,
                     * invalidate the previous analysis token.
                     */

                    if (aiAnalysisTokenInput) {
                        aiAnalysisTokenInput.value = '';
                    }

                });


                /*
                |--------------------------------------------------------------------------
                | DIVISION / COMPLAINT TYPES
                |--------------------------------------------------------------------------
                */

                function getSelectedDivision() {

                    if (!divisionSelect?.value) {
                        return null;
                    }

                    return divisions.find(function(item) {

                        return String(item.id) ===
                            String(divisionSelect.value);

                    }) || null;

                }


                function getSelectedDivisionName() {

                    const division = getSelectedDivision();

                    if (!division) {
                        return '';
                    }

                    return String(
                        division.name || ''
                    ).trim().toLowerCase();

                }


                function isEngineeringDivision() {

                    return getSelectedDivisionName()
                        .includes('engineering');

                }


                function isCommercialDivision() {

                    return getSelectedDivisionName()
                        .includes('commercial');

                }


                function getComplaintTypes(division) {

                    if (!division) {
                        return [];
                    }

                    return division.complaint_types ??
                        division.complaintTypes ??
                        [];

                }


                function populateComplaintTypes(
                    divisionId,
                    selectedId = null
                ) {

                    if (!complaintTypeSelect) {
                        return;
                    }

                    complaintTypeSelect.innerHTML = '';

                    if (!divisionId) {

                        complaintTypeSelect.disabled = true;

                        complaintTypeSelect.innerHTML =
                            '<option value="">Select service division first</option>';

                        return;

                    }


                    const division =
                        divisions.find(function(item) {

                            return String(item.id) ===
                                String(divisionId);

                        });


                    if (!division) {

                        complaintTypeSelect.disabled = true;

                        complaintTypeSelect.innerHTML =
                            '<option value="">No complaint types available</option>';

                        return;

                    }


                    const complaintTypes =
                        getComplaintTypes(division);


                    if (!complaintTypes.length) {

                        complaintTypeSelect.disabled = true;

                        complaintTypeSelect.innerHTML =
                            '<option value="">No complaint types available</option>';

                        return;

                    }


                    complaintTypeSelect.disabled = false;


                    const placeholder =
                        document.createElement('option');

                    placeholder.value = '';

                    placeholder.textContent =
                        'Select complaint type';

                    complaintTypeSelect.appendChild(
                        placeholder
                    );


                    complaintTypes.forEach(function(type) {

                        const option =
                            document.createElement('option');

                        option.value = type.id;

                        option.textContent = type.name;


                        if (
                            selectedId !== null &&
                            String(selectedId) ===
                            String(type.id)
                        ) {

                            option.selected = true;

                        }


                        complaintTypeSelect.appendChild(
                            option
                        );

                    });

                }


                function updateDivisionForm() {

                    if (!addressInput) {
                        return;
                    }


                    if (isEngineeringDivision()) {

                        commercialAccountSection?.classList.add(
                            'hidden'
                        );

                        engineeringLocationSection?.classList.remove(
                            'hidden'
                        );

                        addressInput.required = true;

                        evidenceTitle.textContent =
                            'Add a Photo';

                        evidenceSubtitle.textContent =
                            'A photo can help SWD personnel understand the concern.';

                        evidenceUploadTitle.textContent =
                            'Upload Photo';

                        evidenceUploadHelp.textContent =
                            'JPG, PNG or WEBP · Maximum 5 MB';

                        evidenceIcon.className =
                            'fas fa-camera';

                        beforeSubmitText.textContent =
                            'Check your concern, complaint type, service location, and photo. SWD personnel will review your submission.';


                        initializeMap();


                        setTimeout(function() {

                            if (map) {
                                map.invalidateSize();
                            }

                        }, 250);


                    } else if (isCommercialDivision()) {

                        commercialAccountSection?.classList.remove(
                            'hidden'
                        );

                        engineeringLocationSection?.classList.add(
                            'hidden'
                        );

                        addressInput.required = false;

                        evidenceTitle.textContent =
                            'Supporting Photo';

                        evidenceSubtitle.textContent =
                            'You may attach a bill, meter photo, or other useful image.';

                        evidenceUploadTitle.textContent =
                            'Upload Supporting Photo';

                        evidenceUploadHelp.textContent =
                            'JPG, PNG or WEBP · Maximum 5 MB';

                        evidenceIcon.className =
                            'fas fa-file-image';

                        beforeSubmitText.textContent =
                            'Check your concern and complaint type before submitting. Your registered consumer account will be linked automatically.';


                    } else {

                        commercialAccountSection?.classList.add(
                            'hidden'
                        );

                        engineeringLocationSection?.classList.remove(
                            'hidden'
                        );

                        addressInput.required = false;

                    }

                }


                divisionSelect?.addEventListener(
                    'change',
                    function() {

                        populateComplaintTypes(
                            this.value
                        );

                        updateDivisionForm();

                    }
                );


                complaintTypeSelect?.addEventListener(
                    'change',
                    updateDivisionForm
                );


                if (divisionSelect?.value) {

                    populateComplaintTypes(
                        divisionSelect.value,
                        oldComplaintType
                    );

                }


                updateDivisionForm();


                /*
                |--------------------------------------------------------------------------
                | AI HELP
                |--------------------------------------------------------------------------
                */

                function getNestedValue(object, paths) {

                    for (const path of paths) {

                        const parts = path.split('.');

                        let value = object;


                        for (const part of parts) {

                            if (
                                value === null ||
                                value === undefined ||
                                typeof value !== 'object' ||
                                !(part in value)
                            ) {

                                value = undefined;
                                break;

                            }

                            value = value[part];

                        }


                        if (
                            value !== undefined &&
                            value !== null &&
                            value !== ''
                        ) {

                            return value;

                        }

                    }

                    return null;

                }


                function normalizeSignals(value) {

                    if (Array.isArray(value)) {

                        return value
                            .map(function(item) {

                                if (typeof item === 'string') {
                                    return item;
                                }

                                if (
                                    item &&
                                    typeof item === 'object'
                                ) {

                                    return item.label ||
                                        item.name ||
                                        item.signal ||
                                        item.reason ||
                                        item.message ||
                                        '';

                                }

                                return '';

                            })
                            .filter(Boolean);

                    }


                    if (
                        typeof value === 'string' &&
                        value.trim() !== ''
                    ) {

                        return [value.trim()];

                    }

                    return [];

                }


                function renderAiAnalysis(payload) {

                    const analysis =
                        payload.analysis || {};

                    const matchedCategory =
                        payload.matched_category || null;

                    matchedAiCategory =
                        matchedCategory;


                    const predictedType =
                        getNestedValue(
                            analysis,
                            [
                                'classification.complaint_type',
                                'complaint_type',
                                'classification.type',
                                'predicted_type'
                            ]
                        ) ||
                        matchedCategory?.name ||
                        'Not identified';


                    const urgency =
                        getNestedValue(
                            analysis,
                            [
                                'urgency.level',
                                'urgency',
                                'priority',
                                'risk_level'
                            ]
                        ) ||
                        'Not provided';


                    const supportingSummary =
                        getNestedValue(
                            analysis,
                            [
                                'supporting_evidence.summary'
                            ]
                        );


                    const supportingIndicators =
                        getNestedValue(
                            analysis,
                            [
                                'supporting_evidence.indicators'
                            ]
                        );


                    const urgencyReasons =
                        getNestedValue(
                            analysis,
                            [
                                'urgency.reasons'
                            ]
                        );


                    const reviewReasons =
                        getNestedValue(
                            analysis,
                            [
                                'review.reasons'
                            ]
                        );


                    aiComplaintType.textContent =
                        String(predictedType);

                    aiDivision.textContent =
                        matchedCategory?.division_name ||
                        'No active division matched';

                    aiUrgency.textContent =
                        String(urgency);


                    /*
                     * Keep technical fields updated for compatibility,
                     * but they are hidden from the consumer.
                     */

                    if (aiAnalysisStatus) {
                        aiAnalysisStatus.textContent =
                            'SWD personnel will review this submission.';
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SIMPLE EXPLANATION
                    |--------------------------------------------------------------------------
                    */

                    aiSupportingSummary.textContent = '';

                    aiSupportingIndicators.innerHTML = '';


                    const indicators =
                        Array.isArray(supportingIndicators)
                            ? supportingIndicators
                            : [];


                    if (
                        typeof supportingSummary === 'string' &&
                        supportingSummary.trim() !== ''
                    ) {

                        aiSupportingSummary.textContent =
                            supportingSummary.trim();

                        aiSupportingEvidenceSection.classList.remove(
                            'hidden'
                        );

                    } else {

                        aiSupportingEvidenceSection.classList.add(
                            'hidden'
                        );

                    }


                    if (indicators.length > 0) {

                        indicators.forEach(function(indicator) {

                            const item =
                                document.createElement('li');

                            item.className =
                                'flex items-start gap-2';


                            const icon =
                                document.createElement('span');

                            icon.className =
                                'mt-0.5 text-emerald-600';

                            icon.innerHTML =
                                '<i class="fas fa-check-circle text-[10px]"></i>';


                            const text =
                                document.createElement('span');

                            text.textContent =
                                String(indicator);


                            item.appendChild(icon);
                            item.appendChild(text);

                            aiSupportingIndicators.appendChild(
                                item
                            );

                        });


                        aiSupportingIndicatorsSection.classList.remove(
                            'hidden'
                        );

                    } else {

                        aiSupportingIndicatorsSection.classList.add(
                            'hidden'
                        );

                    }


                    /*
                     * Technical urgency/review data stays available
                     * but hidden from the consumer UI.
                     */

                    if (aiSignals) {

                        aiSignals.innerHTML = '';

                        normalizeSignals(
                            urgencyReasons
                        ).forEach(function(reason) {

                            const item =
                                document.createElement('li');

                            item.textContent = reason;

                            aiSignals.appendChild(item);

                        });

                    }


                    if (aiReview) {

                        aiReview.textContent =
                            normalizeSignals(
                                reviewReasons
                            ).join(' ');

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | CATEGORY MATCH
                    |--------------------------------------------------------------------------
                    */

                    if (matchedCategory) {

                        aiMatchWarning.classList.add(
                            'hidden'
                        );

                        useAiClassification.disabled =
                            false;

                    } else {

                        aiMatchWarning.classList.remove(
                            'hidden'
                        );

                        useAiClassification.disabled =
                            true;

                    }


                    aiAnalysisCard.classList.remove(
                        'hidden'
                    );


                    aiAnalysisCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest'
                    });

                }


                analyzeConcern?.addEventListener(
                    'click',
                    async function() {

                        const complaintDescription =
                            description.value.trim();


                        aiAnalysisError.classList.add(
                            'hidden'
                        );

                        aiAnalysisError.textContent = '';


                        if (complaintDescription.length < 10) {

                            aiAnalysisError.textContent =
                                'Please tell us a little more about what happened before using AI assistance.';

                            aiAnalysisError.classList.remove(
                                'hidden'
                            );

                            description.focus();

                            return;

                        }


                        analyzeConcern.disabled = true;

                        analyzeConcernText.textContent =
                            'Checking Your Concern...';


                        const icon =
                            analyzeConcern.querySelector('i');


                        if (icon) {

                            icon.className =
                                'fas fa-spinner fa-spin';

                        }


                        try {

                            const response =
                                await fetch(
                                    @json(route('consumer.complaints.analyze')),
                                    {
                                        method: 'POST',

                                        headers: {
                                            'Accept': 'application/json',
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN':
                                                document
                                                    .querySelector(
                                                        'meta[name="csrf-token"]'
                                                    )
                                                    ?.getAttribute('content') ||
                                                @json(csrf_token())
                                        },

                                        body: JSON.stringify({
                                            description:
                                                complaintDescription
                                        })
                                    }
                                );


                            const payload =
                                await response.json();


                            if (
                                !response.ok ||
                                !payload.success
                            ) {

                                let message =
                                    payload.message ||
                                    'AI assistance is temporarily unavailable. You can choose the complaint type manually.';


                                if (
                                    response.status === 422 &&
                                    payload.errors?.description
                                ) {

                                    message =
                                        payload.errors.description[0];

                                }


                                throw new Error(message);

                            }


                            if (
                                aiAnalysisTokenInput &&
                                payload.analysis_token
                            ) {

                                aiAnalysisTokenInput.value =
                                    payload.analysis_token;

                            }


                            renderAiAnalysis(
                                payload
                            );


                        } catch (error) {

                            console.error(
                                'AI complaint analysis error:',
                                error
                            );


                            matchedAiCategory = null;


                            if (aiAnalysisTokenInput) {
                                aiAnalysisTokenInput.value = '';
                            }


                            aiAnalysisError.textContent =
                                error.message ||
                                'AI assistance is temporarily unavailable. You can choose the complaint type manually.';

                            aiAnalysisError.classList.remove(
                                'hidden'
                            );


                        } finally {

                            analyzeConcern.disabled = false;

                            analyzeConcernText.textContent =
                                'Help Identify My Concern';


                            if (icon) {

                                icon.className =
                                    'fas fa-wand-magic-sparkles';

                            }

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | USE AI SUGGESTION
                |--------------------------------------------------------------------------
                */

                useAiClassification?.addEventListener(
                    'click',
                    function() {

                        if (!matchedAiCategory) {
                            return;
                        }


                        divisionSelect.value =
                            String(
                                matchedAiCategory.division_id
                            );


                        populateComplaintTypes(
                            matchedAiCategory.division_id,
                            matchedAiCategory.id
                        );


                        updateDivisionForm();


                        complaintTypeSelect.dispatchEvent(
                            new Event('change')
                        );


                        classificationSection.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });


                        /*
                         * Brief confirmation on button.
                         */

                        const originalHtml =
                            useAiClassification.innerHTML;


                        useAiClassification.innerHTML =
                            '<i class="fas fa-check-circle"></i> Complaint Type Selected';


                        useAiClassification.classList.remove(
                            'bg-sky-700',
                            'hover:bg-sky-800'
                        );

                        useAiClassification.classList.add(
                            'bg-emerald-600'
                        );


                        setTimeout(function() {

                            useAiClassification.innerHTML =
                                originalHtml;

                            useAiClassification.classList.remove(
                                'bg-emerald-600'
                            );

                            useAiClassification.classList.add(
                                'bg-sky-700',
                                'hover:bg-sky-800'
                            );

                        }, 1800);

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | PHOTO
                |--------------------------------------------------------------------------
                */

                function formatFileSize(bytes) {

                    if (bytes < 1024) {
                        return bytes + ' bytes';
                    }


                    if (bytes < 1024 * 1024) {

                        return (
                            bytes / 1024
                        ).toFixed(1) + ' KB';

                    }


                    return (
                        bytes /
                        (1024 * 1024)
                    ).toFixed(1) + ' MB';

                }


                function clearPhotoPreview() {

                    if (!photoInput) {
                        return;
                    }


                    photoInput.value = '';


                    if (photoPreview) {
                        photoPreview.src = '';
                    }


                    if (photoName) {
                        photoName.textContent = '';
                    }


                    if (photoSize) {
                        photoSize.textContent = '';
                    }


                    photoPreviewContainer?.classList.add(
                        'hidden'
                    );

                }


                photoInput?.addEventListener(
                    'change',
                    function() {

                        const file =
                            this.files?.[0];


                        if (!file) {

                            clearPhotoPreview();

                            return;

                        }


                        if (
                            file.size >
                            5 * 1024 * 1024
                        ) {

                            alert(
                                'The selected photo must not exceed 5 MB.'
                            );

                            clearPhotoPreview();

                            return;

                        }


                        if (
                            ![
                                'image/jpeg',
                                'image/png',
                                'image/webp'
                            ].includes(file.type)
                        ) {

                            alert(
                                'Please select a JPG, JPEG, PNG, or WEBP image.'
                            );

                            clearPhotoPreview();

                            return;

                        }


                        const reader =
                            new FileReader();


                        reader.onload =
                            function(event) {

                                photoPreview.src =
                                    event.target.result;

                                photoName.textContent =
                                    file.name;

                                photoSize.textContent =
                                    formatFileSize(
                                        file.size
                                    );

                                photoPreviewContainer.classList.remove(
                                    'hidden'
                                );

                            };


                        reader.readAsDataURL(
                            file
                        );

                    }
                );


                removePhoto?.addEventListener(
                    'click',
                    clearPhotoPreview
                );


                /*
                |--------------------------------------------------------------------------
                | MAP
                |--------------------------------------------------------------------------
                */

                function initializeMap() {

                    if (
                        mapInitialized ||
                        !mapElement ||
                        typeof L === 'undefined'
                    ) {

                        return;

                    }


                    const defaultLat = 10.9447;
                    const defaultLng = 123.4247;


                    map =
                        L.map(
                            'complaintMap',
                            {
                                scrollWheelZoom: false
                            }
                        ).setView(
                            [
                                defaultLat,
                                defaultLng
                            ],
                            14
                        );


                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                        {
                            maxZoom: 19,
                            attribution:
                                '&copy; OpenStreetMap contributors'
                        }
                    ).addTo(map);


                    map.on(
                        'click',
                        function(event) {

                            setLocation(
                                event.latlng.lat,
                                event.latlng.lng,
                                true
                            );

                        }
                    );


                    const oldLat =
                        parseFloat(
                            latitudeInput?.value
                        );

                    const oldLng =
                        parseFloat(
                            longitudeInput?.value
                        );


                    if (
                        !Number.isNaN(oldLat) &&
                        !Number.isNaN(oldLng)
                    ) {

                        map.setView(
                            [
                                oldLat,
                                oldLng
                            ],
                            17
                        );


                        setLocation(
                            oldLat,
                            oldLng,
                            false
                        );

                    }


                    mapInitialized = true;


                    requestAnimationFrame(function() {

                        requestAnimationFrame(function() {

                            map.invalidateSize();

                        });

                    });

                }


                async function reverseGeocode(
                    lat,
                    lng
                ) {

                    if (!addressInput) {
                        return;
                    }


                    if (mapAddressStatus) {

                        mapAddressStatus.textContent =
                            'Finding the address...';

                    }


                    try {

                        const url =
                            'https://nominatim.openstreetmap.org/reverse' +
                            '?format=jsonv2' +
                            '&lat=' +
                            encodeURIComponent(lat) +
                            '&lon=' +
                            encodeURIComponent(lng) +
                            '&zoom=18' +
                            '&addressdetails=1' +
                            '&accept-language=en';


                        const response =
                            await fetch(
                                url,
                                {
                                    headers: {
                                        'Accept':
                                            'application/json'
                                    }
                                }
                            );


                        if (!response.ok) {

                            throw new Error(
                                'Unable to identify the address.'
                            );

                        }


                        const data =
                            await response.json();


                        if (!data?.display_name) {

                            throw new Error(
                                'No readable address found.'
                            );

                        }


                        addressInput.value =
                            data.display_name;


                        if (mapAddressStatus) {

                            mapAddressStatus.textContent =
                                'Address found. You may edit it below if needed.';

                        }


                    } catch (error) {

                        console.error(
                            'Reverse geocoding error:',
                            error
                        );


                        if (mapAddressStatus) {

                            mapAddressStatus.textContent =
                                'We could not identify the address automatically. Please enter it manually.';

                        }

                    }

                }


                function setLocation(
                    lat,
                    lng,
                    shouldReverseGeocode = true
                ) {

                    if (
                        !latitudeInput ||
                        !longitudeInput ||
                        !map
                    ) {

                        return;

                    }


                    latitudeInput.value =
                        Number(lat).toFixed(7);

                    longitudeInput.value =
                        Number(lng).toFixed(7);


                    if (marker) {

                        marker.setLatLng([
                            lat,
                            lng
                        ]);

                    } else {

                        marker =
                            L.marker(
                                [
                                    lat,
                                    lng
                                ],
                                {
                                    draggable: true
                                }
                            ).addTo(map);


                        marker.on(
                            'dragend',
                            function(event) {

                                const position =
                                    event.target.getLatLng();


                                setLocation(
                                    position.lat,
                                    position.lng,
                                    true
                                );

                            }
                        );

                    }


                    if (shouldReverseGeocode) {

                        reverseGeocode(
                            lat,
                            lng
                        );

                    }

                }


                locateMe?.addEventListener(
                    'click',
                    function() {

                        if (!navigator.geolocation) {

                            alert(
                                'Location services are not supported by your browser.'
                            );

                            return;

                        }


                        initializeMap();


                        const button = this;


                        button.disabled = true;

                        button.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Locating...';


                        navigator.geolocation.getCurrentPosition(

                            function(position) {

                                const lat =
                                    position.coords.latitude;

                                const lng =
                                    position.coords.longitude;


                                map.setView(
                                    [
                                        lat,
                                        lng
                                    ],
                                    17
                                );


                                setLocation(
                                    lat,
                                    lng,
                                    true
                                );


                                button.disabled = false;

                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use My Location';

                            },


                            function(error) {

                                console.error(
                                    'Geolocation error:',
                                    error
                                );


                                alert(
                                    'Unable to get your location. Please allow location access or select the location manually on the map.'
                                );


                                button.disabled = false;

                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use My Location';

                            },


                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }

                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | MAP RESPONSIVENESS
                |--------------------------------------------------------------------------
                */

                if (
                    mapElement &&
                    window.ResizeObserver
                ) {

                    const mapResizeObserver =
                        new ResizeObserver(function() {

                            if (map) {
                                map.invalidateSize();
                            }

                        });


                    mapResizeObserver.observe(
                        mapElement
                    );

                }


                window.addEventListener(
                    'resize',
                    function() {

                        if (
                            map &&
                            engineeringLocationSection &&
                            !engineeringLocationSection
                                .classList
                                .contains('hidden')
                        ) {

                            map.invalidateSize();

                        }

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | INITIALIZE MAP
                |--------------------------------------------------------------------------
                */

                initializeMap();


                setTimeout(function() {

                    if (
                        isEngineeringDivision() &&
                        map
                    ) {

                        map.invalidateSize();

                    }

                }, 400);

            });
        </script>

    @endpush

@endsection
