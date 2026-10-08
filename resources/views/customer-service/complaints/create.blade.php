@extends('customer-service.layouts.app')



@section('title', 'Create Complaint')



@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">



    <style>
        #complaintMap {

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

                    Create Complaint

                </h1>



                <p class="mt-1 text-sm text-slate-500">

                    Record a complaint received by Customer Service.

                </p>

            </div>



            <a href="{{ route('customer-service.complaints.index') }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl

                       border border-slate-300 bg-white text-slate-700

                       hover:bg-slate-50 transition">



                <i class="fas fa-arrow-left"></i>



                Back to Complaints



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



        <form action="{{ route('customer-service.complaints.store') }}" method="POST" enctype="multipart/form-data"
            id="complaintForm" class="space-y-6">



            @csrf



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

                                Enter the person reporting the concern and link the affected SWD account when applicable.

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
                                    data-address="{{ $consumer->address?->full_address }}" @selected(old('consumer_id') == $consumer->id)>
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
                                value="{{ old('complainant_name') }}" required
                                placeholder="Enter the full name of the person reporting"
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                This is the person who personally reported the complaint to Customer Service.

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
                                value="{{ old('complainant_phone') }}" placeholder="Enter contact number"
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

                                Select the responsible division and complaint type.

                            </p>



                        </div>



                    </div>



                </div>

                <div class="p-5 sm:p-6 space-y-6">



                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">



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
                                        @selected(old('division_id') == $division->id)>



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



                            <select name="complaint_category_id" id="complaint_category_id" required disabled
                                class="w-full rounded-xl border-slate-300

                                       focus:border-sky-500 focus:ring-sky-500

                                       disabled:bg-slate-100 disabled:text-slate-500">



                                <option value="">

                                    Select a division first

                                </option>



                            </select>



                            @error('complaint_category_id')
                                <p class="mt-1 text-sm text-red-600">

                                    {{ $message }}

                                </p>
                            @enderror



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

                                        A service location is required for this complaint. After verification, it can

                                        proceed through the Engineering maintenance workflow.

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

                                        Customer Service will review and process this complaint. Service location and map

                                        information are not required.

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

                                   focus:border-sky-500 focus:ring-sky-500">{{ old('description') }}</textarea>



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

                                    Identify where the Engineering Operation concern is located.

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

                                Click the map to pin the service location. You can also drag the marker to adjust it.

                                The address below will be filled automatically.

                            </p>



                        </div>



                        <div id="complaintMap"
                            class="w-full h-[420px] rounded-2xl border border-slate-300 overflow-hidden relative z-0">

                        </div>



                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">



                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">



                        <div id="mapStatus" class="hidden mt-3 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3">



                            <div class="flex items-start gap-3">



                                <i class="fas fa-location-dot text-sky-600 mt-0.5"></i>



                                <div>

                                    <p class="text-sm font-medium text-sky-900">

                                        Location Selected

                                    </p>



                                    <p id="mapStatusText" class="mt-1 text-xs text-sky-700">

                                        Detecting address...

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



                            <input type="text" name="address" id="address" value="{{ old('address') }}"
                                placeholder="Pin the location above or enter the address manually" required
                                class="w-full rounded-xl border-slate-300

                       focus:border-sky-500 focus:ring-sky-500">



                            <p class="mt-2 text-xs text-slate-500">

                                The address is automatically filled when a location is pinned.

                                You can edit it if the detected address is incomplete or inaccurate.

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



                            <input type="text" name="landmark" id="landmark" value="{{ old('landmark') }}"
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
                            class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700
                                   flex items-center justify-center shrink-0">

                            <i class="fas fa-images"></i>

                        </div>

                        <div>

                            <h2 id="evidenceHeading" class="font-semibold text-slate-900">
                                Supporting Photos
                                <span class="text-red-500">*</span>
                            </h2>

                            <p id="evidenceDescription" class="mt-1 text-sm text-slate-500">
                                Add at least 1 supporting photo of the reported problem. You may upload up to 5 photos.
                            </p>

                        </div>

                    </div>

                </div>

                <div class="p-5 sm:p-6 space-y-5">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                        <button type="button" id="takePhotoButton"
                            class="flex items-center gap-4 rounded-xl border border-sky-200 bg-sky-50
                                   px-4 py-4 text-left hover:bg-sky-100 hover:border-sky-300 transition">

                            <div
                                class="w-11 h-11 rounded-xl bg-white flex items-center justify-center
                                       text-sky-700 shrink-0 border border-sky-100">

                                <i class="fas fa-camera text-lg"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-900">
                                    Take Photo
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Use the device camera
                                </p>

                            </div>

                        </button>

                        <button type="button" id="choosePhotoButton"
                            class="flex items-center gap-4 rounded-xl border border-slate-200 bg-white
                                   px-4 py-4 text-left hover:bg-slate-50 hover:border-slate-300 transition">

                            <div
                                class="w-11 h-11 rounded-xl bg-slate-50 flex items-center justify-center
                                       text-slate-600 shrink-0 border border-slate-200">

                                <i class="fas fa-images text-lg"></i>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-slate-900">
                                    Choose from Device
                                </p>

                                <p class="mt-1 text-xs text-slate-500">
                                    Select one or more images
                                </p>

                            </div>

                        </button>

                    </div>

                    <input type="file" id="devicePhotoInput" accept="image/jpeg,image/png,image/webp" multiple
                        class="hidden">

                    <input type="file" name="photos[]" id="photos" accept="image/jpeg,image/png,image/webp"
                        multiple class="hidden">

                    <div
                        class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2
                               rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">

                        <div class="flex items-center gap-2 text-sm text-slate-600">

                            <i class="fas fa-circle-info text-sky-600"></i>

                            <span>
                                JPG, JPEG, PNG or WEBP. Maximum 5 MB each.
                            </span>

                        </div>

                        <span id="photoCounter" class="text-sm font-semibold text-slate-700">
                            0 / 5 photos
                        </span>

                    </div>

                    <div id="photoRequiredMessage" class="hidden rounded-xl border border-red-200 bg-red-50 px-4 py-3">

                        <div class="flex items-start gap-3">

                            <i class="fas fa-circle-exclamation text-red-600 mt-0.5"></i>

                            <p class="text-sm text-red-700">
                                At least one supporting photo is required.
                            </p>

                        </div>

                    </div>

                    <div id="photoPreviewGrid" class="hidden grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    </div>

                    @error('photos')
                        <p class="text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                    @error('photos.*')
                        <p class="text-sm text-red-600">
                            {{ $message }}
                        </p>
                    @enderror

                </div>

            </div>


            <div
                class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3

                       bg-white border border-slate-200 rounded-2xl p-4 sm:p-5 shadow-sm">



                <p id="beforeSubmitText" class="text-xs text-slate-500">

                    Review the complaint information before submitting.

                </p>



                <div class="flex flex-col-reverse sm:flex-row gap-3">



                    <a href="{{ route('customer-service.complaints.index') }}"
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



                        <i id="submitIcon" class="fas fa-paper-plane"></i>



                        <span id="submitText">

                            Submit Complaint

                        </span>



                    </button>



                </div>



            </div>



        </form>



    </div>




    <div id="cameraModal" class="hidden fixed inset-0 z-[9999] flex items-center justify-center bg-black/80 p-4"
        role="dialog" aria-modal="true" aria-label="Take supporting photo">

        <div class="w-full max-w-2xl overflow-hidden rounded-2xl bg-white shadow-2xl">

            <div class="flex items-center justify-between border-b border-slate-200 p-4">
                <div>
                    <h3 class="font-semibold text-slate-900">Take Supporting Photo</h3>
                    <p class="mt-1 text-xs text-slate-500">Position the reported problem inside the camera view.</p>
                </div>

                <button type="button" id="closeCameraButton"
                    class="w-10 h-10 rounded-xl text-slate-600 hover:bg-slate-100
                           flex items-center justify-center"
                    aria-label="Close camera">
                    <i class="fas fa-xmark"></i>
                </button>
            </div>

            <div class="relative bg-black min-h-[280px] flex items-center justify-center">
                <video id="cameraVideo" autoplay muted playsinline class="w-full max-h-[65vh] object-contain"></video>

                <div id="cameraLoading" class="absolute inset-0 flex items-center justify-center bg-black text-white">
                    <div class="text-center">
                        <i class="fas fa-spinner fa-spin text-2xl"></i>
                        <p class="mt-3 text-sm">Starting camera...</p>
                    </div>
                </div>
            </div>

            <canvas id="cameraCanvas" class="hidden"></canvas>

            <div id="cameraMessage"
                class="hidden mx-4 mt-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3
                       text-sm text-red-700">
            </div>

            <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-3 p-4">
                <button type="button" id="cancelCameraButton"
                    class="rounded-xl border border-slate-300 px-4 py-2.5
                           text-sm font-medium text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>

                <button type="button" id="capturePhotoButton"
                    class="rounded-xl bg-sky-700 px-5 py-2.5
                           text-sm font-semibold text-white hover:bg-sky-800">
                    <i class="fas fa-camera mr-2"></i>
                    Capture Photo
                </button>
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
                @json(old('complaint_category_id'));

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

            const landmarkInput =
                document.getElementById('landmark');

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
                document.getElementById('complaintMap');

            const mapStatus =
                document.getElementById('mapStatus');

            const mapStatusText =
                document.getElementById('mapStatusText');


            const takePhotoButton =
                document.getElementById('takePhotoButton');

            const choosePhotoButton =
                document.getElementById('choosePhotoButton');

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

            const capturePhotoButton =
                document.getElementById('capturePhotoButton');

            const closeCameraButton =
                document.getElementById('closeCameraButton');

            const cancelCameraButton =
                document.getElementById('cancelCameraButton');

            const devicePhotoInput =
                document.getElementById('devicePhotoInput');

            const photosInput =
                document.getElementById('photos');

            const photoPreviewGrid =
                document.getElementById('photoPreviewGrid');

            const photoCounter =
                document.getElementById('photoCounter');

            const photoRequiredMessage =
                document.getElementById('photoRequiredMessage');

            let selectedPhotos = [];
            let cameraStream = null;


            let map = null;
            let marker = null;
            let mapInitialized = false;


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


                    if (beforeSubmitText) {

                        beforeSubmitText.textContent =
                            'Review the Engineering Operation complaint information before submitting.';

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


                    if (beforeSubmitText) {

                        beforeSubmitText.textContent =
                            'Review the Commercial Services complaint information before submitting.';

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


                if (beforeSubmitText) {

                    beforeSubmitText.textContent =
                        'Review the complaint information before submitting.';

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


            function validateSupportingPhoto(file) {

                const allowedTypes = [
                    'image/jpeg',
                    'image/png',
                    'image/webp'
                ];

                if (!allowedTypes.includes(file.type)) {

                    alert(
                        'Only JPG, JPEG, PNG and WEBP images are allowed.'
                    );

                    return false;
                }

                if (file.size > 5 * 1024 * 1024) {

                    alert(
                        'Each supporting photo must not exceed 5 MB.'
                    );

                    return false;
                }

                return true;
            }


            function syncPhotosInput() {

                if (!photosInput) {
                    return;
                }

                const transfer =
                    new DataTransfer();

                selectedPhotos.forEach(
                    function(file) {

                        transfer.items.add(file);

                    }
                );

                photosInput.files =
                    transfer.files;
            }


            function updatePhotoCounter() {

                if (!photoCounter) {
                    return;
                }

                photoCounter.textContent =
                    selectedPhotos.length +
                    ' / 5 photos';
            }


            function renderPhotoPreviews() {

                if (!photoPreviewGrid) {
                    return;
                }

                photoPreviewGrid.innerHTML =
                    '';

                if (selectedPhotos.length === 0) {

                    photoPreviewGrid
                        .classList
                        .add('hidden');

                    updatePhotoCounter();

                    return;
                }

                photoPreviewGrid
                    .classList
                    .remove('hidden');

                selectedPhotos.forEach(
                    function(file, index) {

                        const wrapper =
                            document.createElement('div');

                        wrapper.className =
                            'relative overflow-hidden rounded-xl border border-slate-200 bg-slate-50';

                        const imageWrapper =
                            document.createElement('div');

                        imageWrapper.className =
                            'aspect-square bg-slate-100';

                        const image =
                            document.createElement('img');

                        image.className =
                            'w-full h-full object-cover';

                        image.alt =
                            'Supporting photo ' +
                            (index + 1);

                        const objectUrl =
                            URL.createObjectURL(file);

                        image.src =
                            objectUrl;

                        image.onload =
                            function() {

                                URL.revokeObjectURL(
                                    objectUrl
                                );

                            };

                        imageWrapper.appendChild(
                            image
                        );

                        const removeButton =
                            document.createElement(
                                'button'
                            );

                        removeButton.type =
                            'button';

                        removeButton.className =
                            'absolute top-2 right-2 w-8 h-8 rounded-lg bg-white/95 border border-red-200 text-red-600 flex items-center justify-center shadow-sm hover:bg-red-50';

                        removeButton.title =
                            'Remove photo';

                        removeButton.innerHTML =
                            '<i class="fas fa-xmark"></i>';

                        removeButton.addEventListener(
                            'click',
                            function() {

                                selectedPhotos.splice(
                                    index,
                                    1
                                );

                                syncPhotosInput();

                                renderPhotoPreviews();

                            }
                        );

                        const information =
                            document.createElement('div');

                        information.className =
                            'p-3';

                        const number =
                            document.createElement('p');

                        number.className =
                            'text-xs font-semibold text-slate-700';

                        number.textContent =
                            'Photo ' +
                            (index + 1);

                        const size =
                            document.createElement('p');

                        size.className =
                            'mt-1 text-xs text-slate-500';

                        size.textContent =
                            (
                                file.size /
                                1024 /
                                1024
                            ).toFixed(2) +
                            ' MB';

                        information.appendChild(
                            number
                        );

                        information.appendChild(
                            size
                        );

                        wrapper.appendChild(
                            imageWrapper
                        );

                        wrapper.appendChild(
                            removeButton
                        );

                        wrapper.appendChild(
                            information
                        );

                        photoPreviewGrid.appendChild(
                            wrapper
                        );

                    }
                );

                updatePhotoCounter();

                if (
                    photoRequiredMessage &&
                    selectedPhotos.length > 0
                ) {

                    photoRequiredMessage
                        .classList
                        .add('hidden');

                }
            }


            function addSupportingPhotos(files) {

                const incomingFiles =
                    Array.from(files || []);

                if (incomingFiles.length === 0) {
                    return;
                }

                for (const file of incomingFiles) {

                    if (
                        selectedPhotos.length >= 5
                    ) {

                        alert(
                            'You may upload a maximum of 5 supporting photos.'
                        );

                        break;
                    }

                    if (
                        !validateSupportingPhoto(
                            file
                        )
                    ) {

                        continue;
                    }

                    selectedPhotos.push(
                        file
                    );
                }

                syncPhotosInput();

                renderPhotoPreviews();
            }


            function setCameraMessage(message) {

                if (!cameraMessage) {
                    return;
                }

                cameraMessage.textContent = message || '';
                cameraMessage.classList.toggle('hidden', !message);
            }


            function stopCamera() {

                if (cameraStream) {

                    cameraStream.getTracks().forEach(
                        function(track) {
                            track.stop();
                        }
                    );

                    cameraStream = null;
                }

                if (cameraVideo) {
                    cameraVideo.pause();
                    cameraVideo.srcObject = null;
                }
            }


            function closeCamera() {

                stopCamera();

                if (cameraModal) {
                    cameraModal.classList.add('hidden');
                }

                if (cameraLoading) {
                    cameraLoading.classList.remove('hidden');
                }

                document.body.classList.remove('overflow-hidden');
                setCameraMessage('');
            }


            async function openCamera() {

                if (selectedPhotos.length >= 5) {
                    alert('You already selected the maximum of 5 supporting photos.');
                    return;
                }

                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    alert(
                        'Live camera access is not supported by this browser. Please use Choose from Device instead.');
                    return;
                }

                if (!cameraModal || !cameraVideo) {
                    return;
                }

                stopCamera();
                setCameraMessage('');

                if (cameraLoading) {
                    cameraLoading.classList.remove('hidden');
                }

                cameraModal.classList.remove('hidden');
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

                    cameraVideo.srcObject = cameraStream;
                    await cameraVideo.play();

                    if (cameraLoading) {
                        cameraLoading.classList.add('hidden');
                    }

                } catch (error) {

                    console.error('Camera error:', error);

                    if (cameraLoading) {
                        cameraLoading.classList.add('hidden');
                    }

                    let message = 'Camera access could not be started.';

                    if (!window.isSecureContext) {
                        message =
                            'Camera access requires HTTPS or localhost. Open the system through localhost on this laptop, then try again.';
                    } else if (error && (error.name === 'NotAllowedError' || error.name ===
                            'PermissionDeniedError')) {
                        message =
                            'Camera permission was denied. Allow camera access for this site in your browser, then try again.';
                    } else if (error && error.name === 'NotFoundError') {
                        message =
                            'No camera was detected on this laptop. Please connect or enable a camera, or use Choose from Device.';
                    } else if (error && error.name === 'NotReadableError') {
                        message =
                            'The camera may already be in use by another application. Close the other application and try again.';
                    }

                    setCameraMessage(message);
                }
            }


            function captureCameraPhoto() {

                if (!cameraVideo || !cameraCanvas) {
                    return;
                }

                if (!cameraVideo.videoWidth || !cameraVideo.videoHeight) {
                    setCameraMessage('The camera is not ready yet. Please wait a moment and try again.');
                    return;
                }

                if (selectedPhotos.length >= 5) {
                    setCameraMessage('You already selected the maximum of 5 supporting photos.');
                    return;
                }

                cameraCanvas.width = cameraVideo.videoWidth;
                cameraCanvas.height = cameraVideo.videoHeight;

                const context = cameraCanvas.getContext('2d');

                if (!context) {
                    setCameraMessage('Unable to capture the camera image.');
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
                            setCameraMessage('Unable to capture the photo. Please try again.');
                            return;
                        }

                        const file = new File(
                            [blob],
                            'complaint-photo-' + Date.now() + '.jpg', {
                                type: 'image/jpeg'
                            }
                        );

                        if (!validateSupportingPhoto(file)) {
                            return;
                        }

                        selectedPhotos.push(file);
                        syncPhotosInput();
                        renderPhotoPreviews();
                        closeCamera();
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
                    openCamera
                );
            }


            if (choosePhotoButton) {

                choosePhotoButton.addEventListener(
                    'click',
                    function() {

                        if (
                            selectedPhotos.length >= 5
                        ) {

                            alert(
                                'You already selected the maximum of 5 supporting photos.'
                            );

                            return;
                        }

                        if (devicePhotoInput) {

                            devicePhotoInput.click();

                        }

                    }
                );
            }


            if (devicePhotoInput) {

                devicePhotoInput.addEventListener(
                    'change',
                    function() {

                        addSupportingPhotos(
                            this.files
                        );

                        this.value = '';

                    }
                );
            }


            if (capturePhotoButton) {
                capturePhotoButton.addEventListener('click', captureCameraPhoto);
            }

            if (closeCameraButton) {
                closeCameraButton.addEventListener('click', closeCamera);
            }

            if (cancelCameraButton) {
                cancelCameraButton.addEventListener('click', closeCamera);
            }

            if (cameraModal) {
                cameraModal.addEventListener(
                    'click',
                    function(event) {
                        if (event.target === cameraModal) {
                            closeCamera();
                        }
                    }
                );
            }

            document.addEventListener(
                'keydown',
                function(event) {
                    if (event.key === 'Escape' && cameraModal && !cameraModal.classList.contains('hidden')) {
                        closeCamera();
                    }
                }
            );


            if (complaintForm) {

                complaintForm.addEventListener(
                    'submit',
                    function(event) {

                        if (selectedPhotos.length < 1) {

                            event.preventDefault();

                            if (photoRequiredMessage) {

                                photoRequiredMessage
                                    .classList
                                    .remove('hidden');

                                photoRequiredMessage
                                    .scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'center'
                                    });
                            }

                            return;
                        }

                        stopCamera();


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
                                'Submitting...';

                        }

                    }
                );

            }


            window.addEventListener('beforeunload', stopCamera);


            updateAccountCard();

            populateComplaintTypes(true);

            updateDivisionInformation();

            updateLocationSections();

            updateDivisionSpecificText();

            updateDescriptionCount();

        });
    </script>
@endpush
