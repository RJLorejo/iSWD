@extends('consumer.layouts.app')

@section('title', 'Edit Complaint')

@section('content')
@php($consumer = auth()->user()->consumer)

<div class="mx-auto max-w-5xl space-y-5">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-sky-600">Consumer Service</p>
            <h1 class="mt-1 text-2xl font-bold text-slate-900 sm:text-3xl">Edit Complaint</h1>
            <p class="mt-1 text-sm text-slate-500">Update complaint {{ $complaint->complaint_no }}.</p>
        </div>
        <a href="{{ route('consumer.complaints.show', $complaint) }}" class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50"><i class="fas fa-arrow-left mr-2"></i>Back to Complaint</a>
    </div>

    @if ($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4" role="alert">
            <p class="font-semibold text-red-900">Please check your information</p>
            <ul class="mt-2 list-inside list-disc text-sm text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    <form id="complaintEditForm" method="POST" action="{{ route('consumer.complaints.update', $complaint) }}" enctype="multipart/form-data" class="space-y-5" x-data="{ saving: false }" @submit="saving = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="ai_analysis_token" id="ai_analysis_token">

        <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-900">1. Tell Us What Happened</h2><p class="mt-1 text-sm text-slate-500">Review or update your description using your own words.</p></div>
            <div class="space-y-4 p-5 sm:p-6">
                <div class="flex items-center justify-between"><label for="description" class="text-sm font-semibold text-slate-700">Your Concern <span class="text-red-500">* required</span></label><span id="descriptionCount" class="text-xs text-slate-400">0 / 5000</span></div>
                <textarea id="description" name="description" rows="3" maxlength="5000" required class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm leading-6 focus:border-sky-500 focus:ring-sky-500" placeholder="Describe what happened and when it started.">{{ old('description', $complaint->description) }}</textarea>
                <p class="text-xs text-slate-500"><i class="fas fa-language mr-1 text-sky-600"></i>English, Filipino, Hiligaynon, or mixed language is accepted.</p>
                @error('description')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                <div class="flex flex-col gap-3 rounded-xl border border-sky-100 bg-sky-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                    <div><p class="text-sm font-semibold text-slate-900">Want to check the complaint type again?</p><p class="mt-1 text-xs text-slate-500">Re-analyze your current description using AI.</p></div>
                    <button type="button" id="analyzeConcern" class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 disabled:opacity-60"><i class="fas fa-wand-magic-sparkles mr-2"></i><span id="analyzeConcernText">Re-analyze Concern</span></button>
                </div>
                <div id="aiAnalysisError" class="hidden rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700" role="alert"></div>
                <div id="aiAnalysisCard" class="hidden overflow-hidden rounded-xl border border-sky-200">
                    <div class="border-b border-sky-100 bg-sky-50 p-4"><p class="font-semibold text-slate-900">Updated AI Suggestion</p><p class="text-xs text-slate-500">Based on your current description.</p></div>
                    <div class="space-y-4 p-4">
                        <div><p class="text-xs uppercase text-slate-400">Suggested SWD Complaint Type</p><p id="aiComplaintType" class="mt-1 text-lg font-bold text-slate-900">—</p></div>
                        <div class="rounded-xl bg-slate-50 p-3"><p class="text-xs text-slate-500">Handled By</p><p id="aiDivision" class="mt-1 font-semibold text-slate-800">—</p></div>
                        <div id="aiSupportingEvidenceSection" class="hidden rounded-xl border bg-slate-50 p-4"><p class="text-xs font-semibold uppercase text-slate-400">Why this was suggested</p><p id="aiSupportingSummary" class="mt-2 text-sm text-slate-600"></p><ul id="aiSupportingIndicators" class="mt-3 list-inside list-disc text-xs text-slate-600"></ul></div>
                        <div class="hidden"><p id="aiAnalysisStatus"></p><ul id="aiSignals"></ul><p id="aiReview"></p></div>
                        <div id="aiMatchWarning" class="hidden rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800">No active SWD complaint type could be matched. You can choose a type manually below.</div>
                        <div class="flex flex-col gap-3 border-t pt-4 sm:flex-row sm:items-center sm:justify-between"><p class="text-xs text-slate-500">The current type changes only when you use this suggestion.</p><button type="button" id="useAiClassification" class="rounded-xl bg-sky-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-sky-800 disabled:opacity-50"><i class="fas fa-check mr-2"></i>Use Updated Suggestion</button></div>
                    </div>
                </div>
            </div>
        </section>

        <section id="classificationSection" tabindex="-1" class="overflow-hidden rounded-2xl border border-slate-200 bg-white transition-shadow duration-300">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-900">2. SWD Complaint Type</h2><p class="mt-1 text-sm text-slate-500">Confirm the suggested complaint type or choose it manually.</p></div>
            <div class="grid gap-5 p-5 md:grid-cols-2 sm:p-6">
                <div><label for="division_id" class="mb-2 block text-sm font-semibold text-slate-700">Service Division <span class="text-red-500">* required</span></label>
                    <select id="division_id" name="division_id" required class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm">
                        <option value="">Select service division</option>
                        @foreach ($divisions as $division)
                            <option value="{{ $division->id }}" {{ (string)old('division_id', $complaint->division_id) === (string)$division->id ? 'selected' : '' }}>{{ $division->name }}</option>
                        @endforeach
                    </select>
                    @error('division_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div><label for="complaint_category_id" class="mb-2 block text-sm font-semibold text-slate-700">Complaint Type <span class="text-red-500">* required</span></label>
                    <select id="complaint_category_id" name="complaint_category_id" required disabled class="w-full rounded-xl border-slate-300 bg-white px-4 py-3 text-sm disabled:bg-slate-100"><option value="">Select service division first</option></select>
                    @error('complaint_category_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section id="commercialAccountSection" class="hidden overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="border-b p-5"><h2 class="font-semibold text-slate-900">Your Consumer Account</h2><p class="mt-1 text-sm text-slate-500">This concern uses your registered SWD account.</p></div>
            <div class="grid gap-3 p-5 sm:grid-cols-2"><div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Account Number</p><p class="mt-1 font-semibold">{{ $consumer?->account_number ?: 'Not available' }}</p></div><div class="rounded-xl bg-slate-50 p-4"><p class="text-xs text-slate-500">Account Holder</p><p class="mt-1 font-semibold">{{ $consumer?->full_name ?: auth()->user()->full_name }}</p></div></div>
        </section>

        <section id="engineeringLocationSection" class="overflow-hidden rounded-2xl border border-slate-200 bg-white transition-shadow duration-300">
            <div class="border-b border-slate-100 px-5 py-4"><h2 class="font-semibold text-slate-900">3. Where Is the Problem?</h2><p class="mt-1 text-sm text-slate-500">Confirm the saved location or pin the correct problem location.</p></div>
            <div class="space-y-5 p-5 sm:p-6">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"><div><p class="text-sm font-semibold text-slate-700">Pin the Problem Location <span id="mapRequiredBadge" class="text-red-500">* required for Engineering and Operation</span></p><p class="mt-1 text-xs text-slate-500">Click the map or drag the marker. Pin the actual problem location.</p></div><button type="button" id="locateMe" class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-2.5 text-sm font-semibold text-sky-700"><i class="fas fa-location-crosshairs mr-2"></i>Use My Location</button></div>
                <div class="overflow-hidden rounded-xl border bg-slate-100"><div id="complaintMap" class="h-72 w-full sm:h-80"></div></div>
                <p id="mapAddressStatus" role="status" aria-live="polite" class="rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800">Checking saved location...</p>
                <div><label for="address" class="mb-2 block text-sm font-semibold text-slate-700">Service Address <span id="addressRequiredBadge" class="text-red-500">* required</span></label><input type="text" id="address" name="address" maxlength="500" value="{{ old('address', $complaint->address) }}" placeholder="Enter the problem address" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm">@error('address')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <div><label for="landmark" class="mb-2 block text-sm font-semibold text-slate-700">Nearby Landmark <span class="font-normal text-slate-400">(Optional)</span></label><input type="text" id="landmark" name="landmark" maxlength="255" value="{{ old('landmark', $complaint->landmark) }}" placeholder="Example: Near Sagay Public Market" class="w-full rounded-xl border-slate-300 px-4 py-3 text-sm">@error('landmark')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror</div>
                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $complaint->latitude) }}">
                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $complaint->longitude) }}">
                @error('latitude')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @error('longitude')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <section id="evidenceSection" class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
            <div class="border-b border-slate-100 p-5"><h2 class="font-semibold text-slate-900">4. Supporting Photos <span class="text-red-500">* required</span></h2><p class="mt-1 text-sm text-slate-500">Keep or remove existing photos, then add more. Final total must be 1 to 5 photos.</p></div>
            <div class="space-y-5 p-5 sm:p-6">
                @if ($complaint->photos->count())
                    <div><p class="mb-3 text-sm font-semibold text-slate-700">Existing Photos</p><div class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5" id="existingPhotoGrid">
                        @foreach ($complaint->photos as $existingPhoto)
                            <div class="existing-photo-card overflow-hidden rounded-xl border bg-slate-50" data-photo-id="{{ $existingPhoto->id }}">
                                <div class="aspect-square"><img src="{{ asset('storage/' . $existingPhoto->photo) }}" alt="Supporting photo" class="h-full w-full object-cover"></div>
                                <div class="p-2"><button type="button" class="remove-existing-photo w-full rounded-lg border border-red-200 bg-red-50 px-2 py-2 text-xs font-semibold text-red-600" data-photo-id="{{ $existingPhoto->id }}"><i class="fas fa-trash-can mr-1"></i>Remove</button></div>
                            </div>
                        @endforeach
                    </div></div>
                @endif
                <div id="removedPhotoInputs"></div>
                <input type="file" id="photos" name="photos[]" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden">
                <input type="file" id="devicePhotoPicker" multiple accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="hidden">
                <div class="grid gap-3 sm:grid-cols-2"><button type="button" id="takePhotoButton" class="rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm font-semibold text-sky-700"><i class="fas fa-camera mr-2"></i>Take Photo</button><button type="button" id="choosePhotoButton" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700"><i class="fas fa-images mr-2"></i>Choose from Device</button></div>
                <p class="text-xs text-slate-500">JPG, JPEG, PNG or WEBP. Maximum 5 MB each.</p>
                <div id="newPhotosSection" class="hidden"><p class="mb-3 text-sm font-semibold text-slate-700">New Photos</p><div id="photoPreviewGrid" class="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5"></div></div>
                <p id="photoValidationMessage" class="hidden text-sm font-medium text-red-600" role="alert"></p>
                @error('photos')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @error('photos.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @error('remove_photos')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
                @error('remove_photos.*')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </section>

        <div id="cameraModal" class="fixed inset-0 z-[1000] hidden items-center justify-center bg-slate-950/80 p-4">
            <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b p-4"><div><h3 class="font-semibold">Take Photo</h3><p class="text-xs text-slate-500">Position the concern clearly, then capture.</p></div><button type="button" id="closeCameraButton" class="p-2" aria-label="Close camera"><i class="fas fa-xmark"></i></button></div>
                <div class="bg-black"><video id="cameraVideo" autoplay playsinline muted class="max-h-[65vh] w-full object-contain"></video><canvas id="cameraCanvas" class="hidden"></canvas></div>
                <div class="flex justify-end gap-3 p-4"><button type="button" id="cancelCameraButton" class="rounded-xl border px-4 py-2">Cancel</button><button type="button" id="capturePhotoButton" class="rounded-xl bg-sky-700 px-4 py-2 text-white"><i class="fas fa-camera mr-2"></i>Capture Photo</button></div>
            </div>
        </div>

        <div class="rounded-xl border border-sky-100 bg-sky-50 p-4"><p class="text-sm font-semibold text-sky-900"><i class="fas fa-circle-info mr-2"></i>Before You Save</p><p id="beforeSubmitText" class="mt-1 text-xs text-sky-800">Check your concern, complaint type, service location, and photos.</p></div>
        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <a href="{{ route('consumer.complaints.show', $complaint) }}" class="rounded-xl border border-slate-300 bg-white px-5 py-3 text-center text-sm font-semibold text-slate-700">Cancel</a>
            <button type="submit" id="saveChangesButton" :disabled="saving" class="min-w-[180px] rounded-xl bg-sky-700 px-6 py-3 text-sm font-semibold text-white disabled:opacity-70"><span x-show="!saving"><i class="fas fa-floppy-disk mr-2"></i>Save Changes</span><span x-show="saving" x-cloak><i class="fas fa-spinner fa-spin mr-2"></i>Saving...</span></button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const divisions = @json($divisions);
    const initialCategoryId = @json(old('complaint_category_id', $complaint->complaint_category_id));
    const form = document.getElementById('complaintEditForm');
    const divisionSelect = document.getElementById('division_id');
    const typeSelect = document.getElementById('complaint_category_id');
    const classificationSection = document.getElementById('classificationSection');
    const locationSection = document.getElementById('engineeringLocationSection');
    const commercialSection = document.getElementById('commercialAccountSection');
    const description = document.getElementById('description');
    const tokenInput = document.getElementById('ai_analysis_token');
    const addressInput = document.getElementById('address');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const mapStatus = document.getElementById('mapAddressStatus');
    const photoInput = document.getElementById('photos');
    const devicePicker = document.getElementById('devicePhotoPicker');
    const photoMessage = document.getElementById('photoValidationMessage');
    const previewGrid = document.getElementById('photoPreviewGrid');
    const newPhotosSection = document.getElementById('newPhotosSection');
    const removedInputs = document.getElementById('removedPhotoInputs');
    const evidenceSection = document.getElementById('evidenceSection');
    const cameraModal = document.getElementById('cameraModal');
    const cameraVideo = document.getElementById('cameraVideo');
    const cameraCanvas = document.getElementById('cameraCanvas');
    let selectedPhotos = [], removedPhotoIds = new Set(), cameraStream = null;
    let map = null, marker = null, mapInitialized = false, matchedAiCategory = null;

    const getDivision = () => divisions.find(d => String(d.id) === String(divisionSelect.value)) || null;
    const getDivisionName = () => String(getDivision()?.name || '').toLowerCase();
    const isEngineering = () => getDivisionName().includes('engineering') || getDivisionName().includes('operation');
    const isCommercial = () => getDivisionName().includes('commercial');
    const getTypes = d => d?.complaint_types ?? d?.complaintTypes ?? [];
    const centerScroll = (el, delay = 100) => setTimeout(() => el?.scrollIntoView({behavior:'smooth', block:'center'}), delay);
    const highlight = (el, color = 'ring-sky-300') => {
        if (!el) return;
        el.classList.add('ring-2', color, 'ring-offset-2');
        setTimeout(() => el.classList.remove('ring-2', color, 'ring-offset-2'), 2200);
    };
    function status(message, state = 'warning') {
        const classes = {
            warning:'rounded-xl border border-amber-200 bg-amber-50 px-3 py-3 text-xs leading-5 text-amber-800',
            success:'rounded-xl border border-emerald-200 bg-emerald-50 px-3 py-3 text-xs leading-5 text-emerald-800',
            error:'rounded-xl border border-red-200 bg-red-50 px-3 py-3 text-xs leading-5 text-red-700',
            neutral:'rounded-xl border border-slate-200 bg-slate-50 px-3 py-3 text-xs leading-5 text-slate-600'
        };
        mapStatus.className = classes[state] || classes.neutral;
        mapStatus.textContent = message;
    }
    function validCoordinates() {
        if (!latInput.value.trim() || !lngInput.value.trim()) return false;
        const lat = Number(latInput.value), lng = Number(lngInput.value);
        return Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180;
    }
    function populateTypes(divisionId, selectedId = null) {
        typeSelect.innerHTML = '';
        if (!divisionId) {
            typeSelect.disabled = true;
            typeSelect.add(new Option('Select service division first', ''));
            return;
        }
        const division = divisions.find(d => String(d.id) === String(divisionId));
        const types = getTypes(division);
        if (!types.length) {
            typeSelect.disabled = true;
            typeSelect.add(new Option('No complaint types available', ''));
            return;
        }
        typeSelect.disabled = false;
        typeSelect.add(new Option('Select complaint type', ''));
        types.forEach(t => {
            const option = new Option(t.name, t.id);
            if (selectedId !== null && String(t.id) === String(selectedId)) option.selected = true;
            typeSelect.add(option);
        });
    }
    function updateDivisionForm() {
        const engineering = isEngineering();
        commercialSection.classList.toggle('hidden', !isCommercial());
        // Show the map only for Engineering and Operation complaints.
        // Commercial Services complaints should not display the map.
        locationSection.classList.toggle('hidden', !engineering);
        addressInput.required = engineering;
        const addressBadge = document.getElementById('addressRequiredBadge');
        addressBadge.textContent = engineering ? '* required' : '(Optional for this division)';
        addressBadge.className = engineering ? 'text-red-500' : 'font-normal text-slate-400';
        const mapBadge = document.getElementById('mapRequiredBadge');
        mapBadge.textContent = engineering ? '* required' : '(Optional for this division)';
        mapBadge.className = engineering ? 'text-red-500' : 'font-normal text-slate-400';
        document.getElementById('beforeSubmitText').textContent = engineering
            ? 'Check your concern, complaint type, exact service location, and photos before saving.'
            : 'Check your concern, complaint type, address if applicable, and photos before saving.';
        if (engineering) {
            initializeMap();
            setTimeout(() => map?.invalidateSize(), 200);
        }
    }
    divisionSelect.addEventListener('change', () => { populateTypes(divisionSelect.value); updateDivisionForm(); });
    typeSelect.addEventListener('change', () => { tokenInput.value = ''; });
    description.addEventListener('input', () => {
        document.getElementById('descriptionCount').textContent = description.value.length + ' / 5000';
        tokenInput.value = '';
    });
    document.getElementById('descriptionCount').textContent = description.value.length + ' / 5000';
    populateTypes(divisionSelect.value, initialCategoryId);
    updateDivisionForm();

    // AI complaint re-analysis and selection.
    function pathValue(obj, paths) {
        for (const path of paths) {
            const value = path.split('.').reduce((v, k) => v && v[k] !== undefined ? v[k] : null, obj);
            if (value !== null && value !== undefined && value !== '') return value;
        }
        return null;
    }
    function strings(value) {
        if (Array.isArray(value)) return value.map(x => typeof x === 'string' ? x : (x?.label || x?.name || x?.signal || x?.reason || x?.message || '')).filter(Boolean);
        return typeof value === 'string' && value.trim() ? [value.trim()] : [];
    }
    function renderAnalysis(payload) {
        const analysis = payload.analysis || {};
        matchedAiCategory = payload.matched_category || null;
        document.getElementById('aiComplaintType').textContent = pathValue(analysis, ['classification.complaint_type','complaint_type','classification.type','predicted_type']) || matchedAiCategory?.name || 'Not identified';
        document.getElementById('aiDivision').textContent = matchedAiCategory?.division_name || 'No active division matched';
        const summary = pathValue(analysis, ['supporting_evidence.summary']);
        const indicators = pathValue(analysis, ['supporting_evidence.indicators']);
        document.getElementById('aiSupportingSummary').textContent = typeof summary === 'string' ? summary : '';
        const evidence = document.getElementById('aiSupportingEvidenceSection');
        evidence.classList.toggle('hidden', !summary);
        const list = document.getElementById('aiSupportingIndicators'); list.innerHTML = '';
        (Array.isArray(indicators) ? indicators : []).forEach(x => { const li = document.createElement('li'); li.textContent = String(x); list.appendChild(li); });
        document.getElementById('aiSignals').innerHTML = strings(pathValue(analysis, ['urgency.reasons'])).map(x => {
            const li = document.createElement('li'); li.textContent = x; return li.outerHTML;
        }).join('');
        document.getElementById('aiReview').textContent = strings(pathValue(analysis, ['review.reasons'])).join(' ');
        document.getElementById('aiAnalysisStatus').textContent = 'SWD personnel will review this submission.';
        document.getElementById('aiMatchWarning').classList.toggle('hidden', Boolean(matchedAiCategory));
        document.getElementById('useAiClassification').disabled = !matchedAiCategory;
        document.getElementById('aiAnalysisCard').classList.remove('hidden');
        centerScroll(document.getElementById('aiAnalysisCard'), 100);
    }
    const analyzeButton = document.getElementById('analyzeConcern');
    analyzeButton.addEventListener('click', async () => {
        const errorBox = document.getElementById('aiAnalysisError');
        const text = description.value.trim();
        errorBox.textContent = ''; errorBox.classList.add('hidden');
        if (text.length < 10) {
            errorBox.textContent = 'Please tell us a little more about what happened before using AI assistance.';
            errorBox.classList.remove('hidden'); description.focus(); return;
        }
        analyzeButton.disabled = true;
        document.getElementById('analyzeConcernText').textContent = 'Re-analyzing Concern...';
        const icon = analyzeButton.querySelector('i'); icon.className = 'fas fa-spinner fa-spin';
        try {
            const response = await fetch(@json(route('consumer.complaints.analyze')), {
                method:'POST',
                headers:{'Accept':'application/json','Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content || @json(csrf_token())},
                body:JSON.stringify({description:text})
            });
            const payload = await response.json();
            if (!response.ok || !payload.success) throw new Error(payload.errors?.description?.[0] || payload.message || 'AI assistance is temporarily unavailable.');
            tokenInput.value = payload.analysis_token || '';
            renderAnalysis(payload);
        } catch (error) {
            console.error('AI analysis error:', error);
            matchedAiCategory = null; tokenInput.value = '';
            errorBox.textContent = error.message || 'AI assistance is temporarily unavailable. Choose a type manually.';
            errorBox.classList.remove('hidden');
        } finally {
            analyzeButton.disabled = false;
            document.getElementById('analyzeConcernText').textContent = 'Re-analyze Concern';
            icon.className = 'fas fa-wand-magic-sparkles';
        }
    });
    document.getElementById('useAiClassification').addEventListener('click', function () {
        if (!matchedAiCategory) return;
        divisionSelect.value = String(matchedAiCategory.division_id);
        populateTypes(matchedAiCategory.division_id, matchedAiCategory.id);
        updateDivisionForm();
        typeSelect.dispatchEvent(new Event('change', {bubbles:true}));
        centerScroll(classificationSection, 120);
        highlight(classificationSection);
        const button = this, original = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Updated Suggestion Selected';
        button.classList.remove('bg-sky-700','hover:bg-sky-800'); button.classList.add('bg-emerald-600');
        setTimeout(() => { button.innerHTML = original; button.classList.remove('bg-emerald-600'); button.classList.add('bg-sky-700','hover:bg-sky-800'); }, 1800);
    });

    // Existing and newly selected photos.
    function existingCount() { return document.querySelectorAll('.existing-photo-card:not(.hidden)').length; }
    function photoCount() { return existingCount() + selectedPhotos.length; }
    function showPhotoError(message = '') { photoMessage.textContent = message; photoMessage.classList.toggle('hidden', !message); }
    function syncPhotoInput() { const dt = new DataTransfer(); selectedPhotos.forEach(file => dt.items.add(file)); photoInput.files = dt.files; }
    function syncRemoved() {
        removedInputs.innerHTML = '';
        removedPhotoIds.forEach(id => { const input = document.createElement('input'); input.type = 'hidden'; input.name = 'remove_photos[]'; input.value = id; removedInputs.appendChild(input); });
    }
    function renderPhotos() {
        previewGrid.innerHTML = ''; newPhotosSection.classList.toggle('hidden', !selectedPhotos.length);
        selectedPhotos.forEach((file, index) => {
            const card = document.createElement('div'); card.className = 'overflow-hidden rounded-xl border bg-slate-50';
            const wrap = document.createElement('div'); wrap.className = 'aspect-square';
            const img = document.createElement('img'); img.className = 'h-full w-full object-cover'; img.alt = 'New supporting photo'; img.src = URL.createObjectURL(file); img.onload = () => URL.revokeObjectURL(img.src); wrap.appendChild(img);
            const details = document.createElement('div'); details.className = 'p-2';
            const name = document.createElement('p'); name.className = 'truncate text-xs font-semibold'; name.textContent = file.name;
            const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'mt-2 w-full rounded-lg border border-red-200 bg-red-50 px-2 py-2 text-xs text-red-600'; remove.textContent = 'Remove';
            remove.addEventListener('click', () => { selectedPhotos.splice(index,1); syncPhotoInput(); renderPhotos(); showPhotoError(); });
            details.append(name,remove); card.append(wrap,details); previewGrid.appendChild(card);
        });
    }
    function addPhotos(files) {
        for (const file of Array.from(files || [])) {
            if (!['image/jpeg','image/png','image/webp'].includes(file.type)) { alert('Please select JPG, JPEG, PNG, or WEBP images only.'); continue; }
            if (file.size > 5 * 1024 * 1024) { alert('Each photo must not exceed 5 MB.'); continue; }
            if (photoCount() >= 5) { alert('You can keep a maximum of 5 photos in total.'); break; }
            selectedPhotos.push(file);
        }
        syncPhotoInput(); renderPhotos(); showPhotoError();
    }
    document.querySelectorAll('.remove-existing-photo').forEach(button => button.addEventListener('click', function () {
        const id = String(this.dataset.photoId || ''), card = this.closest('.existing-photo-card');
        if (!id || !card) return;
        if (photoCount() <= 1) { showPhotoError('At least 1 supporting photo must remain.'); centerScroll(evidenceSection, 50); return; }
        removedPhotoIds.add(id); card.classList.add('hidden'); syncRemoved(); showPhotoError();
    }));
    document.getElementById('choosePhotoButton').addEventListener('click', () => {
        if (photoCount() >= 5) return alert('You already have the maximum of 5 photos.');
        devicePicker.click();
    });
    devicePicker.addEventListener('change', function () { addPhotos(this.files); this.value = ''; });

    // Camera modal.
    function stopCamera() { if (cameraStream) cameraStream.getTracks().forEach(t => t.stop()); cameraStream = null; cameraVideo.srcObject = null; }
    function closeCamera() { stopCamera(); cameraModal.classList.add('hidden'); cameraModal.classList.remove('flex'); }
    document.getElementById('takePhotoButton').addEventListener('click', async () => {
        if (photoCount() >= 5) return alert('You already have the maximum of 5 photos.');
        if (!navigator.mediaDevices?.getUserMedia) return alert('Camera access requires HTTPS or localhost. Use Choose from Device instead.');
        try {
            cameraStream = await navigator.mediaDevices.getUserMedia({video:{facingMode:{ideal:'environment'}},audio:false});
            cameraVideo.srcObject = cameraStream; cameraModal.classList.remove('hidden'); cameraModal.classList.add('flex'); await cameraVideo.play();
        } catch (error) { console.error(error); stopCamera(); alert('Unable to open the camera. Allow camera access or use Choose from Device.'); }
    });
    document.getElementById('closeCameraButton').addEventListener('click', closeCamera);
    document.getElementById('cancelCameraButton').addEventListener('click', closeCamera);
    cameraModal.addEventListener('click', e => { if (e.target === cameraModal) closeCamera(); });
    document.getElementById('capturePhotoButton').addEventListener('click', () => {
        if (!cameraVideo.videoWidth || !cameraVideo.videoHeight) return;
        cameraCanvas.width = cameraVideo.videoWidth; cameraCanvas.height = cameraVideo.videoHeight;
        cameraCanvas.getContext('2d').drawImage(cameraVideo,0,0,cameraCanvas.width,cameraCanvas.height);
        cameraCanvas.toBlob(blob => { if (blob) addPhotos([new File([blob],'camera-'+Date.now()+'.jpg',{type:'image/jpeg'})]); closeCamera(); },'image/jpeg',0.9);
    });

    // Leaflet map: preserve saved coordinates and require a valid pin for Engineering/Operation.
    function initializeMap() {
        if (mapInitialized || typeof L === 'undefined') return;
        map = L.map('complaintMap',{scrollWheelZoom:false}).setView([10.8961,123.4155],15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{maxZoom:19,attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
        map.on('click', e => setLocation(e.latlng.lat,e.latlng.lng,true));
        const lat = Number(latInput.value), lng = Number(lngInput.value);
        if (latInput.value.trim() && lngInput.value.trim() && Number.isFinite(lat) && Number.isFinite(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            map.setView([lat,lng],17); setLocation(lat,lng,false); status('Saved location loaded. Confirm the pin marks the actual problem location.','success');
        } else status('No location selected yet. Please place a pin before saving if this complaint is assigned to Engineering and Operation.','warning');
        mapInitialized = true; requestAnimationFrame(() => map.invalidateSize());
    }
    async function reverseGeocode(lat,lng) {
        status('Location selected. Finding the address...','neutral');
        try {
            const url = 'https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat='+encodeURIComponent(lat)+'&lon='+encodeURIComponent(lng)+'&zoom=18&addressdetails=1&accept-language=en';
            const response = await fetch(url,{headers:{Accept:'application/json'}});
            if (!response.ok) throw new Error('Address lookup failed');
            const data = await response.json();
            if (!data.display_name) throw new Error('No address returned');
            addressInput.value = data.display_name; status('Location selected and address found. You may edit the address below.','success');
        } catch (error) { console.warn(error); status('Location selected, but the address could not be found automatically. Enter the address manually.','warning'); }
    }
    function setLocation(lat,lng,lookup = true) {
        if (!map) return;
        lat = Number(lat); lng = Number(lng);
        if (!Number.isFinite(lat) || !Number.isFinite(lng) || lat < -90 || lat > 90 || lng < -180 || lng > 180) return;
        latInput.value = lat.toFixed(7); lngInput.value = lng.toFixed(7);
        if (marker) marker.setLatLng([lat,lng]);
        else { marker = L.marker([lat,lng],{draggable:true}).addTo(map); marker.on('dragend',e => { const p=e.target.getLatLng(); setLocation(p.lat,p.lng,true); }); }
        if (lookup) reverseGeocode(lat,lng); else status('Location selected. Confirm the marker marks the actual problem location.','success');
    }
    document.getElementById('locateMe').addEventListener('click', function () {
        if (!navigator.geolocation) return alert('Location services are not supported by your browser.');
        initializeMap(); if (!map) return alert('Map is not ready. Refresh the page and try again.');
        const button = this; button.disabled = true; button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Locating...';
        navigator.geolocation.getCurrentPosition(pos => {
            const lat=pos.coords.latitude,lng=pos.coords.longitude; map.setView([lat,lng],17); setLocation(lat,lng,true);
            setTimeout(()=>{map.invalidateSize();map.setView([lat,lng],17);},200);
            button.disabled=false;button.innerHTML='<i class="fas fa-location-crosshairs mr-2"></i>Use My Location';
        }, error => {
            console.error(error); button.disabled=false;button.innerHTML='<i class="fas fa-location-crosshairs mr-2"></i>Use My Location';
            alert(error.code===1?'Location permission denied. Allow location access in browser settings.':error.code===3?'Location request timed out. Try again.':'Unable to get your current location.');
        },{enableHighAccuracy:true,timeout:20000,maximumAge:0});
    });
    if (window.ResizeObserver) new ResizeObserver(() => { if (map && !locationSection.classList.contains('hidden')) map.invalidateSize(); }).observe(document.getElementById('complaintMap'));
    window.addEventListener('resize',()=>{if(map)map.invalidateSize();});
    // Do not initialize Leaflet while the map section is hidden for Commercial Services.
    // updateDivisionForm() initializes it when Engineering and Operation is selected.

    form.addEventListener('submit', event => {
        if (isEngineering() && !validCoordinates()) {
            event.preventDefault(); event.stopImmediatePropagation();
            status('Please select the actual problem location on the map before saving this complaint.','error');
            highlight(locationSection,'ring-red-300'); centerScroll(locationSection,80); return;
        }
        if (isEngineering() && !addressInput.value.trim()) {
            event.preventDefault(); event.stopImmediatePropagation();
            addressInput.focus(); centerScroll(addressInput,80); return;
        }
        if (photoCount() < 1) {
            event.preventDefault(); event.stopImmediatePropagation();
            showPhotoError('At least 1 supporting photo is required.'); highlight(evidenceSection,'ring-red-300'); centerScroll(evidenceSection,80); return;
        }
        if (photoCount() > 5) {
            event.preventDefault(); event.stopImmediatePropagation();
            showPhotoError('You can keep a maximum of 5 supporting photos.'); centerScroll(evidenceSection,80);
        }
    });
});
</script>
@endpush
