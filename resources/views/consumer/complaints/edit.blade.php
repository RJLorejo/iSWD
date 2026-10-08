@extends('consumer.layouts.app')

@section('title', 'Edit Complaint')

@section('content')

    @php
        $consumer = auth()->user()->consumer;
    @endphp

    <div class="mx-auto max-w-5xl space-y-5">

        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>

                <p class="text-xs font-bold uppercase tracking-wider text-sky-600">
                    Consumer Service
                </p>

                <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900 sm:text-3xl">
                    Edit Complaint
                </h1>

                <p class="mt-1 max-w-2xl text-sm leading-6 text-slate-500">
                    Update the information for complaint {{ $complaint->complaint_no }}.
                </p>

            </div>

            <a href="{{ route('consumer.complaints.show', $complaint) }}"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">

                <i class="fas fa-arrow-left text-xs"></i>

                Back to Complaint

            </a>

        </div>


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


        <form method="POST" action="{{ route('consumer.complaints.update', $complaint) }}" enctype="multipart/form-data"
            class="space-y-5" x-data="{ saving: false }" @submit="saving = true">

            @csrf
            @method('PUT')

            <input type="hidden" name="ai_analysis_token" id="ai_analysis_token" value="">


            {{-- STEP 1 — CONCERN --}}

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
                                Review or update your description using your own words.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <div class="mb-2 flex items-center justify-between gap-4">

                        <label for="description" class="text-sm font-semibold text-slate-700">

                            Your Concern

                            <span class="text-red-500">* required</span>

                        </label>

                        <span id="descriptionCount" class="text-xs text-slate-400">
                            0 / 5000
                        </span>

                    </div>


                    <textarea id="description" name="description" rows="3" maxlength="5000" required
                        placeholder="Example: Wala kami tubig halin pa sang aga.&#10;&#10;Simply tell us what you noticed and when it started."
                        class="w-full resize-y rounded-xl border-slate-300 px-4 py-3 text-sm leading-6 text-slate-700 placeholder:text-slate-400 focus:border-sky-500 focus:ring-sky-500">{{ old('description', $complaint->description) }}</textarea>


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


                    <div class="mt-5 rounded-xl border border-sky-100 bg-sky-50/60 p-4">

                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                            <div class="flex items-start gap-3">

                                <div
                                    class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-white text-sky-700">

                                    <i class="fas fa-wand-magic-sparkles"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">
                                        Want to check the complaint type again?
                                    </p>

                                    <p class="mt-1 text-xs leading-5 text-slate-500">
                                        Re-analyze your current description and compare the
                                        suggestion with your currently selected complaint type.
                                    </p>

                                </div>

                            </div>


                            <button type="button" id="analyzeConcern"
                                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-60">

                                <i class="fas fa-wand-magic-sparkles"></i>

                                <span id="analyzeConcernText">
                                    Re-analyze Concern
                                </span>

                            </button>

                        </div>

                    </div>


                    <div id="aiAnalysisError"
                        class="mt-4 hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700"></div>


                    <div id="aiAnalysisCard" class="mt-4 hidden overflow-hidden rounded-xl border border-sky-200 bg-white">

                        <div class="border-b border-sky-100 bg-sky-50 px-4 py-3">

                            <div class="flex items-center gap-3">

                                <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-white text-sky-700">

                                    <i class="fas fa-wand-magic-sparkles text-sm"></i>

                                </div>

                                <div>

                                    <p class="text-sm font-semibold text-slate-900">
                                        Updated AI Suggestion
                                    </p>

                                    <p class="text-xs text-slate-500">
                                        Based on the description currently written above.
                                    </p>

                                </div>

                            </div>

                        </div>


                        <div class="space-y-4 p-4 sm:p-5">

                            <div>

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Suggested SWD Complaint Type
                                </p>

                                <p id="aiComplaintType" class="mt-1 text-lg font-bold text-slate-900">
                                    —
                                </p>

                            </div>


                            <div>

                                <div class="rounded-xl bg-slate-50 p-3">

                                    <p class="text-xs text-slate-500">
                                        Handled By
                                    </p>

                                    <p id="aiDivision" class="mt-1 text-sm font-semibold text-slate-800">
                                        —
                                    </p>

                                </div>

                            </div>


                            <div id="aiSupportingEvidenceSection"
                                class="hidden rounded-xl border border-slate-200 bg-slate-50 p-4">

                                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                    Why this was suggested
                                </p>

                                <p id="aiSupportingSummary" class="mt-2 text-sm leading-6 text-slate-600"></p>


                                <div id="aiSupportingIndicatorsSection" class="mt-3 hidden">

                                    <ul id="aiSupportingIndicators" class="space-y-1.5 text-xs leading-5 text-slate-600">
                                    </ul>

                                </div>

                            </div>


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
                                active SWD complaint type. You may keep the current complaint
                                type or choose another one manually below.
                            </div>


                            <div
                                class="flex flex-col gap-3 border-t border-slate-100 pt-4 sm:flex-row sm:items-center sm:justify-between">

                                <p class="text-xs leading-5 text-slate-500">
                                    Your current complaint type will not change unless you use this suggestion.
                                </p>

                                <button type="button" id="useAiClassification"
                                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-50">

                                    <i class="fas fa-check"></i>

                                    Use Updated Suggestion

                                </button>

                            </div>

                        </div>

                    </div>

                </div>

            </section>


            {{-- STEP 2 — OFFICIAL SWD TYPE --}}

            <section id="classificationSection" class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

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
                                Keep the current complaint type or update it if necessary.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="p-5 sm:p-6">

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div>

                            <label for="division_id" class="mb-2 block text-sm font-semibold text-slate-700">

                                Service Division

                                <span class="text-red-500">* required</span>

                            </label>

                            <select id="division_id" name="division_id" required
                                class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm text-slate-700 focus:border-sky-500 focus:ring-sky-500">

                                <option value="">
                                    Select service division
                                </option>

                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}"
                                        {{ old('division_id', $complaint->division_id) == $division->id ? 'selected' : '' }}>
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


                        <div>

                            <label for="complaint_category_id" class="mb-2 block text-sm font-semibold text-slate-700">

                                Complaint Type

                                <span class="text-red-500">* required</span>

                            </label>

                            <select id="complaint_category_id" name="complaint_category_id" required disabled
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


            {{-- COMMERCIAL ACCOUNT --}}

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
                                This concern uses your registered SWD account.
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


            {{-- STEP 3 — LOCATION --}}

            <section id="engineeringLocationSection" class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

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
                                Keep the current service location or update it if necessary.
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
                                Click the map or drag the marker if you need to update the location.
                            </p>

                        </div>


                        <button type="button" id="locateMe"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 transition hover:bg-sky-100">

                            <i class="fas fa-location-crosshairs"></i>

                            Use My Location

                        </button>

                    </div>


                    <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">

                        <div id="complaintMap" class="h-72 w-full sm:h-80"></div>

                    </div>


                    <p id="mapAddressStatus" class="text-xs leading-5 text-slate-500">
                        Select a point on the map or edit the address below.
                    </p>


                    <div>

                        <label for="address" class="mb-2 block text-sm font-semibold text-slate-700">

                            Service Address

                            <span class="text-red-500">* required</span>

                        </label>

                        <input type="text" name="address" id="address"
                            value="{{ old('address', $complaint->address) }}" maxlength="500"
                            placeholder="Enter the address where the problem is located"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('address')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <div>

                        <label for="landmark" class="mb-2 block text-sm font-semibold text-slate-700">

                            Nearby Landmark

                            <span class="font-normal text-slate-400">
                                (Optional)
                            </span>

                        </label>

                        <input type="text" id="landmark" name="landmark"
                            value="{{ old('landmark', $complaint->landmark) }}" maxlength="255"
                            placeholder="Example: Near Sagay Public Market"
                            class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm focus:border-sky-500 focus:ring-sky-500">

                        @error('landmark')
                            <p class="mt-2 text-sm font-medium text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>


                    <input type="hidden" id="latitude" name="latitude"
                        value="{{ old('latitude', $complaint->latitude) }}">

                    <input type="hidden" id="longitude" name="longitude"
                        value="{{ old('longitude', $complaint->longitude) }}">

                </div>

            </section>


            <span id="stepThreeLabel" class="hidden">
                Location
            </span>


            {{-- STEP 4 — SUPPORTING PHOTOS --}}

            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">

                <div class="border-b border-slate-100 px-5 py-4 sm:px-6">
                    <div class="flex items-start gap-3">
                        <div
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-amber-50 text-sm font-bold text-amber-700">
                            4
                        </div>

                        <div>
                            <h2 id="evidenceTitle" class="font-semibold text-slate-900">
                                Supporting Photos   <span class="text-red-500">* required</span>
                            </h2>

                            <p id="evidenceSubtitle" class="mt-1 text-sm text-slate-500">
                                Keep or remove existing photos, then add more if needed. You must keep at least 1 and no
                                more than 5 photos.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-5 p-5 sm:p-6">

                    {{-- Existing photos --}}
                    @if ($complaint->photos->count())
                        <div>
                            <p class="mb-3 text-sm font-semibold text-slate-700">
                                Existing Photos
                            </p>

                            <div id="existingPhotoGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
                                @foreach ($complaint->photos as $existingPhoto)
                                    <div class="existing-photo-card overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                                        data-photo-id="{{ $existingPhoto->id }}">

                                        <div class="aspect-square overflow-hidden bg-white">
                                            <img src="{{ asset('storage/' . $existingPhoto->photo) }}"
                                                alt="Supporting photo" class="h-full w-full object-cover">
                                        </div>

                                        <div class="p-2">
                                            <button type="button"
                                                class="remove-existing-photo inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-2 py-2 text-xs font-semibold text-red-600 transition hover:bg-red-100"
                                                data-photo-id="{{ $existingPhoto->id }}">
                                                <i class="fas fa-trash-can"></i>
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Removed photo IDs are added here by JavaScript --}}
                    <div id="removedPhotoInputs"></div>

                    {{-- Hidden real input submitted to Laravel --}}
                    <input type="file" id="photos" name="photos[]" multiple
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden">

                    {{-- Separate device picker --}}
                    <input type="file" id="devicePhotoPicker" multiple
                        accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden">

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

                    <p class="text-xs leading-5 text-slate-500">
                        Final total: 1 to 5 photos. JPG, JPEG, PNG or WEBP. Maximum 5 MB each.
                    </p>

                    <div id="newPhotosSection" class="hidden">
                        <p class="mb-3 text-sm font-semibold text-slate-700">
                            New Photos
                        </p>

                        <div id="photoPreviewGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5"></div>
                    </div>

                    <p id="photoValidationMessage" class="hidden text-sm font-medium text-red-600"></p>

                    @error('photos')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @error('photos.*')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @error('remove_photos')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror

                    @error('remove_photos.*')
                        <p class="text-sm font-medium text-red-600">{{ $message }}</p>
                    @enderror

                </div>

            </section>

            {{-- CAMERA MODAL --}}
            <div id="cameraModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/80 p-4">
                <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                        <div>
                            <h3 class="font-semibold text-slate-900">Take Photo</h3>
                            <p class="mt-1 text-xs text-slate-500">Position the concern clearly, then capture the photo.
                            </p>
                        </div>

                        <button type="button" id="closeCameraButton"
                            class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-500 transition hover:bg-slate-100">
                            <i class="fas fa-xmark"></i>
                        </button>
                    </div>

                    <div class="bg-black">
                        <video id="cameraVideo" autoplay playsinline muted
                            class="max-h-[65vh] w-full object-contain"></video>
                        <canvas id="cameraCanvas" class="hidden"></canvas>
                    </div>

                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:justify-end">
                        <button type="button" id="cancelCameraButton"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">
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

            <div class="rounded-xl border border-sky-100 bg-sky-50 p-4">

                <div class="flex items-start gap-3">

                    <i class="fas fa-circle-info mt-0.5 text-sky-600"></i>

                    <div>

                        <p class="text-sm font-semibold text-sky-900">
                            Before You Save
                        </p>

                        <p id="beforeSubmitText" class="mt-1 text-xs leading-5 text-sky-800">
                            Check your concern, complaint type, service location, and photo before saving your changes.
                        </p>

                    </div>

                </div>

            </div>


            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                    Cancel
                </a>


                <button type="submit" :disabled="saving"
                    class="inline-flex min-w-[180px] items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white transition hover:bg-sky-800 disabled:cursor-not-allowed disabled:opacity-70">

                    <span x-show="!saving" class="inline-flex items-center gap-2">

                        <i class="fas fa-floppy-disk"></i>

                        Save Changes

                    </span>

                    <span x-show="saving" x-cloak class="inline-flex items-center gap-2">

                        <i class="fas fa-spinner fa-spin"></i>

                        Saving...

                    </span>

                </button>

            </div>

        </form>

    </div>


    @push('scripts')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>


        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const divisions =
                    @json($divisions);

                const selectedComplaintType =
                    @json(old('complaint_category_id', $complaint->complaint_category_id));

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

                const beforeSubmitText =
                    document.getElementById('beforeSubmitText');

                const photosInput =
                    document.getElementById('photos');

                const devicePhotoPicker =
                    document.getElementById('devicePhotoPicker');

                const takePhotoButton =
                    document.getElementById('takePhotoButton');

                const choosePhotoButton =
                    document.getElementById('choosePhotoButton');

                const photoPreviewGrid =
                    document.getElementById('photoPreviewGrid');

                const newPhotosSection =
                    document.getElementById('newPhotosSection');

                const removedPhotoInputs =
                    document.getElementById('removedPhotoInputs');

                const photoValidationMessage =
                    document.getElementById('photoValidationMessage');

                const cameraModal =
                    document.getElementById('cameraModal');

                const cameraVideo =
                    document.getElementById('cameraVideo');

                const cameraCanvas =
                    document.getElementById('cameraCanvas');

                const closeCameraButton =
                    document.getElementById('closeCameraButton');

                const cancelCameraButton =
                    document.getElementById('cancelCameraButton');

                const capturePhotoButton =
                    document.getElementById('capturePhotoButton');

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

                const aiSignals =
                    document.getElementById('aiSignals');

                const aiReview =
                    document.getElementById('aiReview');

                const aiMatchWarning =
                    document.getElementById('aiMatchWarning');

                const useAiClassification =
                    document.getElementById('useAiClassification');

                let selectedPhotos = [];
                let removedPhotoIds = new Set();
                let cameraStream = null;

                let matchedAiCategory = null;

                let map = null;
                let marker = null;
                let mapInitialized = false;


                function updateDescriptionCount() {

                    if (
                        !description ||
                        !descriptionCount
                    ) {
                        return;
                    }

                    descriptionCount.textContent =
                        description.value.length +
                        ' / 5000';
                }


                updateDescriptionCount();


                description?.addEventListener(
                    'input',
                    function() {

                        updateDescriptionCount();

                        if (aiAnalysisTokenInput) {
                            aiAnalysisTokenInput.value = '';
                        }
                    }
                );


                function getSelectedDivision() {

                    if (!divisionSelect?.value) {
                        return null;
                    }

                    return divisions.find(
                        function(item) {

                            return String(item.id) ===
                                String(divisionSelect.value);
                        }
                    ) || null;
                }


                function getSelectedDivisionName() {

                    const division =
                        getSelectedDivision();

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
                        division.complaintTypes ?? [];
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
                        divisions.find(
                            function(item) {

                                return String(item.id) ===
                                    String(divisionId);
                            }
                        );


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


                    complaintTypes.forEach(
                        function(type) {

                            const option =
                                document.createElement('option');

                            option.value =
                                type.id;

                            option.textContent =
                                type.name;


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
                        }
                    );
                }


                function updateDivisionForm() {

                    if (!addressInput) {
                        return;
                    }

                    if (isEngineeringDivision()) {

                        commercialAccountSection?.classList.add('hidden');
                        engineeringLocationSection?.classList.remove('hidden');
                        addressInput.required = true;

                        if (evidenceTitle) {
                            evidenceTitle.textContent = 'Supporting Photos';
                        }

                        if (evidenceSubtitle) {
                            evidenceSubtitle.textContent =
                                'Keep or remove existing photos, then add more if needed. You must keep at least 1 and no more than 5 photos.';
                        }

                        if (beforeSubmitText) {
                            beforeSubmitText.textContent =
                                'Check your concern, complaint type, service location, and supporting photos before saving your changes.';
                        }

                        initializeMap();

                        setTimeout(function() {
                            if (map) {
                                map.invalidateSize();
                            }
                        }, 250);

                    } else if (isCommercialDivision()) {

                        commercialAccountSection?.classList.remove('hidden');
                        engineeringLocationSection?.classList.add('hidden');
                        addressInput.required = false;

                        if (evidenceTitle) {
                            evidenceTitle.textContent = 'Supporting Photos';
                        }

                        if (evidenceSubtitle) {
                            evidenceSubtitle.textContent =
                                'Keep or remove existing photos, then add more if needed. You must keep at least 1 and no more than 5 photos.';
                        }

                        if (beforeSubmitText) {
                            beforeSubmitText.textContent =
                                'Check your concern, complaint type, and supporting photos before saving. Your registered consumer account remains linked automatically.';
                        }

                    } else {

                        commercialAccountSection?.classList.add('hidden');
                        engineeringLocationSection?.classList.remove('hidden');
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
                        selectedComplaintType
                    );
                }


                updateDivisionForm();


                function getNestedValue(
                    object,
                    paths
                ) {

                    for (const path of paths) {

                        const parts =
                            path.split('.');

                        let value =
                            object;


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

                            value =
                                value[part];
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
                            .map(
                                function(item) {

                                    if (
                                        typeof item === 'string'
                                    ) {
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
                                }
                            )
                            .filter(Boolean);
                    }


                    if (
                        typeof value === 'string' &&
                        value.trim() !== ''
                    ) {
                        return [
                            value.trim()
                        ];
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


                    if (aiComplaintType) {

                        aiComplaintType.textContent =
                            String(predictedType);
                    }


                    if (aiDivision) {

                        aiDivision.textContent =
                            matchedCategory?.division_name ||
                            'No active division matched';
                    }


                    if (aiAnalysisStatus) {

                        aiAnalysisStatus.textContent =
                            'SWD personnel will review this submission.';
                    }


                    if (aiSupportingSummary) {
                        aiSupportingSummary.textContent = '';
                    }

                    if (aiSupportingIndicators) {
                        aiSupportingIndicators.innerHTML = '';
                    }


                    const indicators =
                        Array.isArray(
                            supportingIndicators
                        ) ?
                        supportingIndicators : [];


                    if (
                        typeof supportingSummary === 'string' &&
                        supportingSummary.trim() !== ''
                    ) {

                        if (aiSupportingSummary) {

                            aiSupportingSummary.textContent =
                                supportingSummary.trim();
                        }

                        aiSupportingEvidenceSection?.classList.remove(
                            'hidden'
                        );

                    } else {

                        aiSupportingEvidenceSection?.classList.add(
                            'hidden'
                        );
                    }


                    if (indicators.length > 0) {

                        indicators.forEach(
                            function(indicator) {

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

                                aiSupportingIndicators?.appendChild(
                                    item
                                );
                            }
                        );


                        aiSupportingIndicatorsSection?.classList.remove(
                            'hidden'
                        );

                    } else {

                        aiSupportingIndicatorsSection?.classList.add(
                            'hidden'
                        );
                    }


                    if (aiSignals) {

                        aiSignals.innerHTML = '';

                        normalizeSignals(
                            urgencyReasons
                        ).forEach(
                            function(reason) {

                                const item =
                                    document.createElement('li');

                                item.textContent =
                                    reason;

                                aiSignals.appendChild(
                                    item
                                );
                            }
                        );
                    }


                    if (aiReview) {

                        aiReview.textContent =
                            normalizeSignals(
                                reviewReasons
                            ).join(' ');
                    }


                    if (matchedCategory) {

                        aiMatchWarning?.classList.add(
                            'hidden'
                        );

                        if (useAiClassification) {
                            useAiClassification.disabled =
                                false;
                        }

                    } else {

                        aiMatchWarning?.classList.remove(
                            'hidden'
                        );

                        if (useAiClassification) {
                            useAiClassification.disabled =
                                true;
                        }
                    }


                    aiAnalysisCard?.classList.remove(
                        'hidden'
                    );


                    setTimeout(function() {
                        aiAnalysisCard?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                    }, 150);
                }


                analyzeConcern?.addEventListener(
                    'click',
                    async function() {

                        const complaintDescription =
                            description?.value
                            .trim() || '';


                        aiAnalysisError?.classList.add(
                            'hidden'
                        );

                        if (aiAnalysisError) {
                            aiAnalysisError.textContent = '';
                        }


                        if (
                            complaintDescription.length < 10
                        ) {

                            if (aiAnalysisError) {

                                aiAnalysisError.textContent =
                                    'Please tell us a little more about what happened before using AI assistance.';

                                aiAnalysisError.classList.remove(
                                    'hidden'
                                );
                            }

                            description?.focus();

                            return;
                        }


                        analyzeConcern.disabled =
                            true;

                        analyzeConcernText.textContent =
                            'Re-analyzing Concern...';


                        const icon =
                            analyzeConcern.querySelector(
                                'i'
                            );


                        if (icon) {
                            icon.className =
                                'fas fa-spinner fa-spin';
                        }


                        try {

                            const response =
                                await fetch(
                                    @json(route('consumer.complaints.analyze')), {
                                        method: 'POST',

                                        headers: {
                                            'Accept': 'application/json',

                                            'Content-Type': 'application/json',

                                            'X-CSRF-TOKEN': document
                                                .querySelector(
                                                    'meta[name="csrf-token"]'
                                                )
                                                ?.getAttribute(
                                                    'content'
                                                ) ||
                                                @json(csrf_token())
                                        },

                                        body: JSON.stringify({
                                            description: complaintDescription
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
                                    'AI assistance is temporarily unavailable. You may keep the current complaint type or choose another one manually.';


                                if (
                                    response.status === 422 &&
                                    payload.errors?.description
                                ) {

                                    message =
                                        payload.errors.description[0];
                                }


                                throw new Error(
                                    message
                                );
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


                            matchedAiCategory =
                                null;


                            if (aiAnalysisTokenInput) {
                                aiAnalysisTokenInput.value = '';
                            }


                            if (aiAnalysisError) {

                                aiAnalysisError.textContent =
                                    error.message ||
                                    'AI assistance is temporarily unavailable. You may keep the current complaint type or choose another one manually.';

                                aiAnalysisError.classList.remove(
                                    'hidden'
                                );
                            }


                        } finally {

                            analyzeConcern.disabled =
                                false;

                            analyzeConcernText.textContent =
                                'Re-analyze Concern';


                            if (icon) {

                                icon.className =
                                    'fas fa-wand-magic-sparkles';
                            }
                        }
                    }
                );


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


                        setTimeout(function() {
                            classificationSection?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }, 300);


                        const originalHtml =
                            useAiClassification.innerHTML;


                        useAiClassification.innerHTML =
                            '<i class="fas fa-check-circle"></i> Updated Suggestion Selected';


                        useAiClassification.classList.remove(
                            'bg-sky-700',
                            'hover:bg-sky-800'
                        );


                        useAiClassification.classList.add(
                            'bg-emerald-600'
                        );


                        setTimeout(
                            function() {

                                useAiClassification.innerHTML =
                                    originalHtml;

                                useAiClassification.classList.remove(
                                    'bg-emerald-600'
                                );

                                useAiClassification.classList.add(
                                    'bg-sky-700',
                                    'hover:bg-sky-800'
                                );

                            },
                            1800
                        );
                    }
                );


                function formatFileSize(bytes) {
                    if (bytes < 1024) return bytes + ' bytes';
                    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
                    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
                }

                function activeExistingPhotoCount() {
                    return document.querySelectorAll('.existing-photo-card:not(.hidden)').length;
                }

                function finalPhotoCount() {
                    return activeExistingPhotoCount() + selectedPhotos.length;
                }

                function showPhotoValidation(message = '') {
                    if (!photoValidationMessage) return;
                    photoValidationMessage.textContent = message;
                    photoValidationMessage.classList.toggle('hidden', !message);
                }

                function syncPhotosInput() {
                    if (!photosInput) return;

                    const transfer = new DataTransfer();
                    selectedPhotos.forEach(function(file) {
                        transfer.items.add(file);
                    });
                    photosInput.files = transfer.files;
                }

                function syncRemovedPhotoInputs() {
                    if (!removedPhotoInputs) return;
                    removedPhotoInputs.innerHTML = '';

                    removedPhotoIds.forEach(function(id) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'remove_photos[]';
                        input.value = id;
                        removedPhotoInputs.appendChild(input);
                    });
                }

                function renderNewPhotos() {
                    if (!photoPreviewGrid || !newPhotosSection) return;

                    photoPreviewGrid.innerHTML = '';
                    newPhotosSection.classList.toggle('hidden', selectedPhotos.length === 0);

                    selectedPhotos.forEach(function(file, index) {
                        const card = document.createElement('div');
                        card.className = 'overflow-hidden rounded-xl border border-slate-200 bg-slate-50';

                        const imageWrap = document.createElement('div');
                        imageWrap.className = 'aspect-square overflow-hidden bg-white';

                        const image = document.createElement('img');
                        image.className = 'h-full w-full object-cover';
                        image.alt = 'New supporting photo';
                        image.src = URL.createObjectURL(file);
                        image.onload = function() {
                            URL.revokeObjectURL(image.src);
                        };

                        imageWrap.appendChild(image);

                        const details = document.createElement('div');
                        details.className = 'p-2';

                        const name = document.createElement('p');
                        name.className = 'truncate text-xs font-semibold text-slate-700';
                        name.textContent = file.name;

                        const size = document.createElement('p');
                        size.className = 'mt-1 text-[11px] text-slate-500';
                        size.textContent = formatFileSize(file.size);

                        const remove = document.createElement('button');
                        remove.type = 'button';
                        remove.className =
                            'mt-2 inline-flex w-full items-center justify-center gap-2 rounded-lg border border-red-200 bg-red-50 px-2 py-2 text-xs font-semibold text-red-600 hover:bg-red-100';
                        remove.innerHTML = '<i class="fas fa-trash-can"></i> Remove';
                        remove.addEventListener('click', function() {
                            selectedPhotos.splice(index, 1);
                            syncPhotosInput();
                            renderNewPhotos();
                            showPhotoValidation('');
                        });

                        details.appendChild(name);
                        details.appendChild(size);
                        details.appendChild(remove);
                        card.appendChild(imageWrap);
                        card.appendChild(details);
                        photoPreviewGrid.appendChild(card);
                    });
                }

                function isAllowedPhoto(file) {
                    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

                    if (!allowedTypes.includes(file.type)) {
                        alert('Please select JPG, JPEG, PNG, or WEBP images only.');
                        return false;
                    }

                    if (file.size > 5 * 1024 * 1024) {
                        alert('Each photo must not exceed 5 MB.');
                        return false;
                    }

                    return true;
                }

                function addPhotos(files) {
                    const incoming = Array.from(files || []);

                    for (const file of incoming) {
                        if (!isAllowedPhoto(file)) continue;

                        if (finalPhotoCount() >= 5) {
                            alert('You can keep a maximum of 5 supporting photos in total.');
                            break;
                        }

                        selectedPhotos.push(file);
                    }

                    syncPhotosInput();
                    renderNewPhotos();
                    showPhotoValidation('');
                }

                document.querySelectorAll('.remove-existing-photo').forEach(function(button) {
                    button.addEventListener('click', function() {
                        const id = String(this.dataset.photoId || '');
                        const card = this.closest('.existing-photo-card');

                        if (!id || !card) return;

                        if (finalPhotoCount() <= 1) {
                            showPhotoValidation('At least 1 supporting photo must remain.');
                            return;
                        }

                        removedPhotoIds.add(id);
                        card.classList.add('hidden');
                        syncRemovedPhotoInputs();
                        showPhotoValidation('');
                    });
                });

                choosePhotoButton?.addEventListener('click', function() {
                    if (finalPhotoCount() >= 5) {
                        alert('You already have the maximum of 5 supporting photos.');
                        return;
                    }
                    devicePhotoPicker?.click();
                });

                devicePhotoPicker?.addEventListener('change', function() {
                    addPhotos(this.files);
                    this.value = '';
                });

                function stopCamera() {
                    if (cameraStream) {
                        cameraStream.getTracks().forEach(function(track) {
                            track.stop();
                        });
                        cameraStream = null;
                    }

                    if (cameraVideo) {
                        cameraVideo.srcObject = null;
                    }
                }

                function closeCamera() {
                    stopCamera();
                    cameraModal?.classList.add('hidden');
                    cameraModal?.classList.remove('flex');
                }

                takePhotoButton?.addEventListener('click', async function() {
                    if (finalPhotoCount() >= 5) {
                        alert('You already have the maximum of 5 supporting photos.');
                        return;
                    }

                    if (!navigator.mediaDevices?.getUserMedia) {
                        alert(
                            'Camera access requires HTTPS or localhost. You can use Choose from Device instead.');
                        return;
                    }

                    try {
                        cameraStream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                }
                            },
                            audio: false
                        });

                        cameraVideo.srcObject = cameraStream;
                        cameraModal?.classList.remove('hidden');
                        cameraModal?.classList.add('flex');

                        await cameraVideo.play();

                    } catch (error) {
                        console.error('Camera error:', error);
                        stopCamera();
                        alert(
                            'Unable to open the camera. Camera access requires HTTPS or localhost. You can use Choose from Device instead.');
                    }
                });

                closeCameraButton?.addEventListener('click', closeCamera);
                cancelCameraButton?.addEventListener('click', closeCamera);

                cameraModal?.addEventListener('click', function(event) {
                    if (event.target === cameraModal) {
                        closeCamera();
                    }
                });

                capturePhotoButton?.addEventListener('click', function() {
                    if (!cameraVideo || !cameraCanvas || !cameraVideo.videoWidth || !cameraVideo.videoHeight) {
                        return;
                    }

                    cameraCanvas.width = cameraVideo.videoWidth;
                    cameraCanvas.height = cameraVideo.videoHeight;

                    const context = cameraCanvas.getContext('2d');
                    context.drawImage(cameraVideo, 0, 0, cameraCanvas.width, cameraCanvas.height);

                    cameraCanvas.toBlob(function(blob) {
                        if (!blob) return;

                        const file = new File(
                            [blob],
                            'camera-' + Date.now() + '.jpg', {
                                type: 'image/jpeg'
                            }
                        );

                        addPhotos([file]);
                        closeCamera();
                    }, 'image/jpeg', 0.9);
                });

                const complaintForm = photosInput?.closest('form');

                complaintForm?.addEventListener('submit', function(event) {
                    const total = finalPhotoCount();

                    if (total < 1) {
                        event.preventDefault();
                        showPhotoValidation('At least 1 supporting photo is required.');
                        document.getElementById('evidenceTitle')?.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        return;
                    }

                    if (total > 5) {
                        event.preventDefault();
                        showPhotoValidation('You can keep a maximum of 5 supporting photos.');
                    }
                });

                function initializeMap() {

                    if (
                        mapInitialized ||
                        !mapElement ||
                        typeof L === 'undefined'
                    ) {
                        return;
                    }


                    const defaultLat =
                        10.8961;

                    const defaultLng =
                        123.4155;


                    map =
                        L.map(
                            'complaintMap', {
                                scrollWheelZoom: false
                            }
                        ).setView(
                            [
                                defaultLat,
                                defaultLng
                            ],
                            15
                        );


                    L.tileLayer(
                        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            maxZoom: 19,
                            attribution: '&copy; OpenStreetMap contributors'
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


                    const savedLat =
                        parseFloat(
                            latitudeInput?.value
                        );


                    const savedLng =
                        parseFloat(
                            longitudeInput?.value
                        );


                    if (
                        !Number.isNaN(savedLat) &&
                        !Number.isNaN(savedLng)
                    ) {

                        map.setView(
                            [
                                savedLat,
                                savedLng
                            ],
                            17
                        );


                        setLocation(
                            savedLat,
                            savedLng,
                            false
                        );
                    }


                    mapInitialized =
                        true;


                    requestAnimationFrame(
                        function() {

                            requestAnimationFrame(
                                function() {

                                    if (map) {
                                        map.invalidateSize();
                                    }
                                }
                            );
                        }
                    );
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
                                url, {
                                    headers: {
                                        'Accept': 'application/json'
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


                    const parsedLat =
                        Number(lat);

                    const parsedLng =
                        Number(lng);


                    if (
                        Number.isNaN(parsedLat) ||
                        Number.isNaN(parsedLng)
                    ) {
                        return;
                    }


                    latitudeInput.value =
                        parsedLat.toFixed(7);

                    longitudeInput.value =
                        parsedLng.toFixed(7);


                    if (marker) {

                        marker.setLatLng(
                            [
                                parsedLat,
                                parsedLng
                            ]
                        );

                    } else {

                        marker =
                            L.marker(
                                [
                                    parsedLat,
                                    parsedLng
                                ], {
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
                            parsedLat,
                            parsedLng
                        );

                        setTimeout(function() {
                            mapElement?.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });
                        }, 120);
                    }
                }


                locateMe?.addEventListener(
                    'click',
                    function(event) {

                        event.preventDefault();


                        if (!navigator.geolocation) {

                            alert(
                                'Location services are not supported by your browser.'
                            );

                            return;
                        }


                        initializeMap();


                        if (!map) {

                            alert(
                                'The map is not ready. Please refresh the page and try again.'
                            );

                            return;
                        }


                        const button =
                            this;


                        button.disabled =
                            true;


                        button.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Locating...';


                        navigator.geolocation.getCurrentPosition(

                            function(position) {

                                const lat =
                                    position.coords.latitude;

                                const lng =
                                    position.coords.longitude;


                                engineeringLocationSection?.classList.remove(
                                    'hidden'
                                );


                                map.invalidateSize();


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


                                setTimeout(
                                    function() {

                                        map.invalidateSize();

                                        map.setView(
                                            [
                                                lat,
                                                lng
                                            ],
                                            17
                                        );

                                        mapElement?.scrollIntoView({
                                            behavior: 'smooth',
                                            block: 'center'
                                        });

                                    },
                                    200
                                );


                                button.disabled =
                                    false;


                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use My Location';
                            },


                            function(error) {

                                console.error(
                                    'Geolocation error:',
                                    error
                                );


                                button.disabled =
                                    false;


                                button.innerHTML =
                                    '<i class="fas fa-location-crosshairs"></i> Use My Location';


                                if (error.code === 1) {

                                    alert(
                                        'Location permission is blocked. Please allow location access for this site in your browser settings, then try again.'
                                    );

                                    return;
                                }


                                if (error.code === 2) {

                                    alert(
                                        'Your current location is unavailable. Make sure location services are enabled on your computer.'
                                    );

                                    return;
                                }


                                if (error.code === 3) {

                                    alert(
                                        'Getting your location timed out. Please try again.'
                                    );

                                    return;
                                }


                                alert(
                                    'Unable to get your current location.'
                                );
                            },


                            {
                                enableHighAccuracy: true,

                                timeout: 20000,

                                maximumAge: 0
                            }
                        );
                    }
                );


                if (
                    mapElement &&
                    window.ResizeObserver
                ) {

                    const mapResizeObserver =
                        new ResizeObserver(
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


                initializeMap();


                setTimeout(
                    function() {

                        if (
                            isEngineeringDivision() &&
                            map
                        ) {

                            map.invalidateSize();


                            const currentLat =
                                parseFloat(
                                    latitudeInput?.value
                                );


                            const currentLng =
                                parseFloat(
                                    longitudeInput?.value
                                );


                            if (
                                !Number.isNaN(currentLat) &&
                                !Number.isNaN(currentLng)
                            ) {

                                map.setView(
                                    [
                                        currentLat,
                                        currentLng
                                    ],
                                    17
                                );
                            }
                        }

                    },
                    400
                );

            });
        </script>
    @endpush

@endsection
