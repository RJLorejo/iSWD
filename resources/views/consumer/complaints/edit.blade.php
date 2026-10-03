@extends('consumer.layouts.app')

@section('title', 'Edit Complaint')

@section('content')

    @php
        $consumer = auth()->user()->consumer;
    @endphp

    <div class="max-w-4xl mx-auto space-y-6">

        <div>
            <h1 class="text-3xl font-bold text-slate-900">
                Edit Complaint
            </h1>

            <p class="mt-1 text-slate-500">
                Update the information for complaint {{ $complaint->complaint_no }}.
            </p>
        </div>

        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">
                <div class="flex gap-3">
                    <div class="text-red-600 mt-0.5">
                        <i class="fas fa-circle-exclamation"></i>
                    </div>

                    <div>
                        <h2 class="font-semibold text-red-800">
                            Please correct the following:
                        </h2>

                        <ul class="mt-2 list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <form
            action="{{ route('consumer.complaints.update', $complaint) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-6"
            x-data="{ saving: false }"
            @submit="saving = true"
        >

            @csrf
            @method('PUT')

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700 flex items-center justify-center">
                            <i class="fas fa-layer-group"></i>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Complaint Classification
                            </h2>

                            <p class="text-sm text-slate-500">
                                Update the division or complaint type if necessary.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">

                    <div>

                        <label for="division_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            Division
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="division_id"
                            id="division_id"
                            required
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"
                        >
                            <option value="">
                                Select Division
                            </option>

                            @foreach ($divisions as $division)
                                <option
                                    value="{{ $division->id }}"
                                    {{ old('division_id', $complaint->division_id) == $division->id ? 'selected' : '' }}
                                >
                                    {{ $division->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('division_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="complaint_category_id" class="block text-sm font-semibold text-slate-700 mb-2">
                            Complaint Type
                            <span class="text-red-500">*</span>
                        </label>

                        <select
                            name="complaint_category_id"
                            id="complaint_category_id"
                            required
                            disabled
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500 disabled:bg-slate-100 disabled:text-slate-400"
                        >
                            <option value="">
                                Select a division first
                            </option>
                        </select>

                        @error('complaint_category_id')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                </div>

            </div>

            <div
                id="commercialAccountSection"
                class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
            >

                <div class="px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-violet-100 text-violet-700 flex items-center justify-center">
                            <i class="fas fa-user-check"></i>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Consumer Account
                            </h2>

                            <p class="text-sm text-slate-500">
                                This concern is linked automatically to your registered SWD account.
                            </p>
                        </div>

                    </div>
                </div>

                <div class="p-6">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide font-semibold text-slate-400">
                                Account Number
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                {{ $consumer?->account_number ?: 'Not available' }}
                            </p>
                        </div>

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">
                            <p class="text-xs uppercase tracking-wide font-semibold text-slate-400">
                                Account Holder
                            </p>

                            <p class="mt-1 font-semibold text-slate-800">
                                {{ $consumer?->full_name ?: auth()->user()->full_name }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-100">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center">
                            <i class="fas fa-message"></i>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Describe Your Concern
                            </h2>

                            <p id="descriptionHelp" class="text-sm text-slate-500">
                                Update the details of your concern.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="p-6">

                    <label for="description" class="block text-sm font-semibold text-slate-700 mb-2">
                        Description
                        <span class="text-red-500">*</span>
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        rows="6"
                        maxlength="5000"
                        required
                        class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"
                    >{{ old('description', $complaint->description) }}</textarea>

                    <div class="mt-2 flex items-start justify-between gap-4">

                        <p id="descriptionGuidance" class="text-xs text-slate-500">
                            Include useful information that may help SWD personnel understand your concern.
                        </p>

                        <span id="descriptionCount" class="shrink-0 text-xs text-slate-400">
                            0 / 5000
                        </span>

                    </div>

                    @error('description')
                        <p class="mt-2 text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div
                id="engineeringLocationSection"
                class="hidden bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden"
            >

                <div class="px-6 py-5 border-b border-slate-100">

                    <div class="flex items-center gap-3">

                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <i class="fas fa-location-dot"></i>
                        </div>

                        <div>
                            <h2 class="font-semibold text-slate-900">
                                Service Location
                            </h2>

                            <p class="text-sm text-slate-500">
                                Update the location if necessary.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="p-6 space-y-6">

                    <div>

                        <label for="address" class="block text-sm font-semibold text-slate-700 mb-2">
                            Address
                            <span class="text-red-500">*</span>
                        </label>

                        <textarea
                            name="address"
                            id="address"
                            rows="3"
                            maxlength="500"
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"
                        >{{ old('address', $complaint->address) }}</textarea>

                        @error('address')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div>

                        <label for="landmark" class="block text-sm font-semibold text-slate-700 mb-2">
                            Nearby Landmark
                            <span class="font-normal text-slate-400">
                                (Optional)
                            </span>
                        </label>

                        <input
                            type="text"
                            name="landmark"
                            id="landmark"
                            maxlength="255"
                            value="{{ old('landmark', $complaint->landmark) }}"
                            placeholder="Example: Near the barangay hall"
                            class="w-full rounded-xl border-slate-300 focus:border-sky-500 focus:ring-sky-500"
                        >

                        @error('landmark')
                            <p class="mt-2 text-sm text-red-600">
                                {{ $message }}
                            </p>
                        @enderror

                    </div>

                    <div class="border-t border-slate-100 pt-6">

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">

                            <div>
                                <h3 class="text-sm font-semibold text-slate-700">
                                    Pin Location
                                    <span class="font-normal text-slate-400">
                                        (Optional)
                                    </span>
                                </h3>

                                <p class="mt-1 text-xs text-slate-500">
                                    Click the map or drag the marker if you need to update the location.
                                </p>
                            </div>

                            <button
                                type="button"
                                id="locateMe"
                                class="inline-flex items-center justify-center gap-2 rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700 hover:bg-sky-100 transition"
                            >
                                <i class="fas fa-location-crosshairs"></i>
                                Use My Location
                            </button>

                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-200">
                            <div id="complaintMap" class="w-full h-80"></div>
                        </div>

                        <div
                            id="detectedLocationBox"
                            class="hidden mt-4 rounded-xl border border-sky-100 bg-sky-50 p-4"
                        >

                            <div class="flex gap-3">

                                <i class="fas fa-location-dot text-sky-600 mt-1"></i>

                                <div class="min-w-0">

                                    <p class="text-xs font-semibold uppercase tracking-wide text-sky-700">
                                        Selected Location
                                    </p>

                                    <p id="detectedLocation" class="mt-1 text-sm text-slate-700 break-words">
                                        —
                                    </p>

                                    <p id="reverseGeocodeStatus" class="mt-1 text-xs text-slate-500"></p>

                                    <button
                                        type="button"
                                        id="useDetectedAddress"
                                        class="hidden mt-3 rounded-lg bg-white border border-sky-200 px-3 py-2 text-xs font-semibold text-sky-700 hover:bg-sky-50"
                                    >
                                        <i class="fas fa-arrow-down mr-1"></i>
                                        Use detected location as address
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <input
                        type="hidden"
                        name="latitude"
                        id="latitude"
                        value="{{ old('latitude', $complaint->latitude) }}"
                    >

                    <input
                        type="hidden"
                        name="longitude"
                        id="longitude"
                        value="{{ old('longitude', $complaint->longitude) }}"
                    >

                </div>

            </div>

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">

                <div class="px-6 py-5 border-b border-slate-100">

                    <div class="flex items-center gap-3">

                        <div
                            id="evidenceIconBox"
                            class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center"
                        >
                            <i id="evidenceIcon" class="fas fa-camera"></i>
                        </div>

                        <div>
                            <h2 id="evidenceHeading" class="font-semibold text-slate-900">
                                Supporting Photo
                            </h2>

                            <p id="evidenceDescription" class="text-sm text-slate-500">
                                Keep the current photo or choose a replacement.
                            </p>
                        </div>

                    </div>

                </div>

                <div class="p-6 space-y-4">

                    @if ($complaint->photo)

                        <div>

                            <p id="currentEvidenceLabel" class="mb-2 text-sm font-semibold text-slate-700">
                                Current Photo
                            </p>

                            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                                <img
                                    src="{{ asset('storage/' . $complaint->photo) }}"
                                    alt="Current complaint photo"
                                    class="w-full max-h-80 object-contain"
                                >
                            </div>

                        </div>

                    @endif

                    <label
                        for="photo"
                        class="flex cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 px-6 py-8 text-center hover:border-sky-400 hover:bg-sky-50/50 transition"
                    >

                        <div class="w-12 h-12 rounded-xl bg-white shadow-sm flex items-center justify-center text-sky-600">
                            <i class="fas fa-image text-xl"></i>
                        </div>

                        <p id="uploadTitle" class="mt-3 text-sm font-semibold text-slate-700">
                            {{ $complaint->photo ? 'Choose a replacement photo' : 'Choose a supporting photo' }}
                        </p>

                        <p id="uploadHelp" class="mt-1 text-xs text-slate-500">
                            JPG, JPEG, PNG or WEBP up to 5 MB
                        </p>

                        <input
                            type="file"
                            name="photo"
                            id="photo"
                            accept="image/jpeg,image/png,image/webp"
                            class="hidden"
                        >

                    </label>

                    <div id="photoPreviewContainer" class="hidden">

                        <div class="flex items-center gap-4 rounded-xl border border-sky-200 bg-sky-50 p-4">

                            <img
                                id="photoPreview"
                                src=""
                                alt="New photo preview"
                                class="w-20 h-20 rounded-lg object-cover border border-slate-200"
                            >

                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-semibold text-sky-700">
                                    New image selected
                                </p>

                                <p id="photoName" class="mt-1 text-sm font-semibold text-slate-700 truncate"></p>
                                <p id="photoSize" class="mt-1 text-xs text-slate-500"></p>
                            </div>

                            <button
                                type="button"
                                id="removePhoto"
                                class="w-9 h-9 rounded-lg text-red-600 hover:bg-red-50 transition"
                            >
                                <i class="fas fa-trash-can"></i>
                            </button>

                        </div>

                    </div>

                    @error('photo')
                        <p class="text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3">

                <a
                    href="{{ route('consumer.complaints.show', $complaint) }}"
                    class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    :disabled="saving"
                    class="inline-flex items-center justify-center gap-2 rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white hover:bg-sky-800 disabled:opacity-60 disabled:cursor-not-allowed transition"
                >

                    <span x-show="!saving">
                        <i class="fas fa-floppy-disk mr-2"></i>
                        Save Changes
                    </span>

                    <span x-show="saving" x-cloak>
                        <i class="fas fa-spinner fa-spin mr-2"></i>
                        Saving...
                    </span>

                </button>

            </div>

        </form>

    </div>

    @push('scripts')

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        >

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const divisions = @json($divisions);

                const divisionSelect =
                    document.getElementById('division_id');

                const complaintTypeSelect =
                    document.getElementById('complaint_category_id');

                const commercialAccountSection =
                    document.getElementById('commercialAccountSection');

                const engineeringLocationSection =
                    document.getElementById('engineeringLocationSection');

                const description =
                    document.getElementById('description');

                const descriptionHelp =
                    document.getElementById('descriptionHelp');

                const descriptionGuidance =
                    document.getElementById('descriptionGuidance');

                const descriptionCount =
                    document.getElementById('descriptionCount');

                const addressInput =
                    document.getElementById('address');

                const evidenceHeading =
                    document.getElementById('evidenceHeading');

                const evidenceDescription =
                    document.getElementById('evidenceDescription');

                const evidenceIcon =
                    document.getElementById('evidenceIcon');

                const currentEvidenceLabel =
                    document.getElementById('currentEvidenceLabel');

                const uploadTitle =
                    document.getElementById('uploadTitle');

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

                const detectedLocationBox =
                    document.getElementById('detectedLocationBox');

                const detectedLocation =
                    document.getElementById('detectedLocation');

                const reverseGeocodeStatus =
                    document.getElementById('reverseGeocodeStatus');

                const useDetectedAddress =
                    document.getElementById('useDetectedAddress');

                const locateMe =
                    document.getElementById('locateMe');

                const selectedComplaintType =
                    @json(old('complaint_category_id', $complaint->complaint_category_id));

                const hasExistingPhoto =
                    @json((bool) $complaint->photo);

                let map = null;
                let marker = null;
                let mapInitialized = false;
                let lastDetectedAddress = '';

                const defaultLat = 10.9447;
                const defaultLng = 123.4247;

                function getSelectedDivision() {

                    if (!divisionSelect || !divisionSelect.value) {
                        return null;
                    }

                    return divisions.find(function(division) {

                        return String(division.id) ===
                            String(divisionSelect.value);

                    }) || null;
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

                    return getSelectedDivisionName() ===
                        'engineering operation';
                }

                function isCommercialDivision() {

                    return getSelectedDivisionName() ===
                        'commercial services';
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

                        const option =
                            document.createElement('option');

                        option.value = '';

                        option.textContent =
                            'Select a division first';

                        complaintTypeSelect.appendChild(
                            option
                        );

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
                    });
                }

                function updateDescriptionGuidance() {

                    if (
                        !description ||
                        !descriptionHelp ||
                        !descriptionGuidance
                    ) {
                        return;
                    }

                    if (!isCommercialDivision()) {

                        descriptionHelp.textContent =
                            'Update the details of your concern.';

                        descriptionGuidance.textContent =
                            'Include useful information that may help SWD personnel understand your concern.';

                        description.placeholder =
                            'Describe the water service concern, when you noticed it, and any important details that may help Sagay Water District personnel...';

                        return;
                    }

                    descriptionHelp.textContent =
                        'Update the details of your account, billing, meter, or other Commercial Services concern.';

                    description.placeholder =
                        'Describe your Commercial Services concern and include any relevant bill, meter, account, or service information...';

                    const selectedOption =
                        complaintTypeSelect.options[
                            complaintTypeSelect.selectedIndex
                        ];

                    const complaintTypeName =
                        selectedOption ?
                            String(
                                selectedOption.textContent || ''
                            ).trim().toLowerCase() :
                            '';

                    let guidance =
                        'Describe the concern clearly and include relevant information from your bill, meter, account, or service record when available. Do not include your password.';

                    if (
                        complaintTypeName.includes('erroneous') &&
                        complaintTypeName.includes('reading')
                    ) {

                        guidance =
                            'Describe the meter reading shown on your bill and the reading currently displayed on your meter, if available. You may attach a clear photo of the bill or meter as supporting evidence.';

                    } else if (
                        complaintTypeName.includes('large consumption') ||
                        complaintTypeName.includes('high consumption')
                    ) {

                        guidance =
                            'Describe the unusual increase in consumption. Include the affected billing period and any meter reading or consumption information available to you.';

                    } else if (
                        complaintTypeName.includes('re-class') ||
                        complaintTypeName.includes('reclass')
                    ) {

                        guidance =
                            'Describe your current service classification, the classification you are requesting, and the reason for the requested change.';

                    } else if (
                        complaintTypeName.includes('malfunction') &&
                        complaintTypeName.includes('meter')
                    ) {

                        guidance =
                            'Describe what you observed with the meter, such as being stopped, damaged, unreadable, leaking, or operating unusually.';

                    } else if (
                        complaintTypeName.includes('bill')
                    ) {

                        guidance =
                            'Describe the billing concern clearly. Include the billing period, amount, meter reading, or other relevant bill information when available.';

                    } else if (
                        complaintTypeName.includes('account')
                    ) {

                        guidance =
                            'Describe the account-related concern and the specific information or service you need Sagay Water District to review.';
                    }

                    descriptionGuidance.textContent =
                        guidance;
                }

                function updateEvidenceSection() {

                    if (isCommercialDivision()) {

                        if (evidenceHeading) {
                            evidenceHeading.textContent =
                                'Supporting Evidence';
                        }

                        if (evidenceDescription) {
                            evidenceDescription.textContent =
                                'Keep the current evidence or choose a replacement image.';
                        }

                        if (evidenceIcon) {
                            evidenceIcon.className =
                                'fas fa-file-image';
                        }

                        if (currentEvidenceLabel) {
                            currentEvidenceLabel.textContent =
                                'Current Evidence';
                        }

                        if (uploadTitle) {

                            uploadTitle.textContent =
                                hasExistingPhoto ?
                                    'Choose replacement evidence' :
                                    'Choose supporting evidence';
                        }

                        return;
                    }

                    if (evidenceHeading) {
                        evidenceHeading.textContent =
                            'Supporting Photo';
                    }

                    if (evidenceDescription) {
                        evidenceDescription.textContent =
                            'Keep the current photo or choose a replacement.';
                    }

                    if (evidenceIcon) {
                        evidenceIcon.className =
                            'fas fa-camera';
                    }

                    if (currentEvidenceLabel) {
                        currentEvidenceLabel.textContent =
                            'Current Photo';
                    }

                    if (uploadTitle) {

                        uploadTitle.textContent =
                            hasExistingPhoto ?
                                'Choose a replacement photo' :
                                'Choose a supporting photo';
                    }
                }

                function initializeMap() {

                    if (
                        mapInitialized ||
                        !mapElement ||
                        typeof L === 'undefined'
                    ) {
                        return;
                    }

                    map =
                        L.map(
                            mapElement,
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

                    const savedLat =
                        parseFloat(
                            latitudeInput.value
                        );

                    const savedLng =
                        parseFloat(
                            longitudeInput.value
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

                    mapInitialized = true;

                    requestAnimationFrame(function() {

                        requestAnimationFrame(function() {

                            if (!map) {
                                return;
                            }

                            map.invalidateSize();

                            const currentLat =
                                parseFloat(
                                    latitudeInput.value
                                );

                            const currentLng =
                                parseFloat(
                                    longitudeInput.value
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

                            } else {

                                map.setView(
                                    [
                                        defaultLat,
                                        defaultLng
                                    ],
                                    14
                                );
                            }
                        });
                    });
                }

                function refreshMap() {

                    initializeMap();

                    if (!map) {
                        return;
                    }

                    setTimeout(function() {

                        if (!map) {
                            return;
                        }

                        map.invalidateSize();

                        const currentLat =
                            parseFloat(
                                latitudeInput.value
                            );

                        const currentLng =
                            parseFloat(
                                longitudeInput.value
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

                        } else {

                            map.setView(
                                [
                                    defaultLat,
                                    defaultLng
                                ],
                                14
                            );
                        }

                    }, 250);
                }

                function updateDivisionForm() {

                    if (isCommercialDivision()) {

                        if (commercialAccountSection) {
                            commercialAccountSection
                                .classList
                                .remove('hidden');
                        }

                        if (engineeringLocationSection) {
                            engineeringLocationSection
                                .classList
                                .add('hidden');
                        }

                        if (addressInput) {
                            addressInput.required = false;
                        }

                    } else {

                        if (commercialAccountSection) {
                            commercialAccountSection
                                .classList
                                .add('hidden');
                        }

                        if (engineeringLocationSection) {
                            engineeringLocationSection
                                .classList
                                .remove('hidden');
                        }

                        if (addressInput) {
                            addressInput.required =
                                isEngineeringDivision();
                        }

                        if (isEngineeringDivision()) {
                            refreshMap();
                        }
                    }

                    updateDescriptionGuidance();
                    updateEvidenceSection();
                }

                async function reverseGeocode(
                    lat,
                    lng
                ) {

                    if (
                        !detectedLocationBox ||
                        !detectedLocation ||
                        !reverseGeocodeStatus ||
                        !useDetectedAddress
                    ) {
                        return;
                    }

                    detectedLocationBox
                        .classList
                        .remove('hidden');

                    detectedLocation.textContent =
                        'Detecting location...';

                    reverseGeocodeStatus.textContent =
                        'Please wait while the selected location is identified.';

                    useDetectedAddress
                        .classList
                        .add('hidden');

                    lastDetectedAddress = '';

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
                                    method: 'GET',
                                    headers: {
                                        'Accept':
                                            'application/json'
                                    }
                                }
                            );

                        if (!response.ok) {

                            throw new Error(
                                'Reverse geocoding request failed.'
                            );
                        }

                        const data =
                            await response.json();

                        if (
                            !data ||
                            !data.display_name
                        ) {

                            throw new Error(
                                'No readable address was found.'
                            );
                        }

                        lastDetectedAddress =
                            String(
                                data.display_name
                            ).trim();

                        detectedLocation.textContent =
                            lastDetectedAddress;

                        reverseGeocodeStatus.textContent =
                            'Review the detected address before using it for your complaint.';

                        useDetectedAddress
                            .classList
                            .remove('hidden');

                    } catch (error) {

                        console.error(
                            'Reverse geocoding error:',
                            error
                        );

                        lastDetectedAddress = '';

                        detectedLocation.textContent =
                            'Unable to identify a readable address.';

                        reverseGeocodeStatus.textContent =
                            'You can still enter the service address manually.';

                        useDetectedAddress
                            .classList
                            .add('hidden');
                    }
                }

                function setLocation(
                    lat,
                    lng,
                    shouldReverseGeocode = true
                ) {

                    if (
                        !map ||
                        !latitudeInput ||
                        !longitudeInput
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
                                ],
                                {
                                    draggable: true
                                }
                            ).addTo(map);

                        marker.on(
                            'dragend',
                            function(event) {

                                const position =
                                    event
                                        .target
                                        .getLatLng();

                                map.setView(
                                    [
                                        position.lat,
                                        position.lng
                                    ],
                                    map.getZoom()
                                );

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
                    }
                }

                if (
                    divisionSelect &&
                    complaintTypeSelect
                ) {

                    divisionSelect.addEventListener(
                        'change',
                        function() {

                            populateComplaintTypes(
                                this.value,
                                null
                            );

                            updateDivisionForm();
                        }
                    );

                    complaintTypeSelect.addEventListener(
                        'change',
                        function() {

                            updateDescriptionGuidance();
                        }
                    );

                    if (divisionSelect.value) {

                        populateComplaintTypes(
                            divisionSelect.value,
                            selectedComplaintType
                        );
                    }

                    updateDivisionForm();
                }

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

                if (description) {

                    updateDescriptionCount();

                    description.addEventListener(
                        'input',
                        updateDescriptionCount
                    );
                }

                function formatFileSize(bytes) {

                    if (bytes < 1024) {
                        return bytes + ' bytes';
                    }

                    if (
                        bytes <
                        1024 * 1024
                    ) {

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

                    if (photoPreviewContainer) {

                        photoPreviewContainer
                            .classList
                            .add('hidden');
                    }
                }

                if (photoInput) {

                    photoInput.addEventListener(
                        'change',
                        function() {

                            const file =
                                this.files &&
                                this.files[0];

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

                            const allowedTypes = [
                                'image/jpeg',
                                'image/png',
                                'image/webp'
                            ];

                            if (
                                !allowedTypes.includes(
                                    file.type
                                )
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

                                    if (photoPreview) {

                                        photoPreview.src =
                                            event.target.result;
                                    }

                                    if (photoName) {

                                        photoName.textContent =
                                            file.name;
                                    }

                                    if (photoSize) {

                                        photoSize.textContent =
                                            formatFileSize(
                                                file.size
                                            );
                                    }

                                    if (photoPreviewContainer) {

                                        photoPreviewContainer
                                            .classList
                                            .remove('hidden');
                                    }
                                };

                            reader.readAsDataURL(
                                file
                            );
                        }
                    );
                }

                if (removePhoto) {

                    removePhoto.addEventListener(
                        'click',
                        function() {

                            clearPhotoPreview();
                        }
                    );
                }

                if (useDetectedAddress) {

                    useDetectedAddress.addEventListener(
                        'click',
                        function(event) {

                            event.preventDefault();

                            if (
                                !lastDetectedAddress ||
                                !addressInput
                            ) {
                                return;
                            }

                            addressInput.value =
                                lastDetectedAddress;

                            addressInput.dispatchEvent(
                                new Event(
                                    'input',
                                    {
                                        bubbles: true
                                    }
                                )
                            );

                            addressInput.dispatchEvent(
                                new Event(
                                    'change',
                                    {
                                        bubbles: true
                                    }
                                )
                            );

                            reverseGeocodeStatus.textContent =
                                'The detected location was copied to the address field. You may edit it to add local details such as the purok or house number.';

                            addressInput.scrollIntoView({
                                behavior: 'smooth',
                                block: 'center'
                            });

                            setTimeout(function() {

                                addressInput.focus();

                            }, 300);
                        }
                    );
                }

if (locateMe) {

    locateMe.addEventListener('click', function(event) {

        event.preventDefault();

        const button = this;

        if (!navigator.geolocation) {
            alert('Your browser does not support location services.');
            return;
        }

        initializeMap();

        if (!map) {
            alert('The map is not ready. Please refresh the page and try again.');
            return;
        }

        button.disabled = true;

        button.innerHTML =
            '<i class="fas fa-spinner fa-spin"></i> Getting Location...';

        navigator.geolocation.getCurrentPosition(

            function(position) {

                const lat =
                    position.coords.latitude;

                const lng =
                    position.coords.longitude;

                engineeringLocationSection.classList.remove(
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

                setTimeout(function() {

                    map.invalidateSize();

                    map.setView(
                        [
                            lat,
                            lng
                        ],
                        17
                    );

                }, 200);

                button.disabled = false;

                button.innerHTML =
                    '<i class="fas fa-location-crosshairs"></i> Use My Location';
            },

            function(error) {

                console.error(
                    'Geolocation error:',
                    error
                );

                button.disabled = false;

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
    });
}

                initializeMap();

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

                setTimeout(function() {

                    if (
                        isEngineeringDivision() &&
                        map
                    ) {

                        map.invalidateSize();

                        const currentLat =
                            parseFloat(
                                latitudeInput.value
                            );

                        const currentLng =
                            parseFloat(
                                longitudeInput.value
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

                        } else {

                            map.setView(
                                [
                                    defaultLat,
                                    defaultLng
                                ],
                                14
                            );
                        }
                    }

                }, 500);
            });
        </script>

    @endpush

@endsection
