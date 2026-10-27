@extends('consumer.layouts.app')

@section('title', 'Report a Water Service Concern')

@section('content')
    @php
        $consumer = auth()->user()->consumer;
    @endphp

    <div class="mx-auto max-w-5xl space-y-5">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-wider text-sky-600">
                    Consumer Service
                </p>
                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Report a Water Service Concern
                </h1>
                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Tell us what happened in your own words. You do not need to know the official SWD complaint type.
                </p>
            </div>

            <a href="{{ route('consumer.complaints.index') }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                <i class="fas fa-arrow-left text-xs"></i>
                My Complaints
            </a>
        </div>

        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4" role="alert">
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

        <form id="complaintForm" method="POST"
            action="{{ route('consumer.complaints.store') }}"
            enctype="multipart/form-data"
            class="space-y-5"
            x-data="{ submitting: false }"
            @submit="submitting = true">

            @csrf

            <input type="hidden" name="ai_analysis_token" id="ai_analysis_token" value="">

            {{-- STEP 1: CONCERN --}}
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-sky-50 text-sm font-bold text-sky-700">
                            1
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">Tell Us What Happened</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Describe the problem or service you need using your own words.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="mb-2 flex items-center justify-between gap-4">
                        <label for="description" class="text-sm font-semibold text-slate-700">
                            Your Concern <span class="text-red-500">* required</span>
                        </label>
                        <span id="descriptionCount" class="text-xs text-slate-400">0 / 5000</span>
                    </div>

                    <textarea id="description" name="description" rows="3" maxlength="5000" required
                        placeholder="Example: Wala kami tubig halin pa sang aga.&#10;&#10;Tell us what you noticed and when it started."
                        class="w-full resize-y rounded-xl border-slate-300 px-4 py-3 text-sm leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>

                    @error('description')
                        <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    <div class="mt-3 flex items-start gap-2">
                        <i class="fas fa-language mt-0.5 text-sm text-sky-600"></i>
                        <p class="text-xs leading-5 text-slate-500">
                            You may explain naturally in English, Filipino, Hiligaynon, or mixed language.
                        </p>
                    </div>

                    {{-- AI HELP --}}
                    <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50/60 p-4">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sky-700">
                                    <i class="fas fa-wand-magic-sparkles"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">
                                        Not sure what complaint type this is?
                                    </p>
                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Let the system help match your concern with an official SWD complaint type.
                                    </p>
                                </div>
                            </div>

                            <button type="button" id="analyzeConcern"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-60">
                                <i class="fas fa-wand-magic-sparkles"></i>
                                <span id="analyzeConcernText">Help Identify My Concern</span>
                            </button>
                        </div>
                    </div>

                    <div id="aiAnalysisError"
                        class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"
                        role="alert"></div>

                    {{-- AI RESULT --}}
                    <div id="aiAnalysisCard"
                        class="mt-4 hidden overflow-hidden rounded-xl border border-sky-200 bg-white">

                        <div class="border-b border-sky-100 bg-sky-50 px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sky-700">
                                    <i class="fas fa-wand-magic-sparkles text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">AI Assistance</p>
                                    <p class="text-xs text-slate-500">Based on what you described.</p>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4 p-4 sm:p-5">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Suggested SWD Complaint Type
                                </p>
                                <p id="aiComplaintType" class="mt-1 text-lg font-bold text-slate-900">—</p>
                            </div>

                            <div class="rounded-xl bg-slate-50 p-3">
                                <p class="text-xs text-slate-500">Handled By</p>
                                <p id="aiDivision" class="mt-1 text-sm font-semibold text-slate-800">—</p>
                            </div>

                            <div id="aiSupportingEvidenceSection"
                                class="hidden rounded-xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Why this was suggested
                                </p>
                                <p id="aiSupportingSummary" class="mt-2 text-sm leading-6 text-slate-600"></p>
                                <div id="aiSupportingIndicatorsSection" class="mt-3 hidden">
                                    <ul id="aiSupportingIndicators" class="space-y-1.5 text-xs leading-5 text-slate-600"></ul>
                                </div>
                            </div>

                            {{-- Hidden compatibility elements --}}
                            <div class="hidden">
                                <p id="aiAnalysisStatus"></p>
                                <div id="aiSignalsSection"><ul id="aiSignals"></ul></div>
                                <div id="aiReviewSection"><p id="aiReview"></p></div>
                            </div>

                            <div id="aiMatchWarning"
                                class="hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs leading-5 text-amber-800">
                                We could not automatically match this concern with an active SWD complaint type.
                                Please choose the complaint type manually below.
                            </div>

                            <div class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">
                                <p class="text-xs leading-5 text-slate-500">
                                    Check the suggestion before continuing.
                                </p>
                                <button type="button" id="useAiClassification"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-50"
                                    disabled>
                                    <i class="fas fa-check"></i>
                                    Use This Complaint Type
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- STEP 2: COMPLAINT TYPE --}}
            <section id="classificationSection" tabindex="-1"
                aria-label="SWD Complaint Type selection"
                class="scroll-mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white transition-shadow duration-300">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-sm font-bold text-blue-700">
                            2
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">SWD Complaint Type</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Confirm the complaint type suggested above or choose it manually.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div>
                            <label for="division_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Service Division <span class="text-red-500">* required</span>
                            </label>

                            <select id="division_id" name="division_id" required
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Select service division</option>
                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                        {{ $division->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('division_id')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="complaint_category_id" class="mb-2 block text-sm font-semibold text-slate-700">
                                Complaint Type <span class="text-red-500">* required</span>
                            </label>

                            <select id="complaint_category_id" name="complaint_category_id" required disabled
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500 disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-400">
                                <option value="">Select service division first</option>
                            </select>

                            @error('complaint_category_id')
                                <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </section>

            {{-- COMMERCIAL ACCOUNT --}}
            <section id="commercialAccountSection"
                class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-violet-50 text-violet-700">
                            <i class="fas fa-user-check text-sm"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">Your Consumer Account</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                This concern will use your registered SWD account.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-3 p-5 sm:grid-cols-2 sm:p-6">
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">Account Number</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $consumer?->account_number ?: 'Not available' }}
                        </p>
                    </div>
                    <div class="rounded-xl bg-slate-50 p-4">
                        <p class="text-xs text-slate-500">Account Holder</p>
                        <p class="mt-1 text-sm font-semibold text-slate-800">
                            {{ $consumer?->full_name ?: auth()->user()->full_name }}
                        </p>
                    </div>
                </div>
            </section>

            {{-- STEP 3: LOCATION --}}
            <section id="engineeringLocationSection"
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white transition-shadow duration-300">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-emerald-700">
                            3
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">Where Is the Problem?</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Select the location or enter the service address.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <p class="text-sm font-semibold text-slate-700">Pin the Problem Location</p>
                                <span id="mapRequiredBadge"
                                    class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700">
                                    * Required for Engineering
                                </span>
                            </div>
                            <p class="mt-1 text-xs leading-5 text-slate-500">
                                Click the map, drag the marker, or use your current location.
                                The pin should identify where the water-service problem occurred.
                            </p>
                        </div>

                        <button type="button" id="locateMe"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                            <i class="fas fa-location-crosshairs"></i>
                            Use My Location
                        </button>
                    </div>

                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
                        <div id="complaintMap" class="h-72 w-full sm:h-80"></div>
                    </div>

                    <p id="mapAddressStatus" role="status" aria-live="polite"
                        class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800">
                        <i class="fas fa-location-dot mr-1"></i>
                        No location selected yet. Select a pin on the map before submitting an Engineering complaint.
                    </p>

                    <div>
                        <label for="address" class="mb-2 block text-sm font-semibold text-slate-700">
                            Service Address
                            <span id="addressRequiredLabel" class="text-red-500">* required for Engineering</span>
                        </label>

                        <input type="text" name="address" id="address"
                            value="{{ old('address') }}" maxlength="500"
                            placeholder="Enter the address where the problem is located"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('address')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="landmark" class="mb-2 block text-sm font-semibold text-slate-700">
                            Nearby Landmark
                            <span class="font-normal text-slate-400">(Optional)</span>
                        </label>

                        <input type="text" id="landmark" name="landmark"
                            value="{{ old('landmark') }}" maxlength="255"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('landmark')
                            <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                </div>
            </section>

            {{-- STEP 4: PHOTOS --}}
            <section id="evidenceSection" class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-sm font-bold text-amber-700">
                            4
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Supporting Photos <span class="text-red-500">* required</span>
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">
                                Add 1 to 5 photos to help SWD personnel understand the concern.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4 p-5 sm:p-6">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <button type="button" id="takePhotoButton"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">
                            <i class="fas fa-camera"></i>
                            Take Photo
                        </button>

                        <button type="button" id="choosePhotoButton"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                            <i class="fas fa-images"></i>
                            Choose from Device
                        </button>
                    </div>

                    <input type="file" id="devicePhotoInput"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        multiple class="hidden">

                    <input type="file" id="photos" name="photos[]"
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                        multiple class="hidden">

                    <div class="flex items-center justify-between gap-4">
                        <p class="text-xs leading-5 text-slate-500">
                            JPG, JPEG, PNG or WEBP · Maximum 5 MB each · 1–5 photos required
                        </p>
                        <span id="photoCount" class="shrink-0 text-xs font-semibold text-slate-500">0 / 5</span>
                    </div>

                    <div id="photoError"
                        class="hidden rounded-xl border border-red-200 bg-red-50 p-3 text-sm text-red-700"
                        role="alert"></div>

                    <div id="photoPreviewContainer" class="hidden">
                        <p class="mb-3 text-sm font-semibold text-slate-700">Selected Photos</p>
                        <div id="photoPreviewGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-5"></div>
                    </div>

                    @error('photos')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </section>

            {{-- CAMERA MODAL --}}
            <div id="cameraModal"
                class="fixed inset-0 z-[9999] hidden items-center justify-center bg-slate-950/80 p-4">
                <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">Take Photo</h3>
                            <p class="mt-1 text-xs text-slate-500">
                                Position the concern clearly inside the camera view.
                            </p>
                        </div>
                        <button type="button" id="closeCameraButton"
                            aria-label="Close camera"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>

                    <div class="bg-black">
                        <video id="cameraVideo" autoplay playsinline muted
                            class="max-h-[65vh] w-full object-contain"></video>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>

                    <div id="cameraError"
                        class="hidden border-t border-red-200 bg-red-50 px-5 py-3 text-sm text-red-700"
                        role="alert"></div>

                    <div class="flex flex-col-reverse gap-3 border-t border-slate-200 p-4 sm:flex-row sm:justify-end">
                        <button type="button" id="cancelCameraButton"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                            Cancel
                        </button>
                        <button type="button" id="capturePhotoButton"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-5 py-2.5 text-sm font-semibold text-white hover:bg-sky-800">
                            <i class="fas fa-camera"></i>
                            Capture Photo
                        </button>
                    </div>
                </div>
            </div>

            {{-- BEFORE SUBMIT --}}
            <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-circle-info mt-0.5 text-sky-600"></i>
                    <div>
                        <p class="text-sm font-semibold text-sky-900">Before You Submit</p>
                        <p id="beforeSubmitText" class="mt-1 text-xs leading-5 text-sky-800">
                            Check your concern, complaint type, location, and photos.
                            SWD personnel will review your submission.
                        </p>
                    </div>
                </div>
            </div>

            {{-- SUBMIT BUTTONS --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('consumer.complaints.index') }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </a>

                <button type="submit" id="submitComplaintButton"
                    :disabled="submitting"
                    class="inline-flex min-w-[180px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-70">

                    <span x-show="!submitting" class="inline-flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        Submit Complaint
                    </span>

                    <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
                        <i class="fas fa-spinner animate-spin"></i>
                        Submitting...
                    </span>
                </button>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            'use strict';

            const divisions = @json($divisions);
            const oldComplaintType = @json(old('complaint_category_id'));

            const form = document.getElementById('complaintForm');
            const description = document.getElementById('description');
            const descriptionCount = document.getElementById('descriptionCount');
            const aiTokenInput = document.getElementById('ai_analysis_token');

            const divisionSelect = document.getElementById('division_id');
            const complaintTypeSelect = document.getElementById('complaint_category_id');
            const classificationSection = document.getElementById('classificationSection');

            const commercialAccountSection = document.getElementById('commercialAccountSection');
            const locationSection = document.getElementById('engineeringLocationSection');
            const addressInput = document.getElementById('address');
            const addressRequiredLabel = document.getElementById('addressRequiredLabel');
            const mapRequiredBadge = document.getElementById('mapRequiredBadge');
            const mapElement = document.getElementById('complaintMap');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');
            const mapAddressStatus = document.getElementById('mapAddressStatus');
            const locateMeButton = document.getElementById('locateMe');
            const beforeSubmitText = document.getElementById('beforeSubmitText');

            const analyzeButton = document.getElementById('analyzeConcern');
            const analyzeButtonText = document.getElementById('analyzeConcernText');
            const aiAnalysisCard = document.getElementById('aiAnalysisCard');
            const aiAnalysisError = document.getElementById('aiAnalysisError');
            const aiComplaintType = document.getElementById('aiComplaintType');
            const aiDivision = document.getElementById('aiDivision');
            const aiSupportingEvidenceSection = document.getElementById('aiSupportingEvidenceSection');
            const aiSupportingSummary = document.getElementById('aiSupportingSummary');
            const aiSupportingIndicatorsSection = document.getElementById('aiSupportingIndicatorsSection');
            const aiSupportingIndicators = document.getElementById('aiSupportingIndicators');
            const aiSignals = document.getElementById('aiSignals');
            const aiReview = document.getElementById('aiReview');
            const aiAnalysisStatus = document.getElementById('aiAnalysisStatus');
            const aiMatchWarning = document.getElementById('aiMatchWarning');
            const useAiClassification = document.getElementById('useAiClassification');

            const photosInput = document.getElementById('photos');
            const devicePhotoInput = document.getElementById('devicePhotoInput');
            const takePhotoButton = document.getElementById('takePhotoButton');
            const choosePhotoButton = document.getElementById('choosePhotoButton');
            const photoPreviewContainer = document.getElementById('photoPreviewContainer');
            const photoPreviewGrid = document.getElementById('photoPreviewGrid');
            const photoCount = document.getElementById('photoCount');
            const photoError = document.getElementById('photoError');
            const evidenceSection = document.getElementById('evidenceSection');

            const cameraModal = document.getElementById('cameraModal');
            const cameraVideo = document.getElementById('cameraVideo');
            const cameraCanvas = document.getElementById('cameraCanvas');
            const cameraError = document.getElementById('cameraError');
            const closeCameraButton = document.getElementById('closeCameraButton');
            const cancelCameraButton = document.getElementById('cancelCameraButton');
            const capturePhotoButton = document.getElementById('capturePhotoButton');

            let matchedAiCategory = null;
            let analysisDescription = '';
            let map = null;
            let marker = null;
            let mapInitialized = false;
            let cameraStream = null;
            let selectedPhotos = [];
            let isSubmitting = false;

            const MAX_PHOTOS = 5;
            const MAX_PHOTO_SIZE = 5 * 1024 * 1024;
            const ALLOWED_PHOTO_TYPES = ['image/jpeg', 'image/png', 'image/webp'];

            /*
             * Smoothly center a section in the viewport.
             */
            function scrollToCenter(element, delay = 100) {
                if (!element) return;

                window.setTimeout(function () {
                    const rect = element.getBoundingClientRect();
                    const targetTop = window.scrollY + rect.top -
                        (window.innerHeight / 2) + (rect.height / 2);

                    window.scrollTo({
                        top: Math.max(0, targetTop),
                        behavior: 'smooth'
                    });
                }, delay);
            }

            function highlightSection(element, colorClasses = ['ring-sky-300']) {
                if (!element) return;

                element.classList.add('ring-2', 'ring-offset-2', ...colorClasses);

                window.setTimeout(function () {
                    element.classList.remove('ring-2', 'ring-offset-2', ...colorClasses);
                }, 2200);
            }

            /*
             * Description counter and AI-token invalidation.
             */
            function updateDescriptionCount() {
                if (description && descriptionCount) {
                    descriptionCount.textContent = description.value.length + ' / 5000';
                }
            }

            updateDescriptionCount();

            description?.addEventListener('input', function () {
                updateDescriptionCount();

                if (aiTokenInput) aiTokenInput.value = '';

                matchedAiCategory = null;
                analysisDescription = '';

                if (useAiClassification) useAiClassification.disabled = true;

                if (aiAnalysisCard) aiAnalysisCard.classList.add('hidden');
                if (aiAnalysisError) aiAnalysisError.classList.add('hidden');
            });

            /*
             * Division and complaint-type helpers.
             */
            function getSelectedDivision() {
                if (!divisionSelect?.value) return null;

                return divisions.find(function (division) {
                    return String(division.id) === String(divisionSelect.value);
                }) || null;
            }

            function getSelectedDivisionName() {
                return String(getSelectedDivision()?.name || '').trim().toLowerCase();
            }

            function isEngineeringDivision() {
                const name = getSelectedDivisionName();
                return name.includes('engineering') || name.includes('operation');
            }

            function isCommercialDivision() {
                return getSelectedDivisionName().includes('commercial');
            }

            function getComplaintTypes(division) {
                if (!division) return [];

                return division.complaint_types ?? division.complaintTypes ?? [];
            }

            function populateComplaintTypes(divisionId, selectedId = null) {
                if (!complaintTypeSelect) return;

                complaintTypeSelect.innerHTML = '';

                if (!divisionId) {
                    complaintTypeSelect.disabled = true;
                    complaintTypeSelect.add(new Option('Select service division first', ''));
                    return;
                }

                const division = divisions.find(function (item) {
                    return String(item.id) === String(divisionId);
                });

                const types = getComplaintTypes(division);

                if (!division || types.length === 0) {
                    complaintTypeSelect.disabled = true;
                    complaintTypeSelect.add(new Option('No complaint types available', ''));
                    return;
                }

                complaintTypeSelect.disabled = false;
                complaintTypeSelect.add(new Option('Select complaint type', ''));

                types.forEach(function (type) {
                    const option = new Option(type.name, type.id);

                    if (selectedId !== null && String(selectedId) === String(type.id)) {
                        option.selected = true;
                    }

                    complaintTypeSelect.add(option);
                });
            }

            function updateDivisionForm() {
                const engineering = isEngineeringDivision();
                const commercial = isCommercialDivision();

                commercialAccountSection?.classList.toggle('hidden', !commercial);

                /*
                 * The location panel remains visible for non-commercial divisions.
                 * A map pin and address are mandatory only for Engineering/Operation.
                 */
                locationSection?.classList.toggle('hidden', commercial);

                if (addressInput) {
                    addressInput.required = engineering;
                }

                if (addressRequiredLabel) {
                    addressRequiredLabel.textContent = engineering
                        ? '* required'
                        : '(Optional for this division)';

                    addressRequiredLabel.className = engineering
                        ? 'text-red-500'
                        : 'font-normal text-slate-400';
                }

                if (mapRequiredBadge) {
                    mapRequiredBadge.textContent = engineering
                        ? '* Required'
                        : 'Recommended';

                    mapRequiredBadge.className = engineering
                        ? 'rounded-full bg-red-50 px-2.5 py-1 text-xs font-semibold text-red-700'
                        : 'rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700';
                }

                if (beforeSubmitText) {
                    beforeSubmitText.textContent = engineering
                        ? 'Check your concern, complaint type, map pin, service address, and photos before submitting.'
                        : commercial
                            ? 'Check your concern and complaint type before submitting. Your registered consumer account will be linked automatically.'
                            : 'Check your concern, complaint type, and photos before submitting. Adding a map location can help SWD personnel.';
                }

                if (!commercial) {
                    initializeMap();

                    window.setTimeout(function () {
                        if (map) map.invalidateSize();
                    }, 250);
                }
            }

            divisionSelect?.addEventListener('change', function () {
                populateComplaintTypes(this.value);
                updateDivisionForm();
            });

            complaintTypeSelect?.addEventListener('change', updateDivisionForm);

            if (divisionSelect?.value) {
                populateComplaintTypes(divisionSelect.value, oldComplaintType);
            }

            updateDivisionForm();

            /*
             * AI result helpers.
             */
            function getNestedValue(object, paths) {
                for (const path of paths) {
                    let value = object;

                    for (const part of path.split('.')) {
                        if (value === null || value === undefined ||
                            typeof value !== 'object' || !(part in value)) {
                            value = undefined;
                            break;
                        }

                        value = value[part];
                    }

                    if (value !== undefined && value !== null && value !== '') {
                        return value;
                    }
                }

                return null;
            }

            function normalizeSignals(value) {
                if (Array.isArray(value)) {
                    return value.map(function (item) {
                        if (typeof item === 'string') return item;

                        if (item && typeof item === 'object') {
                            return item.label || item.name || item.signal ||
                                item.reason || item.message || '';
                        }

                        return '';
                    }).filter(Boolean);
                }

                if (typeof value === 'string' && value.trim()) {
                    return [value.trim()];
                }

                return [];
            }

            function renderAiAnalysis(payload) {
                const analysis = payload.analysis || {};
                matchedAiCategory = payload.matched_category || null;
                analysisDescription = description.value.trim();

                const predictedType = getNestedValue(analysis, [
                    'classification.complaint_type',
                    'complaint_type',
                    'classification.type',
                    'predicted_type'
                ]) || matchedAiCategory?.name || 'Not identified';

                const supportingSummary = getNestedValue(analysis, [
                    'supporting_evidence.summary'
                ]);

                const supportingIndicators = getNestedValue(analysis, [
                    'supporting_evidence.indicators'
                ]);

                const urgencyReasons = getNestedValue(analysis, ['urgency.reasons']);
                const reviewReasons = getNestedValue(analysis, ['review.reasons']);

                if (aiComplaintType) aiComplaintType.textContent = String(predictedType);

                if (aiDivision) {
                    aiDivision.textContent = matchedAiCategory?.division_name ||
                        'No active division matched';
                }

                if (aiAnalysisStatus) {
                    aiAnalysisStatus.textContent = 'SWD personnel will review this submission.';
                }

                if (aiSupportingSummary) {
                    aiSupportingSummary.textContent = supportingSummary || '';
                }

                aiSupportingEvidenceSection?.classList.toggle(
                    'hidden',
                    !(typeof supportingSummary === 'string' && supportingSummary.trim())
                );

                if (aiSupportingIndicators) {
                    aiSupportingIndicators.innerHTML = '';

                    (Array.isArray(supportingIndicators) ? supportingIndicators : [])
                        .forEach(function (indicator) {
                            const item = document.createElement('li');
                            item.className = 'flex items-start gap-2';

                            const icon = document.createElement('span');
                            icon.className = 'mt-0.5 text-emerald-600';
                            icon.innerHTML = '<i class="fas fa-check-circle text-[10px]"></i>';

                            const text = document.createElement('span');
                            text.textContent = String(indicator);

                            item.append(icon, text);
                            aiSupportingIndicators.appendChild(item);
                        });
                }

                aiSupportingIndicatorsSection?.classList.toggle(
                    'hidden',
                    !Array.isArray(supportingIndicators) || supportingIndicators.length === 0
                );

                if (aiSignals) {
                    aiSignals.innerHTML = '';

                    normalizeSignals(urgencyReasons).forEach(function (reason) {
                        const item = document.createElement('li');
                        item.textContent = reason;
                        aiSignals.appendChild(item);
                    });
                }

                if (aiReview) {
                    aiReview.textContent = normalizeSignals(reviewReasons).join(' ');
                }

                const hasMatch = Boolean(
                    matchedAiCategory &&
                    matchedAiCategory.id &&
                    matchedAiCategory.division_id
                );

                aiMatchWarning?.classList.toggle('hidden', hasMatch);

                if (useAiClassification) {
                    useAiClassification.disabled = !hasMatch;
                }

                aiAnalysisCard?.classList.remove('hidden');
                scrollToCenter(aiAnalysisCard);
            }

            analyzeButton?.addEventListener('click', async function () {
                const complaintDescription = description?.value.trim() || '';

                aiAnalysisError?.classList.add('hidden');

                if (complaintDescription.length < 10) {
                    if (aiAnalysisError) {
                        aiAnalysisError.textContent =
                            'Please tell us a little more about what happened before using AI assistance.';
                        aiAnalysisError.classList.remove('hidden');
                    }

                    description?.focus();
                    return;
                }

                analyzeButton.disabled = true;

                if (analyzeButtonText) {
                    analyzeButtonText.textContent = 'Checking Your Concern...';
                }

                const icon = analyzeButton.querySelector('i');

                if (icon) icon.className = 'fas fa-spinner fa-spin';

                try {
                    const response = await fetch(@json(route('consumer.complaints.analyze')), {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                ?.getAttribute('content') || @json(csrf_token())
                        },
                        body: JSON.stringify({ description: complaintDescription })
                    });

                    const payload = await response.json();

                    if (!response.ok || !payload.success) {
                        let message = payload.message ||
                            'AI assistance is temporarily unavailable. You can choose the complaint type manually.';

                        if (response.status === 422 && payload.errors?.description?.[0]) {
                            message = payload.errors.description[0];
                        }

                        throw new Error(message);
                    }

                    if (aiTokenInput) {
                        aiTokenInput.value = payload.analysis_token || '';
                    }

                    renderAiAnalysis(payload);
                } catch (error) {
                    console.error('AI complaint analysis error:', error);

                    matchedAiCategory = null;

                    if (aiTokenInput) aiTokenInput.value = '';

                    if (aiAnalysisError) {
                        aiAnalysisError.textContent = error.message ||
                            'AI assistance is temporarily unavailable. You can choose the complaint type manually.';
                        aiAnalysisError.classList.remove('hidden');
                    }
                } finally {
                    analyzeButton.disabled = false;

                    if (analyzeButtonText) {
                        analyzeButtonText.textContent = 'Help Identify My Concern';
                    }

                    if (icon) icon.className = 'fas fa-wand-magic-sparkles';
                }
            });

            /*
             * Use AI suggestion and focus the complaint-type section.
             */
            useAiClassification?.addEventListener('click', function () {
                if (!matchedAiCategory) return;

                /*
                 * Do not apply a result to text that changed after AI analysis.
                 */
                if (description.value.trim() !== analysisDescription) {
                    if (aiAnalysisError) {
                        aiAnalysisError.textContent =
                            'Your concern has changed. Please analyze it again before using the suggestion.';
                        aiAnalysisError.classList.remove('hidden');
                    }

                    aiTokenInput.value = '';
                    return;
                }

                divisionSelect.value = String(matchedAiCategory.division_id);

                populateComplaintTypes(
                    matchedAiCategory.division_id,
                    matchedAiCategory.id
                );

                updateDivisionForm();

                complaintTypeSelect.dispatchEvent(new Event('change', { bubbles: true }));

                scrollToCenter(classificationSection, 150);
                highlightSection(classificationSection, ['ring-sky-300']);

                const originalHtml = useAiClassification.innerHTML;

                useAiClassification.innerHTML =
                    '<i class="fas fa-check-circle"></i> Complaint Type Selected';

                useAiClassification.classList.remove('bg-sky-700', 'hover:bg-sky-800');
                useAiClassification.classList.add('bg-emerald-600');

                window.setTimeout(function () {
                    useAiClassification.innerHTML = originalHtml;
                    useAiClassification.classList.remove('bg-emerald-600');
                    useAiClassification.classList.add('bg-sky-700', 'hover:bg-sky-800');
                }, 1800);
            });

            /*
             * Location validation.
             */
            function hasValidMapLocation() {
                const lat = latitudeInput?.value.trim() || '';
                const lng = longitudeInput?.value.trim() || '';

                if (!lat || !lng) return false;

                const latitude = Number(lat);
                const longitude = Number(lng);

                return Number.isFinite(latitude) &&
                    Number.isFinite(longitude) &&
                    latitude >= -90 && latitude <= 90 &&
                    longitude >= -180 && longitude <= 180;
            }

            function showMapStatus(message, type = 'warning') {
                if (!mapAddressStatus) return;

                const styles = {
                    warning: 'rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800',
                    success: 'rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-xs leading-5 text-emerald-800',
                    error: 'rounded-xl border border-red-200 bg-red-50 px-3 py-3 text-xs leading-5 text-red-700'
                };

                mapAddressStatus.className = styles[type] || styles.warning;
                mapAddressStatus.textContent = message;
            }

            /*
             * Photos: selection, previews, and removal.
             */
            function showPhotoError(message) {
                if (!photoError) return;

                photoError.textContent = message;
                photoError.classList.remove('hidden');
            }

            function clearPhotoError() {
                if (!photoError) return;

                photoError.textContent = '';
                photoError.classList.add('hidden');
            }

            function syncPhotosInput() {
                if (!photosInput) return;

                const transfer = new DataTransfer();

                selectedPhotos.forEach(function (file) {
                    transfer.items.add(file);
                });

                photosInput.files = transfer.files;
            }

            function renderPhotoPreviews() {
                if (!photoPreviewGrid || !photoPreviewContainer) return;

                photoPreviewGrid.innerHTML = '';

                selectedPhotos.forEach(function (file, index) {
                    const url = URL.createObjectURL(file);
                    const card = document.createElement('div');

                    card.className =
                        'relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50';

                    const image = document.createElement('img');
                    image.src = url;
                    image.alt = 'Supporting photo ' + (index + 1);
                    image.className = 'h-28 w-full object-cover';
                    image.onload = function () {
                        URL.revokeObjectURL(url);
                    };

                    const info = document.createElement('div');
                    info.className = 'p-2';

                    const name = document.createElement('p');
                    name.className = 'truncate text-xs font-semibold text-slate-700';
                    name.textContent = file.name;

                    const size = document.createElement('p');
                    size.className = 'mt-0.5 text-[11px] text-slate-500';
                    size.textContent = (file.size / (1024 * 1024)).toFixed(2) + ' MB';

                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.className =
                        'mt-2 inline-flex items-center gap-1 text-xs font-semibold text-red-600';
                    remove.innerHTML = '<i class="fas fa-trash-can"></i> Remove';

                    remove.addEventListener('click', function () {
                        selectedPhotos.splice(index, 1);
                        syncPhotosInput();
                        renderPhotoPreviews();
                        clearPhotoError();
                    });

                    info.append(name, size, remove);
                    card.append(image, info);
                    photoPreviewGrid.appendChild(card);
                });

                photoPreviewContainer.classList.toggle('hidden', selectedPhotos.length === 0);

                if (photoCount) {
                    photoCount.textContent = selectedPhotos.length + ' / ' + MAX_PHOTOS;
                }
            }

            function addSupportingPhotos(files) {
                clearPhotoError();

                for (const file of Array.from(files || [])) {
                    if (selectedPhotos.length >= MAX_PHOTOS) {
                        showPhotoError('You may add a maximum of 5 supporting photos.');
                        break;
                    }

                    if (!ALLOWED_PHOTO_TYPES.includes(file.type)) {
                        showPhotoError('Please use JPG, JPEG, PNG, or WEBP images only.');
                        continue;
                    }

                    if (file.size > MAX_PHOTO_SIZE) {
                        showPhotoError('Each supporting photo must not exceed 5 MB.');
                        continue;
                    }

                    selectedPhotos.push(file);
                }

                syncPhotosInput();
                renderPhotoPreviews();
            }

            choosePhotoButton?.addEventListener('click', function () {
                if (selectedPhotos.length >= MAX_PHOTOS) {
                    showPhotoError('You already have the maximum of 5 supporting photos.');
                    return;
                }

                devicePhotoInput?.click();
            });

            devicePhotoInput?.addEventListener('change', function () {
                addSupportingPhotos(this.files);
                this.value = '';
            });

            /*
             * Camera controls.
             */
            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(function (track) {
                        track.stop();
                    });

                    cameraStream = null;
                }

                if (cameraVideo) cameraVideo.srcObject = null;
            }

            function closeCamera() {
                stopCamera();
                cameraModal?.classList.add('hidden');
                cameraModal?.classList.remove('flex');
            }

            async function openCamera() {
                clearPhotoError();

                if (selectedPhotos.length >= MAX_PHOTOS) {
                    showPhotoError('You already have the maximum of 5 supporting photos.');
                    return;
                }

                cameraModal?.classList.remove('hidden');
                cameraModal?.classList.add('flex');
                cameraError?.classList.add('hidden');

                if (cameraError) cameraError.textContent = '';

                if (!navigator.mediaDevices?.getUserMedia) {
                    if (cameraError) {
                        cameraError.textContent =
                            'Camera access requires HTTPS or localhost. You can use Choose from Device instead.';
                        cameraError.classList.remove('hidden');
                    }

                    return;
                }

                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: { facingMode: { ideal: 'environment' } },
                        audio: false
                    });

                    if (cameraVideo) {
                        cameraVideo.srcObject = cameraStream;
                        await cameraVideo.play();
                    }
                } catch (error) {
                    if (cameraError) {
                        cameraError.textContent =
                            'Unable to open the camera. Please allow camera permission or use Choose from Device instead.';
                        cameraError.classList.remove('hidden');
                    }
                }
            }

            takePhotoButton?.addEventListener('click', openCamera);
            closeCameraButton?.addEventListener('click', closeCamera);
            cancelCameraButton?.addEventListener('click', closeCamera);

            cameraModal?.addEventListener('click', function (event) {
                if (event.target === cameraModal) closeCamera();
            });

            capturePhotoButton?.addEventListener('click', function () {
                if (!cameraVideo?.videoWidth || !cameraVideo?.videoHeight || !cameraCanvas) {
                    if (cameraError) {
                        cameraError.textContent =
                            'The camera is not ready yet. Please wait a moment and try again.';
                        cameraError.classList.remove('hidden');
                    }

                    return;
                }

                cameraCanvas.width = cameraVideo.videoWidth;
                cameraCanvas.height = cameraVideo.videoHeight;

                const context = cameraCanvas.getContext('2d');
                context.drawImage(
                    cameraVideo,
                    0,
                    0,
                    cameraCanvas.width,
                    cameraCanvas.height
                );

                cameraCanvas.toBlob(function (blob) {
                    if (!blob) {
                        showPhotoError('Unable to capture the photo. Please try again.');
                        return;
                    }

                    const file = new File(
                        [blob],
                        'complaint-photo-' + Date.now() + '.jpg',
                        { type: 'image/jpeg' }
                    );

                    addSupportingPhotos([file]);
                    closeCamera();
                }, 'image/jpeg', 0.9);
            });

            /*
             * Submit validation.
             *
             * Capture phase is intentional so invalid data is stopped before
             * the Alpine submit handler switches the button to "Submitting".
             */
            form?.addEventListener('submit', function (event) {
                if (isSubmitting) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    return;
                }

                if (isEngineeringDivision() && !hasValidMapLocation()) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    showMapStatus(
                        'Please select the actual problem location on the map before submitting this Engineering/Operation complaint.',
                        'error'
                    );

                    locationSection?.classList.add('ring-2', 'ring-red-300', 'ring-offset-2');
                    scrollToCenter(locationSection, 100);

                    window.setTimeout(function () {
                        locationSection?.classList.remove('ring-2', 'ring-red-300', 'ring-offset-2');
                    }, 2500);

                    return;
                }

                if (isEngineeringDivision() && !addressInput?.value.trim()) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    addressInput?.focus();
                    scrollToCenter(locationSection, 100);

                    showMapStatus(
                        'Please enter or confirm the service address for this Engineering/Operation complaint.',
                        'error'
                    );

                    return;
                }

                if (selectedPhotos.length < 1) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    showPhotoError('At least one supporting photo is required.');
                    scrollToCenter(evidenceSection, 100);
                    highlightSection(evidenceSection, ['ring-red-300']);

                    return;
                }

                if (selectedPhotos.length > MAX_PHOTOS) {
                    event.preventDefault();
                    event.stopImmediatePropagation();

                    showPhotoError('You may add a maximum of 5 supporting photos.');
                    scrollToCenter(evidenceSection, 100);
                    return;
                }

                isSubmitting = true;
                stopCamera();
            }, true);

            /*
             * Leaflet map.
             */
            function initializeMap() {
                if (mapInitialized || !mapElement || typeof L === 'undefined') return;

                const defaultLat = 10.8961;
                const defaultLng = 123.4155;

                map = L.map('complaintMap', {
                    scrollWheelZoom: false
                }).setView([defaultLat, defaultLng], 15);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                map.on('click', function (event) {
                    setLocation(event.latlng.lat, event.latlng.lng, true);
                });

                const oldLat = Number(latitudeInput?.value);
                const oldLng = Number(longitudeInput?.value);

                if (
                    latitudeInput?.value &&
                    longitudeInput?.value &&
                    Number.isFinite(oldLat) &&
                    Number.isFinite(oldLng) &&
                    oldLat >= -90 && oldLat <= 90 &&
                    oldLng >= -180 && oldLng <= 180
                ) {
                    map.setView([oldLat, oldLng], 17);
                    setLocation(oldLat, oldLng, false);
                }

                mapInitialized = true;

                requestAnimationFrame(function () {
                    map?.invalidateSize();
                });
            }

            async function reverseGeocode(lat, lng) {
                if (!addressInput) return;

                showMapStatus('Finding the address for your selected location...', 'warning');

                try {
                    const url = 'https://nominatim.openstreetmap.org/reverse' +
                        '?format=jsonv2' +
                        '&lat=' + encodeURIComponent(lat) +
                        '&lon=' + encodeURIComponent(lng) +
                        '&zoom=18' +
                        '&addressdetails=1' +
                        '&accept-language=en';

                    const response = await fetch(url, {
                        headers: { 'Accept': 'application/json' }
                    });

                    if (!response.ok) {
                        throw new Error('Unable to identify the address.');
                    }

                    const data = await response.json();

                    if (!data?.display_name) {
                        throw new Error('No readable address found.');
                    }

                    addressInput.value = data.display_name;

                    showMapStatus(
                        'Location selected and coordinates recorded. Please confirm the pin and service address.',
                        'success'
                    );
                } catch (error) {
                    console.error('Reverse geocoding error:', error);

                    showMapStatus(
                        'Your map coordinates are recorded, but the address could not be found automatically. Please enter the service address manually.',
                        'warning'
                    );
                }
            }

            function setLocation(lat, lng, shouldReverseGeocode = true) {
                if (!latitudeInput || !longitudeInput || !map) return;

                const latitude = Number(lat);
                const longitude = Number(lng);

                if (
                    !Number.isFinite(latitude) ||
                    !Number.isFinite(longitude) ||
                    latitude < -90 || latitude > 90 ||
                    longitude < -180 || longitude > 180
                ) {
                    showMapStatus('The selected coordinates are invalid. Please select another location.', 'error');
                    return;
                }

                latitudeInput.value = latitude.toFixed(7);
                longitudeInput.value = longitude.toFixed(7);

                showMapStatus(
                    'Location selected and coordinates recorded. Please confirm the pin is at the actual problem location.',
                    'success'
                );

                if (marker) {
                    marker.setLatLng([latitude, longitude]);
                } else {
                    marker = L.marker([latitude, longitude], {
                        draggable: true
                    }).addTo(map);

                    marker.on('dragend', function (event) {
                        const position = event.target.getLatLng();

                        setLocation(position.lat, position.lng, true);
                    });
                }

                if (shouldReverseGeocode) {
                    reverseGeocode(latitude, longitude);
                }
            }

            locateMeButton?.addEventListener('click', function () {
                if (!navigator.geolocation) {
                    showMapStatus(
                        'Location services are not supported by your browser. Please select the location manually on the map.',
                        'error'
                    );
                    return;
                }

                initializeMap();

                const button = this;
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Locating...';

                navigator.geolocation.getCurrentPosition(
                    function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        map?.setView([lat, lng], 17);
                        setLocation(lat, lng, true);

                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use My Location';
                    },
                    function (error) {
                        console.error('Geolocation error:', error);

                        showMapStatus(
                            'Unable to get your location. Allow location access or select the location manually on the map.',
                            'error'
                        );

                        button.disabled = false;
                        button.innerHTML = '<i class="fas fa-location-crosshairs"></i> Use My Location';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 0
                    }
                );
            });

            /*
             * Map resize support.
             */
            if (mapElement && window.ResizeObserver) {
                const mapResizeObserver = new ResizeObserver(function () {
                    if (map) map.invalidateSize();
                });

                mapResizeObserver.observe(mapElement);
            }

            window.addEventListener('resize', function () {
                if (map && locationSection && !locationSection.classList.contains('hidden')) {
                    map.invalidateSize();
                }
            });

            initializeMap();

            window.setTimeout(function () {
                if (map && locationSection && !locationSection.classList.contains('hidden')) {
                    map.invalidateSize();
                }
            }, 400);

            window.addEventListener('beforeunload', stopCamera);
        });
    </script>
@endpush
