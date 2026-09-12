@extends('consumer.layouts.app')

@section('title', 'Edit Complaint')

@section('content')

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER --}}
        <div class="mb-8">

            <a href="{{ route('consumer.complaints.show', $complaint) }}"
                class="inline-flex items-center gap-2 text-sm font-semibold text-sky-700 hover:text-sky-800">

                <i class="fas fa-arrow-left"></i>

                Back to Complaint

            </a>

            <div class="flex items-center gap-3 mb-3">

                <div
                    class="w-12 h-12 rounded-2xl
                           bg-sky-100 text-sky-700
                           flex items-center justify-center
                           text-2xl">
                    <i class="fas fa-pencil-alt"></i>
                </div>

                <div>

                    <h1 class="text-2xl font-bold text-slate-800">
                        Edit Complaint {{ $complaint->complaint_no }}
                    </h1>

                    <p class="text-sm text-slate-500">
                        Update the details of your concern.
                    </p>

                </div>

            </div>


            {{-- NOTICE --}}
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4">

                <p class="text-sm text-amber-800">

                    <strong>Pending complaint:</strong>
                    You can update your complaint while Customer Service
                    has not yet processed it.

                </p>

            </div>

        </div>


        {{-- VALIDATION ERRORS --}}
        @if ($errors->any())

            <div class="mb-6 bg-red-50 border border-red-200 rounded-2xl p-5">

                <div class="font-semibold text-red-700 mb-2">
                    Please check the following:
                </div>

                <ul class="list-disc ml-5 text-sm text-red-600 space-y-1">

                    @foreach ($errors->all() as $error)

                        <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

        @endif


        <form
            method="POST"
            action="{{ route('consumer.complaints.update', $complaint) }}"
            enctype="multipart/form-data"
            class="space-y-8"
        >

            @csrf
            @method('PUT')


            {{-- ========================================================= --}}
            {{-- CATEGORY --}}
            {{-- ========================================================= --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                       border border-slate-200 p-6 md:p-8"
            >

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        What is your concern?
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Select the option that best describes your concern.
                    </p>

                </div>


                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

                    @foreach ($categories as $category)

                        <label class="relative cursor-pointer block">

                            <input
                                type="radio"
                                name="complaint_category_id"
                                value="{{ $category->id }}"
                                class="peer sr-only"
                                {{ old(
                                    'complaint_category_id',
                                    $complaint->complaint_category_id
                                ) == $category->id ? 'checked' : '' }}
                                required
                            >

                            <div
                                class="
                                    h-full
                                    rounded-2xl
                                    border-2
                                    border-slate-200
                                    bg-white
                                    p-5
                                    transition-all
                                    duration-200

                                    hover:border-sky-300
                                    hover:bg-sky-50/50

                                    peer-checked:border-sky-600
                                    peer-checked:bg-sky-50
                                    peer-checked:shadow-md
                                "
                            >

                                <div class="flex items-start justify-between gap-4">

                                    <div class="flex-1">

                                        <h3 class="font-semibold text-slate-800">

                                            {{ $category->name }}

                                        </h3>


                                        @if ($category->description)

                                            <p class="text-sm text-slate-500 mt-2">

                                                {{ $category->description }}

                                            </p>

                                        @endif

                                    </div>


                                    {{-- CUSTOM RADIO --}}

                                    <div
                                        class="
                                            flex-shrink-0
                                            w-6 h-6
                                            rounded-full
                                            border-2
                                            border-slate-300
                                            flex items-center justify-center
                                            transition
                                            peer-checked:border-sky-600
                                        "
                                    >

                                        <div
                                            class="
                                                w-3 h-3
                                                rounded-full
                                                bg-sky-600
                                                scale-0
                                                transition-transform
                                                peer-checked:scale-100
                                            "
                                        ></div>

                                    </div>

                                </div>

                            </div>

                        </label>

                    @endforeach

                </div>


                {{-- CATEGORY GUIDANCE --}}

                <div
                    id="categoryHelp"
                    class="
                        hidden
                        mt-6
                        rounded-2xl
                        bg-sky-50
                        border
                        border-sky-100
                        p-5
                    "
                >

                    <div class="flex gap-3">

                        <div
                            class="
                                w-10 h-10
                                flex-shrink-0
                                rounded-xl
                                bg-white
                                flex items-center justify-center
                                text-xl
                                shadow-sm
                            "
                        >
                            <i class="fas fa-info-circle text-sky-600"></i>
                        </div>

                        <div>

                            <h3
                                id="categoryHelpTitle"
                                class="font-semibold text-sky-800"
                            ></h3>

                            <p
                                id="categoryHelpText"
                                class="text-sm text-sky-700 mt-1 leading-6"
                            ></p>

                        </div>

                    </div>

                </div>

            </section>


            {{-- ========================================================= --}}
            {{-- COMPLAINT DETAILS --}}
            {{-- ========================================================= --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                       border border-slate-200 p-6 md:p-8"
            >

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Tell us about the problem
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Update the information about your concern.
                    </p>

                </div>


                {{-- SUBJECT --}}

                <div class="mb-6">

                    <label
                        for="subject"
                        class="block text-sm font-semibold
                               text-slate-700 mb-2"
                    >

                        Short description

                        <span class="text-red-500">*</span>

                    </label>


                    <input
                        id="subject"
                        name="subject"
                        type="text"
                        value="{{ old('subject', $complaint->subject) }}"
                        required
                        maxlength="255"
                        placeholder="Example: No water in our house"
                        class="
                            w-full rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        "
                    >

                </div>


                {{-- DESCRIPTION --}}

                <div>

                    <label
                        for="description"
                        class="block text-sm font-semibold
                               text-slate-700 mb-2"
                    >

                        What happened?

                        <span class="text-red-500">*</span>

                    </label>


                    <textarea
                        id="description"
                        name="description"
                        rows="5"
                        required
                        maxlength="5000"
                        placeholder="Example: We have had no water since this morning."
                        class="
                            w-full rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        "
                    >{{ old('description', $complaint->description) }}</textarea>


                    <p class="text-xs text-slate-400 mt-2">
                        Please avoid passwords, payment details, or other
                        sensitive information.
                    </p>

                </div>

            </section>


            {{-- ========================================================= --}}
            {{-- LOCATION --}}
            {{-- ========================================================= --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                       border border-slate-200 p-6 md:p-8"
            >

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Where is the concern?
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        Update the location if necessary.
                    </p>

                </div>


                {{-- ADDRESS --}}

                <div class="mb-6">

                    <label
                        for="address"
                        class="block text-sm font-semibold
                               text-slate-700 mb-2"
                    >

                        Address

                        <span class="text-red-500">*</span>

                    </label>


                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        required
                        maxlength="500"
                        placeholder="House number, street/purok, barangay"
                        class="
                            w-full rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        "
                    >{{ old('address', $complaint->address) }}</textarea>

                </div>


                {{-- LANDMARK --}}

                <div class="mb-6">

                    <label
                        for="landmark"
                        class="block text-sm font-semibold
                               text-slate-700 mb-2"
                    >

                        Nearby landmark

                        <span class="text-slate-400 font-normal">
                            (Optional)
                        </span>

                    </label>


                    <input
                        id="landmark"
                        name="landmark"
                        type="text"
                        value="{{ old('landmark', $complaint->landmark) }}"
                        maxlength="255"
                        placeholder="Example: Near barangay hall"
                        class="
                            w-full rounded-xl
                            border-slate-300
                            px-4 py-3
                            focus:border-sky-500
                            focus:ring-sky-500
                        "
                    >

                </div>


                {{-- MAP --}}

                <div>

                    <div class="flex items-center justify-between mb-3">

                        <label
                            class="block text-sm font-semibold
                                   text-slate-700"
                        >

                            Pin the problem location

                            <span class="text-slate-400 font-normal">
                                (Optional but recommended)
                            </span>

                        </label>


                        <button
                            type="button"
                            id="locateMe"
                            class="
                                text-sm font-semibold
                                text-sky-600
                                hover:text-sky-800
                            "
                        >

                            📍 Use my location

                        </button>

                    </div>


                    {{-- EXACT SAME MAP STYLE AS CREATE --}}

                    <div
                        id="complaintMap"
                        class="
                            w-full h-80
                            rounded-2xl
                            border border-slate-300
                            overflow-hidden
                            bg-slate-100
                        "
                    ></div>


                    <p class="text-xs text-slate-500 mt-3">

                        Drag the marker or click the map to update
                        where the problem occurred.

                    </p>


                    {{-- EXISTING COORDINATES --}}

                    <input
                        type="hidden"
                        id="latitude"
                        name="latitude"
                        value="{{ old('latitude', $complaint->latitude) }}"
                    >

                    <input
                        type="hidden"
                        id="longitude"
                        name="longitude"
                        value="{{ old('longitude', $complaint->longitude) }}"
                    >

                </div>

            </section>


            {{-- ========================================================= --}}
            {{-- PHOTO --}}
            {{-- ========================================================= --}}

            <section
                class="bg-white rounded-3xl shadow-sm
                       border border-slate-200 p-6 md:p-8"
            >

                <div class="mb-6">

                    <h2 class="text-lg font-bold text-slate-800">
                        Update photo
                    </h2>

                    <p class="text-sm text-slate-500 mt-1">
                        You may replace the existing photo.
                    </p>

                </div>


                @if ($complaint->photo)

                    <div class="mb-5">

                        <p class="text-sm font-semibold text-slate-700 mb-3">
                            Current photo
                        </p>

                        <img
                            src="{{ asset('storage/' . $complaint->photo) }}"
                            alt="Complaint photo"
                            class="
                                w-full max-w-md
                                rounded-2xl
                                border border-slate-200
                            "
                        >

                    </div>

                @endif


                <input
                    type="file"
                    name="photo"
                    accept="image/jpeg,image/png,image/webp"
                    class="
                        w-full rounded-xl
                        border border-slate-300
                        px-4 py-3
                        text-sm
                    "
                >


                <p class="text-xs text-slate-400 mt-2">
                    JPG, PNG, or WEBP. Maximum 5 MB.
                </p>

            </section>


            {{-- ========================================================= --}}
            {{-- ACTIONS --}}
            {{-- ========================================================= --}}

            <div
                class="
                    bg-white rounded-3xl
                    border border-slate-200
                    shadow-sm p-6 md:p-8
                "
            >

                <div class="flex items-start gap-4">

                    <div
                        class="
                            w-11 h-11 rounded-xl
                            bg-sky-100
                            flex items-center justify-center
                            text-xl flex-shrink-0
                        "
                    >
                        <i class="fas fa-info-circle"></i>
                    </div>


                    <div>

                        <h3 class="font-bold text-slate-800">
                            Before you save
                        </h3>

                        <p class="text-sm text-slate-500 mt-1 leading-6">

                            Make sure the information is correct.
                            Your updated complaint will remain pending
                            until Customer Service reviews it.

                        </p>

                    </div>

                </div>


                <div
                    class="
                        mt-8
                        flex flex-col sm:flex-row
                        justify-end gap-3
                    "
                >

                    <a
                        href="{{ route('consumer.complaints.show', $complaint) }}"
                        class="
                            px-6 py-3 rounded-xl
                            border border-slate-300
                            text-slate-700 text-center
                            hover:bg-slate-50 transition
                        "
                    >
                        Cancel
                    </a>


                    <button
                        type="submit"
                        class="
                            px-7 py-3 rounded-xl
                            bg-sky-700 text-white
                            font-semibold
                            hover:bg-sky-800 transition
                            shadow-lg shadow-sky-700/20
                        "
                    >
                        Save Changes
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- ========================================================= --}}
    {{-- LEAFLET MAP --}}
    {{-- ========================================================= --}}

    @push('scripts')

        <link
            rel="stylesheet"
            href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        />

        <script
            src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js">
        </script>


        <script>

            document.addEventListener('DOMContentLoaded', function() {

                const defaultLat = 10.9447;
                const defaultLng = 123.4247;


                const latitudeInput =
                    document.getElementById('latitude');

                const longitudeInput =
                    document.getElementById('longitude');


                const savedLat =
                    parseFloat(latitudeInput.value);

                const savedLng =
                    parseFloat(longitudeInput.value);


                const startLat =
                    !isNaN(savedLat)
                        ? savedLat
                        : defaultLat;


                const startLng =
                    !isNaN(savedLng)
                        ? savedLng
                        : defaultLng;


                const map =
                    L.map('complaintMap')
                        .setView(
                            [startLat, startLng],
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


                let marker = null;


                function setLocation(lat, lng) {

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
                                [lat, lng],
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
                                    position.lng
                                );

                            }
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Restore existing complaint location
                |--------------------------------------------------------------------------
                */

                if (
                    !isNaN(savedLat) &&
                    !isNaN(savedLng)
                ) {

                    setLocation(
                        savedLat,
                        savedLng
                    );

                }


                /*
                |--------------------------------------------------------------------------
                | Click map
                |--------------------------------------------------------------------------
                */

                map.on(
                    'click',
                    function(event) {

                        setLocation(
                            event.latlng.lat,
                            event.latlng.lng
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Use my location
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('locateMe')
                    .addEventListener(
                        'click',
                        function() {

                            if (!navigator.geolocation) {

                                alert(
                                    'Location services are not supported by your browser.'
                                );

                                return;

                            }


                            navigator.geolocation.getCurrentPosition(

                                function(position) {

                                    const lat =
                                        position.coords.latitude;

                                    const lng =
                                        position.coords.longitude;


                                    map.setView(
                                        [lat, lng],
                                        17
                                    );


                                    setLocation(
                                        lat,
                                        lng
                                    );

                                },

                                function() {

                                    alert(
                                        'Unable to get your location. Please allow location access or select the location manually on the map.'
                                    );

                                }

                            );

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | Leaflet rendering fix
                |--------------------------------------------------------------------------
                */

                setTimeout(function() {

                    map.invalidateSize();

                }, 300);

            });

        </script>

    @endpush


    {{-- ========================================================= --}}
    {{-- CATEGORY GUIDANCE --}}
    {{-- ========================================================= --}}

    @push('scripts')

        <script>

            document.addEventListener('DOMContentLoaded', function() {

                const categoryHelp =
                    document.getElementById('categoryHelp');

                const categoryHelpTitle =
                    document.getElementById('categoryHelpTitle');

                const categoryHelpText =
                    document.getElementById('categoryHelpText');


                const guidance = {

                    @foreach ($categories as $category)

                        "{{ $category->id }}": {

                            title:
                                @json($category->name),

                            text:
                                @json(
                                    $category->description
                                    ?: 'Please provide details about your concern so Customer Service can assist you.'
                                )

                        }

                        {{ !$loop->last ? ',' : '' }}

                    @endforeach

                };


                const categoryInputs =
                    document.querySelectorAll(
                        'input[name="complaint_category_id"]'
                    );


                function showCategoryHelp(categoryId) {

                    const data =
                        guidance[categoryId];


                    if (!data) {

                        categoryHelp.classList.add('hidden');

                        return;

                    }


                    categoryHelpTitle.textContent =
                        data.title;

                    categoryHelpText.textContent =
                        data.text;


                    categoryHelp.classList.remove('hidden');

                }


                categoryInputs.forEach(
                    function(input) {

                        input.addEventListener(
                            'change',
                            function() {

                                showCategoryHelp(
                                    this.value
                                );

                            }
                        );

                    }
                );


                /*
                |--------------------------------------------------------------------------
                | Restore selected category
                |--------------------------------------------------------------------------
                */

                const selectedCategory =
                    document.querySelector(
                        'input[name="complaint_category_id"]:checked'
                    );


                if (selectedCategory) {

                    showCategoryHelp(
                        selectedCategory.value
                    );

                }

            });

        </script>

    @endpush

@endsection
