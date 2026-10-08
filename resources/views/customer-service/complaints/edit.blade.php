@extends('customer-service.layouts.app')



@section('title', 'Edit Complaint')



@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">



    <style>
        #complaint-edit-map {

            min-height: 420px;

            width: 100%;

            z-index: 0;

        }



        .leaflet-container {

            font-family: inherit;

        }
    </style>
@endpush



@section('content')



    <div class="mx-3 space-y-6">



        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">



            <div>

                <p class="text-sm text-slate-500">

                    Complaint Management

                </p>



                <h1 class="text-2xl font-bold text-slate-900">

                    Edit Complaint

                </h1>



                <p class="mt-1 text-sm text-slate-500">

                    Update complaint {{ $complaint->complaint_no }}.

                </p>

            </div>



            <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl

                       border border-slate-300 bg-white text-slate-700

                       hover:bg-slate-50 transition">



                <i class="fas fa-arrow-left"></i>



                Back to Complaint



            </a>



        </div>



        @if ($errors->any())



            <div class="rounded-2xl border border-red-200 bg-red-50 p-5">



                <div class="flex gap-3">



                    <div class="mt-0.5 text-red-600">

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



        <form action="{{ route('customer-service.complaints.update', $complaint) }}" method="POST"
            enctype="multipart/form-data" id="complaintForm" class="space-y-6">



            @csrf

            @method('PUT')



            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">



                <div class="px-5 sm:px-6 py-5 border-b border-slate-100">



                    <div class="flex items-center gap-3">



                        <div
                            class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700

                                   flex items-center justify-center shrink-0">



                            <i class="fas fa-user"></i>



                        </div>



                        <div>



                            <h2 class="font-semibold text-slate-900">

                                Complainant Information

                            </h2>



                            <p class="mt-1 text-sm text-slate-500">

                                Update the person reporting the concern and the affected SWD account when applicable.

                            </p>



                        </div>



                    </div>



                </div>



                <div class="p-5 sm:p-6 space-y-6">



                    <div>



                        <label for="consumer_id" class="block text-sm font-medium text-slate-700 mb-2">

                            Affected SWD Account

                            <span class="font-normal text-slate-400">(Optional)</span>

                        </label>



                        <select name="consumer_id" id="consumer_id"
                            class="w-full rounded-xl border-slate-300
           focus:border-sky-500 focus:ring-sky-500">

                            <option value="">
                                No account linked
                            </option>

                            @foreach ($consumers as $consumer)
                                <option value="{{ $consumer->id }}" data-account="{{ $consumer->account_number }}"
                                    data-name="{{ $consumer->full_name }}" data-phone="{{ $consumer->phone }}"
                                    data-address="{{ $consumer->address?->full_address }}" @selected(old('consumer_id', isset($complaint) ? $complaint->consumer_id : null) == $consumer->id)>
                                    @if ($consumer->account_number)
                                        {{ $consumer->account_number }} —
                                    @endif

                                    {{ $consumer->full_name }}
                                </option>
                            @endforeach

                        </select>



                        <p class="mt-2 text-xs text-slate-500">

                            Select the water account affected by the complaint, if any. Selecting an account will

                            automatically fill in the person reporting and contact number below — you can still edit

                            them afterward.

                        </p>



                        @error('consumer_id')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror



                    </div>


                    <div id="selectedAccountCard" class="hidden rounded-2xl border border-sky-200 bg-sky-50 p-4">

                        <div class="flex items-start gap-3">

                            <div
                                class="w-10 h-10 rounded-xl bg-sky-100 text-sky-700
                   flex items-center justify-center shrink-0">

                                <i class="fas fa-id-card"></i>

                            </div>

                            <div class="flex-1 min-w-0">

                                <p class="text-xs font-semibold uppercase tracking-wide text-sky-600">
                                    Linked SWD Account
                                </p>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-3">

                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Account Number
                                        </p>

                                        <p id="selectedAccountNumber"
                                            class="mt-1 text-sm font-semibold text-slate-900 break-words">
                                            —
                                        </p>
                                    </div>

                                    <div>
                                        <p class="text-xs text-slate-500">
                                            Account Holder
                                        </p>

                                        <p id="selectedAccountHolder"
                                            class="mt-1 text-sm font-semibold text-slate-900 break-words">
                                            —
                                        </p>
                                    </div>

                                    <div class="sm:col-span-2">
                                        <p class="text-xs text-slate-500">
                                            Account Address
                                        </p>

                                        <p id="selectedAccountAddress"
                                            class="mt-1 text-sm font-semibold text-slate-900 break-words">
                                            —
                                        </p>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">



                        <div>



                            <label for="complainant_name" class="block text-sm font-medium text-slate-700 mb-2">

                                Person Reporting

                                <span class="text-red-500">*</span>

                            </label>



                            <input type="text" name="complainant_name" id="complainant_name"
                                value="{{ old('complainant_name', $complaint->complainant_name ?: optional($complaint->consumer)->full_name) }}"
                                required placeholder="Enter the full name of the person reporting"
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                This is the person who reported the complaint to Customer Service.

                            </p>



                            @error('complainant_name')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                        <div>



                            <label for="complainant_phone" class="block text-sm font-medium text-slate-700 mb-2">

                                Contact Number

                                <span class="font-normal text-slate-400">(Optional)</span>

                            </label>



                            <input type="text" name="complainant_phone" id="complainant_phone"
                                value="{{ old('complainant_phone', $complaint->complainant_phone ?: optional($complaint->consumer)->phone) }}"
                                placeholder="Enter contact number"
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                Contact information of the person reporting the complaint.

                            </p>



                            @error('complainant_phone')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                    </div>



                    <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">



                        <div class="flex items-start gap-3">



                            <i class="fas fa-circle-info text-sky-600 mt-0.5"></i>



                            <div>



                                <p class="text-sm font-semibold text-slate-800">

                                    Person Reporting and Affected Account

                                </p>



                                <p class="mt-1 text-xs leading-5 text-slate-600">

                                    The person reporting identifies who brought the concern to Customer Service. The

                                    affected SWD account identifies which water account the complaint concerns. They do

                                    not have to be the same person.

                                </p>



                            </div>



                        </div>



                    </div>



                </div>



            </div>



            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">



                <div class="px-5 sm:px-6 py-5 border-b border-slate-100">



                    <div class="flex items-center gap-3">



                        <div
                            class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600

                                   flex items-center justify-center shrink-0">



                            <i class="fas fa-file-circle-exclamation"></i>



                        </div>



                        <div>



                            <h2 class="font-semibold text-slate-900">

                                Complaint Classification

                            </h2>



                            <p class="mt-1 text-sm text-slate-500">

                                Update the responsible division, complaint type, and complaint details.

                            </p>



                        </div>



                    </div>



                </div>



                <div class="p-5 sm:p-6 space-y-6">



                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">



                        <div>



                            <label for="division_id" class="block text-sm font-medium text-slate-700 mb-2">

                                Division

                                <span class="text-red-500">*</span>

                            </label>



                            <select name="division_id" id="division_id" required
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500">



                                <option value="">

                                    Select Division

                                </option>



                                @foreach ($divisions as $division)
                                    <option value="{{ $division->id }}" data-name="{{ $division->name }}"
                                        @selected(old('division_id', $complaint->division_id) == $division->id)>



                                        {{ $division->name }}



                                    </option>
                                @endforeach



                            </select>



                            @error('division_id')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                        <div>



                            <label for="complaint_category_id" class="block text-sm font-medium text-slate-700 mb-2">



                                Complaint Type

                                <span class="text-red-500">*</span>



                            </label>



                            <select name="complaint_category_id" id="complaint_category_id" required
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500

                                       disabled:bg-slate-100 disabled:text-slate-500">



                                <option value="">

                                    Select Complaint Type

                                </option>



                            </select>



                            @error('complaint_category_id')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                        <div>



                            <label class="block text-sm font-medium text-slate-700 mb-2">

                                Status

                            </label>



                            <div
                                class="w-full rounded-xl border border-slate-200 bg-slate-50

                                       px-4 py-2.5 text-slate-700">



                                {{ $complaint->status }}



                            </div>



                            <p class="mt-2 text-xs text-slate-500">

                                Status is managed through complaint workflow actions.

                            </p>



                        </div>



                    </div>


                    <div id="commercialAddressSection"
                        class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

                        <div class="px-5 sm:px-6 py-5 border-b border-slate-100">

                            <div class="flex items-center gap-3">

                                <div
                                    class="w-10 h-10 rounded-xl
                       bg-violet-100 text-violet-700
                       flex items-center justify-center shrink-0">

                                    <i class="fas fa-location-dot"></i>

                                </div>

                                <div>

                                    <h2 class="font-semibold text-slate-900">
                                        Complainant Address
                                    </h2>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Enter the complainant's address because no SWD account is linked to this Commercial
                                        Services complaint.
                                    </p>

                                </div>

                            </div>

                        </div>

                        <div class="p-5 sm:p-6">

                            <label for="commercial_address" class="block text-sm font-medium text-slate-700 mb-2">

                                Complainant Address
                                <span class="text-red-500">*</span>

                            </label>

                            <input type="text" id="commercial_address" value="{{ old('address') }}"
                                placeholder="Enter the complainant's complete address"
                                class="w-full rounded-xl border-slate-300
                   focus:border-sky-500 focus:ring-sky-500">

                            <p class="mt-2 text-xs text-slate-500">
                                This address is used only when the Commercial Services complaint has no linked SWD account.
                            </p>

                        </div>

                    </div>



                    <div id="divisionInformation" class="hidden">



                        <div id="engineeringInformation" class="hidden rounded-xl border border-sky-200 bg-sky-50 p-4">



                            <div class="flex items-start gap-3">



                                <div
                                    class="w-9 h-9 rounded-lg bg-sky-100 text-sky-700

                                           flex items-center justify-center shrink-0">



                                    <i class="fas fa-screwdriver-wrench"></i>



                                </div>



                                <div>



                                    <p class="text-sm font-semibold text-sky-900">

                                        Engineering Operation

                                    </p>



                                    <p class="mt-1 text-sm text-sky-700">

                                        A service location is required for Engineering Operation complaints.

                                    </p>



                                </div>



                            </div>



                        </div>



                        <div id="commercialInformation"
                            class="hidden rounded-xl border border-violet-200 bg-violet-50 p-4">



                            <div class="flex items-start gap-3">



                                <div
                                    class="w-9 h-9 rounded-lg bg-violet-100 text-violet-700

                                           flex items-center justify-center shrink-0">



                                    <i class="fas fa-file-invoice"></i>



                                </div>



                                <div>



                                    <p class="text-sm font-semibold text-violet-900">

                                        Commercial Services

                                    </p>



                                    <p class="mt-1 text-sm text-violet-700">

                                        Customer Service handles this concern. Service location and map information are

                                        not required.

                                    </p>



                                </div>



                            </div>



                        </div>



                    </div>



                    <div>



                        <label for="description" class="block text-sm font-medium text-slate-700 mb-2">

                            Description

                            <span class="text-red-500">*</span>

                        </label>



                        <textarea name="description" id="description" rows="5" required maxlength="5000"
                            placeholder="Describe the consumer's concern in detail..."
                            class="w-full rounded-xl border-slate-300

                                   focus:border-sky-500 focus:ring-sky-500">{{ old('description', $complaint->description) }}</textarea>



                        <div class="flex items-start justify-between gap-4 mt-2">



                            <p id="descriptionHelp" class="text-xs text-slate-500">

                                Provide enough information for the complaint to be properly reviewed.

                            </p>



                            <p class="text-xs text-slate-400 shrink-0">

                                <span id="descriptionCount">0</span>/5000

                            </p>



                        </div>



                        @error('description')
                            <p class="mt-1 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror



                    </div>



                </div>



            </div>



            <div id="engineeringLocationSection"
                class="hidden bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">



                <div class="px-5 sm:px-6 py-5 border-b border-slate-100">



                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">



                        <div class="flex items-center gap-3">



                            <div
                                class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700

                                       flex items-center justify-center shrink-0">



                                <i class="fas fa-location-dot"></i>



                            </div>



                            <div>



                                <h2 class="font-semibold text-slate-900">

                                    Service Location

                                </h2>



                                <p class="mt-1 text-sm text-slate-500">

                                    Update the location of the Engineering Operation concern.

                                </p>



                            </div>



                        </div>



                        <span
                            class="inline-flex self-start sm:self-auto items-center gap-2

                                   rounded-full bg-emerald-50 px-3 py-1.5

                                   text-xs font-medium text-emerald-700">



                            <i class="fas fa-screwdriver-wrench"></i>



                            Engineering Operation



                        </span>



                    </div>



                </div>



                <div class="p-5 sm:p-6 space-y-6">



                    <div>



                        <div class="mb-3">



                            <label class="block text-sm font-medium text-slate-700">

                                Pin Service Location

                                <span class="font-normal text-slate-400">(Optional)</span>

                            </label>



                            <p class="mt-1 text-xs text-slate-500">

                                The saved location is shown below. Click the map or drag the marker only if the service

                                location needs to be changed.

                            </p>



                        </div>



                        <div id="complaint-edit-map"
                            class="w-full h-[420px] rounded-2xl border border-slate-300 overflow-hidden relative z-0">

                        </div>



                        <input type="hidden" name="latitude" id="latitude"
                            value="{{ old('latitude', $complaint->latitude) }}">



                        <input type="hidden" name="longitude" id="longitude"
                            value="{{ old('longitude', $complaint->longitude) }}">



                        <div id="mapStatus" class="mt-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">



                            <div class="flex items-start gap-3">



                                <i class="fas fa-location-dot text-sky-600 mt-0.5"></i>



                                <div>



                                    <p class="text-sm font-medium text-sky-900">

                                        Service Location

                                    </p>



                                    <p id="mapStatusText" class="mt-1 text-xs text-sky-700">



                                        @if (old('latitude', $complaint->latitude) && old('longitude', $complaint->longitude))
                                            Current saved location is shown. Move the marker only if the location needs to

                                            be changed.
                                        @else
                                            Click the map to select the service location.
                                        @endif



                                    </p>



                                </div>



                            </div>



                        </div>



                        @error('latitude')
                            <p class="mt-2 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror



                        @error('longitude')
                            <p class="mt-2 text-sm text-red-600">

                                {{ $message }}

                            </p>
                        @enderror



                    </div>



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">



                        <div>



                            <label for="address" class="block text-sm font-medium text-slate-700 mb-2">



                                Address

                                <span class="text-red-500">*</span>



                            </label>



                            <input type="text" name="address" id="address"
                                value="{{ old('address', $complaint->address) }}"
                                placeholder="Pin the location above or enter the address manually" required
                                class="w-full rounded-xl border-slate-300

                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                Moving the map marker automatically updates this address. You can still edit it manually if

                                the detected address is inaccurate.

                            </p>



                            @error('address')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                        <div>



                            <label for="landmark" class="block text-sm font-medium text-slate-700 mb-2">



                                Nearby Landmark

                                <span class="font-normal text-slate-400">(Optional)</span>



                            </label>



                            <input type="text" name="landmark" id="landmark"
                                value="{{ old('landmark', $complaint->landmark) }}"
                                placeholder="Near barangay hall, school, store, etc."
                                class="w-full rounded-xl border-slate-300

                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                Add a nearby landmark to help the plumber locate the service area.

                            </p>



                            @error('landmark')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



                        </div>



                    </div>



                </div>



            </div>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                            <i class="fas fa-images"></i>
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-900">Supporting Photos <span class="text-red-500">*</span>
                            </h2>
                            <p class="mt-1 text-sm text-slate-500">Keep, remove, or add supporting photos. At least 1 and
                                at most 5 photos are required.</p>
                        </div>
                    </div>
                </div>

                <div class="p-5 sm:p-6 space-y-6">
                    @if ($complaint->photos->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between gap-3 mb-3">
                                <p class="text-sm font-semibold text-slate-800">Current Photos</p>
                                <span id="editExistingPhotoCounter"
                                    class="text-xs font-medium text-slate-500">{{ $complaint->photos->count() }}
                                    saved</span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                @foreach ($complaint->photos as $photo)
                                    <div class="edit-existing-photo relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50"
                                        data-photo-id="{{ $photo->id }}">
                                        <div class="aspect-square bg-slate-100">
                                            <img src="{{ asset('storage/' . $photo->photo) }}"
                                                alt="Supporting photo {{ $loop->iteration }}"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="p-3">
                                            <p class="text-xs font-semibold text-slate-700">Photo {{ $loop->iteration }}
                                            </p>
                                            <p class="edit-photo-status mt-1 text-xs text-slate-500">Saved photo</p>
                                        </div>
                                        <button type="button"
                                            class="edit-remove-existing-photo absolute top-2 right-2 w-8 h-8 rounded-lg bg-white/95 border border-red-200 text-red-600 flex items-center justify-center shadow-sm hover:bg-red-50"
                                            data-photo-id="{{ $photo->id }}" title="Remove photo">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="{{ $complaint->photos->isNotEmpty() ? 'border-t border-slate-100 pt-5' : '' }}">
                        <p class="text-sm font-semibold text-slate-800">Add Supporting Photos</p>
                        <p class="mt-1 text-xs text-slate-500">Use the camera or choose one or more images from this
                            device.</p>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mt-4">
                            <button type="button" id="editTakePhotoButton"
                                class="flex items-center gap-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-4 text-left hover:bg-sky-100 hover:border-sky-300 transition">
                                <div
                                    class="w-11 h-11 rounded-xl bg-white flex items-center justify-center text-sky-700 shrink-0 border border-sky-100">
                                    <i class="fas fa-camera text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Take Photo</p>
                                    <p class="mt-1 text-xs text-slate-500">Open the live camera</p>
                                </div>
                            </button>

                            <button type="button" id="editChoosePhotoButton"
                                class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white px-4 py-4 text-left hover:bg-slate-50 hover:border-slate-300 transition">
                                <div
                                    class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center text-slate-600 shrink-0 border border-slate-200">
                                    <i class="fas fa-images text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Choose from Device</p>
                                    <p class="mt-1 text-xs text-slate-500">Select one or more images</p>
                                </div>
                            </button>
                        </div>

                        <input type="file" id="editDevicePhotoInput" accept="image/jpeg,image/png,image/webp" multiple
                            class="hidden">
                        <input type="file" name="photos[]" id="editPhotosInput"
                            accept="image/jpeg,image/png,image/webp" multiple class="hidden">
                        <div id="editRemovedPhotoInputs"></div>

                        <div
                            class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex items-center gap-2 text-sm text-slate-600">
                                <i class="fas fa-circle-info text-sky-600"></i>
                                <span>JPG, JPEG, PNG or WEBP. Maximum 5 MB each.</span>
                            </div>
                            <span id="editPhotoCounter"
                                class="text-sm font-semibold text-slate-700">{{ $complaint->photos->count() }} / 5
                                photos</span>
                        </div>

                        <div id="editPhotoRequiredMessage"
                            class="hidden mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>
                                <p class="text-sm text-red-700">At least one supporting photo must remain.</p>
                            </div>
                        </div>

                        <div id="editNewPhotoGrid"
                            class="hidden mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4"></div>

                        @error('photos')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('photos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('remove_photos.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3

                       bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">



                <p id="beforeSubmitText" class="text-xs text-slate-500">

                    Review the updated complaint information before saving.

                </p>



                <div class="flex flex-col-reverse sm:flex-row gap-3">



                    <a href="{{ route('customer-service.complaints.show', $complaint) }}"
                        class="inline-flex items-center justify-center gap-2

                               px-5 py-3 rounded-xl

                               border border-slate-300 text-slate-700

                               hover:bg-slate-50 transition">



                        <i class="fas fa-xmark"></i>



                        Cancel



                    </a>



                    <button type="submit" id="submitButton"
                        class="inline-flex items-center justify-center gap-2

                               px-6 py-3 rounded-xl

                               bg-sky-700 text-white font-semibold

                               hover:bg-sky-800 transition

                               disabled:opacity-60 disabled:cursor-not-allowed">



                        <i id="submitIcon" class="fas fa-floppy-disk"></i>



                        <span id="submitText">

                            Save Changes

                        </span>



                    </button>



                </div>



            </div>



        </form>



    </div>



    <div id="editCameraModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
        role="dialog" aria-modal="true" aria-label="Take supporting photo">
        <div class="w-full max-w-xl overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 p-4">
                <h3 class="font-semibold text-slate-900">Take Supporting Photo</h3>
                <button type="button" id="editCloseCameraButton"
                    class="rounded-lg px-3 py-2 text-slate-600 hover:bg-slate-100" aria-label="Close camera"><i
                        class="fas fa-xmark"></i></button>
            </div>
            <div class="relative bg-black min-h-64 flex items-center justify-center">
                <video id="editCameraVideo" autoplay muted playsinline class="max-h-[60vh] w-full object-contain"></video>
                <div id="editCameraLoading" class="absolute inset-0 flex items-center justify-center bg-black text-white">
                    <div class="text-center"><i class="fas fa-spinner fa-spin text-2xl"></i>
                        <p class="mt-2 text-sm">Starting camera...</p>
                    </div>
                </div>
            </div>
            <canvas id="editCameraCanvas" class="hidden"></canvas>
            <p id="editCameraMessage" class="hidden px-4 pt-3 text-sm text-red-700" role="alert"></p>
            <div class="flex justify-end gap-3 p-4">
                <button type="button" id="editCancelCameraButton"
                    class="rounded-xl border border-slate-300 px-4 py-2.5">Cancel</button>
                <button type="button" id="editCapturePhotoButton"
                    class="rounded-xl bg-sky-700 px-4 py-2.5 font-semibold text-white hover:bg-sky-800"><i
                        class="fas fa-camera mr-2"></i>Capture Photo</button>
            </div>
        </div>
    </div>

@endsection



@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const divisions = @json($divisions);

            const selectedComplaintType =
                @json(old('complaint_category_id', $complaint->complaint_category_id));

            const complaintForm =
                document.getElementById('complaintForm');

            const consumerSelect =
                document.getElementById('consumer_id');

            const selectedAccountCard =
                document.getElementById('selectedAccountCard');

            const selectedAccountNumber =
                document.getElementById('selectedAccountNumber');

            const selectedAccountHolder =
                document.getElementById('selectedAccountHolder');

            const selectedAccountAddress =
                document.getElementById('selectedAccountAddress');

            const complainantNameInput =
                document.getElementById('complainant_name');

            const complainantPhoneInput =
                document.getElementById('complainant_phone');

            const divisionSelect =
                document.getElementById('division_id');

            const complaintTypeSelect =
                document.getElementById('complaint_category_id');

            const divisionInformation =
                document.getElementById('divisionInformation');

            const engineeringInformation =
                document.getElementById('engineeringInformation');

            const commercialInformation =
                document.getElementById('commercialInformation');

            const engineeringLocationSection =
                document.getElementById('engineeringLocationSection');

            const commercialAddressSection =
                document.getElementById('commercialAddressSection');

            const addressInput =
                document.getElementById('address');

            const commercialAddressInput =
                document.getElementById('commercial_address');

            const latitudeInput =
                document.getElementById('latitude');

            const longitudeInput =
                document.getElementById('longitude');

            const descriptionInput =
                document.getElementById('description');

            const descriptionCount =
                document.getElementById('descriptionCount');

            const descriptionHelp =
                document.getElementById('descriptionHelp');

            const evidenceHeading =
                document.getElementById('evidenceHeading');

            const evidenceDescription =
                document.getElementById('evidenceDescription');

            const currentEvidenceLabel =
                document.getElementById('currentEvidenceLabel');

            const photoLabel =
                document.getElementById('photoLabel');

            const beforeSubmitText =
                document.getElementById('beforeSubmitText');

            const submitButton =
                document.getElementById('submitButton');

            const submitIcon =
                document.getElementById('submitIcon');

            const submitText =
                document.getElementById('submitText');

            const mapElement =
                document.getElementById('complaint-edit-map');

            const mapStatus =
                document.getElementById('mapStatus');

            const mapStatusText =
                document.getElementById('mapStatusText');


            const takePhotoButton =
                document.getElementById('takePhotoButton');

            const choosePhotoButton =
                document.getElementById('choosePhotoButton');

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


            const cameraModal =
                document.getElementById('cameraModal');

            const cameraVideo =
                document.getElementById('cameraVideo');

            const cameraCanvas =
                document.getElementById('cameraCanvas');

            const cameraLoading =
                document.getElementById('cameraLoading');

            const cameraMessage =
                document.getElementById('cameraMessage');

            const closeCameraButton =
                document.getElementById('closeCameraButton');

            const cancelCameraButton =
                document.getElementById('cancelCameraButton');

            const capturePhotoButton =
                document.getElementById('capturePhotoButton');


            let map = null;
            let marker = null;
            let mapInitialized = false;

            let cameraStream = null;


            const initialDivision =
                divisions.find(function(division) {

                    return String(division.id) ===
                        String(
                            divisionSelect ?
                            divisionSelect.value :
                            ''
                        );

                }) || null;


            const initialDivisionName =
                initialDivision ?
                String(
                    initialDivision.name || ''
                ).toLowerCase() :
                '';


            const initiallyCommercial =
                initialDivisionName.includes(
                    'commercial'
                );


            const initiallyEngineering =
                initialDivisionName.includes(
                    'engineering'
                );


            let engineeringAddressValue = '';

            let commercialAddressValue = '';


            if (initiallyEngineering) {

                engineeringAddressValue =
                    addressInput ?
                    addressInput.value :
                    '';

            }


            if (
                initiallyCommercial &&
                !(
                    consumerSelect &&
                    consumerSelect.value
                )
            ) {

                commercialAddressValue =
                    commercialAddressInput &&
                    commercialAddressInput.value ?
                    commercialAddressInput.value :
                    (
                        addressInput ?
                        addressInput.value :
                        ''
                    );


                if (commercialAddressInput) {

                    commercialAddressInput.value =
                        commercialAddressValue;

                }

            }


            const defaultLatitude = 10.8961;
            const defaultLongitude = 123.4155;


            function getSelectedDivision() {

                if (
                    !divisionSelect ||
                    !divisionSelect.value
                ) {

                    return null;

                }


                return divisions.find(
                    function(division) {

                        return String(
                            division.id
                        ) === String(
                            divisionSelect.value
                        );

                    }
                ) || null;

            }


            function getDivisionName() {

                const division =
                    getSelectedDivision();


                return division ?
                    String(
                        division.name || ''
                    ).trim() :
                    '';

            }


            function isEngineering() {

                return getDivisionName()
                    .toLowerCase()
                    .includes('engineering');

            }


            function isCommercial() {

                return getDivisionName()
                    .toLowerCase()
                    .includes('commercial');

            }


            function hasLinkedConsumer() {

                return Boolean(
                    consumerSelect &&
                    consumerSelect.value
                );

            }


            function getSelectedConsumerOption() {

                if (
                    !consumerSelect ||
                    !consumerSelect.value
                ) {

                    return null;

                }


                return consumerSelect.options[
                    consumerSelect.selectedIndex
                ] || null;

            }


            function updateAccountCard() {

                if (
                    !consumerSelect ||
                    !selectedAccountCard
                ) {

                    return;

                }


                const option =
                    getSelectedConsumerOption();


                if (!option) {

                    selectedAccountCard
                        .classList
                        .add('hidden');


                    if (selectedAccountNumber) {

                        selectedAccountNumber.textContent =
                            '—';

                    }


                    if (selectedAccountHolder) {

                        selectedAccountHolder.textContent =
                            '—';

                    }


                    if (selectedAccountAddress) {

                        selectedAccountAddress.textContent =
                            '—';

                    }


                    return;

                }


                const accountNumber =
                    String(
                        option.dataset.account || ''
                    ).trim();


                const accountHolder =
                    String(
                        option.dataset.name || ''
                    ).trim();


                const accountAddress =
                    String(
                        option.dataset.address || ''
                    ).trim();


                if (selectedAccountNumber) {

                    selectedAccountNumber.textContent =
                        accountNumber || '—';

                }


                if (selectedAccountHolder) {

                    selectedAccountHolder.textContent =
                        accountHolder || '—';

                }


                if (selectedAccountAddress) {

                    selectedAccountAddress.textContent =
                        accountAddress ||
                        'No registered address';

                }


                selectedAccountCard
                    .classList
                    .remove('hidden');

            }


            function autofillComplainantFromAccount() {

                const option =
                    getSelectedConsumerOption();


                if (!option) {

                    return;

                }


                const accountHolder =
                    String(
                        option.dataset.name || ''
                    ).trim();


                const phone =
                    String(
                        option.dataset.phone || ''
                    ).trim();


                if (
                    complainantNameInput &&
                    accountHolder
                ) {

                    complainantNameInput.value =
                        accountHolder;

                }


                if (
                    complainantPhoneInput &&
                    phone
                ) {

                    complainantPhoneInput.value =
                        phone;

                }

            }


            function populateComplaintTypes(
                preserveSelection = true
            ) {

                if (!complaintTypeSelect) {

                    return;

                }


                const division =
                    getSelectedDivision();


                const currentValue =
                    preserveSelection ?
                    (
                        complaintTypeSelect.value ||
                        selectedComplaintType
                    ) :
                    '';


                complaintTypeSelect.innerHTML = '';


                if (!division) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value = '';

                    option.textContent =
                        'Select a division first';


                    complaintTypeSelect
                        .appendChild(option);


                    complaintTypeSelect.disabled =
                        true;


                    return;

                }


                const placeholder =
                    document.createElement(
                        'option'
                    );


                placeholder.value = '';

                placeholder.textContent =
                    'Select Complaint Type';


                complaintTypeSelect
                    .appendChild(
                        placeholder
                    );


                const complaintTypes =
                    division.complaint_types ||
                    division.complaintTypes || [];


                complaintTypes.forEach(
                    function(type) {

                        const option =
                            document.createElement(
                                'option'
                            );


                        option.value =
                            type.id;

                        option.textContent =
                            type.name;


                        complaintTypeSelect
                            .appendChild(
                                option
                            );

                    }
                );


                complaintTypeSelect.disabled =
                    false;


                if (
                    currentValue &&
                    Array.from(
                        complaintTypeSelect.options
                    ).some(
                        function(option) {

                            return String(
                                option.value
                            ) === String(
                                currentValue
                            );

                        }
                    )
                ) {

                    complaintTypeSelect.value =
                        currentValue;

                }

            }


            function setMapStatus(message) {

                if (
                    !mapStatus ||
                    !mapStatusText
                ) {

                    return;

                }


                if (!message) {

                    mapStatus.classList.add(
                        'hidden'
                    );

                    mapStatusText.textContent =
                        '';


                    return;

                }


                mapStatusText.textContent =
                    message;

                mapStatus.classList.remove(
                    'hidden'
                );

            }


            async function reverseGeocode(
                latitude,
                longitude
            ) {

                setMapStatus(
                    'Detecting address...'
                );


                try {

                    const response =
                        await fetch(
                            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(latitude)}&lon=${encodeURIComponent(longitude)}&zoom=18&addressdetails=1`, {
                                headers: {
                                    'Accept': 'application/json'
                                }
                            }
                        );


                    if (!response.ok) {

                        throw new Error(
                            'Unable to detect address.'
                        );

                    }


                    const data =
                        await response.json();


                    const detectedAddress =
                        String(
                            data.display_name || ''
                        ).trim();


                    if (detectedAddress) {

                        engineeringAddressValue =
                            detectedAddress;


                        if (addressInput) {

                            addressInput.value =
                                detectedAddress;

                        }


                        setMapStatus(
                            detectedAddress
                        );

                    } else {

                        setMapStatus(
                            'Location selected. Please enter the address manually.'
                        );

                    }

                } catch (error) {

                    setMapStatus(
                        'Location selected, but the address could not be detected. Please enter it manually.'
                    );

                }

            }


            function setMarker(
                latitude,
                longitude,
                detectAddress = true
            ) {

                if (!map) {

                    return;

                }


                const lat =
                    Number(latitude);

                const lng =
                    Number(longitude);


                if (
                    !Number.isFinite(lat) ||
                    !Number.isFinite(lng)
                ) {

                    return;

                }


                if (!marker) {

                    marker =
                        L.marker(
                            [lat, lng], {
                                draggable: true
                            }
                        ).addTo(map);


                    marker.on(
                        'dragend',
                        function(event) {

                            const position =
                                event.target
                                .getLatLng();


                            if (latitudeInput) {

                                latitudeInput.value =
                                    position.lat
                                    .toFixed(7);

                            }


                            if (longitudeInput) {

                                longitudeInput.value =
                                    position.lng
                                    .toFixed(7);

                            }


                            reverseGeocode(
                                position.lat,
                                position.lng
                            );

                        }
                    );

                } else {

                    marker.setLatLng(
                        [lat, lng]
                    );

                }


                if (latitudeInput) {

                    latitudeInput.value =
                        lat.toFixed(7);

                }


                if (longitudeInput) {

                    longitudeInput.value =
                        lng.toFixed(7);

                }


                map.setView(
                    [lat, lng],
                    Math.max(
                        map.getZoom(),
                        17
                    )
                );


                if (detectAddress) {

                    reverseGeocode(
                        lat,
                        lng
                    );

                } else {

                    if (
                        engineeringAddressValue
                    ) {

                        setMapStatus(
                            engineeringAddressValue
                        );

                    } else {

                        setMapStatus(
                            'Saved service location.'
                        );

                    }

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
                        mapElement
                    ).setView(
                        [
                            defaultLatitude,
                            defaultLongitude
                        ],
                        14
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

                        setMarker(
                            event.latlng.lat,
                            event.latlng.lng,
                            true
                        );

                    }
                );


                mapInitialized = true;


                const savedLatitude =
                    latitudeInput ?
                    parseFloat(
                        latitudeInput.value
                    ) :
                    NaN;


                const savedLongitude =
                    longitudeInput ?
                    parseFloat(
                        longitudeInput.value
                    ) :
                    NaN;


                if (
                    Number.isFinite(
                        savedLatitude
                    ) &&
                    Number.isFinite(
                        savedLongitude
                    )
                ) {

                    setMarker(
                        savedLatitude,
                        savedLongitude,
                        false
                    );

                }


                setTimeout(
                    function() {

                        if (map) {

                            map.invalidateSize();

                        }

                    },
                    150
                );

            }


            function updateDivisionInformation() {

                if (
                    !divisionInformation ||
                    !engineeringInformation ||
                    !commercialInformation
                ) {

                    return;

                }


                if (isEngineering()) {

                    divisionInformation
                        .classList
                        .remove('hidden');

                    engineeringInformation
                        .classList
                        .remove('hidden');

                    commercialInformation
                        .classList
                        .add('hidden');


                    return;

                }


                if (isCommercial()) {

                    divisionInformation
                        .classList
                        .remove('hidden');

                    engineeringInformation
                        .classList
                        .add('hidden');

                    commercialInformation
                        .classList
                        .remove('hidden');


                    return;

                }


                divisionInformation
                    .classList
                    .add('hidden');

                engineeringInformation
                    .classList
                    .add('hidden');

                commercialInformation
                    .classList
                    .add('hidden');

            }


            function updateLocationSections() {

                const engineering =
                    isEngineering();

                const commercial =
                    isCommercial();

                const linkedConsumer =
                    hasLinkedConsumer();


                if (engineering) {

                    if (
                        engineeringLocationSection
                    ) {

                        engineeringLocationSection
                            .classList
                            .remove('hidden');

                    }


                    if (
                        commercialAddressSection
                    ) {

                        commercialAddressSection
                            .classList
                            .add('hidden');

                    }


                    if (addressInput) {

                        addressInput.value =
                            engineeringAddressValue;

                        addressInput.required =
                            true;

                        addressInput.name =
                            'address';

                    }


                    if (
                        commercialAddressInput
                    ) {

                        commercialAddressInput
                            .required = false;

                        commercialAddressInput
                            .removeAttribute(
                                'name'
                            );

                    }


                    initializeMap();


                    setTimeout(
                        function() {

                            if (map) {

                                map.invalidateSize();

                            }

                        },
                        150
                    );


                    return;

                }


                if (commercial) {

                    if (
                        engineeringLocationSection
                    ) {

                        engineeringLocationSection
                            .classList
                            .add('hidden');

                    }


                    if (linkedConsumer) {

                        if (
                            commercialAddressSection
                        ) {

                            commercialAddressSection
                                .classList
                                .add('hidden');

                        }


                        if (
                            commercialAddressInput
                        ) {

                            commercialAddressInput
                                .required = false;

                            commercialAddressInput
                                .removeAttribute(
                                    'name'
                                );

                        }


                        if (addressInput) {

                            addressInput.required =
                                false;

                            addressInput
                                .removeAttribute(
                                    'name'
                                );

                        }

                    } else {

                        if (
                            commercialAddressSection
                        ) {

                            commercialAddressSection
                                .classList
                                .remove('hidden');

                        }


                        if (
                            commercialAddressInput
                        ) {

                            commercialAddressInput
                                .value =
                                commercialAddressValue;

                            commercialAddressInput
                                .required = true;

                            commercialAddressInput
                                .name = 'address';

                        }


                        if (addressInput) {

                            addressInput.required =
                                false;

                            addressInput
                                .removeAttribute(
                                    'name'
                                );

                        }

                    }


                    return;

                }


                if (
                    engineeringLocationSection
                ) {

                    engineeringLocationSection
                        .classList
                        .add('hidden');

                }


                if (
                    commercialAddressSection
                ) {

                    commercialAddressSection
                        .classList
                        .add('hidden');

                }


                if (addressInput) {

                    addressInput.required =
                        false;

                    addressInput
                        .removeAttribute(
                            'name'
                        );

                }


                if (
                    commercialAddressInput
                ) {

                    commercialAddressInput
                        .required = false;

                    commercialAddressInput
                        .removeAttribute(
                            'name'
                        );

                }

            }


            function updateDivisionSpecificText() {

                if (isEngineering()) {

                    if (descriptionHelp) {

                        descriptionHelp.textContent =
                            'Describe the Engineering Operation concern clearly, including what happened and where the problem is occurring.';

                    }


                    if (evidenceHeading) {

                        evidenceHeading.textContent =
                            'Supporting Photo';

                    }


                    if (evidenceDescription) {

                        evidenceDescription.textContent =
                            'Upload a clear photo of the reported service problem when available.';

                    }


                    if (
                        currentEvidenceLabel
                    ) {

                        currentEvidenceLabel
                            .textContent =
                            'Current Complaint Photo';

                    }


                    if (photoLabel) {

                        photoLabel.textContent =
                            'Replace Complaint Photo';

                    }


                    if (beforeSubmitText) {

                        beforeSubmitText.textContent =
                            'Review the Engineering Operation complaint information before saving changes.';

                    }


                    return;

                }


                if (isCommercial()) {

                    if (descriptionHelp) {

                        descriptionHelp.textContent =
                            'Describe the Commercial Services concern clearly so Customer Service can review and process it.';

                    }


                    if (evidenceHeading) {

                        evidenceHeading.textContent =
                            'Supporting Evidence';

                    }


                    if (evidenceDescription) {

                        evidenceDescription.textContent =
                            'Upload an image that may help Customer Service review the concern, if available.';

                    }


                    if (
                        currentEvidenceLabel
                    ) {

                        currentEvidenceLabel
                            .textContent =
                            'Current Supporting Evidence';

                    }


                    if (photoLabel) {

                        photoLabel.textContent =
                            'Replace Supporting Image';

                    }


                    if (beforeSubmitText) {

                        beforeSubmitText.textContent =
                            'Review the Commercial Services complaint information before saving changes.';

                    }


                    return;

                }


                if (descriptionHelp) {

                    descriptionHelp.textContent =
                        'Provide enough information for the complaint to be properly reviewed.';

                }


                if (evidenceHeading) {

                    evidenceHeading.textContent =
                        'Supporting Photo / Evidence';

                }


                if (evidenceDescription) {

                    evidenceDescription.textContent =
                        'Upload a supporting image when available.';

                }


                if (
                    currentEvidenceLabel
                ) {

                    currentEvidenceLabel
                        .textContent =
                        'Current Supporting Image';

                }


                if (photoLabel) {

                    photoLabel.textContent =
                        'Replace Supporting Image';

                }


                if (beforeSubmitText) {

                    beforeSubmitText.textContent =
                        'Review the complaint information before saving changes.';

                }

            }


            function updateDescriptionCount() {

                if (
                    !descriptionInput ||
                    !descriptionCount
                ) {

                    return;

                }


                descriptionCount.textContent =
                    descriptionInput.value.length;

            }


            function formatPhotoSize(bytes) {

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


            function validatePhoto(file) {

                if (!file) {

                    return false;

                }


                const maximumSize =
                    5 * 1024 * 1024;


                if (
                    file.size >
                    maximumSize
                ) {

                    alert(
                        'The selected photo must not exceed 5 MB.'
                    );


                    return false;

                }


                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];


                if (
                    file.type &&
                    !allowedTypes.includes(
                        file.type
                    )
                ) {

                    alert(
                        'Please select a JPG, JPEG, PNG, or WEBP image.'
                    );


                    return false;

                }


                return true;

            }


            function showPhotoPreview(file) {

                if (!file) {

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
                                formatPhotoSize(
                                    file.size
                                );

                        }


                        if (
                            photoPreviewContainer
                        ) {

                            photoPreviewContainer
                                .classList
                                .remove('hidden');

                        }

                    };


                reader.readAsDataURL(
                    file
                );

            }


            function clearSelectedPhoto() {

                if (photoInput) {

                    photoInput.value = '';

                }


                if (photoPreview) {

                    photoPreview.src = '';

                }


                if (photoName) {

                    photoName.textContent = '';

                }


                if (photoSize) {

                    photoSize.textContent = '';

                }


                if (
                    photoPreviewContainer
                ) {

                    photoPreviewContainer
                        .classList
                        .add('hidden');

                }

            }


            function setCameraMessage(message) {

                if (!cameraMessage) {

                    return;

                }


                if (!message) {

                    cameraMessage.textContent =
                        '';

                    cameraMessage.classList.add(
                        'hidden'
                    );


                    return;

                }


                cameraMessage.textContent =
                    message;

                cameraMessage.classList.remove(
                    'hidden'
                );

            }


            function stopCamera() {

                if (cameraStream) {

                    cameraStream
                        .getTracks()
                        .forEach(
                            function(track) {

                                track.stop();

                            }
                        );


                    cameraStream = null;

                }


                if (cameraVideo) {

                    cameraVideo.pause();

                    cameraVideo.srcObject =
                        null;

                }

            }


            function closeCamera() {

                stopCamera();


                if (cameraModal) {

                    cameraModal.classList.add(
                        'hidden'
                    );

                }


                if (cameraLoading) {

                    cameraLoading.classList.remove(
                        'hidden'
                    );

                }


                document.body.classList.remove(
                    'overflow-hidden'
                );


                setCameraMessage('');

            }


            async function openCamera() {

                if (
                    !navigator.mediaDevices ||
                    !navigator.mediaDevices
                    .getUserMedia
                ) {

                    alert(
                        'Camera access is not supported by this browser. Please use Choose from Device instead.'
                    );


                    return;

                }


                if (
                    !cameraModal ||
                    !cameraVideo
                ) {

                    return;

                }


                stopCamera();

                setCameraMessage('');


                if (cameraLoading) {

                    cameraLoading.classList.remove(
                        'hidden'
                    );

                }


                cameraModal.classList.remove(
                    'hidden'
                );


                document.body.classList.add(
                    'overflow-hidden'
                );


                try {

                    cameraStream =
                        await navigator.mediaDevices
                        .getUserMedia({
                            video: {
                                facingMode: {
                                    ideal: 'environment'
                                }
                            },
                            audio: false
                        });


                    cameraVideo.srcObject =
                        cameraStream;


                    cameraVideo.onloadedmetadata =
                        function() {

                            if (cameraLoading) {

                                cameraLoading.classList.add(
                                    'hidden'
                                );

                            }

                        };


                    await cameraVideo.play();


                    if (cameraLoading) {

                        cameraLoading.classList.add(
                            'hidden'
                        );

                    }

                } catch (error) {

                    console.error(
                        'Camera error:',
                        error
                    );


                    if (cameraLoading) {

                        cameraLoading.classList.add(
                            'hidden'
                        );

                    }


                    let message =
                        'Camera access could not be started.';


                    if (
                        error &&
                        (
                            error.name ===
                            'NotAllowedError' ||
                            error.name ===
                            'PermissionDeniedError'
                        )
                    ) {

                        message =
                            'Camera permission was denied. Allow camera access in your browser settings, then try again.';

                    } else if (
                        error &&
                        error.name ===
                        'NotFoundError'
                    ) {

                        message =
                            'No camera was detected on this device. Please use Choose from Device instead.';

                    } else if (
                        error &&
                        error.name ===
                        'NotReadableError'
                    ) {

                        message =
                            'The camera is already being used by another application. Close the other application and try again.';

                    } else if (
                        !window.isSecureContext
                    ) {

                        message =
                            'Camera access requires HTTPS or localhost. Please open the system using a secure connection.';

                    }


                    setCameraMessage(
                        message
                    );

                }

            }


            function captureCameraPhoto() {

                if (
                    !cameraVideo ||
                    !cameraCanvas ||
                    !photoInput
                ) {

                    return;

                }


                if (
                    !cameraVideo.videoWidth ||
                    !cameraVideo.videoHeight
                ) {

                    setCameraMessage(
                        'The camera is not ready yet. Please wait a moment and try again.'
                    );


                    return;

                }


                cameraCanvas.width =
                    cameraVideo.videoWidth;

                cameraCanvas.height =
                    cameraVideo.videoHeight;


                const context =
                    cameraCanvas.getContext(
                        '2d'
                    );


                if (!context) {

                    setCameraMessage(
                        'Unable to capture the camera image.'
                    );


                    return;

                }


                context.drawImage(
                    cameraVideo,
                    0,
                    0,
                    cameraCanvas.width,
                    cameraCanvas.height
                );


                cameraCanvas.toBlob(
                    function(blob) {

                        if (!blob) {

                            setCameraMessage(
                                'Unable to capture the photo. Please try again.'
                            );


                            return;

                        }


                        const file =
                            new File(
                                [blob],
                                'complaint-photo-' +
                                Date.now() +
                                '.jpg', {
                                    type: 'image/jpeg'
                                }
                            );


                        if (
                            !validatePhoto(
                                file
                            )
                        ) {

                            return;

                        }


                        try {

                            const transfer =
                                new DataTransfer();


                            transfer.items.add(
                                file
                            );


                            photoInput.files =
                                transfer.files;


                            showPhotoPreview(
                                file
                            );


                            closeCamera();

                        } catch (error) {

                            console.error(
                                'Unable to attach captured photo:',
                                error
                            );


                            setCameraMessage(
                                'The captured photo could not be attached. Please use Choose from Device instead.'
                            );

                        }

                    },
                    'image/jpeg',
                    0.9
                );

            }


            if (addressInput) {

                addressInput.addEventListener(
                    'input',
                    function() {

                        engineeringAddressValue =
                            addressInput.value;

                    }
                );

            }


            if (
                commercialAddressInput
            ) {

                commercialAddressInput
                    .addEventListener(
                        'input',
                        function() {

                            commercialAddressValue =
                                commercialAddressInput
                                .value;

                        }
                    );

            }


            if (consumerSelect) {

                consumerSelect.addEventListener(
                    'change',
                    function() {

                        updateAccountCard();

                        autofillComplainantFromAccount();

                        updateLocationSections();

                    }
                );

            }


            if (divisionSelect) {

                divisionSelect.addEventListener(
                    'change',
                    function() {

                        populateComplaintTypes(
                            false
                        );

                        updateDivisionInformation();

                        updateLocationSections();

                        updateDivisionSpecificText();

                    }
                );

            }


            if (descriptionInput) {

                descriptionInput.addEventListener(
                    'input',
                    updateDescriptionCount
                );

            }


            if (takePhotoButton) {

                takePhotoButton.addEventListener(
                    'click',
                    function() {

                        openCamera();

                    }
                );

            }


            if (choosePhotoButton) {

                choosePhotoButton.addEventListener(
                    'click',
                    function() {

                        if (photoInput) {

                            photoInput.click();

                        }

                    }
                );

            }


            if (photoInput) {

                photoInput.addEventListener(
                    'change',
                    function() {

                        const file =
                            this.files &&
                            this.files.length ?
                            this.files[0] :
                            null;


                        if (!file) {

                            return;

                        }


                        if (
                            !validatePhoto(
                                file
                            )
                        ) {

                            this.value = '';

                            return;

                        }


                        showPhotoPreview(
                            file
                        );

                    }
                );

            }


            if (removePhoto) {

                removePhoto.addEventListener(
                    'click',
                    clearSelectedPhoto
                );

            }


            if (capturePhotoButton) {

                capturePhotoButton.addEventListener(
                    'click',
                    captureCameraPhoto
                );

            }


            if (closeCameraButton) {

                closeCameraButton.addEventListener(
                    'click',
                    closeCamera
                );

            }


            if (cancelCameraButton) {

                cancelCameraButton.addEventListener(
                    'click',
                    closeCamera
                );

            }


            if (cameraModal) {

                cameraModal.addEventListener(
                    'click',
                    function(event) {

                        if (
                            event.target ===
                            cameraModal
                        ) {

                            closeCamera();

                        }

                    }
                );

            }


            document.addEventListener(
                'keydown',
                function(event) {

                    if (
                        event.key === 'Escape' &&
                        cameraModal &&
                        !cameraModal.classList
                        .contains('hidden')
                    ) {

                        closeCamera();

                    }

                }
            );


            if (complaintForm) {

                complaintForm.addEventListener(
                    'submit',
                    function() {

                        if (
                            isCommercial() &&
                            !hasLinkedConsumer()
                        ) {

                            if (
                                commercialAddressInput
                            ) {

                                commercialAddressValue =
                                    commercialAddressInput
                                    .value;


                                commercialAddressInput
                                    .name =
                                    'address';


                                commercialAddressInput
                                    .required =
                                    true;

                            }


                            if (addressInput) {

                                addressInput
                                    .removeAttribute(
                                        'name'
                                    );

                                addressInput.required =
                                    false;

                            }

                        } else if (
                            isEngineering()
                        ) {

                            if (addressInput) {

                                engineeringAddressValue =
                                    addressInput.value;

                                addressInput.name =
                                    'address';

                                addressInput.required =
                                    true;

                            }


                            if (
                                commercialAddressInput
                            ) {

                                commercialAddressInput
                                    .removeAttribute(
                                        'name'
                                    );

                                commercialAddressInput
                                    .required =
                                    false;

                            }

                        } else {

                            if (addressInput) {

                                addressInput
                                    .removeAttribute(
                                        'name'
                                    );

                                addressInput.required =
                                    false;

                            }


                            if (
                                commercialAddressInput
                            ) {

                                commercialAddressInput
                                    .removeAttribute(
                                        'name'
                                    );

                                commercialAddressInput
                                    .required =
                                    false;

                            }

                        }


                        stopCamera();


                        if (submitButton) {

                            submitButton.disabled =
                                true;

                        }


                        if (submitIcon) {

                            submitIcon.className =
                                'fas fa-spinner fa-spin';

                        }


                        if (submitText) {

                            submitText.textContent =
                                'Saving Changes...';

                        }

                    }
                );

            }


            window.addEventListener(
                'beforeunload',
                stopCamera
            );


            updateAccountCard();

            populateComplaintTypes(true);

            updateDivisionInformation();

            updateLocationSections();

            updateDivisionSpecificText();

            updateDescriptionCount();

        });
    </script>
@endpush


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('complaintForm');
            const takeButton = document.getElementById('editTakePhotoButton');
            const chooseButton = document.getElementById('editChoosePhotoButton');
            const deviceInput = document.getElementById('editDevicePhotoInput');
            const photosInput = document.getElementById('editPhotosInput');
            const newPhotoGrid = document.getElementById('editNewPhotoGrid');
            const photoCounter = document.getElementById('editPhotoCounter');
            const requiredMessage = document.getElementById('editPhotoRequiredMessage');
            const removedInputs = document.getElementById('editRemovedPhotoInputs');
            const existingCards = document.querySelectorAll('.edit-existing-photo');
            const removeExistingButtons = document.querySelectorAll('.edit-remove-existing-photo');
            const modal = document.getElementById('editCameraModal');
            const video = document.getElementById('editCameraVideo');
            const canvas = document.getElementById('editCameraCanvas');
            const loading = document.getElementById('editCameraLoading');
            const cameraMessage = document.getElementById('editCameraMessage');
            const captureButton = document.getElementById('editCapturePhotoButton');
            const closeButton = document.getElementById('editCloseCameraButton');
            const cancelButton = document.getElementById('editCancelCameraButton');

            const originalExistingCount = {{ $complaint->photos->count() }};
            let selectedPhotos = [];
            let removedPhotoIds = [];
            let cameraStream = null;

            function remainingExistingCount() {
                return Math.max(0, originalExistingCount - removedPhotoIds.length);
            }

            function finalPhotoCount() {
                return remainingExistingCount() + selectedPhotos.length;
            }

            function validPhoto(file) {
                const allowed = ['image/jpeg', 'image/png', 'image/webp'];
                if (!allowed.includes(file.type)) {
                    alert('Only JPG, JPEG, PNG and WEBP images are allowed.');
                    return false;
                }
                if (file.size > 5 * 1024 * 1024) {
                    alert('Each supporting photo must not exceed 5 MB.');
                    return false;
                }
                return true;
            }

            function syncNewFiles() {
                const transfer = new DataTransfer();
                selectedPhotos.forEach(file => transfer.items.add(file));
                photosInput.files = transfer.files;
            }

            function syncRemovedInputs() {
                removedInputs.innerHTML = '';
                removedPhotoIds.forEach(id => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'remove_photos[]';
                    input.value = id;
                    removedInputs.appendChild(input);
                });
            }

            function updateCounter() {
                photoCounter.textContent = finalPhotoCount() + ' / 5 photos';
                if (finalPhotoCount() >= 1) requiredMessage.classList.add('hidden');
            }

            function renderNewPhotos() {
                newPhotoGrid.innerHTML = '';
                if (!selectedPhotos.length) newPhotoGrid.classList.add('hidden');
                else newPhotoGrid.classList.remove('hidden');

                selectedPhotos.forEach((file, index) => {
                    const card = document.createElement('div');
                    card.className = 'relative overflow-hidden rounded-xl border border-sky-200 bg-sky-50';
                    const imageBox = document.createElement('div');
                    imageBox.className = 'aspect-square bg-slate-100';
                    const img = document.createElement('img');
                    img.className = 'w-full h-full object-cover';
                    img.alt = 'New supporting photo ' + (index + 1);
                    const url = URL.createObjectURL(file);
                    img.src = url;
                    img.onload = () => URL.revokeObjectURL(url);
                    imageBox.appendChild(img);
                    const remove = document.createElement('button');
                    remove.type = 'button';
                    remove.title = 'Remove photo';
                    remove.className =
                        'absolute top-2 right-2 w-8 h-8 rounded-lg bg-white/95 border border-red-200 text-red-600 flex items-center justify-center shadow-sm hover:bg-red-50';
                    remove.innerHTML = '<i class="fas fa-xmark"></i>';
                    remove.addEventListener('click', function() {
                        selectedPhotos.splice(index, 1);
                        syncNewFiles();
                        renderNewPhotos();
                    });
                    const info = document.createElement('div');
                    info.className = 'p-3';
                    info.innerHTML = '<p class="text-xs font-semibold text-slate-700">New Photo ' + (index +
                            1) + '</p><p class="mt-1 text-xs text-slate-500">' + (file.size / 1024 / 1024)
                        .toFixed(2) + ' MB</p>';
                    card.append(imageBox, remove, info);
                    newPhotoGrid.appendChild(card);
                });
                updateCounter();
            }

            function addPhotos(files) {
                for (const file of Array.from(files || [])) {
                    if (finalPhotoCount() >= 5) {
                        alert('A complaint may have a maximum of 5 supporting photos.');
                        break;
                    }
                    if (validPhoto(file)) selectedPhotos.push(file);
                }
                syncNewFiles();
                renderNewPhotos();
            }

            removeExistingButtons.forEach(button => button.addEventListener('click', function() {
                const id = String(this.dataset.photoId);
                if (removedPhotoIds.includes(id)) return;
                removedPhotoIds.push(id);
                const card = document.querySelector('.edit-existing-photo[data-photo-id="' + id + '"]');
                if (card) {
                    card.classList.add('opacity-40');
                    const status = card.querySelector('.edit-photo-status');
                    if (status) status.textContent = 'Will be removed';
                    this.innerHTML = '<i class="fas fa-rotate-left text-xs"></i>';
                    this.title = 'Undo removal';
                    this.classList.remove('text-red-600', 'border-red-200');
                    this.classList.add('text-sky-700', 'border-sky-200');
                    this.onclick = null;
                    this.addEventListener('click', function undo(event) {
                        event.stopImmediatePropagation();
                        removedPhotoIds = removedPhotoIds.filter(value => value !== id);
                        card.classList.remove('opacity-40');
                        if (status) status.textContent = 'Saved photo';
                        this.innerHTML = '<i class="fas fa-trash-can text-xs"></i>';
                        this.title = 'Remove photo';
                        this.classList.remove('text-sky-700', 'border-sky-200');
                        this.classList.add('text-red-600', 'border-red-200');
                        this.removeEventListener('click', undo);
                        syncRemovedInputs();
                        updateCounter();
                    });
                }
                syncRemovedInputs();
                updateCounter();
            }));

            function setCameraMessage(message) {
                cameraMessage.textContent = message || '';
                cameraMessage.classList.toggle('hidden', !message);
            }

            function stopCamera() {
                if (cameraStream) {
                    cameraStream.getTracks().forEach(track => track.stop());
                    cameraStream = null;
                }
                if (video) {
                    video.pause();
                    video.srcObject = null;
                }
            }

            function closeCamera() {
                stopCamera();
                modal.classList.add('hidden');
                loading.classList.remove('hidden');
                document.body.classList.remove('overflow-hidden');
                setCameraMessage('');
            }
            async function openCamera() {
                if (finalPhotoCount() >= 5) {
                    alert('This complaint already has the maximum of 5 supporting photos.');
                    return;
                }
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert(
                        'Camera access is not supported by this browser. Please use Choose from Device instead.'
                        );
                    return;
                }
                stopCamera();
                setCameraMessage('');
                loading.classList.remove('hidden');
                modal.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
                try {
                    cameraStream = await navigator.mediaDevices.getUserMedia({
                        video: {
                            facingMode: {
                                ideal: 'environment'
                            }
                        },
                        audio: false
                    });
                    video.srcObject = cameraStream;
                    await video.play();
                    loading.classList.add('hidden');
                } catch (error) {
                    loading.classList.add('hidden');
                    let message = 'Camera access could not be started.';
                    if (!window.isSecureContext) message = 'Camera access requires HTTPS or localhost.';
                    else if (error && (error.name === 'NotAllowedError' || error.name ===
                            'PermissionDeniedError')) message =
                        'Camera permission was denied. Allow camera access in your browser settings, then try again.';
                    else if (error && error.name === 'NotFoundError') message =
                        'No camera was detected on this device.';
                    else if (error && error.name === 'NotReadableError') message =
                        'The camera is already being used by another application.';
                    setCameraMessage(message);
                }
            }

            function capturePhoto() {
                if (!video.videoWidth || !video.videoHeight) {
                    setCameraMessage('The camera is not ready yet.');
                    return;
                }
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const context = canvas.getContext('2d');
                context.drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(blob => {
                    if (!blob) {
                        setCameraMessage('Unable to capture the photo.');
                        return;
                    }
                    const file = new File([blob], 'complaint-photo-' + Date.now() + '.jpg', {
                        type: 'image/jpeg'
                    });
                    addPhotos([file]);
                    closeCamera();
                }, 'image/jpeg', 0.9);
            }

            takeButton?.addEventListener('click', openCamera);
            chooseButton?.addEventListener('click', function() {
                if (finalPhotoCount() >= 5) alert(
                    'This complaint already has the maximum of 5 supporting photos.');
                else deviceInput.click();
            });
            deviceInput?.addEventListener('change', function() {
                addPhotos(this.files);
                this.value = '';
            });
            captureButton?.addEventListener('click', capturePhoto);
            closeButton?.addEventListener('click', closeCamera);
            cancelButton?.addEventListener('click', closeCamera);
            modal?.addEventListener('click', event => {
                if (event.target === modal) closeCamera();
            });
            document.addEventListener('keydown', event => {
                if (event.key === 'Escape' && modal && !modal.classList.contains('hidden')) closeCamera();
            });
            window.addEventListener('beforeunload', stopCamera);

            form?.addEventListener('submit', function(event) {
                if (finalPhotoCount() < 1) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    requiredMessage.classList.remove('hidden');
                    requiredMessage.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    const submitButton = document.getElementById('submitButton');
                    const submitIcon = document.getElementById('submitIcon');
                    const submitText = document.getElementById('submitText');
                    if (submitButton) submitButton.disabled = false;
                    if (submitIcon) submitIcon.className = 'fas fa-floppy-disk';
                    if (submitText) submitText.textContent = 'Save Changes';
                    return false;
                }
                if (finalPhotoCount() > 5) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                    alert('A complaint may have a maximum of 5 supporting photos.');
                    return false;
                }
                stopCamera();
            }, true);

            updateCounter();
        });
    </script>
@endpush
