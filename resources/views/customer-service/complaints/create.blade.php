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



                            <i class="fas fa-camera"></i>



                        </div>



                        <div>



                            <h2 id="evidenceHeading" class="font-semibold text-slate-900">

                                Supporting Photo

                            </h2>



                            <p id="evidenceDescription" class="mt-1 text-sm text-slate-500">

                                Attach a photo of the reported problem if available.

                            </p>



                        </div>



                    </div>



                </div>



                <div class="p-5 sm:p-6">



                    <label for="photo" id="photoLabel" class="block text-sm font-medium text-slate-700 mb-2">

                        Complaint Photo

                        <span class="font-normal text-slate-400">(Optional)</span>

                    </label>



                    <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/webp"
                        class="block w-full text-sm text-slate-600

                               file:mr-4 file:py-2.5 file:px-4

                               file:rounded-xl file:border-0

                               file:text-sm file:font-semibold

                               file:bg-sky-50 file:text-sky-700

                               hover:file:bg-sky-100">



                    <p class="mt-2 text-xs text-slate-500">

                        JPG, JPEG, PNG or WEBP. Maximum file size: 5 MB.

                    </p>



                    @error('photo')
                        <p class="mt-1 text-sm text-red-600">

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



@endsection



@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const divisions = @json($divisions);
            const oldComplaintType = @json(old('complaint_category_id'));

            const complaintForm = document.getElementById('complaintForm');

            const consumerSelect = document.getElementById('consumer_id');
            const selectedAccountCard = document.getElementById('selectedAccountCard');
            const selectedAccountNumber = document.getElementById('selectedAccountNumber');
            const selectedAccountHolder = document.getElementById('selectedAccountHolder');
            const selectedAccountAddress = document.getElementById('selectedAccountAddress');

            const complainantNameInput = document.getElementById('complainant_name');
            const complainantPhoneInput = document.getElementById('complainant_phone');

            const divisionSelect = document.getElementById('division_id');
            const complaintTypeSelect = document.getElementById('complaint_category_id');

            const divisionInformation = document.getElementById('divisionInformation');
            const engineeringInformation = document.getElementById('engineeringInformation');
            const commercialInformation = document.getElementById('commercialInformation');

            const engineeringLocationSection = document.getElementById('engineeringLocationSection');
            const commercialAddressSection = document.getElementById('commercialAddressSection');

            const addressInput = document.getElementById('address');
            const commercialAddressInput = document.getElementById('commercial_address');
            const landmarkInput = document.getElementById('landmark');
            const latitudeInput = document.getElementById('latitude');
            const longitudeInput = document.getElementById('longitude');

            const descriptionInput = document.getElementById('description');
            const descriptionCount = document.getElementById('descriptionCount');
            const descriptionHelp = document.getElementById('descriptionHelp');

            const evidenceHeading = document.getElementById('evidenceHeading');
            const evidenceDescription = document.getElementById('evidenceDescription');
            const photoLabel = document.getElementById('photoLabel');

            const beforeSubmitText = document.getElementById('beforeSubmitText');
            const submitButton = document.getElementById('submitButton');
            const submitIcon = document.getElementById('submitIcon');
            const submitText = document.getElementById('submitText');

            const mapElement = document.getElementById('complaintMap');
            const mapStatus = document.getElementById('mapStatus');
            const mapStatusText = document.getElementById('mapStatusText');

            let map = null;
            let marker = null;
            let mapInitialized = false;

            let engineeringAddressValue = addressInput ? addressInput.value : '';
            let commercialAddressValue = commercialAddressInput ?
                commercialAddressInput.value :
                '';

            const defaultLatitude = 10.9447;
            const defaultLongitude = 123.4247;

            function getSelectedDivision() {
                if (!divisionSelect || !divisionSelect.value) {
                    return null;
                }

                return divisions.find(function(division) {
                    return String(division.id) === String(divisionSelect.value);
                }) || null;
            }

            function getDivisionName() {
                const division = getSelectedDivision();

                return division ?
                    String(division.name || '').trim() :
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
                if (!consumerSelect) {
                    return null;
                }

                if (!consumerSelect.value) {
                    return null;
                }

                return consumerSelect.options[
                    consumerSelect.selectedIndex
                ] || null;
            }

            function getSelectedAccountAddress() {
                const option = getSelectedConsumerOption();

                if (!option) {
                    return '';
                }

                return String(
                    option.dataset.address || ''
                ).trim();
            }

            function updateAccountCard() {
                if (!consumerSelect || !selectedAccountCard) {
                    return;
                }

                const option = getSelectedConsumerOption();

                if (!option) {
                    selectedAccountCard.classList.add('hidden');

                    if (selectedAccountNumber) {
                        selectedAccountNumber.textContent = '—';
                    }

                    if (selectedAccountHolder) {
                        selectedAccountHolder.textContent = '—';
                    }

                    if (selectedAccountAddress) {
                        selectedAccountAddress.textContent = '—';
                    }

                    return;
                }

                const accountNumber =
                    String(option.dataset.account || '').trim();

                const accountHolder =
                    String(option.dataset.name || '').trim();

                const accountAddress =
                    String(option.dataset.address || '').trim();

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
                        accountAddress || 'No registered address';
                }

                selectedAccountCard.classList.remove('hidden');
            }

            function autofillComplainantFromAccount() {
                const option = getSelectedConsumerOption();

                if (!option) {
                    return;
                }

                const accountHolder =
                    String(option.dataset.name || '').trim();

                const phone =
                    String(option.dataset.phone || '').trim();

                if (
                    complainantNameInput &&
                    accountHolder
                ) {
                    complainantNameInput.value = accountHolder;
                }

                if (
                    complainantPhoneInput &&
                    phone
                ) {
                    complainantPhoneInput.value = phone;
                }
            }

            function populateComplaintTypes(
                preserveSelection = true
            ) {
                if (!complaintTypeSelect) {
                    return;
                }

                const division = getSelectedDivision();

                const currentValue =
                    preserveSelection ?
                    complaintTypeSelect.value :
                    '';

                complaintTypeSelect.innerHTML = '';

                if (!division) {
                    const option =
                        document.createElement('option');

                    option.value = '';
                    option.textContent =
                        'Select a division first';

                    complaintTypeSelect.appendChild(option);
                    complaintTypeSelect.disabled = true;

                    return;
                }

                const placeholder =
                    document.createElement('option');

                placeholder.value = '';
                placeholder.textContent =
                    'Select Complaint Type';

                complaintTypeSelect.appendChild(placeholder);

                const complaintTypes =
                    division.complaint_types ||
                    division.complaintTypes || [];

                complaintTypes.forEach(function(type) {
                    const option =
                        document.createElement('option');

                    option.value = type.id;
                    option.textContent = type.name;

                    complaintTypeSelect.appendChild(option);
                });

                complaintTypeSelect.disabled = false;

                const desiredValue =
                    currentValue ||
                    oldComplaintType ||
                    '';

                if (
                    desiredValue &&
                    Array.from(
                        complaintTypeSelect.options
                    ).some(function(option) {
                        return String(option.value) ===
                            String(desiredValue);
                    })
                ) {
                    complaintTypeSelect.value =
                        desiredValue;
                }
            }

            function setMapStatus(message) {
                if (!mapStatus || !mapStatusText) {
                    return;
                }

                if (!message) {
                    mapStatus.classList.add('hidden');
                    mapStatusText.textContent = '';

                    return;
                }

                mapStatusText.textContent = message;
                mapStatus.classList.remove('hidden');
            }

            async function reverseGeocode(
                latitude,
                longitude
            ) {
                setMapStatus('Detecting address...');

                try {
                    const response = await fetch(
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

                    const data = await response.json();

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

                        setMapStatus(detectedAddress);
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

                const lat = Number(latitude);
                const lng = Number(longitude);

                if (
                    !Number.isFinite(lat) ||
                    !Number.isFinite(lng)
                ) {
                    return;
                }

                if (!marker) {
                    marker = L.marker(
                        [lat, lng], {
                            draggable: true
                        }
                    ).addTo(map);

                    marker.on(
                        'dragend',
                        function(event) {
                            const position =
                                event.target.getLatLng();

                            if (latitudeInput) {
                                latitudeInput.value =
                                    position.lat.toFixed(7);
                            }

                            if (longitudeInput) {
                                longitudeInput.value =
                                    position.lng.toFixed(7);
                            }

                            reverseGeocode(
                                position.lat,
                                position.lng
                            );
                        }
                    );
                } else {
                    marker.setLatLng([lat, lng]);
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
                    reverseGeocode(lat, lng);
                } else if (
                    engineeringAddressValue
                ) {
                    setMapStatus(
                        engineeringAddressValue
                    );
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

                map = L.map(
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
                    Number.isFinite(savedLatitude) &&
                    Number.isFinite(savedLongitude)
                ) {
                    setMarker(
                        savedLatitude,
                        savedLongitude,
                        false
                    );
                }

                setTimeout(function() {
                    if (map) {
                        map.invalidateSize();
                    }
                }, 150);
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
                    divisionInformation.classList.remove(
                        'hidden'
                    );

                    engineeringInformation.classList.remove(
                        'hidden'
                    );

                    commercialInformation.classList.add(
                        'hidden'
                    );

                    return;
                }

                if (isCommercial()) {
                    divisionInformation.classList.remove(
                        'hidden'
                    );

                    engineeringInformation.classList.add(
                        'hidden'
                    );

                    commercialInformation.classList.remove(
                        'hidden'
                    );

                    return;
                }

                divisionInformation.classList.add('hidden');
                engineeringInformation.classList.add(
                    'hidden'
                );
                commercialInformation.classList.add(
                    'hidden'
                );
            }

            function updateLocationSections() {
                const engineering =
                    isEngineering();

                const commercial =
                    isCommercial();

                const linkedConsumer =
                    hasLinkedConsumer();

                if (engineering) {
                    if (engineeringLocationSection) {
                        engineeringLocationSection.classList.remove(
                            'hidden'
                        );
                    }

                    if (commercialAddressSection) {
                        commercialAddressSection.classList.add(
                            'hidden'
                        );
                    }

                    if (addressInput) {
                        if (
                            addressInput.value !==
                            engineeringAddressValue
                        ) {
                            addressInput.value =
                                engineeringAddressValue;
                        }

                        addressInput.required = true;
                        addressInput.name = 'address';
                    }

                    if (commercialAddressInput) {
                        commercialAddressInput.required =
                            false;

                        commercialAddressInput.removeAttribute(
                            'name'
                        );
                    }

                    initializeMap();

                    setTimeout(function() {
                        if (map) {
                            map.invalidateSize();
                        }
                    }, 150);

                    return;
                }

                if (commercial) {
                    if (engineeringLocationSection) {
                        engineeringLocationSection.classList.add(
                            'hidden'
                        );
                    }

                    if (linkedConsumer) {
                        if (commercialAddressSection) {
                            commercialAddressSection.classList.add(
                                'hidden'
                            );
                        }

                        if (commercialAddressInput) {
                            commercialAddressInput.required =
                                false;

                            commercialAddressInput.removeAttribute(
                                'name'
                            );
                        }

                        if (addressInput) {
                            addressInput.required = false;
                            addressInput.removeAttribute(
                                'name'
                            );
                        }
                    } else {
                        if (commercialAddressSection) {
                            commercialAddressSection.classList.remove(
                                'hidden'
                            );
                        }

                        if (commercialAddressInput) {
                            commercialAddressInput.value =
                                commercialAddressValue;

                            commercialAddressInput.required =
                                true;

                            commercialAddressInput.name =
                                'address';
                        }

                        if (addressInput) {
                            addressInput.required = false;
                            addressInput.removeAttribute(
                                'name'
                            );
                        }
                    }

                    return;
                }

                if (engineeringLocationSection) {
                    engineeringLocationSection.classList.add(
                        'hidden'
                    );
                }

                if (commercialAddressSection) {
                    commercialAddressSection.classList.add(
                        'hidden'
                    );
                }

                if (addressInput) {
                    addressInput.required = false;
                    addressInput.removeAttribute('name');
                }

                if (commercialAddressInput) {
                    commercialAddressInput.required = false;
                    commercialAddressInput.removeAttribute(
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

                    if (photoLabel) {
                        photoLabel.textContent =
                            'Complaint Photo';
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

                    if (photoLabel) {
                        photoLabel.textContent =
                            'Supporting Image';
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

                if (photoLabel) {
                    photoLabel.textContent =
                        'Supporting Image';
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

            if (addressInput) {
                addressInput.addEventListener(
                    'input',
                    function() {
                        engineeringAddressValue =
                            addressInput.value;
                    }
                );
            }

            if (commercialAddressInput) {
                commercialAddressInput.addEventListener(
                    'input',
                    function() {
                        commercialAddressValue =
                            commercialAddressInput.value;
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
                        populateComplaintTypes(false);
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

            if (complaintForm) {
                complaintForm.addEventListener(
                    'submit',
                    function() {
                        if (
                            isCommercial() &&
                            !hasLinkedConsumer()
                        ) {
                            if (commercialAddressInput) {
                                commercialAddressValue =
                                    commercialAddressInput.value;

                                commercialAddressInput.name =
                                    'address';

                                commercialAddressInput.required =
                                    true;
                            }

                            if (addressInput) {
                                addressInput.removeAttribute(
                                    'name'
                                );

                                addressInput.required = false;
                            }
                        } else if (isEngineering()) {
                            if (addressInput) {
                                engineeringAddressValue =
                                    addressInput.value;

                                addressInput.name = 'address';
                                addressInput.required = true;
                            }

                            if (commercialAddressInput) {
                                commercialAddressInput.removeAttribute(
                                    'name'
                                );

                                commercialAddressInput.required =
                                    false;
                            }
                        } else {
                            if (addressInput) {
                                addressInput.removeAttribute(
                                    'name'
                                );

                                addressInput.required = false;
                            }

                            if (commercialAddressInput) {
                                commercialAddressInput.removeAttribute(
                                    'name'
                                );

                                commercialAddressInput.required =
                                    false;
                            }
                        }

                        if (submitButton) {
                            submitButton.disabled = true;
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

            updateAccountCard();
            populateComplaintTypes(true);
            updateDivisionInformation();
            updateLocationSections();
            updateDivisionSpecificText();
            updateDescriptionCount();
        });
    </script>
@endpush
